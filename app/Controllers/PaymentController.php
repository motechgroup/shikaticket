<?php
namespace App\Controllers;
use App\Models\Setting;
use App\Models\User;

class PaymentController
{
	public function mpesa(): void
	{
        if ((Setting::get('payments.mpesa.enabled', '0') !== '1')) {
            http_response_code(400);
            echo 'M-Pesa is disabled';
            return;
        }
        $orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
        if ($orderId <= 0) { http_response_code(400); echo 'Missing order_id'; return; }

        // Fetch order
        $stmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) { http_response_code(404); echo 'Order not found'; return; }

        // Build STK Push request
        $shortcode = Setting::get('payments.mpesa.shortcode', '');
        $passkey = Setting::get('payments.mpesa.passkey', '');
        $consumerKey = Setting::get('payments.mpesa.consumer_key', '');
        $consumerSecret = Setting::get('payments.mpesa.consumer_secret', '');
        $env = Setting::get('payments.mpesa.env', 'sandbox');
        // If a custom https callback is configured (e.g., ngrok/public URL), use it; otherwise default to local base_url
        $configuredCallback = Setting::get('payments.mpesa.callback_url', '');
        $callbackUrl = $configuredCallback !== '' ? $configuredCallback : base_url('/pay/mpesa/callback');
        // Safety: if admin stored only a base URL, append the required callback path
        if (strpos($callbackUrl, '/pay/mpesa/callback') === false) {
            $callbackUrl = rtrim($callbackUrl, '/');
            $callbackUrl .= '/pay/mpesa/callback';
        }

        if (!$shortcode || !$passkey || !$consumerKey || !$consumerSecret) {
            http_response_code(500);
            echo 'M-Pesa not configured';
            return;
        }

        // Get MSISDN from logged-in user phone (international format 2547XXXXXXXX)
        // Prefer explicit phone from request, fallback to user's phone
        $explicit = preg_replace('/\D+/', '', ($_GET['msisdn'] ?? $_POST['msisdn'] ?? ''));
        $user = User::findById((int)($_SESSION['user_id'] ?? 0));
        $msisdn = $explicit !== '' ? $explicit : preg_replace('/\D+/', '', $user['phone'] ?? '');
        // Optional: sandbox MSISDN override (enable via settings if you want to always use the simulator number)
        if ($env === 'sandbox' && \App\Models\Setting::get('payments.mpesa.force_sandbox_msisdn', '0') === '1') {
            $msisdn = '254708374149';
        }
        if (!$msisdn) { http_response_code(400); echo 'User phone missing'; return; }

        // Access token
        $tokenUrl = $env === 'production' ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $ch = curl_init($tokenUrl);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)],
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $tokenResp = curl_exec($ch);
        if ($tokenResp === false) { http_response_code(502); echo 'Token error'; return; }
        $token = json_decode($tokenResp, true)['access_token'] ?? null;
        if (!$token) { http_response_code(502); echo 'Token parse error'; return; }

        // Password and payload
        $timestamp = date('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);
        $stkUrl = $env === 'production' ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)ceil($order['total_amount']),
            'PartyA' => $msisdn,
            'PartyB' => $shortcode,
            'PhoneNumber' => $msisdn,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => 'ORDER' . $orderId,
            'TransactionDesc' => 'Ticket purchase #' . $orderId,
        ];

        // Record initiated payment
        db()->prepare('INSERT INTO payments (order_id, provider, provider_ref, amount, currency, status) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$orderId, 'mpesa', null, $order['total_amount'], $order['currency'], 'initiated']);

        // Initiate STK
        $ch2 = curl_init($stkUrl);
        curl_setopt_array($ch2, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $stkResp = curl_exec($ch2);
        if ($stkResp === false) {
            flash_set('error', 'Failed to initiate M-Pesa payment.');
            redirect(base_url('/user/orders'));
        }
        $resp = json_decode($stkResp, true) ?: [];
        // Optionally store CheckoutRequestID
        $checkoutId = $resp['CheckoutRequestID'] ?? null;
        if ($checkoutId) {
            db()->prepare('UPDATE payments SET provider_ref = ? WHERE order_id = ? AND provider = ? ORDER BY id DESC LIMIT 1')
                ->execute([$checkoutId, $orderId, 'mpesa']);
        }
        // Persist raw response for troubleshooting
        db()->prepare('UPDATE payments SET payload = ? WHERE order_id = ? AND provider = ? ORDER BY id DESC LIMIT 1')
            ->execute([json_encode($resp), $orderId, 'mpesa']);
        $rc = $resp['ResponseCode'] ?? null;
        $desc = $resp['ResponseDescription'] ?? ($resp['errorMessage'] ?? 'Request sent');
        if ((string)$rc === '0') {
            flash_set('success', 'M-Pesa: ' . $desc);
        } else {
            flash_set('error', 'M-Pesa Error: ' . $desc);
        }
        redirect(base_url('/user/orders?order_id=' . $orderId));
	}

    public function mpesaReconcile(): void
    {
        // Re-query STK push status using CheckoutRequestID and update order if successful
        $orderId = (int)($_GET['order_id'] ?? 0);
        if ($orderId <= 0) { http_response_code(400); echo 'Missing order_id'; return; }
        $payStmt = db()->prepare("SELECT * FROM payments WHERE order_id = ? AND provider = 'mpesa' ORDER BY id DESC LIMIT 1");
        $payStmt->execute([$orderId]);
        $payment = $payStmt->fetch();
        if (!$payment) { http_response_code(404); echo 'Payment not found'; return; }

        // If already successful, short-circuit
        if (($payment['status'] ?? '') === 'successful') { echo 'OK'; return; }

        $env = Setting::get('payments.mpesa.env', 'sandbox');
        $shortcode = Setting::get('payments.mpesa.shortcode', '');
        $passkey = Setting::get('payments.mpesa.passkey', '');
        $consumerKey = Setting::get('payments.mpesa.consumer_key', '');
        $consumerSecret = Setting::get('payments.mpesa.consumer_secret', '');
        $checkoutId = $payment['provider_ref'];
        if (!$checkoutId) { http_response_code(400); echo 'Missing checkout id'; return; }

        // Token
        $tokenUrl = $env === 'production' ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $ch = curl_init($tokenUrl);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)],
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $tokenResp = curl_exec($ch);
        $token = json_decode($tokenResp, true)['access_token'] ?? null;
        if (!$token) { http_response_code(502); echo 'Token error'; return; }

        // Build query payload
        $timestamp = date('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);
        $queryUrl = $env === 'production' ? 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query' : 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutId,
        ];
        $ch2 = curl_init($queryUrl);
        curl_setopt_array($ch2, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($ch2);
        $json = json_decode($resp, true) ?: [];
        // Persist latest payload for diagnostics
        db()->prepare('UPDATE payments SET payload = ? WHERE id = ?')->execute([json_encode($json), (int)$payment['id']]);
        $resultCode = (int)($json['ResultCode'] ?? 1);
        if ($resultCode === 0) {
            // Success: update records and generate tickets (reuse callback code path)
            // Simulate minimal mpesa receipt number when not provided
            $mpesaRef = $json['MpesaReceiptNumber'] ?? ($json['CheckoutRequestID'] ?? $checkoutId);
            db()->prepare('UPDATE payments SET status = ?, provider_ref = ? WHERE id = ?')->execute(['successful', $mpesaRef, (int)$payment['id']]);
            db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['paid', $orderId]);
            // Trigger same post-success flow by calling mpesaCallback-like ticket generation
            // Build a slim pseudo-callback structure to reuse code by calling a private helper would be cleaner,
            // but we inline a minimal ticket/email generation here for simplicity.
            $this->generateTicketsAndSendEmails($orderId);
            echo 'OK';
        } else {
            http_response_code(200);
            echo 'PENDING';
        }
    }

    private function generateTicketsAndSendEmails(int $orderId): void
    {
        $orderStmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch();
        if (!$order) { return; }
        $userStmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $userStmt->execute([$order['user_id']]);
        $user = $userStmt->fetch();
        $itemStmt = db()->prepare('SELECT oi.*, e.title, e.event_date, e.venue FROM order_items oi JOIN events e ON e.id = oi.event_id WHERE oi.order_id = ?');
        $itemStmt->execute([$orderId]);
        $items = $itemStmt->fetchAll();
        $mailer = new \App\Services\Mailer();
        $ticketsSummaryHtml = '';
        foreach ($items as $item) {
            $itemId = (int)($item['id'] ?? 0);
            if (!$itemId) { continue; }
            $qty = max(1, (int)($item['quantity'] ?? 1));
            $ins = db()->prepare('INSERT INTO tickets (order_item_id, code, qr_path, status) VALUES (?, ?, ?, "valid")');
            $codes = [];
            for ($i=0; $i<$qty; $i++) {
                $code = substr(str_shuffle('0123456789'), 0, 6);
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                $qrDir = __DIR__ . '/../../public/uploads/qrs';
                if (!is_dir($qrDir)) { @mkdir($qrDir, 0777, true); }
                $qrRel = 'uploads/qrs/' . $code . '.png';
                $qrAbs = __DIR__ . '/../../public/' . $qrRel;
                @file_put_contents($qrAbs, @file_get_contents($qrUrl));
                $ins->execute([$itemId, $code, $qrRel]);
                $codes[] = ['code'=>$code, 'qr'=>$qrRel];
            }
            $ticketsSummaryHtml .= '<div style="margin:16px 0;padding:12px;border:1px solid #1f2937;border-radius:8px">';
            $ticketsSummaryHtml .= '<div style="font-weight:600;margin-bottom:8px">' . htmlspecialchars($item['title']) . ' • ' . htmlspecialchars($item['event_date']) . ' at ' . htmlspecialchars($item['venue']) . '</div>';
            $ticketsSummaryHtml .= '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px">';
            foreach ($codes as $c) {
                $ticketsSummaryHtml .= '<div style="text-align:center;border:1px solid #1f2937;border-radius:6px;padding:8px">';
                $ticketsSummaryHtml .= '<img src="' . htmlspecialchars(base_url('../' . $c['qr'])) . '" alt="QR" style="width:100%;height:auto" />';
                $ticketsSummaryHtml .= '<div style="margin-top:6px;font-weight:600">' . htmlspecialchars($c['code']) . '</div>';
                $ticketsSummaryHtml .= '</div>';
            }
            $ticketsSummaryHtml .= '</div></div>';
        }
        $firstItem = $items[0] ?? [];
        $html1 = \App\Services\EmailTemplates::render('ticket_confirmation', [
            'event_title' => $firstItem['title'] ?? '',
            'event_date' => $firstItem['event_date'] ?? '',
            'venue' => $firstItem['venue'] ?? '',
            'order_id' => $orderId,
            'ticket_code' => '',
        ]);
        if ($html1 === '') { $html1 = '<h1>Ticket Confirmation</h1>'; }
        $html1 .= $ticketsSummaryHtml;
        $mailer->send($user['email'] ?? '', 'Ticket Confirmation', $html1);
        $html2 = \App\Services\EmailTemplates::render('order_receipt', [
            'amount' => $order['total_amount'] ?? '',
            'currency' => $order['currency'] ?? '',
            'order_id' => $orderId,
        ]);
        if ($html2 !== '') { $mailer->send($user['email'] ?? '', 'Order Receipt', $html2); }
    }

	public function paypal(): void
	{
        if ((Setting::get('payments.paypal.enabled', '0') !== '1')) {
            http_response_code(400);
            echo 'PayPal is disabled';
            return;
        }
        echo 'PayPal initiation stub';
	}

	public function flutterwave(): void
	{
        if ((Setting::get('payments.flutterwave.enabled', '0') !== '1')) {
            http_response_code(400);
            echo 'Flutterwave is disabled';
            return;
        }
        echo 'Flutterwave initiation stub';
	}

	public function mpesaCallback(): void
	{
		// Read JSON callback
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true) ?: [];
		$stk = $data['Body']['stkCallback'] ?? null;
		if (!$stk) { echo 'OK'; return; }
		$resultCode = (int)($stk['ResultCode'] ?? 1);
		$checkoutId = $stk['CheckoutRequestID'] ?? '';
		$items = $stk['CallbackMetadata']['Item'] ?? [];
		$amount = null; $mpesaRef = null;
		foreach ($items as $it) {
			if (($it['Name'] ?? '') === 'Amount') { $amount = $it['Value'] ?? null; }
			if (($it['Name'] ?? '') === 'MpesaReceiptNumber') { $mpesaRef = $it['Value'] ?? null; }
		}
		// Map payment by checkoutId
		$stmt = db()->prepare("SELECT * FROM payments WHERE provider = 'mpesa' AND provider_ref = ? ORDER BY id DESC LIMIT 1");
		$stmt->execute([$checkoutId]);
		$payment = $stmt->fetch();
		if (!$payment) { echo 'OK'; return; }
		$orderId = (int)$payment['order_id'];
		if ($resultCode === 0) {
			db()->prepare('UPDATE payments SET status = ?, provider_ref = ? WHERE id = ?')->execute(['successful', $mpesaRef ?: $checkoutId, $payment['id']]);
			db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['paid', $orderId]);
            // Send confirmation emails
            $orderStmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch();
            if ($order) {
                $userStmt = db()->prepare('SELECT * FROM users WHERE id = ?');
                $userStmt->execute([$order['user_id']]);
                $user = $userStmt->fetch();
                $itemStmt = db()->prepare('SELECT oi.*, e.title, e.event_date, e.venue FROM order_items oi JOIN events e ON e.id = oi.event_id WHERE oi.order_id = ?');
                $itemStmt->execute([$orderId]);
                $items = $itemStmt->fetchAll();
                $mailer = new \App\Services\Mailer();
                $ticketsSummaryHtml = '';
                foreach ($items as $item) {
                    $itemId = (int)($item['id'] ?? 0);
                    if (!$itemId) { continue; }
                    $qty = max(1, (int)($item['quantity'] ?? 1));
                    $ins = db()->prepare('INSERT INTO tickets (order_item_id, code, qr_path, status) VALUES (?, ?, ?, "valid")');
                    $codes = [];
                    for ($i=0; $i<$qty; $i++) {
                        $code = substr(str_shuffle('0123456789'), 0, 6);
                        // Create a local PNG by downloading from a reliable QR service
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                        $qrDir = __DIR__ . '/../../public/uploads/qrs';
                        if (!is_dir($qrDir)) { @mkdir($qrDir, 0777, true); }
                        $qrRel = 'uploads/qrs/' . $code . '.png';
                        $qrAbs = __DIR__ . '/../../public/' . $qrRel;
                        @file_put_contents($qrAbs, @file_get_contents($qrUrl));
                        $ins->execute([$itemId, $code, $qrRel]);
                        $codes[] = ['code'=>$code, 'qr'=>$qrRel];
                    }
                    // Build per-item email block
                    $ticketsSummaryHtml .= '<div style="margin:16px 0;padding:12px;border:1px solid #1f2937;border-radius:8px">';
                    $ticketsSummaryHtml .= '<div style="font-weight:600;margin-bottom:8px">' . htmlspecialchars($item['title']) . ' • ' . htmlspecialchars($item['event_date']) . ' at ' . htmlspecialchars($item['venue']) . '</div>';
                    $ticketsSummaryHtml .= '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px">';
                    foreach ($codes as $c) {
                        $ticketsSummaryHtml .= '<div style="text-align:center;border:1px solid #1f2937;border-radius:6px;padding:8px">';
                        $ticketsSummaryHtml .= '<img src="' . htmlspecialchars(base_url('../' . $c['qr'])) . '" alt="QR" style="width:100%;height:auto" />';
                        $ticketsSummaryHtml .= '<div style="margin-top:6px;font-weight:600">' . htmlspecialchars($c['code']) . '</div>';
                        $ticketsSummaryHtml .= '</div>';
                    }
                    $ticketsSummaryHtml .= '</div></div>';
                }

                // Prepare email using template (append tickets grid)
                $firstItem = $items[0] ?? [];
                $html1 = \App\Services\EmailTemplates::render('ticket_confirmation', [
                    'event_title' => $firstItem['title'] ?? '',
                    'event_date' => $firstItem['event_date'] ?? '',
                    'venue' => $firstItem['venue'] ?? '',
                    'order_id' => $orderId,
                    'ticket_code' => '',
                ]);
                if ($html1 === '') {
                    $html1 = '<h1>Ticket Confirmation</h1>';
                }
                $html1 .= $ticketsSummaryHtml;
                $mailer->send($user['email'] ?? '', 'Ticket Confirmation', $html1);
                // Send SMS with ticket codes and scanner link
                try {
                    $sms = new \App\Services\Sms();
                    if ($sms->isConfigured() && !empty($user['phone'])) {
                        $codesFlat = [];
                        foreach ($items as $it2) {
                            $itTickets = db()->prepare('SELECT code FROM tickets t JOIN order_items oi ON oi.id=t.order_item_id WHERE oi.id = ? ORDER BY t.id ASC');
                            $itTickets->execute([(int)$it2['id']]);
                            $codesFlat = array_merge($codesFlat, array_map(function($r){ return $r['code']; }, $itTickets->fetchAll()));
                        }
                        $codesText = implode(', ', array_slice($codesFlat, 0, 5));
                        $scanLink = base_url('/scanner');
                        $ticketLink = base_url('/tickets/view?code=' . urlencode($codesFlat[0] ?? ''));
                        $body = \App\Services\SmsTemplates::render('payment_success', [
                            'order_id' => $orderId,
                            'tickets' => $codesText . ' | Link: ' . $ticketLink,
                        ]);
                        if ($body === '') { $body = 'Order #' . $orderId . ' confirmed. Tickets: ' . $codesText . ' ' . $ticketLink; }
                        $sms->send($user['phone'], $body);
                    }
                } catch (\Throwable $e) { /* ignore sms errors */ }
                $html2 = \App\Services\EmailTemplates::render('order_receipt', [
                    'amount' => $order['total_amount'] ?? '',
                    'currency' => $order['currency'] ?? '',
                    'order_id' => $orderId,
                ]);
                if ($html2 !== '') { $mailer->send($user['email'] ?? '', 'Order Receipt', $html2); }
            }
		} else {
			db()->prepare('UPDATE payments SET status = ? WHERE id = ?')->execute(['failed', $payment['id']]);
			db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['failed', $orderId]);
		}
		echo 'OK';
	}
}



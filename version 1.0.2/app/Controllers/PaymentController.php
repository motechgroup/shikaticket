<?php
namespace App\Controllers;
use App\Models\Setting;
use App\Models\User;

class PaymentController
{
    private function awardLoyaltyPointsForOrder(int $orderId): void
    {
        try {
            $stmt = db()->prepare('SELECT user_id, total_amount FROM orders WHERE id = ?');
            $stmt->execute([$orderId]);
            $row = $stmt->fetch();
            if (!$row) { return; }
            $userId = (int)$row['user_id']; $amount = (float)$row['total_amount'];
            if ($userId <= 0) { return; }
            // Configurable: points per 100 KES for event orders
            $perHundred = (int)\App\Models\Setting::get('loyalty.points.order_per_100_kes', '1');
            if ($perHundred < 0) { $perHundred = 0; }
            $points = (int)floor(($amount / 100) * $perHundred);
            if ($points <= 0) { return; }
            db()->prepare('INSERT INTO user_points (user_id, points, reason, reference_type, reference_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())')
              ->execute([$userId, $points, 'Purchase reward', 'order', $orderId]);
        } catch (\Throwable $e) {}
    }
    public function mpesa(): void
    {
        if ((Setting::get('payments.mpesa.enabled', '0') !== '1')) {
            http_response_code(400);
            echo 'M-Pesa is disabled';
            return;
        }
        // Support campaign payments without an order by passing type=campaign_request&reference={request_id}
        $type = $_GET['type'] ?? '';
        if ($type === 'marketing_order') {
            // Reach-based marketing order (organizer/travel) payment
            $orderId = (int)($_GET['order_id'] ?? 0);
            $amountOverride = (float)($_GET['amount'] ?? 0);
            if ($orderId <= 0) { http_response_code(400); echo 'Missing order_id'; return; }

            // Load marketing order row
            $stmt = db()->prepare('SELECT * FROM marketing_orders WHERE id = ?');
            $stmt->execute([$orderId]);
            $mOrder = $stmt->fetch();
            if (!$mOrder) { http_response_code(404); echo 'Marketing order not found'; return; }

            $amount = (float)($mOrder['total_cost'] ?? 0);
            if ($amount <= 0) { $amount = $amountOverride; }
            if ($amount <= 0) { http_response_code(400); echo 'Invalid amount'; return; }

            // Determine payer phone by account type with optional override
            $msisdn = '';
            if (($mOrder['account_type'] ?? '') === 'organizer') {
                $q = db()->prepare('SELECT mpesa_phone, phone FROM organizers WHERE id = ?');
                $q->execute([(int)($mOrder['account_id'] ?? 0)]);
                $row = $q->fetch();
                $msisdn = preg_replace('/\D+/', '', ($row['mpesa_phone'] ?? ($row['phone'] ?? '')));
            } elseif (($mOrder['account_type'] ?? '') === 'travel_agency') {
                $q = db()->prepare('SELECT mpesa_phone, phone FROM travel_agencies WHERE id = ?');
                $q->execute([(int)($mOrder['account_id'] ?? 0)]);
                $row = $q->fetch();
                $msisdn = preg_replace('/\D+/', '', ($row['mpesa_phone'] ?? ($row['phone'] ?? '')));
            }
            $override = preg_replace('/\D+/', '', ($_GET['msisdn'] ?? ''));
            if ($override !== '') { $msisdn = $override; }

            // Sandbox override if enabled
            $env = Setting::get('payments.mpesa.env', 'sandbox');
            if ($msisdn === '' && $env === 'sandbox' && \App\Models\Setting::get('payments.mpesa.force_sandbox_msisdn', '0') === '1') {
                $msisdn = '254708374149';
            }
            if ($msisdn === '') { http_response_code(400); echo 'User phone missing'; return; }

            // Config
            $shortcode = Setting::get('payments.mpesa.shortcode', '');
            $passkey = Setting::get('payments.mpesa.passkey', '');
            $consumerKey = Setting::get('payments.mpesa.consumer_key', '');
            $consumerSecret = Setting::get('payments.mpesa.consumer_secret', '');
            $configuredCallback = Setting::get('payments.mpesa.callback_url', '');
            $callbackUrl = $configuredCallback !== '' ? $configuredCallback : base_url('/pay/mpesa/callback');
            if (strpos($callbackUrl, '/pay/mpesa/callback') === false) { $callbackUrl = rtrim($callbackUrl, '/') . '/pay/mpesa/callback'; }
            if (!$shortcode || !$passkey || !$consumerKey || !$consumerSecret) { http_response_code(500); echo 'M-Pesa not configured'; return; }

            // Mark pending
            try { db()->prepare('UPDATE marketing_orders SET payment_status = ? WHERE id = ?')->execute(['pending', $orderId]); } catch (\Throwable $e) {}

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

            $timestamp = date('YmdHis');
            $password = base64_encode($shortcode . $passkey . $timestamp);
            $stkUrl = $env === 'production' ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $payload = [
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int)ceil($amount),
                'PartyA' => $msisdn,
                'PartyB' => $shortcode,
                'PhoneNumber' => $msisdn,
                'CallBackURL' => $callbackUrl,
                'AccountReference' => 'MKTORD' . $orderId,
                'TransactionDesc' => 'Marketing order #' . $orderId,
            ];

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
            $resp = json_decode($stkResp, true) ?: [];
            $checkoutId = $resp['CheckoutRequestID'] ?? null;
            if ($checkoutId) {
                db()->prepare('UPDATE marketing_orders SET payment_reference = ?, payment_status = ? WHERE id = ?')
                    ->execute([$checkoutId, 'pending', $orderId]);
            }
            $desc = $resp['ResponseDescription'] ?? ($resp['errorMessage'] ?? 'Request sent');
            flash_set('info', 'M-Pesa: ' . $desc);
            $returnTo = (($mOrder['account_type'] ?? '') === 'travel_agency') ? '/travel/marketing' : '/organizer/marketing';
            redirect(base_url($returnTo));
            return;
        }
        if ($type === 'campaign_request') {
            $requestId = (int)($_GET['reference'] ?? 0);
            if ($requestId <= 0) { http_response_code(400); echo 'Missing reference'; return; }

            // Load campaign request
            $stmt = db()->prepare('SELECT * FROM marketing_campaign_requests WHERE id = ?');
            $stmt->execute([$requestId]);
            $req = $stmt->fetch();
            if (!$req) { http_response_code(404); echo 'Campaign request not found'; return; }

            $amountOverride = (float)($_GET['amount'] ?? 0);
            $amount = $req['calculated_cost'] !== null ? (float)$req['calculated_cost'] : $amountOverride;
            if ($amount <= 0) { http_response_code(400); echo 'Invalid amount'; return; }

            // Determine payer phone based on account type
            $msisdn = '';
            if (($req['account_type'] ?? '') === 'organizer') {
                $q = db()->prepare('SELECT mpesa_phone, phone FROM organizers WHERE id = ?');
                $q->execute([(int)$req['account_id']]);
                $row = $q->fetch();
                $msisdn = preg_replace('/\D+/', '', ($row['mpesa_phone'] ?? ($row['phone'] ?? '')));
            } elseif (($req['account_type'] ?? '') === 'travel_agency') {
                $q = db()->prepare('SELECT mpesa_phone, phone FROM travel_agencies WHERE id = ?');
                $q->execute([(int)$req['account_id']]);
                $row = $q->fetch();
                $msisdn = preg_replace('/\D+/', '', ($row['mpesa_phone'] ?? ($row['phone'] ?? '')));
            }
            // Optional override via query param
            $override = preg_replace('/\D+/', '', ($_GET['msisdn'] ?? ''));
            if ($override !== '') { $msisdn = $override; }

            // Sandbox fallback if configured
            if ($msisdn === '') {
                $env = Setting::get('payments.mpesa.env', 'sandbox');
                if ($env === 'sandbox' && \App\Models\Setting::get('payments.mpesa.force_sandbox_msisdn', '0') === '1') {
                    $msisdn = '254708374149';
                }
            }
            if ($msisdn === '') { http_response_code(400); echo 'User phone missing'; return; }

            // Build STK Push request using settings like normal flow
            $shortcode = Setting::get('payments.mpesa.shortcode', '');
            $passkey = Setting::get('payments.mpesa.passkey', '');
            $consumerKey = Setting::get('payments.mpesa.consumer_key', '');
            $consumerSecret = Setting::get('payments.mpesa.consumer_secret', '');
            $env = Setting::get('payments.mpesa.env', 'sandbox');
            $configuredCallback = Setting::get('payments.mpesa.callback_url', '');
            $callbackUrl = $configuredCallback !== '' ? $configuredCallback : base_url('/pay/mpesa/callback');
            if (strpos($callbackUrl, '/pay/mpesa/callback') === false) { $callbackUrl = rtrim($callbackUrl, '/') . '/pay/mpesa/callback'; }
            if (!$shortcode || !$passkey || !$consumerKey || !$consumerSecret) { http_response_code(500); echo 'M-Pesa not configured'; return; }

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

            $timestamp = date('YmdHis');
            $password = base64_encode($shortcode . $passkey . $timestamp);
            $stkUrl = $env === 'production' ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            $payload = [
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int)ceil($amount),
                'PartyA' => $msisdn,
                'PartyB' => $shortcode,
                'PhoneNumber' => $msisdn,
                'CallBackURL' => $callbackUrl,
                'AccountReference' => 'CAMPAIGN' . $requestId,
                'TransactionDesc' => 'Marketing campaign #' . $requestId,
            ];

            // Mark request as payment pending
            db()->prepare('UPDATE marketing_campaign_requests SET payment_status = ? WHERE id = ?')
                ->execute(['pending', $requestId]);

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
            $resp = json_decode($stkResp, true) ?: [];
            $checkoutId = $resp['CheckoutRequestID'] ?? null;
            if ($checkoutId) {
                db()->prepare('UPDATE marketing_campaign_requests SET payment_reference = ? WHERE id = ?')
                    ->execute([$checkoutId, $requestId]);
            }
            $desc = $resp['ResponseDescription'] ?? ($resp['errorMessage'] ?? 'Request sent');
            if (!$checkoutId) {
                // Mark failed to help diagnostics
                try { db()->prepare('UPDATE marketing_campaign_requests SET payment_status = ? WHERE id = ?')->execute(['failed', $requestId]); } catch (\Throwable $e) {}
            }
            // Redirect back to marketing page with a notice
            flash_set('info', 'M-Pesa: ' . $desc);
            $returnTo = ($req['account_type'] ?? '') === 'travel_agency' ? '/travel/marketing' : '/organizer/marketing';
            redirect(base_url($returnTo));
            return;
        }
        // Default flow: expects an order_id
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
            redirect(base_url('/user/orders?order_id=' . $orderId . '&mpesa_payment=1'));
        } else {
            flash_set('error', 'M-Pesa Error: ' . $desc);
            redirect(base_url('/user/orders?order_id=' . $orderId));
        }
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
            try { $this->awardLoyaltyPointsForOrder($orderId); } catch (\Throwable $e) {}
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
            $ins = db()->prepare('INSERT INTO tickets (order_item_id, code, qr_path, status, tier) VALUES (?, ?, ?, "valid", ?)');
            $codes = [];
            for ($i=0; $i<$qty; $i++) {
                $code = substr(str_shuffle('0123456789'), 0, 6);
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                $qrDir = __DIR__ . '/../../public/uploads/qrs';
                if (!is_dir($qrDir)) { @mkdir($qrDir, 0777, true); }
                $qrRel = 'uploads/qrs/' . $code . '.png';
                $qrAbs = __DIR__ . '/../../public/' . $qrRel;
                @file_put_contents($qrAbs, @file_get_contents($qrUrl));
                $ins->execute([$itemId, $code, $qrRel, $item['tier'] ?? 'regular']);
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

        // Also send SMS with ticket codes and quick link (works for real phone numbers)
        try {
            $sms = new \App\Services\Sms();
            if ($sms->isConfigured() && !empty($user['phone'])) {
                error_log("Attempting to send SMS for order {$orderId} to user phone: {$user['phone']}");
                
                $codesFlat = [];
                foreach ($items as $it2) {
                    $itTickets = db()->prepare('SELECT code FROM tickets t JOIN order_items oi ON oi.id=t.order_item_id WHERE oi.id = ? ORDER BY t.id ASC');
                    $itTickets->execute([(int)$it2['id']]);
                    $codesFlat = array_merge($codesFlat, array_map(function($r){ return $r['code']; }, $itTickets->fetchAll()));
                }
                $codesText = implode(', ', array_slice($codesFlat, 0, 5));
                $ticketLink = base_url('/tickets/view?code=' . urlencode($codesFlat[0] ?? ''));
                $body = \App\Services\SmsTemplates::render('payment_success', [
                    'order_id' => $orderId,
                    'tickets' => $codesText . ' | Link: ' . $ticketLink,
                ]);
                if ($body === '') { $body = 'Order #' . $orderId . ' confirmed. Tickets: ' . $codesText . ' ' . $ticketLink; }
                
                error_log("SMS message for order {$orderId}: {$body}");
                $result = $sms->send($user['phone'], $body);
                error_log("SMS send result for order {$orderId}: " . ($result ? 'SUCCESS' : 'FAILED'));
            } else {
                error_log("SMS not sent for order {$orderId} - Service configured: " . ($sms->isConfigured() ? 'YES' : 'NO') . ", User phone: " . ($user['phone'] ?? 'EMPTY'));
            }
        } catch (\Throwable $e) { 
            error_log("SMS sending error for order {$orderId}: " . $e->getMessage());
        }
    }

	public function paypal(): void
	{
        if ((Setting::get('payments.paypal.enabled', '0') !== '1')) {
            http_response_code(400);
            echo 'PayPal is disabled';
            return;
        }

		$env = Setting::get('payments.paypal.env', 'sandbox');
		$clientId = Setting::get('payments.paypal.client_id', '');
		$secret = Setting::get('payments.paypal.secret', '');
		$base = $env === 'production' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
		$returnUrl = base_url('/pay/paypal');
		$cancelUrl = base_url('/user/orders');

		if ($clientId === '' || $secret === '') { http_response_code(500); echo 'PayPal not configured'; return; }

		// If PayPal redirected back with token (order id), capture it
		$ppOrderId = $_GET['token'] ?? '';
		if ($ppOrderId !== '') {
			// Capture
			$chTok = curl_init($base . '/v1/oauth2/token');
			curl_setopt_array($chTok, [
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERPWD => $clientId . ':' . $secret,
			]);
			$respTok = curl_exec($chTok);
			$access = json_decode($respTok, true)['access_token'] ?? null;
			if (!$access) { http_response_code(502); echo 'PayPal token error'; return; }
			$chCap = curl_init($base . '/v2/checkout/orders/' . urlencode($ppOrderId) . '/capture');
			curl_setopt_array($chCap, [
				CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $access],
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
			]);
			$respCap = curl_exec($chCap);
			$data = json_decode($respCap, true) ?: [];
			$status = $data['status'] ?? '';
			// Try to map back to our order via reference
			$ref = '';
			try { $ref = ($data['purchase_units'][0]['reference_id'] ?? ''); } catch (\Throwable $e) {}
			$orderId = (int)preg_replace('/\D+/', '', $ref);
			if ($status === 'COMPLETED' && $orderId > 0) {
				// Record payment
				db()->prepare('INSERT INTO payments (order_id, provider, provider_ref, amount, currency, status, payload) VALUES (?, ?, ?, (SELECT total_amount FROM orders WHERE id=?), (SELECT currency FROM orders WHERE id=?), ?, ?)')
					->execute([$orderId, 'paypal', $ppOrderId, $orderId, $orderId, 'successful', json_encode($data)]);
				db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['paid', $orderId]);
				$this->generateTicketsAndSendEmails($orderId);
				flash_set('success', 'PayPal payment completed.');
				redirect(base_url('/user/orders?order_id=' . $orderId));
			}
			flash_set('error', 'PayPal capture failed.');
			redirect(base_url('/user/orders'));
		}

		// Otherwise: create PayPal order and redirect to approval
		$orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
		if ($orderId <= 0) { http_response_code(400); echo 'Missing order_id'; return; }
		$stmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
		$stmt->execute([$orderId]);
		$order = $stmt->fetch();
		if (!$order) { http_response_code(404); echo 'Order not found'; return; }

        $amount = number_format((float)$order['total_amount'], 2, '.', '');
        $currency = strtoupper($order['currency'] ?? 'KES');
        // PayPal supports specific currencies. If unsupported, fallback to USD to avoid initiation failures
        $supported = [
            'AUD','BRL','CAD','CNY','CZK','DKK','EUR','HKD','HUF','INR','ILS','JPY','MYR','MXN','TWD','NZD','NOK','PHP','PLN','GBP','RUB','SGD','SEK','CHF','THB','USD'
        ];
        if (!in_array($currency, $supported, true)) { $currency = 'USD'; }

		// Get access token
		$ch = curl_init($base . '/v1/oauth2/token');
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERPWD => $clientId . ':' . $secret,
		]);
		$tok = curl_exec($ch);
		$accessToken = json_decode($tok, true)['access_token'] ?? null;
		if (!$accessToken) { http_response_code(502); echo 'PayPal token error'; return; }

		$payload = [
			'intent' => 'CAPTURE',
			'purchase_units' => [[
				'reference_id' => 'ORDER' . $orderId,
				'amount' => [ 'currency_code' => $currency, 'value' => $amount ]
			]],
			'application_context' => [
				'return_url' => $returnUrl,
				'cancel_url' => $cancelUrl
			]
		];
		$ch2 = curl_init($base . '/v2/checkout/orders');
		curl_setopt_array($ch2, [
			CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $accessToken],
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_RETURNTRANSFER => true,
		]);
        $resp = curl_exec($ch2);
        $data = json_decode($resp, true) ?: [];
		$approve = '';
		foreach (($data['links'] ?? []) as $lnk) { if (($lnk['rel'] ?? '') === 'approve') { $approve = $lnk['href'] ?? ''; break; } }
		if ($approve !== '') {
			// Record initiated payment
			db()->prepare('INSERT INTO payments (order_id, provider, provider_ref, amount, currency, status, payload) VALUES (?, ?, ?, ?, ?, ?, ?)')
				->execute([$orderId, 'paypal', $data['id'] ?? null, $order['total_amount'], $currency, 'initiated', json_encode($data)]);
			header('Location: ' . $approve);
			exit;
		}
        // Persist payload for diagnostics when initiation fails
        try {
            db()->prepare("INSERT INTO payments (order_id, provider, provider_ref, amount, currency, status, payload) VALUES (?, 'paypal', NULL, ?, ?, 'failed', ?)")
                ->execute([$orderId, $order['total_amount'], $currency, json_encode(['request'=>$payload,'response'=>$data])]);
        } catch (\Throwable $e) {}
        http_response_code(502);
        echo 'PayPal initiation failed';
	}

	public function flutterwave(): void
	{
        if ((Setting::get('payments.flutterwave.enabled', '0') !== '1')) {
            http_response_code(400);
            echo 'Flutterwave is disabled';
            return;
        }

        $env = Setting::get('payments.flutterwave.env', 'sandbox');
        $publicKey = Setting::get('payments.flutterwave.public_key', '');
        $secretKey = Setting::get('payments.flutterwave.secret_key', '');
        if ($publicKey === '' || $secretKey === '') { http_response_code(500); echo 'Flutterwave not configured'; return; }

        // Campaign request payment flow
        $type = $_GET['type'] ?? '';
        if ($type === 'campaign_request') {
            $requestId = (int)($_GET['reference'] ?? 0);
            if ($requestId <= 0) { http_response_code(400); echo 'Missing reference'; return; }
            $stmt = db()->prepare('SELECT * FROM marketing_campaign_requests WHERE id = ?');
            $stmt->execute([$requestId]);
            $req = $stmt->fetch();
            if (!$req) { http_response_code(404); echo 'Campaign request not found'; return; }

            $amount = number_format((float)($req['calculated_cost'] ?? 0), 2, '.', '');
            if ((float)$amount <= 0) { http_response_code(400); echo 'Invalid amount'; return; }

            $user = ['email' => ''];
            if (($req['account_type'] ?? '') === 'organizer') {
                $u = db()->prepare('SELECT email, phone, full_name FROM organizers WHERE id = ?'); $u->execute([(int)$req['account_id']]); $user = $u->fetch() ?: $user;
            } else {
                $u = db()->prepare('SELECT email, phone, company_name as full_name FROM travel_agencies WHERE id = ?'); $u->execute([(int)$req['account_id']]); $user = $u->fetch() ?: $user;
            }

            $currency = 'KES';
            $txRef = 'CAMPAIGN' . $requestId . '-' . time();
            $redirectUrl = base_url('/pay/flutterwave');

            $payload = [
                'tx_ref' => $txRef,
                'amount' => $amount,
                'currency' => $currency,
                'redirect_url' => $redirectUrl,
                'customer' => [
                    'email' => $user['email'] ?? 'client@example.com',
                    'phonenumber' => preg_replace('/\D+/', '', $user['phone'] ?? ''),
                    'name' => $user['full_name'] ?? 'Client',
                ],
                'customizations' => [
                    'title' => 'Marketing Campaign #' . $requestId,
                    'description' => 'Campaign payment',
                ],
            ];

            $ch = curl_init('https://api.flutterwave.com/v3/payments');
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $secretKey],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
            ]);
            $resp = curl_exec($ch);
            $json = json_decode($resp, true) ?: [];
            $link = $json['data']['link'] ?? '';
            if ($link !== '') {
                // Store tx_ref on campaign request for later verification
                db()->prepare('UPDATE marketing_campaign_requests SET payment_reference = ?, payment_status = ? WHERE id = ?')
                    ->execute([$txRef, 'pending', $requestId]);
                header('Location: ' . $link);
                exit;
            }
            http_response_code(502);
            echo 'Flutterwave initiation failed';
            return;
        }

        // If redirected back from Flutterwave, verify the transaction
        $status = $_GET['status'] ?? '';
        $txId = $_GET['transaction_id'] ?? '';
        if ($status !== '' && $txId !== '') {
            $base = 'https://api.flutterwave.com'; // same for sandbox/live
            $verifyUrl = $base . '/v3/transactions/' . urlencode($txId) . '/verify';
            $ch = curl_init($verifyUrl);
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $secretKey],
                CURLOPT_RETURNTRANSFER => true,
            ]);
            $resp = curl_exec($ch);
            $json = json_decode($resp, true) ?: [];
            $ok = (($json['status'] ?? '') === 'success') && (($json['data']['status'] ?? '') === 'successful');
            // Extract order id or campaign id from tx_ref
            $txRef = $json['data']['tx_ref'] ?? '';
            if (preg_match('/CAMPAIGN(\d+)/', $txRef, $mm)) {
                $requestId = (int)($mm[1] ?? 0);
                $ok = (($json['status'] ?? '') === 'success') && (($json['data']['status'] ?? '') === 'successful');
                if ($ok && $requestId > 0) {
                    db()->prepare('UPDATE marketing_campaign_requests SET payment_status = ?, payment_reference = ? WHERE id = ?')
                        ->execute(['paid', (string)($json['data']['id'] ?? $txId), $requestId]);
                    // Send SMS
                    try { 
                        $sms = new \App\Services\Sms(); 
                        if ($sms->isConfigured()) {
                            $reqRowStmt = db()->prepare('SELECT account_type, account_id FROM marketing_campaign_requests WHERE id = ?');
                            $reqRowStmt->execute([$requestId]);
                            $rrow = $reqRowStmt->fetch();
                            $phone = '';
                            if (($rrow['account_type'] ?? '') === 'organizer') {
                                $u = db()->prepare('SELECT phone FROM organizers WHERE id = ?'); $u->execute([(int)$rrow['account_id']]); $row=$u->fetch(); $phone=$row['phone'] ?? '';
                            } else {
                                $u = db()->prepare('SELECT phone FROM travel_agencies WHERE id = ?'); $u->execute([(int)$rrow['account_id']]); $row=$u->fetch(); $phone=$row['phone'] ?? '';
                            }
                            if ($phone !== '') { $sms->send($phone, 'Campaign payment received. Request #' . $requestId . ' is queued for review.'); }
                        }
                    } catch (\Throwable $e) {}
                    flash_set('success', 'Campaign payment confirmed.');
                    $returnTo = (($rrow['account_type'] ?? '') === 'travel_agency') ? '/travel/marketing' : '/organizer/marketing';
                    redirect(base_url($returnTo));
                    return;
                }
            }
            
            // Check if this is a travel booking
            if (preg_match('/TRAVEL(\d+)/', $txRef, $m)) {
                $bookingId = (int)($m[1] ?? 0);
                
                if ($ok && $bookingId > 0) {
                    // Get travel booking details
                    $stmt = db()->prepare('
                        SELECT tb.*, tp.id as payment_id, ta.company_name, ta.commission_rate, ta.id as agency_id
                        FROM travel_bookings tb
                        JOIN travel_destinations td ON td.id = tb.destination_id
                        JOIN travel_agencies ta ON ta.id = td.agency_id
                        LEFT JOIN travel_payments tp ON tp.booking_id = tb.id AND tp.id = (
                            SELECT MAX(id) FROM travel_payments WHERE booking_id = tb.id
                        )
                        WHERE tb.id = ?
                    ');
                    $stmt->execute([$bookingId]);
                    $booking = $stmt->fetch();
                    
                    if ($booking) {
                        // Calculate commission
                        $commissionRate = (float)($booking['commission_rate'] ?? 0);
                        $commissionAmount = ($booking['total_amount'] * $commissionRate) / 100;
                        $netAmount = $booking['total_amount'] - $commissionAmount;
                        
                        // Update or create payment record
                        if ($booking['payment_id']) {
                            db()->prepare('UPDATE travel_payments SET payment_status = ?, transaction_reference = ?, paid_at = NOW() WHERE id = ?')
                                ->execute(['paid', (string)$txId, $booking['payment_id']]);
                        } else {
                            db()->prepare('
                                INSERT INTO travel_payments 
                                (booking_id, agency_id, amount, currency, payment_method, payment_status, 
                                 commission_amount, agency_amount, transaction_reference, paid_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                            ')->execute([
                                $bookingId, $booking['agency_id'], $booking['total_amount'], 
                                $booking['currency'], 'flutterwave', 'paid', $commissionAmount, 
                                $netAmount, (string)$txId
                            ]);
                        }
                        
                        // Update booking status
                        db()->prepare('UPDATE travel_bookings SET status = ? WHERE id = ?')
                            ->execute(['confirmed', $bookingId]);
                        
                        // Send confirmation email
                        $this->sendTravelBookingConfirmation($bookingId);
                        
                        flash_set('success', 'Flutterwave payment completed! Your travel booking is confirmed.');
                        redirect(base_url('/user/travel-bookings/show?id=' . $bookingId . '&payment_success=1'));
                    } else {
                        flash_set('error', 'Travel booking not found for Flutterwave tx_ref.');
                        redirect(base_url('/user/travel-bookings'));
                    }
                } else {
                    flash_set('error', 'Flutterwave verification failed for travel booking.');
                    redirect(base_url('/user/travel-bookings'));
                }
            } else {
                // Regular order processing
                $orderId = 0;
                if (preg_match('/ORDER(\d+)/', $txRef, $m)) { $orderId = (int)($m[1] ?? 0); }
                if ($ok && $orderId > 0) {
                    // Confirm order exists to avoid null amount insertion
                    $ordStmt = db()->prepare('SELECT total_amount, currency FROM orders WHERE id = ?');
                    $ordStmt->execute([$orderId]);
                    $ord = $ordStmt->fetch();
                    if (!$ord) { flash_set('error', 'Order not found for Flutterwave tx_ref.'); redirect(base_url('/user/orders')); }
                    // Record payment if not already recorded
                    db()->prepare('INSERT INTO payments (order_id, provider, provider_ref, amount, currency, status, payload) VALUES (?, ?, ?, ?, ?, ?, ?)')
                        ->execute([$orderId, 'flutterwave', (string)$txId, $ord['total_amount'], $ord['currency'], 'successful', json_encode($json)]);
                    db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['paid', $orderId]);
                    $this->generateTicketsAndSendEmails($orderId);
                    flash_set('success', 'Flutterwave payment completed.');
                    redirect(base_url('/user/orders/show?id=' . $orderId . '&payment_success=1'));
                } else {
                    flash_set('error', 'Flutterwave verification failed.');
                    redirect(base_url('/user/orders'));
                }
            }
        }

        // Otherwise: create payment and redirect to hosted checkout
        $orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
        if ($orderId <= 0) { http_response_code(400); echo 'Missing order_id'; return; }
        $stmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) { http_response_code(404); echo 'Order not found'; return; }

        $amount = number_format((float)$order['total_amount'], 2, '.', '');
        $currency = strtoupper($order['currency'] ?? 'KES');
        $user = User::findById((int)($_SESSION['user_id'] ?? 0));
        $txRef = 'ORDER' . $orderId . '-' . time();
        $redirectUrl = base_url('/pay/flutterwave');

        $payload = [
            'tx_ref' => $txRef,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => $redirectUrl,
            'customer' => [
                'email' => $user['email'] ?? 'guest@example.com',
                'phonenumber' => preg_replace('/\D+/', '', $user['phone'] ?? ''),
                'name' => trim(($user['name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            ],
            'customizations' => [
                'title' => 'Ticket Order #' . $orderId,
                'description' => 'Event tickets',
            ],
        ];

        $ch = curl_init('https://api.flutterwave.com/v3/payments');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $secretKey],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $resp = curl_exec($ch);
        $json = json_decode($resp, true) ?: [];
        $link = $json['data']['link'] ?? '';
        if ($link !== '') {
            // Record initiated payment
            db()->prepare('INSERT INTO payments (order_id, provider, provider_ref, amount, currency, status, payload) VALUES (?, ?, ?, ?, ?, ?, ?)')
                ->execute([$orderId, 'flutterwave', $txRef, $order['total_amount'], $currency, 'initiated', json_encode($json)]);
            header('Location: ' . $link);
            exit;
        }
        // Persist payload on failure for diagnostics
        try {
            db()->prepare("INSERT INTO payments (order_id, provider, provider_ref, amount, currency, status, payload) VALUES (?, 'flutterwave', NULL, ?, ?, 'failed', ?)")
                ->execute([$orderId, $order['total_amount'], $currency, json_encode(['request'=>$payload,'response'=>$json])]);
        } catch (\Throwable $e) {}
        http_response_code(502);
        echo 'Flutterwave initiation failed';
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
		
		// Check for travel payment first - look by checkout ID or account reference
		error_log("M-Pesa callback: Looking for travel payment with checkout ID: " . $checkoutId);
		error_log("M-Pesa callback: Raw callback data: " . $raw);
		error_log("M-Pesa callback: Parsed data: " . json_encode($data));
		
		$stmt = db()->prepare("SELECT * FROM travel_payments WHERE payment_method = 'mpesa' AND (transaction_reference = ? OR transaction_reference LIKE ?) ORDER BY id DESC LIMIT 1");
		$stmt->execute([$checkoutId, '%' . $checkoutId . '%']);
		$travelPayment = $stmt->fetch();
		
		if ($travelPayment) {
			error_log("M-Pesa callback: Found travel payment ID " . $travelPayment['id'] . " for booking ID " . $travelPayment['booking_id']);
		} else {
			error_log("M-Pesa callback: No travel payment found with checkout ID: " . $checkoutId);
		}
		
		// If not found by transaction_reference, try to find by account reference pattern
		if (!$travelPayment && !empty($stk['MerchantRequestID'])) {
			error_log("M-Pesa callback: Trying to find by MerchantRequestID: " . $stk['MerchantRequestID']);
			$stmt = db()->prepare("SELECT * FROM travel_payments WHERE payment_method = 'mpesa' AND transaction_reference LIKE ? ORDER BY id DESC LIMIT 1");
			$stmt->execute(['%' . $stk['MerchantRequestID'] . '%']);
			$travelPayment = $stmt->fetch();
			
			if ($travelPayment) {
				error_log("M-Pesa callback: Found travel payment by MerchantRequestID: " . $travelPayment['id']);
			}
		}
		
		if ($travelPayment) {
			$this->handleTravelPaymentCallback($travelPayment, $resultCode, $mpesaRef ?: $checkoutId);
			echo 'OK';
			return;
		}
		
        // First, map to marketing order using stored payment_reference
        $moStmt = db()->prepare("SELECT * FROM marketing_orders WHERE payment_reference = ? ORDER BY id DESC LIMIT 1");
        $moStmt->execute([$checkoutId]);
        $mOrder = $moStmt->fetch();
        if ($mOrder) {
            if ($resultCode === 0) {
                db()->prepare('UPDATE marketing_orders SET payment_status = ?, updated_at = NOW() WHERE id = ?')
                    ->execute(['paid', (int)$mOrder['id']]);
                // Notify via SMS (best-effort)
                try {
                    $sms = new \App\Services\Sms();
                    if ($sms->isConfigured()) {
                        $phone = '';
                        if (($mOrder['account_type'] ?? '') === 'organizer') {
                            $r = db()->prepare('SELECT phone FROM organizers WHERE id = ?'); $r->execute([(int)$mOrder['account_id']]); $row=$r->fetch(); $phone=$row['phone'] ?? '';
                        } else {
                            $r = db()->prepare('SELECT phone FROM travel_agencies WHERE id = ?'); $r->execute([(int)$mOrder['account_id']]); $row=$r->fetch(); $phone=$row['phone'] ?? '';
                        }
                        if ($phone !== '') { $sms->send($phone, 'Marketing order #' . $mOrder['id'] . ' payment received. We\'ll start processing shortly.'); }
                    }
                } catch (\Throwable $e) {}
            }
            echo 'OK';
            return;
        }

        // Then try: map to marketing campaign request using stored payment_reference
        $mStmt = db()->prepare("SELECT * FROM marketing_campaign_requests WHERE payment_reference = ? ORDER BY id DESC LIMIT 1");
        $mStmt->execute([$checkoutId]);
        $mReq = $mStmt->fetch();
        if ($mReq) {
            if ($resultCode === 0) {
                // Mark as paid and notify via SMS
                db()->prepare('UPDATE marketing_campaign_requests SET payment_status = ?, updated_at = NOW() WHERE id = ?')
                    ->execute(['paid', (int)$mReq['id']]);
                try {
                    $sms = new \App\Services\Sms();
                    if ($sms->isConfigured()) {
                        $phone = '';
                        if (($mReq['account_type'] ?? '') === 'organizer') {
                            $r = db()->prepare('SELECT phone FROM organizers WHERE id = ?'); $r->execute([(int)$mReq['account_id']]); $row=$r->fetch(); $phone=$row['phone'] ?? '';
                        } else {
                            $r = db()->prepare('SELECT phone FROM travel_agencies WHERE id = ?'); $r->execute([(int)$mReq['account_id']]); $row=$r->fetch(); $phone=$row['phone'] ?? '';
                        }
                        if ($phone !== '') {
                            $sms->send($phone, 'Campaign payment received. Request #' . $mReq['id'] . ' is queued for review.');
                        }
                    }
                } catch (\Throwable $e) {}
            } else {
                // keep pending
            }
            echo 'OK';
            return;
        }

        // Map payment by checkoutId for regular orders
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
                    $ins = db()->prepare('INSERT INTO tickets (order_item_id, code, qr_path, status, tier) VALUES (?, ?, ?, "valid", ?)');
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
                        $ins->execute([$itemId, $code, $qrRel, $item['tier'] ?? 'regular']);
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
                try { $this->awardLoyaltyPointsForOrder($orderId); } catch (\Throwable $e) {}
            }
		} else {
            // Do NOT mark as failed immediately. Many real transactions complete a few seconds later
            // and are only confirmable via STK Query or a delayed callback. Keep it pending and let
            // the reconcile job or subsequent callback flip to paid.
            try {
                db()->prepare('UPDATE payments SET status = ? WHERE id = ?')->execute(['initiated', $payment['id']]);
            } catch (\Throwable $e) {}
            try {
                db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['pending', $orderId]);
            } catch (\Throwable $e) {}
		}
		echo 'OK';
	}
    
    private function handleTravelPaymentCallback(array $travelPayment, int $resultCode, string $mpesaRef): void
    {
        if ($resultCode === 0) {
            // Payment successful
            db()->prepare('UPDATE travel_payments SET payment_status = ?, transaction_reference = ?, paid_at = NOW() WHERE id = ?')
                ->execute(['paid', $mpesaRef, $travelPayment['id']]);
            
            db()->prepare('UPDATE travel_bookings SET status = ? WHERE id = ?')
                ->execute(['confirmed', $travelPayment['booking_id']]);
            
            // Send confirmation email and SMS
            $this->sendTravelBookingConfirmation($travelPayment['booking_id']);
            
            error_log("Travel payment callback: Successfully processed booking ID " . $travelPayment['booking_id']);
        } else {
            // Payment failed
            db()->prepare('UPDATE travel_payments SET payment_status = ? WHERE id = ?')
                ->execute(['failed', $travelPayment['id']]);
            
            error_log("Travel payment callback: Payment failed for booking ID " . $travelPayment['booking_id'] . " with result code " . $resultCode);
        }
    }
    
    private function sendTravelBookingConfirmation(int $bookingId): void
    {
        try {
            error_log("Travel booking confirmation: Starting for booking ID {$bookingId}");
            
            $stmt = db()->prepare('
                SELECT tb.*, td.title as destination_title, td.departure_date, td.departure_location,
                       ta.company_name, ta.contact_person, ta.email as agency_email, ta.phone as agency_phone,
                       u.first_name, u.last_name, u.email as user_email, u.phone as user_phone
                FROM travel_bookings tb
                JOIN travel_destinations td ON td.id = tb.destination_id
                JOIN travel_agencies ta ON ta.id = td.agency_id
                JOIN users u ON u.id = tb.user_id
                WHERE tb.id = ?
            ');
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                error_log("Travel booking confirmation: Booking not found for ID {$bookingId}");
                return;
            }
            
            error_log("Travel booking confirmation: Found booking for user phone: {$booking['user_phone']}");
            
            // Generate travel ticket code
            $ticketCode = substr(str_shuffle('0123456789'), 0, 6);
            
            // Create travel ticket record
            $stmt = db()->prepare('
                INSERT INTO travel_tickets (booking_id, ticket_code, status, created_at) 
                VALUES (?, ?, "valid", NOW())
            ');
            $stmt->execute([$bookingId, $ticketCode]);
            
            // Send email confirmation
            $mailer = new \App\Services\Mailer();
            $subject = 'Travel Booking Confirmation - ' . $booking['destination_title'];
            
            $html = "
                <h2>Travel Booking Confirmation</h2>
                <p>Dear " . htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) . ",</p>
                <p>Your travel booking has been confirmed successfully!</p>
                
                <h3>Booking Details</h3>
                <ul>
                    <li><strong>Destination:</strong> " . htmlspecialchars($booking['destination_title']) . "</li>
                    <li><strong>Departure Date:</strong> " . date('M j, Y', strtotime($booking['departure_date'])) . "</li>
                    <li><strong>Departure Location:</strong> " . htmlspecialchars($booking['departure_location']) . "</li>
                    <li><strong>Participants:</strong> " . $booking['participants_count'] . " person(s)</li>
                    <li><strong>Total Amount:</strong> " . $booking['currency'] . " " . number_format($booking['total_amount'], 2) . "</li>
                    <li><strong>Ticket Code:</strong> " . $ticketCode . "</li>
                </ul>
                
                <h3>Travel Agency Contact</h3>
                <ul>
                    <li><strong>Agency:</strong> " . htmlspecialchars($booking['company_name']) . "</li>
                    <li><strong>Contact Person:</strong> " . htmlspecialchars($booking['contact_person']) . "</li>
                    <li><strong>Email:</strong> " . htmlspecialchars($booking['agency_email']) . "</li>
                    <li><strong>Phone:</strong> " . htmlspecialchars($booking['agency_phone']) . "</li>
                </ul>
                
                <p>Please keep this confirmation email and your ticket code for your travel.</p>
                <p>Thank you for choosing our travel services!</p>
            ";
            
            $mailer->send($booking['user_email'], $subject, $html);
            
            // Send SMS notification
            try {
                $sms = new \App\Services\Sms();
                error_log("Travel booking SMS: Service configured: " . ($sms->isConfigured() ? 'YES' : 'NO'));
                
                if ($sms->isConfigured() && !empty($booking['user_phone'])) {
                    // Generate public ticket link (no login required)
                    $ticketLink = base_url('/travel-tickets/view?code=' . $ticketCode);
                    
                    // Use SMS template
                    $message = \App\Services\SmsTemplates::render('travel_booking_confirmed', [
                        'destination' => $booking['destination_title'],
                        'ticket_code' => $ticketCode,
                        'booking_reference' => $booking['booking_reference'] ?? $booking['id'],
                        'ticket_link' => $ticketLink,
                        'agency_name' => $booking['company_name'],
                        'agency_phone' => $booking['agency_phone']
                    ]);
                    
                    // Fallback if no template is configured
                    if ($message === '') {
                        $message = "Travel booking confirmed! Destination: " . $booking['destination_title'] . 
                                  ". Ticket Code: " . $ticketCode . 
                                  ". View ticket: " . $ticketLink .
                                  ". Contact: " . $booking['company_name'] . " at " . $booking['agency_phone'];
                    }
                    
                    error_log("Travel booking SMS: Sending to {$booking['user_phone']}: {$message}");
                    $result = $sms->send($booking['user_phone'], $message);
                    error_log("Travel booking SMS: Send result: " . ($result ? 'SUCCESS' : 'FAILED'));
                } else {
                    error_log("Travel booking SMS: Not sent - Service configured: " . ($sms->isConfigured() ? 'YES' : 'NO') . ", User phone: " . ($booking['user_phone'] ?? 'EMPTY'));
                }
            } catch (\Throwable $e) {
                // SMS sending failed, but email was sent
                error_log("Travel booking SMS error: " . $e->getMessage());
            }
            
        } catch (\Throwable $e) {
            // Log error but don't fail the callback
        }
    }
}



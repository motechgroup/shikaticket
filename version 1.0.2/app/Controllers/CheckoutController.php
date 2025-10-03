<?php
namespace App\Controllers;

use App\Models\Setting;

class CheckoutController
{
	public function create(): void
	{
		// Allow guest checkout: collect or reuse user by phone
		$eventId = (int)($_POST['event_id'] ?? 0);
		$quantity = max(1, (int)($_POST['quantity'] ?? 1));
		$tier = trim($_POST['tier'] ?? 'regular');
        $gateway = trim($_POST['gateway'] ?? 'mpesa');
        $msisdn = trim($_POST['msisdn'] ?? '');
		$guestName = trim($_POST['name'] ?? '');
		$guestEmail = trim($_POST['email'] ?? '');
		if ($eventId <= 0) { redirect(base_url('/')); }

		// Load event
		$stmt = db()->prepare('SELECT * FROM events WHERE id = ? AND is_published = 1');
		$stmt->execute([$eventId]);
		$event = $stmt->fetch();
		if (!$event) { redirect(base_url('/')); }

		// Determine unit price by tier
		$unit = null;
		switch ($tier) {
			case 'early_bird': $unit = $event['early_bird_price'] ?? null; break;
			case 'regular': $unit = $event['regular_price'] ?? $event['price']; break;
			case 'vip': $unit = $event['vip_price'] ?? null; break;
			case 'vvip': $unit = $event['vvip_price'] ?? null; break;
			case 'group': $unit = $event['group_price'] ?? null; if (!empty($event['group_size'])) { $quantity = 1; } break;
			default: $unit = $event['regular_price'] ?? $event['price'];
		}
		if ($unit === null) { flash_set('error', 'Selected ticket tier is unavailable.'); redirect(base_url('/events/show?id='.$eventId)); }

		$total = (float)$unit * (int)$quantity;
		$currency = $event['currency'] ?? 'KES';

		// Resolve user id: prefer logged-in; otherwise create/find by phone
		$userId = (int)($_SESSION['user_id'] ?? 0);
		if ($userId === 0) {
			$normalized = preg_replace('/\D+/', '', $msisdn);
			if ($normalized === '') { flash_set('error', 'Phone number is required for payment.'); redirect(base_url('/events/show?id='.$eventId)); }
			try {
				$u = \App\Models\User::findByPhone($normalized);
				if ($u) {
					$userId = (int)$u['id'];
					// Optionally backfill missing email
					if ($guestEmail !== '' && empty($u['email'])) {
						db()->prepare('UPDATE users SET email = ? WHERE id = ?')->execute([$guestEmail, $userId]);
					}
				} else {
					$randPass = bin2hex(random_bytes(6));
					$userId = \App\Models\User::create($normalized, $guestEmail, $randPass);
				}
				// Store msisdn in session for payment redirect UX (optional)
				$_SESSION['user_id'] = $_SESSION['user_id'] ?? $userId; // do not set full role, keep lightweight
			} catch (\Throwable $e) {
				flash_set('error', 'Could not prepare checkout.');
				redirect(base_url('/events/show?id='.$eventId));
			}
		}

		// Create order
		$pdo = db();
		$pdo->beginTransaction();
		try {
			$stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, currency, status) VALUES (?, ?, ?, ?)');
			$stmt->execute([$userId > 0 ? $userId : (int)($_SESSION['user_id'] ?? 0), $total, $currency, 'pending']);
			$orderId = (int)$pdo->lastInsertId();

			$stmt = $pdo->prepare('INSERT INTO order_items (order_id, event_id, quantity, unit_price, tier) VALUES (?, ?, ?, ?, ?)');
			$stmt->execute([$orderId, $eventId, $quantity, $unit, $tier]);
			
			// Calculate and record featured commission if applicable
			require_once __DIR__ . '/FeaturedContentController.php';
			$baseCommission = 0; // Base commission is handled elsewhere
			FeaturedContentController::recordFeaturedCommission('event', $eventId, null, $orderId, null, null, $total, $currency, $baseCommission);
			$pdo->commit();
		} catch (\Throwable $e) {
			$pdo->rollBack();
			flash_set('error', 'Could not create order.');
			redirect(base_url('/events/show?id='.$eventId));
		}

		// Redirect to gateway initiation respecting toggles
        switch ($gateway) {
            case 'mpesa':
                $q = '?order_id=' . $orderId;
                if ($msisdn !== '') { $q .= '&msisdn=' . urlencode($msisdn); }
                redirect(base_url('/pay/mpesa') . $q);
                break;
			case 'paypal': redirect(base_url('/pay/paypal') . '?order_id=' . $orderId); break;
			case 'flutterwave': redirect(base_url('/pay/flutterwave') . '?order_id=' . $orderId); break;
			default: redirect(base_url('/pay/mpesa') . '?order_id=' . $orderId); break;
		}
	}
}



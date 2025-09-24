<?php
namespace App\Controllers;

class OrganizerController
{
	public function dashboard(): void
	{
		require_organizer();
		view('organizer/dashboard');
	}

	public function reports(): void
	{
		require_organizer();
		$organizerId = (int)$_SESSION['organizer_id'];
		$summary = db()->prepare('SELECT COUNT(*) AS orders, SUM(total_amount) AS revenue FROM orders WHERE id IN (SELECT oi.order_id FROM order_items oi JOIN events e ON e.id = oi.event_id WHERE e.organizer_id = ?) AND status = "paid"');
		$summary->execute([$organizerId]);
		$summary = $summary->fetch() ?: ['orders'=>0,'revenue'=>0];
		$events = db()->prepare('SELECT e.id, e.title, SUM(oi.quantity*oi.unit_price) AS revenue, COUNT(DISTINCT oi.order_id) AS orders FROM events e LEFT JOIN order_items oi ON oi.event_id = e.id LEFT JOIN orders o ON o.id = oi.order_id AND o.status = "paid" WHERE e.organizer_id = ? GROUP BY e.id, e.title ORDER BY e.created_at DESC');
		$events->execute([$organizerId]);
		$perEvent = $events->fetchAll();
		view('organizer/reports', compact('summary', 'perEvent'));
	}

	public function withdrawals(): void
	{
		require_organizer();
		$organizerId = (int)$_SESSION['organizer_id'];
		$wd = db()->prepare('SELECT * FROM withdrawals WHERE organizer_id = ? ORDER BY created_at DESC');
		$wd->execute([$organizerId]);
		$withdrawals = $wd->fetchAll();
		view('organizer/withdrawals', compact('withdrawals'));
	}

	public function eventReport(): void
	{
		require_organizer();
		$eventId = (int)($_GET['id'] ?? 0);
		$orgId = (int)$_SESSION['organizer_id'];
		$check = db()->prepare('SELECT * FROM events WHERE id = ? AND organizer_id = ? LIMIT 1');
		$check->execute([$eventId, $orgId]);
		$event = $check->fetch();
		if (!$event) { redirect(base_url('/organizer/events')); }
		$stats = db()->prepare('SELECT COUNT(*) AS tickets, SUM(oi.quantity*oi.unit_price) AS revenue FROM order_items oi JOIN orders o ON o.id = oi.order_id WHERE oi.event_id = ? AND o.status = "paid"');
		$stats->execute([$eventId]);
		$stats = $stats->fetch() ?: ['tickets'=>0,'revenue'=>0];
		$orders = db()->prepare('SELECT o.id, o.total_amount, o.currency, o.created_at FROM orders o JOIN order_items oi ON oi.order_id = o.id WHERE oi.event_id = ? AND o.status = "paid" ORDER BY o.created_at DESC');
		$orders->execute([$eventId]);
		$orders = $orders->fetchAll();
		// Ticket breakdown for this event
		$ticketRows = db()->prepare('SELECT t.code, t.status, t.redeemed_at FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id WHERE oi.event_id = ? ORDER BY t.id DESC');
		$ticketRows->execute([$eventId]);
		$tickets = $ticketRows->fetchAll();
		view('organizer/event_report', compact('event', 'stats', 'orders', 'tickets'));
	}

	public function requestWithdrawal(): void
	{
		require_organizer();
		$amount = (float)($_POST['amount'] ?? 0);
		if ($amount <= 0) { redirect(base_url('/organizer/withdrawals')); }
		$stmt = db()->prepare('INSERT INTO withdrawals (organizer_id, amount, currency, status, notes) VALUES (?, ?, ?, ?, ?)');
		$stmt->execute([(int)$_SESSION['organizer_id'], $amount, 'KES', 'requested', trim($_POST['notes'] ?? '')]);
        flash_set('success', 'Withdrawal request submitted.');
        // SMS confirmation to organizer
        try {
            $org = db()->prepare('SELECT phone FROM organizers WHERE id = ?');
            $org->execute([$_SESSION['organizer_id']]);
            $phone = $org->fetch()['phone'] ?? '';
            $sms = new \App\Services\Sms();
            if ($phone && $sms->isConfigured()) {
                $body = \App\Services\SmsTemplates::render('withdrawal_request', ['amount' => number_format($amount, 2)]);
                if ($body === '') { $body = 'Withdrawal request received: KES ' . number_format($amount, 2); }
                $sms->send($phone, $body);
            }
        } catch (\Throwable $e) {}
        redirect(base_url('/organizer/withdrawals'));
    }

    public function profile(): void
    {
        require_organizer();
        $stmt = db()->prepare('SELECT * FROM organizers WHERE id = ?');
        $stmt->execute([$_SESSION['organizer_id']]);
        $organizer = $stmt->fetch();
        view('organizer/profile', compact('organizer'));
    }

    public function profileSave(): void
    {
        require_organizer();
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $full = trim($_POST['full_name'] ?? '');
        if ($email === '' || $full === '') { redirect(base_url('/organizer/profile')); }
        $setAvatar = '';
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/avatars';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $fn = 'org_' . $_SESSION['organizer_id'] . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['avatar']['tmp_name'], $destDir . '/' . $fn);
            $setAvatar = ', avatar_path = ' . db()->quote('uploads/avatars/' . $fn);
        }
        // If phone changed, clear verification and create OTP
        $current = db()->prepare('SELECT phone FROM organizers WHERE id = ?');
        $current->execute([$_SESSION['organizer_id']]);
        $prevPhone = $current->fetch()['phone'] ?? '';
        if ($phone !== '' && $phone !== $prevPhone) {
            db()->prepare('UPDATE organizers SET phone = ?, phone_verified_at = NULL WHERE id = ?')->execute([$phone, $_SESSION['organizer_id']]);
            // send OTP
            $otp = str_pad((string)rand(0,999999), 6, '0', STR_PAD_LEFT);
            $expires = date('Y-m-d H:i:s', time()+600);
            db()->prepare('INSERT INTO organizer_tokens (organizer_id, token, type, expires_at) VALUES (?, ?, ?, ?)')
              ->execute([$_SESSION['organizer_id'], $otp, 'phone_otp', $expires]);
            try { $sms = new \App\Services\Sms(); if ($sms->isConfigured()) { $sms->send($phone, 'Your Ticko OTP: ' . $otp); } } catch (\Throwable $e) {}
        }
        $sql = 'UPDATE organizers SET full_name = :full, email = :email' . $setAvatar . ' WHERE id = :id';
        $stmt = db()->prepare($sql);
        $stmt->execute([':full'=>$full, ':email'=>$email, ':id'=>$_SESSION['organizer_id']]);
        if (!empty($_POST['password'])) {
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            db()->prepare('UPDATE organizers SET password_hash = ? WHERE id = ?')->execute([$hash, $_SESSION['organizer_id']]);
        }
        flash_set('success', 'Profile updated.');
        redirect(base_url('/organizer/profile'));
    }

    public function startPhoneVerify(): void
    {
        require_organizer();
        $row = db()->prepare('SELECT phone FROM organizers WHERE id = ?');
        $row->execute([$_SESSION['organizer_id']]);
        $phone = $row->fetch()['phone'] ?? '';
        if ($phone === '') { redirect(base_url('/organizer/profile')); }
        $otp = str_pad((string)rand(0,999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time()+600);
        db()->prepare('INSERT INTO organizer_tokens (organizer_id, token, type, expires_at) VALUES (?, ?, ?, ?)')
          ->execute([$_SESSION['organizer_id'], $otp, 'phone_otp', $expires]);
        try { $sms = new \App\Services\Sms(); if ($sms->isConfigured()) { $sms->send($phone, 'Your Ticko OTP: ' . $otp); } } catch (\Throwable $e) {}
        flash_set('success', 'OTP sent to your phone.');
        redirect(base_url('/organizer/profile'));
    }

    public function confirmPhoneVerify(): void
    {
        require_organizer();
        $otp = trim($_POST['otp'] ?? '');
        if ($otp === '') { redirect(base_url('/organizer/profile')); }
        $stmt = db()->prepare("SELECT * FROM organizer_tokens WHERE organizer_id = ? AND token = ? AND type='phone_otp' AND used_at IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->execute([$_SESSION['organizer_id'], $otp]);
        $row = $stmt->fetch();
        if ($row) {
            db()->prepare('UPDATE organizer_tokens SET used_at = NOW() WHERE id = ?')->execute([$row['id']]);
            db()->prepare('UPDATE organizers SET phone_verified_at = NOW() WHERE id = ?')->execute([$_SESSION['organizer_id']]);
            flash_set('success', 'Phone verified.');
        } else {
            flash_set('error', 'Invalid OTP.');
        }
        redirect(base_url('/organizer/profile'));
    }
}



<?php
namespace App\Controllers;
use App\Models\ScannerDevice;

class OrganizerController
{
	public function dashboard(): void
	{
		require_organizer();
		$organizerId = (int)$_SESSION['organizer_id'];
		// Totals
		$totals = db()->prepare('SELECT COUNT(DISTINCT e.id) AS events_count FROM events e WHERE e.organizer_id = ?');
		$totals->execute([$organizerId]);
		$eventsCount = (int)($totals->fetch()['events_count'] ?? 0);
		$orders = db()->prepare('SELECT COUNT(DISTINCT o.id) AS orders_count, COALESCE(SUM(o.total_amount),0) AS revenue FROM orders o JOIN order_items oi ON oi.order_id=o.id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id=? AND o.status="paid"');
		$orders->execute([$organizerId]);
		$ordRow = $orders->fetch() ?: ['orders_count'=>0,'revenue'=>0];
		$revenue = (float)($ordRow['revenue'] ?? 0);
		$ordersCount = (int)($ordRow['orders_count'] ?? 0);
		$tickets = db()->prepare('SELECT COUNT(t.id) AS sold, SUM(CASE WHEN t.status="redeemed" THEN 1 ELSE 0 END) AS redeemed FROM tickets t JOIN order_items oi ON oi.id=t.order_item_id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id=?');
		$tickets->execute([$organizerId]);
		$tix = $tickets->fetch() ?: ['sold'=>0,'redeemed'=>0];
		$recent = db()->prepare('SELECT o.id, o.total_amount, o.currency, o.created_at, e.title FROM orders o JOIN order_items oi ON oi.order_id=o.id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id=? AND o.status="paid" ORDER BY o.created_at DESC LIMIT 10');
		$recent->execute([$organizerId]);
		$recentOrders = $recent->fetchAll();
		view('organizer/dashboard', compact('eventsCount','ordersCount','revenue','tix','recentOrders'));
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
		$wd = db()->prepare('SELECT w.*, e.title AS event_title FROM withdrawals w LEFT JOIN events e ON e.id = w.event_id WHERE w.organizer_id = ? ORDER BY created_at DESC');
		$wd->execute([$organizerId]);
		$withdrawals = $wd->fetchAll();
		// commission percent
		$org = db()->prepare('SELECT commission_percent FROM organizers WHERE id = ?');
		$org->execute([$organizerId]);
		$commission = (float)($org->fetch()['commission_percent'] ?? 0);
		$commissionRate = max(0.0, min(100.0, $commission)) / 100.0;
		// compute available per event: net = paid revenue - commission - withdrawals(approved/paid)
		$rows = db()->prepare('SELECT e.id, e.title, COALESCE(SUM(CASE WHEN o.status="paid" THEN oi.quantity*oi.unit_price ELSE 0 END),0) AS gross FROM events e LEFT JOIN order_items oi ON oi.event_id = e.id LEFT JOIN orders o ON o.id = oi.order_id WHERE e.organizer_id = ? GROUP BY e.id, e.title ORDER BY e.created_at DESC');
		$rows->execute([$organizerId]);
		$events = $rows->fetchAll();
		$balances = [];
		foreach ($events as $ev) {
			$evId = (int)$ev['id'];
			$gross = (float)($ev['gross'] ?? 0);
			$commissionDue = $gross * $commissionRate;
			$withdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id='.(int)$organizerId.' AND (event_id='.(int)$evId.' OR event_id IS NULL) AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
			$balances[$evId] = max(0, $gross - $commissionDue - $withdrawn);
		}
		$overallGross = array_sum(array_column($events, 'gross'));
		$overallCommission = $overallGross * $commissionRate;
		$overallWithdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id='.(int)$organizerId.' AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
		$overallAvailable = max(0, $overallGross - $overallCommission - $overallWithdrawn);
		view('organizer/withdrawals', compact('withdrawals','events','balances','overallAvailable','commission'));
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
		$eventId = isset($_POST['event_id']) && $_POST['event_id'] !== '' ? (int)$_POST['event_id'] : null;
		if ($amount <= 0) { redirect(base_url('/organizer/withdrawals')); }
		$organizerId = (int)$_SESSION['organizer_id'];
		$org = db()->prepare('SELECT commission_percent FROM organizers WHERE id = ?');
		$org->execute([$organizerId]);
		$commissionRate = max(0.0, min(100.0, (float)($org->fetch()['commission_percent'] ?? 0))) / 100.0;
		if ($eventId) {
			$revStmt = db()->prepare('SELECT COALESCE(SUM(CASE WHEN o.status="paid" THEN oi.quantity*oi.unit_price ELSE 0 END),0) AS gross FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id=? AND e.id=?');
			$revStmt->execute([$organizerId, $eventId]);
			$gross = (float)($revStmt->fetch()['gross'] ?? 0);
			$commissionDue = $gross * $commissionRate;
			$wq = db()->prepare('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id = ? AND (event_id = ? OR event_id IS NULL) AND status IN ("approved","paid")');
			$wq->execute([$organizerId, $eventId]);
			$withdrawn = (float)($wq->fetch()['wsum'] ?? 0);
			$available = max(0, $gross - $commissionDue - $withdrawn);
		} else {
			$gross = (float)(db()->query('SELECT COALESCE(SUM(oi.quantity*oi.unit_price),0) AS gross FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id='.(int)$organizerId.' AND o.status="paid"')->fetch()['gross'] ?? 0);
			$commissionDue = $gross * $commissionRate;
			$withdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id='.(int)$organizerId.' AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
			$available = max(0, $gross - $commissionDue - $withdrawn);
		}
		if ($amount > $available + 0.01) { flash_set('error', 'Amount exceeds available balance'); redirect(base_url('/organizer/withdrawals')); }
		$stmt = db()->prepare('INSERT INTO withdrawals (organizer_id, event_id, amount, currency, status, notes) VALUES (?, ?, ?, ?, ?, ?)');
		$stmt->execute([$organizerId, $eventId, $amount, 'KES', 'requested', trim($_POST['notes'] ?? '')]);
        flash_set('success', 'Withdrawal request submitted.');
        try {
            $orgRow = db()->prepare('SELECT phone FROM organizers WHERE id = ?');
            $orgRow->execute([$_SESSION['organizer_id']]);
            $phone = $orgRow->fetch()['phone'] ?? '';
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

	// Scanner Device Management
	public function scannerDevices(): void
	{
		require_organizer();
		$organizerId = (int)$_SESSION['organizer_id'];
		$devices = ScannerDevice::findByOrganizer($organizerId);
		
		// Get scan reports for organizer's devices
		$scanReports = [];
		if (!empty($devices)) {
			$deviceIds = array_column($devices, 'id');
			$placeholders = str_repeat('?,', count($deviceIds) - 1) . '?';
			
			$stmt = db()->prepare("
				SELECT t.*, t.code as ticket_code, t.redeemed_at, t.tier,
					   sd.device_name, sd.device_code,
					   e.title as event_title, e.event_date, e.venue,
					   oi.quantity, oi.unit_price
				FROM tickets t
				LEFT JOIN scanner_devices sd ON sd.id = t.scanner_device_id
				JOIN order_items oi ON oi.id = t.order_item_id
				JOIN events e ON e.id = oi.event_id
				WHERE t.scanner_device_id IN ($placeholders)
				AND t.status = 'redeemed'
				ORDER BY t.redeemed_at DESC
				LIMIT 100
			");
			$stmt->execute($deviceIds);
			$scanReports = $stmt->fetchAll();
		}
		
		view('organizer/scanner_devices', compact('devices', 'scanReports'));
	}

	public function createScannerDevice(): void
	{
		require_organizer();
		$deviceName = trim($_POST['device_name'] ?? '');
		if ($deviceName === '') {
			flash_set('error', 'Device name is required.');
			redirect(base_url('/organizer/scanner-devices'));
		}
		
		$organizerId = (int)$_SESSION['organizer_id'];
		$deviceId = ScannerDevice::create($organizerId, $deviceName);
		flash_set('success', 'Scanner device created successfully.');
		redirect(base_url('/organizer/scanner-devices'));
	}

	public function updateScannerDevice(): void
	{
		require_organizer();
		$deviceId = (int)($_POST['device_id'] ?? 0);
		$deviceName = trim($_POST['device_name'] ?? '');
		$isActive = isset($_POST['is_active']);
		
		if ($deviceId <= 0 || $deviceName === '') {
			flash_set('error', 'Invalid device data.');
			redirect(base_url('/organizer/scanner-devices'));
		}
		
		// Verify device belongs to organizer
		$stmt = db()->prepare('SELECT id FROM scanner_devices WHERE id = ? AND organizer_id = ?');
		$stmt->execute([$deviceId, $_SESSION['organizer_id']]);
		if (!$stmt->fetch()) {
			flash_set('error', 'Device not found.');
			redirect(base_url('/organizer/scanner-devices'));
		}
		
		ScannerDevice::update($deviceId, $deviceName, $isActive);
		flash_set('success', 'Scanner device updated successfully.');
		redirect(base_url('/organizer/scanner-devices'));
	}

	public function deleteScannerDevice(): void
	{
		require_organizer();
		$deviceId = (int)($_POST['device_id'] ?? 0);
		
		if ($deviceId <= 0) {
			flash_set('error', 'Invalid device ID.');
			redirect(base_url('/organizer/scanner-devices'));
		}
		
		// Verify device belongs to organizer
		$stmt = db()->prepare('SELECT id FROM scanner_devices WHERE id = ? AND organizer_id = ?');
		$stmt->execute([$deviceId, $_SESSION['organizer_id']]);
		if (!$stmt->fetch()) {
			flash_set('error', 'Device not found.');
			redirect(base_url('/organizer/scanner-devices'));
		}
		
		ScannerDevice::delete($deviceId);
		flash_set('success', 'Scanner device deleted successfully.');
		redirect(base_url('/organizer/scanner-devices'));
	}

	public function eventScannerAssignments(): void
	{
		require_organizer();
		
		// Get organizer's events
		$events = db()->prepare('SELECT * FROM events WHERE organizer_id = ? AND is_published = 1 ORDER BY event_date DESC');
		$events->execute([$_SESSION['organizer_id']]);
		$events = $events->fetchAll();
		
		// Get organizer's own scanner devices
		$assignedDevices = db()->prepare('
			SELECT sd.*
			FROM scanner_devices sd
			WHERE sd.organizer_id = ? AND sd.is_active = 1
			ORDER BY sd.created_at DESC
		');
		$assignedDevices->execute([$_SESSION['organizer_id']]);
		$assignedDevices = $assignedDevices->fetchAll();
		
		view('organizer/event_scanner_assignments', compact('events', 'assignedDevices'));
	}

	public function assignScannerToEvent(): void
	{
		require_organizer();
		$eventId = (int)($_POST['event_id'] ?? 0);
		$scannerDeviceId = (int)($_POST['scanner_device_id'] ?? 0);
		
		if ($eventId <= 0 || $scannerDeviceId <= 0) {
			flash_set('error', 'Invalid event or scanner device.');
			redirect(base_url('/organizer/event-scanner-assignments'));
		}
		
		// Verify event ownership
		$stmt = db()->prepare('SELECT 1 FROM events WHERE id = ? AND organizer_id = ? LIMIT 1');
		$stmt->execute([$eventId, $_SESSION['organizer_id']]);
		if (!$stmt->fetch()) {
			flash_set('error', 'Event not found.');
			redirect(base_url('/organizer/event-scanner-assignments'));
		}
		
		// Verify scanner device belongs to this organizer
		$stmt = db()->prepare('SELECT 1 FROM scanner_devices WHERE id = ? AND organizer_id = ? AND is_active = 1 LIMIT 1');
		$stmt->execute([$scannerDeviceId, $_SESSION['organizer_id']]);
		if (!$stmt->fetch()) {
			flash_set('error', 'Scanner device not found or not yours.');
			redirect(base_url('/organizer/event-scanner-assignments'));
		}
		
		// Check if already assigned to this event
		$stmt = db()->prepare('SELECT 1 FROM event_scanner_assignments WHERE event_id = ? AND scanner_device_id = ? AND is_active = 1 LIMIT 1');
		$stmt->execute([$eventId, $scannerDeviceId]);
		if ($stmt->fetch()) {
			flash_set('error', 'Scanner device is already assigned to this event.');
			redirect(base_url('/organizer/event-scanner-assignments'));
		}
		
		// Assign scanner to event
		$stmt = db()->prepare('INSERT INTO event_scanner_assignments (event_id, scanner_device_id, organizer_id) VALUES (?, ?, ?)');
		$stmt->execute([$eventId, $scannerDeviceId, $_SESSION['organizer_id']]);
		
		flash_set('success', 'Scanner device assigned to event successfully.');
		redirect(base_url('/organizer/event-scanner-assignments'));
	}

	public function unassignScannerFromEvent(): void
	{
		require_organizer();
		$assignmentId = (int)($_POST['assignment_id'] ?? 0);
		
		if ($assignmentId <= 0) {
			flash_set('error', 'Invalid assignment ID.');
			redirect(base_url('/organizer/event-scanner-assignments'));
		}
		
		// Verify assignment ownership
		$stmt = db()->prepare('SELECT 1 FROM event_scanner_assignments WHERE id = ? AND organizer_id = ? LIMIT 1');
		$stmt->execute([$assignmentId, $_SESSION['organizer_id']]);
		if (!$stmt->fetch()) {
			flash_set('error', 'Assignment not found.');
			redirect(base_url('/organizer/event-scanner-assignments'));
		}
		
		// Deactivate assignment
		$stmt = db()->prepare('UPDATE event_scanner_assignments SET is_active = 0 WHERE id = ?');
		$stmt->execute([$assignmentId]);
		
		flash_set('success', 'Scanner device unassigned from event successfully.');
		redirect(base_url('/organizer/event-scanner-assignments'));
	}
}



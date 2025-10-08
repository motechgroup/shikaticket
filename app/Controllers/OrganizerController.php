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
		// compute available per event: net = paid revenue - commission - featured_commission - withdrawals(approved/paid)
		$rows = db()->prepare('SELECT e.id, e.title, e.is_featured, e.featured_commission, COALESCE(SUM(CASE WHEN o.status="paid" THEN oi.quantity*oi.unit_price ELSE 0 END),0) AS gross FROM events e LEFT JOIN order_items oi ON oi.event_id = e.id LEFT JOIN orders o ON o.id = oi.order_id WHERE e.organizer_id = ? GROUP BY e.id, e.title, e.is_featured, e.featured_commission ORDER BY e.created_at DESC');
		$rows->execute([$organizerId]);
		$events = $rows->fetchAll();
		$balances = [];
		foreach ($events as $ev) {
			$evId = (int)$ev['id'];
			$gross = (float)($ev['gross'] ?? 0);
			$commissionDue = $gross * $commissionRate;
			
			// Add featured commission if event is featured
			$featuredCommissionDue = 0;
			if ($ev['is_featured'] && $ev['featured_commission'] > 0) {
				$featuredCommissionDue = $gross * ((float)$ev['featured_commission'] / 100.0);
			}
			
			$withdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id='.(int)$organizerId.' AND (event_id='.(int)$evId.' OR event_id IS NULL) AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
			$balances[$evId] = max(0, $gross - $commissionDue - $featuredCommissionDue - $withdrawn);
		}
		$overallGross = array_sum(array_column($events, 'gross'));
		$overallCommission = $overallGross * $commissionRate;
		
		// Calculate overall featured commission
		$overallFeaturedCommission = 0;
		foreach ($events as $ev) {
			if ($ev['is_featured'] && $ev['featured_commission'] > 0) {
				$gross = (float)($ev['gross'] ?? 0);
				$overallFeaturedCommission += $gross * ((float)$ev['featured_commission'] / 100.0);
			}
		}
		
		$overallWithdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id='.(int)$organizerId.' AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
		$overallAvailable = max(0, $overallGross - $overallCommission - $overallFeaturedCommission - $overallWithdrawn);
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
		
		// Check if payment information is complete and verified
		$paymentInfo = db()->prepare('SELECT payout_method, payment_info_verified FROM organizers WHERE id = ?');
		$paymentInfo->execute([$organizerId]);
		$payment = $paymentInfo->fetch();
		
		if (empty($payment['payout_method'])) {
			flash_set('error', 'Please set up your payment information in your profile before requesting withdrawals.');
			redirect(base_url('/organizer/profile'));
			return;
		}
		
		if (!($payment['payment_info_verified'] ?? 0)) {
			flash_set('error', 'Your payment information is pending admin verification. Please wait for verification before requesting withdrawals.');
			redirect(base_url('/organizer/withdrawals'));
			return;
		}
		$org = db()->prepare('SELECT commission_percent FROM organizers WHERE id = ?');
		$org->execute([$organizerId]);
		$commissionRate = max(0.0, min(100.0, (float)($org->fetch()['commission_percent'] ?? 0))) / 100.0;
		if ($eventId) {
			$revStmt = db()->prepare('SELECT e.is_featured, e.featured_commission, COALESCE(SUM(CASE WHEN o.status="paid" THEN oi.quantity*oi.unit_price ELSE 0 END),0) AS gross FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id=? AND e.id=? GROUP BY e.is_featured, e.featured_commission');
			$revStmt->execute([$organizerId, $eventId]);
			$eventData = $revStmt->fetch();
			$gross = (float)($eventData['gross'] ?? 0);
			$commissionDue = $gross * $commissionRate;
			
			// Add featured commission if event is featured
			$featuredCommissionDue = 0;
			if ($eventData['is_featured'] && $eventData['featured_commission'] > 0) {
				$featuredCommissionDue = $gross * ((float)$eventData['featured_commission'] / 100.0);
			}
			
			$wq = db()->prepare('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id = ? AND (event_id = ? OR event_id IS NULL) AND status IN ("approved","paid")');
			$wq->execute([$organizerId, $eventId]);
			$withdrawn = (float)($wq->fetch()['wsum'] ?? 0);
			$available = max(0, $gross - $commissionDue - $featuredCommissionDue - $withdrawn);
		} else {
			$gross = (float)(db()->query('SELECT COALESCE(SUM(oi.quantity*oi.unit_price),0) AS gross FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id='.(int)$organizerId.' AND o.status="paid"')->fetch()['gross'] ?? 0);
			$commissionDue = $gross * $commissionRate;
			
			// Calculate overall featured commission for all events
			$featuredCommissionDue = 0;
			$featuredStmt = db()->prepare('SELECT e.featured_commission, COALESCE(SUM(CASE WHEN o.status="paid" THEN oi.quantity*oi.unit_price ELSE 0 END),0) AS gross FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN events e ON e.id=oi.event_id WHERE e.organizer_id=? AND e.is_featured=1 AND e.featured_commission > 0 GROUP BY e.featured_commission');
			$featuredStmt->execute([$organizerId]);
			$featuredEvents = $featuredStmt->fetchAll();
			foreach ($featuredEvents as $fe) {
				$featuredCommissionDue += (float)$fe['gross'] * ((float)$fe['featured_commission'] / 100.0);
			}
			
			$withdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE organizer_id='.(int)$organizerId.' AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
			$available = max(0, $gross - $commissionDue - $featuredCommissionDue - $withdrawn);
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
			$destDir = __DIR__ . '/../../uploads/avatars';
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
			try { $sms = new \App\Services\Sms(); if ($sms->isConfigured()) { $sms->send($phone, 'Your ShikaTicket OTP: ' . $otp); } } catch (\Throwable $e) {}
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
		try { $sms = new \App\Services\Sms(); if ($sms->isConfigured()) { $sms->send($phone, 'Your ShikaTicket OTP: ' . $otp); } } catch (\Throwable $e) {}
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

	public function savePaymentInfo(): void
	{
		require_organizer();
		verify_csrf();
		
		$payoutMethod = trim($_POST['payout_method'] ?? '');
		$bankName = trim($_POST['bank_name'] ?? '');
		$bankAccountName = trim($_POST['bank_account_name'] ?? '');
		$bankAccountNumber = trim($_POST['bank_account_number'] ?? '');
		$bankCode = trim($_POST['bank_code'] ?? '');
		$mpesaPhone = trim($_POST['mpesa_phone'] ?? '');
		$paypalEmail = trim($_POST['paypal_email'] ?? '');
		$otherPaymentDetails = trim($_POST['other_payment_details'] ?? '');
		
		if (empty($payoutMethod)) {
			flash_set('error', 'Please select a payment method.');
			redirect(base_url('/organizer/profile'));
			return;
		}
		
		// Validate required fields based on payment method
		$validationErrors = [];
		
		switch ($payoutMethod) {
			case 'bank_transfer':
				if (empty($bankName)) $validationErrors[] = 'Bank name is required';
				if (empty($bankAccountName)) $validationErrors[] = 'Account name is required';
				if (empty($bankAccountNumber)) $validationErrors[] = 'Account number is required';
				if (empty($bankCode)) $validationErrors[] = 'Bank code is required';
				break;
			case 'mpesa':
				if (empty($mpesaPhone)) $validationErrors[] = 'M-Pesa phone number is required';
				break;
			case 'paypal':
				if (empty($paypalEmail)) $validationErrors[] = 'PayPal email is required';
				elseif (!filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) $validationErrors[] = 'Invalid PayPal email format';
				break;
			case 'other':
				if (empty($otherPaymentDetails)) $validationErrors[] = 'Payment details are required';
				break;
		}
		
		if (!empty($validationErrors)) {
			flash_set('error', 'Validation errors: ' . implode(', ', $validationErrors));
			redirect(base_url('/organizer/profile'));
			return;
		}
		
		// Update payment information
		$sql = 'UPDATE organizers SET 
			payout_method = ?, 
			bank_name = ?, 
			bank_account_name = ?, 
			bank_account_number = ?, 
			bank_code = ?, 
			mpesa_phone = ?, 
			paypal_email = ?, 
			other_payment_details = ?, 
			payment_info_updated_at = NOW(),
			payment_info_verified = 0
			WHERE id = ?';
		
		$stmt = db()->prepare($sql);
		$stmt->execute([
			$payoutMethod,
			$bankName,
			$bankAccountName,
			$bankAccountNumber,
			$bankCode,
			$mpesaPhone,
			$paypalEmail,
			$otherPaymentDetails,
			$_SESSION['organizer_id']
		]);
		
		flash_set('success', 'Payment information saved successfully. Admin verification is required before withdrawals.');
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

	public function marketing(): void
	{
		require_organizer();
		view('organizer/marketing');
	}

	public function campaignReports(): void
	{
		require_organizer();
		view('organizer/campaign_reports');
	}

	public function campaignRequest(): void
	{
		require_organizer();
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				$input = json_decode(file_get_contents('php://input'), true);
				
                // Validate required fields
                $required = ['campaign_name', 'event_id', 'target_audience', 'message_content', 'tier', 'payment_method'];
				foreach ($required as $field) {
					if (empty($input[$field])) {
						echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
						return;
					}
				}
				
                // If dates not provided, auto-fill from event date (start = today, end = event_date)
                if (empty($input['start_date']) || empty($input['end_date'])) {
                    $ev = db()->prepare('SELECT event_date FROM events WHERE id = ? AND organizer_id = ?');
                    $ev->execute([(int)$input['event_id'], (int)$_SESSION['organizer_id']]);
                    $row = $ev->fetch();
                    $today = date('Y-m-d');
                    $eventDate = $row && !empty($row['event_date']) ? date('Y-m-d', strtotime($row['event_date'])) : $today;
                    // start = max(today, today); end = max(start, eventDate)
                    $input['start_date'] = $input['start_date'] ?? $today;
                    if (strtotime($input['start_date']) > strtotime($eventDate)) { $eventDate = $input['start_date']; }
                    $input['end_date'] = $input['end_date'] ?? $eventDate;
                }

                // Get pricing for the tier
				$stmt = db()->prepare('SELECT price_per_sms, max_messages FROM marketing_pricing_settings WHERE tier_name = ? AND account_type = "organizer"');
				$stmt->execute([$input['package_name'] ?? $input['tier']]);
				$pricing = $stmt->fetch();
				
				if (!$pricing) {
					echo json_encode(['success' => false, 'message' => 'Invalid campaign tier']);
					return;
				}
				
				// Calculate package cost (maximum messages × price per SMS)
				$maxMessages = $pricing['max_messages'] ?: 10000; // Default to 10000 if unlimited
				$totalCost = $maxMessages * $pricing['price_per_sms'];
				
                // Insert campaign request with payment info
                $stmt = db()->prepare('
                    INSERT INTO marketing_campaign_requests 
                    (account_type, account_id, campaign_name, event_id, target_audience, message_content, 
                     start_date, end_date, budget, tier, notes, payment_method, calculated_cost, estimated_messages, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", NOW())
                ');
                
                $stmt->execute([
                    'organizer',
                    $_SESSION['organizer_id'],
                    $input['campaign_name'],
                    $input['event_id'],
                    $input['target_audience'],
                    $input['message_content'],
                    $input['start_date'],
                    $input['end_date'],
                    $totalCost,
                    $input['package_name'] ?? $input['tier'],
                    $input['notes'] ?? null,
                    $input['payment_method'],
                    $totalCost,
                    $maxMessages
                ]);
				
				$requestId = db()->lastInsertId();
				
				// Notify organizer that request was created (before payment)
				try {
					$sms = new \App\Services\Sms();
					if ($sms->isConfigured()) {
						$u = db()->prepare('SELECT phone FROM organizers WHERE id = ?');
						$u->execute([$_SESSION['organizer_id']]);
						$phone = $u->fetch()['phone'] ?? '';
						if ($phone !== '') {
							$sms->send($phone, 'Your marketing campaign request #' . $requestId . ' was received. Complete payment to start processing.');
						}
					}
				} catch (\Throwable $e) {}
				
				// Create payment URL based on payment method
				$paymentUrl = $this->createPaymentUrl($input['payment_method'], $totalCost, $requestId, 'campaign_request');
				if (!empty($input['payer_phone'])) {
					$paymentUrl .= '&msisdn=' . urlencode($input['payer_phone']);
				}
				
				echo json_encode([
					'success' => true, 
					'message' => 'Campaign request submitted successfully',
					'payment_required' => true,
					'payment_url' => $paymentUrl,
					'amount' => $totalCost,
					'request_id' => $requestId
				]);
				
			} catch (Exception $e) {
				echo json_encode(['success' => false, 'message' => 'Error submitting campaign request: ' . $e->getMessage()]);
			}
		} else {
			http_response_code(405);
			echo json_encode(['success' => false, 'message' => 'Method not allowed']);
		}
	}

	private function createPaymentUrl(string $paymentMethod, float $amount, int $requestId, string $type): string
	{
		$baseUrl = base_url('/pay');
		
		switch ($paymentMethod) {
			case 'mpesa':
				return $baseUrl . '/mpesa?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
			case 'flutterwave':
				return $baseUrl . '/flutterwave?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
			case 'paypal':
				return $baseUrl . '/paypal?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
			default:
				return $baseUrl . '/mpesa?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
		}
	}

	public function eventsApi(): void
	{
		require_organizer();
		
		try {
			$stmt = db()->prepare('
				SELECT id, title, event_date, is_published, created_at 
				FROM events 
				WHERE organizer_id = ? AND is_published IN (0, 1)
				ORDER BY event_date DESC, created_at DESC
			');
			$stmt->execute([$_SESSION['organizer_id']]);
			$events = $stmt->fetchAll();
			
			header('Content-Type: application/json');
			echo json_encode([
				'success' => true,
				'events' => $events
			]);
		} catch (Exception $e) {
			header('Content-Type: application/json');
			echo json_encode([
				'success' => false,
				'message' => 'Error fetching events: ' . $e->getMessage()
			]);
			}

	}

    // Create reach-based marketing order and redirect to payment
    public function marketingOrder(): void
    {
        require_organizer();
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $eventId = (int)($input['event_id'] ?? 0);
            $reach = (int)($input['reach'] ?? 0);
            $channel = $input['channel'] ?? 'both';
            $mixEmail = (int)($input['mix_email_percent'] ?? 0);
            if ($channel === 'sms') { $mixEmail = 0; }
            elseif ($channel === 'email') { $mixEmail = 100; }
            $mixEmail = max(0, min(100, $mixEmail));
            $unitSms = (float)($input['unit_price_sms'] ?? 0);
            $unitEmail = (float)($input['unit_price_email'] ?? 0);
            $paymentMethod = $input['payment_method'] ?? 'mpesa';
            $audienceType = $input['audience_type'] ?? 'recommend';
            $payerPhone = preg_replace('/\D+/', '', (string)($input['payer_phone'] ?? ''));
            if ($eventId<=0 || $reach<=0) { echo json_encode(['success'=>false,'message'=>'Invalid request']); return; }
            $smsContacts = max(0, $reach - (int)round($reach*$mixEmail/100));
            $emailContacts = (int)round($reach*$mixEmail/100);
            $total = ($smsContacts*$unitSms) + ($emailContacts*$unitEmail);
            $stmt = db()->prepare('INSERT INTO marketing_orders (account_type,account_id,item_type,item_id,reach,unit_price_sms,unit_price_email,mix_email_percent,total_cost,payment_method,last_log) VALUES ("organizer", ?, "event", ?, ?, ?, ?, ?, ?, ?, ?)');
            $lastLog = json_encode(['audience_type'=>$audienceType]);
            $stmt->execute([$_SESSION['organizer_id'],$eventId,$reach,$unitSms,$unitEmail,$mixEmail,$total,$paymentMethod,$lastLog]);
            $orderId = (int)db()->lastInsertId();
            $payUrl = base_url('/pay/'.$paymentMethod.'?type=marketing_order&order_id='.$orderId.'&amount='.$total);
            if ($payerPhone !== '') { $payUrl .= '&msisdn=' . urlencode($payerPhone); }
            echo json_encode(['success'=>true,'payment_url'=>$payUrl,'order_id'=>$orderId]);
        } catch (\Exception $e) {
            echo json_encode(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
        }
    }

    public function campaignDetailsApi(): void
    {
        require_organizer();
        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid campaign id']); return; }
        try {
            // Ensure the campaign belongs to this organizer
            $own = db()->prepare('SELECT * FROM marketing_campaigns WHERE id = ? AND organizer_id = ?');
            $own->execute([$id, (int)($_SESSION['organizer_id'] ?? 0)]);
            $camp = $own->fetch();
            if (!$camp) { echo json_encode(['success'=>false,'message'=>'Campaign not found']); return; }

            // Aggregate metrics
            $stats = db()->prepare('SELECT delivery_type, delivery_status, COUNT(*) as count FROM campaign_deliveries WHERE campaign_id = ? GROUP BY delivery_type, delivery_status');
            $stats->execute([$id]);
            $byType = $stats->fetchAll();

            $recent = db()->prepare('SELECT delivery_type, delivery_status, user_id, sent_at FROM campaign_deliveries WHERE campaign_id = ? ORDER BY sent_at DESC LIMIT 50');
            $recent->execute([$id]);
            $recentDel = $recent->fetchAll();

            $conv = db()->prepare('SELECT id, user_id, conversion_value, conversion_time FROM campaign_conversions WHERE campaign_id = ? ORDER BY conversion_time DESC LIMIT 50');
            $conv->execute([$id]);
            $recentConv = $conv->fetchAll();

            echo json_encode(['success'=>true,'campaign'=>[
                'id'=>$camp['id'], 'name'=>$camp['name'], 'status'=>$camp['status'], 'created_at'=>$camp['created_at']
            ], 'breakdown'=>$byType, 'recent_deliveries'=>$recentDel, 'recent_conversions'=>$recentConv]);
        } catch (\Throwable $e) {
            echo json_encode(['success'=>false,'message'=>'Server error: '.$e->getMessage()]);
        }
    }
}



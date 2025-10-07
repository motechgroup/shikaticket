<?php
namespace App\Controllers;
use App\Models\Organizer;
use App\Models\Setting;

class AdminController
{
	public function index(): void
	{
		require_admin();
        // Finance summary (system-owned only)
        $systemOrganizerId = $this->getOrCreateAdminOrganizerId();
        $systemAgencyId = $this->getOrCreateAdminAgencyId();

        // System events revenue via orders/order_items
        $orders = db()->query(
            "SELECT COUNT(DISTINCT ord.id) AS total_orders, SUM(oi.quantity*oi.unit_price) AS gross
             FROM orders ord
             JOIN order_items oi ON oi.order_id = ord.id
             JOIN events e ON e.id = oi.event_id
             WHERE ord.status='paid' AND e.organizer_id = " . (int)$systemOrganizerId
        )->fetch();
        $byCurrency = db()->query(
            "SELECT ord.currency, SUM(oi.quantity*oi.unit_price) AS gross
             FROM orders ord
             JOIN order_items oi ON oi.order_id = ord.id
             JOIN events e ON e.id = oi.event_id
             WHERE ord.status='paid' AND e.organizer_id = " . (int)$systemOrganizerId .
             " GROUP BY ord.currency"
        )->fetchAll();

        // System travel revenue (paid bookings for admin agency)
        $systemTravel = [ 'bookings' => 0, 'revenue' => 0.0 ];
        try {
            $row = db()->query(
                "SELECT COUNT(*) as bookings, COALESCE(SUM(tb.total_amount),0) as revenue
                 FROM travel_bookings tb
                 JOIN travel_destinations td ON td.id = tb.destination_id
                 JOIN travel_payments tp ON tp.booking_id = tb.id
                 WHERE tp.payment_status='paid' AND td.agency_id = " . (int)$systemAgencyId
            )->fetch();
            if ($row) { $systemTravel = [ 'bookings' => (int)$row['bookings'], 'revenue' => (float)$row['revenue'] ]; }
        } catch (\Throwable $e) {}
        $pendingWithdrawals = db()->query("
            SELECT w.*, 
                   o.full_name as organizer_name,
                   ta.company_name as agency_name
            FROM withdrawals w 
            LEFT JOIN organizers o ON o.id = w.organizer_id 
            LEFT JOIN travel_agencies ta ON ta.id = w.travel_agency_id
            WHERE w.status='requested' ORDER BY w.created_at DESC
        ")->fetchAll();
        // Platform revenue from commissions
        $commissionRevenue = 0.0;
        try {
            // Event commissions
            $rows = db()->query("SELECT e.organizer_id, o.commission_percent, SUM(oi.quantity*oi.unit_price) AS gross FROM order_items oi JOIN orders ord ON ord.id=oi.order_id AND ord.status='paid' JOIN events e ON e.id=oi.event_id JOIN organizers o ON o.id=e.organizer_id GROUP BY e.organizer_id, o.commission_percent")->fetchAll();
            foreach ($rows as $r) { $commissionRevenue += ((float)($r['commission_percent'] ?? 0) / 100.0) * (float)($r['gross'] ?? 0); }
            
            // Travel agency commissions
            $travelRows = db()->query("SELECT td.agency_id, ta.commission_percent, SUM(CASE WHEN tp.payment_status='paid' THEN tb.total_amount ELSE 0 END) AS gross FROM travel_destinations td JOIN travel_bookings tb ON tb.destination_id = td.id JOIN travel_payments tp ON tp.booking_id = tb.id JOIN travel_agencies ta ON ta.id = td.agency_id GROUP BY td.agency_id, ta.commission_percent")->fetchAll();
            foreach ($travelRows as $r) { $commissionRevenue += ((float)($r['commission_percent'] ?? 0) / 100.0) * (float)($r['gross'] ?? 0); }
        } catch (\Throwable $e) {}
        
        // Get pending feature requests
        $featureRequests = [];
        try {
            $featureRequests = db()->query("
                SELECT 
                    fr.*,
                    CASE 
                        WHEN fr.content_type = 'event' THEN e.title
                        WHEN fr.content_type = 'travel_destination' THEN td.title
                    END as content_title,
                    CASE 
                        WHEN fr.requester_type = 'organizer' THEN o.full_name
                        WHEN fr.requester_type = 'travel_agency' THEN ta.company_name
                    END as requester_name,
                    CASE 
                        WHEN fr.requester_type = 'organizer' THEN o.email
                        WHEN fr.requester_type = 'travel_agency' THEN ta.email
                    END as requester_email
                FROM feature_requests fr
                LEFT JOIN events e ON fr.content_type = 'event' AND fr.content_id = e.id
                LEFT JOIN travel_destinations td ON fr.content_type = 'travel_destination' AND fr.content_id = td.id
                LEFT JOIN organizers o ON fr.requester_type = 'organizer' AND fr.requester_id = o.id
                LEFT JOIN travel_agencies ta ON fr.requester_type = 'travel_agency' AND fr.requester_id = ta.id
                WHERE fr.status = 'pending'
                ORDER BY fr.created_at DESC
                LIMIT 10
            ")->fetchAll();
        } catch (\Throwable $e) {}
        
        view('admin/index', compact('orders', 'byCurrency', 'pendingWithdrawals', 'commissionRevenue', 'featureRequests', 'systemTravel'));
	}

	public function loginForm(): void
	{
		view('admin/login');
	}

	public function login(): void
	{
		// Admin auth using settings with env fallbacks
		$email = trim($_POST['email'] ?? '');
		$password = $_POST['password'] ?? '';
		$adminEmail = Setting::get('admin.email', getenv('ADMIN_EMAIL') ?: 'admin@example.com');
		$adminPass = Setting::get('admin.password', getenv('ADMIN_PASSWORD') ?: 'admin123');
		if ($email === $adminEmail && $password === $adminPass) {
			$_SESSION['admin'] = $email;
			$_SESSION['admin_id'] = 1; // Set admin ID for tracking assignments
			$_SESSION['role'] = 'admin';
			redirect(base_url('/admin'));
		}
		flash_set('error', 'Invalid admin credentials');
		redirect(base_url('/admin/login'));
	}

	public function profile(): void
	{
		require_admin();
		$email = Setting::get('admin.email', getenv('ADMIN_EMAIL') ?: 'admin@example.com');
		view('admin/profile', compact('email'));
	}

	public function profileUpdate(): void
	{
		require_admin();
		$email = trim($_POST['email'] ?? '');
		$password = trim($_POST['password'] ?? '');
		if ($email !== '') { Setting::set('admin.email', $email); }
		if ($password !== '') { Setting::set('admin.password', $password); }
		flash_set('success', 'Profile updated.');
		redirect(base_url('/admin/profile'));
	}

	public function verifyOrganizerPayment(): void
	{
		require_admin();
		verify_csrf();
		
		$id = (int)($_POST['id'] ?? 0);
		$action = trim($_POST['action'] ?? '');
		
		if ($id > 0 && in_array($action, ['verify', 'reject'])) {
			$verified = $action === 'verify' ? 1 : 0;
			db()->prepare('UPDATE organizers SET payment_info_verified = ? WHERE id = ?')->execute([$verified, $id]);
			
			if ($action === 'verify') {
				flash_set('success', 'Organizer payment information verified.');
			} else {
				flash_set('success', 'Organizer payment information rejected.');
			}
		}
		
		redirect(base_url('/admin/organizers/show?id=' . $id));
	}

	public function verifyTravelAgencyPayment(): void
	{
		require_admin();
		verify_csrf();
		
		$id = (int)($_POST['id'] ?? 0);
		$action = trim($_POST['action'] ?? '');
		
		if ($id > 0 && in_array($action, ['verify', 'reject'])) {
			$verified = $action === 'verify' ? 1 : 0;
			db()->prepare('UPDATE travel_agencies SET payment_info_verified = ? WHERE id = ?')->execute([$verified, $id]);
			
			if ($action === 'verify') {
				flash_set('success', 'Travel agency payment information verified.');
			} else {
				flash_set('success', 'Travel agency payment information rejected.');
			}
		}
		
		redirect(base_url('/admin/travel/agencies/show?id=' . $id));
	}

	public function organizers(): void
	{
		require_admin();
        $organizers = db()->query('SELECT * FROM organizers ORDER BY created_at DESC')->fetchAll();
		view('admin/organizers', compact('organizers'));
	}

    public function exportOrganizers(): void
    {
        require_admin();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=organizers.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Full Name','Email','Phone','Approved','Created']);
        $stmt = db()->query('SELECT id, full_name, email, phone, is_approved, created_at FROM organizers ORDER BY id ASC');
        foreach ($stmt->fetchAll() as $row) {
            fputcsv($out, [$row['id'],$row['full_name'],$row['email'],$row['phone'],(int)$row['is_approved'],$row['created_at']]);
        }
        fclose($out); exit;
    }

	public function approveOrganizer(): void
	{
		require_admin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Organizer::approve($id);
            // Send approval email
            $orgStmt = db()->prepare('SELECT * FROM organizers WHERE id = ?');
            $orgStmt->execute([$id]);
            $org = $orgStmt->fetch();
            if ($org) {
                $mailer = new \App\Services\Mailer();
                $tpl = Setting::get('email.organizer_approved', '');
                if ($tpl) {
                    $html = str_replace('{{full_name}}', htmlspecialchars($org['full_name']), $tpl);
                } else {
                    ob_start(); $fullName = $org['full_name']; include __DIR__ . '/../Views/emails/organizer_approved.php'; $content = ob_get_clean();
                    ob_start(); include __DIR__ . '/../Views/emails/layout.php'; $html = ob_get_clean();
                }
                $mailer->send($org['email'], 'Organizer Approved', $html);
            }
        }
        redirect(base_url('/admin/organizers'));
	}

	public function setOrganizerCommission(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		$commission = (float)($_POST['commission_percent'] ?? 0);
		if ($id > 0) {
			$stmt = db()->prepare('UPDATE organizers SET commission_percent = ? WHERE id = ?');
			$stmt->execute([$commission, $id]);
		}
		flash_set('success', 'Commission updated');
		redirect(base_url('/admin/organizers'));
	}

	public function deleteOrganizer(): void
	{
		require_admin();
		verify_csrf();
		
		$id = (int)($_POST['id'] ?? 0);
		if ($id <= 0) {
			flash_set('error', 'Invalid organizer ID.');
			redirect(base_url('/admin/organizers'));
			return;
		}

		// Check if organizer exists
		$organizer = db()->prepare('SELECT * FROM organizers WHERE id = ?');
		$organizer->execute([$id]);
		$org = $organizer->fetch();
		
		if (!$org) {
			flash_set('error', 'Organizer not found.');
			redirect(base_url('/admin/organizers'));
			return;
		}

		// Check if organizer has any events
		$eventsCount = db()->prepare('SELECT COUNT(*) FROM events WHERE organizer_id = ?');
		$eventsCount->execute([$id]);
		$hasEvents = $eventsCount->fetchColumn() > 0;

		// Start transaction for data integrity
		$db = db();
		$db->beginTransaction();
		
		try {
			if ($hasEvents) {
				// Get all events for this organizer
				$events = db()->prepare('SELECT id FROM events WHERE organizer_id = ?');
				$events->execute([$id]);
				$eventIds = $events->fetchAll(\PDO::FETCH_COLUMN);

				foreach ($eventIds as $eventId) {
					// Get order IDs for this event to clean up payments later
					$orderIdsStmt = db()->prepare('SELECT DISTINCT order_id FROM order_items WHERE event_id = ?');
					$orderIdsStmt->execute([$eventId]);
					$orderIds = $orderIdsStmt->fetchAll(\PDO::FETCH_COLUMN);
					
					// Delete tickets for this event
					db()->prepare('DELETE FROM tickets WHERE order_item_id IN (SELECT id FROM order_items WHERE event_id = ?)')->execute([$eventId]);
					
					// Delete order items for this event
					db()->prepare('DELETE FROM order_items WHERE event_id = ?')->execute([$eventId]);
					
					// Clean up orphaned orders (orders with no items left)
					foreach ($orderIds as $orderId) {
						$remainingItems = db()->prepare('SELECT COUNT(*) FROM order_items WHERE order_id = ?');
						$remainingItems->execute([$orderId]);
						$itemCount = $remainingItems->fetchColumn();
						
						// If order has no more items, delete it and its payments
						if ($itemCount == 0) {
							db()->prepare('DELETE FROM payments WHERE order_id = ?')->execute([$orderId]);
							db()->prepare('DELETE FROM orders WHERE id = ?')->execute([$orderId]);
						}
					}
				}
				
				// Delete all events for this organizer
				db()->prepare('DELETE FROM events WHERE organizer_id = ?')->execute([$id]);
			}

			// Delete scanner devices for this organizer
			try {
				db()->prepare('DELETE FROM event_scanner_assignments WHERE scanner_device_id IN (SELECT id FROM scanner_devices WHERE organizer_id = ?)')->execute([$id]);
				db()->prepare('DELETE FROM scanner_devices WHERE organizer_id = ?')->execute([$id]);
			} catch (\Throwable $e) {
				// Scanner devices table may not exist in older versions
				error_log('Scanner devices cleanup failed: ' . $e->getMessage());
			}

			// Delete organizer tokens
			db()->prepare('DELETE FROM organizer_tokens WHERE organizer_id = ?')->execute([$id]);
			
			// Delete organizer followers
			db()->prepare('DELETE FROM organizer_followers WHERE organizer_id = ?')->execute([$id]);
			
			// Delete withdrawals for this organizer
			db()->prepare('DELETE FROM withdrawals WHERE organizer_id = ?')->execute([$id]);
			
			// Finally, delete the organizer
			db()->prepare('DELETE FROM organizers WHERE id = ?')->execute([$id]);
			
			$db->commit();
			
			// Log the deletion
			try {
				\App\Models\AuditLogger::log(
					'organizer_deleted',
					"Organizer '{$org['full_name']}' (ID: {$id}) was deleted by admin",
					['organizer_id' => $id, 'organizer_name' => $org['full_name'], 'had_events' => $hasEvents],
					'warning'
				);
			} catch (\Throwable $e) {
				error_log('Audit log failed: ' . $e->getMessage());
			}
			
			$eventText = $hasEvents ? ' and all associated events, tickets, and orders' : '';
			flash_set('success', "Organizer '{$org['full_name']}' has been deleted{$eventText}.");
			
		} catch (\Throwable $e) {
			$db->rollBack();
			error_log('Delete organizer error: ' . $e->getMessage());
			error_log('Stack trace: ' . $e->getTraceAsString());
			flash_set('error', 'Failed to delete organizer: ' . $e->getMessage());
		}
		
		redirect(base_url('/admin/organizers'));
	}

	public function users(): void
	{
		require_admin();
        $users = db()->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
		view('admin/users', compact('users'));
	}

    public function exportUsers(): void
    {
        require_admin();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Email','Phone','Created']);
        $stmt = db()->query('SELECT id, email, phone, created_at FROM users ORDER BY id ASC');
        foreach ($stmt->fetchAll() as $row) { fputcsv($out, [$row['id'],$row['email'],$row['phone'],$row['created_at']]); }
        fclose($out); exit;
    }

	public function toggleUser(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		$active = (int)($_POST['is_active'] ?? 1);
		if ($id > 0) {
			db()->prepare('UPDATE users SET is_active = ? WHERE id = ?')->execute([$active, $id]);
		}
		redirect(base_url('/admin/users'));
	}

	public function deleteUser(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
		}
		redirect(base_url('/admin/users'));
	}

	public function toggleOrganizer(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		$active = (int)($_POST['is_active'] ?? 1);
		if ($id > 0) {
			db()->prepare('UPDATE organizers SET is_active = ? WHERE id = ?')->execute([$active, $id]);
		}
		redirect(base_url('/admin/organizers'));
	}

	public function organizerEdit(): void
	{
		require_admin();
		$id = (int)($_GET['id'] ?? 0);
		if ($id > 0) {
			$stmt = db()->prepare('SELECT * FROM organizers WHERE id = ?');
			$stmt->execute([$id]);
			$org = $stmt->fetch();
			if (!$org) { redirect(base_url('/admin/organizers')); }
		} else {
			$org = ['id'=>0,'full_name'=>'','email'=>'','phone'=>'','is_approved'=>0,'is_active'=>1];
		}
		view('admin/organizer_edit', compact('org'));
	}

	public function organizerSave(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		$full = trim($_POST['full_name'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$phone = trim($_POST['phone'] ?? '');
		$approved = isset($_POST['is_approved']) ? 1 : 0;
		$active = isset($_POST['is_active']) ? 1 : 0;
		if ($id > 0) {
			$stmt = db()->prepare('UPDATE organizers SET full_name=?, email=?, phone=?, is_approved=?, is_active=? WHERE id=?');
			$stmt->execute([$full, $email, $phone, $approved, $active, $id]);
		} else {
			$pass = password_hash($_POST['password'] ?? 'changeme123', PASSWORD_DEFAULT);
			$stmt = db()->prepare('INSERT INTO organizers (full_name, phone, email, password_hash, is_approved, is_active) VALUES (?, ?, ?, ?, ?, ?)');
			$stmt->execute([$full, $phone, $email, $pass, $approved, $active]);
		}
		redirect(base_url('/admin/organizers'));
	}

	public function organizerShow(): void
	{
		require_admin();
		$id = (int)($_GET['id'] ?? 0);
		if ($id <= 0) { redirect(base_url('/admin/organizers')); }
		$orgStmt = db()->prepare('SELECT * FROM organizers WHERE id = ?');
		$orgStmt->execute([$id]);
		$organizer = $orgStmt->fetch();
		if (!$organizer) { redirect(base_url('/admin/organizers')); }
		// Events pagination and search
		$ePage = max(1, (int)($_GET['e_page'] ?? 1));
		$eSize = 10; $eOffset = ($ePage - 1) * $eSize; $eQ = trim($_GET['e_q'] ?? '');
		if ($eQ !== '') {
			$sql = 'SELECT * FROM events WHERE organizer_id = ? AND (title LIKE ? OR venue LIKE ?) ORDER BY created_at DESC LIMIT ' . (int)$eSize . ' OFFSET ' . (int)$eOffset;
			$events = db()->prepare($sql);
			$like = '%' . $eQ . '%';
			$events->execute([$id, $like, $like]);
		} else {
			$sql = 'SELECT * FROM events WHERE organizer_id = ? ORDER BY created_at DESC LIMIT ' . (int)$eSize . ' OFFSET ' . (int)$eOffset;
			$events = db()->prepare($sql);
			$events->execute([$id]);
		}
		$events = $events->fetchAll();
		// Payments pagination and search
		$pPage = max(1, (int)($_GET['p_page'] ?? 1)); $pSize = 10; $pOffset = ($pPage - 1) * $pSize; $pQ = trim($_GET['p_q'] ?? '');
		try {
			$pSql = 'SELECT p.* FROM payments p JOIN orders o ON o.id = p.order_id WHERE o.organizer_id = ? ' . ($pQ!==''?'AND (p.provider LIKE ? OR p.status LIKE ?) ':'') . 'ORDER BY p.created_at DESC LIMIT ' . (int)$pSize . ' OFFSET ' . (int)$pOffset;
			$payments = db()->prepare($pSql);
			if ($pQ !== '') { $likep = '%' . $pQ . '%'; $payments->execute([$id, $likep, $likep]); }
			else { $payments->execute([$id]); }
			$payments = $payments->fetchAll();
		} catch (\PDOException $e) {
			$pSql2 = 'SELECT p.* FROM payments p JOIN orders o ON o.id = p.order_id JOIN order_items oi ON oi.order_id = o.id JOIN events e ON e.id = oi.event_id WHERE e.organizer_id = ? ' . ($pQ!==''?'AND (p.provider LIKE ? OR p.status LIKE ?) ':'') . 'ORDER BY p.created_at DESC LIMIT ' . (int)$pSize . ' OFFSET ' . (int)$pOffset;
			$payments = db()->prepare($pSql2);
			if ($pQ !== '') { $likep = '%' . $pQ . '%'; $payments->execute([$id, $likep, $likep]); }
			else { $payments->execute([$id]); }
			$payments = $payments->fetchAll();
		}
		// Revenue summary (successful payments)
		$rev = 0.0;
		foreach ($payments as $p) { if (($p['status'] ?? '') === 'successful') { $rev += (float)($p['amount'] ?? 0); } }
		// Withdrawals history
		$wPage = max(1, (int)($_GET['w_page'] ?? 1)); $wSize = 10; $wOffset = ($wPage-1)*$wSize; $wQ = trim($_GET['w_q'] ?? '');
		try {
			$wSql = 'SELECT * FROM withdrawals WHERE organizer_id = ? ' . ($wQ!==''?'AND status LIKE ? ':'') . 'ORDER BY created_at DESC LIMIT ' . (int)$wSize . ' OFFSET ' . (int)$wOffset;
			$wdStmt = db()->prepare($wSql);
			if ($wQ !== '') { $likew = '%' . $wQ . '%'; $wdStmt->execute([$id, $likew]); }
			else { $wdStmt->execute([$id]); }
			$withdrawals = $wdStmt->fetchAll();
		} catch (\PDOException $e) { $withdrawals = []; }
		$summary = [
			'events_count' => count($events),
			'revenue' => $rev,
		];
		view('admin/organizer_show', compact('organizer', 'events', 'payments', 'withdrawals', 'summary', 'ePage', 'pPage', 'wPage', 'eQ', 'pQ', 'wQ'));
	}

	public function events(): void
	{
		require_admin();
        $events = db()->query('SELECT e.*, o.full_name AS organizer_name FROM events e LEFT JOIN organizers o ON o.id = e.organizer_id ORDER BY e.created_at DESC')->fetchAll();
		view('admin/events', compact('events'));
	}

	public function eventCreate(): void
	{
		require_admin();
		$cats = db()->query('SELECT id, name FROM event_categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC')->fetchAll();
		view('admin/event_create', ['categories' => $cats]);
	}

	public function eventStore(): void
	{
		require_admin();
		$title = trim($_POST['title'] ?? '');
        $venue = trim($_POST['venue'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $eventDate = $_POST['event_date'] ?? null;
        $eventTime = $_POST['event_time'] ?? null;
        $desc = trim($_POST['description'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $regularPrice = isset($_POST['regular_price']) ? (float)$_POST['regular_price'] : null;
        $earlyBirdPrice = isset($_POST['early_bird_price']) ? (float)$_POST['early_bird_price'] : null;
        $earlyBirdUntil = $_POST['early_bird_until'] ?? null;
        $vipPrice = isset($_POST['vip_price']) ? (float)$_POST['vip_price'] : null;
        $vvipPrice = isset($_POST['vvip_price']) ? (float)$_POST['vvip_price'] : null;
        $groupPrice = isset($_POST['group_price']) ? (float)$_POST['group_price'] : null;
        $groupSize = isset($_POST['group_size']) ? (int)$_POST['group_size'] : null;
        $currency = trim($_POST['currency'] ?? 'KES');
        $dressCode = trim($_POST['dress_code'] ?? '');
        $lineup = trim($_POST['lineup'] ?? '');
        // Admin-owned event must be attached to a real organizer due to FK
        $organizerId = $this->getOrCreateAdminOrganizerId();
		$posterPath = null;
		if (!empty($_FILES['poster']['tmp_name'])) {
			$ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
			$destDir = __DIR__ . '/../../public/uploads';
			if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
			$filename = 'poster_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
			$dest = $destDir . '/' . $filename;
			move_uploaded_file($_FILES['poster']['tmp_name'], $dest);
			$posterPath = 'uploads/' . $filename;
		}
        $stmt = db()->prepare('INSERT INTO events (organizer_id, title, description, category, event_date, event_time, venue, poster_path, capacity, regular_price, early_bird_price, early_bird_until, vip_price, vvip_price, group_price, group_size, currency, dress_code, lineup, is_featured, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)');
        $stmt->execute([$organizerId, $title, $desc, $category, $eventDate, $eventTime, $venue, $posterPath, $capacity, $regularPrice, $earlyBirdPrice, $earlyBirdUntil, $vipPrice, $vvipPrice, $groupPrice, $groupSize, $currency, $dressCode, $lineup]);
		flash_set('success', 'Event created.');
		redirect(base_url('/admin/events'));
	}

	private function getOrCreateAdminOrganizerId(): int
	{
		try {
			$row = db()->query("SELECT id FROM organizers WHERE email = 'admin@system.local' LIMIT 1")->fetch();
			if ($row && (int)$row["id"] > 0) { return (int)$row["id"]; }
			$stmt = db()->prepare('INSERT INTO organizers (full_name, phone, email, password_hash, is_approved) VALUES (?, ?, ?, ?, 1)');
			$stmt->execute(['System Organizer', '0000000000', 'admin@system.local', password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT)]);
			return (int)db()->lastInsertId();
		} catch (\PDOException $e) {
			return 1; // Fallback if a seeded organizer exists
		}
	}

	public function banners(): void
	{
		require_admin();
		$banners = db()->query('SELECT * FROM banners ORDER BY sort_order ASC, created_at DESC')->fetchAll();
		view('admin/banners', compact('banners'));
	}

	public function bannerCreate(): void
	{
		require_admin();
		view('admin/banner_create');
	}

	public function bannerStore(): void
	{
		require_admin();
		$title = trim($_POST['title'] ?? '');
		$link = trim($_POST['link_url'] ?? '');
		$imagePath = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            // Validate recommended dimensions 1600x500
            [$w,$h] = @getimagesize($_FILES['image']['tmp_name']) ?: [0,0];
            if ($w < 1200 || $h < 400) {
                flash_set('error', 'Banner should be at least 1600x500 (recommended)');
                redirect(base_url('/admin/banners/create'));
            }
			$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$destDir = __DIR__ . '/../../public/uploads/banners';
			if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
			$filename = 'banner_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
			$dest = $destDir . '/' . $filename;
			move_uploaded_file($_FILES['image']['tmp_name'], $dest);
			$imagePath = 'uploads/banners/' . $filename;
		}
		$stmt = db()->prepare('INSERT INTO banners (title, image_path, link_url, sort_order, is_active) VALUES (?, ?, ?, ?, 1)');
		$stmt->execute([$title, $imagePath, $link, (int)($_POST['sort_order'] ?? 0)]);
		flash_set('success', 'Banner added.');
		redirect(base_url('/admin/banners'));
	}

	public function bannerDelete(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			db()->prepare('DELETE FROM banners WHERE id = ?')->execute([$id]);
		}
		redirect(base_url('/admin/banners'));
	}

    public function bannerEdit(): void
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = db()->prepare('SELECT * FROM banners WHERE id = ?');
        $stmt->execute([$id]);
        $banner = $stmt->fetch();
        if (!$banner) { redirect(base_url('/admin/banners')); }
        view('admin/banner_edit', compact('banner'));
    }

    public function bannerUpdate(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link_url'] ?? '');
        $order = (int)($_POST['sort_order'] ?? 0);
        $setImg = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/banners';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'banner_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            $setImg = ', image_path = ' . db()->quote('uploads/banners/' . $filename);
        }
        $sql = 'UPDATE banners SET title = :title, link_url = :link, sort_order = :ord' . $setImg . ' WHERE id = :id';
        $stmt = db()->prepare($sql);
        $stmt->execute([':title'=>$title, ':link'=>$link, ':ord'=>$order, ':id'=>$id]);
        flash_set('success', 'Banner updated.');
        redirect(base_url('/admin/banners'));
    }

    public function bannerToggle(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $active = (int)($_POST['is_active'] ?? 1);
        if ($id > 0) {
            db()->prepare('UPDATE banners SET is_active = ? WHERE id = ?')->execute([$active, $id]);
        }
        redirect(base_url('/admin/banners'));
    }

	public function eventEdit(): void
	{
		require_admin();
		$id = (int)($_GET['id'] ?? 0);
		$stmt = db()->prepare('SELECT * FROM events WHERE id = ?');
		$stmt->execute([$id]);
		$event = $stmt->fetch();
		if (!$event) { redirect(base_url('/admin/events')); }
		view('admin/event_edit', compact('event'));
	}

	public function eventShow(): void
	{
		require_admin();
		$id = (int)($_GET['id'] ?? 0);
		$stmt = db()->prepare('SELECT e.*, o.full_name AS organizer_name FROM events e LEFT JOIN organizers o ON o.id = e.organizer_id WHERE e.id = ?');
		$stmt->execute([$id]);
		$event = $stmt->fetch();
		if (!$event) { redirect(base_url('/admin/events')); }
		view('admin/event_show', compact('event'));
	}

	public function eventUpdate(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		$title = trim($_POST['title'] ?? '');
		$venue = trim($_POST['venue'] ?? '');
		$category = trim($_POST['category'] ?? '');
		$eventDate = $_POST['event_date'] ?? null;
		$eventTime = $_POST['event_time'] ?? null;
		$desc = trim($_POST['description'] ?? '');
		$posterSet = '';
		if (!empty($_FILES['poster']['tmp_name'])) {
			$ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
			$destDir = __DIR__ . '/../../public/uploads';
			if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
			$filename = 'poster_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
			$dest = $destDir . '/' . $filename;
			move_uploaded_file($_FILES['poster']['tmp_name'], $dest);
			$posterSet = ', poster_path = ' . db()->quote('uploads/' . $filename);
		}
		$sql = 'UPDATE events SET title=:title, venue=:venue, category=:category, event_date=:date, event_time=:time, description=:desc' . $posterSet . ' WHERE id=:id';
		$stmt = db()->prepare($sql);
		$stmt->execute([':title'=>$title, ':venue'=>$venue, ':category'=>$category, ':date'=>$eventDate, ':time'=>$eventTime, ':desc'=>$desc, ':id'=>$id]);
		flash_set('success', 'Event updated.');
		redirect(base_url('/admin/events'));
	}

	public function eventPublish(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		$published = (int)($_POST['published'] ?? 0);
		if ($id > 0) {
			db()->prepare('UPDATE events SET is_published = ? WHERE id = ?')->execute([$published, $id]);
		}
		redirect(base_url('/admin/events'));
	}

	public function eventFeature(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		$featured = (int)($_POST['featured'] ?? 0);
		if ($id > 0) {
			db()->prepare('UPDATE events SET is_featured = ? WHERE id = ?')->execute([$featured, $id]);
		}
		redirect(base_url('/admin/events'));
	}

	public function eventDelete(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			db()->prepare('DELETE FROM events WHERE id = ?')->execute([$id]);
		}
		redirect(base_url('/admin/events'));
	}

	// --- Admin Scanner Device Management (for system-owned events) ---
	public function adminScannerDevices(): void
	{
		require_admin();
		$organizerId = $this->getOrCreateAdminOrganizerId();
		$agencyId = $this->getOrCreateAdminAgencyId();
		$devices = \App\Models\ScannerDevice::findByOrganizer($organizerId);
		$eventsStmt = db()->prepare('SELECT id, title, event_date FROM events WHERE organizer_id = ? ORDER BY event_date DESC');
		$eventsStmt->execute([$organizerId]);
		$events = $eventsStmt->fetchAll();
		$destStmt = db()->prepare('SELECT id, title, departure_date FROM travel_destinations WHERE agency_id = ? ORDER BY departure_date DESC');
		$destStmt->execute([$agencyId]);
		$destinations = $destStmt->fetchAll();
		view('admin/scanner_devices', compact('devices','events','destinations'));
	}

	public function adminCreateScannerDevice(): void
	{
		require_admin(); verify_csrf();
		$name = trim($_POST['device_name'] ?? '');
		if ($name === '') { flash_set('error','Device name required'); redirect(base_url('/admin/scanners')); }
		$organizerId = $this->getOrCreateAdminOrganizerId();
		\App\Models\ScannerDevice::create($organizerId, $name);
		flash_set('success','Scanner created');
		redirect(base_url('/admin/scanners'));
	}

	public function adminUpdateScannerDevice(): void
	{
		require_admin(); verify_csrf();
		$id = (int)($_POST['device_id'] ?? 0);
		$name = trim($_POST['device_name'] ?? '');
		$isActive = isset($_POST['is_active']);
		if ($id <= 0 || $name === '') { flash_set('error','Invalid device'); redirect(base_url('/admin/scanners')); }
		\App\Models\ScannerDevice::update($id, $name, $isActive);
		flash_set('success','Scanner updated');
		redirect(base_url('/admin/scanners'));
	}

	public function adminDeleteScannerDevice(): void
	{
		require_admin(); verify_csrf();
		$id = (int)($_POST['device_id'] ?? 0);
		if ($id > 0) { \App\Models\ScannerDevice::delete($id); }
		flash_set('success','Scanner deleted');
		redirect(base_url('/admin/scanners'));
	}

	public function adminScannerAssignments(): void
	{
		require_admin();
		$organizerId = $this->getOrCreateAdminOrganizerId();
		$events = db()->prepare('SELECT * FROM events WHERE organizer_id = ? AND is_published = 1 ORDER BY event_date DESC');
		$events->execute([$organizerId]);
		$events = $events->fetchAll();
		$devices = \App\Models\ScannerDevice::findByOrganizer($organizerId);
		view('admin/scanner_assignments', compact('events','devices'));
	}

	public function adminAssignScannerToEvent(): void
	{
		require_admin(); verify_csrf();
		$scannerId = (int)($_POST['scanner_id'] ?? 0);
		$eventId = (int)($_POST['event_id'] ?? 0);
		if ($scannerId <= 0 || $eventId <= 0) { redirect(base_url('/admin/scanners/assignments')); }
        // Include organizer_id to satisfy FK constraints
        $organizerId = $this->getOrCreateAdminOrganizerId();
        // Upsert to avoid duplicate key on unique_event_scanner (scanner_device_id, event_id)
        $sql = 'INSERT INTO event_scanner_assignments (scanner_device_id, event_id, organizer_id, is_active, assigned_at) VALUES (?, ?, ?, 1, NOW()) 
                ON DUPLICATE KEY UPDATE is_active = VALUES(is_active), assigned_at = VALUES(assigned_at), organizer_id = VALUES(organizer_id)';
        $stmt = db()->prepare($sql);
        $stmt->execute([$scannerId, $eventId, $organizerId]);
        flash_set('success','Scanner linked to event');
		redirect(base_url('/admin/scanners/assignments'));
	}

	public function adminUnassignScannerFromEvent(): void
	{
		require_admin(); verify_csrf();
		$scannerId = (int)($_POST['scanner_id'] ?? 0);
		$eventId = (int)($_POST['event_id'] ?? 0);
		if ($scannerId <= 0 || $eventId <= 0) { redirect(base_url('/admin/scanners/assignments')); }
		$stmt = db()->prepare('UPDATE event_scanner_assignments SET is_active = 0 WHERE scanner_device_id = ? AND event_id = ?');
		$stmt->execute([$scannerId, $eventId]);
		flash_set('success','Scanner unassigned');
		redirect(base_url('/admin/scanners/assignments'));
	}

	// --- Admin Travel Scanners for system destinations ---
	public function adminTravelScanners(): void
	{
		require_admin();
		$agencyId = $this->getOrCreateAdminAgencyId();
		$rows = db()->prepare('SELECT * FROM travel_scanner_devices WHERE travel_agency_id = ? ORDER BY created_at DESC');
		$rows->execute([$agencyId]);
		$devices = $rows->fetchAll();
		view('admin/travel_scanner_devices', compact('devices'));
	}

	public function adminCreateTravelScanner(): void
	{
		require_admin(); verify_csrf();
		$name = trim($_POST['device_name'] ?? '');
		if ($name === '') { flash_set('error','Device name required'); redirect(base_url('/admin/travel/scanners')); }
		$agencyId = $this->getOrCreateAdminAgencyId();
		$code = strtoupper(substr(md5(uniqid()),0,8));
		db()->prepare('INSERT INTO travel_scanner_devices (travel_agency_id, device_name, device_code, is_active) VALUES (?, ?, ?, 1)')->execute([$agencyId, $name, $code]);
		flash_set('success','Travel scanner created');
		redirect(base_url('/admin/travel/scanners'));
	}

	public function adminUpdateTravelScanner(): void
	{
		require_admin(); verify_csrf();
		$id = (int)($_POST['device_id'] ?? 0);
		$name = trim($_POST['device_name'] ?? '');
		$isActive = isset($_POST['is_active']) ? 1 : 0;
		if ($id <= 0 || $name === '') { redirect(base_url('/admin/travel/scanners')); }
		db()->prepare('UPDATE travel_scanner_devices SET device_name = ?, is_active = ? WHERE id = ?')->execute([$name, $isActive, $id]);
		flash_set('success','Travel scanner updated');
		redirect(base_url('/admin/travel/scanners'));
	}

	public function adminDeleteTravelScanner(): void
	{
		require_admin(); verify_csrf();
		$id = (int)($_POST['device_id'] ?? 0);
		if ($id > 0) { db()->prepare('DELETE FROM travel_scanner_devices WHERE id = ?')->execute([$id]); }
		flash_set('success','Travel scanner deleted');
		redirect(base_url('/admin/travel/scanners'));
	}

	public function adminTravelScannerAssignments(): void
	{
		require_admin();
		$agencyId = $this->getOrCreateAdminAgencyId();
		$destinations = db()->prepare('SELECT id, title, departure_date FROM travel_destinations WHERE agency_id = ? AND is_published = 1 ORDER BY departure_date DESC');
		$destinations->execute([$agencyId]);
		$destinations = $destinations->fetchAll();
		$devices = db()->prepare('SELECT * FROM travel_scanner_devices WHERE travel_agency_id = ? AND is_active = 1 ORDER BY created_at DESC');
		$devices->execute([$agencyId]);
		$devices = $devices->fetchAll();
		view('admin/travel_scanner_assignments', compact('destinations','devices'));
	}

	public function adminAssignTravelScannerToDestination(): void
	{
		require_admin(); verify_csrf();
		$scannerId = (int)($_POST['scanner_id'] ?? 0);
		$destinationId = (int)($_POST['destination_id'] ?? 0);
		if ($scannerId <= 0 || $destinationId <= 0) { redirect(base_url('/admin/travel/scanners/assignments')); }
        db()->prepare('INSERT INTO travel_scanner_assignments (scanner_device_id, destination_id, is_active, assigned_at) VALUES (?, ?, 1, NOW()) 
                       ON DUPLICATE KEY UPDATE is_active=VALUES(is_active), assigned_at=VALUES(assigned_at)')->execute([$scannerId, $destinationId]);
        flash_set('success','Scanner linked to destination');
		redirect(base_url('/admin/travel/scanners/assignments'));
	}

	public function adminUnassignTravelScannerFromDestination(): void
	{
		require_admin(); verify_csrf();
		$scannerId = (int)($_POST['scanner_id'] ?? 0);
		$destinationId = (int)($_POST['destination_id'] ?? 0);
		if ($scannerId <= 0 || $destinationId <= 0) { redirect(base_url('/admin/travel/scanners/assignments')); }
		db()->prepare('UPDATE travel_scanner_assignments SET is_active = 0 WHERE scanner_device_id = ? AND destination_id = ?')->execute([$scannerId, $destinationId]);
		flash_set('success','Scanner unassigned');
		redirect(base_url('/admin/travel/scanners/assignments'));
	}

	public function scans(): void
	{
		require_admin();
		$scans = db()->query('SELECT t.code, t.tier, t.redeemed_at, o.full_name AS organizer, e.title AS event_title, e.venue, sd.device_name, sd.device_code FROM tickets t JOIN order_items oi ON oi.id=t.order_item_id JOIN events e ON e.id=oi.event_id LEFT JOIN organizers o ON o.id=t.redeemed_by LEFT JOIN scanner_devices sd ON sd.id=t.scanner_device_id WHERE t.status="redeemed" ORDER BY t.redeemed_at DESC')->fetchAll();
		view('admin/scans', compact('scans'));
	}

    // --- Travel Module ---
    public function travelAgencies(): void
    {
        require_admin();
        try {
            $agencies = db()->query('SELECT * FROM travel_agencies ORDER BY created_at DESC')->fetchAll();
        } catch (\PDOException $e) { $agencies = []; }
        view('admin/travel_agencies', compact('agencies'));
    }

    public function exportTravelAgencies(): void
    {
        require_admin();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=travel_agencies.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Company','Email','Phone','Approved','Created']);
        $stmt = db()->query('SELECT id, company_name, email, phone, is_approved, created_at FROM travel_agencies ORDER BY id ASC');
        foreach ($stmt->fetchAll() as $row) { fputcsv($out, [$row['id'],$row['company_name'],$row['email'],$row['phone'],(int)$row['is_approved'],$row['created_at']]); }
        fclose($out); exit;
    }

    public function approveTravelAgency(): void
    {
        require_admin(); verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            db()->prepare('UPDATE travel_agencies SET is_approved = 1 WHERE id = ?')->execute([$id]);
            
            // Send approval notification email
            try {
                $agencyStmt = db()->prepare('SELECT * FROM travel_agencies WHERE id = ?');
                $agencyStmt->execute([$id]);
                $agency = $agencyStmt->fetch();
                
                if ($agency) {
                    $siteName = \App\Models\Setting::get('site.name', 'ShikaTicket');
                    $loginUrl = base_url('/travel/login');
                    
                    $html = \App\Services\EmailTemplates::render('travel_agency_approved', [
                        'name' => $agency['contact_person'],
                        'company_name' => $agency['company_name'],
                        'email' => $agency['email'],
                        'site_name' => $siteName,
                        'login_url' => $loginUrl
                    ]);
                    
                    if ($html !== '') {
                        $subject = "🎉 Congratulations! Your Travel Agency Has Been Approved - {$siteName}";
                        $mailer = new \App\Services\Mailer();
                        if ($mailer->isConfigured()) {
                            $emailSent = $mailer->send($agency['email'], $subject, $html);
                            if ($emailSent) {
                                error_log('Travel agency approval email sent successfully to: ' . $agency['email']);
                            } else {
                                error_log('Travel agency approval email failed to send to: ' . $agency['email']);
                            }
                        } else {
                            error_log('Email service not configured for travel agency approval notification');
                        }
                    }
                }
            } catch (\Throwable $e) {
                error_log('Travel agency approval email sending error: ' . $e->getMessage());
            }
            
            flash_set('success', 'Agency approved and notification sent.');
        }
        redirect(base_url('/admin/travel/agencies'));
    }

    public function verifyTravelAgencyPhone(): void
    {
        require_admin(); verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            db()->prepare('UPDATE travel_agencies SET phone_verified = 1 WHERE id = ?')->execute([$id]);
            flash_set('success', 'Agency phone marked as verified.');
        }
        redirect(base_url('/admin/travel/agencies'));
    }

    public function setTravelAgencyCommission(): void
    {
        require_admin(); verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $rate = (float)($_POST['commission_rate'] ?? 0);
        if ($id > 0) {
            db()->prepare('UPDATE travel_agencies SET commission_rate = ? WHERE id = ?')->execute([$rate, $id]);
            flash_set('success', 'Commission updated.');
        }
        redirect(base_url('/admin/travel/agencies'));
    }

    public function toggleTravelAgency(): void
    {
        require_admin();
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $isActive = (int)($_POST['is_active'] ?? 0);
        if ($id > 0) {
            db()->prepare('UPDATE travel_agencies SET is_active = ? WHERE id = ?')->execute([$isActive, $id]);
            flash_set('success', 'Travel agency status updated.');
        }
		redirect(base_url('/admin/travel/agencies/show?id=' . $id));
	}

	public function deleteTravelAgency(): void
	{
		require_admin();
		verify_csrf();
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			// Get agency details before deletion for logging
			$stmt = db()->prepare('SELECT company_name, email FROM travel_agencies WHERE id = ?');
			$stmt->execute([$id]);
			$agency = $stmt->fetch();

			// Delete the agency
			db()->prepare('DELETE FROM travel_agencies WHERE id = ?')->execute([$id]);

			// Log the action
			if ($agency) {
				\App\Models\AuditLogger::log('travel_agency_delete', 'Travel agency deleted', [
					'agency_id' => $id,
					'company_name' => $agency['company_name'],
					'email' => $agency['email']
				], 'warning');
			}

			flash_set('success', 'Travel agency deleted successfully.');
		}
		redirect(base_url('/admin/travel/agencies'));
	}

	public function createAccounts(): void
	{
		require_admin();
		view('admin/create_accounts');
	}

	public function createUser(): void
	{
		require_admin();
		verify_csrf();
		
		$fullName = trim($_POST['full_name'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$phone = trim($_POST['phone'] ?? '');
		$password = trim($_POST['password'] ?? '');
		$isActive = (int)($_POST['is_active'] ?? 1);
		$sendEmail = isset($_POST['send_email']);
		$sendSms = isset($_POST['send_sms']);
		
		if (empty($fullName) || empty($email)) {
			flash_set('error', 'Full name and email are required.');
			redirect(base_url('/admin/accounts/create'));
			return;
		}
		
		// Check if email already exists
		$existingUser = db()->prepare('SELECT id FROM users WHERE email = ?');
		$existingUser->execute([$email]);
		if ($existingUser->fetch()) {
			flash_set('error', 'A user with this email already exists.');
			redirect(base_url('/admin/accounts/create'));
			return;
		}
		
		// Split full name into first and last name
		$fullName = trim($fullName); // Clean the input first
		$nameParts = explode(' ', $fullName, 2);
		$firstName = trim($nameParts[0]);
		$lastName = isset($nameParts[1]) ? trim($nameParts[1]) : '';
		
		// Generate password if not provided
		if (empty($password)) {
			$password = $this->generateSecurePassword();
		}
		
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		
		// Create user
		$stmt = db()->prepare('INSERT INTO users (first_name, last_name, email, phone, password_hash, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
		$stmt->execute([$firstName, $lastName, $email, $phone, $passwordHash, $isActive]);
		
		$userId = db()->lastInsertId();
		
		// Send credentials
		$this->sendUserCredentials($userId, $email, $password, $phone, $sendEmail, $sendSms);
		
		flash_set('success', 'User account created successfully. Credentials have been sent.');
		redirect(base_url('/admin/accounts/create'));
	}

	public function createOrganizer(): void
	{
		require_admin();
		verify_csrf();
		
		$fullName = trim($_POST['full_name'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$phone = trim($_POST['phone'] ?? '');
		$password = trim($_POST['password'] ?? '');
		$commissionPercent = (float)($_POST['commission_percent'] ?? 10.0);
		$isApproved = (int)($_POST['is_approved'] ?? 1);
		$sendEmail = isset($_POST['send_email']);
		$sendSms = isset($_POST['send_sms']);
		
		if (empty($fullName) || empty($email) || empty($phone)) {
			flash_set('error', 'Full name, email, and phone are required.');
			redirect(base_url('/admin/accounts/create'));
			return;
		}
		
		// Check if email already exists
		$existingOrg = db()->prepare('SELECT id FROM organizers WHERE email = ?');
		$existingOrg->execute([$email]);
		if ($existingOrg->fetch()) {
			flash_set('error', 'An organizer with this email already exists.');
			redirect(base_url('/admin/accounts/create'));
			return;
		}
		
		// Generate password if not provided
		if (empty($password)) {
			$password = $this->generateSecurePassword();
		}
		
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		
		// Create organizer
		$stmt = db()->prepare('INSERT INTO organizers (full_name, email, phone, password_hash, commission_percent, is_approved, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
		$stmt->execute([$fullName, $email, $phone, $passwordHash, $commissionPercent, $isApproved]);
		
		$organizerId = db()->lastInsertId();
		
		// Send credentials
		$this->sendOrganizerCredentials($organizerId, $email, $password, $phone, $sendEmail, $sendSms);
		
		flash_set('success', 'Organizer account created successfully. Credentials have been sent.');
		redirect(base_url('/admin/accounts/create'));
	}

	public function createTravelAgency(): void
	{
		require_admin();
		verify_csrf();
		
		$companyName = trim($_POST['company_name'] ?? '');
		$contactPerson = trim($_POST['contact_person'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$phone = trim($_POST['phone'] ?? '');
		$password = trim($_POST['password'] ?? '');
		$website = trim($_POST['website'] ?? '');
		$city = trim($_POST['city'] ?? '');
		$country = trim($_POST['country'] ?? '');
		$commissionRate = (float)($_POST['commission_rate'] ?? 10.0);
		$description = trim($_POST['description'] ?? '');
		$isApproved = (int)($_POST['is_approved'] ?? 1);
		$sendEmail = isset($_POST['send_email']);
		$sendSms = isset($_POST['send_sms']);
		
		if (empty($companyName) || empty($contactPerson) || empty($email) || empty($phone)) {
			flash_set('error', 'Company name, contact person, email, and phone are required.');
			redirect(base_url('/admin/accounts/create'));
			return;
		}
		
		// Check if email already exists
		$existingAgency = db()->prepare('SELECT id FROM travel_agencies WHERE email = ?');
		$existingAgency->execute([$email]);
		if ($existingAgency->fetch()) {
			flash_set('error', 'A travel agency with this email already exists.');
			redirect(base_url('/admin/accounts/create'));
			return;
		}
		
		// Generate password if not provided
		if (empty($password)) {
			$password = $this->generateSecurePassword();
		}
		
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		
		// Handle logo upload
		$logoPath = null;
		if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
			$uploadDir = __DIR__ . '/../../public/uploads/travel/agencies/';
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0777, true);
			}
			
			$extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
			$filename = 'agency_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
			$uploadPath = $uploadDir . $filename;
			
			if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
				$logoPath = 'uploads/travel/agencies/' . $filename;
			}
		}
		
		// Create travel agency
		$stmt = db()->prepare('INSERT INTO travel_agencies (company_name, contact_person, email, phone, password_hash, website, city, country, description, commission_rate, is_approved, logo_path, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
		$stmt->execute([$companyName, $contactPerson, $email, $phone, $passwordHash, $website, $city, $country, $description, $commissionRate, $isApproved, $logoPath]);
		
		$agencyId = db()->lastInsertId();
		
		// Send credentials
		$this->sendTravelAgencyCredentials($agencyId, $email, $password, $phone, $sendEmail, $sendSms);
		
		flash_set('success', 'Travel agency account created successfully. Credentials have been sent.');
		redirect(base_url('/admin/accounts/create'));
	}

	private function generateSecurePassword(): string
	{
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
		$password = '';
		$length = 12;
		
		for ($i = 0; $i < $length; $i++) {
			$password .= $chars[rand(0, strlen($chars) - 1)];
		}
		
		return $password;
	}

	private function processTemplate($template, $variables): string
	{
		$processed = $template;
		foreach ($variables as $key => $value) {
			$processed = str_replace('{{' . $key . '}}', $value, $processed);
		}
		return $processed;
	}

	private function sendUserCredentials($userId, $email, $password, $phone, $sendEmail, $sendSms): void
	{
		$siteName = \App\Models\Setting::get('site.name', 'Ticko');
		$loginUrl = base_url('/login');
		
		// Send email
		if ($sendEmail) {
			try {
				// Get email template
				$template = db()->prepare('SELECT message FROM communication_templates WHERE name = ? AND is_active = 1');
				$template->execute(['new_user_credentials']);
				$templateData = $template->fetch();
				
				if ($templateData) {
					$body = $this->processTemplate($templateData['message'], [
						'site_name' => $siteName,
						'email' => $email,
						'password' => $password,
						'login_url' => $loginUrl
					]);
				} else {
					// Fallback to hardcoded message
					$body = "Welcome to {$siteName}!\n\nYour account has been created with the following credentials:\n\nEmail: {$email}\nPassword: {$password}\n\nYou can log in at: {$loginUrl}\n\nPlease change your password after your first login for security.\n\nBest regards,\n{$siteName} Team";
				}
				
				$subject = "Welcome to {$siteName} - Your Account Credentials";
				
				$mail = new \App\Services\Mail();
				if ($mail->isConfigured()) {
					$mail->send($email, $subject, $body);
				}
			} catch (\Throwable $e) {
				// Log error but don't fail the account creation
			}
		}
		
		// Send SMS
		if ($sendSms && !empty($phone)) {
			try {
				// Get SMS template
				$template = db()->prepare('SELECT message FROM communication_templates WHERE name = ? AND is_active = 1');
				$template->execute(['new_user_sms_credentials']);
				$templateData = $template->fetch();
				
				if ($templateData) {
					$message = $this->processTemplate($templateData['message'], [
						'site_name' => $siteName,
						'email' => $email,
						'password' => $password,
						'login_url' => $loginUrl
					]);
				} else {
					// Fallback to hardcoded message
					$message = "Welcome to {$siteName}! Your account is ready. Email: {$email}, Password: {$password}. Login: {$loginUrl}";
				}
				
				$sms = new \App\Services\Sms();
				if ($sms->isConfigured()) {
					$sms->send($phone, $message);
				}
			} catch (\Throwable $e) {
				// Log error but don't fail the account creation
			}
		}
	}

	private function sendOrganizerCredentials($organizerId, $email, $password, $phone, $sendEmail, $sendSms): void
	{
		$siteName = \App\Models\Setting::get('site.name', 'Ticko');
		$loginUrl = base_url('/organizer/login');
		
		// Send email
		if ($sendEmail) {
			try {
				// Get email template
				$template = db()->prepare('SELECT message FROM communication_templates WHERE name = ? AND is_active = 1');
				$template->execute(['new_organizer_credentials']);
				$templateData = $template->fetch();
				
				if ($templateData) {
					$body = $this->processTemplate($templateData['message'], [
						'site_name' => $siteName,
						'email' => $email,
						'password' => $password,
						'login_url' => $loginUrl
					]);
				} else {
					// Fallback to hardcoded message
					$body = "Welcome to {$siteName} as an Event Organizer!\n\nYour organizer account has been created with the following credentials:\n\nEmail: {$email}\nPassword: {$password}\n\nYou can access your organizer dashboard at: {$loginUrl}\n\nPlease change your password after your first login for security.\n\nBest regards,\n{$siteName} Team";
				}
				
				$subject = "Welcome to {$siteName} - Organizer Account Created";
				
				$mail = new \App\Services\Mail();
				if ($mail->isConfigured()) {
					$mail->send($email, $subject, $body);
				}
			} catch (\Throwable $e) {
				// Log error but don't fail the account creation
			}
		}
		
		// Send SMS
		if ($sendSms && !empty($phone)) {
			try {
				// Get SMS template
				$template = db()->prepare('SELECT message FROM communication_templates WHERE name = ? AND is_active = 1');
				$template->execute(['new_organizer_sms_credentials']);
				$templateData = $template->fetch();
				
				if ($templateData) {
					$message = $this->processTemplate($templateData['message'], [
						'site_name' => $siteName,
						'email' => $email,
						'password' => $password,
						'login_url' => $loginUrl
					]);
				} else {
					// Fallback to hardcoded message
					$message = "Welcome to {$siteName}! Your organizer account is ready. Email: {$email}, Password: {$password}. Login: {$loginUrl}";
				}
				
				$sms = new \App\Services\Sms();
				if ($sms->isConfigured()) {
					$sms->send($phone, $message);
				}
			} catch (\Throwable $e) {
				// Log error but don't fail the account creation
			}
		}
	}

	private function sendTravelAgencyCredentials($agencyId, $email, $password, $phone, $sendEmail, $sendSms): void
	{
		$siteName = \App\Models\Setting::get('site.name', 'Ticko');
		$loginUrl = base_url('/travel/login');
		
		// Send email
		if ($sendEmail) {
			try {
				// Get email template
				$template = db()->prepare('SELECT message FROM communication_templates WHERE name = ? AND is_active = 1');
				$template->execute(['new_travel_agency_credentials']);
				$templateData = $template->fetch();
				
				if ($templateData) {
					$body = $this->processTemplate($templateData['message'], [
						'site_name' => $siteName,
						'email' => $email,
						'password' => $password,
						'login_url' => $loginUrl
					]);
				} else {
					// Fallback to hardcoded message
					$body = "Welcome to {$siteName} as a Travel Agency!\n\nYour travel agency account has been created with the following credentials:\n\nEmail: {$email}\nPassword: {$password}\n\nYou can access your travel agency dashboard at: {$loginUrl}\n\nPlease change your password after your first login for security.\n\nBest regards,\n{$siteName} Team";
				}
				
				$subject = "Welcome to {$siteName} - Travel Agency Account Created";
				
				$mail = new \App\Services\Mail();
				if ($mail->isConfigured()) {
					$mail->send($email, $subject, $body);
				}
			} catch (\Throwable $e) {
				// Log error but don't fail the account creation
			}
		}
		
		// Send SMS
		if ($sendSms && !empty($phone)) {
			try {
				// Get SMS template
				$template = db()->prepare('SELECT message FROM communication_templates WHERE name = ? AND is_active = 1');
				$template->execute(['new_travel_agency_sms_credentials']);
				$templateData = $template->fetch();
				
				if ($templateData) {
					$message = $this->processTemplate($templateData['message'], [
						'site_name' => $siteName,
						'email' => $email,
						'password' => $password,
						'login_url' => $loginUrl
					]);
				} else {
					// Fallback to hardcoded message
					$message = "Welcome to {$siteName}! Your travel agency account is ready. Email: {$email}, Password: {$password}. Login: {$loginUrl}";
				}
				
				$sms = new \App\Services\Sms();
				if ($sms->isConfigured()) {
					$sms->send($phone, $message);
				}
			} catch (\Throwable $e) {
				// Log error but don't fail the account creation
			}
		}
	}

	public function travelAgencyShow(): void
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect(base_url('/admin/travel/agencies'));
            return;
        }

        // Get agency details
        $stmt = db()->prepare('SELECT * FROM travel_agencies WHERE id = ?');
        $stmt->execute([$id]);
        $agency = $stmt->fetch();
        
        if (!$agency) {
            flash_set('error', 'Travel agency not found.');
            redirect(base_url('/admin/travel/agencies'));
            return;
        }

        // Get agency destinations
        $destinations = db()->prepare('SELECT * FROM travel_destinations WHERE agency_id = ? ORDER BY created_at DESC');
        $destinations->execute([$id]);
        $destinations = $destinations->fetchAll();

        // Get agency bookings summary
        $bookingsSummary = db()->prepare('
            SELECT 
                COUNT(*) as total_bookings,
                COALESCE(SUM(CASE WHEN tp.payment_status = "paid" THEN tb.total_amount ELSE 0 END), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN tp.payment_status = "paid" THEN 1 ELSE 0 END), 0) as paid_bookings
            FROM travel_bookings tb 
            LEFT JOIN travel_payments tp ON tp.booking_id = tb.id 
            WHERE tb.destination_id IN (SELECT id FROM travel_destinations WHERE agency_id = ?)
        ');
        $bookingsSummary->execute([$id]);
        $summary = $bookingsSummary->fetch();

        // Get agency withdrawals
        $withdrawals = db()->prepare('
            SELECT w.*, td.title as destination_title 
            FROM withdrawals w 
            LEFT JOIN travel_destinations td ON td.id = w.destination_id 
            WHERE w.travel_agency_id = ? 
            ORDER BY w.created_at DESC
        ');
        $withdrawals->execute([$id]);
        $withdrawals = $withdrawals->fetchAll();

        view('admin/travel_agency_show', compact('agency', 'destinations', 'summary', 'withdrawals'));
    }

    public function travelDestinations(): void
    {
        require_admin();
        try {
            $rows = db()->query('SELECT td.*, ta.company_name FROM travel_destinations td LEFT JOIN travel_agencies ta ON ta.id = td.agency_id ORDER BY td.created_at DESC')->fetchAll();
        } catch (\PDOException $e) { $rows = []; }
        view('admin/travel_destinations', ['destinations' => $rows]);
    }

	public function travelDestinationCreate(): void
	{
		require_admin();
        // Admin-owned destination: default agency is system-owned
        $defaultAgencyId = $this->getOrCreateAdminAgencyId();
        view('admin/travel_destination_create');
	}

	private function getOrCreateAdminAgencyId(): int
	{
		try {
			$row = db()->query("SELECT id FROM travel_agencies WHERE email = 'admin@system.local' LIMIT 1")->fetch();
			if ($row && (int)$row['id'] > 0) { return (int)$row['id']; }
			$stmt = db()->prepare('INSERT INTO travel_agencies (company_name, contact_person, email, phone, password_hash, is_approved, is_active, phone_verified) VALUES (?, ?, ?, ?, ?, 1, 1, 1)');
			$stmt->execute(['System Travel', 'Admin', 'admin@system.local', '0000000000', password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT)]);
			return (int)db()->lastInsertId();
		} catch (\PDOException $e) {
			return 1; // Fallback if a seeded agency exists
		}
	}

	public function travelDestinationStore(): void
	{
		require_admin();
        // Admin-owned destination must be attached to a real agency due to FK
        $agencyId = $this->getOrCreateAdminAgencyId();
        $title = trim($_POST['title'] ?? '');
        $destination = trim($_POST['destination'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $durationDays = (int)($_POST['duration_days'] ?? 1);
        $price = (float)($_POST['price'] ?? 0);
        $currency = trim($_POST['currency'] ?? 'KES');
        $maxParticipants = (int)($_POST['max_participants'] ?? 50);
        $minParticipants = (int)($_POST['min_participants'] ?? 1);
        $departureLocation = trim($_POST['departure_location'] ?? '');
        $departureDate = $_POST['departure_date'] ?? '';
        $returnDate = $_POST['return_date'] ?? '';
        $bookingDeadline = $_POST['booking_deadline'] ?? '';
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $childrenAllowed = isset($_POST['children_allowed']) ? 1 : 0;

        // List fields (stored as JSON arrays)
        $includes = json_encode(array_filter(array_map('trim', explode("\n", $_POST['includes'] ?? ''))));
        $excludes = json_encode(array_filter(array_map('trim', explode("\n", $_POST['excludes'] ?? ''))));
        $requirements = json_encode(array_filter(array_map('trim', explode("\n", $_POST['requirements'] ?? ''))));
        $itinerary = json_encode(array_filter(array_map('trim', explode("\n", $_POST['itinerary'] ?? ''))));

        // Handle main image
        $imagePath = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/travel';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'destination_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = 'uploads/travel/' . $filename;
            }
        }

        // Handle gallery images
        $galleryPaths = [];
        if (!empty($_FILES['gallery']['name']) && is_array($_FILES['gallery']['name'])) {
            $destDir = __DIR__ . '/../../public/uploads/travel';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            foreach ($_FILES['gallery']['name'] as $idx => $name) {
                if (($_FILES['gallery']['error'][$idx] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) { continue; }
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $file = 'gallery_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                $dest = $destDir . '/' . $file;
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$idx], $dest)) {
                    $galleryPaths[] = 'uploads/travel/' . $file;
                }
            }
        }

        $stmt = db()->prepare('
            INSERT INTO travel_destinations (
                agency_id, title, destination, description, duration_days, price, currency,
                max_participants, min_participants, departure_location, departure_date, return_date,
                booking_deadline, is_featured, is_published, children_allowed, image_path, gallery_paths,
                includes, excludes, requirements, itinerary
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ');
        $stmt->execute([
            $agencyId, $title, $destination, $description, $durationDays, $price, $currency,
            $maxParticipants, $minParticipants, $departureLocation, $departureDate, $returnDate,
            $bookingDeadline, $isFeatured, $isPublished, $childrenAllowed, $imagePath, json_encode($galleryPaths),
            $includes, $excludes, $requirements, $itinerary
        ]);
		flash_set('success', 'Destination created.');
		redirect(base_url('/admin/travel/destinations'));
	}

	// Scanner assignment methods removed - organizers manage their own devices

	public function settings(): void
	{
		require_admin();
        $settings = Setting::all();
		view('admin/settings', compact('settings'));
	}

	public function saveSettings(): void
	{
		require_admin();
        // Payment toggles
        Setting::set('payments.mpesa.enabled', isset($_POST['payments_mpesa_enabled']) ? '1' : '0');
        Setting::set('payments.paypal.enabled', isset($_POST['payments_paypal_enabled']) ? '1' : '0');
        Setting::set('payments.flutterwave.enabled', isset($_POST['payments_flutterwave_enabled']) ? '1' : '0');

        // M-Pesa STK config
        Setting::set('payments.mpesa.consumer_key', trim($_POST['mpesa_consumer_key'] ?? ''));
        Setting::set('payments.mpesa.consumer_secret', trim($_POST['mpesa_consumer_secret'] ?? ''));
        Setting::set('payments.mpesa.shortcode', trim($_POST['mpesa_shortcode'] ?? ''));
        Setting::set('payments.mpesa.passkey', trim($_POST['mpesa_passkey'] ?? ''));
        Setting::set('payments.mpesa.env', trim($_POST['mpesa_env'] ?? 'sandbox'));
        Setting::set('payments.mpesa.callback_url', trim($_POST['mpesa_callback_url'] ?? ''));

        // PayPal config
        Setting::set('payments.paypal.client_id', trim($_POST['paypal_client_id'] ?? ''));
        Setting::set('payments.paypal.secret', trim($_POST['paypal_secret'] ?? ''));
        Setting::set('payments.paypal.env', trim($_POST['paypal_env'] ?? 'sandbox'));

        // Flutterwave config
        Setting::set('payments.flutterwave.public_key', trim($_POST['flutterwave_public_key'] ?? ''));
        Setting::set('payments.flutterwave.secret_key', trim($_POST['flutterwave_secret_key'] ?? ''));
        Setting::set('payments.flutterwave.encryption_key', trim($_POST['flutterwave_encryption_key'] ?? ''));

        // Site settings
        Setting::set('site.name', trim($_POST['site_name'] ?? 'Ticko'));
        Setting::set('site.description', trim($_POST['site_description'] ?? ''));
        
        // Contact information
        Setting::set('site.phone', trim($_POST['site_phone'] ?? '+254 700 000 000'));
        Setting::set('site.email', trim($_POST['site_email'] ?? 'info@example.com'));
        Setting::set('site.address', trim($_POST['site_address'] ?? 'Nairobi, Kenya'));
        Setting::set('site.facebook', trim($_POST['site_facebook'] ?? ''));
        Setting::set('site.twitter', trim($_POST['site_twitter'] ?? ''));
        Setting::set('site.instagram', trim($_POST['site_instagram'] ?? ''));
        // Handle logo & favicon uploads
        if (!empty($_FILES['site_logo']['tmp_name'])) {
            $ext = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/site';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'logo_' . time() . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($_FILES['site_logo']['tmp_name'], $dest);
            Setting::set('site.logo', 'uploads/site/' . $filename);
        }
        if (!empty($_FILES['site_favicon']['tmp_name'])) {
            $ext = pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/site';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'favicon_' . time() . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($_FILES['site_favicon']['tmp_name'], $dest);
            Setting::set('site.favicon', 'uploads/site/' . $filename);
        }

        // SEO
        Setting::set('seo.meta_title', trim($_POST['seo_meta_title'] ?? ''));
        Setting::set('seo.meta_description', trim($_POST['seo_meta_description'] ?? ''));
        Setting::set('seo.meta_keywords', trim($_POST['seo_meta_keywords'] ?? ''));
        Setting::set('seo.meta_robots', trim($_POST['seo_meta_robots'] ?? 'index,follow'));
        Setting::set('seo.twitter', trim($_POST['seo_twitter'] ?? ''));
        if (!empty($_FILES['seo_og_image']['tmp_name'])) {
            $ext = pathinfo($_FILES['seo_og_image']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/site';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'og_' . time() . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($_FILES['seo_og_image']['tmp_name'], $dest);
            Setting::set('seo.og_image', 'uploads/site/' . $filename);
        }

        // Mobile scanner app links
        Setting::set('apps.android_scanner_url', trim($_POST['apps_android_scanner_url'] ?? ''));
        Setting::set('apps.ios_scanner_url', trim($_POST['apps_ios_scanner_url'] ?? ''));

        // SMTP
        Setting::set('smtp.host', trim($_POST['smtp_host'] ?? ''));
        Setting::set('smtp.port', trim($_POST['smtp_port'] ?? '587'));
        Setting::set('smtp.username', trim($_POST['smtp_username'] ?? ''));
        Setting::set('smtp.password', trim($_POST['smtp_password'] ?? ''));
        Setting::set('smtp.encryption', trim($_POST['smtp_encryption'] ?? 'tls'));
        Setting::set('smtp.from_email', trim($_POST['smtp_from_email'] ?? ''));
        Setting::set('smtp.from_name', trim($_POST['smtp_from_name'] ?? 'ShikaTicket'));

        // Email templates (optional inline edits on settings page)
        if (isset($_POST['user_welcome'])) {
            Setting::set('email.user_welcome', $_POST['user_welcome']);
        }
        if (isset($_POST['organizer_approved'])) {
            Setting::set('email.organizer_approved', $_POST['organizer_approved']);
        }

        flash_set('success', 'Settings saved.');
        redirect(base_url('/admin/settings'));
	}

    public function sendTestEmail(): void
    {
        require_admin();
        $to = trim($_POST['test_email'] ?? '');
        if ($to === '') { flash_set('error', 'Enter a valid email.'); redirect(base_url('/admin/settings')); }
        try {
            $mailer = new \App\Services\Mailer();
            $fromName = \App\Models\Setting::get('smtp.from_name', \App\Models\Setting::get('site.name', 'ShikaTicket'));
            $subject = 'Test Email from ' . $fromName;
            $html = '<h1>SMTP Test Successful</h1><p>If you can read this, your SMTP settings are working.</p>';
            $ok = $mailer->send($to, $subject, $html);
            if ($ok) { flash_set('success', 'Test email sent to ' . $to); }
            else { flash_set('error', 'Failed to send test email. Check SMTP credentials.'); }
        } catch (\Throwable $e) {
            flash_set('error', 'Mailer error: ' . $e->getMessage());
        }
        redirect(base_url('/admin/settings'));
    }

    public function restoreMpesaFromEnv(): void
    {
        require_admin();
        // Restore from environment variables if defined; otherwise keep existing values
        $map = [
            'payments.mpesa.consumer_key' => getenv('MPESA_CONSUMER_KEY') ?: '',
            'payments.mpesa.consumer_secret' => getenv('MPESA_CONSUMER_SECRET') ?: '',
            'payments.mpesa.shortcode' => getenv('MPESA_SHORTCODE') ?: '',
            'payments.mpesa.passkey' => getenv('MPESA_PASSKEY') ?: '',
            'payments.mpesa.env' => getenv('MPESA_ENV') ?: '',
            'payments.mpesa.callback_url' => getenv('MPESA_CALLBACK_URL') ?: ''
        ];
        $count = 0;
        foreach ($map as $k => $v) {
            if ($v !== '') { \App\Models\Setting::set($k, $v); $count++; }
        }
        if ($count > 0) { flash_set('success', 'M-Pesa credentials restored from environment.'); }
        else { flash_set('error', 'No M-Pesa env vars found. Please re-enter credentials and Save.'); }
        redirect(base_url('/admin/settings'));
    }

	public function emailTemplates(): void
	{
		require_admin();
		$templates = [
			'user_welcome' => Setting::get('email.user_welcome', ''),
			'organizer_approved' => Setting::get('email.organizer_approved', ''),
			'password_reset' => Setting::get('email.password_reset', ''),
			'verify_email' => Setting::get('email.verify_email', ''),
			'ticket_confirmation' => Setting::get('email.ticket_confirmation', ''),
			'order_receipt' => Setting::get('email.order_receipt', ''),
		];
		view('admin/email_templates', compact('templates'));
	}

    public function smsTemplates(): void
    {
        require_admin();
        $templates = [
            'welcome_user' => Setting::get('sms.welcome_user', 'Welcome to ShikaTicket!'),
            'payment_success' => Setting::get('sms.payment_success', 'Order #{{order_id}} confirmed. Tickets: {{tickets}}'),
            'user_otp' => Setting::get('sms.user_otp', 'Your Ticko login code is {{otp}}'),
            'organizer_otp' => Setting::get('sms.organizer_otp', 'Your Ticko OTP: {{otp}}'),
            'withdrawal_request' => Setting::get('sms.withdrawal_request', 'We received your withdrawal request of KES {{amount}}'),
            'travel_booking_confirmed' => Setting::get('sms.travel_booking_confirmed', 'Travel booking confirmed! Destination: {{destination}}. Ticket Code: {{ticket_code}}. Booking Reference: {{booking_reference}}. View ticket: {{ticket_link}}. Contact: {{agency_name}} at {{agency_phone}}'),
        ];
        view('admin/sms_templates', compact('templates'));
    }

    public function saveSmsTemplates(): void
    {
        require_admin();
        foreach (($_POST['templates'] ?? []) as $k => $v) {
            Setting::set('sms.' . $k, $v);
        }
        flash_set('success', 'SMS templates saved.');
        redirect(base_url('/admin/sms-templates'));
    }

    public function testSms(): void
    {
        require_admin();
        
        $phoneNumber = $_POST['phone_number'] ?? '';
        $message = $_POST['message'] ?? 'Test SMS from Ticko Admin Panel';
        
        if (empty($phoneNumber)) {
            flash_set('error', 'Phone number is required.');
            redirect(base_url('/admin/sms-templates'));
            return;
        }
        
        try {
            $sms = new \App\Services\Sms();
            
            if (!$sms->isConfigured()) {
                flash_set('error', 'SMS service is not configured. Please check your SMS settings.');
                redirect(base_url('/admin/sms-templates'));
                return;
            }
            
            $result = $sms->send($phoneNumber, $message);
            
            if ($result) {
                flash_set('success', 'Test SMS sent successfully to ' . $phoneNumber);
            } else {
                flash_set('error', 'Failed to send test SMS. Check SMS logs for details.');
            }
            
        } catch (\Throwable $e) {
            error_log("Admin SMS test error: " . $e->getMessage());
            flash_set('error', 'Error sending test SMS: ' . $e->getMessage());
        }
        
        redirect(base_url('/admin/sms-templates'));
    }

	public function saveEmailTemplates(): void
	{
		require_admin();
		Setting::set('email.user_welcome', $_POST['user_welcome'] ?? '');
		Setting::set('email.organizer_approved', $_POST['organizer_approved'] ?? '');
		Setting::set('email.password_reset', $_POST['password_reset'] ?? '');
		Setting::set('email.verify_email', $_POST['verify_email'] ?? '');
		Setting::set('email.ticket_confirmation', $_POST['ticket_confirmation'] ?? '');
		Setting::set('email.order_receipt', $_POST['order_receipt'] ?? '');
		flash_set('success', 'Email templates saved.');
		redirect(base_url('/admin/email-templates'));
	}

    // Pages CRUD
    public function pages(): void
    {
        require_admin();
        $pages = db()->query('SELECT * FROM pages ORDER BY created_at DESC')->fetchAll();
        view('admin/pages', compact('pages'));
    }

    public function partners(): void
    {
        require_admin();
        $partners = db()->query('SELECT * FROM partners ORDER BY created_at DESC')->fetchAll();
        view('admin/partners', compact('partners'));
    }

    public function withdrawalsIndex(): void
    {
        require_admin();
        $q = trim($_GET['q'] ?? '');
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = 'WHERE (w.status LIKE ? OR o.full_name LIKE ? OR ta.company_name LIKE ?)';
            $like = '%' . $q . '%';
            $params = [$like, $like, $like];
        }
        $stmt = db()->prepare('
            SELECT w.*, 
                   o.full_name as organizer_name, o.phone as organizer_phone,
                   ta.company_name as agency_name, ta.phone as agency_phone,
                   e.title as event_title, td.title as destination_title
            FROM withdrawals w 
            LEFT JOIN organizers o ON o.id = w.organizer_id 
            LEFT JOIN travel_agencies ta ON ta.id = w.travel_agency_id
            LEFT JOIN events e ON e.id = w.event_id
            LEFT JOIN travel_destinations td ON td.id = w.destination_id
            ' . $where . ' ORDER BY w.created_at DESC
        ');
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        view('admin/withdrawals', ['withdrawals' => $rows, 'q' => $q]);
    }

    public function withdrawalsUpdate(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'requested');
        $adminNotes = trim($_POST['admin_notes'] ?? '');
        
        if ($id > 0) {
            try {
                // Update withdrawal status
                db()->prepare('UPDATE withdrawals SET status = ?, admin_notes = ?, updated_at = CASE WHEN ? IN ("approved","paid","rejected") THEN NOW() ELSE updated_at END WHERE id = ?')
                  ->execute([$status, $adminNotes, $status, $id]);
                
                // Send SMS to organizer or travel agency
                try {
                    $row = db()->prepare('SELECT w.amount, o.phone as organizer_phone, ta.phone as agency_phone FROM withdrawals w LEFT JOIN organizers o ON o.id = w.organizer_id LEFT JOIN travel_agencies ta ON ta.id = w.travel_agency_id WHERE w.id = ?');
                    $row->execute([$id]);
                    $data = $row->fetch();
                    $phone = $data['organizer_phone'] ?? $data['agency_phone'] ?? '';
                    if ($phone) {
                        $sms = new \App\Services\Sms();
                        if ($sms->isConfigured()) {
                            $tplKey = 'withdrawal_' . $status; // e.g., withdrawal_approved
                            $body = \App\Services\SmsTemplates::render($tplKey, [
                                'amount' => number_format((float)($data['amount'] ?? 0), 2),
                                'status' => $status,
                            ]);
                            if ($body === '') { $body = 'Withdrawal ' . $status . ' for KES ' . number_format((float)($data['amount'] ?? 0), 2); }
                            $sms->send($phone, $body);
                        }
                    }
                } catch (\Throwable $e) {
                    // SMS error - don't fail the request
                }
                
                // Check if this is an AJAX request (from modal)
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Withdrawal status updated successfully',
                        'status' => $status
                    ]);
                    exit;
                }
                
                flash_set('success', 'Withdrawal status updated successfully');
            } catch (\Throwable $e) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update withdrawal status: ' . $e->getMessage()
                    ]);
                    exit;
                }
                flash_set('error', 'Failed to update withdrawal status');
            }
        } else {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid withdrawal ID'
                ]);
                exit;
            }
            flash_set('error', 'Invalid withdrawal ID');
        }
        
        redirect(base_url('/admin/withdrawals'));
    }

    public function partnerStatus(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'new');
        if ($id > 0) { db()->prepare('UPDATE partners SET status = ? WHERE id = ?')->execute([$status, $id]); }
        redirect(base_url('/admin/partners'));
    }

    public function pageEdit(): void
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('SELECT * FROM pages WHERE id = ?');
            $stmt->execute([$id]);
            $page = $stmt->fetch();
            if (!$page) { redirect(base_url('/admin/pages')); }
            view('admin/page_edit', compact('page'));
        } else {
            $page = ['id'=>0,'slug'=>'','title'=>'','content'=>'','is_published'=>1];
            view('admin/page_edit', compact('page'));
        }
    }

    public function pageSave(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $slug = trim($_POST['slug'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $pub = isset($_POST['is_published']) ? 1 : 0;
        if ($slug === '' || $title === '') { redirect(base_url('/admin/pages/edit?id=' . $id)); }
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE pages SET slug=?, title=?, content=?, is_published=? WHERE id=?');
            $stmt->execute([$slug, $title, $content, $pub, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO pages (slug, title, content, is_published) VALUES (?, ?, ?, ?)');
            $stmt->execute([$slug, $title, $content, $pub]);
        }
        redirect(base_url('/admin/pages'));
    }

    public function pageDelete(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) { db()->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]); }
        redirect(base_url('/admin/pages'));
    }

    public function partnerLogos(): void
    {
        require_admin();
        $rows = db()->query('SELECT * FROM partner_logos ORDER BY sort_order ASC, created_at DESC')->fetchAll();
        view('admin/partner_logos', ['logos' => $rows]);
    }

    public function partnerLogosCreate(): void
    {
        require_admin();
        view('admin/partner_logos_create');
    }

    public function partnerLogosStore(): void
    {
        require_admin();
        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link_url'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        $imagePath = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            [$w,$h] = @getimagesize($_FILES['image']['tmp_name']) ?: [0,0];
            if ($w < 100 || $h < 50) { flash_set('error','Logo too small'); redirect(base_url('/admin/partner-logos/create')); }
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/partners';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'partner_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            $imagePath = 'uploads/partners/' . $filename;
        }
        db()->prepare('INSERT INTO partner_logos (title, image_path, link_url, sort_order, is_active) VALUES (?, ?, ?, ?, 1)')->execute([$title, $imagePath, $link, $sort]);
        redirect(base_url('/admin/partner-logos'));
    }

    public function partnerLogosDelete(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) { db()->prepare('DELETE FROM partner_logos WHERE id = ?')->execute([$id]); }
        redirect(base_url('/admin/partner-logos'));
    }

    public function partnerLogosEdit(): void
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = db()->prepare('SELECT * FROM partner_logos WHERE id = ?');
        $stmt->execute([$id]);
        $logo = $stmt->fetch();
        if (!$logo) { redirect(base_url('/admin/partner-logos')); }
        view('admin/partner_logos_edit', compact('logo'));
    }

    public function partnerLogosUpdate(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link_url'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        $setImg = '';
        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads/partners';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'partner_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            $setImg = ', image_path = ' . db()->quote('uploads/partners/' . $filename);
        }
        $sql = 'UPDATE partner_logos SET title=:t, link_url=:l, sort_order=:s' . $setImg . ' WHERE id=:id';
        db()->prepare($sql)->execute([':t'=>$title, ':l'=>$link, ':s'=>$sort, ':id'=>$id]);
        redirect(base_url('/admin/partner-logos'));
    }

    public function partnerLogosToggle(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $active = (int)($_POST['is_active'] ?? 1);
        if ($id > 0) { db()->prepare('UPDATE partner_logos SET is_active = ? WHERE id = ?')->execute([$active, $id]); }
        redirect(base_url('/admin/partner-logos'));
    }

    // Travel Banners Management
    public function travelBanners(): void
    {
        require_admin();
        try {
            $banners = db()->query('SELECT * FROM travel_banners ORDER BY sort_order ASC, created_at DESC')->fetchAll();
        } catch (\PDOException $e) { $banners = []; }
        view('admin/travel_banners', compact('banners'));
    }

    public function travelBannerCreate(): void
    {
        require_admin();
        view('admin/travel_banner_create');
    }

    public function travelBannerStore(): void
    {
        require_admin();
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $buttonText = trim($_POST['button_text'] ?? '');
        $buttonUrl = trim($_POST['button_url'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if ($title === '') {
            flash_set('error', 'Title is required.');
            redirect(base_url('/admin/travel-banners/create'));
        }

        // Handle image upload
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/travel/banners/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'travel_banner_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = 'uploads/travel/banners/' . $filename;
            }
        }

        if ($imagePath === '') {
            flash_set('error', 'Image is required.');
            redirect(base_url('/admin/travel-banners/create'));
        }

        try {
            $stmt = db()->prepare('
                INSERT INTO travel_banners (title, subtitle, description, image_path, button_text, button_url, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$title, $subtitle, $description, $imagePath, $buttonText, $buttonUrl, $sortOrder]);
            flash_set('success', 'Travel banner created successfully.');
        } catch (\PDOException $e) {
            flash_set('error', 'Failed to create travel banner.');
        }
        redirect(base_url('/admin/travel-banners'));
    }

    public function travelBannerEdit(): void
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { redirect(base_url('/admin/travel-banners')); }
        
        try {
            $stmt = db()->prepare('SELECT * FROM travel_banners WHERE id = ?');
            $stmt->execute([$id]);
            $banner = $stmt->fetch();
            if (!$banner) { redirect(base_url('/admin/travel-banners')); }
        } catch (\PDOException $e) { redirect(base_url('/admin/travel-banners')); }
        
        view('admin/travel_banner_edit', compact('banner'));
    }

    public function travelBannerUpdate(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $buttonText = trim($_POST['button_text'] ?? '');
        $buttonUrl = trim($_POST['button_url'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if ($id <= 0 || $title === '') {
            flash_set('error', 'Invalid data.');
            redirect(base_url('/admin/travel-banners'));
        }

        // Get current banner
        try {
            $stmt = db()->prepare('SELECT image_path FROM travel_banners WHERE id = ?');
            $stmt->execute([$id]);
            $currentBanner = $stmt->fetch();
            if (!$currentBanner) { redirect(base_url('/admin/travel-banners')); }
        } catch (\PDOException $e) { redirect(base_url('/admin/travel-banners')); }

        $imagePath = $currentBanner['image_path'];

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/travel/banners/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'travel_banner_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = 'uploads/travel/banners/' . $filename;
            }
        }

        try {
            $stmt = db()->prepare('
                UPDATE travel_banners 
                SET title = ?, subtitle = ?, description = ?, image_path = ?, button_text = ?, button_url = ?, sort_order = ?
                WHERE id = ?
            ');
            $stmt->execute([$title, $subtitle, $description, $imagePath, $buttonText, $buttonUrl, $sortOrder, $id]);
            flash_set('success', 'Travel banner updated successfully.');
        } catch (\PDOException $e) {
            flash_set('error', 'Failed to update travel banner.');
        }
        redirect(base_url('/admin/travel-banners'));
    }

    public function travelBannerDelete(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                db()->prepare('DELETE FROM travel_banners WHERE id = ?')->execute([$id]);
                flash_set('success', 'Travel banner deleted successfully.');
            } catch (\PDOException $e) {
                flash_set('error', 'Failed to delete travel banner.');
            }
        }
        redirect(base_url('/admin/travel-banners'));
    }

    public function travelBannerToggle(): void
    {
        require_admin();
        $id = (int)($_POST['id'] ?? 0);
        $active = (int)($_POST['active'] ?? 0);
        if ($id > 0) {
            try {
                db()->prepare('UPDATE travel_banners SET is_active = ? WHERE id = ?')->execute([$active, $id]);
            } catch (\PDOException $e) {}
        }
        redirect(base_url('/admin/travel-banners'));
    }

    public function hotelsIndex(): void
    {
        require_admin();
        
        $q = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? '');
        
        $sql = 'SELECT * FROM hotel_applications WHERE 1=1';
        $params = [];
        
        if ($q !== '') {
            $sql .= ' AND (hotel_name LIKE ? OR contact_person LIKE ? OR email LIKE ? OR location LIKE ?)';
            $like = "%$q%";
            $params = array_merge($params, [$like, $like, $like, $like]);
        }
        
        if ($status !== '') {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        
        $sql .= ' ORDER BY created_at DESC';
        
        try {
            $stmt = db()->prepare($sql);
            $stmt->execute($params);
            $hotels = $stmt->fetchAll();
        } catch (\PDOException $e) {
            $hotels = [];
        }
        
        // Get statistics
        $stats = [];
        try {
            $stmt = db()->query('SELECT status, COUNT(*) as count FROM hotel_applications GROUP BY status');
            $statusCounts = $stmt->fetchAll();
            foreach ($statusCounts as $row) {
                $stats[$row['status']] = (int)$row['count'];
            }
        } catch (\PDOException $e) {
            $stats = [];
        }
        
        view('admin/hotels/index', compact('hotels', 'q', 'status', 'stats'));
    }

    public function hotelShow(): void
    {
        require_admin();
        
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect('/admin/hotels');
            return;
        }
        
        try {
            $stmt = db()->prepare('SELECT * FROM hotel_applications WHERE id = ?');
            $stmt->execute([$id]);
            $hotel = $stmt->fetch();
            
            if (!$hotel) {
                redirect('/admin/hotels');
                return;
            }
            
            view('admin/hotels/show', compact('hotel'));
        } catch (\PDOException $e) {
            redirect('/admin/hotels');
        }
    }

    public function updateHotelStatus(): void
    {
        require_admin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/hotels');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $adminNotes = trim($_POST['admin_notes'] ?? '');
        
        if ($id <= 0 || !in_array($status, ['pending', 'approved', 'rejected', 'contacted'])) {
            flash_set('error', 'Invalid request.');
            redirect('/admin/hotels');
            return;
        }
        
        try {
            $stmt = db()->prepare('UPDATE hotel_applications SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$status, $adminNotes, $id]);
            
            flash_set('success', 'Hotel application status updated successfully.');
        } catch (\PDOException $e) {
            flash_set('error', 'Failed to update hotel application status.');
        }
        
        redirect('/admin/hotels/show?id=' . $id);
    }

	public function marketing(): void
	{
		require_admin();
		
		// Get marketing statistics
		$marketingStats = [
			'pending_requests' => 0,
			'approved_campaigns' => 0,
			'active_campaigns' => 0,
			'total_revenue' => 0.00
		];
		
		// Get campaign requests
		$campaignRequests = [];
		
		// Get packages
		$packages = [];
		
		view('admin/marketing', compact('marketingStats', 'campaignRequests', 'packages'));
	}

	public function marketingPricingApi(): void
	{
		header('Content-Type: application/json');
		
		try {
			$tier = $_GET['tier'] ?? '';
			$accountType = $_GET['account_type'] ?? '';
			
			if (empty($tier) || empty($accountType)) {
				echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
				return;
			}


			
			$stmt = db()->prepare('SELECT price_per_sms, max_messages, features FROM marketing_pricing_settings WHERE tier_name = ? AND account_type = ? AND is_active = 1');
			$stmt->execute([$tier, $accountType]);
			$pricing = $stmt->fetch();
			
			if ($pricing) {
				echo json_encode([
					'success' => true,
					'pricing' => $pricing
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'Pricing not found'
				]);
			}
			
		} catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'message' => 'Error fetching pricing: ' . $e->getMessage()
			]);
		}
	}

	public function marketingCampaignsApi(): void
	{
		require_admin();
		header('Content-Type: application/json');
		
		try {
			if ($_SERVER['REQUEST_METHOD'] === 'GET') {
				$campaignId = $_GET['id'] ?? null;
				
				if ($campaignId) {
					// Get single campaign
					$stmt = db()->prepare('
						SELECT mcr.*, 
						       CASE 
						           WHEN mcr.account_type = "organizer" THEN o.full_name
						           WHEN mcr.account_type = "travel_agency" THEN ta.company_name
						       END as account_name
						FROM marketing_campaign_requests mcr
						LEFT JOIN organizers o ON mcr.account_type = "organizer" AND mcr.account_id = o.id
						LEFT JOIN travel_agencies ta ON mcr.account_type = "travel_agency" AND mcr.account_id = ta.id
						WHERE mcr.id = ?
					');
					$stmt->execute([$campaignId]);
					$campaign = $stmt->fetch();
					
					if ($campaign) {
						echo json_encode(['success' => true, 'campaign' => $campaign]);
					} else {
						echo json_encode(['success' => false, 'message' => 'Campaign not found']);
					}
				} else {
					// Get all campaigns
					$stmt = db()->prepare('
						SELECT mcr.*, 
						       CASE 
						           WHEN mcr.account_type = "organizer" THEN o.full_name
						           WHEN mcr.account_type = "travel_agency" THEN ta.company_name
						       END as account_name
						FROM marketing_campaign_requests mcr
						LEFT JOIN organizers o ON mcr.account_type = "organizer" AND mcr.account_id = o.id
						LEFT JOIN travel_agencies ta ON mcr.account_type = "travel_agency" AND mcr.account_id = ta.id
						ORDER BY mcr.created_at DESC
					');
					$stmt->execute();
					$campaigns = $stmt->fetchAll();
					
					echo json_encode(['success' => true, 'campaigns' => $campaigns]);
				}
			}
			
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	public function marketingCampaignsUpdateStatusApi(): void
	{
		require_admin();
		header('Content-Type: application/json');
		
		try {
			$input = json_decode(file_get_contents('php://input'), true);
			$campaignId = $input['campaign_id'] ?? null;
			$status = $input['status'] ?? null;
			
			if (!$campaignId || !$status) {
				echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
				return;
			}
			
			$stmt = db()->prepare('
				UPDATE marketing_campaign_requests 
				SET status = ?, approved_by = ?, approved_at = NOW() 
				WHERE id = ?
			');
			$stmt->execute([$status, $_SESSION['admin_id'], $campaignId]);
			
			echo json_encode(['success' => true, 'message' => 'Campaign status updated']);
			
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	public function marketingStatsApi(): void
	{
		require_admin();
		header('Content-Type: application/json');
		
		try {
			$stats = [];
			
			// Pending requests
			$stmt = db()->prepare('SELECT COUNT(*) as count FROM marketing_campaign_requests WHERE status = "pending"');
			$stmt->execute();
			$stats['pending'] = $stmt->fetch()['count'];
			
			// Approved requests
			$stmt = db()->prepare('SELECT COUNT(*) as count FROM marketing_campaign_requests WHERE status = "approved"');
			$stmt->execute();
			$stats['approved'] = $stmt->fetch()['count'];
			
			// Active campaigns
			$stmt = db()->prepare('SELECT COUNT(*) as count FROM marketing_campaign_requests WHERE status = "running"');
			$stmt->execute();
			$stats['active'] = $stmt->fetch()['count'];
			
			// Total revenue
			$stmt = db()->prepare('SELECT SUM(calculated_cost) as total FROM marketing_campaign_requests WHERE payment_status = "paid"');
			$stmt->execute();
			$stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;
			
			echo json_encode(['success' => true, 'stats' => $stats]);
			
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	public function marketingPackagesApi(): void
	{
        require_admin();
        // Ensure clean JSON responses (avoid stray output)
        while (function_exists('ob_get_level') && ob_get_level() > 0) { @ob_end_clean(); }
        header('Content-Type: application/json');
		
		try {
			if ($_SERVER['REQUEST_METHOD'] === 'GET') {
				$packageId = $_GET['id'] ?? null;
				
				if ($packageId) {
					// Get single package
					$stmt = db()->prepare('SELECT * FROM marketing_pricing_settings WHERE id = ?');
					$stmt->execute([$packageId]);
					$package = $stmt->fetch();
					
                    if ($package) {
                        echo json_encode(['success' => true, 'package' => $package]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Package not found']);
                    }
				} else {
					// Get all packages
					$stmt = db()->prepare('SELECT * FROM marketing_pricing_settings ORDER BY account_type, tier_name');
					$stmt->execute();
					$packages = $stmt->fetchAll();
					
					echo json_encode(['success' => true, 'packages' => $packages]);
				}
			} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// Create new package
                $input = json_decode(file_get_contents('php://input'), true);
                // Normalize inputs
                $tierName = trim($input['tier_name'] ?? '');
                $accountType = ($input['account_type'] ?? '') === 'travel_agency' ? 'travel_agency' : 'organizer';
                $pricePerSms = (float)($input['price_per_sms'] ?? 0);
                $maxMessages = (int)($input['max_messages'] ?? 0);
                $features = trim($input['features'] ?? '');
                if ($features === '') { $features = '[]'; }
                // Validate JSON
                $decoded = json_decode($features, true);
                if (!is_array($decoded)) { $features = '[]'; }
                $isActive = !empty($input['is_active']) ? 1 : 0;

                $stmt = db()->prepare('
                    INSERT INTO marketing_pricing_settings 
                    (tier_name, account_type, price_per_sms, max_messages, features, is_active, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ');
                
                $stmt->execute([
                    $tierName,
                    $accountType,
                    $pricePerSms,
                    $maxMessages,
                    $features,
                    $isActive
                ]);
				
                echo json_encode(['success' => true, 'message' => 'Package created successfully']);
                return;
				
            } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
				// Update package
                $input = json_decode(file_get_contents('php://input'), true);
                $packageId = $input['package_id'] ?? null;
				
				if (!$packageId) {
					echo json_encode(['success' => false, 'message' => 'Package ID required']);
					return;
				}
                // Normalize
                $tierName = trim($input['tier_name'] ?? '');
                $accountType = ($input['account_type'] ?? '') === 'travel_agency' ? 'travel_agency' : 'organizer';
                $pricePerSms = (float)($input['price_per_sms'] ?? 0);
                $maxMessages = (int)($input['max_messages'] ?? 0);
                $features = trim($input['features'] ?? '');
                if ($features === '') { $features = '[]'; }
                $decoded = json_decode($features, true);
                if (!is_array($decoded)) { $features = '[]'; }
                $isActive = !empty($input['is_active']) ? 1 : 0;

				$stmt = db()->prepare('
					UPDATE marketing_pricing_settings 
					SET tier_name = ?, account_type = ?, price_per_sms = ?, max_messages = ?, 
					    features = ?, is_active = ?, updated_at = NOW()
					WHERE id = ?
				');
				
				$stmt->execute([
                    $tierName,
                    $accountType,
                    $pricePerSms,
                    $maxMessages,
                    $features,
                    $isActive,
					$packageId
				]);
				
                echo json_encode(['success' => true, 'message' => 'Package updated successfully']);
                return;
            } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                // Delete package
                $input = json_decode(file_get_contents('php://input'), true);
                $packageId = $input['package_id'] ?? null;
                if (!$packageId) {
                    echo json_encode(['success' => false, 'message' => 'Package ID required']);
                    return;
                }
                $stmt = db()->prepare('DELETE FROM marketing_pricing_settings WHERE id = ?');
                $stmt->execute([$packageId]);
                echo json_encode(['success' => true, 'message' => 'Package deleted successfully']);
                return;
			}
			
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}
    // Public: list active packages for a given account type (organizer|travel_agency)
    public function marketingPublicPackagesApi(): void
    {
        header('Content-Type: application/json');
        try {
            $accountType = $_GET['account_type'] ?? '';
            if ($accountType !== 'organizer' && $accountType !== 'travel_agency') {
                echo json_encode(['success' => false, 'message' => 'Invalid account_type']);
                return;
            }
            $stmt = db()->prepare('SELECT id, tier_name, account_type, price_per_sms, max_messages, features FROM marketing_pricing_settings WHERE account_type = ? AND is_active = 1 ORDER BY price_per_sms ASC, tier_name ASC');
            $stmt->execute([$accountType]);
            $packages = $stmt->fetchAll();
            echo json_encode(['success' => true, 'packages' => $packages]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // Public: simple unit rates and volume tiers for reach-based orders
    public function marketingRatesApi(): void
    {
        header('Content-Type: application/json');
        try {
            $accountType = $_GET['account_type'] ?? 'organizer';
            if ($accountType !== 'organizer' && $accountType !== 'travel_agency') {
                $accountType = 'organizer';
            }
            $sms = (float)Setting::get($accountType.'.marketing.sms_rate', $accountType==='organizer' ? '1.20' : '1.30');
            $email = (float)Setting::get($accountType.'.marketing.email_rate', '0.15');
            $tiers = [1000, 5000, 10000, 25000, 50000];
            echo json_encode(['success'=>true, 'rates'=>['sms'=>$sms,'email'=>$email],'tiers'=>$tiers]);
        } catch (\Exception $e) {
            echo json_encode(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
        }
    }

    // Admin: list/update marketing orders
    public function marketingOrdersApi(): void
    {
        require_admin();
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $stmt = db()->prepare('SELECT * FROM marketing_orders ORDER BY created_at DESC');
                $stmt->execute();
                echo json_encode(['success'=>true,'orders'=>$stmt->fetchAll()]);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                $orderId = (int)($input['order_id'] ?? 0);
                $status = $input['status'] ?? '';
                if ($orderId<=0 || $status==='') { echo json_encode(['success'=>false,'message'=>'Invalid request']); return; }

                // Fetch order row
                $ordStmt = db()->prepare('SELECT * FROM marketing_orders WHERE id = ?');
                $ordStmt->execute([$orderId]);
                $order = $ordStmt->fetch();
                if (!$order) { echo json_encode(['success'=>false,'message'=>'Order not found']); return; }

                // When admin approves or starts running, create a campaign record (if not exists) and trigger delivery
                $createdCampaignId = null;
                $logMsgs = [];
                if (in_array($status, ['approved','running'], true)) {
                    try {
                        // Check for existing linked campaign by looking for a reference in last_log JSON
                        $existingId = null;
                        if (!empty($order['last_log'])) {
                            $decoded = json_decode($order['last_log'], true);
                            if (is_array($decoded) && !empty($decoded['campaign_id'])) { $existingId = (int)$decoded['campaign_id']; }
                        }

                        if (!$existingId) {
                            // Build default content from the item
                            $smsContent = '';
                            $emailContent = '';
                            if (($order['item_type'] ?? '') === 'event') {
                                $ev = db()->prepare('SELECT id, title FROM events WHERE id = ?');
                                $ev->execute([(int)$order['item_id']]);
                                $event = $ev->fetch();
                                $title = $event['title'] ?? 'your event';
                                $link = base_url('/events/show?id=' . (int)$order['item_id']);
                                $smsContent = 'Quick Boost: ' . $title . ' - Get tickets: ' . $link;
                                $emailContent = 'Quick Boost for <strong>' . htmlspecialchars($title) . '</strong>. Grab tickets now: ' . $link;
                            } elseif (($order['item_type'] ?? '') === 'destination') {
                                $link = base_url('/travel/destination?id=' . (int)$order['item_id']);
                                $smsContent = 'Quick Boost: New travel deal! View: ' . $link;
                                $emailContent = 'Quick Boost: New travel deal is live. View details: ' . $link;
                            }

                            // Audience type from order log if present
                            $audType = 'recommend';
                            if (!empty($order['last_log'])) { $j = json_decode($order['last_log'], true); if (is_array($j) && !empty($j['audience_type'])) { $audType = $j['audience_type']; } }

                            // Insert campaign row (minimal fields to work with DeliveryService)
                            $ins = db()->prepare('INSERT INTO marketing_campaigns (name, description, campaign_type, organizer_id, travel_agency_id, target_audience, message_content, sms_content, email_content, budget, scheduled_at, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())');
                            $name = 'QB Order #' . $orderId;
                            $desc = 'Auto-created from marketing_order #' . $orderId;
                            $ctype = ($order['item_type'] ?? 'event');
                            $organizerId = ($order['account_type'] ?? '') === 'organizer' ? (int)$order['account_id'] : null;
                            $agencyId = ($order['account_type'] ?? '') === 'travel_agency' ? (int)$order['account_id'] : null;
                            $targetJson = json_encode(['quick_boost'=>true,'source_order_id'=>$orderId,'item_type'=>$order['item_type'],'item_id'=>(int)$order['item_id'],'audience_type'=>$audType]);
                            $budget = (float)($order['total_cost'] ?? 0);
                            $statusForCampaign = 'approved'; // allow startCampaign
                            $ins->execute([$name, $desc, $ctype, $organizerId, $agencyId, $targetJson, $smsContent, $smsContent, $emailContent, $budget, $statusForCampaign]);
                            $createdCampaignId = (int)db()->lastInsertId();
                            $logMsgs[] = 'Campaign created: ' . $createdCampaignId;

                            // Persist mapping inside last_log JSON (non-breaking)
                            $newLog = ['campaign_id'=>$createdCampaignId, 'created_at'=>date('c')];
                            $logJson = json_encode($newLog);
                            db()->prepare('UPDATE marketing_orders SET last_log = ? WHERE id = ?')->execute([$logJson, $orderId]);
                        } else {
                            $createdCampaignId = $existingId;
                            $logMsgs[] = 'Using existing campaign: ' . $existingId;
                        }

                        // If requested to run, trigger delivery engine
                        if ($createdCampaignId && $status === 'running') {
                            require_once __DIR__ . '/../../marketing/app/Services/DeliveryService.php';
                            $delivery = new \App\Services\DeliveryService();
                            $ok = $delivery->startCampaign($createdCampaignId);
                            $logMsgs[] = 'Delivery ' . ($ok ? 'started' : 'failed to start');
                        }
                    } catch (\Throwable $e) {
                        echo json_encode(['success'=>false,'message'=>'Failed to instantiate campaign: '.$e->getMessage()]);
                        return;
                    }
                }

                // Update order status
                $stmt = db()->prepare('UPDATE marketing_orders SET status = ?, updated_at = NOW() WHERE id = ?');
                $stmt->execute([$status,$orderId]);
                echo json_encode(['success'=>true,'message'=>'Order updated','campaign_id'=>$createdCampaignId,'log'=>$logMsgs]);
            }
        } catch (\Exception $e) {
            echo json_encode(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
        }
    }

    // Admin order detail page
    public function marketingOrderShow(): void
    {
        require_admin();
        $orderId = (int)($_GET['id'] ?? 0);
        if ($orderId <= 0) { echo 'Invalid order id'; return; }
        $stmt = db()->prepare('SELECT * FROM marketing_orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) { echo 'Order not found'; return; }
        view('admin/marketing_order', compact('order'));
    }

    // end of class
}


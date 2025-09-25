<?php
namespace App\Controllers;
use App\Models\Organizer;
use App\Models\Setting;

class AdminController
{
	public function index(): void
	{
		require_admin();
        // Finance summary
        $orders = db()->query("SELECT COUNT(*) AS total_orders, SUM(total_amount) AS gross FROM orders WHERE status='paid'")->fetch();
        $byCurrency = db()->query("SELECT currency, SUM(total_amount) AS gross FROM orders WHERE status='paid' GROUP BY currency")->fetchAll();
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
        view('admin/index', compact('orders', 'byCurrency', 'pendingWithdrawals', 'commissionRevenue'));
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

	public function organizers(): void
	{
		require_admin();
        $organizers = db()->query('SELECT * FROM organizers ORDER BY created_at DESC')->fetchAll();
		view('admin/organizers', compact('organizers'));
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

	public function users(): void
	{
		require_admin();
        $users = db()->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
		view('admin/users', compact('users'));
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

	public function deleteOrganizer(): void
	{
		require_admin();
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			db()->prepare('DELETE FROM organizers WHERE id = ?')->execute([$id]);
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

    public function approveTravelAgency(): void
    {
        require_admin(); verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            db()->prepare('UPDATE travel_agencies SET is_approved = 1 WHERE id = ?')->execute([$id]);
            flash_set('success', 'Agency approved.');
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

    public function travelDestinations(): void
    {
        require_admin();
        try {
            $rows = db()->query('SELECT td.*, ta.company_name FROM travel_destinations td JOIN travel_agencies ta ON ta.id = td.agency_id ORDER BY td.created_at DESC')->fetchAll();
        } catch (\PDOException $e) { $rows = []; }
        view('admin/travel_destinations', ['destinations' => $rows]);
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

        // SMTP
        Setting::set('smtp.host', trim($_POST['smtp_host'] ?? ''));
        Setting::set('smtp.port', trim($_POST['smtp_port'] ?? '587'));
        Setting::set('smtp.username', trim($_POST['smtp_username'] ?? ''));
        Setting::set('smtp.password', trim($_POST['smtp_password'] ?? ''));
        Setting::set('smtp.encryption', trim($_POST['smtp_encryption'] ?? 'tls'));
        Setting::set('smtp.from_email', trim($_POST['smtp_from_email'] ?? ''));
        Setting::set('smtp.from_name', trim($_POST['smtp_from_name'] ?? 'Ticko'));

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
            $fromName = \App\Models\Setting::get('smtp.from_name', \App\Models\Setting::get('site.name', 'Ticko'));
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
            'welcome_user' => Setting::get('sms.welcome_user', 'Welcome to Ticko!'),
            'payment_success' => Setting::get('sms.payment_success', 'Order #{{order_id}} confirmed. Tickets: {{tickets}}'),
            'organizer_otp' => Setting::get('sms.organizer_otp', 'Your Ticko OTP: {{otp}}'),
            'withdrawal_request' => Setting::get('sms.withdrawal_request', 'We received your withdrawal request of KES {{amount}}'),
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
        if ($id > 0) {
            db()->prepare('UPDATE withdrawals SET status = ?, updated_at = CASE WHEN ? IN ("approved","paid","rejected") THEN NOW() ELSE updated_at END WHERE id = ?')
              ->execute([$status, $status, $id]);
            // send SMS to organizer or travel agency
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
            } catch (\Throwable $e) {}
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
}



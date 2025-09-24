<?php
namespace App\Controllers;
use App\Models\Event as EventModel;
use App\Models\User;
use App\Models\Setting;

class EventController
{
	public function index(): void
	{
		require_organizer();
		$events = db()->prepare('SELECT * FROM events WHERE organizer_id = ? ORDER BY created_at DESC');
        $organizerId = $_SESSION['organizer_id'] ?? 0;
        $events->execute([$organizerId]);
        $events = $events->fetchAll();
		view('organizer/events/index', compact('events'));
	}

	public function create(): void
	{
		require_organizer();
		// Require phone verification before allowing event creation
		$org = db()->prepare('SELECT phone_verified_at FROM organizers WHERE id = ?');
		$org->execute([$_SESSION['organizer_id'] ?? 0]);
		$row = $org->fetch();
		if (empty($row['phone_verified_at'])) {
			flash_set('error', 'Verify your phone to create events.');
			redirect(base_url('/organizer/verify-otp'));
		}
		view('organizer/events/create');
	}

	public function store(): void
	{
		require_organizer();
		$org = db()->prepare('SELECT phone_verified_at FROM organizers WHERE id = ?');
		$org->execute([$_SESSION['organizer_id'] ?? 0]);
		$row = $org->fetch();
		if (empty($row['phone_verified_at'])) { redirect(base_url('/organizer/verify-otp')); }
		$organizerId = (int)($_SESSION['organizer_id'] ?? 0);
        if ($organizerId <= 0) { redirect(base_url('/organizer/login')); }
		// Handle poster upload (expects 1080x1080)
		$posterPath = null;
		if (!empty($_FILES['poster']['tmp_name'])) {
			[$w, $h] = @getimagesize($_FILES['poster']['tmp_name']) ?: [0,0];
			if ($w != 1080 || $h != 1080) {
				flash_set('error', 'Poster must be exactly 1080x1080 pixels.');
				redirect(base_url('/organizer/events/create'));
			}
			$ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
			$destDir = __DIR__ . '/../../public/uploads';
			if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
			$filename = 'poster_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
			$dest = $destDir . '/' . $filename;
			move_uploaded_file($_FILES['poster']['tmp_name'], $dest);
			$posterPath = 'uploads/' . $filename;
		}
		$data = [
            'organizer_id' => $organizerId,
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
			'category' => trim($_POST['category'] ?? ''),
            'event_date' => $_POST['date'] ?? null,
            'event_time' => $_POST['time'] ?? null,
            'venue' => trim($_POST['venue'] ?? ''),
			'poster_path' => $posterPath,
            'capacity' => (int)($_POST['capacity'] ?? 0),
			'price' => (float)($_POST['price'] ?? 0),
			'regular_price' => isset($_POST['regular_price']) ? (float)$_POST['regular_price'] : null,
			'early_bird_price' => isset($_POST['early_bird_price']) ? (float)$_POST['early_bird_price'] : null,
			'early_bird_until' => $_POST['early_bird_until'] ?? null,
			'vip_price' => isset($_POST['vip_price']) ? (float)$_POST['vip_price'] : null,
			'vvip_price' => isset($_POST['vvip_price']) ? (float)$_POST['vvip_price'] : null,
			'group_price' => isset($_POST['group_price']) ? (float)$_POST['group_price'] : null,
			'group_size' => isset($_POST['group_size']) ? (int)$_POST['group_size'] : null,
            'currency' => trim($_POST['currency'] ?? 'KES'),
			'dress_code' => trim($_POST['dress_code'] ?? ''),
			'lineup' => trim($_POST['lineup'] ?? ''),
            'is_featured' => 0,
            'is_published' => 1,
        ];
        if ($data['title'] === '') { redirect(base_url('/organizer/events/create')); }
        EventModel::create($data);
        redirect(base_url('/organizer/events'));
	}

	public function edit(): void
	{
        $id = (int)($_GET['id'] ?? 0);
        $organizerId = (int)($_SESSION['organizer_id'] ?? 0);
        if ($id <= 0 || $organizerId <= 0) { redirect(base_url('/organizer/events')); }
        $stmt = db()->prepare('SELECT * FROM events WHERE id = ? AND organizer_id = ? LIMIT 1');
        $stmt->execute([$id, $organizerId]);
        $event = $stmt->fetch();
        if (!$event) { redirect(base_url('/organizer/events')); }
        view('organizer/events/edit', compact('event'));
	}

	public function update(): void
	{
        $organizerId = (int)($_SESSION['organizer_id'] ?? 0);
        $id = (int)($_POST['id'] ?? 0);
        if ($organizerId <= 0 || $id <= 0) { redirect(base_url('/organizer/events')); }
        $title = trim($_POST['title'] ?? '');
        if ($title === '') { flash_set('error', 'Title is required.'); redirect(base_url('/organizer/events/edit?id=' . $id)); }

        // Optional replace poster
        $posterSet = null; $posterFile = null;
        if (!empty($_FILES['poster']['tmp_name'])) {
            [$w, $h] = @getimagesize($_FILES['poster']['tmp_name']) ?: [0,0];
            if ($w != 1080 || $h != 1080) {
                flash_set('error', 'Poster must be exactly 1080x1080 pixels.');
                redirect(base_url('/organizer/events/edit?id=' . $id));
            }
            $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
            $destDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($destDir)) { @mkdir($destDir, 0777, true); }
            $filename = 'poster_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $dest = $destDir . '/' . $filename;
            move_uploaded_file($_FILES['poster']['tmp_name'], $dest);
            $posterFile = 'uploads/' . $filename;
            $posterSet = 'poster_path = :poster_path,';
        }

        $sql = 'UPDATE events SET title=:title, description=:description, category=:category, event_date=:event_date, event_time=:event_time, venue=:venue, ' .
               ($posterSet ? $posterSet : '') .
               ' capacity=:capacity, regular_price=:regular_price, early_bird_price=:early_bird_price, early_bird_until=:early_bird_until, vip_price=:vip_price, vvip_price=:vvip_price, group_price=:group_price, group_size=:group_size, currency=:currency, dress_code=:dress_code, lineup=:lineup WHERE id=:id AND organizer_id=:organizer_id';
        $stmt = db()->prepare($sql);
        $params = [
            ':title' => $title,
            ':description' => trim($_POST['description'] ?? ''),
            ':category' => trim($_POST['category'] ?? ''),
            ':event_date' => $_POST['date'] ?? null,
            ':event_time' => $_POST['time'] ?? null,
            ':venue' => trim($_POST['venue'] ?? ''),
            ':capacity' => (int)($_POST['capacity'] ?? 0),
            ':regular_price' => isset($_POST['regular_price']) ? (float)$_POST['regular_price'] : null,
            ':early_bird_price' => isset($_POST['early_bird_price']) ? (float)$_POST['early_bird_price'] : null,
            ':early_bird_until' => $_POST['early_bird_until'] ?? null,
            ':vip_price' => isset($_POST['vip_price']) ? (float)$_POST['vip_price'] : null,
            ':vvip_price' => isset($_POST['vvip_price']) ? (float)$_POST['vvip_price'] : null,
            ':group_price' => isset($_POST['group_price']) ? (float)$_POST['group_price'] : null,
            ':group_size' => isset($_POST['group_size']) ? (int)$_POST['group_size'] : null,
            ':currency' => trim($_POST['currency'] ?? 'KES'),
            ':dress_code' => trim($_POST['dress_code'] ?? ''),
            ':lineup' => trim($_POST['lineup'] ?? ''),
            ':id' => $id,
            ':organizer_id' => $organizerId,
        ];
        if ($posterFile) { $params[':poster_path'] = $posterFile; }
        $stmt->execute($params);
        flash_set('success', 'Event updated.');
        redirect(base_url('/organizer/events'));
	}

	public function destroy(): void
	{
        $organizerId = (int)($_SESSION['organizer_id'] ?? 0);
        $id = (int)($_POST['id'] ?? 0);
        if ($organizerId > 0 && $id > 0) {
            $stmt = db()->prepare('DELETE FROM events WHERE id = ? AND organizer_id = ?');
            $stmt->execute([$id, $organizerId]);
            flash_set('success', 'Event deleted.');
        }
        redirect(base_url('/organizer/events'));
	}

	public function show(): void
	{
		$id = (int)($_GET['id'] ?? 0);
		if ($id <= 0) { redirect(base_url('/')); }
		$stmt = db()->prepare('SELECT * FROM events WHERE id = ? AND is_published = 1');
		$stmt->execute([$id]);
		$event = $stmt->fetch();
		if (!$event) { redirect(base_url('/')); }
        $settings = [
            'mpesa' => Setting::get('payments.mpesa.enabled', '0') === '1',
            'paypal' => Setting::get('payments.paypal.enabled', '0') === '1',
            'flutterwave' => Setting::get('payments.flutterwave.enabled', '0') === '1',
        ];
        $userPhone = '';
        if (!empty($_SESSION['user_id'])) {
            $u = User::findById((int)$_SESSION['user_id']);
            $userPhone = $u['phone'] ?? '';
        }
        view('events/show', compact('event', 'settings', 'userPhone'));
	}
}



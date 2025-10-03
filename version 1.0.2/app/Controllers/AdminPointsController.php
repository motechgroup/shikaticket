<?php
namespace App\Controllers;
use App\Models\Setting;

class AdminPointsController
{
    public function index(): void
    {
        require_admin();
        $stmt = db()->query('SELECT up.*, u.email FROM user_points up LEFT JOIN users u ON u.id=up.user_id ORDER BY up.created_at DESC LIMIT 300');
        $entries = $stmt->fetchAll();
        // Load current loyalty configuration
        $config = [
            'order_per_100_kes' => (int)Setting::get('loyalty.points.order_per_100_kes', '1'),
            'travel_per_100_kes' => (int)Setting::get('loyalty.points.travel_per_100_kes', '1'),
            'login' => (int)Setting::get('loyalty.points.login', '0'),
            'add_email' => (int)Setting::get('loyalty.points.add_email', '0'),
        ];
        view('admin/points/index', ['entries'=>$entries, 'config'=>$config]);
    }

    public function add(): void
    {
        require_admin(); verify_csrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Manual award');
        if ($userId > 0 && $points > 0) {
            db()->prepare('INSERT INTO user_points (user_id, points, reason, created_at) VALUES (?, ?, ?, NOW())')->execute([$userId, $points, $reason]);
        }
        flash_set('success', 'Points added.');
        redirect(base_url('/admin/points'));
    }

    public function remove(): void
    {
        require_admin(); verify_csrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Manual deduction');
        if ($userId > 0 && $points > 0) {
            db()->prepare('INSERT INTO user_points (user_id, points, reason, created_at) VALUES (?, ?, ?, NOW())')->execute([$userId, -$points, $reason]);
        }
        flash_set('success', 'Points removed.');
        redirect(base_url('/admin/points'));
    }

    public function saveConfig(): void
    {
        require_admin(); verify_csrf();
        $order = max(0, (int)($_POST['order_per_100_kes'] ?? 0));
        $travel = max(0, (int)($_POST['travel_per_100_kes'] ?? 0));
        $login = max(0, (int)($_POST['login'] ?? 0));
        $addEmail = max(0, (int)($_POST['add_email'] ?? 0));
        try {
            Setting::set('loyalty.points.order_per_100_kes', (string)$order);
            Setting::set('loyalty.points.travel_per_100_kes', (string)$travel);
            Setting::set('loyalty.points.login', (string)$login);
            Setting::set('loyalty.points.add_email', (string)$addEmail);
            flash_set('success', 'Loyalty configuration saved.');
        } catch (\Throwable $e) {
            flash_set('error', 'Failed to save configuration.');
        }
        redirect(base_url('/admin/points'));
    }
}



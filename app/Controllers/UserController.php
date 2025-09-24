<?php
namespace App\Controllers;

class UserController
{
	public function dashboard(): void
	{
		require_user();
		view('user/dashboard');
	}

	public function orders(): void
	{
		require_user();
		$stmt = db()->prepare('SELECT o.*, p.status AS payment_status FROM orders o LEFT JOIN payments p ON p.order_id = o.id AND p.id = (SELECT MAX(id) FROM payments WHERE order_id = o.id) WHERE o.user_id = ? ORDER BY o.created_at DESC');
		$stmt->execute([$_SESSION['user_id']]);
		$orders = $stmt->fetchAll();
		view('user/orders', compact('orders'));
	}

	public function account(): void
	{
		require_user();
		$stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
		$stmt->execute([$_SESSION['user_id']]);
		$user = $stmt->fetch() ?: [];
		view('user/account', compact('user'));
	}

	public function accountUpdate(): void
	{
		require_user();
		$phone = trim($_POST['phone'] ?? '');
		$email = trim($_POST['email'] ?? '');
		if ($phone === '' || $email === '') { flash_set('error', 'Phone and Email are required.'); redirect(base_url('/user/account')); }
		$stmt = db()->prepare('UPDATE users SET phone = ?, email = ? WHERE id = ?');
		try {
			$stmt->execute([$phone, $email, $_SESSION['user_id']]);
			flash_set('success', 'Account updated.');
		} catch (\PDOException $e) {
			flash_set('error', 'Update failed. Phone might already be taken.');
		}
		redirect(base_url('/user/account'));
	}

	public function orderShow(): void
	{
		require_user();
		$id = (int)($_GET['id'] ?? 0);
		$stmt = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
		$stmt->execute([$id, $_SESSION['user_id']]);
		$order = $stmt->fetch();
		if (!$order) { redirect(base_url('/user/orders')); }
		$items = db()->prepare('SELECT oi.*, e.title FROM order_items oi JOIN events e ON e.id = oi.event_id WHERE oi.order_id = ?');
		$items->execute([$id]);
		$items = $items->fetchAll();
		$payments = db()->prepare('SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC');
		$payments->execute([$id]);
		$payments = $payments->fetchAll();
        // Fetch tickets for this order (guard if tickets table is missing)
        $tickets = [];
        try {
            $tstmt = db()->prepare('SELECT t.*, e.title, e.event_date, e.venue FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id JOIN events e ON e.id = oi.event_id WHERE oi.order_id = ? ORDER BY t.id ASC');
            $tstmt->execute([$id]);
            $tickets = $tstmt->fetchAll();
        } catch (\PDOException $e) {
            // table may not exist yet; show order without tickets
            $tickets = [];
        }
        view('user/order_show', compact('order', 'items', 'payments', 'tickets'));
	}

    public function orderStatus(): void
    {
        require_user();
        $id = (int)($_GET['id'] ?? 0);
        $stmt = db()->prepare('SELECT status FROM orders WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $_SESSION['user_id']]);
        $row = $stmt->fetch();
        header('Content-Type: application/json');
        echo json_encode(['status' => $row['status'] ?? 'unknown']);
    }
}



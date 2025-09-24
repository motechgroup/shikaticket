<?php
namespace App\Controllers;

class ScannerController
{
	private function requireScannerAuth(): void
	{
		if (!isset($_SESSION['organizer_id'])) {
			redirect(base_url('/scanner/login'));
		}
	}

	public function loginForm(): void
	{
		view('scanner/login');
	}

	public function login(): void
	{
		$email = trim($_POST['email'] ?? '');
		$password = $_POST['password'] ?? '';
		$stmt = db()->prepare('SELECT * FROM organizers WHERE email = ? LIMIT 1');
		$stmt->execute([$email]);
		$org = $stmt->fetch();
		if ($org && password_verify($password, $org['password_hash'])) {
			$_SESSION['organizer_id'] = $org['id'];
			$_SESSION['role'] = 'organizer';
			redirect(base_url('/scanner'));
		}
		echo 'Invalid credentials';
	}

	public function index(): void
	{
		$this->requireScannerAuth();
		view('scanner/index');
	}

    public function verify(): void
	{
		$this->requireScannerAuth();
        $inputCode = trim($_POST['code'] ?? $_GET['code'] ?? '');
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        if ($inputCode === '') { echo json_encode(['ok'=>false,'msg'=>'No code']); return; }
		// Lookup ticket by code
		$stmt = db()->prepare('SELECT t.*, oi.event_id, e.organizer_id FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id JOIN events e ON e.id = oi.event_id WHERE t.code = ? LIMIT 1');
		$stmt->execute([$inputCode]);
		$ticket = $stmt->fetch();
		if (!$ticket) { echo json_encode(['ok'=>false,'msg'=>'Not found']); return; }
		if ((int)$ticket['organizer_id'] !== (int)$_SESSION['organizer_id']) { echo json_encode(['ok'=>false,'msg'=>'Ticket not for your event']); return; }
		if ($ticket['status'] !== 'valid') { echo json_encode(['ok'=>false,'msg'=>'Already redeemed']); return; }
		// Mark redeemed
		db()->prepare('UPDATE tickets SET status="redeemed", redeemed_at=NOW(), redeemed_by=? WHERE id=?')->execute([$_SESSION['organizer_id'], $ticket['id']]);
		echo json_encode(['ok'=>true,'msg'=>'Ticket valid & redeemed']);
	}
}



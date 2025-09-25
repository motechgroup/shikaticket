<?php
namespace App\Controllers;
use App\Models\ScannerDevice;

class ScannerController
{
	private function requireScannerAuth(): void
	{
		if (!isset($_SESSION['scanner_device_id'])) {
			redirect(base_url('/scanner/login'));
		}
	}

	public function loginForm(): void
	{
		view('scanner/login');
	}

	public function login(): void
	{
		$deviceCode = strtoupper(trim($_POST['device_code'] ?? ''));
		if ($deviceCode === '') {
			echo 'Device code is required';
			return;
		}
		
		$device = ScannerDevice::findByDeviceCode($deviceCode);
		if (!$device || !$device['is_active']) {
			echo 'Invalid or inactive device code';
			return;
		}
		
		// Check if device is assigned to any active event
		$assignedEvents = ScannerDevice::getAssignedEvents($device['id']);
		if (empty($assignedEvents)) {
			echo 'Device not assigned to any event';
			return;
		}
		
		$_SESSION['scanner_device_id'] = $device['id'];
		$_SESSION['scanner_device_name'] = $device['device_name'];
		$_SESSION['scanner_device_code'] = $device['device_code'];
		$_SESSION['scanner_assigned_events'] = $assignedEvents;
		redirect(base_url('/scanner'));
	}

	public function index(): void
	{
		$this->requireScannerAuth();
		$assignedEvents = $_SESSION['scanner_assigned_events'] ?? [];
		view('scanner/index', compact('assignedEvents'));
	}

    public function verify(): void
	{
		$this->requireScannerAuth();
        $inputCode = trim($_POST['code'] ?? $_GET['code'] ?? '');
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        if ($inputCode === '') { echo json_encode(['ok'=>false,'msg'=>'No code']); return; }
		// Lookup ticket by code with tier information
		$stmt = db()->prepare('SELECT t.*, oi.event_id, oi.tier, e.organizer_id, e.title as event_title, e.venue FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id JOIN events e ON e.id = oi.event_id WHERE t.code = ? LIMIT 1');
		$stmt->execute([$inputCode]);
		$ticket = $stmt->fetch();
		if (!$ticket) { echo json_encode(['ok'=>false,'msg'=>'Not found']); return; }
		// Check if ticket is for one of the assigned events
		$assignedEventIds = array_column($_SESSION['scanner_assigned_events'] ?? [], 'id');
		if (!in_array((int)$ticket['event_id'], $assignedEventIds)) { 
			echo json_encode(['ok'=>false,'msg'=>'Ticket not for assigned event']); 
			return; 
		}
		if ($ticket['status'] !== 'valid') { echo json_encode(['ok'=>false,'msg'=>'Already redeemed']); return; }
		// Mark redeemed with scanner device tracking
		db()->prepare('UPDATE tickets SET status="redeemed", redeemed_at=NOW(), redeemed_by=?, scanner_device_id=? WHERE id=?')->execute([$ticket['organizer_id'], $_SESSION['scanner_device_id'], $ticket['id']]);
		
		// Format tier name for display
		$tierDisplay = ucwords(str_replace('_', ' ', $ticket['tier']));
		
		echo json_encode([
			'ok' => true,
			'msg' => 'Ticket valid & redeemed',
			'ticket_type' => $tierDisplay,
			'event_title' => $ticket['event_title'],
			'venue' => $ticket['venue']
		]);
	}
}



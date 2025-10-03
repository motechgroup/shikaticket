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
		
		// First try to find as event scanner device
		$device = ScannerDevice::findByDeviceCode($deviceCode);
		if ($device && $device['is_active']) {
			// Check if device is assigned to any active event
			$assignedEvents = ScannerDevice::getAssignedEvents($device['id']);
			if (!empty($assignedEvents)) {
				$_SESSION['scanner_device_id'] = $device['id'];
				$_SESSION['scanner_device_name'] = $device['device_name'];
				$_SESSION['scanner_device_code'] = $device['device_code'];
				$_SESSION['scanner_assigned_events'] = $assignedEvents;
				redirect(base_url('/scanner'));
				return;
			}
		}
		
		// If not found as event device, try travel scanner device
		$stmt = db()->prepare('
			SELECT tsd.*, ta.company_name 
			FROM travel_scanner_devices tsd
			JOIN travel_agencies ta ON tsd.travel_agency_id = ta.id
			WHERE tsd.device_code = ? AND tsd.is_active = 1
		');
		$stmt->execute([$deviceCode]);
		$travelDevice = $stmt->fetch();
		
		if ($travelDevice) {
			$_SESSION['scanner_device_id'] = $travelDevice['id'];
			$_SESSION['scanner_device_name'] = $travelDevice['device_name'];
			$_SESSION['scanner_device_code'] = $travelDevice['device_code'];
			$_SESSION['scanner_assigned_events'] = []; // No events for travel devices
			
			// Debug logging
			error_log('Travel scanner login: Device ID = ' . $travelDevice['id']);
			error_log('Travel scanner login: Session set successfully');
			
			redirect(base_url('/scanner'));
			return;
		}
		
		echo 'Invalid or inactive device code';
	}

	public function index(): void
	{
		$this->requireScannerAuth();
		$assignedEvents = $_SESSION['scanner_assigned_events'] ?? [];
		view('scanner/index', compact('assignedEvents'));
	}

    public function verify(): void
	{
		try {
			$this->requireScannerAuth();
			$inputCode = trim($_POST['code'] ?? $_GET['code'] ?? '');
			header('Content-Type: application/json');
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			
			// Debug logging
			error_log('Scanner verify: Input code = ' . $inputCode);
			error_log('Scanner verify: Session device ID = ' . ($_SESSION['scanner_device_id'] ?? 'NOT SET'));
			
			if ($inputCode === '') { 
				echo json_encode(['ok'=>false,'msg'=>'No code']); 
				return; 
			}
			
			// First try to find as event ticket
			$stmt = db()->prepare('SELECT t.*, oi.event_id, oi.tier, e.organizer_id, e.title as event_title, e.venue FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id JOIN events e ON e.id = oi.event_id WHERE t.code = ? LIMIT 1');
			$stmt->execute([$inputCode]);
			$ticket = $stmt->fetch();
			
			if ($ticket) {
				// This is an event ticket
				$assignedEventIds = array_column($_SESSION['scanner_assigned_events'] ?? [], 'id');
				if (!in_array((int)$ticket['event_id'], $assignedEventIds)) { 
					echo json_encode(['ok'=>false,'msg'=>'Ticket not for assigned event']); 
					return; 
				}
				if ($ticket['status'] !== 'valid') { 
					echo json_encode(['ok'=>false,'msg'=>'Already redeemed']); 
					return; 
				}
				
				// Mark redeemed with scanner device tracking
				$updateStmt = db()->prepare('UPDATE tickets SET status="redeemed", redeemed_at=NOW(), redeemed_by=?, scanner_device_id=? WHERE id=?');
				$updateStmt->execute([$ticket['organizer_id'], $_SESSION['scanner_device_id'], $ticket['id']]);
				
				// Format tier name for display
				$tierDisplay = ucwords(str_replace('_', ' ', $ticket['tier']));
				
				echo json_encode([
					'ok' => true,
					'msg' => 'Event ticket valid & redeemed',
					'ticket_type' => $tierDisplay,
					'event_title' => $ticket['event_title'],
					'venue' => $ticket['venue']
				]);
				return;
			}
			
			// If not an event ticket, try as travel booking reference
			$stmt = db()->prepare('
				SELECT tb.*, td.title as destination_title, ta.company_name, ta.id as agency_id
				FROM travel_bookings tb
				JOIN travel_destinations td ON tb.destination_id = td.id
				JOIN travel_agencies ta ON td.agency_id = ta.id
				WHERE tb.booking_reference = ? AND tb.status = "confirmed"
			');
			$stmt->execute([$inputCode]);
			$booking = $stmt->fetch();
			
			if ($booking) {
				// Check if already scanned
				$stmt = db()->prepare('SELECT * FROM travel_booking_scans WHERE booking_id = ? AND scanner_device_id = ?');
				$stmt->execute([$booking['id'], $_SESSION['scanner_device_id']]);
				$existingScan = $stmt->fetch();
				
				if ($existingScan) {
					echo json_encode(['ok'=>false,'msg'=>'Travel booking already scanned']);
					return;
				}
				
				// Record scan
				$insertStmt = db()->prepare('INSERT INTO travel_booking_scans (booking_id, scanner_device_id, scan_status) VALUES (?, ?, ?)');
				$insertStmt->execute([$booking['id'], $_SESSION['scanner_device_id'], 'valid']);
				
				echo json_encode([
					'ok' => true,
					'msg' => 'Travel booking verified',
					'destination' => $booking['destination_title'],
					'participants' => $booking['participants_count'],
					'travel_date' => $booking['travel_date']
				]);
				return;
			}
			
			// Not found
			echo json_encode(['ok'=>false,'msg'=>'Not found']);
			
		} catch (Exception $e) {
			// Log the error for debugging
			error_log('Scanner verify error: ' . $e->getMessage());
			error_log('Scanner verify trace: ' . $e->getTraceAsString());
			
			// Return a proper JSON error response
			header('Content-Type: application/json');
			echo json_encode([
				'ok' => false,
				'msg' => 'Server error: ' . $e->getMessage()
			]);
		}
	}
}



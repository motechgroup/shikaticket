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
            // Fetch active assignments (may be empty). We still allow login.
            $assignedEvents = ScannerDevice::getAssignedEvents($device['id']);
            $_SESSION['scanner_device_id'] = $device['id'];
            $_SESSION['scanner_device_name'] = $device['device_name'];
            $_SESSION['scanner_device_code'] = $device['device_code'];
            $_SESSION['scanner_assigned_events'] = $assignedEvents;
            $_SESSION['scanner_organizer_id'] = (int)($device['organizer_id'] ?? 0);
            redirect(base_url('/scanner'));
            return;
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
            $_SESSION['scanner_agency_id'] = (int)($travelDevice['travel_agency_id'] ?? 0);
            $_SESSION['scanner_organizer_id'] = 0; // No organizer for travel devices
            $_SESSION['is_travel_scanner'] = true; // Flag to identify travel scanner
			
			// Debug logging
			error_log('Travel scanner login: Device ID = ' . $travelDevice['id']);
			error_log('Travel scanner login: Agency ID = ' . $travelDevice['travel_agency_id']);
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

    public function debugSession(): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'session_device_id' => $_SESSION['scanner_device_id'] ?? 'NOT SET',
            'session_agency_id' => $_SESSION['scanner_agency_id'] ?? 'NOT SET',
            'session_organizer_id' => $_SESSION['scanner_organizer_id'] ?? 'NOT SET',
            'is_travel_scanner' => $_SESSION['is_travel_scanner'] ?? 'NOT SET',
            'session_id' => session_id(),
            'all_scanner_session_keys' => array_keys(array_filter($_SESSION, function($key) {
                return strpos($key, 'scanner_') === 0;
            }, ARRAY_FILTER_USE_KEY)),
            'all_session_keys' => array_keys($_SESSION),
            'auth_required' => !isset($_SESSION['scanner_device_id'])
        ]);
    }

    public function testScan(): void
    {
        // This endpoint bypasses authentication for testing
        header('Content-Type: application/json');
        
        $inputCode = trim($_GET['code'] ?? $_POST['code'] ?? '');
        
        if ($inputCode === '') {
            echo json_encode(['ok' => false, 'msg' => 'No code provided']);
            return;
        }
        
        // Test the exact verification logic without session requirements
        $stmt = db()->prepare('
            SELECT tb.id, tb.booking_reference, tb.participants_count, tb.travel_date,
                   td.title as destination_title, ta.company_name, ta.id as agency_id
            FROM travel_bookings tb
            JOIN travel_destinations td ON tb.destination_id = td.id
            JOIN travel_agencies ta ON td.agency_id = ta.id
            WHERE tb.booking_reference = ? 
            AND tb.status = "confirmed"
            LIMIT 1
        ');
        $stmt->execute([$inputCode]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            echo json_encode(['ok' => false, 'msg' => 'Booking not found']);
            return;
        }
        
        echo json_encode([
            'ok' => true,
            'msg' => 'Booking found (test mode)',
            'booking_id' => $booking['id'],
            'booking_reference' => $booking['booking_reference'],
            'destination' => $booking['destination_title'],
            'agency_id' => $booking['agency_id'],
            'participants' => $booking['participants_count']
        ]);
    }

    public function verify(): void
	{
		try {
			$this->requireScannerAuth();
			$inputCode = trim($_POST['code'] ?? $_GET['code'] ?? '');
			
			// Set response headers for better performance
			header('Content-Type: application/json');
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			header('Pragma: no-cache');
			header('Expires: 0');
			
		// Debug logging
		error_log('Scanner verify: Input code = ' . $inputCode);
		error_log('Scanner verify: Session device ID = ' . ($_SESSION['scanner_device_id'] ?? 'NOT SET'));
		error_log('Scanner verify: Session agency ID = ' . ($_SESSION['scanner_agency_id'] ?? 'NOT SET'));
		error_log('Scanner verify: Is travel scanner = ' . ($_SESSION['is_travel_scanner'] ?? 'NOT SET'));
		error_log('Scanner verify: Session ID = ' . session_id());
		error_log('Scanner verify: All session keys = ' . implode(', ', array_keys($_SESSION)));
			
			// Basic validation
			if ($inputCode === '') { 
				echo json_encode(['ok'=>false,'msg'=>'No code provided']); 
				return; 
			}
			
			// Validate input length (ticket codes should be 6 digits, booking refs can be longer)
			if (strlen($inputCode) < 4 || strlen($inputCode) > 20) {
				echo json_encode(['ok'=>false,'msg'=>'Invalid code format']); 
				return;
			}
			
			// Get session data once
			$scannerDeviceId = $_SESSION['scanner_device_id'] ?? null;
			$scannerOrganizerId = (int)($_SESSION['scanner_organizer_id'] ?? 0);
			$assignedEventIds = array_column($_SESSION['scanner_assigned_events'] ?? [], 'id');
			$scannerAgencyId = (int)($_SESSION['scanner_agency_id'] ?? 0);
			
			if (!$scannerDeviceId) {
				echo json_encode(['ok'=>false,'msg'=>'Scanner device not authenticated']); 
				return;
			}
			
			// Try event ticket first (most common case)
			$ticket = $this->verifyEventTicket($inputCode, $scannerDeviceId, $scannerOrganizerId, $assignedEventIds);
			if ($ticket) {
				echo json_encode($ticket);
				return;
			}
			
			// Try travel booking if not event ticket
			$booking = $this->verifyTravelBooking($inputCode, $scannerDeviceId, $scannerAgencyId);
			if ($booking) {
				echo json_encode($booking);
				return;
			}
			
			// Not found
			echo json_encode(['ok'=>false,'msg'=>'Code not found or invalid']);
			
		} catch (\Exception $e) {
			// Log the error for debugging
			error_log('Scanner verify error: ' . $e->getMessage());
			
			// Return a proper JSON error response
			header('Content-Type: application/json');
			echo json_encode([
				'ok' => false,
				'msg' => 'Server error occurred'
			]);
		}
	}
	
	private function verifyEventTicket(string $inputCode, $scannerDeviceId, int $scannerOrganizerId, array $assignedEventIds): ?array
	{
		// Optimized query with better indexing
		$stmt = db()->prepare('
			SELECT t.id, t.status, t.redeemed_at, t.redeemed_by, 
				   oi.event_id, oi.tier, 
				   e.organizer_id, e.title as event_title, e.venue
			FROM tickets t 
			JOIN order_items oi ON oi.id = t.order_item_id 
			JOIN events e ON e.id = oi.event_id 
			WHERE t.code = ? 
			LIMIT 1
		');
		$stmt->execute([$inputCode]);
		$ticket = $stmt->fetch();
		
		if (!$ticket) {
			return null;
		}
		
		// Check permissions
		if (!empty($assignedEventIds)) {
			if (!in_array((int)$ticket['event_id'], $assignedEventIds)) { 
				return ['ok'=>false,'msg'=>'Ticket not for assigned event']; 
			}
		} else {
			// No explicit assignments: allow if device belongs to same organizer
			if ($scannerOrganizerId > 0 && (int)$ticket['organizer_id'] !== $scannerOrganizerId) {
				return ['ok'=>false,'msg'=>'Ticket not permitted for this device'];
			}
		}
		
		// Check if already redeemed
		if ($ticket['status'] !== 'valid') { 
			return ['ok'=>false,'msg'=>'Already redeemed']; 
		}
		
		// Mark as redeemed with scanner device tracking
		$updateStmt = db()->prepare('
			UPDATE tickets 
			SET status="redeemed", redeemed_at=NOW(), redeemed_by=?, scanner_device_id=? 
			WHERE id=? AND status="valid"
		');
		$updateResult = $updateStmt->execute([$ticket['organizer_id'], $scannerDeviceId, $ticket['id']]);
		
		if (!$updateResult || $updateStmt->rowCount() === 0) {
			return ['ok'=>false,'msg'=>'Ticket redemption failed - may have been redeemed by another device'];
		}
		
		// Format tier name for display
		$tierDisplay = ucwords(str_replace('_', ' ', $ticket['tier'] ?? 'Standard'));
		
		return [
			'ok' => true,
			'msg' => 'Event ticket valid & redeemed',
			'ticket_type' => $tierDisplay,
			'event_title' => $ticket['event_title'],
			'venue' => $ticket['venue']
		];
	}
	
	private function verifyTravelBooking(string $inputCode, $scannerDeviceId, int $scannerAgencyId): ?array
	{
		// Check if this is a travel scanner device
		if ($scannerAgencyId === 0) {
			return null;
		}
		
        // Simple query - try by booking reference first (e.g. JBU0RK)
        $stmt = db()->prepare('
            SELECT tb.id, tb.booking_reference, tb.participants_count, tb.travel_date,
                   td.title as destination_title, ta.company_name, ta.id as agency_id
            FROM travel_bookings tb
            JOIN travel_destinations td ON tb.destination_id = td.id
            JOIN travel_agencies ta ON td.agency_id = ta.id
            WHERE tb.booking_reference = ? 
            AND tb.status = "confirmed"
            LIMIT 1
        ');
        $stmt->execute([$inputCode]);
        $booking = $stmt->fetch();
        
        // If not found by booking reference, try travel ticket code (e.g. 6-digit like 871569)
        if (!$booking) {
            $stmt = db()->prepare('
                SELECT tb.id, tb.booking_reference, tb.participants_count, tb.travel_date,
                       td.title as destination_title, ta.company_name, ta.id as agency_id
                FROM travel_tickets tt
                JOIN travel_bookings tb ON tb.id = tt.booking_id
                JOIN travel_destinations td ON tb.destination_id = td.id
                JOIN travel_agencies ta ON td.agency_id = ta.id
                WHERE tt.ticket_code = ? 
                AND tb.status = "confirmed"
                LIMIT 1
            ');
            $stmt->execute([$inputCode]);
            $booking = $stmt->fetch();
        }
        
        if (!$booking) {
            return null;
        }
		
		// Check permissions - same logic as events (scanner belongs to same agency)
		if ($scannerAgencyId > 0 && (int)$booking['agency_id'] !== $scannerAgencyId) {
			return ['ok'=>false,'msg'=>'Booking not permitted for this device'];
		}
		
		// Check if already scanned (same as event ticket redeemed check)
		$stmt = db()->prepare('
			SELECT id FROM travel_booking_scans 
			WHERE booking_id = ? AND scanner_device_id = ?
		');
		$stmt->execute([$booking['id'], $scannerDeviceId]);
		$existingScan = $stmt->fetch();
		
		if ($existingScan) {
			return ['ok'=>false,'msg'=>'Travel booking already scanned'];
		}
		
		// Record scan (same as marking ticket as redeemed)
		$insertStmt = db()->prepare('
			INSERT INTO travel_booking_scans (booking_id, scanner_device_id, scan_status, scanned_at) 
			VALUES (?, ?, "valid", NOW())
		');
		$insertResult = $insertStmt->execute([$booking['id'], $scannerDeviceId]);
		
		if (!$insertResult) {
			return ['ok'=>false,'msg'=>'Failed to record scan'];
		}
		
		return [
			'ok' => true,
			'msg' => 'Travel booking verified',
			'destination' => $booking['destination_title'],
			'participants' => $booking['participants_count'],
			'travel_date' => $booking['travel_date']
		];
	}
}



<?php
namespace App\Controllers;

class UserController
{
	public function dashboard(): void
	{
		require_user();
		$userId = (int)$_SESSION['user_id'];
		
		// Get user info
		$stmt = db()->prepare('SELECT first_name, last_name, email, phone, created_at FROM users WHERE id = ?');
		$stmt->execute([$userId]);
		$user = $stmt->fetch();
		
		// Get user statistics
		// Total orders
		$stmt = db()->prepare('SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_spent FROM orders WHERE user_id = ? AND status = "paid"');
		$stmt->execute([$userId]);
		$orderStats = $stmt->fetch();
		
		// Total tickets
		$stmt = db()->prepare('
			SELECT COUNT(t.id) as total_tickets, 
				   SUM(CASE WHEN t.status = "redeemed" THEN 1 ELSE 0 END) as redeemed_tickets
			FROM tickets t 
			JOIN order_items oi ON oi.id = t.order_item_id 
			JOIN orders o ON o.id = oi.order_id 
			WHERE o.user_id = ? AND o.status = "paid"
		');
		$stmt->execute([$userId]);
		$ticketStats = $stmt->fetch() ?: ['total_tickets' => 0, 'redeemed_tickets' => 0];
		
		// Total travel bookings
		$stmt = db()->prepare('SELECT COUNT(*) as total_bookings, COALESCE(SUM(total_amount), 0) as total_travel_spent FROM travel_bookings WHERE user_id = ? AND status = "confirmed"');
		$stmt->execute([$userId]);
		$travelStats = $stmt->fetch() ?: ['total_bookings' => 0, 'total_travel_spent' => 0];
		
		// Recent orders
		$stmt = db()->prepare('
			SELECT o.*, e.title as event_title, e.event_date, e.venue
			FROM orders o 
			JOIN order_items oi ON oi.order_id = o.id 
			JOIN events e ON e.id = oi.event_id 
			WHERE o.user_id = ? AND o.status = "paid"
			ORDER BY o.created_at DESC 
			LIMIT 5
		');
		$stmt->execute([$userId]);
		$recentOrders = $stmt->fetchAll();
		
		// Recent travel bookings
		$stmt = db()->prepare('
			SELECT tb.*, td.title as destination_title, td.departure_date, ta.company_name
			FROM travel_bookings tb
			JOIN travel_destinations td ON td.id = tb.destination_id
			JOIN travel_agencies ta ON ta.id = td.agency_id
			WHERE tb.user_id = ? AND tb.status = "confirmed"
			ORDER BY tb.booking_date DESC 
			LIMIT 3
		');
		$stmt->execute([$userId]);
		$recentBookings = $stmt->fetchAll();
		
		// Check for pending travel bookings with M-Pesa payments
		$stmt = db()->prepare('
			SELECT tb.id, tb.status, tp.payment_method, tp.payment_status, tp.transaction_reference
			FROM travel_bookings tb
			LEFT JOIN travel_payments tp ON tp.booking_id = tb.id AND tp.id = (SELECT MAX(id) FROM travel_payments WHERE booking_id = tb.id)
			WHERE tb.user_id = ? AND tb.status = "pending" AND tp.payment_method = "mpesa" AND tp.payment_status = "pending"
			ORDER BY tb.booking_date DESC
			LIMIT 1
		');
		$stmt->execute([$userId]);
		$pendingBooking = $stmt->fetch();
		
		view('user/dashboard', compact('user', 'orderStats', 'ticketStats', 'travelStats', 'recentOrders', 'recentBookings', 'pendingBooking'));
	}

	public function orders(): void
	{
		require_user();
        // Show only successful orders for a clean UX
        $stmt = db()->prepare("SELECT o.*, p.status AS payment_status
            FROM orders o
            LEFT JOIN payments p ON p.order_id = o.id AND p.id = (SELECT MAX(id) FROM payments WHERE order_id = o.id)
            WHERE o.user_id = ? AND o.status = 'paid'
            ORDER BY o.created_at DESC");
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

    public function travelBookings(): void
    {
        require_user();
        
        $stmt = db()->prepare('
            SELECT tb.*, td.title as destination_title, td.departure_date, 
                   ta.company_name, tp.payment_status
            FROM travel_bookings tb
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN travel_agencies ta ON ta.id = td.agency_id
            LEFT JOIN travel_payments tp ON tp.booking_id = tb.id AND tp.id = (
                SELECT MAX(id) FROM travel_payments WHERE booking_id = tb.id
            )
            WHERE tb.user_id = ?
            ORDER BY tb.booking_date DESC
        ');
        $stmt->execute([$_SESSION['user_id']]);
        $bookings = $stmt->fetchAll();
        
        view('user/travel_bookings', compact('bookings'));
    }

    public function travelBookingShow(): void
    {
        require_user();
        
        $id = (int)($_GET['id'] ?? 0);
        $stmt = db()->prepare('
            SELECT tb.*, td.title as destination_title, td.destination, td.departure_date, 
                   td.departure_location, ta.company_name, ta.contact_person, ta.email as agency_email, 
                   ta.phone as agency_phone
            FROM travel_bookings tb
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN travel_agencies ta ON ta.id = td.agency_id
            WHERE tb.id = ? AND tb.user_id = ?
        ');
        $stmt->execute([$id, $_SESSION['user_id']]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            redirect(base_url('/user/travel-bookings'));
        }
        
        // Get payment details
        $stmt = db()->prepare('SELECT * FROM travel_payments WHERE booking_id = ? ORDER BY id DESC');
        $stmt->execute([$id]);
        $payments = $stmt->fetchAll();
        
        // Get ticket details
        $stmt = db()->prepare('SELECT * FROM travel_tickets WHERE booking_id = ? ORDER BY id DESC');
        $stmt->execute([$id]);
        $tickets = $stmt->fetchAll();
        
        view('user/travel_booking_show', compact('booking', 'payments', 'tickets'));
    }
}



<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Setting;

class TravelBookingController
{
    public function create(): void
    {
        require_user();
        
        $destinationId = (int)($_POST['destination_id'] ?? 0);
        $participantsCount = (int)($_POST['participants_count'] ?? 1);
        
        if ($destinationId <= 0 || $participantsCount <= 0) {
            flash_set('error', 'Invalid booking data.');
            redirect(base_url('/travel'));
        }
        
        // Get destination details
        $stmt = db()->prepare('
            SELECT td.*, ta.company_name, ta.commission_rate
            FROM travel_destinations td 
            JOIN travel_agencies ta ON ta.id = td.agency_id 
            WHERE td.id = ? AND td.is_published = 1 AND ta.is_approved = 1 AND ta.is_active = 1
        ');
        $stmt->execute([$destinationId]);
        $destination = $stmt->fetch();
        
        if (!$destination) {
            flash_set('error', 'Destination not found or not available.');
            redirect(base_url('/travel'));
        }
        
        // Check if booking deadline has passed
        if (!empty($destination['booking_deadline']) && strtotime($destination['booking_deadline']) < time()) {
            flash_set('error', 'Booking deadline has passed for this destination.');
            redirect(base_url('/travel/destination?id=' . $destinationId));
        }
        
        // Check availability
        if ($participantsCount > (int)$destination['max_participants']) {
            flash_set('error', 'Too many participants. Maximum allowed: ' . $destination['max_participants']);
            redirect(base_url('/travel/destination?id=' . $destinationId));
        }
        
        if ($participantsCount < (int)$destination['min_participants']) {
            flash_set('error', 'Minimum participants required: ' . $destination['min_participants']);
            redirect(base_url('/travel/destination?id=' . $destinationId));
        }
        
        // Calculate total amount
        $unitPrice = (float)$destination['price'];
        $totalAmount = $unitPrice * $participantsCount;
        $currency = $destination['currency'];
        
        // Create booking
        try {
            // Generate booking reference
            $bookingReference = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
            
            $stmt = db()->prepare('
                INSERT INTO travel_bookings 
                (destination_id, user_id, booking_reference, participants_count, participant_details, total_amount, currency, status, special_requests, booking_date, travel_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
            ');
            $stmt->execute([
                $destinationId,
                $_SESSION['user_id'],
                $bookingReference,
                $participantsCount,
                '', // participant_details - can be filled later
                $totalAmount,
                $currency,
                'pending',
                '', // special_requests - can be filled later
                $destination['departure_date'] // travel_date
            ]);
            
            $bookingId = db()->lastInsertId();
            
            // Calculate and record featured commission if applicable
            require_once __DIR__ . '/FeaturedContentController.php';
            $baseCommission = 0; // Base commission is handled elsewhere
            FeaturedContentController::recordFeaturedCommission('travel_destination', $destinationId, $bookingId, null, $destination['agency_id'], null, $totalAmount, $currency, $baseCommission);
            
            // Redirect to checkout
            redirect(base_url('/travel/checkout?booking_id=' . $bookingId));
            
        } catch (\PDOException $e) {
            // Log the actual error for debugging
            error_log('Travel booking creation error: ' . $e->getMessage());
            flash_set('error', 'Failed to create booking: ' . $e->getMessage());
            redirect(base_url('/travel/destination?id=' . $destinationId));
        }
    }
    
    public function checkout(): void
    {
        require_user();
        
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        
        if ($bookingId <= 0) {
            flash_set('error', 'Invalid booking.');
            redirect(base_url('/travel'));
        }
        
        // Get booking details with destination and agency info
        $stmt = db()->prepare('
            SELECT tb.*, td.title as destination_title, td.destination, td.departure_date, 
                   td.departure_location, ta.company_name, ta.commission_rate
            FROM travel_bookings tb
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN travel_agencies ta ON ta.id = td.agency_id
            WHERE tb.id = ? AND tb.user_id = ?
        ');
        $stmt->execute([$bookingId, $_SESSION['user_id']]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            flash_set('error', 'Booking not found.');
            redirect(base_url('/travel'));
        }
        
        // If booking is already confirmed and user is coming from payment, redirect to booking page
        if ($booking['status'] === 'confirmed' && isset($_GET['mpesa_payment'])) {
            redirect(base_url('/user/travel-bookings/show?id=' . $bookingId . '&payment_success=1'));
        }
        
        // If booking is not pending, show error
        if ($booking['status'] !== 'pending') {
            flash_set('error', 'Booking already processed.');
            redirect(base_url('/travel'));
        }
        
        // Get user details
        $user = User::findById($_SESSION['user_id']);
        
        view('travel/checkout', compact('booking', 'user'));
    }
    
    public function payment(): void
    {
        require_user();
        
        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $provider = trim($_POST['provider'] ?? '');
        
        if ($bookingId <= 0 || $provider === '') {
            flash_set('error', 'Invalid payment data.');
            redirect(base_url('/travel'));
        }
        
        // Get booking details
        $stmt = db()->prepare('
            SELECT tb.*, td.title as destination_title, td.agency_id, ta.company_name, ta.commission_rate
            FROM travel_bookings tb
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN travel_agencies ta ON ta.id = td.agency_id
            WHERE tb.id = ? AND tb.user_id = ? AND tb.status = ?
        ');
        $stmt->execute([$bookingId, $_SESSION['user_id'], 'pending']);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            flash_set('error', 'Booking not found or already processed.');
            redirect(base_url('/travel'));
        }
        
        // Calculate commission
        $commissionRate = (float)($booking['commission_rate'] ?? 0);
        $commissionAmount = ($booking['total_amount'] * $commissionRate) / 100;
        $netAmount = $booking['total_amount'] - $commissionAmount;
        
        // Create payment record
        $stmt = db()->prepare('
            INSERT INTO travel_payments 
            (booking_id, agency_id, amount, currency, payment_method, payment_status, commission_amount, agency_amount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $bookingId,
            $booking['agency_id'],
            $booking['total_amount'],
            $booking['currency'],
            $provider,
            'initiated',
            $commissionAmount,
            $netAmount
        ]);
        
        $paymentId = db()->lastInsertId();
        
        // Redirect to payment gateway
        switch ($provider) {
            case 'mpesa':
                $this->initiateMpesa($booking, $paymentId);
                break;
            case 'flutterwave':
                $this->initiateFlutterwave($booking, $paymentId);
                break;
            case 'paypal':
                $this->initiatePayPal($booking, $paymentId);
                break;
            default:
                flash_set('error', 'Invalid payment provider.');
                redirect(base_url('/travel/checkout?booking_id=' . $bookingId));
        }
    }
    
    private function initiateMpesa(array $booking, int $paymentId): void
    {
        if (Setting::get('payments.mpesa.enabled', '0') !== '1') {
            flash_set('error', 'M-Pesa payments are currently disabled.');
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
        
        $phone = trim($_POST['phone'] ?? '');
        if (empty($phone)) {
            flash_set('error', 'Phone number is required for M-Pesa.');
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
        
        // Normalize phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 9 && substr($phone, 0, 1) !== '0') {
            $phone = '254' . $phone;
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // Create M-Pesa STK Push
        $businessShortCode = Setting::get('payments.mpesa.shortcode', '');
        $passkey = Setting::get('payments.mpesa.passkey', '');
        $timestamp = date('YmdHis');
        $password = base64_encode($businessShortCode . $passkey . $timestamp);
        
        // Get callback URL configuration (same as PaymentController)
        $configuredCallback = Setting::get('payments.mpesa.callback_url', '');
        $callbackUrl = $configuredCallback !== '' ? $configuredCallback : base_url('/pay/mpesa/callback');
        // Safety: if admin stored only a base URL, append the required callback path
        if (strpos($callbackUrl, '/pay/mpesa/callback') === false) {
            $callbackUrl = rtrim($callbackUrl, '/');
            $callbackUrl .= '/pay/mpesa/callback';
        }
        
        $payload = [
            'BusinessShortCode' => $businessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$booking['total_amount'],
            'PartyA' => $phone,
            'PartyB' => $businessShortCode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => 'TRAVEL' . $booking['id'],
            'TransactionDesc' => 'Travel booking #' . $booking['id']
        ];
        
        $env = Setting::get('payments.mpesa.env', 'sandbox');
        $baseUrl = $env === 'live' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';
        $consumerKey = Setting::get('payments.mpesa.consumer_key', '');
        $consumerSecret = Setting::get('payments.mpesa.consumer_secret', '');
        
        // Get access token
        $ch = curl_init($baseUrl . '/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Log token response for debugging
        error_log('M-Pesa Token Response: ' . $response);
        error_log('M-Pesa Token HTTP Code: ' . $httpCode);
        error_log('M-Pesa Callback URL: ' . $callbackUrl);
        
        if ($response === false || !empty($curlError)) {
            flash_set('error', 'Failed to connect to M-Pesa for authentication: ' . $curlError);
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
        
        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? '';
        
        if (empty($accessToken)) {
            $errorMsg = 'Invalid credentials or service unavailable';
            if (isset($tokenData['error'])) {
                $errorMsg = $tokenData['error'];
            } elseif (isset($tokenData['error_description'])) {
                $errorMsg = $tokenData['error_description'];
            } elseif ($httpCode !== 200) {
                $errorMsg = 'HTTP Error ' . $httpCode;
            }
            
            flash_set('error', 'Failed to initialize M-Pesa payment: ' . $errorMsg);
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
        
        // Initiate STK Push
        $ch = curl_init($baseUrl . '/mpesa/stkpush/v1/processrequest');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Log the response for debugging
        error_log('M-Pesa STK Push Response: ' . $response);
        error_log('M-Pesa HTTP Code: ' . $httpCode);
        if ($curlError) {
            error_log('M-Pesa CURL Error: ' . $curlError);
        }
        
        if ($response === false || !empty($curlError)) {
            flash_set('error', 'Failed to connect to M-Pesa: ' . $curlError);
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
            // Update payment with checkout ID
            db()->prepare('UPDATE travel_payments SET transaction_reference = ? WHERE id = ?')
                ->execute([$result['CheckoutRequestID'], $paymentId]);
            
            flash_set('success', 'M-Pesa payment initiated. Please check your phone and enter your PIN.');
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id'] . '&mpesa_payment=1'));
        } else {
            $errorMsg = 'Unknown error';
            if (isset($result['ResponseDescription'])) {
                $errorMsg = $result['ResponseDescription'];
            } elseif (isset($result['errorMessage'])) {
                $errorMsg = $result['errorMessage'];
            } elseif (isset($result['error'])) {
                $errorMsg = $result['error'];
            } elseif ($httpCode !== 200) {
                $errorMsg = 'HTTP Error ' . $httpCode;
            }
            
            flash_set('error', 'Failed to initiate M-Pesa payment: ' . $errorMsg);
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
    }
    
    private function initiateFlutterwave(array $booking, int $paymentId): void
    {
        if (Setting::get('payments.flutterwave.enabled', '0') !== '1') {
            flash_set('error', 'Flutterwave payments are currently disabled.');
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
        
        $publicKey = Setting::get('payments.flutterwave.public_key', '');
        $secretKey = Setting::get('payments.flutterwave.secret_key', '');
        
        if (empty($publicKey) || empty($secretKey)) {
            flash_set('error', 'Flutterwave not configured.');
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
        
        $user = User::findById($_SESSION['user_id']);
        $amount = number_format((float)$booking['total_amount'], 2, '.', '');
        $currency = strtoupper($booking['currency'] ?? 'KES');
        $txRef = 'TRAVEL' . $booking['id'] . '-' . time();
        
        $payload = [
            'tx_ref' => $txRef,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => base_url('/pay/flutterwave'),
            'customer' => [
                'email' => $user['email'] ?? 'guest@example.com',
                'phonenumber' => preg_replace('/\D+/', '', $user['phone'] ?? ''),
                'name' => trim(($user['name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            ],
            'customizations' => [
                'title' => 'Travel Booking #' . $booking['id'],
                'description' => $booking['destination_title'],
            ],
        ];
        
        $ch = curl_init('https://api.flutterwave.com/v3/payments');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $secretKey
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);
        $result = json_decode($response, true);
        
        if (isset($result['data']['link']) && !empty($result['data']['link'])) {
            // Update payment with transaction reference
            db()->prepare('UPDATE travel_payments SET transaction_reference = ? WHERE id = ?')
                ->execute([$txRef, $paymentId]);
            
            // Redirect to Flutterwave
            header('Location: ' . $result['data']['link']);
            exit;
        } else {
            flash_set('error', 'Failed to initiate Flutterwave payment.');
            redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
        }
    }
    
    private function initiatePayPal(array $booking, int $paymentId): void
    {
        flash_set('error', 'PayPal integration for travel bookings is coming soon.');
        redirect(base_url('/travel/checkout?booking_id=' . $booking['id']));
    }

    public function checkPaymentStatus(): void
    {
        require_user();
        
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        
        if ($bookingId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid booking ID']);
            return;
        }
        
        // Get booking and payment status
        $stmt = db()->prepare('
            SELECT tb.status as booking_status, tp.payment_status, tp.payment_method, tp.transaction_reference
            FROM travel_bookings tb
            LEFT JOIN travel_payments tp ON tp.booking_id = tb.id AND tp.id = (
                SELECT MAX(id) FROM travel_payments WHERE booking_id = tb.id
            )
            WHERE tb.id = ? AND tb.user_id = ?
        ');
        $stmt->execute([$bookingId, $_SESSION['user_id']]);
        $result = $stmt->fetch();
        
        if (!$result) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
            return;
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'booking_status' => $result['booking_status'],
            'payment_status' => $result['payment_status'] ?? 'none',
            'payment_method' => $result['payment_method'] ?? null,
            'transaction_reference' => $result['transaction_reference'] ?? null
        ]);
    }

    public function reconcilePayment(): void
    {
        // Add debug logging at the very start
        error_log("TravelBookingController::reconcilePayment - Method called");
        error_log("TravelBookingController::reconcilePayment - POST data: " . json_encode($_POST));
        error_log("TravelBookingController::reconcilePayment - GET data: " . json_encode($_GET));
        
        require_user();
        
        // Handle both POST and GET requests
        $bookingId = (int)($_POST['booking_id'] ?? $_GET['booking_id'] ?? 0);
        $paymentMethod = trim($_POST['payment_method'] ?? $_GET['payment_method'] ?? '');
        
        // Add debug logging
        error_log("TravelBookingController::reconcilePayment - Booking ID: $bookingId, Payment Method: $paymentMethod");
        
        if ($bookingId <= 0 || $paymentMethod === '') {
            error_log("TravelBookingController::reconcilePayment - Invalid data: bookingId=$bookingId, paymentMethod='$paymentMethod'");
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid reconciliation data.']);
            return;
        }
        
        // Get booking details
        $stmt = db()->prepare('
            SELECT tb.*, tp.id as payment_id, tp.transaction_reference, tp.payment_status
            FROM travel_bookings tb
            LEFT JOIN travel_payments tp ON tp.booking_id = tb.id AND tp.id = (
                SELECT MAX(id) FROM travel_payments WHERE booking_id = tb.id
            )
            WHERE tb.id = ? AND tb.user_id = ?
        ');
        $stmt->execute([$bookingId, $_SESSION['user_id']]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            error_log("TravelBookingController::reconcilePayment - Booking not found for ID: $bookingId");
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found.']);
            return;
        }
        
        if ($booking['status'] === 'confirmed') {
            error_log("TravelBookingController::reconcilePayment - Booking already confirmed: $bookingId");
            echo json_encode(['success' => true, 'message' => 'Booking is already confirmed!', 'redirect' => base_url('/user/travel-bookings/show?id=' . $bookingId)]);
            return;
        }
        
        // Attempt to reconcile based on payment method
        $reconciled = false;
        
        if ($paymentMethod === 'mpesa' && !empty($booking['transaction_reference'])) {
            error_log("TravelBookingController::reconcilePayment - Attempting M-Pesa reconciliation for booking: $bookingId");
            $reconciled = $this->reconcileMpesaPayment($booking);
        } elseif ($paymentMethod === 'flutterwave' && !empty($booking['transaction_reference'])) {
            error_log("TravelBookingController::reconcilePayment - Attempting Flutterwave reconciliation for booking: $bookingId");
            $reconciled = $this->reconcileFlutterwavePayment($booking);
        } else {
            error_log("TravelBookingController::reconcilePayment - No valid payment method or transaction reference found");
        }
        
        if ($reconciled) {
            error_log("TravelBookingController::reconcilePayment - Payment successfully reconciled for booking: $bookingId");
            echo json_encode(['success' => true, 'message' => 'Payment confirmed! Your travel booking is now confirmed.', 'redirect' => base_url('/user/travel-bookings/show?id=' . $bookingId . '&payment_success=1')]);
        } else {
            error_log("TravelBookingController::reconcilePayment - Payment reconciliation failed for booking: $bookingId");
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Payment not yet confirmed. Please try again in a few moments.']);
        }
    }
    
    private function reconcileMpesaPayment(array $booking): bool
    {
        error_log("TravelBookingController::reconcileMpesaPayment - Starting reconciliation for booking: " . $booking['id']);
        
        if (empty($booking['transaction_reference'])) {
            error_log("TravelBookingController::reconcileMpesaPayment - No transaction reference found");
            return false;
        }
        
        error_log("TravelBookingController::reconcileMpesaPayment - Transaction reference: " . $booking['transaction_reference']);
        
        $businessShortCode = Setting::get('payments.mpesa.shortcode', '');
        $passkey = Setting::get('payments.mpesa.passkey', '');
        $consumerKey = Setting::get('payments.mpesa.consumer_key', '');
        $consumerSecret = Setting::get('payments.mpesa.consumer_secret', '');
        
        if (empty($businessShortCode) || empty($passkey) || empty($consumerKey) || empty($consumerSecret)) {
            return false;
        }
        
        $env = Setting::get('payments.mpesa.env', 'sandbox');
        $baseUrl = $env === 'live' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';
        
        // Get access token
        $ch = curl_init($baseUrl . '/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)],
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);
        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? '';
        
        if (empty($accessToken)) {
            return false;
        }
        
        // Query STK status
        $timestamp = date('YmdHis');
        $password = base64_encode($businessShortCode . $passkey . $timestamp);
        
        $payload = [
            'BusinessShortCode' => $businessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $booking['transaction_reference']
        ];
        
        $ch = curl_init($baseUrl . '/mpesa/stkpushquery/v1/query');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);
        $result = json_decode($response, true);
        
        error_log("TravelBookingController::reconcileMpesaPayment - STK Query Response: " . json_encode($result));
        
        if (isset($result['ResultCode']) && $result['ResultCode'] === '0') {
            // Payment successful
            $mpesaRef = $result['MpesaReceiptNumber'] ?? $booking['transaction_reference'];
            
            db()->prepare('UPDATE travel_payments SET payment_status = ?, transaction_reference = ?, paid_at = NOW() WHERE id = ?')
                ->execute(['paid', $mpesaRef, $booking['payment_id']]);
            
            db()->prepare('UPDATE travel_bookings SET status = ? WHERE id = ?')
                ->execute(['confirmed', $booking['id']]);
            
            // Send confirmation email and SMS
            error_log("TravelBookingController::reconcileMpesaPayment - Calling sendTravelBookingConfirmation for booking: " . $booking['id']);
            $this->sendTravelBookingConfirmation($booking['id']);
            error_log("TravelBookingController::reconcileMpesaPayment - sendTravelBookingConfirmation completed for booking: " . $booking['id']);

            // Award loyalty points for confirmed travel booking (configurable)
            try {
                $perHundred = (int)Setting::get('loyalty.points.travel_per_100_kes', '1');
                if ($perHundred < 0) { $perHundred = 0; }
                $points = (int)floor(((float)$booking['total_amount'] / 100) * $perHundred);
                if ($points > 0) {
                    db()->prepare('INSERT INTO user_points (user_id, points, reason, reference_type, reference_id, created_at) VALUES ((SELECT user_id FROM travel_bookings WHERE id = ?), ?, ?, ?, ?, NOW())')
                        ->execute([$booking['id'], $points, 'Travel booking reward', 'travel_booking', $booking['id']]);
                }
            } catch (\Throwable $e) { /* ignore */ }
            
            error_log("TravelBookingController::reconcileMpesaPayment - Payment successfully reconciled for booking: " . $booking['id']);
            return true;
        }
        
        return false;
    }
    
    private function reconcileFlutterwavePayment(array $booking): bool
    {
        if (empty($booking['transaction_reference'])) {
            return false;
        }
        
        $secretKey = Setting::get('payments.flutterwave.secret_key', '');
        if (empty($secretKey)) {
            return false;
        }
        
        // Extract transaction ID from tx_ref
        $txRef = $booking['transaction_reference'];
        if (strpos($txRef, 'TRAVEL') !== 0) {
            return false;
        }
        
        // For Flutterwave, we need to get the actual transaction ID from the callback
        // For now, we'll check if there's a successful payment record
        $stmt = db()->prepare('SELECT * FROM travel_payments WHERE booking_id = ? AND payment_status = ?');
        $stmt->execute([$booking['id'], 'paid']);
        $successfulPayment = $stmt->fetch();
        
        if ($successfulPayment) {
            // Update booking status
            db()->prepare('UPDATE travel_bookings SET status = ? WHERE id = ?')
                ->execute(['confirmed', $booking['id']]);
            
            // Send confirmation email
            $this->sendTravelBookingConfirmation($booking['id']);
            
            return true;
        }
        
        return false;
    }
    
    private function sendTravelBookingConfirmation(int $bookingId): void
    {
        try {
            $stmt = db()->prepare('
                SELECT tb.*, td.title as destination_title, td.departure_date, td.departure_location,
                       ta.company_name, ta.contact_person, ta.email as agency_email, ta.phone as agency_phone,
                       u.email as user_email, u.phone as user_phone
                FROM travel_bookings tb
                JOIN travel_destinations td ON td.id = tb.destination_id
                JOIN travel_agencies ta ON ta.id = td.agency_id
                JOIN users u ON u.id = tb.user_id
                WHERE tb.id = ?
            ');
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) return;
            
            // Generate travel ticket code
            $ticketCode = substr(str_shuffle('0123456789'), 0, 6);
            
            // Create travel ticket record
            $stmt = db()->prepare('
                INSERT INTO travel_tickets (booking_id, ticket_code, status, created_at) 
                VALUES (?, ?, "valid", NOW())
            ');
            $stmt->execute([$bookingId, $ticketCode]);

            // Generate and store local QR image (match events QR behavior)
            try {
                // Ensure directory exists
                $dir = __DIR__ . '/../../uploads/qrs/travel/';
                if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                $qrPathRel = 'uploads/qrs/travel/' . $ticketCode . '.png';
                $qrPathAbs = $dir . $ticketCode . '.png';
                // Generate via external API and store locally
                $qrData = @file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ticketCode));
                if ($qrData !== false) {
                    file_put_contents($qrPathAbs, $qrData);
                    // Save path in DB if column exists (safe update)
                    try { db()->prepare('UPDATE travel_tickets SET qr_path = ? WHERE ticket_code = ?')->execute([$qrPathRel, $ticketCode]); } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {
                error_log('Failed to generate local QR for travel ticket: ' . $e->getMessage());
            }
            
            // Send email confirmation
            $mailer = new \App\Services\Mailer();
            $subject = 'Travel Booking Confirmation - ' . $booking['destination_title'];
            
            $html = "
                <h2>Travel Booking Confirmation</h2>
                <p>Dear Customer,</p>
                <p>Your travel booking has been confirmed successfully!</p>
                
                <h3>Booking Details</h3>
                <ul>
                    <li><strong>Destination:</strong> " . htmlspecialchars($booking['destination_title']) . "</li>
                    <li><strong>Departure Date:</strong> " . date('M j, Y', strtotime($booking['departure_date'])) . "</li>
                    <li><strong>Departure Location:</strong> " . htmlspecialchars($booking['departure_location']) . "</li>
                    <li><strong>Participants:</strong> " . $booking['participants_count'] . " person(s)</li>
                    <li><strong>Total Amount:</strong> " . $booking['currency'] . " " . number_format($booking['total_amount'], 2) . "</li>
                    <li><strong>Ticket Code:</strong> " . $ticketCode . "</li>
                </ul>
                
                <h3>Travel Agency Contact</h3>
                <ul>
                    <li><strong>Agency:</strong> " . htmlspecialchars($booking['company_name']) . "</li>
                    <li><strong>Contact Person:</strong> " . htmlspecialchars($booking['contact_person']) . "</li>
                    <li><strong>Email:</strong> " . htmlspecialchars($booking['agency_email']) . "</li>
                    <li><strong>Phone:</strong> " . htmlspecialchars($booking['agency_phone']) . "</li>
                </ul>
                
                <p>Please keep this confirmation email and your ticket code for your travel.</p>
                <p>Thank you for choosing our travel services!</p>
            ";
            
            $mailer->send($booking['user_email'], $subject, $html);
            
            // Send SMS notification
            try {
                error_log("TravelBookingController::sendTravelBookingConfirmation - Attempting to send SMS for booking: " . $bookingId);
                
                $sms = new \App\Services\Sms();
                if ($sms->isConfigured()) {
                    error_log("TravelBookingController::sendTravelBookingConfirmation - SMS service is configured");
                    
                    // Generate public ticket link (no login required)
                    $ticketLink = base_url('/travel-tickets/view') . '?code=' . urlencode($ticketCode);
                    
                    // Use SMS template
                    $message = \App\Services\SmsTemplates::render('travel_booking_confirmed', [
                        'destination' => $booking['destination_title'],
                        'ticket_code' => $ticketCode,
                        'booking_reference' => $booking['booking_reference'] ?? $booking['id'],
                        'ticket_link' => $ticketLink,
                        'agency_name' => $booking['company_name'],
                        'agency_phone' => $booking['agency_phone']
                    ]);
                    
                    error_log("TravelBookingController::sendTravelBookingConfirmation - SMS template result: " . $message);
                    
                    // Fallback if no template is configured
                    if ($message === '') {
                        $message = "Travel booking confirmed! Destination: " . $booking['destination_title'] . 
                                  ". Ticket Code: " . $ticketCode . 
                                  ". View ticket: " . $ticketLink .
                                  ". Contact: " . $booking['company_name'] . " at " . $booking['agency_phone'];
                        error_log("TravelBookingController::sendTravelBookingConfirmation - Using fallback SMS message");
                    }
                    
                    error_log("TravelBookingController::sendTravelBookingConfirmation - Sending SMS to: " . $booking['user_phone']);
                    error_log("TravelBookingController::sendTravelBookingConfirmation - SMS message: " . $message);
                    
                    $result = $sms->send($booking['user_phone'], $message);
                    error_log("TravelBookingController::sendTravelBookingConfirmation - SMS send result: " . json_encode($result));
                } else {
                    error_log("TravelBookingController::sendTravelBookingConfirmation - SMS service is not configured");
                }
            } catch (\Throwable $e) {
                // SMS sending failed, but email was sent
                error_log("TravelBookingController::sendTravelBookingConfirmation - SMS sending failed for travel booking: " . $e->getMessage());
                error_log("TravelBookingController::sendTravelBookingConfirmation - SMS error trace: " . $e->getTraceAsString());
            }
            
        } catch (\Throwable $e) {
            // Log error but don't fail the callback
        }
    }
    
    public function confirmPayment(): void
    {
        require_user();
        
        $bookingId = (int)($_GET['booking_id'] ?? 0);
        
        if ($bookingId <= 0) {
            flash_set('error', 'Invalid booking.');
            redirect(base_url('/travel'));
        }
        
        // Get booking details
        $stmt = db()->prepare('
            SELECT tb.*, td.title as destination_title, td.departure_date, 
                   td.departure_location, ta.company_name
            FROM travel_bookings tb
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN travel_agencies ta ON ta.id = td.agency_id
            WHERE tb.id = ? AND tb.user_id = ?
        ');
        $stmt->execute([$bookingId, $_SESSION['user_id']]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            flash_set('error', 'Booking not found.');
            redirect(base_url('/travel'));
        }
        
        // Get payment details
        $stmt = db()->prepare('SELECT * FROM travel_payments WHERE booking_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$bookingId]);
        $payment = $stmt->fetch();
        
        view('travel/booking_confirmation', compact('booking', 'payment'));
    }
    
    public function viewTicket(): void
    {
        $ticketCode = trim($_GET['code'] ?? '');
        if ($ticketCode === '') { 
            echo 'Missing ticket code'; 
            return; 
        }
        
        // Get ticket details with booking and destination information
        $stmt = db()->prepare('
            SELECT tt.*, tb.*, td.title as destination_title, td.departure_date, 
                   td.departure_location, ta.company_name, ta.contact_person,
                   ta.email as agency_email, ta.phone as agency_phone,
                   u.phone as user_phone, u.email as user_email
            FROM travel_tickets tt
            JOIN travel_bookings tb ON tb.id = tt.booking_id
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN travel_agencies ta ON ta.id = td.agency_id
            JOIN users u ON u.id = tb.user_id
            WHERE tt.ticket_code = ? LIMIT 1
        ');
        $stmt->execute([$ticketCode]);
        $ticket = $stmt->fetch();
        
        if (!$ticket) { 
            echo 'Ticket not found'; 
            return; 
        }
        
        // Generate QR code URL via our local endpoint to avoid CSP
        $qrUrl = base_url('/travel-tickets/qr?code=' . urlencode($ticketCode));
        
        include __DIR__ . '/../Views/travel/ticket_view.php';
    }
}

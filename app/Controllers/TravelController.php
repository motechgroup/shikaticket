<?php
namespace App\Controllers;

use App\Models\TravelAgency;

class TravelController
{
    public function dashboard(): void
    {
        require_travel_agency();
        
        $agencyId = $_SESSION['travel_agency_id'];
        $agency = TravelAgency::findById($agencyId);
        
        // Get destinations count
        $destinations = TravelAgency::getDestinationsByAgency($agencyId);
        $destinationsCount = count($destinations);
        $featuredCount = count(array_filter($destinations, fn($d) => $d['is_featured']));
        
        // Get bookings count
        $bookingsStmt = db()->prepare('
            SELECT COUNT(*) as total_bookings,
                   SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_bookings,
                   SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_bookings
            FROM travel_bookings 
            WHERE destination_id IN (
                SELECT id FROM travel_destinations WHERE agency_id = ?
            )
        ');
        $bookingsStmt->execute([$agencyId]);
        $bookings = $bookingsStmt->fetch();
        
        // Get recent bookings
        $recentBookings = db()->prepare('
            SELECT tb.*, td.title as destination_title, u.email as customer_name, u.email as customer_email
            FROM travel_bookings tb
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN users u ON u.id = tb.user_id
            WHERE td.agency_id = ?
            ORDER BY tb.booking_date DESC
            LIMIT 10
        ');
        $recentBookings->execute([$agencyId]);
        $recentBookings = $recentBookings->fetchAll();
        
        travel_view('travel/dashboard', compact('agency', 'destinationsCount', 'featuredCount', 'bookings', 'recentBookings'));
    }

    public function destinations(): void
    {
        require_travel_agency();
        
        $agencyId = $_SESSION['travel_agency_id'];
        $destinations = TravelAgency::getDestinationsByAgency($agencyId);
        
        // Get admin-set commission rate for destinations
        $commissionRate = $this->getCommissionRate('destination_featured_commission');
        
        travel_view('travel/destinations/index', compact('destinations', 'commissionRate'));
    }
    
    /**
     * Get commission rate from settings
     */
    private function getCommissionRate(string $key): float
    {
        try {
            $stmt = db()->prepare('SELECT value FROM settings WHERE `key` = ?');
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            
            return $result ? (float)$result['value'] : 5.00; // Default to 5%
        } catch (\Exception $e) {
            return 5.00; // Default to 5% on error
        }
    }

    public function destinationCreate(): void
    {
        require_travel_agency();
        // Only allow creating destinations if the agency phone is verified
        $agency = \App\Models\TravelAgency::findById((int)($_SESSION['travel_agency_id'] ?? 0));
        if (!$agency || (int)($agency['phone_verified'] ?? 0) !== 1) {
            flash_set('error', 'Your phone number is not verified yet. You can browse your dashboard, but you cannot create destinations until verification is completed.');
            redirect(base_url('/travel/dashboard'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'agency_id' => $_SESSION['travel_agency_id'],
                'title' => trim($_POST['title'] ?? ''),
                'destination' => trim($_POST['destination'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'duration_days' => (int)($_POST['duration_days'] ?? 1),
                'price' => (float)($_POST['price'] ?? 0),
                'currency' => trim($_POST['currency'] ?? 'KES'),
                'max_participants' => (int)($_POST['max_participants'] ?? 50),
                'min_participants' => (int)($_POST['min_participants'] ?? 1),
                'departure_location' => trim($_POST['departure_location'] ?? ''),
                'departure_date' => $_POST['departure_date'] ?? '',
                'return_date' => $_POST['return_date'] ?? '',
                'booking_deadline' => $_POST['booking_deadline'] ?? '',
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'is_published' => isset($_POST['is_published']) ? 1 : 0,
                'children_allowed' => isset($_POST['children_allowed']) ? 1 : 0,
                'includes' => json_encode(array_filter(explode("\n", $_POST['includes'] ?? ''))),
                'excludes' => json_encode(array_filter(explode("\n", $_POST['excludes'] ?? ''))),
                'requirements' => json_encode(array_filter(explode("\n", $_POST['requirements'] ?? ''))),
                'itinerary' => json_encode(array_filter(explode("\n", $_POST['itinerary'] ?? '')))
            ];

            // Validation
            if ($data['title'] === '' || $data['destination'] === '' || 
                $data['price'] <= 0 || $data['departure_date'] === '') {
                flash_set('error', 'Please fill all required fields.');
                redirect(base_url('/travel/destinations/create'));
            }

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/travel/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'destination_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image_path'] = 'uploads/travel/' . $filename;
                }
            }
            // Handle gallery uploads
            $galleryPaths = [];
            if (!empty($_FILES['gallery']['name']) && is_array($_FILES['gallery']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/travel/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                foreach ($_FILES['gallery']['name'] as $idx => $name) {
                    if (($_FILES['gallery']['error'][$idx] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) { continue; }
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $file = 'gallery_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                    $dest = $uploadDir . $file;
                    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$idx], $dest)) {
                        $galleryPaths[] = 'uploads/travel/' . $file;
                    }
                }
            }

            try {
                $stmt = db()->prepare('
                    INSERT INTO travel_destinations 
                    (agency_id, title, destination, description, duration_days, price, currency, 
                     max_participants, min_participants, departure_location, departure_date, 
                     return_date, booking_deadline, is_featured, is_published, children_allowed, image_path, gallery_paths,
                     includes, excludes, requirements, itinerary)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ');
                
                if ($stmt->execute([
                    $data['agency_id'], $data['title'], $data['destination'], $data['description'],
                    $data['duration_days'], $data['price'], $data['currency'], $data['max_participants'],
                    $data['min_participants'], $data['departure_location'], $data['departure_date'],
                    $data['return_date'], $data['booking_deadline'], $data['is_featured'],
                    $data['is_published'], $data['children_allowed'], $data['image_path'] ?? null, json_encode($galleryPaths), $data['includes'],
                    $data['excludes'], $data['requirements'], $data['itinerary']
                ])) {
                    flash_set('success', 'Destination created successfully!');
                    redirect(base_url('/travel/destinations'));
                } else {
                    flash_set('error', 'Failed to create destination.');
                    redirect(base_url('/travel/destinations/create'));
                }
            } catch (\PDOException $e) {
                flash_set('error', 'Failed to create destination.');
                redirect(base_url('/travel/destinations/create'));
            }
        }

        travel_view('travel/destinations/create');
    }

    public function destinationEdit(): void
    {
        require_travel_agency();
        $agencyId = (int)($_SESSION['travel_agency_id'] ?? 0);
        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        if ($id <= 0) { redirect(base_url('/travel/destinations')); }
        $stmt = db()->prepare('SELECT * FROM travel_destinations WHERE id = ? AND agency_id = ?');
        $stmt->execute([$id, $agencyId]);
        $dest = $stmt->fetch();
        if (!$dest) { redirect(base_url('/travel/destinations')); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fields = ['title','destination','description','duration_days','price','currency','max_participants','min_participants','departure_location','departure_date','return_date','booking_deadline'];
            $updates = [];$values=[];
            foreach ($fields as $f) { $updates[] = "$f = ?"; $values[] = trim($_POST[$f] ?? (string)($dest[$f] ?? '')); }
            $childrenAllowed = isset($_POST['children_allowed']) ? 1 : 0; $updates[]='children_allowed=?'; $values[]=$childrenAllowed;
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0; $updates[]='is_featured=?'; $values[]=$isFeatured;
            $isPublished = isset($_POST['is_published']) ? 1 : 0; $updates[]='is_published=?'; $values[]=$isPublished;
            // Optional new image
            if (isset($_FILES['image']) && $_FILES['image']['error']===UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/travel/'; if (!is_dir($uploadDir)) { mkdir($uploadDir,0777,true);} 
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file = 'destination_' . time() . '_' . rand(1000,9999) . '.' . $ext; $destPath = $uploadDir . $file;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) { $updates[]='image_path=?'; $values[]='uploads/travel/' . $file; }
            }
            // Optional gallery additions (append)
            $gallery = json_decode($dest['gallery_paths'] ?? '[]', true) ?: [];
            if (!empty($_FILES['gallery']['name']) && is_array($_FILES['gallery']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/travel/'; if (!is_dir($uploadDir)) { mkdir($uploadDir,0777,true);} 
                foreach ($_FILES['gallery']['name'] as $i=>$n) {
                    if (($_FILES['gallery']['error'][$i] ?? UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) continue;
                    $ext = pathinfo($n, PATHINFO_EXTENSION);
                    $file = 'gallery_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $uploadDir.$file)) { $gallery[]='uploads/travel/' . $file; }
                }
                $updates[]='gallery_paths=?'; $values[]=json_encode($gallery);
            }
            $values[]=$id; $values[]=$agencyId;
            $sql = 'UPDATE travel_destinations SET ' . implode(', ', $updates) . ' WHERE id = ? AND agency_id = ?';
            db()->prepare($sql)->execute($values);
            flash_set('success','Destination updated.');
            redirect(base_url('/travel/destinations'));
        }

        // Render edit view quickly by reusing create form with prefill (simple version)
        travel_view('travel/destinations/create', ['dest' => $dest]);
    }

    public function bookings(): void
    {
        require_travel_agency();
        
        $agencyId = $_SESSION['travel_agency_id'];
        
        $bookings = db()->prepare('
            SELECT tb.*, td.title as destination_title, u.email as customer_name, u.email as customer_email, u.phone as customer_phone
            FROM travel_bookings tb
            JOIN travel_destinations td ON td.id = tb.destination_id
            JOIN users u ON u.id = tb.user_id
            WHERE td.agency_id = ?
            ORDER BY tb.booking_date DESC
        ');
        $bookings->execute([$agencyId]);
        $bookings = $bookings->fetchAll();
        
        travel_view('travel/bookings', compact('bookings'));
    }

    public function profile(): void
    {
        require_travel_agency();
        
        $agencyId = $_SESSION['travel_agency_id'];
        $agency = TravelAgency::findById($agencyId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'company_name' => trim($_POST['company_name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'country' => trim($_POST['country'] ?? ''),
                'website' => trim($_POST['website'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];

            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/travel/agencies/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'agency_' . $agencyId . '_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                    $data['logo_path'] = 'uploads/travel/agencies/' . $filename;
                }
            }

            try {
                $updateFields = [];
                $updateValues = [];
                
                foreach ($data as $field => $value) {
                    $updateFields[] = "$field = ?";
                    $updateValues[] = $value;
                }
                
                if (isset($data['logo_path'])) {
                    $updateFields[] = "logo_path = ?";
                    $updateValues[] = $data['logo_path'];
                }
                
                $updateValues[] = $agencyId;
                
                $stmt = db()->prepare('UPDATE travel_agencies SET ' . implode(', ', $updateFields) . ' WHERE id = ?');
                
                if ($stmt->execute($updateValues)) {
                    flash_set('success', 'Profile updated successfully!');
                    redirect(base_url('/travel/profile'));
                } else {
                    flash_set('error', 'Failed to update profile.');
                    redirect(base_url('/travel/profile'));
                }
            } catch (\PDOException $e) {
                flash_set('error', 'Failed to update profile.');
                redirect(base_url('/travel/profile'));
            }
        }

        travel_view('travel/profile', compact('agency'));
    }

    public function savePaymentInfo(): void
    {
        require_travel_agency();
        verify_csrf();
        
        $payoutMethod = trim($_POST['payout_method'] ?? '');
        $bankName = trim($_POST['bank_name'] ?? '');
        $bankAccountName = trim($_POST['bank_account_name'] ?? '');
        $bankAccountNumber = trim($_POST['bank_account_number'] ?? '');
        $bankCode = trim($_POST['bank_code'] ?? '');
        $mpesaPhone = trim($_POST['mpesa_phone'] ?? '');
        $paypalEmail = trim($_POST['paypal_email'] ?? '');
        $otherPaymentDetails = trim($_POST['other_payment_details'] ?? '');
        
        if (empty($payoutMethod)) {
            flash_set('error', 'Please select a payment method.');
            redirect(base_url('/travel/profile'));
            return;
        }
        
        // Validate required fields based on payment method
        $validationErrors = [];
        
        switch ($payoutMethod) {
            case 'bank_transfer':
                if (empty($bankName)) $validationErrors[] = 'Bank name is required';
                if (empty($bankAccountName)) $validationErrors[] = 'Account name is required';
                if (empty($bankAccountNumber)) $validationErrors[] = 'Account number is required';
                if (empty($bankCode)) $validationErrors[] = 'Bank code is required';
                break;
            case 'mpesa':
                if (empty($mpesaPhone)) $validationErrors[] = 'M-Pesa phone number is required';
                break;
            case 'paypal':
                if (empty($paypalEmail)) $validationErrors[] = 'PayPal email is required';
                elseif (!filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) $validationErrors[] = 'Invalid PayPal email format';
                break;
            case 'other':
                if (empty($otherPaymentDetails)) $validationErrors[] = 'Payment details are required';
                break;
        }
        
        if (!empty($validationErrors)) {
            flash_set('error', 'Validation errors: ' . implode(', ', $validationErrors));
            redirect(base_url('/travel/profile'));
            return;
        }
        
        // Update payment information
        $sql = 'UPDATE travel_agencies SET 
            payout_method = ?, 
            bank_name = ?, 
            bank_account_name = ?, 
            bank_account_number = ?, 
            bank_code = ?, 
            mpesa_phone = ?, 
            paypal_email = ?, 
            other_payment_details = ?, 
            payment_info_updated_at = NOW(),
            payment_info_verified = 0
            WHERE id = ?';
        
        $stmt = db()->prepare($sql);
        $stmt->execute([
            $payoutMethod,
            $bankName,
            $bankAccountName,
            $bankAccountNumber,
            $bankCode,
            $mpesaPhone,
            $paypalEmail,
            $otherPaymentDetails,
            $_SESSION['travel_agency_id']
        ]);
        
        flash_set('success', 'Payment information saved successfully. Admin verification is required before withdrawals.');
        redirect(base_url('/travel/profile'));
    }

    public function startPhoneVerify(): void
    {
        require_travel_agency(); verify_csrf();
        $agencyId = (int)($_SESSION['travel_agency_id'] ?? 0);
        $agency = TravelAgency::findById($agencyId);
        if (!$agency) { redirect(base_url('/travel/profile')); }
        // Generate a 6-digit OTP and store with short expiry in travel_agency_tokens
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 10 * 60);
        // Remove any existing tokens for this agency, then insert a new one
        db()->prepare('DELETE FROM travel_agency_tokens WHERE agency_id = ?')->execute([$agencyId]);
        db()->prepare('INSERT INTO travel_agency_tokens (agency_id, token, expires_at) VALUES (?, ?, ?)')
            ->execute([$agencyId, $otp, $expires]);
        // Send SMS if SMS configured
        try {
            $sms = new \App\Services\Sms();
            if ($sms->isConfigured() && !empty($agency['phone'])) {
                $body = "Your verification code is: $otp";
                $sms->send($agency['phone'], $body);
            }
        } catch (\Throwable $e) {}
        flash_set('success', 'Verification code sent to your phone.');
        redirect(base_url('/travel/profile'));
    }

    public function confirmPhoneVerify(): void
    {
        require_travel_agency(); verify_csrf();
        $agencyId = (int)($_SESSION['travel_agency_id'] ?? 0);
        $code = trim($_POST['code'] ?? '');
        if ($code === '') { flash_set('error', 'Enter the verification code.'); redirect(base_url('/travel/profile')); }
        // Fetch latest token and validate against user input; tolerate spaces and case
        $stmt = db()->prepare('SELECT token, expires_at FROM travel_agency_tokens WHERE agency_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$agencyId]);
        $tok = $stmt->fetch();
        $input = preg_replace('/\s+/', '', $code);
        $dbToken = isset($tok['token']) ? preg_replace('/\s+/', '', (string)$tok['token']) : '';
        $notExpired = isset($tok['expires_at']) ? (strtotime($tok['expires_at']) >= time()) : false;
        if ($dbToken !== '' && hash_equals($dbToken, $input) && $notExpired) {
            db()->prepare('UPDATE travel_agencies SET phone_verified = 1 WHERE id = ?')->execute([$agencyId]);
            db()->prepare('DELETE FROM travel_agency_tokens WHERE agency_id = ?')->execute([$agencyId]);
            flash_set('success', 'Phone verified successfully.');
        } else {
            if ($dbToken !== '' && !$notExpired) {
                flash_set('error', 'Verification code expired. Please request a new code.');
            } else {
                flash_set('error', 'Invalid verification code.');
            }
        }
        redirect(base_url('/travel/profile'));
    }
    
    public function withdrawals(): void
    {
        require_travel_agency();
        $agencyId = (int)$_SESSION['travel_agency_id'];
        
        // Get withdrawal history
        $wd = db()->prepare('SELECT w.*, td.title AS destination_title FROM withdrawals w LEFT JOIN travel_destinations td ON td.id = w.destination_id WHERE w.travel_agency_id = ? ORDER BY created_at DESC');
        $wd->execute([$agencyId]);
        $withdrawals = $wd->fetchAll();
        
        // Get commission percentage
        $agency = db()->prepare('SELECT commission_percent FROM travel_agencies WHERE id = ?');
        $agency->execute([$agencyId]);
        $commission = (float)($agency->fetch()['commission_percent'] ?? 10.0);
        $commissionRate = max(0.0, min(100.0, $commission)) / 100.0;
        
        // Compute available revenue per destination: net = paid revenue - commission - featured_commission - withdrawals(approved/paid)
        $rows = db()->prepare('SELECT td.id, td.title, td.is_featured, td.featured_commission, COALESCE(SUM(CASE WHEN tp.payment_status="paid" THEN tb.total_amount ELSE 0 END),0) AS gross FROM travel_destinations td LEFT JOIN travel_bookings tb ON tb.destination_id = td.id LEFT JOIN travel_payments tp ON tp.booking_id = tb.id WHERE td.agency_id = ? GROUP BY td.id, td.title, td.is_featured, td.featured_commission ORDER BY td.id DESC');
        $rows->execute([$agencyId]);
        $destinations = $rows->fetchAll();
        
        $balances = [];
        foreach ($destinations as $dest) {
            $destId = (int)$dest['id'];
            $gross = (float)($dest['gross'] ?? 0);
            $commissionDue = $gross * $commissionRate;
            
            // Add featured commission if destination is featured
            $featuredCommissionDue = 0;
            if ($dest['is_featured'] && $dest['featured_commission'] > 0) {
                $featuredCommissionDue = $gross * ((float)$dest['featured_commission'] / 100.0);
            }
            
            $withdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE travel_agency_id='.(int)$agencyId.' AND (destination_id='.(int)$destId.' OR destination_id IS NULL) AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
            $balances[$destId] = max(0, $gross - $commissionDue - $featuredCommissionDue - $withdrawn);
        }
        
        $overallGross = array_sum(array_column($destinations, 'gross'));
        $overallCommission = $overallGross * $commissionRate;
        
        // Calculate overall featured commission
        $overallFeaturedCommission = 0;
        foreach ($destinations as $dest) {
            if ($dest['is_featured'] && $dest['featured_commission'] > 0) {
                $gross = (float)($dest['gross'] ?? 0);
                $overallFeaturedCommission += $gross * ((float)$dest['featured_commission'] / 100.0);
            }
        }
        
        $overallWithdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE travel_agency_id='.(int)$agencyId.' AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
        $overallAvailable = max(0, $overallGross - $overallCommission - $overallFeaturedCommission - $overallWithdrawn);
        
        travel_view('travel/withdrawals', compact('withdrawals', 'destinations', 'balances', 'overallAvailable', 'commission'));
    }
    
    public function requestWithdrawal(): void
    {
        require_travel_agency();
        $amount = (float)($_POST['amount'] ?? 0);
        $destinationId = isset($_POST['destination_id']) && $_POST['destination_id'] !== '' ? (int)$_POST['destination_id'] : null;
        
        if ($amount <= 0) { 
            redirect(base_url('/travel/withdrawals')); 
        }
        
        $agencyId = (int)$_SESSION['travel_agency_id'];
        
        // Check if payment information is complete and verified
        $paymentInfo = db()->prepare('SELECT payout_method, payment_info_verified FROM travel_agencies WHERE id = ?');
        $paymentInfo->execute([$agencyId]);
        $payment = $paymentInfo->fetch();
        
        if (empty($payment['payout_method'])) {
            flash_set('error', 'Please set up your payment information in your profile before requesting withdrawals.');
            redirect(base_url('/travel/profile'));
            return;
        }
        
        if (!($payment['payment_info_verified'] ?? 0)) {
            flash_set('error', 'Your payment information is pending admin verification. Please wait for verification before requesting withdrawals.');
            redirect(base_url('/travel/withdrawals'));
            return;
        }
        
        // Get commission percentage
        $agency = db()->prepare('SELECT commission_percent FROM travel_agencies WHERE id = ?');
        $agency->execute([$agencyId]);
        $commissionRate = max(0.0, min(100.0, (float)($agency->fetch()['commission_percent'] ?? 10.0))) / 100.0;
        
        // Calculate available balance
        if ($destinationId) {
            // Per destination withdrawal
            $revStmt = db()->prepare('SELECT td.is_featured, td.featured_commission, COALESCE(SUM(CASE WHEN tp.payment_status="paid" THEN tb.total_amount ELSE 0 END),0) AS gross FROM travel_bookings tb JOIN travel_destinations td ON td.id = tb.destination_id JOIN travel_payments tp ON tp.booking_id = tb.id WHERE td.agency_id = ? AND td.id = ? GROUP BY td.is_featured, td.featured_commission');
            $revStmt->execute([$agencyId, $destinationId]);
            $destData = $revStmt->fetch();
            $gross = (float)($destData['gross'] ?? 0);
            $commissionDue = $gross * $commissionRate;
            
            // Add featured commission if destination is featured
            $featuredCommissionDue = 0;
            if ($destData['is_featured'] && $destData['featured_commission'] > 0) {
                $featuredCommissionDue = $gross * ((float)$destData['featured_commission'] / 100.0);
            }
            
            $wq = db()->prepare('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE travel_agency_id = ? AND (destination_id = ? OR destination_id IS NULL) AND status IN ("approved","paid")');
            $wq->execute([$agencyId, $destinationId]);
            $withdrawn = (float)($wq->fetch()['wsum'] ?? 0);
            $available = max(0, $gross - $commissionDue - $featuredCommissionDue - $withdrawn);
        } else {
            // Overall withdrawal
            $gross = (float)(db()->query('SELECT COALESCE(SUM(CASE WHEN tp.payment_status="paid" THEN tb.total_amount ELSE 0 END),0) AS gross FROM travel_bookings tb JOIN travel_destinations td ON td.id = tb.destination_id JOIN travel_payments tp ON tp.booking_id = tb.id WHERE td.agency_id='.(int)$agencyId)->fetch()['gross'] ?? 0);
            $commissionDue = $gross * $commissionRate;
            
            // Calculate overall featured commission for all destinations
            $featuredCommissionDue = 0;
            $featuredStmt = db()->prepare('SELECT td.featured_commission, COALESCE(SUM(CASE WHEN tp.payment_status="paid" THEN tb.total_amount ELSE 0 END),0) AS gross FROM travel_bookings tb JOIN travel_destinations td ON td.id = tb.destination_id JOIN travel_payments tp ON tp.booking_id = tb.id WHERE td.agency_id = ? AND td.is_featured = 1 AND td.featured_commission > 0 GROUP BY td.featured_commission');
            $featuredStmt->execute([$agencyId]);
            $featuredDestinations = $featuredStmt->fetchAll();
            foreach ($featuredDestinations as $fd) {
                $featuredCommissionDue += (float)$fd['gross'] * ((float)$fd['featured_commission'] / 100.0);
            }
            
            $withdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE travel_agency_id='.(int)$agencyId.' AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
            $available = max(0, $gross - $commissionDue - $featuredCommissionDue - $withdrawn);
        }
        
        if ($amount > $available + 0.01) { 
            flash_set('error', 'Amount exceeds available balance'); 
            redirect(base_url('/travel/withdrawals')); 
        }
        
        // Insert withdrawal request
        $stmt = db()->prepare('INSERT INTO withdrawals (travel_agency_id, destination_id, amount, currency, status, notes) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$agencyId, $destinationId, $amount, 'KES', 'requested', trim($_POST['notes'] ?? '')]);
        
        flash_set('success', 'Withdrawal request submitted.');
        
        // Send SMS notification
        try {
            $agencyRow = db()->prepare('SELECT phone FROM travel_agencies WHERE id = ?');
            $agencyRow->execute([$agencyId]);
            $phone = $agencyRow->fetch()['phone'] ?? '';
            $sms = new \App\Services\Sms();
            if ($phone && $sms->isConfigured()) {
                $body = \App\Services\SmsTemplates::render('withdrawal_request', ['amount' => number_format($amount, 2)]);
                if ($body === '') { 
                    $body = 'Withdrawal request received: KES ' . number_format($amount, 2); 
                }
                $sms->send($phone, $body);
            }
        } catch (\Throwable $e) {}
        
        redirect(base_url('/travel/withdrawals'));
    }
    
    public function scanner(): void
    {
        require_travel_agency();
        $agencyId = $_SESSION['travel_agency_id'];
        $stmt = db()->prepare('SELECT * FROM travel_scanner_devices WHERE travel_agency_id = ?');
        $stmt->execute([$agencyId]);
        $devices = $stmt->fetchAll();
        travel_view('travel/scanner/index', compact('devices'));
    }
    
    public function createScanner(): void
    {
        require_travel_agency();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $agencyId = $_SESSION['travel_agency_id'];
            $deviceName = $_POST['device_name'] ?? '';
            
            if (empty($deviceName)) {
                flash_set('error', 'Device name cannot be empty.');
                redirect(base_url('/travel/scanner/create'));
            }

            $deviceCode = 'T' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            try {
                $stmt = db()->prepare('INSERT INTO travel_scanner_devices (travel_agency_id, device_name, device_code) VALUES (?, ?, ?)');
                $stmt->execute([$agencyId, $deviceName, $deviceCode]);
                flash_set('success', 'Scanner device created successfully! Code: ' . $deviceCode);
                redirect(base_url('/travel/scanner'));
            } catch (Exception $e) {
                flash_set('error', 'Failed to create scanner device: ' . $e->getMessage());
                redirect(base_url('/travel/scanner/create'));
            }
        }
        travel_view('travel/scanner/create');
    }
    
    
    public function toggleScanner(): void
    {
        require_travel_agency();
        verify_csrf();
        $deviceId = $_POST['device_id'] ?? null;
        $agencyId = $_SESSION['travel_agency_id'];
        $isActive = $_POST['is_active'] ?? null;

        if (!$deviceId || !in_array($isActive, ['0', '1'])) {
            flash_set('error', 'Invalid request.');
            redirect(base_url('/travel/scanner'));
        }

        try {
            $stmt = db()->prepare('UPDATE travel_scanner_devices SET is_active = ? WHERE id = ? AND travel_agency_id = ?');
            $stmt->execute([$isActive, $deviceId, $agencyId]);
            flash_set('success', 'Scanner device status updated.');
        } catch (Exception $e) {
            flash_set('error', 'Failed to update device status: ' . $e->getMessage());
        }
        redirect(base_url('/travel/scanner'));
    }
    
    public function scannerScan(): void
    {
        require_travel_agency();
        $deviceId = $_GET['device_id'] ?? null;
        $agencyId = $_SESSION['travel_agency_id'];
        
        if (!$deviceId) {
            flash_set('error', 'Device ID is required.');
            redirect(base_url('/travel/scanner'));
        }
        
        // Verify device belongs to this agency
        $stmt = db()->prepare('SELECT * FROM travel_scanner_devices WHERE id = ? AND travel_agency_id = ? AND is_active = 1');
        $stmt->execute([$deviceId, $agencyId]);
        $device = $stmt->fetch();
        
        if (!$device) {
            flash_set('error', 'Invalid or inactive scanner device.');
            redirect(base_url('/travel/scanner'));
        }
        
        // Store device info in session and redirect to universal scanner
        $_SESSION['travel_scanner_device_id'] = $device['id'];
        $_SESSION['travel_scanner_device_code'] = $device['device_code'];
        $_SESSION['travel_scanner_agency_id'] = $agencyId;
        
        // Redirect to universal scanner with device code
        redirect(base_url('/scanner/login?device_code=' . urlencode($device['device_code'])));
    }
    
    public function getAvailableScanners(): void
    {
        header('Content-Type: application/json');
        
        // Check if user is logged in as travel agency
        if (!isset($_SESSION['travel_agency_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in as travel agency']);
            return;
        }
        
        $destinationId = $_GET['destination_id'] ?? null;
        $agencyId = $_SESSION['travel_agency_id'];
        
        if (!$destinationId) {
            echo json_encode(['success' => false, 'message' => 'Destination ID is required']);
            return;
        }
        
        try {
            // Get all active scanner devices for this agency
            $stmt = db()->prepare('SELECT id, device_name, device_code FROM travel_scanner_devices WHERE travel_agency_id = ? AND is_active = 1');
            $stmt->execute([$agencyId]);
            $scanners = $stmt->fetchAll();
            
            // Get currently assigned scanners for this destination
            $stmt = db()->prepare('
                SELECT tsd.id, tsd.device_name, tsd.device_code 
                FROM travel_scanner_devices tsd
                JOIN travel_scanner_assignments tsa ON tsd.id = tsa.scanner_device_id
                WHERE tsa.destination_id = ? AND tsd.travel_agency_id = ?
            ');
            $stmt->execute([$destinationId, $agencyId]);
            $assignedScanners = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'scanners' => $scanners,
                'assigned_scanners' => $assignedScanners,
                'debug' => [
                    'agency_id' => $agencyId,
                    'destination_id' => $destinationId,
                    'scanner_count' => count($scanners),
                    'assigned_count' => count($assignedScanners)
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to load scanners: ' . $e->getMessage(),
                'debug' => [
                    'agency_id' => $agencyId,
                    'destination_id' => $destinationId,
                    'error' => $e->getMessage()
                ]
            ]);
        }
    }
    
    public function assignScanner(): void
    {
        header('Content-Type: application/json');
        
        // Check if user is logged in as travel agency
        if (!isset($_SESSION['travel_agency_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in as travel agency']);
            return;
        }
        
        verify_csrf();
        
        $destinationId = $_POST['destination_id'] ?? null;
        $scannerId = $_POST['scanner_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $agencyId = $_SESSION['travel_agency_id'];
        
        if (!$destinationId || !$scannerId || !in_array($action, ['assign', 'unassign'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request parameters']);
            return;
        }
        
        try {
            // Verify the scanner belongs to this agency
            $stmt = db()->prepare('SELECT id FROM travel_scanner_devices WHERE id = ? AND travel_agency_id = ? AND is_active = 1');
            $stmt->execute([$scannerId, $agencyId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Invalid scanner device']);
                return;
            }
            
            // Verify the destination belongs to this agency
            $stmt = db()->prepare('SELECT id FROM travel_destinations WHERE id = ? AND agency_id = ?');
            $stmt->execute([$destinationId, $agencyId]);
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Invalid destination']);
                return;
            }
            
            if ($action === 'assign') {
                // Check if already assigned
                $stmt = db()->prepare('SELECT id FROM travel_scanner_assignments WHERE scanner_device_id = ? AND destination_id = ?');
                $stmt->execute([$scannerId, $destinationId]);
                if ($stmt->fetch()) {
                    echo json_encode(['success' => false, 'message' => 'Scanner is already assigned to this destination']);
                    return;
                }
                
                // Assign scanner
                $stmt = db()->prepare('INSERT INTO travel_scanner_assignments (scanner_device_id, destination_id, assigned_at) VALUES (?, ?, NOW())');
                $stmt->execute([$scannerId, $destinationId]);
                
                echo json_encode(['success' => true, 'message' => 'Scanner assigned successfully']);
                
            } else { // unassign
                // Remove assignment
                $stmt = db()->prepare('DELETE FROM travel_scanner_assignments WHERE scanner_device_id = ? AND destination_id = ?');
                $stmt->execute([$scannerId, $destinationId]);
                
                echo json_encode(['success' => true, 'message' => 'Scanner unassigned successfully']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to update assignment: ' . $e->getMessage()]);
        }
    }

    public function deleteScanner(): void
    {
        header('Content-Type: application/json');
        
        // Check if user is logged in as travel agency
        if (!isset($_SESSION['travel_agency_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in as travel agency']);
            return;
        }
        
        verify_csrf();
        
        $scannerId = $_POST['scanner_id'] ?? null;
        $agencyId = $_SESSION['travel_agency_id'];
        
        if (!$scannerId) {
            echo json_encode(['success' => false, 'message' => 'Scanner ID is required']);
            return;
        }
        
        try {
            // Verify the scanner belongs to this agency
            $stmt = db()->prepare('SELECT id, device_name FROM travel_scanner_devices WHERE id = ? AND travel_agency_id = ?');
            $stmt->execute([$scannerId, $agencyId]);
            $scanner = $stmt->fetch();
            
            if (!$scanner) {
                echo json_encode(['success' => false, 'message' => 'Scanner device not found or access denied']);
                return;
            }
            
            // Check if scanner has any scans recorded
            $stmt = db()->prepare('SELECT COUNT(*) as scan_count FROM travel_booking_scans WHERE scanner_device_id = ?');
            $stmt->execute([$scannerId]);
            $scanData = $stmt->fetch();
            $hasScans = $scanData['scan_count'] > 0;
            
            if ($hasScans) {
                // Soft delete - deactivate instead of hard delete to preserve scan history
                $stmt = db()->prepare('UPDATE travel_scanner_devices SET is_active = 0, updated_at = NOW() WHERE id = ? AND travel_agency_id = ?');
                $stmt->execute([$scannerId, $agencyId]);
                
                // Remove all assignments
                $stmt = db()->prepare('DELETE FROM travel_scanner_assignments WHERE scanner_device_id = ?');
                $stmt->execute([$scannerId]);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Scanner deactivated successfully (scan history preserved)',
                    'action' => 'deactivated'
                ]);
            } else {
                // Hard delete - no scan history to preserve
                // Remove assignments first
                $stmt = db()->prepare('DELETE FROM travel_scanner_assignments WHERE scanner_device_id = ?');
                $stmt->execute([$scannerId]);
                
                // Delete the scanner device
                $stmt = db()->prepare('DELETE FROM travel_scanner_devices WHERE id = ? AND travel_agency_id = ?');
                $stmt->execute([$scannerId, $agencyId]);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Scanner deleted successfully',
                    'action' => 'deleted'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to delete scanner: ' . $e->getMessage()]);
        }
    }
    
    public function scannerVerify(): void
    {
        require_travel_agency();
        verify_csrf();
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        
        $bookingReference = trim($_POST['booking_reference'] ?? '');
        $agencyId = $_SESSION['travel_agency_id'];
        
        if (empty($bookingReference)) {
            echo json_encode(['success' => false, 'message' => 'Booking reference is required']);
            return;
        }
        
        try {
            // Find the booking
            $stmt = db()->prepare('
                SELECT tb.*, td.title as destination_title, ta.company_name, ta.id as agency_id,
                       u.first_name, u.last_name
                FROM travel_bookings tb
                JOIN travel_destinations td ON tb.destination_id = td.id
                JOIN travel_agencies ta ON td.agency_id = ta.id
                JOIN users u ON tb.user_id = u.id
                WHERE tb.booking_reference = ? AND ta.id = ? AND tb.status = "confirmed"
            ');
            $stmt->execute([$bookingReference, $agencyId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                echo json_encode(['success' => false, 'message' => 'Booking not found or not confirmed']);
                return;
            }
            
            // Check if already scanned
            $stmt = db()->prepare('SELECT * FROM travel_booking_scans WHERE booking_id = ?');
            $stmt->execute([$booking['id']]);
            $existingScan = $stmt->fetch();
            
            if ($existingScan) {
                echo json_encode(['success' => false, 'message' => 'Booking already scanned']);
                return;
            }
            
            // Return success with booking details
            echo json_encode([
                'success' => true,
                'message' => 'Booking verified successfully',
                'booking' => [
                    'booking_reference' => $booking['booking_reference'],
                    'destination_title' => $booking['destination_title'],
                    'travel_date' => $booking['travel_date'],
                    'participants_count' => $booking['participants_count'],
                    'customer_name' => $booking['first_name'] . ' ' . $booking['last_name'],
                    'status' => $booking['status']
                ]
            ]);
            
        } catch (Exception $e) {
            error_log('Travel scanner verify error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error occurred']);
        }
    }
    
    public function editScanner(): void
    {
        require_travel_agency();
        $deviceId = $_GET['id'] ?? null;
        $agencyId = $_SESSION['travel_agency_id'];
        
        if (!$deviceId) {
            flash_set('error', 'Device ID is required.');
            redirect(base_url('/travel/scanner'));
        }
        
        // Get device details
        $stmt = db()->prepare('SELECT * FROM travel_scanner_devices WHERE id = ? AND travel_agency_id = ?');
        $stmt->execute([$deviceId, $agencyId]);
        $device = $stmt->fetch();
        
        if (!$device) {
            flash_set('error', 'Device not found or access denied.');
            redirect(base_url('/travel/scanner'));
        }
        
        travel_view('travel/scanner/edit', compact('device'));
    }
    
    public function updateScanner(): void
    {
        require_travel_agency();
        verify_csrf();
        
        $deviceId = $_POST['device_id'] ?? null;
        $agencyId = $_SESSION['travel_agency_id'];
        $deviceName = trim($_POST['device_name'] ?? '');
        $isActive = (int)($_POST['is_active'] ?? 0);
        
        if (!$deviceId || empty($deviceName)) {
            flash_set('error', 'Device ID and name are required.');
            redirect(base_url('/travel/scanner'));
        }
        
        try {
            // Update device
            $stmt = db()->prepare('UPDATE travel_scanner_devices SET device_name = ?, is_active = ?, updated_at = NOW() WHERE id = ? AND travel_agency_id = ?');
            $stmt->execute([$deviceName, $isActive, $deviceId, $agencyId]);
            
            flash_set('success', 'Scanner device updated successfully.');
            redirect(base_url('/travel/scanner'));
        } catch (Exception $e) {
            flash_set('error', 'Failed to update scanner device: ' . $e->getMessage());
            redirect(base_url('/travel/scanner/edit?id=' . $deviceId));
        }
    }

    public function marketing(): void
    {
        require_travel_agency();
        travel_view('travel/marketing');
    }

    public function campaignReports(): void
    {
        require_travel_agency();
        travel_view('travel/campaign_reports');
    }

    public function campaignRequest(): void
    {
        require_travel_agency();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields
                $required = ['campaign_name', 'destination_id', 'target_audience', 'message_content', 'tier', 'payment_method'];
                foreach ($required as $field) {
                    if (empty($input[$field])) {
                        echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                        return;
                    }
                }
                
                // If dates not provided, auto-fill from destination departure date
                if (empty($input['start_date']) || empty($input['end_date'])) {
                    $dst = db()->prepare('SELECT departure_date FROM travel_destinations WHERE id = ? AND agency_id = ?');
                    $dst->execute([(int)$input['destination_id'], (int)$_SESSION['travel_agency_id']]);
                    $row = $dst->fetch();
                    $today = date('Y-m-d');
                    $depDate = $row && !empty($row['departure_date']) ? date('Y-m-d', strtotime($row['departure_date'])) : $today;
                    $input['start_date'] = $input['start_date'] ?? $today;
                    if (strtotime($input['start_date']) > strtotime($depDate)) { $depDate = $input['start_date']; }
                    $input['end_date'] = $input['end_date'] ?? $depDate;
                }

                // Get pricing for the tier
                $stmt = db()->prepare('SELECT price_per_sms, max_messages FROM marketing_pricing_settings WHERE tier_name = ? AND account_type = "travel_agency"');
                $stmt->execute([$input['package_name'] ?? $input['tier']]);
                $pricing = $stmt->fetch();
                
                if (!$pricing) {
                    echo json_encode(['success' => false, 'message' => 'Invalid campaign tier']);
                    return;
                }
                
                // Calculate package cost (maximum messages × price per SMS)
                $maxMessages = $pricing['max_messages'] ?: 10000; // Default to 10000 if unlimited
                $totalCost = $maxMessages * $pricing['price_per_sms'];
                
                // Insert campaign request with payment info
                $stmt = db()->prepare('
                    INSERT INTO marketing_campaign_requests 
                    (account_type, account_id, campaign_name, destination_id, target_audience, message_content, 
                     start_date, end_date, tier, notes, payment_method, calculated_cost, estimated_messages, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "pending", NOW())
                ');
                
                $stmt->execute([
                    'travel_agency',
                    $_SESSION['travel_agency_id'],
                    $input['campaign_name'],
                    $input['destination_id'],
                    $input['target_audience'],
                    $input['message_content'],
                    $input['start_date'],
                    $input['end_date'],
                    $input['package_name'] ?? $input['tier'],
                    $input['notes'] ?? null,
                    $input['payment_method'],
                    $totalCost,
                    $maxMessages
                ]);
                
                $requestId = db()->lastInsertId();
                
                // Notify agency that request was created (before payment)
                try {
                    $sms = new \App\Services\Sms();
                    if ($sms->isConfigured()) {
                        $u = db()->prepare('SELECT phone FROM travel_agencies WHERE id = ?');
                        $u->execute([$_SESSION['travel_agency_id']]);
                        $phone = $u->fetch()['phone'] ?? '';
                        if ($phone !== '') {
                            $sms->send($phone, 'Your destination campaign request #' . $requestId . ' was received. Complete payment to start processing.');
                        }
                    }
                } catch (\Throwable $e) {}

                // Create payment URL based on payment method
                $paymentUrl = $this->createPaymentUrl($input['payment_method'], $totalCost, $requestId, 'campaign_request');
                if (!empty($input['payer_phone'])) {
                    $paymentUrl .= '&msisdn=' . urlencode($input['payer_phone']);
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Campaign request submitted successfully',
                    'payment_required' => true,
                    'payment_url' => $paymentUrl,
                    'amount' => $totalCost,
                    'request_id' => $requestId
                ]);
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error submitting campaign request: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
    }

    private function createPaymentUrl(string $paymentMethod, float $amount, int $requestId, string $type): string
    {
        $baseUrl = base_url('/pay');
        
        switch ($paymentMethod) {
            case 'mpesa':
                return $baseUrl . '/mpesa?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
            case 'flutterwave':
                return $baseUrl . '/flutterwave?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
            case 'paypal':
                return $baseUrl . '/paypal?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
            default:
                return $baseUrl . '/mpesa?amount=' . $amount . '&type=' . $type . '&reference=' . $requestId;
        }
    }

    public function destinationsApi(): void
    {
        require_travel_agency();
        
        try {
            $stmt = db()->prepare('
                SELECT id, title, destination, departure_date, is_published, created_at 
                FROM travel_destinations 
                WHERE agency_id = ? AND is_published = 1
                ORDER BY departure_date DESC, created_at DESC
            ');
            $stmt->execute([$_SESSION['travel_agency_id']]);
            $destinations = $stmt->fetchAll();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'destinations' => $destinations
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching destinations: ' . $e->getMessage()
            ]);
        }
    }

    // Create reach-based marketing order for travel agency
    public function marketingOrder(): void
    {
        require_travel_agency();
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $destinationId = (int)($input['destination_id'] ?? 0);
            $reach = (int)($input['reach'] ?? 0);
            $mixEmail = (int)($input['mix_email_percent'] ?? 0);
            $unitSms = (float)($input['unit_price_sms'] ?? 0);
            $unitEmail = (float)($input['unit_price_email'] ?? 0);
            $paymentMethod = $input['payment_method'] ?? 'mpesa';
            if ($destinationId<=0 || $reach<=0) { echo json_encode(['success'=>false,'message'=>'Invalid request']); return; }
            $smsContacts = max(0, $reach - (int)round($reach*$mixEmail/100));
            $emailContacts = (int)round($reach*$mixEmail/100);
            $total = ($smsContacts*$unitSms) + ($emailContacts*$unitEmail);
            $stmt = db()->prepare('INSERT INTO marketing_orders (account_type,account_id,item_type,item_id,reach,unit_price_sms,unit_price_email,mix_email_percent,total_cost,payment_method) VALUES ("travel_agency", ?, "destination", ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$_SESSION['travel_agency_id'],$destinationId,$reach,$unitSms,$unitEmail,$mixEmail,$total,$paymentMethod]);
            $orderId = (int)db()->lastInsertId();
            $payUrl = base_url('/pay/'.$paymentMethod.'?type=marketing_order&order_id='.$orderId.'&amount='.$total);
            echo json_encode(['success'=>true,'payment_url'=>$payUrl,'order_id'=>$orderId]);
        } catch (\Exception $e) {
            echo json_encode(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
        }
    }
}

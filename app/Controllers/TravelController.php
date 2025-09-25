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
        
        view('travel/dashboard', compact('agency', 'destinationsCount', 'featuredCount', 'bookings', 'recentBookings'));
    }

    public function destinations(): void
    {
        require_travel_agency();
        
        $agencyId = $_SESSION['travel_agency_id'];
        $destinations = TravelAgency::getDestinationsByAgency($agencyId);
        
        view('travel/destinations/index', compact('destinations'));
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

        view('travel/destinations/create');
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
        view('travel/destinations/create', ['dest' => $dest]);
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
        
        view('travel/bookings', compact('bookings'));
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

        view('travel/profile', compact('agency'));
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
}

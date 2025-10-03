<?php
namespace App\Controllers;

class PagesController
{
    public function show(): void
    {
        $slug = trim($_GET['slug'] ?? '');
        if ($slug === '') { http_response_code(404); echo 'Page not found'; return; }
        $stmt = db()->prepare('SELECT * FROM pages WHERE slug = ? AND is_published = 1 LIMIT 1');
        $stmt->execute([$slug]);
        $page = $stmt->fetch();
        if (!$page) { http_response_code(404); echo 'Page not found'; return; }
        view('pages/show', compact('page'));
    }

    public function travelIndex(): void
    {
        $q = trim($_GET['q'] ?? '');
        $country = trim($_GET['country'] ?? '');
        $city = trim($_GET['city'] ?? '');
        $onlyFeatured = (int)($_GET['featured'] ?? 0);
        $sql = 'SELECT td.*, ta.company_name, ta.logo_path, ta.id as agency_id FROM travel_destinations td JOIN travel_agencies ta ON ta.id = td.agency_id WHERE td.is_published = 1 AND ta.is_approved = 1';
        $params = [];
        if ($q !== '') { $sql .= ' AND (td.title LIKE ? OR td.destination LIKE ? OR ta.company_name LIKE ?)'; $like = "%$q%"; array_push($params,$like,$like,$like); }
        if ($country !== '') { $sql .= ' AND ta.country = ?'; $params[] = $country; }
        if ($city !== '') { $sql .= ' AND ta.city = ?'; $params[] = $city; }
        if ($onlyFeatured === 1) { $sql .= ' AND td.is_featured = 1'; }
        $sql .= ' ORDER BY td.is_featured DESC, td.created_at DESC LIMIT 30';
        try { $stmt = db()->prepare($sql); $stmt->execute($params); $destinations = $stmt->fetchAll(); } catch (\PDOException $e) { $destinations = []; }
        $featured = \App\Models\TravelAgency::getFeaturedDestinations(6);
        $latest = \App\Models\TravelAgency::getAllPublishedDestinations(12, 0);
        
        // Fetch travel banners
        try {
            $banners = db()->query('SELECT * FROM travel_banners WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC')->fetchAll();
        } catch (\PDOException $e) { $banners = []; }
        
        view('travel/public_index', compact('destinations','featured','latest','q','country','city','banners'));
    }

    public function travelDestinationShow(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { http_response_code(404); echo 'Destination not found'; return; }
        
        $stmt = db()->prepare('
            SELECT td.*, ta.company_name, ta.logo_path, ta.contact_person, ta.email, ta.phone, ta.website, ta.description as agency_description, ta.id as agency_id
            FROM travel_destinations td 
            JOIN travel_agencies ta ON ta.id = td.agency_id 
            WHERE td.id = ? AND td.is_published = 1 AND ta.is_approved = 1 AND ta.is_active = 1
        ');
        $stmt->execute([$id]);
        $destination = $stmt->fetch();
        
        if (!$destination) { http_response_code(404); echo 'Destination not found'; return; }
        
        // Decode JSON fields
        $destination['includes'] = json_decode($destination['includes'] ?? '[]', true);
        $destination['excludes'] = json_decode($destination['excludes'] ?? '[]', true);
        $destination['requirements'] = json_decode($destination['requirements'] ?? '[]', true);
        $destination['itinerary'] = json_decode($destination['itinerary'] ?? '[]', true);
        $destination['gallery_paths'] = json_decode($destination['gallery_paths'] ?? '[]', true);
        
        // Fetch related destinations (same agency, different destination)
        $relatedStmt = db()->prepare('
            SELECT td.id, td.title, td.image_path, td.price, td.currency, td.duration_days, ta.company_name
            FROM travel_destinations td 
            JOIN travel_agencies ta ON ta.id = td.agency_id 
            WHERE td.id != ? AND td.agency_id = ? AND td.is_published = 1 AND ta.is_approved = 1 AND ta.is_active = 1
            ORDER BY td.created_at DESC 
            LIMIT 4
        ');
        $relatedStmt->execute([$id, $destination['agency_id']]);
        $relatedDestinations = $relatedStmt->fetchAll();
        
        // If not enough related destinations from same agency, get popular ones (excluding already selected ones)
        if (count($relatedDestinations) < 4) {
            $limit = 4 - count($relatedDestinations);
            
            // Get IDs of already selected destinations to exclude them
            $excludeIds = [$id]; // Always exclude current destination
            foreach ($relatedDestinations as $related) {
                $excludeIds[] = $related['id'];
            }
            $excludeIdsStr = implode(',', array_map('intval', $excludeIds));
            
            $popularStmt = db()->prepare('
                SELECT td.id, td.title, td.image_path, td.price, td.currency, td.duration_days, ta.company_name
                FROM travel_destinations td 
                JOIN travel_agencies ta ON ta.id = td.agency_id 
                WHERE td.id NOT IN (' . $excludeIdsStr . ') AND td.is_published = 1 AND ta.is_approved = 1 AND ta.is_active = 1
                ORDER BY td.created_at DESC 
                LIMIT ' . (int)$limit . '
            ');
            $popularStmt->execute();
            $popularDestinations = $popularStmt->fetchAll();
            $relatedDestinations = array_merge($relatedDestinations, $popularDestinations);
        }
        
        // Remove any duplicates by ID (safety check)
        $uniqueDestinations = [];
        $seenIds = [];
        foreach ($relatedDestinations as $dest) {
            if (!in_array($dest['id'], $seenIds)) {
                $uniqueDestinations[] = $dest;
                $seenIds[] = $dest['id'];
            }
        }
        $relatedDestinations = $uniqueDestinations;
        
        view('travel/destination_show', compact('destination', 'relatedDestinations'));
    }

    public function search(): void
    {
        $query = trim($_GET['q'] ?? '');
        $events = [];
        $destinations = [];
        
        if (!empty($query)) {
            // Search events
            $eventStmt = db()->prepare('
                SELECT e.*, o.full_name as organizer_name
                FROM events e 
                JOIN organizers o ON e.organizer_id = o.id
                WHERE (e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ? OR e.category LIKE ?)
                AND e.is_published = 1 AND e.event_date >= CURDATE()
                ORDER BY e.event_date ASC 
                LIMIT 10
            ');
            $searchTerm = "%$query%";
            $eventStmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $events = $eventStmt->fetchAll();
            
            // Search travel destinations
            $destStmt = db()->prepare('
                SELECT td.*, ta.company_name, ta.logo_path
                FROM travel_destinations td 
                JOIN travel_agencies ta ON ta.id = td.agency_id
                WHERE (td.title LIKE ? OR td.description LIKE ? OR td.destination LIKE ? OR ta.company_name LIKE ?)
                AND td.is_published = 1 AND ta.is_approved = 1 AND ta.is_active = 1
                ORDER BY td.created_at DESC 
                LIMIT 10
            ');
            $destStmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $destinations = $destStmt->fetchAll();
        }
        
        view('search/results', compact('query', 'events', 'destinations'));
    }

    public function travelAgencyShow(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { http_response_code(404); echo 'Agency not found'; return; }

        // Fetch agency
        $stmt = db()->prepare('SELECT * FROM travel_agencies WHERE id = ? AND is_active = 1 AND is_approved = 1 LIMIT 1');
        $stmt->execute([$id]);
        $agency = $stmt->fetch();
        if (!$agency) { http_response_code(404); echo 'Agency not found'; return; }

        // Followers count and following state
        $followersCount = 0; $isFollowing = false;
        try {
            $stmt = db()->prepare('SELECT COUNT(*) AS c FROM travel_agency_followers WHERE agency_id = ?');
            $stmt->execute([$id]);
            $followersCount = (int)($stmt->fetch()['c'] ?? 0);
            if (isset($_SESSION['user_id'])) {
                $stmt = db()->prepare('SELECT 1 FROM travel_agency_followers WHERE agency_id = ? AND user_id = ? LIMIT 1');
                $stmt->execute([$id, (int)$_SESSION['user_id']]);
                $isFollowing = (bool)$stmt->fetch();
            }
        } catch (\PDOException $e) {
            $followersCount = 0; $isFollowing = false;
        }

        // Ratings summary
        $avgRating = 0; $ratingsCount = 0;
        try {
            $stmt = db()->prepare('SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS ratings_count FROM travel_agency_ratings WHERE agency_id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch() ?: [];
            $avgRating = (float)($row['avg_rating'] ?? 0);
            $ratingsCount = (int)($row['ratings_count'] ?? 0);
        } catch (\PDOException $e) {}

        // Past destinations (already departed)
        $pastDestinations = [];
        try {
            $stmt = db()->prepare('SELECT * FROM travel_destinations WHERE agency_id = ? AND departure_date < CURDATE() ORDER BY departure_date DESC LIMIT 12');
            $stmt->execute([$id]);
            $pastDestinations = $stmt->fetchAll();
        } catch (\PDOException $e) {}

        view('travel/agency_show', compact('agency','followersCount','isFollowing','avgRating','ratingsCount','pastDestinations'));
    }

    public function travelAgencyFollow(): void
    {
        if (!isset($_SESSION['user_id'])) { flash_set('error','Please login to follow'); redirect('/login'); return; }
        $agencyId = (int)($_POST['agency_id'] ?? 0);
        if ($agencyId <= 0) { redirect('/travel'); return; }
        try {
            $stmt = db()->prepare('INSERT IGNORE INTO travel_agency_followers (agency_id, user_id, created_at) VALUES (?, ?, NOW())');
            $stmt->execute([$agencyId, (int)$_SESSION['user_id']]);
            flash_set('success','You are now following this travel agency.');
        } catch (\PDOException $e) { flash_set('error','Could not follow at this time.'); }
        redirect('/travel/agency?id=' . $agencyId);
    }

    public function travelAgencyUnfollow(): void
    {
        if (!isset($_SESSION['user_id'])) { flash_set('error','Please login to unfollow'); redirect('/login'); return; }
        $agencyId = (int)($_POST['agency_id'] ?? 0);
        if ($agencyId <= 0) { redirect('/travel'); return; }
        try {
            $stmt = db()->prepare('DELETE FROM travel_agency_followers WHERE agency_id = ? AND user_id = ?');
            $stmt->execute([$agencyId, (int)$_SESSION['user_id']]);
            flash_set('success','Unfollowed successfully.');
        } catch (\PDOException $e) { flash_set('error','Could not unfollow at this time.'); }
        redirect('/travel/agency?id=' . $agencyId);
    }

    public function travelAgencyRate(): void
    {
        if (!isset($_SESSION['user_id'])) { flash_set('error','Please login to rate'); redirect('/login'); return; }
        $agencyId = (int)($_POST['agency_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        if ($agencyId <= 0 || $rating < 1 || $rating > 5) { redirect('/travel'); return; }
        try {
            $stmt = db()->prepare('REPLACE INTO travel_agency_ratings (agency_id, user_id, rating, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$agencyId, (int)$_SESSION['user_id'], $rating]);
            flash_set('success','Thanks for your rating!');
        } catch (\PDOException $e) { flash_set('error','Could not save rating.'); }
        redirect('/travel/agency?id=' . $agencyId);
    }

    public function hotelsComingSoon(): void
    {
        // Get platform statistics for credibility
        $stats = $this->getPlatformStats();
        
        // Get site settings for branding
        $siteTitle = \App\Models\Setting::get('site.title') ?? 'Ticko';
        $siteLogo = \App\Models\Setting::get('site.logo') ?? '/uploads/site/logo.png';
        
        standalone_view('hotels/coming_soon', compact('stats', 'siteTitle', 'siteLogo'));
    }

    public function hotelApplication(): void
    {
        // Check if this is an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        // For AJAX requests, start output buffering to prevent any unwanted output
        if ($isAjax) {
            ob_start();
            // Debug: Log what we're about to send
            error_log('AJAX Hotel Application - Starting request processing');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) {
                ob_clean(); // Clear any output buffer
                header('Content-Type: application/json');
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            redirect('/hotels');
            return;
        }

        // Validate CSRF token
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            if ($isAjax) {
                ob_clean(); // Clear any output buffer
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid request. Please refresh the page and try again.']);
                exit;
            }
            flash_set('error', 'Invalid request. Please try again.');
            redirect('/hotels');
            return;
        }

        // Get form data
        $hotelName = trim($_POST['hotel_name'] ?? '');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $rooms = (int)($_POST['rooms'] ?? 0);
        $website = trim($_POST['website'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $whyInterested = trim($_POST['why_interested'] ?? '');

        // Validate required fields
        $errors = [];
        if (empty($hotelName)) $errors[] = 'Hotel name is required';
        if (empty($contactPerson)) $errors[] = 'Contact person is required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
        if (empty($phone)) $errors[] = 'Phone number is required';
        if (empty($location)) $errors[] = 'Location is required';
        if ($rooms <= 0) $errors[] = 'Number of rooms must be greater than 0';

        if (!empty($errors)) {
            if ($isAjax) {
                ob_clean(); // Clear any output buffer
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
                exit;
            }
            flash_set('error', implode('. ', $errors));
            redirect('/hotels');
            return;
        }

        // Save application to database
        try {
            $stmt = db()->prepare('
                INSERT INTO hotel_applications 
                (hotel_name, contact_person, email, phone, location, rooms, website, experience, why_interested, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $hotelName, $contactPerson, $email, $phone, $location, $rooms, $website, $experience, $whyInterested, 'pending'
            ]);

            // Send notification email to admin
            $this->sendHotelApplicationNotification([
                'hotel_name' => $hotelName,
                'contact_person' => $contactPerson,
                'email' => $email,
                'phone' => $phone,
                'location' => $location,
                'rooms' => $rooms,
                'website' => $website,
                'experience' => $experience,
                'why_interested' => $whyInterested
            ]);

            if ($isAjax) {
                ob_clean(); // Clear any output buffer
                header('Content-Type: application/json');
                $response = json_encode(['success' => true, 'message' => 'Application submitted successfully!']);
                error_log('AJAX Response: ' . $response);
                echo $response;
                exit;
            }
            
            flash_set('success', 'Thank you for your application! We will review it and get back to you within 2-3 business days.');
        } catch (\PDOException $e) {
            error_log('Hotel application error: ' . $e->getMessage());
            
            if ($isAjax) {
                ob_clean(); // Clear any output buffer
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'There was an error submitting your application. Please try again.']);
                exit;
            }
            
            flash_set('error', 'There was an error submitting your application. Please try again.');
        }

        redirect('/hotels');
    }

    private function getPlatformStats(): array
    {
        $stats = [];
        
        try {
            // Total events
            $stmt = db()->query('SELECT COUNT(*) as count FROM events WHERE is_published = 1');
            $stats['total_events'] = (int)($stmt->fetch()['count'] ?? 0);
            
            // Total users
            $stmt = db()->query('SELECT COUNT(*) as count FROM users WHERE is_active = 1');
            $stats['total_users'] = (int)($stmt->fetch()['count'] ?? 0);
            
            // Total organizers
            $stmt = db()->query('SELECT COUNT(*) as count FROM organizers WHERE is_active = 1 AND is_approved = 1');
            $stats['total_organizers'] = (int)($stmt->fetch()['count'] ?? 0);
            
            // Total travel bookings
            $stmt = db()->query('SELECT COUNT(*) as count FROM travel_bookings WHERE status = "confirmed"');
            $stats['total_bookings'] = (int)($stmt->fetch()['count'] ?? 0);
            
            // Total revenue (estimated)
            $stmt = db()->query('SELECT SUM(total_amount) as total FROM travel_bookings WHERE status = "confirmed"');
            $revenue = (float)($stmt->fetch()['total'] ?? 0);
            $stats['total_revenue'] = $revenue;
            
        } catch (\PDOException $e) {
            // Default stats if database query fails
            $stats = [
                'total_events' => 150,
                'total_users' => 2500,
                'total_organizers' => 85,
                'total_bookings' => 320,
                'total_revenue' => 1250000
            ];
        }
        
        return $stats;
    }

    private function sendHotelApplicationNotification(array $data): void
    {
        try {
            $adminEmail = \App\Models\Setting::get('admin.email') ?? 'admin@ticko.com';
            $siteTitle = \App\Models\Setting::get('site.title') ?? 'Ticko';
            
            $subject = "New Hotel Application - {$data['hotel_name']}";
            $message = "
                <h2>New Hotel Application Received</h2>
                <p><strong>Hotel Name:</strong> {$data['hotel_name']}</p>
                <p><strong>Contact Person:</strong> {$data['contact_person']}</p>
                <p><strong>Email:</strong> {$data['email']}</p>
                <p><strong>Phone:</strong> {$data['phone']}</p>
                <p><strong>Location:</strong> {$data['location']}</p>
                <p><strong>Number of Rooms:</strong> {$data['rooms']}</p>
                <p><strong>Website:</strong> {$data['website']}</p>
                <p><strong>Experience:</strong> {$data['experience']}</p>
                <p><strong>Why Interested:</strong> {$data['why_interested']}</p>
                <p><strong>Submitted:</strong> " . date('Y-m-d H:i:s') . "</p>
            ";
            
            $mailer = new \App\Services\Mailer();
            $mailer->send($adminEmail, $subject, $message);
        } catch (\Exception $e) {
            error_log('Hotel application notification error: ' . $e->getMessage());
        }
    }
}



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
        $sql .= ' ORDER BY td.created_at DESC LIMIT 30';
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
        
        view('travel/destination_show', compact('destination'));
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
}



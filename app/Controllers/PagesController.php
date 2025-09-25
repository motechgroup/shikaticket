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
        $sql = 'SELECT td.*, ta.company_name, ta.logo_path FROM travel_destinations td JOIN travel_agencies ta ON ta.id = td.agency_id WHERE td.is_published = 1 AND ta.is_approved = 1';
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
            SELECT td.*, ta.company_name, ta.logo_path, ta.contact_person, ta.email, ta.phone, ta.website, ta.description as agency_description
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
}



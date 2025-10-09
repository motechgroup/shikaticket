<?php
namespace App\Controllers;

class SitemapController
{
    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base = rtrim(base_url('/'), '/');
        $urls = [];
        $now = date('c');
        
        // Static pages
        $urls[] = ['loc' => $base . '/', 'changefreq' => 'daily', 'priority' => '1.0'];
        $urls[] = ['loc' => $base . '/events', 'changefreq' => 'hourly', 'priority' => '0.9'];
        $urls[] = ['loc' => $base . '/travel', 'changefreq' => 'daily', 'priority' => '0.9'];
        $urls[] = ['loc' => $base . '/partners', 'changefreq' => 'weekly', 'priority' => '0.6'];
        $urls[] = ['loc' => $base . '/auth/login', 'changefreq' => 'monthly', 'priority' => '0.5'];
        $urls[] = ['loc' => $base . '/auth/register', 'changefreq' => 'monthly', 'priority' => '0.5'];
        $urls[] = ['loc' => $base . '/auth/organizer-register', 'changefreq' => 'monthly', 'priority' => '0.5'];
        $urls[] = ['loc' => $base . '/hotels', 'changefreq' => 'weekly', 'priority' => '0.6'];
        
        // CMS pages (if present)
        try {
            $pages = db()->query('SELECT slug, updated_at FROM pages WHERE is_published=1')->fetchAll();
            foreach ($pages as $p) {
                $urls[] = [
                    'loc' => $base . '/page?slug=' . urlencode($p['slug']),
                    'lastmod' => !empty($p['updated_at']) ? date('c', strtotime($p['updated_at'])) : $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.5'
                ];
            }
        } catch (\Throwable $e) {}
        
        // Events
        try {
            $evs = db()->query('SELECT id, updated_at FROM events WHERE is_published=1 ORDER BY updated_at DESC')->fetchAll();
            foreach ($evs as $ev) {
                $urls[] = [
                    'loc' => $base . '/events/show?id=' . (int)$ev['id'],
                    'lastmod' => !empty($ev['updated_at']) ? date('c', strtotime($ev['updated_at'])) : $now,
                    'changefreq' => 'daily',
                    'priority' => '0.8'
                ];
            }
        } catch (\Throwable $e) {}
        
        // Travel Destinations
        try {
            $dests = db()->query('SELECT id, updated_at FROM travel_destinations WHERE is_published=1 ORDER BY updated_at DESC')->fetchAll();
            foreach ($dests as $dest) {
                $urls[] = [
                    'loc' => $base . '/travel/destination?id=' . (int)$dest['id'],
                    'lastmod' => !empty($dest['updated_at']) ? date('c', strtotime($dest['updated_at'])) : $now,
                    'changefreq' => 'daily',
                    'priority' => '0.8'
                ];
            }
        } catch (\Throwable $e) {}

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $u) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
            if (!empty($u['lastmod'])) echo "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            if (!empty($u['changefreq'])) echo "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            if (!empty($u['priority'])) echo "    <priority>" . $u['priority'] . "</priority>\n";
            echo "  </url>\n";
        }
        echo "</urlset>";
    }
}



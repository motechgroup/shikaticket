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
        $urls[] = ['loc' => $base . '/partners', 'changefreq' => 'weekly', 'priority' => '0.4'];
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



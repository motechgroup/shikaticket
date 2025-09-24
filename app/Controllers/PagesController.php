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
}



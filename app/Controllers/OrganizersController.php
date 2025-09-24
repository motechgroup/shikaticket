<?php
namespace App\Controllers;

class OrganizersController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { redirect(base_url('/')); }
        $org = db()->prepare('SELECT id, full_name, email, phone, avatar_path, created_at, commission_percent FROM organizers WHERE id = ? AND is_active = 1');
        $org->execute([$id]);
        $organizer = $org->fetch();
        if (!$organizer) { redirect(base_url('/')); }
        $followers = db()->prepare('SELECT COUNT(*) AS c FROM organizer_followers WHERE organizer_id = ?');
        $followers->execute([$id]);
        $followersCount = (int)($followers->fetch()['c'] ?? 0);
        $events = db()->prepare('SELECT id, title, poster_path, event_date FROM events WHERE organizer_id = ? AND is_published = 1 ORDER BY event_date DESC');
        $events->execute([$id]);
        $eventsList = $events->fetchAll();
        $isFollowing = false;
        if (isset($_SESSION['user_id'])) {
            $ck = db()->prepare('SELECT 1 FROM organizer_followers WHERE organizer_id = ? AND user_id = ? LIMIT 1');
            $ck->execute([$id, (int)$_SESSION['user_id']]);
            $isFollowing = (bool)$ck->fetch();
        }
        view('organizers/show', compact('organizer','followersCount','eventsList','isFollowing'));
    }

    public function follow(): void
    {
        require_user();
        $id = (int)($_POST['organizer_id'] ?? 0);
        if ($id <= 0) { redirect(base_url('/')); }
        try {
            $stmt = db()->prepare('INSERT IGNORE INTO organizer_followers (organizer_id, user_id) VALUES (?, ?)');
            $stmt->execute([$id, (int)$_SESSION['user_id']]);
        } catch (\Throwable $e) {}
        redirect(base_url('/organizers/show?id=' . $id));
    }

    public function unfollow(): void
    {
        require_user();
        $id = (int)($_POST['organizer_id'] ?? 0);
        if ($id <= 0) { redirect(base_url('/')); }
        try { db()->prepare('DELETE FROM organizer_followers WHERE organizer_id = ? AND user_id = ?')->execute([$id, (int)$_SESSION['user_id']]); } catch (\Throwable $e) {}
        redirect(base_url('/organizers/show?id=' . $id));
    }
}

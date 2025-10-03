<?php
namespace App\Controllers;

class UserPointsController
{
    public function index(): void
    {
        require_user();
        $userId = (int)$_SESSION['user_id'];
        $stmt = db()->prepare('SELECT * FROM user_points WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        $entries = $stmt->fetchAll();
        $total = 0; foreach ($entries as $e) { $total += (int)$e['points']; }
        view('user/points', ['entries'=>$entries, 'total'=>$total]);
    }
}



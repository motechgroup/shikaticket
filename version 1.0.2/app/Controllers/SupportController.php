<?php
namespace App\Controllers;

class SupportController
{
    public function index(): void
    {
        require_user();
        // Ensure user has an open conversation or show start form
        $stmt = db()->prepare('SELECT * FROM support_conversations WHERE user_id = ? AND status = "open" ORDER BY id DESC LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $conversation = $stmt->fetch();
        view('user/support', compact('conversation'));
    }

    public function start(): void
    {
        require_user();
        $subject = trim($_POST['subject'] ?? 'Support Request');
        // Create conversation if none open
        $stmt = db()->prepare('SELECT id FROM support_conversations WHERE user_id = ? AND status = "open" ORDER BY id DESC LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if (!$row) {
            db()->prepare('INSERT INTO support_conversations (user_id, subject, status, last_message_at) VALUES (?, ?, "open", NOW())')
              ->execute([$_SESSION['user_id'], $subject]);
            $convId = (int)db()->lastInsertId();
        } else { $convId = (int)$row['id']; }
        // First message (optional)
        $msg = trim($_POST['message'] ?? '');
        if ($msg !== '') {
            db()->prepare('INSERT INTO support_messages (conversation_id, sender_type, sender_id, message, created_at) VALUES (?, "user", ?, ?, NOW())')
              ->execute([$convId, $_SESSION['user_id'], $msg]);
            db()->prepare('UPDATE support_conversations SET last_message_at = NOW() WHERE id = ?')->execute([$convId]);
        }
        redirect(base_url('/support'));
    }

    public function messages(): void
    {
        require_user(); header('Content-Type: application/json');
        $convId = (int)($_GET['conversation_id'] ?? 0);
        if ($convId === 0) {
            $stmt = db()->prepare('SELECT id FROM support_conversations WHERE user_id = ? AND status = "open" ORDER BY id DESC LIMIT 1');
            $stmt->execute([$_SESSION['user_id']]);
            $convId = (int)($stmt->fetch()['id'] ?? 0);
        }
        if ($convId === 0) { echo json_encode(['messages'=>[],'conversation_id'=>0]); return; }
        $stmt = db()->prepare('SELECT sender_type, message, created_at FROM support_messages WHERE conversation_id = ? ORDER BY id ASC');
        $stmt->execute([$convId]);
        echo json_encode(['messages'=>$stmt->fetchAll(),'conversation_id'=>$convId]);
    }

    public function send(): void
    {
        require_user();
        $convId = (int)($_POST['conversation_id'] ?? 0);
        if ($convId === 0) { redirect(base_url('/support')); }
        $msg = trim($_POST['message'] ?? '');
        if ($msg === '') { redirect(base_url('/support')); }
        db()->prepare('INSERT INTO support_messages (conversation_id, sender_type, sender_id, message, created_at) VALUES (?, "user", ?, ?, NOW())')
          ->execute([$convId, $_SESSION['user_id'], $msg]);
        db()->prepare('UPDATE support_conversations SET last_message_at = NOW() WHERE id = ?')->execute([$convId]);
        redirect(base_url('/support'));
    }
}



<?php
namespace App\Controllers;

class AdminSupportController
{
    public function index(): void
    {
        require_admin();
        $rows = db()->query('SELECT sc.*, u.email FROM support_conversations sc LEFT JOIN users u ON u.id = sc.user_id ORDER BY sc.last_message_at DESC, sc.id DESC LIMIT 100')->fetchAll();
        view('admin/support/index', ['conversations'=>$rows]);
    }

    public function show(): void
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { redirect(base_url('/admin/support')); }
        view('admin/support/show', ['conversation_id'=>$id]);
    }

    public function messages(): void
    {
        require_admin(); header('Content-Type: application/json');
        $id = (int)($_GET['conversation_id'] ?? 0);
        $stmt = db()->prepare('SELECT sender_type, message, created_at FROM support_messages WHERE conversation_id = ? ORDER BY id ASC');
        $stmt->execute([$id]);
        echo json_encode(['messages'=>$stmt->fetchAll()]);
    }

    public function send(): void
    {
        require_admin();
        $id = (int)($_POST['conversation_id'] ?? 0);
        $msg = trim($_POST['message'] ?? '');
        if ($id <= 0 || $msg === '') { redirect(base_url('/admin/support')); }
        $adminId = $_SESSION['admin_id'] ?? 0;
        db()->prepare('INSERT INTO support_messages (conversation_id, sender_type, sender_id, message, created_at) VALUES (?, "admin", ?, ?, NOW())')
          ->execute([$id, $adminId, $msg]);
        db()->prepare('UPDATE support_conversations SET last_message_at = NOW() WHERE id = ?')->execute([$id]);
        redirect(base_url('/admin/support/conversation?id=' . $id));
    }
}



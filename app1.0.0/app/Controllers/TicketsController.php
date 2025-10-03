<?php
namespace App\Controllers;

class TicketsController
{
    public function view(): void
    {
        $code = trim($_GET['code'] ?? '');
        if ($code === '') { echo 'Missing ticket code'; return; }
        $stmt = db()->prepare('SELECT t.*, e.title, e.event_date, e.venue FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id JOIN events e ON e.id = oi.event_id WHERE t.code = ? LIMIT 1');
        $stmt->execute([$code]);
        $ticket = $stmt->fetch();
        if (!$ticket) { echo 'Ticket not found'; return; }
        $qr = base_url('/' . ($ticket['qr_path'] ?? ''));
        include __DIR__ . '/../Views/tickets/view.php';
    }
}

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

    /**
     * Server-side PDF download for broad browser compatibility
     */
    public function download(): void
    {
        $code = trim($_GET['code'] ?? '');
        if ($code === '') { echo 'Missing ticket code'; return; }
        try {
            $stmt = db()->prepare('SELECT t.*, e.title, e.event_date, e.venue FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id JOIN events e ON e.id = oi.event_id WHERE t.code = ? LIMIT 1');
            $stmt->execute([$code]);
            $ticket = $stmt->fetch();
            if (!$ticket) { echo 'Ticket not found'; return; }

            // Absolute filesystem path for QR image
            $qrRel = $ticket['qr_path'] ?? '';
            $qrAbs = realpath(__DIR__ . '/../../public/' . ltrim($qrRel, '/'));

            // Load TCPDF
            require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('ShikaTicket');
            $pdf->SetAuthor('ShikaTicket');
            $pdf->SetTitle('Ticket #' . $code);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 15, 15);
            $pdf->AddPage();

            $html = '<h2 style="font-family: helvetica;">Ticket #' . htmlspecialchars($code) . '</h2>';
            $html .= '<div style="font-family: helvetica; font-size: 12px;">' .
                     htmlspecialchars($ticket['title'] ?? '') . '<br>' .
                     htmlspecialchars(($ticket['event_date'] ?? '') . ' • ' . ($ticket['venue'] ?? '')) .
                     '</div><br/>';
            $pdf->writeHTML($html, true, false, true, false, '');

            if ($qrAbs && file_exists($qrAbs)) {
                $pdf->Image($qrAbs, 15, 50, 80, 80, '', '', '', true);
            }

            $pdf->SetY(140);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, 'Present this ticket at entry.', 0, 1);

            $fileName = 'ticket-' . $code . '.pdf';
            // Force download
            $pdf->Output($fileName, 'D');
        } catch (\Throwable $e) {
            error_log('Ticket PDF error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Failed to generate PDF.';
        }
    }

    /**
     * Server-side PDF download for TRAVEL tickets (booking/destination tickets)
     */
    public function downloadTravel(): void
    {
        $code = trim($_GET['code'] ?? '');
        if ($code === '') { echo 'Missing ticket code'; return; }
        try {
            // Load travel ticket
            $stmt = db()->prepare('SELECT tt.*, td.title, td.destination, td.departure_date FROM travel_tickets tt JOIN travel_bookings tb ON tb.id = tt.booking_id JOIN travel_destinations td ON td.id = tb.destination_id WHERE tt.ticket_code = ? LIMIT 1');
            $stmt->execute([$code]);
            $t = $stmt->fetch();
            if (!$t) { echo 'Ticket not found'; return; }

            // Resolve QR source
            $qrRel = $t['qr_path'] ?? '';
            $qrAbs = $qrRel ? realpath(__DIR__ . '/../../public/' . ltrim($qrRel, '/')) : '';
            $qrSrc = '';
            $tmp = '';
            if ($qrAbs && file_exists($qrAbs)) {
                $qrSrc = $qrAbs;
            } else {
                // Fallback to external generator; download to temp file to ensure embedding works
                $external = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                $data = @file_get_contents($external);
                if ($data !== false) {
                    $tmp = tempnam(sys_get_temp_dir(), 'qr_');
                    file_put_contents($tmp, $data);
                    $qrSrc = $tmp;
                }
            }

            // Build PDF
            require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('ShikaTicket');
            $pdf->SetAuthor('ShikaTicket');
            $pdf->SetTitle('Travel Ticket #' . $code);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 15, 15);
            $pdf->AddPage();

            $html = '<h2 style="font-family: helvetica;">Travel Ticket #' . htmlspecialchars($code) . '</h2>';
            $subtitle = htmlspecialchars(($t['title'] ?? '') . ' • ' . ($t['destination'] ?? ''));
            $dateTxt = !empty($t['departure_date']) ? date('M j, Y', strtotime($t['departure_date'])) : '';
            $html .= '<div style="font-family: helvetica; font-size: 12px;">' . $subtitle . '<br>' . $dateTxt . '</div><br/>';
            $pdf->writeHTML($html, true, false, true, false, '');

            if ($qrSrc && file_exists($qrSrc)) {
                $pdf->Image($qrSrc, 15, 50, 80, 80, '', '', '', true);
            }

            $pdf->SetY(140);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, 'Present this ticket at entry.', 0, 1);

            $fileName = 'travel-ticket-' . $code . '.pdf';
            $pdf->Output($fileName, 'D');

            if ($tmp && file_exists($tmp)) { @unlink($tmp); }
        } catch (\Throwable $e) {
            error_log('Travel ticket PDF error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Failed to generate PDF.';
        }
    }

    /**
     * Serve a QR image for TRAVEL tickets from our own domain (CSP-safe)
     */
    public function qrTravel(): void
    {
        $code = trim($_GET['code'] ?? '');
        if ($code === '') { http_response_code(400); return; }
        try {
            $stmt = db()->prepare('SELECT qr_path FROM travel_tickets WHERE ticket_code = ? LIMIT 1');
            $stmt->execute([$code]);
            $row = $stmt->fetch();
            $qrRel = $row['qr_path'] ?? '';
            $qrAbs = $qrRel !== '' ? (__DIR__ . '/../../public/' . ltrim($qrRel, '/')) : '';

            header('Content-Type: image/png');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            // Serve local if present
            if ($qrAbs !== '' && is_file($qrAbs)) {
                readfile($qrAbs);
                return;
            }
            // Fallback: fetch from external generator server-side and stream
            $external = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
            $data = @file_get_contents($external);
            if ($data !== false) {
                echo $data;
                // Attempt to persist locally for future requests
                try {
                    $dir = __DIR__ . '/../../public/uploads/qrs/travel/';
                    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                    $rel = 'uploads/qrs/travel/' . $code . '.png';
                    @file_put_contents($dir . $code . '.png', $data);
                    try { db()->prepare('UPDATE travel_tickets SET qr_path = ? WHERE ticket_code = ?')->execute([$rel, $code]); } catch (\Throwable $e) {}
                } catch (\Throwable $e) {}
                return;
            }
            // Last resort: simple PNG placeholder
            echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAQAAAB5xQmTAAAAMElEQVR42u3BMQEAAADCoPVP7WcIoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADwGJQ0AAEZVbV0AAAAASUVORK5CYII=');
        } catch (\Throwable $e) {
            // Never fail hard for QR endpoint; serve a tiny transparent PNG to avoid 500s
            header('Content-Type: image/png');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=');
        }
    }
}

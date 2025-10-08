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
        if ($code === '') { 
            http_response_code(400);
            echo 'Missing ticket code'; 
            return; 
        }
        
        try {
            $stmt = db()->prepare('SELECT t.*, e.title, e.event_date, e.venue FROM tickets t JOIN order_items oi ON oi.id = t.order_item_id JOIN events e ON e.id = oi.event_id WHERE t.code = ? LIMIT 1');
            $stmt->execute([$code]);
            $ticket = $stmt->fetch();
            if (!$ticket) { 
                http_response_code(404);
                echo 'Ticket not found'; 
                return; 
            }

            // Load TCPDF
            require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';
            
            // Create PDF with better settings
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('ShikaTicket');
            $pdf->SetAuthor('ShikaTicket');
            $pdf->SetTitle('Ticket #' . $code);
            $pdf->SetSubject('Event Ticket');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(false);
            $pdf->AddPage();

            // Header with logo
            $logoPath = realpath(__DIR__ . '/../../uploads/site/logo.png');
            if ($logoPath && file_exists($logoPath)) {
                // Add logo at the top
                $pdf->Image($logoPath, 15, 10, 30, 15, '', '', '', true);
                $pdf->SetY(30);
            }
            
            $pdf->SetFont('helvetica', 'B', 24);
            $pdf->SetTextColor(239, 68, 68); // Red color
            $pdf->Cell(0, 15, 'ShikaTicket', 0, 1, 'C');
            
            // Reset color
            $pdf->SetTextColor(0, 0, 0);
            
            // Ticket info
            $pdf->SetFont('helvetica', 'B', 18);
            $pdf->Cell(0, 10, 'Ticket #' . $code, 0, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 8, htmlspecialchars($ticket['title'] ?? ''), 0, 1, 'C');
            
            if (!empty($ticket['event_date'])) {
                $date = date('M j, Y', strtotime($ticket['event_date']));
                $venue = $ticket['venue'] ?? '';
                $pdf->Cell(0, 8, $date . ($venue ? ' • ' . htmlspecialchars($venue) : ''), 0, 1, 'C');
            }

            // QR Code
            $qrRel = $ticket['qr_path'] ?? '';
            $qrAbs = '';
            if ($qrRel) {
                $qrAbs = realpath(__DIR__ . '/../../' . ltrim($qrRel, '/'));
            }
            
            if ($qrAbs && file_exists($qrAbs)) {
                // Center the QR code
                $qrSize = 80; // mm
                $pageWidth = $pdf->getPageWidth() - 30; // Account for margins
                $qrX = ($pageWidth - $qrSize) / 2;
                $pdf->Image($qrAbs, $qrX, 80, $qrSize, $qrSize, '', '', '', true);
            } else {
                // Fallback: generate QR code externally
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                try {
                    $qrData = @file_get_contents($qrUrl);
                    if ($qrData !== false) {
                        $tmpFile = tempnam(sys_get_temp_dir(), 'qr_');
                        file_put_contents($tmpFile, $qrData);
                        $qrSize = 80;
                        $pageWidth = $pdf->getPageWidth() - 30;
                        $qrX = ($pageWidth - $qrSize) / 2;
                        $pdf->Image($tmpFile, $qrX, 80, $qrSize, $qrSize);
                        unlink($tmpFile);
                    }
                } catch (\Throwable $e) {
                    // Add placeholder text if QR generation fails
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->SetTextColor(128, 128, 128);
                    $pdf->Cell(0, 10, 'QR Code: ' . $code, 0, 1, 'C');
                    $pdf->SetTextColor(0, 0, 0);
                }
            }

            // Footer instructions
            $pdf->SetY(170);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Present this ticket at entry', 0, 1, 'C');
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(0, 8, 'Keep this ticket safe and secure', 0, 1, 'C');
            $pdf->Cell(0, 8, 'Generated on ' . date('M j, Y H:i'), 0, 1, 'C');

            // Set proper headers
            $fileName = 'ticket-' . $code . '.pdf';
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Output PDF
            $pdf->Output($fileName, 'D');
            
        } catch (\Throwable $e) {
            error_log('Ticket PDF error: ' . $e->getMessage());
            error_log('Ticket PDF trace: ' . $e->getTraceAsString());
            http_response_code(500);
            header('Content-Type: text/plain');
            echo 'Failed to generate PDF. Please try again later.';
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
            $qrAbs = $qrRel ? realpath(__DIR__ . '/../../' . ltrim($qrRel, '/')) : '';
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
            $qrAbs = $qrRel !== '' ? (__DIR__ . '/../../' . ltrim($qrRel, '/')) : '';

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
                    $dir = __DIR__ . '/../../uploads/qrs/travel/';
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

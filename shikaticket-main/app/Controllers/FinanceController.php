<?php

namespace App\Controllers;

class FinanceController
{
    /**
     * Admin finance dashboard
     */
    public function adminDashboard(): void
    {
        require_admin();
        
        // Get date range from request
        $startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
        $endDate = $_GET['end_date'] ?? date('Y-m-t'); // Last day of current month
        
        // Get overall statistics
        $stats = $this->getAdminStats($startDate, $endDate);
        
        // Get revenue breakdown
        $revenueBreakdown = $this->getRevenueBreakdown($startDate, $endDate);
        
        // Get top performers
        $topEvents = $this->getTopEvents($startDate, $endDate);
        $topDestinations = $this->getTopDestinations($startDate, $endDate);
        
        // Get commission analytics
        $commissionStats = $this->getCommissionStats($startDate, $endDate);
        
        view('admin/finance/dashboard', [
            'stats' => $stats,
            'revenueBreakdown' => $revenueBreakdown,
            'topEvents' => $topEvents,
            'topDestinations' => $topDestinations,
            'commissionStats' => $commissionStats,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    
    /**
     * Travel agency finance dashboard
     */
    public function travelDashboard(): void
    {
        require_travel_agency();
        
        $agencyId = (int)$_SESSION['travel_agency_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        // Get agency statistics
        $stats = $this->getTravelAgencyStats($agencyId, $startDate, $endDate);
        
        // Get destination performance
        $destinationStats = $this->getDestinationStats($agencyId, $startDate, $endDate);
        
        // Get booking analytics
        $bookingStats = $this->getBookingStats($agencyId, $startDate, $endDate);
        
        view('travel/finance/dashboard', [
            'stats' => $stats,
            'destinationStats' => $destinationStats,
            'bookingStats' => $bookingStats,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    
    /**
     * Organizer finance dashboard
     */
    public function organizerDashboard(): void
    {
        require_organizer();
        
        $organizerId = (int)$_SESSION['organizer_id'];
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        // Get organizer statistics
        $stats = $this->getOrganizerStats($organizerId, $startDate, $endDate);
        
        // Get event performance
        $eventStats = $this->getEventStats($organizerId, $startDate, $endDate);
        
        // Get ticket sales analytics
        $salesStats = $this->getSalesStats($organizerId, $startDate, $endDate);
        
        view('organizer/finance/dashboard', [
            'stats' => $stats,
            'eventStats' => $eventStats,
            'salesStats' => $salesStats,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    
    /**
     * Export reports
     */
    public function exportReport(): void
    {
        $userType = $_GET['type'] ?? '';
        $format = $_GET['format'] ?? 'csv';
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        switch ($userType) {
            case 'admin':
                require_admin();
                $this->exportAdminReport($format, $startDate, $endDate);
                break;
            case 'travel':
                require_travel_agency();
                $this->exportTravelReport($format, $startDate, $endDate);
                break;
            case 'organizer':
                require_organizer();
                $this->exportOrganizerReport($format, $startDate, $endDate);
                break;
            default:
                http_response_code(404);
                echo 'Invalid report type';
        }
    }
    
    /**
     * Get admin statistics
     */
    private function getAdminStats(string $startDate, string $endDate): array
    {
        try {
            // Event revenue from orders
            $eventRevenue = db()->prepare("
                SELECT COALESCE(SUM(oi.quantity * oi.unit_price), 0) as total_revenue
                FROM orders o
                JOIN order_items oi ON oi.order_id = o.id
                WHERE o.status = 'paid' 
                AND DATE(o.created_at) BETWEEN ? AND ?
            ");
            $eventRevenue->execute([$startDate, $endDate]);
            $eventRev = $eventRevenue->fetch();
            
            // Travel revenue from travel bookings
            $travelRevenue = db()->prepare("
                SELECT COALESCE(SUM(tb.total_amount), 0) as total_revenue
                FROM travel_bookings tb
                JOIN travel_payments tp ON tp.booking_id = tb.id
                WHERE tp.payment_status = 'paid' 
                AND DATE(tb.booking_date) BETWEEN ? AND ?
            ");
            $travelRevenue->execute([$startDate, $endDate]);
            $travelRev = $travelRevenue->fetch();
            
            // Event commissions (using default 5% commission rate)
            $eventCommissions = db()->prepare("
                SELECT COALESCE(SUM(oi.quantity * oi.unit_price * 5 / 100), 0) as total_commissions
                FROM orders o
                JOIN order_items oi ON oi.order_id = o.id
                WHERE o.status = 'paid' 
                AND DATE(o.created_at) BETWEEN ? AND ?
            ");
            $eventCommissions->execute([$startDate, $endDate]);
            $eventComm = $eventCommissions->fetch();
            
            // Travel commissions
            $travelCommissions = db()->prepare("
                SELECT COALESCE(SUM(tb.total_amount * ta.commission_percent / 100), 0) as total_commissions
                FROM travel_bookings tb
                JOIN travel_payments tp ON tp.booking_id = tb.id
                JOIN travel_destinations td ON td.id = tb.destination_id
                JOIN travel_agencies ta ON ta.id = td.agency_id
                WHERE tp.payment_status = 'paid' 
                AND DATE(tb.booking_date) BETWEEN ? AND ?
            ");
            $travelCommissions->execute([$startDate, $endDate]);
            $travelComm = $travelCommissions->fetch();
            
            $eventRevenueAmount = (float)$eventRev['total_revenue'];
            $travelRevenueAmount = (float)$travelRev['total_revenue'];
            $eventCommissionAmount = (float)$eventComm['total_commissions'];
            $travelCommissionAmount = (float)$travelComm['total_commissions'];
            
            $totalRevenue = $eventRevenueAmount + $travelRevenueAmount;
            $totalCommissions = $eventCommissionAmount + $travelCommissionAmount;
            $netRevenue = $totalRevenue - $totalCommissions;
            
            return [
                'total_revenue' => $totalRevenue,
                'event_revenue' => $eventRevenueAmount,
                'travel_revenue' => $travelRevenueAmount,
                'total_commissions' => $totalCommissions,
                'event_commissions' => $eventCommissionAmount,
                'travel_commissions' => $travelCommissionAmount,
                'net_revenue' => $netRevenue
            ];
        } catch (\Exception $e) {
            // Log error for debugging
            error_log("Finance stats error: " . $e->getMessage());
            
            return [
                'total_revenue' => 0,
                'event_revenue' => 0,
                'travel_revenue' => 0,
                'total_commissions' => 0,
                'event_commissions' => 0,
                'travel_commissions' => 0,
                'net_revenue' => 0
            ];
        }
    }
    
    /**
     * Get travel agency statistics
     */
    private function getTravelAgencyStats(int $agencyId, string $startDate, string $endDate): array
    {
        try {
            // Total bookings and revenue
            $stats = db()->prepare("
                SELECT 
                    COUNT(tb.id) as total_bookings,
                    COALESCE(SUM(CASE WHEN tp.payment_status = 'paid' THEN tb.total_amount ELSE 0 END), 0) as total_revenue,
                    COALESCE(SUM(CASE WHEN tp.payment_status = 'paid' THEN (tb.total_amount * ta.commission_percent / 100) ELSE 0 END), 0) as total_commissions,
                    COALESCE(SUM(CASE WHEN tp.payment_status = 'paid' THEN (tb.total_amount - (tb.total_amount * ta.commission_percent / 100)) ELSE 0 END), 0) as net_revenue
                FROM travel_bookings tb
                LEFT JOIN travel_payments tp ON tp.booking_id = tb.id
                LEFT JOIN travel_destinations td ON td.id = tb.destination_id
                LEFT JOIN travel_agencies ta ON ta.id = td.agency_id
                WHERE td.agency_id = ? AND DATE(tb.booking_date) BETWEEN ? AND ?
            ");
            $stats->execute([$agencyId, $startDate, $endDate]);
            $result = $stats->fetch();
            
            return [
                'total_bookings' => (int)$result['total_bookings'],
                'total_revenue' => (float)$result['total_revenue'],
                'total_commissions' => (float)$result['total_commissions'],
                'net_revenue' => (float)$result['net_revenue']
            ];
        } catch (\Exception $e) {
            return [
                'total_bookings' => 0,
                'total_revenue' => 0,
                'total_commissions' => 0,
                'net_revenue' => 0
            ];
        }
    }
    
    /**
     * Get organizer statistics
     */
    private function getOrganizerStats(int $organizerId, string $startDate, string $endDate): array
    {
        try {
            // Total ticket sales and revenue
            $stats = db()->prepare("
                SELECT 
                    COUNT(oi.id) as total_tickets,
                    COALESCE(SUM(CASE WHEN o.status = 'paid' THEN oi.quantity ELSE 0 END), 0) as tickets_sold,
                    COALESCE(SUM(CASE WHEN o.status = 'paid' THEN (oi.quantity * oi.unit_price) ELSE 0 END), 0) as total_revenue,
                    COALESCE(SUM(CASE WHEN o.status = 'paid' THEN (oi.quantity * oi.unit_price * o.commission_percent / 100) ELSE 0 END), 0) as total_commissions,
                    COALESCE(SUM(CASE WHEN o.status = 'paid' THEN (oi.quantity * oi.unit_price - (oi.quantity * oi.unit_price * o.commission_percent / 100)) ELSE 0 END), 0) as net_revenue
                FROM order_items oi
                LEFT JOIN orders o ON o.id = oi.order_id
                LEFT JOIN events e ON e.id = oi.event_id
                WHERE e.organizer_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
            ");
            $stats->execute([$organizerId, $startDate, $endDate]);
            $result = $stats->fetch();
            
            return [
                'total_tickets' => (int)$result['total_tickets'],
                'tickets_sold' => (int)$result['tickets_sold'],
                'total_revenue' => (float)$result['total_revenue'],
                'total_commissions' => (float)$result['total_commissions'],
                'net_revenue' => (float)$result['net_revenue']
            ];
        } catch (\Exception $e) {
            return [
                'total_tickets' => 0,
                'tickets_sold' => 0,
                'total_revenue' => 0,
                'total_commissions' => 0,
                'net_revenue' => 0
            ];
        }
    }
    
    /**
     * Export admin report
     */
    private function exportAdminReport(string $format, string $startDate, string $endDate): void
    {
        $data = $this->getAdminStats($startDate, $endDate);
        $filename = "admin_finance_report_{$startDate}_to_{$endDate}";
        
        if ($format === 'csv') {
            $this->exportCSV($filename, $data, 'admin');
        } else {
            $this->exportPDF($filename, $data, 'admin');
        }
    }
    
    /**
     * Export travel agency report
     */
    private function exportTravelReport(string $format, string $startDate, string $endDate): void
    {
        $agencyId = (int)$_SESSION['travel_agency_id'];
        $data = $this->getTravelAgencyStats($agencyId, $startDate, $endDate);
        $filename = "travel_finance_report_{$startDate}_to_{$endDate}";
        
        if ($format === 'csv') {
            $this->exportCSV($filename, $data, 'travel');
        } else {
            $this->exportPDF($filename, $data, 'travel');
        }
    }
    
    /**
     * Export organizer report
     */
    private function exportOrganizerReport(string $format, string $startDate, string $endDate): void
    {
        $organizerId = (int)$_SESSION['organizer_id'];
        $data = $this->getOrganizerStats($organizerId, $startDate, $endDate);
        $filename = "organizer_finance_report_{$startDate}_to_{$endDate}";
        
        if ($format === 'csv') {
            $this->exportCSV($filename, $data, 'organizer');
        } else {
            $this->exportPDF($filename, $data, 'organizer');
        }
    }
    
    /**
     * Export CSV report
     */
    private function exportCSV(string $filename, array $data, string $type): void
    {
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['Metric', 'Value']);
        
        // Add data based on type
        switch ($type) {
            case 'admin':
                fputcsv($output, ['Total Revenue', $data['total_revenue']]);
                fputcsv($output, ['Event Revenue', $data['event_revenue']]);
                fputcsv($output, ['Travel Revenue', $data['travel_revenue']]);
                fputcsv($output, ['Total Commissions', $data['total_commissions']]);
                fputcsv($output, ['Net Revenue', $data['net_revenue']]);
                break;
            case 'travel':
                fputcsv($output, ['Total Bookings', $data['total_bookings']]);
                fputcsv($output, ['Total Revenue', $data['total_revenue']]);
                fputcsv($output, ['Total Commissions', $data['total_commissions']]);
                fputcsv($output, ['Net Revenue', $data['net_revenue']]);
                break;
            case 'organizer':
                fputcsv($output, ['Total Tickets', $data['total_tickets']]);
                fputcsv($output, ['Tickets Sold', $data['tickets_sold']]);
                fputcsv($output, ['Total Revenue', $data['total_revenue']]);
                fputcsv($output, ['Total Commissions', $data['total_commissions']]);
                fputcsv($output, ['Net Revenue', $data['net_revenue']]);
                break;
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export PDF report
     */
    private function exportPDF(string $filename, array $data, string $type): void
    {
        // Clean any existing output buffer
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Check if TCPDF is available
        if (file_exists(__DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php')) {
            $this->generatePDFWithTCPDF($filename, $data, $type);
        } else {
            // Fallback to HTML download
            $this->generateHTMLReport($filename, $data, $type);
        }
    }
    
    /**
     * Generate PDF using TCPDF
     */
    private function generatePDFWithTCPDF(string $filename, array $data, string $type): void
    {
        // Clean output buffer to prevent TCPDF errors
        if (ob_get_level()) {
            ob_clean();
        }
        
        require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('ShikaTicket System');
        $pdf->SetAuthor('ShikaTicket');
        $pdf->SetTitle('Finance Report - ' . ucfirst($type));
        $pdf->SetSubject('Financial Analysis Report');
        
        // Set margins
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        
        // Add a page
        $pdf->AddPage();
        
        // Add site logo if available - try different logo files
        $possibleLogos = [
            'uploads/site/logo_1758739154.png', // Most recent logo
            'uploads/site/logo.png', // Default logo
            'uploads/site/logo_1758721470.png' // Older logo
        ];
        
        $logoPath = null;
        foreach ($possibleLogos as $logo) {
            $testPath = __DIR__ . '/../../public/' . $logo;
            if (file_exists($testPath)) {
                $logoPath = $testPath;
                break;
            }
        }
        
        if ($logoPath && file_exists($logoPath)) {
            // Get image dimensions to maintain aspect ratio
            $imageInfo = @getimagesize($logoPath);
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                $maxWidth = 40; // Maximum width in mm
                $maxHeight = 15; // Maximum height in mm
                
                // Calculate aspect ratio
                $aspectRatio = $width / $height;
                
                if ($width > $height) {
                    // Landscape image
                    $displayWidth = $maxWidth;
                    $displayHeight = $maxWidth / $aspectRatio;
                    if ($displayHeight > $maxHeight) {
                        $displayHeight = $maxHeight;
                        $displayWidth = $maxHeight * $aspectRatio;
                    }
                } else {
                    // Portrait or square image
                    $displayHeight = $maxHeight;
                    $displayWidth = $maxHeight * $aspectRatio;
                    if ($displayWidth > $maxWidth) {
                        $displayWidth = $maxWidth;
                        $displayHeight = $maxWidth / $aspectRatio;
                    }
                }
                
                // Suppress PNG warnings and add logo
                $oldErrorReporting = error_reporting(0);
                $pdf->Image($logoPath, 15, 10, $displayWidth, $displayHeight);
                error_reporting($oldErrorReporting);
                $pdf->Ln($displayHeight + 5);
            } else {
                $pdf->Ln(10);
            }
        } else {
            $pdf->Ln(10);
        }
        
        // Set font for title
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(44, 62, 80); // Dark blue color
        $pdf->Cell(0, 10, 'ShikaTicket Finance Report', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 14);
        $pdf->SetTextColor(52, 73, 94); // Gray color
        $pdf->Cell(0, 8, ucfirst($type) . ' Financial Summary', 0, 1, 'C');
        $pdf->Cell(0, 8, 'Generated on: ' . date('F j, Y'), 0, 1, 'C');
        $pdf->Ln(15);
        
        // Report period
        $dateRange = $_GET['start_date'] ?? date('Y-m-01') . ' to ' . ($_GET['end_date'] ?? date('Y-m-t'));
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'Report Period: ' . $dateRange, 0, 1, 'L');
        $pdf->Ln(5);
        
        // Create table header
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetFillColor(52, 73, 94); // Dark gray background
        $pdf->SetTextColor(255, 255, 255); // White text
        $pdf->Cell(100, 10, 'Financial Metric', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Amount (KES)', 1, 1, 'C', true);
        
        // Reset colors for table content
        $pdf->SetFillColor(248, 249, 250); // Light gray background
        $pdf->SetTextColor(0, 0, 0); // Black text
        $pdf->SetFont('helvetica', '', 9);
        
        switch ($type) {
            case 'admin':
                $this->addAdminTableRows($pdf, $data);
                break;
            case 'travel':
                $this->addTravelTableRows($pdf, $data);
                break;
            case 'organizer':
                $this->addOrganizerTableRows($pdf, $data);
                break;
        }
        
        // Add footer with copyright
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(100, 100, 100); // Gray color
        
        // Footer line
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(5);
        
        // Copyright and company info
        $pdf->Cell(0, 4, '© ' . date('Y') . ' ShikaTicket. All rights reserved.', 0, 1, 'C');
        $pdf->Cell(0, 4, 'Nairobi, Kenya | +254 700 000 000 | info@example.com', 0, 1, 'C');
        $pdf->Cell(0, 4, 'Made with ❤️ · Developed by Motech Digital Agency', 0, 1, 'C');
        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->Cell(0, 3, 'This report was generated automatically by the ShikaTicket system.', 0, 1, 'C');
        
        // Output PDF
        $pdf->Output($filename . '.pdf', 'D');
        exit;
    }
    
    /**
     * Add admin table rows
     */
    private function addAdminTableRows($pdf, array $data): void
    {
        $rows = [
            ['Total Revenue', number_format($data['total_revenue'], 2)],
            ['Event Revenue', number_format($data['event_revenue'], 2)],
            ['Travel Revenue', number_format($data['travel_revenue'], 2)],
            ['Event Commissions', number_format($data['event_commissions'], 2)],
            ['Travel Commissions', number_format($data['travel_commissions'], 2)],
            ['Total Commissions', number_format($data['total_commissions'], 2)],
            ['Net Revenue', number_format($data['net_revenue'], 2)]
        ];
        
        foreach ($rows as $index => $row) {
            $fill = ($index % 2 == 0); // Alternate row colors
            $pdf->Cell(100, 8, $row[0], 1, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 9); // Bold for currency values
            $pdf->Cell(60, 8, 'KES ' . $row[1], 1, 1, 'R', $fill);
            $pdf->SetFont('helvetica', '', 9); // Reset font
        }
    }
    
    /**
     * Add travel table rows
     */
    private function addTravelTableRows($pdf, array $data): void
    {
        $rows = [
            ['Total Bookings', $data['total_bookings']],
            ['Total Revenue', number_format($data['total_revenue'], 2)],
            ['Total Commissions', number_format($data['total_commissions'], 2)],
            ['Net Revenue', number_format($data['net_revenue'], 2)]
        ];
        
        foreach ($rows as $index => $row) {
            $fill = ($index % 2 == 0); // Alternate row colors
            $pdf->Cell(100, 8, $row[0], 1, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 9); // Bold for values
            $value = (is_numeric($row[1]) && $row[1] > 100) ? 'KES ' . $row[1] : $row[1];
            $pdf->Cell(60, 8, $value, 1, 1, 'R', $fill);
            $pdf->SetFont('helvetica', '', 9); // Reset font
        }
    }
    
    /**
     * Add organizer table rows
     */
    private function addOrganizerTableRows($pdf, array $data): void
    {
        $rows = [
            ['Total Tickets Available', $data['total_tickets']],
            ['Tickets Sold', $data['tickets_sold']],
            ['Total Revenue', number_format($data['total_revenue'], 2)],
            ['Total Commissions', number_format($data['total_commissions'], 2)],
            ['Net Revenue', number_format($data['net_revenue'], 2)]
        ];
        
        foreach ($rows as $index => $row) {
            $fill = ($index % 2 == 0); // Alternate row colors
            $pdf->Cell(100, 8, $row[0], 1, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 9); // Bold for values
            $value = (is_numeric($row[1]) && $row[1] > 100) ? 'KES ' . $row[1] : $row[1];
            $pdf->Cell(60, 8, $value, 1, 1, 'R', $fill);
            $pdf->SetFont('helvetica', '', 9); // Reset font
        }
    }
    
    /**
     * Generate HTML report (fallback)
     */
    private function generateHTMLReport(string $filename, array $data, string $type): void
    {
        $html = $this->generateReportHTML($data, $type);
        
        header('Content-Type: text/html; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}.html\"");
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $html;
        exit;
    }
    
    /**
     * Generate report HTML
     */
    private function generateReportHTML(array $data, string $type): string
    {
        $dateRange = $_GET['start_date'] ?? date('Y-m-01') . ' to ' . ($_GET['end_date'] ?? date('Y-m-t'));
        $reportDate = date('F j, Y');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Finance Report - ' . ucfirst($type) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0; color: #666; }
        .report-info { margin-bottom: 30px; }
        .report-info p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .currency { text-align: right; font-weight: bold; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>ShikaTicket Finance Report</h1>
        <p>' . ucfirst($type) . ' Financial Summary</p>
        <p>Generated on ' . $reportDate . '</p>
    </div>
    
    <div class="report-info">
        <p><strong>Report Period:</strong> ' . $dateRange . '</p>
        <p><strong>Report Type:</strong> ' . ucfirst($type) . ' Financial Analysis</p>
    </div>';
        
        switch ($type) {
            case 'admin':
                $html .= '<h2>Administrative Financial Summary</h2>
                <table>
                    <tr><th>Financial Metric</th><th>Amount (KES)</th></tr>
                    <tr><td>Total Revenue</td><td class="currency">' . number_format($data['total_revenue'], 2) . '</td></tr>
                    <tr><td>Event Revenue</td><td class="currency">' . number_format($data['event_revenue'], 2) . '</td></tr>
                    <tr><td>Travel Revenue</td><td class="currency">' . number_format($data['travel_revenue'], 2) . '</td></tr>
                    <tr><td>Event Commissions</td><td class="currency">' . number_format($data['event_commissions'], 2) . '</td></tr>
                    <tr><td>Travel Commissions</td><td class="currency">' . number_format($data['travel_commissions'], 2) . '</td></tr>
                    <tr><td>Total Commissions</td><td class="currency">' . number_format($data['total_commissions'], 2) . '</td></tr>
                    <tr><td><strong>Net Revenue</strong></td><td class="currency"><strong>' . number_format($data['net_revenue'], 2) . '</strong></td></tr>
                </table>';
                break;
                
            case 'travel':
                $html .= '<h2>Travel Agency Financial Summary</h2>
                <table>
                    <tr><th>Financial Metric</th><th>Value</th></tr>
                    <tr><td>Total Bookings</td><td class="currency">' . $data['total_bookings'] . '</td></tr>
                    <tr><td>Total Revenue</td><td class="currency">' . number_format($data['total_revenue'], 2) . '</td></tr>
                    <tr><td>Total Commissions</td><td class="currency">' . number_format($data['total_commissions'], 2) . '</td></tr>
                    <tr><td><strong>Net Revenue</strong></td><td class="currency"><strong>' . number_format($data['net_revenue'], 2) . '</strong></td></tr>
                </table>';
                break;
                
            case 'organizer':
                $html .= '<h2>Organizer Financial Summary</h2>
                <table>
                    <tr><th>Financial Metric</th><th>Value</th></tr>
                    <tr><td>Total Tickets Available</td><td class="currency">' . $data['total_tickets'] . '</td></tr>
                    <tr><td>Tickets Sold</td><td class="currency">' . $data['tickets_sold'] . '</td></tr>
                    <tr><td>Total Revenue</td><td class="currency">' . number_format($data['total_revenue'], 2) . '</td></tr>
                    <tr><td>Total Commissions</td><td class="currency">' . number_format($data['total_commissions'], 2) . '</td></tr>
                    <tr><td><strong>Net Revenue</strong></td><td class="currency"><strong>' . number_format($data['net_revenue'], 2) . '</strong></td></tr>
                </table>';
                break;
        }
        
        $html .= '
    <div class="footer">
        <p>This report was generated automatically by the ShikaTicket system.</p>
        <p>For questions or support, please contact the system administrator.</p>
    </div>
    
    <script>
        // Auto-print when opened
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>';
        
        return $html;
    }
    
    // Additional helper methods for detailed analytics
    private function getRevenueBreakdown(string $startDate, string $endDate): array
    {
        try {
            // Event revenue breakdown
            $eventRevenue = db()->prepare("
                SELECT COALESCE(SUM(oi.quantity * oi.unit_price), 0) as event_revenue
                FROM orders o
                JOIN order_items oi ON oi.order_id = o.id
                WHERE o.status = 'paid' 
                AND DATE(o.created_at) BETWEEN ? AND ?
            ");
            $eventRevenue->execute([$startDate, $endDate]);
            $eventRev = $eventRevenue->fetch();
            
            // Travel revenue breakdown
            $travelRevenue = db()->prepare("
                SELECT COALESCE(SUM(tb.total_amount), 0) as travel_revenue
                FROM travel_bookings tb
                JOIN travel_payments tp ON tp.booking_id = tb.id
                WHERE tp.payment_status = 'paid' 
                AND DATE(tb.booking_date) BETWEEN ? AND ?
            ");
            $travelRevenue->execute([$startDate, $endDate]);
            $travelRev = $travelRevenue->fetch();
            
            return [
                'event_revenue' => (float)$eventRev['event_revenue'],
                'travel_revenue' => (float)$travelRev['travel_revenue']
            ];
        } catch (\Exception $e) {
            return [
                'event_revenue' => 0,
                'travel_revenue' => 0
            ];
        }
    }
    
    private function getTopEvents(string $startDate, string $endDate): array
    {
        try {
            $topEvents = db()->prepare("
                SELECT 
                    e.title,
                    e.id,
                    o.full_name as organizer_name,
                    COALESCE(SUM(oi.quantity * oi.unit_price), 0) as revenue,
                    COALESCE(SUM(oi.quantity), 0) as tickets_sold
                FROM events e
                LEFT JOIN organizers o ON o.id = e.organizer_id
                LEFT JOIN order_items oi ON oi.event_id = e.id
                LEFT JOIN orders ord ON ord.id = oi.order_id AND ord.status = 'paid'
                WHERE DATE(COALESCE(ord.created_at, e.created_at)) BETWEEN ? AND ?
                GROUP BY e.id, e.title, o.full_name
                HAVING revenue > 0
                ORDER BY revenue DESC
                LIMIT 5
            ");
            $topEvents->execute([$startDate, $endDate]);
            return $topEvents->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getTopDestinations(string $startDate, string $endDate): array
    {
        try {
            $topDestinations = db()->prepare("
                SELECT 
                    td.title,
                    td.id,
                    ta.company_name as agency_name,
                    COALESCE(SUM(tb.total_amount), 0) as revenue,
                    COUNT(tb.id) as bookings
                FROM travel_destinations td
                LEFT JOIN travel_agencies ta ON ta.id = td.agency_id
                LEFT JOIN travel_bookings tb ON tb.destination_id = td.id
                LEFT JOIN travel_payments tp ON tp.booking_id = tb.id
                WHERE tp.payment_status = 'paid' 
                AND DATE(tb.booking_date) BETWEEN ? AND ?
                GROUP BY td.id, td.title, ta.company_name
                HAVING revenue > 0
                ORDER BY revenue DESC
                LIMIT 5
            ");
            $topDestinations->execute([$startDate, $endDate]);
            return $topDestinations->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getCommissionStats(string $startDate, string $endDate): array
    {
        try {
            // Event commissions (using default 5% commission rate)
            $eventCommissions = db()->prepare("
                SELECT COALESCE(SUM(oi.quantity * oi.unit_price * 5 / 100), 0) as event_commissions
                FROM orders o
                JOIN order_items oi ON oi.order_id = o.id
                WHERE o.status = 'paid' 
                AND DATE(o.created_at) BETWEEN ? AND ?
            ");
            $eventCommissions->execute([$startDate, $endDate]);
            $eventComm = $eventCommissions->fetch();
            
            // Travel commissions
            $travelCommissions = db()->prepare("
                SELECT COALESCE(SUM(tb.total_amount * ta.commission_percent / 100), 0) as travel_commissions
                FROM travel_bookings tb
                JOIN travel_payments tp ON tp.booking_id = tb.id
                JOIN travel_destinations td ON td.id = tb.destination_id
                JOIN travel_agencies ta ON ta.id = td.agency_id
                WHERE tp.payment_status = 'paid' 
                AND DATE(tb.booking_date) BETWEEN ? AND ?
            ");
            $travelCommissions->execute([$startDate, $endDate]);
            $travelComm = $travelCommissions->fetch();
            
            return [
                'event_commissions' => (float)$eventComm['event_commissions'],
                'travel_commissions' => (float)$travelComm['travel_commissions']
            ];
        } catch (\Exception $e) {
            return [
                'event_commissions' => 0,
                'travel_commissions' => 0
            ];
        }
    }
    
    private function getDestinationStats(int $agencyId, string $startDate, string $endDate): array
    {
        // Implementation for destination performance
        return [];
    }
    
    private function getBookingStats(int $agencyId, string $startDate, string $endDate): array
    {
        // Implementation for booking analytics
        return [];
    }
    
    private function getEventStats(int $organizerId, string $startDate, string $endDate): array
    {
        // Implementation for event performance
        return [];
    }
    
    private function getSalesStats(int $organizerId, string $startDate, string $endDate): array
    {
        // Implementation for sales analytics
        return [];
    }
}

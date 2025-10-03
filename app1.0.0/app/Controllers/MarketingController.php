<?php

namespace App\Controllers;

class MarketingController
{
    public function dashboard()
    {
        require_admin();
        
        // Include marketing module files
        require_once __DIR__ . '/../../marketing/app/Services/AnalyticsService.php';
        require_once __DIR__ . '/../../marketing/app/Services/TargetingService.php';
        require_once __DIR__ . '/../../marketing/app/Services/DeliveryService.php';
        require_once __DIR__ . '/../../marketing/app/Services/ReportingService.php';
        
        $analytics = new \App\Services\AnalyticsService();
        $targeting = new \App\Services\TargetingService();
        $delivery = new \App\Services\DeliveryService();
        $reporting = new \App\Services\ReportingService();
        
        $stats = $this->getMarketingStats();
        $recentCampaigns = $this->getRecentCampaigns();
        $topPerformers = $this->getTopPerformingCampaigns();
        $userSegments = $analytics->getSegmentStats();
        
        $data = [
            'stats' => $stats,
            'recent_campaigns' => $recentCampaigns,
            'top_performers' => $topPerformers,
            'user_segments' => $userSegments
        ];
        
        $this->view('marketing/dashboard', $data);
    }

    public function campaigns()
    {
        require_admin();
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $campaigns = $this->getCampaigns($limit, $offset);
        $totalCampaigns = $this->getCampaignCount();
        
        $data = [
            'campaigns' => $campaigns,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalCampaigns / $limit),
                'total_items' => $totalCampaigns
            ]
        ];
        
        $this->view('marketing/campaigns', $data);
    }

    public function createCampaign()
    {
        require_admin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateCampaign();
            return;
        }
        
        $organizers = $this->getOrganizers();
        $agencies = $this->getTravelAgencies();
        $templates = $this->getMarketingTemplates();
        
        $data = [
            'organizers' => $organizers,
            'agencies' => $agencies,
            'templates' => $templates
        ];
        
        $this->view('marketing/create_campaign', $data);
    }

    public function campaignAnalytics()
    {
        require_admin();
        
        $campaignId = (int)($_GET['id'] ?? 0);
        if ($campaignId <= 0) {
            redirect('/marketing/campaigns');
            return;
        }
        
        $campaign = $this->getCampaign($campaignId);
        if (!$campaign) {
            flash_set('error', 'Campaign not found.');
            redirect('/marketing/campaigns');
            return;
        }
        
        require_once __DIR__ . '/../../marketing/app/Services/ReportingService.php';
        $reporting = new \App\Services\ReportingService();
        
        $analytics = $reporting->getCampaignAnalytics($campaignId);
        $conversions = $this->getCampaignConversions($campaignId);
        $deliveryStats = $this->getDeliveryStats($campaignId);
        
        $data = [
            'campaign' => $campaign,
            'analytics' => $analytics,
            'conversions' => $conversions,
            'delivery_stats' => $deliveryStats
        ];
        
        $this->view('marketing/campaign_analytics', $data);
    }

    public function userSegmentation()
    {
        require_admin();
        
        require_once __DIR__ . '/../../marketing/app/Services/AnalyticsService.php';
        $analytics = new \App\Services\AnalyticsService();
        
        $segments = $analytics->getUserSegments();
        $segmentStats = $analytics->getSegmentStats();
        
        $data = [
            'segments' => $segments,
            'segment_stats' => $segmentStats
        ];
        
        $this->view('marketing/user_segmentation', $data);
    }

    public function templates()
    {
        require_admin();
        
        $templates = $this->getMarketingTemplates();
        
        $data = ['templates' => $templates];
        $this->view('marketing/templates', $data);
    }

    public function reports()
    {
        require_admin();
        
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');
        
        require_once __DIR__ . '/../../marketing/app/Services/ReportingService.php';
        $reporting = new \App\Services\ReportingService();
        
        $reports = $reporting->generateReports($dateFrom, $dateTo);
        
        $data = [
            'reports' => $reports,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        $this->view('marketing/reports', $data);
    }

    public function organizerRequests()
    {
        require_admin();
        
        $requests = $this->getOrganizerCampaignRequests();
        
        $data = ['requests' => $requests];
        $this->view('marketing/organizer_requests', $data);
    }

    public function agencyRequests()
    {
        require_admin();
        
        $requests = $this->getAgencyCampaignRequests();
        
        $data = ['requests' => $requests];
        $this->view('marketing/agency_requests', $data);
    }

    // AJAX Methods
    public function audiencePreview()
    {
        require_admin();
        
        header('Content-Type: application/json');
        
        $criteria = $_GET['criteria'] ?? [];
        require_once __DIR__ . '/../../marketing/app/Services/TargetingService.php';
        $targeting = new \App\Services\TargetingService();
        $metrics = $targeting->calculateAudienceMetrics($criteria);
        
        echo json_encode([
            'success' => true,
            'data' => $metrics
        ]);
    }

    public function approveCampaign()
    {
        require_admin();
        
        header('Content-Type: application/json');
        
        $campaignId = (int)($_POST['campaign_id'] ?? 0);
        if ($campaignId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid campaign ID']);
            return;
        }
        
        $db = db();
        $db->prepare('UPDATE marketing_campaigns SET status = ? WHERE id = ?')
           ->execute(['approved', $campaignId]);
        
        echo json_encode(['success' => true, 'message' => 'Campaign approved']);
    }

    public function startCampaign()
    {
        require_admin();
        
        header('Content-Type: application/json');
        
        $campaignId = (int)($_POST['campaign_id'] ?? 0);
        // Optional: selected recipients provided as arrays of emails/phones or user IDs
        $selectedEmails = isset($_POST['emails']) ? (array)$_POST['emails'] : [];
        $selectedPhones = isset($_POST['phones']) ? (array)$_POST['phones'] : [];
        $selectedUserIds = isset($_POST['user_ids']) ? array_map('intval', (array)$_POST['user_ids']) : [];
        if ($campaignId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid campaign ID']);
            return;
        }
        
        // Ensure dependencies are loaded because the main autoloader only maps App\\ to app/
        require_once __DIR__ . '/../../marketing/app/Services/AnalyticsService.php';
        require_once __DIR__ . '/../../marketing/app/Services/TargetingService.php';
        require_once __DIR__ . '/../../marketing/app/Services/DeliveryService.php';
        $delivery = new \App\Services\DeliveryService();

        // If recipients were provided, send directly; otherwise use default audience
        if (!empty($selectedEmails) || !empty($selectedPhones) || !empty($selectedUserIds)) {
            $okAny = false;
            // Phones: ensure we pass campaignId so messages are logged and default content is built
            foreach ($selectedPhones as $p) {
                if ($p !== '') { $okAny = $delivery->sendSms($p, '', $campaignId) || $okAny; }
            }
            // Emails
            foreach ($selectedEmails as $e) {
                if ($e !== '') { $okAny = $delivery->sendEmail($e, 'Marketing Message', '', $campaignId) || $okAny; }
            }
            // For user_ids we can look up contact details
            if (!empty($selectedUserIds)) {
                $in = str_repeat('?,', count($selectedUserIds) - 1) . '?';
                $stmt = db()->prepare("SELECT id, email, phone FROM users WHERE id IN ($in)");
                $stmt->execute($selectedUserIds);
                $rows = $stmt->fetchAll();
                foreach ($rows as $r) {
                    if (!empty($r['phone'])) { $delivery->sendSms($r['phone'], '', $campaignId); $okAny = true; }
                    if (!empty($r['email'])) { $delivery->sendEmail($r['email'], 'Marketing Message', '', $campaignId); $okAny = true; }
                }
            }
            $result = $okAny;
        } else {
            $result = $delivery->startCampaign($campaignId);
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Campaign started']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to start campaign']);
        }
    }

    // Recipients API with filters: audience (all|past|recommend) and q search
    public function campaignRecipientsApi(): void
    {
        require_admin();
        header('Content-Type: application/json');
        try {
            $campaignId = (int)($_GET['id'] ?? 0);
            if ($campaignId <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid campaign id']); return; }
            $row = db()->prepare('SELECT * FROM marketing_campaigns WHERE id = ?');
            $row->execute([$campaignId]);
            $campaign = $row->fetch();
            if (!$campaign) { echo json_encode(['success'=>false,'message'=>'Campaign not found']); return; }
            $limit = max(1, (int)($_GET['limit'] ?? 1000));
            $aud = $_GET['audience'] ?? '';
            $q = trim($_GET['q'] ?? '');

            $users = [];
            $like = '%' . $q . '%';

            if ($aud === 'all') {
                $sql = 'SELECT id, first_name, last_name, email, phone FROM users';
                if ($q !== '') { $sql .= ' WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)'; }
                $sql .= ' ORDER BY created_at DESC LIMIT ' . (int)$limit;
                $stmt = db()->prepare($sql);
                if ($q !== '') { $stmt->execute([$like,$like,$like,$like]); } else { $stmt->execute(); }
                $users = $stmt->fetchAll();
            } elseif ($aud === 'past') {
                if (!empty($campaign['organizer_id'])) {
                    $sql = 'SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, u.phone FROM users u '
                         . 'JOIN orders o ON o.user_id = u.id AND o.status = "paid" '
                         . 'JOIN order_items oi ON oi.order_id = o.id '
                         . 'JOIN events e ON e.id = oi.event_id '
                         . 'WHERE e.organizer_id = ?';
                    if ($q !== '') { $sql .= ' AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)'; }
                    $sql .= ' ORDER BY o.created_at DESC LIMIT ' . (int)$limit;
                    $stmt = db()->prepare($sql);
                    $params = [(int)$campaign['organizer_id']];
                    if ($q !== '') { $params = array_merge($params, [$like,$like,$like,$like]); }
                    $stmt->execute($params);
                    $users = $stmt->fetchAll();
                } elseif (!empty($campaign['travel_agency_id'])) {
                    $sql = 'SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, u.phone FROM users u '
                         . 'JOIN travel_bookings tb ON tb.user_id = u.id '
                         . 'JOIN travel_destinations td ON td.id = tb.destination_id '
                         . 'WHERE td.agency_id = ? AND tb.status = "confirmed"';
                    if ($q !== '') { $sql .= ' AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)'; }
                    $sql .= ' ORDER BY tb.created_at DESC LIMIT ' . (int)$limit;
                    try {
                        $stmt = db()->prepare($sql);
                        $params = [(int)$campaign['travel_agency_id']];
                        if ($q !== '') { $params = array_merge($params, [$like,$like,$like,$like]); }
                        $stmt->execute($params);
                        $users = $stmt->fetchAll();
                    } catch (\Throwable $te) {
                        $stmt = db()->prepare('SELECT id, first_name, last_name, email, phone FROM users ORDER BY created_at DESC LIMIT ' . (int)$limit);
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                    }
                } else {
                    $stmt = db()->prepare('SELECT id, first_name, last_name, email, phone FROM users ORDER BY created_at DESC LIMIT ' . (int)$limit);
                    $stmt->execute();
                    $users = $stmt->fetchAll();
                }
            } else { // recommend
                // Fallback to all if analytics unavailable via API context
                $stmt = db()->prepare('SELECT id, first_name, last_name, email, phone FROM users ORDER BY created_at DESC LIMIT ' . (int)$limit);
                $stmt->execute();
                $users = $stmt->fetchAll();
            }

            $emails = array_values(array_filter(array_map(function($u){ return trim((string)($u['email'] ?? '')); }, $users), function($e){ return $e !== ''; }));
            $phones = array_values(array_filter(array_map(function($u){ return preg_replace('/\D+/', '', (string)($u['phone'] ?? '')); }, $users), function($p){ return $p !== ''; }));

            echo json_encode(['success'=>true,'recipients'=>['users'=>$users,'emails'=>$emails,'phones'=>$phones]]);
        } catch (\Throwable $e) {
            echo json_encode(['success'=>false,'message'=>'Server error: '.$e->getMessage()]);
        }
    }

    // Helper Methods
    private function getMarketingStats()
    {
        $stats = [];
        
        // Total campaigns
        $stats['total_campaigns'] = db()->query("SELECT COUNT(*) FROM marketing_campaigns")->fetchColumn();
        
        // Active campaigns
        $stats['active_campaigns'] = db()->query("SELECT COUNT(*) FROM marketing_campaigns WHERE status IN ('running', 'approved')")->fetchColumn();
        
        // Total deliveries
        $stats['total_deliveries'] = db()->query("SELECT COUNT(*) FROM campaign_deliveries")->fetchColumn();
        
        // Total conversions
        $stats['total_conversions'] = db()->query("SELECT COUNT(*) FROM campaign_conversions")->fetchColumn();
        
        // Revenue this month
        $stats['monthly_revenue'] = db()->query("
            SELECT COALESCE(SUM(amount), 0) FROM marketing_payments 
            WHERE payment_status = 'completed' 
            AND MONTH(paid_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(paid_at) = YEAR(CURRENT_DATE())
        ")->fetchColumn();
        
        return $stats;
    }

    private function getRecentCampaigns()
    {
        return db()->query("
            SELECT mc.*, 
                   o.full_name as organizer_name,
                   ta.company_name as agency_name,
                   COUNT(cd.id) as delivery_count,
                   COUNT(cc.id) as conversion_count
            FROM marketing_campaigns mc
            LEFT JOIN organizers o ON o.id = mc.organizer_id
            LEFT JOIN travel_agencies ta ON ta.id = mc.travel_agency_id
            LEFT JOIN campaign_deliveries cd ON cd.campaign_id = mc.id
            LEFT JOIN campaign_conversions cc ON cc.campaign_id = mc.id
            GROUP BY mc.id
            ORDER BY mc.created_at DESC
            LIMIT 10
        ")->fetchAll();
    }

    private function getTopPerformingCampaigns()
    {
        return db()->query("
            SELECT mc.*,
                   o.full_name as organizer_name,
                   ta.company_name as agency_name,
                   COUNT(cc.id) as conversions,
                   SUM(cc.conversion_value) as total_revenue
            FROM marketing_campaigns mc
            LEFT JOIN organizers o ON o.id = mc.organizer_id
            LEFT JOIN travel_agencies ta ON ta.id = mc.travel_agency_id
            LEFT JOIN campaign_conversions cc ON cc.campaign_id = mc.id
            WHERE mc.status = 'completed'
            GROUP BY mc.id
            HAVING conversions > 0
            ORDER BY conversions DESC, total_revenue DESC
            LIMIT 5
        ")->fetchAll();
    }

    private function getCampaigns($limit, $offset)
    {
        // Convert to integers to prevent SQL injection
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $stmt = db()->prepare("
            SELECT mc.*,
                   o.full_name as organizer_name,
                   ta.company_name as agency_name,
                   COUNT(cd.id) as delivery_count,
                   COUNT(cc.id) as conversion_count
            FROM marketing_campaigns mc
            LEFT JOIN organizers o ON o.id = mc.organizer_id
            LEFT JOIN travel_agencies ta ON ta.id = mc.travel_agency_id
            LEFT JOIN campaign_deliveries cd ON cd.campaign_id = mc.id
            LEFT JOIN campaign_conversions cc ON cc.campaign_id = mc.id
            GROUP BY mc.id
            ORDER BY mc.created_at DESC
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getCampaignCount()
    {
        return db()->query("SELECT COUNT(*) FROM marketing_campaigns")->fetchColumn();
    }

    private function handleCreateCampaign()
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'campaign_type' => $_POST['campaign_type'] ?? '',
            'organizer_id' => (int)($_POST['organizer_id'] ?? 0) ?: null,
            'travel_agency_id' => (int)($_POST['travel_agency_id'] ?? 0) ?: null,
            'message_content' => trim($_POST['message_content'] ?? ''),
            'sms_content' => trim($_POST['sms_content'] ?? ''),
            'email_content' => trim($_POST['email_content'] ?? ''),
            'budget' => (float)($_POST['budget'] ?? 0),
            'target_audience' => json_encode($_POST['target_audience'] ?? []),
            'scheduled_at' => $_POST['scheduled_at'] ?? null
        ];

        // Validation
        if (empty($data['name']) || empty($data['message_content'])) {
            flash_set('error', 'Campaign name and message content are required.');
            redirect('/marketing/campaigns/create');
            return;
        }

        if (!$data['organizer_id'] && !$data['travel_agency_id']) {
            flash_set('error', 'Please select either an organizer or travel agency.');
            redirect('/marketing/campaigns/create');
            return;
        }

        // Create campaign
        $sql = "INSERT INTO marketing_campaigns (
            name, description, campaign_type, organizer_id, travel_agency_id, 
            admin_id, target_audience, message_content, sms_content, email_content,
            budget, scheduled_at, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_approval', NOW())";

        db()->prepare($sql)->execute([
            $data['name'], $data['description'], $data['campaign_type'],
            $data['organizer_id'], $data['travel_agency_id'], 1, // admin_id = 1 for now
            $data['target_audience'], $data['message_content'], $data['sms_content'],
            $data['email_content'], $data['budget'], $data['scheduled_at']
        ]);

        flash_set('success', 'Marketing campaign created successfully. Awaiting approval.');
        redirect('/marketing/campaigns');
    }

    private function getOrganizers()
    {
        return db()->query("SELECT id, full_name, email FROM organizers WHERE is_active = 1 ORDER BY full_name")->fetchAll();
    }

    private function getTravelAgencies()
    {
        return db()->query("SELECT id, company_name, contact_person, email FROM travel_agencies WHERE is_active = 1 ORDER BY company_name")->fetchAll();
    }

    private function getMarketingTemplates()
    {
        return db()->query("SELECT * FROM marketing_templates WHERE is_active = 1 ORDER BY name")->fetchAll();
    }

    private function getCampaign($id)
    {
        $stmt = db()->prepare("
            SELECT mc.*,
                   o.full_name as organizer_name,
                   ta.company_name as agency_name
            FROM marketing_campaigns mc
            LEFT JOIN organizers o ON o.id = mc.organizer_id
            LEFT JOIN travel_agencies ta ON ta.id = mc.travel_agency_id
            WHERE mc.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    private function getCampaignConversions($campaignId)
    {
        $stmt = db()->prepare("
            SELECT cc.*, u.first_name, u.last_name, u.email
            FROM campaign_conversions cc
            JOIN users u ON u.id = cc.user_id
            WHERE cc.campaign_id = ?
            ORDER BY cc.conversion_time DESC
        ");
        $stmt->execute([$campaignId]);
        return $stmt->fetchAll();
    }

    private function getDeliveryStats($campaignId)
    {
        $stmt = db()->prepare("
            SELECT 
                delivery_type,
                delivery_status,
                COUNT(*) as count,
                SUM(delivery_cost) as total_cost
            FROM campaign_deliveries
            WHERE campaign_id = ?
            GROUP BY delivery_type, delivery_status
        ");
        $stmt->execute([$campaignId]);
        return $stmt->fetchAll();
    }

    private function getOrganizerCampaignRequests()
    {
        return db()->query("
            SELECT mc.*, o.full_name as organizer_name
            FROM marketing_campaigns mc
            JOIN organizers o ON o.id = mc.organizer_id
            WHERE mc.status = 'pending_approval' AND mc.organizer_id IS NOT NULL
            ORDER BY mc.created_at DESC
        ")->fetchAll();
    }

    private function getAgencyCampaignRequests()
    {
        return db()->query("
            SELECT mc.*, ta.company_name as agency_name
            FROM marketing_campaigns mc
            JOIN travel_agencies ta ON ta.id = mc.travel_agency_id
            WHERE mc.status = 'pending_approval' AND mc.travel_agency_id IS NOT NULL
            ORDER BY mc.created_at DESC
        ")->fetchAll();
    }

    private function view($template, $data = [])
    {
        extract($data);
        
        // Define base_url function if not exists
        if (!function_exists('base_url')) {
            function base_url($path = '') {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $script = dirname($_SERVER['SCRIPT_NAME']);
                return $protocol . '://' . $host . $script . $path;
            }
        }
        
        include __DIR__ . "/../../marketing/app/Views/{$template}.php";
    }
}

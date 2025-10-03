<?php
namespace App\Controllers;
use App\Services\Sms;

class CommunicationController
{
    public function index(): void
    {
        require_admin();
        
        // Get recent communications
        $stmt = db()->prepare('
            SELECT c.*, "Admin" as created_by_name 
            FROM communications c
            ORDER BY c.created_at DESC
            LIMIT 50
        ');
        $stmt->execute();
        $communications = $stmt->fetchAll();
        
        view('admin/communications/index', compact('communications'));
    }
    
    public function create(): void
    {
        require_admin();
        
        // Get communication templates
        $stmt = db()->prepare('SELECT * FROM communication_templates WHERE is_active = 1 ORDER BY name');
        $stmt->execute();
        $templates = $stmt->fetchAll();
        
        view('admin/communications/create', compact('templates'));
    }
    
    public function store(): void
    {
        require_admin();
        
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $recipientType = $_POST['recipient_type'] ?? '';
        $recipientIds = $_POST['recipient_ids'] ?? [];
        $templateId = $_POST['template_id'] ?? null;
        
        if (empty($title) || empty($message) || empty($recipientType)) {
            flash_set('error', 'Please fill in all required fields.');
            redirect(base_url('/admin/communications/create'));
            return;
        }
        
        // If template is selected, merge with template message
        if ($templateId) {
            $stmt = db()->prepare('SELECT * FROM communication_templates WHERE id = ? AND is_active = 1');
            $stmt->execute([$templateId]);
            $template = $stmt->fetch();
            
            if ($template) {
                $message = $template['message'];
            }
        }
        
        try {
            db()->beginTransaction();
            
            // Create communication record
            $stmt = db()->prepare('
                INSERT INTO communications (title, message, recipient_type, recipient_ids, created_by) 
                VALUES (?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $title,
                $message,
                $recipientType,
                $recipientType === 'custom' ? json_encode($recipientIds) : null,
                $_SESSION['admin_id']
            ]);
            
            $communicationId = db()->lastInsertId();
            
            // Get recipients based on type
            $recipients = $this->getRecipientsForCommunication($recipientType, $recipientIds);
            
            // Create recipient records
            $stmt = db()->prepare('
                INSERT INTO communication_recipients (communication_id, recipient_type, recipient_id, phone_number, name) 
                VALUES (?, ?, ?, ?, ?)
            ');
            
            $totalRecipients = 0;
            foreach ($recipients as $recipient) {
                $stmt->execute([
                    $communicationId,
                    $recipient['type'],
                    $recipient['id'],
                    $recipient['phone'],
                    $recipient['name']
                ]);
                $totalRecipients++;
            }
            
            // Update total recipients count
            db()->prepare('UPDATE communications SET total_recipients = ? WHERE id = ?')
                ->execute([$totalRecipients, $communicationId]);
            
            db()->commit();
            
            flash_set('success', 'Communication created successfully with ' . $totalRecipients . ' recipients.');
            redirect(base_url('/admin/communications/show?id=' . $communicationId));
            
        } catch (\Throwable $e) {
            db()->rollback();
            error_log("Communication creation error: " . $e->getMessage());
            flash_set('error', 'Failed to create communication: ' . $e->getMessage());
            redirect(base_url('/admin/communications/create'));
        }
    }
    
    public function show(): void
    {
        require_admin();
        
        $communicationId = (int)($_GET['id'] ?? 0);
        
        $stmt = db()->prepare('
            SELECT c.*, "Admin" as created_by_name 
            FROM communications c
            WHERE c.id = ?
        ');
        $stmt->execute([$communicationId]);
        $communication = $stmt->fetch();
        
        if (!$communication) {
            flash_set('error', 'Communication not found.');
            redirect(base_url('/admin/communications'));
            return;
        }
        
        // Get recipients
        $stmt = db()->prepare('
            SELECT * FROM communication_recipients 
            WHERE communication_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$communicationId]);
        $recipients = $stmt->fetchAll();
        
        view('admin/communications/show', compact('communication', 'recipients'));
    }
    
    public function send(): void
    {
        require_admin();
        
        $communicationId = (int)($_POST['communication_id'] ?? 0);
        
        $stmt = db()->prepare('SELECT * FROM communications WHERE id = ?');
        $stmt->execute([$communicationId]);
        $communication = $stmt->fetch();
        
        if (!$communication) {
            flash_set('error', 'Communication not found.');
            redirect(base_url('/admin/communications'));
            return;
        }
        
        if ($communication['status'] !== 'draft') {
            flash_set('error', 'Communication has already been sent or is in progress.');
            redirect(base_url('/admin/communications/show?id=' . $communicationId));
            return;
        }
        
        try {
            // Update status to sending
            db()->prepare('UPDATE communications SET status = ?, sent_at = NOW() WHERE id = ?')
                ->execute(['sending', $communicationId]);
            
            // Get pending recipients
            $stmt = db()->prepare('
                SELECT * FROM communication_recipients 
                WHERE communication_id = ? AND status = "pending"
                ORDER BY id
            ');
            $stmt->execute([$communicationId]);
            $recipients = $stmt->fetchAll();
            
            $sentCount = 0;
            $failedCount = 0;
            
            $sms = new Sms();
            
            foreach ($recipients as $recipient) {
                try {
                    // Replace placeholders in message
                    $message = $this->replacePlaceholders($communication['message'], $recipient);
                    
                    // Send SMS
                    $result = $sms->send($recipient['phone_number'], $message);
                    
                    if ($result) {
                        db()->prepare('
                            UPDATE communication_recipients 
                            SET status = "sent", sent_at = NOW() 
                            WHERE id = ?
                        ')->execute([$recipient['id']]);
                        $sentCount++;
                    } else {
                        db()->prepare('
                            UPDATE communication_recipients 
                            SET status = "failed", error_message = "SMS send failed" 
                            WHERE id = ?
                        ')->execute([$recipient['id']]);
                        $failedCount++;
                    }
                    
                    // Small delay to avoid overwhelming the SMS service
                    usleep(100000); // 0.1 second delay
                    
                } catch (\Throwable $e) {
                    db()->prepare('
                        UPDATE communication_recipients 
                        SET status = "failed", error_message = ? 
                        WHERE id = ?
                    ')->execute([$e->getMessage(), $recipient['id']]);
                    $failedCount++;
                }
            }
            
            // Update communication status
            $status = $failedCount > 0 ? 'completed' : 'completed'; // Both are completed, but we track failures
            db()->prepare('
                UPDATE communications 
                SET status = ?, sent_count = ?, failed_count = ? 
                WHERE id = ?
            ')->execute([$status, $sentCount, $failedCount, $communicationId]);
            
            flash_set('success', 'Communication sent successfully. Sent: ' . $sentCount . ', Failed: ' . $failedCount);
            redirect(base_url('/admin/communications/show?id=' . $communicationId));
            
        } catch (\Throwable $e) {
            error_log("Communication sending error: " . $e->getMessage());
            flash_set('error', 'Failed to send communication: ' . $e->getMessage());
            redirect(base_url('/admin/communications/show?id=' . $communicationId));
        }
    }
    
    public function getRecipients(): void
    {
        require_admin();
        
        $recipientType = $_GET['type'] ?? '';
        $search = $_GET['search'] ?? '';
        
        // Debug logging
        error_log("CommunicationController::getRecipients - Type: $recipientType, Search: $search");
        
        $recipients = $this->fetchRecipients($recipientType, $search);
        
        // Debug logging
        error_log("CommunicationController::getRecipients - Found " . count($recipients) . " recipients");
        
        header('Content-Type: application/json');
        echo json_encode($recipients);
    }
    
    public function directSms(): void
    {
        require_admin();
        
        // Handle server-side search fallback
        $searchType = $_GET['search_type'] ?? '';
        $searchTerm = $_GET['search_term'] ?? '';
        $searchResults = [];
        
        if ($searchType && $searchTerm) {
            $searchResults = $this->fetchRecipients($searchType, $searchTerm);
        }
        
        view('admin/communications/direct_sms', [
            'searchResults' => $searchResults,
            'searchType' => $searchType,
            'searchTerm' => $searchTerm
        ]);
    }
    
    public function sendDirectSms(): void
    {
        require_admin();
        
        // Debug logging
        error_log("DirectSms::sendDirectSms - POST data: " . json_encode($_POST));
        
        $phoneNumber = trim($_POST['phone_number'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $recipientType = $_POST['recipient_type'] ?? '';
        $recipientId = $_POST['recipient_id'] ?? '';
        $recipientName = $_POST['recipient_name'] ?? '';
        
        error_log("DirectSms::sendDirectSms - Parsed data - Phone: '$phoneNumber', Message: '$message', Type: '$recipientType', ID: '$recipientId', Name: '$recipientName'");
        
        // Additional debugging
        error_log("DirectSms::sendDirectSms - Session data: " . json_encode($_SESSION));
        
        if (empty($phoneNumber) || empty($message)) {
            error_log("DirectSms::sendDirectSms - Validation failed - Phone: '$phoneNumber', Message: '$message'");
            flash_set('error', 'Phone number and message are required.');
            redirect(base_url('/admin/communications/direct-sms'));
            return;
        }
        
        try {
            // Debug: Check if SMS class exists
            if (!class_exists('App\Services\Sms')) {
                error_log("DirectSms::sendDirectSms - SMS class not found, trying to load manually");
                require_once __DIR__ . '/../Services/Sms.php';
            }
            
            $sms = new Sms();
            
            if (!$sms->isConfigured()) {
                flash_set('error', 'SMS service is not configured.');
                redirect(base_url('/admin/communications/direct-sms'));
                return;
            }
            
            // Send the SMS
            $result = $sms->send($phoneNumber, $message);
            
            if ($result) {
                // Create a simple communication record for tracking
                $stmt = db()->prepare('
                    INSERT INTO communications (title, message, recipient_type, total_recipients, sent_count, failed_count, status, created_by, sent_at) 
                    VALUES (?, ?, ?, 1, 1, 0, "completed", ?, NOW())
                ');
                $stmt->execute([
                    'Direct SMS to ' . $phoneNumber,
                    $message,
                    $recipientType ?: 'direct',
                    $_SESSION['admin_id']
                ]);
                
                $communicationId = db()->lastInsertId();
                
                // Create recipient record
                $stmt = db()->prepare('
                    INSERT INTO communication_recipients (communication_id, recipient_type, recipient_id, phone_number, name, status, sent_at) 
                    VALUES (?, ?, ?, ?, ?, "sent", NOW())
                ');
                $stmt->execute([
                    $communicationId,
                    $recipientType ?: 'direct',
                    $recipientId ?: 0,
                    $phoneNumber,
                    $recipientName ?: 'Direct SMS Recipient'
                ]);
                
                flash_set('success', 'SMS sent successfully to ' . $phoneNumber);
            } else {
                flash_set('error', 'Failed to send SMS. Please try again.');
            }
            
        } catch (\Throwable $e) {
            error_log("Direct SMS error: " . $e->getMessage());
            flash_set('error', 'Error sending SMS: ' . $e->getMessage());
        }
        
        redirect(base_url('/admin/communications/direct-sms'));
    }
    
    private function getRecipientsForCommunication(string $recipientType, array $recipientIds = []): array
    {
        return $this->fetchRecipients($recipientType, '', $recipientIds);
    }
    
    private function fetchRecipients(string $recipientType, string $search = '', array $recipientIds = []): array
    {
        $recipients = [];
        
        switch ($recipientType) {
            case 'users':
                $sql = 'SELECT id, CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, "")) as name, phone FROM users WHERE phone IS NOT NULL AND phone != ""';
                $params = [];
                
                if ($search) {
                    $sql .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
                    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
                }
                
                if (!empty($recipientIds)) {
                    $placeholders = str_repeat('?,', count($recipientIds) - 1) . '?';
                    $sql .= " AND id IN ($placeholders)";
                    $params = array_merge($params, $recipientIds);
                }
                
                $sql .= ' ORDER BY first_name, last_name LIMIT 1000';
                
                $stmt = db()->prepare($sql);
                $stmt->execute($params);
                $users = $stmt->fetchAll();
                
                foreach ($users as $user) {
                    $recipients[] = [
                        'id' => $user['id'],
                        'type' => 'user',
                        'name' => $user['name'],
                        'phone' => $user['phone']
                    ];
                }
                break;
                
            case 'organizers':
                $sql = 'SELECT id, full_name as name, phone FROM organizers WHERE phone IS NOT NULL AND phone != ""';
                $params = [];
                
                if ($search) {
                    $sql .= ' AND (full_name LIKE ? OR phone LIKE ?)';
                    $params = ["%$search%", "%$search%"];
                }
                
                if (!empty($recipientIds)) {
                    $placeholders = str_repeat('?,', count($recipientIds) - 1) . '?';
                    $sql .= " AND id IN ($placeholders)";
                    $params = array_merge($params, $recipientIds);
                }
                
                $sql .= ' ORDER BY full_name LIMIT 1000';
                
                $stmt = db()->prepare($sql);
                $stmt->execute($params);
                $organizers = $stmt->fetchAll();
                
                foreach ($organizers as $organizer) {
                    $recipients[] = [
                        'id' => $organizer['id'],
                        'type' => 'organizer',
                        'name' => $organizer['name'],
                        'phone' => $organizer['phone']
                    ];
                }
                break;
                
            case 'travel_agencies':
                $sql = 'SELECT id, CONCAT(company_name, " (", contact_person, ")") as name, phone FROM travel_agencies WHERE phone IS NOT NULL AND phone != ""';
                $params = [];
                
                if ($search) {
                    $sql .= ' AND (company_name LIKE ? OR contact_person LIKE ? OR phone LIKE ?)';
                    $params = ["%$search%", "%$search%", "%$search%"];
                }
                
                if (!empty($recipientIds)) {
                    $placeholders = str_repeat('?,', count($recipientIds) - 1) . '?';
                    $sql .= " AND id IN ($placeholders)";
                    $params = array_merge($params, $recipientIds);
                }
                
                $sql .= ' ORDER BY company_name LIMIT 1000';
                
                $stmt = db()->prepare($sql);
                $stmt->execute($params);
                $agencies = $stmt->fetchAll();
                
                foreach ($agencies as $agency) {
                    $recipients[] = [
                        'id' => $agency['id'],
                        'type' => 'travel_agency',
                        'name' => $agency['name'],
                        'phone' => $agency['phone']
                    ];
                }
                break;
        }
        
        return $recipients;
    }
    
    private function replacePlaceholders(string $message, array $recipient): string
    {
        $replacements = [
            '{{name}}' => $recipient['name'],
            '{{phone}}' => $recipient['phone_number'],
            '{{recipient_type}}' => ucfirst(str_replace('_', ' ', $recipient['recipient_type']))
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
}

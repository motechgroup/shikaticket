<?php

namespace App\Controllers;

class FeaturedContentController
{
    /**
     * Show featured content management dashboard
     */
    public function index(): void
    {
        require_admin();
        
        // Get featured events
        $featuredEvents = $this->getFeaturedEvents();
        
        // Get featured travel destinations
        $featuredDestinations = $this->getFeaturedDestinations();
        
        // Get pending requests for featured content
        $pendingRequests = $this->getPendingFeaturedRequests();
        
        // Get pending feature requests
        $featureRequests = \App\Controllers\FeatureRequestController::getPendingRequests();
        
        // Get default commission rates from settings
        $eventCommissionRate = $this->getCommissionRate('event_featured_commission');
        $destinationCommissionRate = $this->getCommissionRate('destination_featured_commission');
        
        view('admin/featured-content/index', [
            'featuredEvents' => $featuredEvents,
            'featuredDestinations' => $featuredDestinations,
            'pendingRequests' => $pendingRequests,
            'featureRequests' => $featureRequests,
            'eventCommissionRate' => $eventCommissionRate,
            'destinationCommissionRate' => $destinationCommissionRate
        ]);
    }

    /**
     * Feature an event
     */
    public function featureEvent(): void
    {
        require_admin();
        verify_csrf();
        
        $eventId = (int)($_POST['event_id'] ?? 0);
        $commission = (float)($_POST['commission'] ?? 5.00);
        $notes = trim($_POST['notes'] ?? '');
        
        if ($eventId <= 0) {
            flash_set('error', 'Invalid event ID');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        try {
            // Check if event exists and is published
            $stmt = db()->prepare('SELECT id, title, organizer_id FROM events WHERE id = ? AND is_published = 1');
            $stmt->execute([$eventId]);
            $event = $stmt->fetch();
            
            if (!$event) {
                flash_set('error', 'Event not found or not published');
                redirect(base_url('/admin/featured-content'));
                return;
            }
            
            // Feature the event
            $stmt = db()->prepare('
                UPDATE events 
                SET is_featured = 1, featured_commission = ?, featured_at = NOW(), featured_by = ?
                WHERE id = ?
            ');
            $stmt->execute([$commission, $_SESSION['admin_id'], $eventId]);
            
            // Log the action
            $this->logFeaturedAction('event', $eventId, 'featured', $commission, $notes);
            
            // Send notification to organizer
            $this->sendFeatureApprovalNotification('event', $event);
            
            flash_set('success', 'Event featured successfully with ' . $commission . '% commission');
            
        } catch (\Exception $e) {
            error_log('Error featuring event: ' . $e->getMessage());
            flash_set('error', 'Failed to feature event');
        }
        
        redirect(base_url('/admin/featured-content'));
    }

    /**
     * Unfeature an event
     */
    public function unfeatureEvent(): void
    {
        require_admin();
        verify_csrf();
        
        $eventId = (int)($_POST['event_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        
        if ($eventId <= 0) {
            flash_set('error', 'Invalid event ID');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        try {
            // Unfeature the event
            $stmt = db()->prepare('
                UPDATE events 
                SET is_featured = 0, featured_commission = 0, featured_at = NULL, featured_by = NULL
                WHERE id = ?
            ');
            $stmt->execute([$eventId]);
            
            // Log the action
            $this->logFeaturedAction('event', $eventId, 'unfeatured', 0, $notes);
            
            flash_set('success', 'Event unfeatured successfully');
            
        } catch (\Exception $e) {
            error_log('Error unfeaturing event: ' . $e->getMessage());
            flash_set('error', 'Failed to unfeature event');
        }
        
        redirect(base_url('/admin/featured-content'));
    }

    /**
     * Feature a travel destination
     */
    public function featureDestination(): void
    {
        require_admin();
        verify_csrf();
        
        $destinationId = (int)($_POST['destination_id'] ?? 0);
        $commission = (float)($_POST['commission'] ?? 5.00);
        $notes = trim($_POST['notes'] ?? '');
        
        if ($destinationId <= 0) {
            flash_set('error', 'Invalid destination ID');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        try {
            // Check if destination exists and is published
            $stmt = db()->prepare('SELECT id, title, agency_id FROM travel_destinations WHERE id = ? AND is_published = 1');
            $stmt->execute([$destinationId]);
            $destination = $stmt->fetch();
            
            if (!$destination) {
                flash_set('error', 'Destination not found or not published');
                redirect(base_url('/admin/featured-content'));
                return;
            }
            
            // Feature the destination
            $stmt = db()->prepare('
                UPDATE travel_destinations 
                SET is_featured = 1, featured_commission = ?, featured_at = NOW(), featured_by = ?
                WHERE id = ?
            ');
            $stmt->execute([$commission, $_SESSION['admin_id'], $destinationId]);
            
            // Log the action
            $this->logFeaturedAction('travel_destination', $destinationId, 'featured', $commission, $notes);
            
            // Send notification to travel agency
            $this->sendFeatureApprovalNotification('travel_destination', $destination);
            
            flash_set('success', 'Travel destination featured successfully with ' . $commission . '% commission');
            
        } catch (\Exception $e) {
            error_log('Error featuring destination: ' . $e->getMessage());
            flash_set('error', 'Failed to feature destination');
        }
        
        redirect(base_url('/admin/featured-content'));
    }

    /**
     * Unfeature a travel destination
     */
    public function unfeatureDestination(): void
    {
        require_admin();
        verify_csrf();
        
        $destinationId = (int)($_POST['destination_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        
        if ($destinationId <= 0) {
            flash_set('error', 'Invalid destination ID');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        try {
            // Unfeature the destination
            $stmt = db()->prepare('
                UPDATE travel_destinations 
                SET is_featured = 0, featured_commission = 0, featured_at = NULL, featured_by = NULL
                WHERE id = ?
            ');
            $stmt->execute([$destinationId]);
            
            // Log the action
            $this->logFeaturedAction('travel_destination', $destinationId, 'unfeatured', 0, $notes);
            
            flash_set('success', 'Travel destination unfeatured successfully');
            
        } catch (\Exception $e) {
            error_log('Error unfeaturing destination: ' . $e->getMessage());
            flash_set('error', 'Failed to unfeature destination');
        }
        
        redirect(base_url('/admin/featured-content'));
    }

    /**
     * Get featured events
     */
    private function getFeaturedEvents(): array
    {
        $stmt = db()->prepare('
            SELECT e.*, o.full_name as organizer_name, o.email as organizer_email
            FROM events e
            JOIN organizers o ON e.organizer_id = o.id
            WHERE e.is_featured = 1
            ORDER BY e.featured_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get featured travel destinations
     */
    private function getFeaturedDestinations(): array
    {
        $stmt = db()->prepare('
            SELECT td.*, ta.company_name, ta.email as agency_email
            FROM travel_destinations td
            JOIN travel_agencies ta ON td.agency_id = ta.id
            WHERE td.is_featured = 1
            ORDER BY td.featured_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get pending featured requests (events and destinations that could be featured)
     */
    private function getPendingFeaturedRequests(): array
    {
        // Get events that are published but not featured
        $stmt = db()->prepare('
            SELECT 
                e.id,
                e.title,
                e.event_date,
                e.price,
                e.currency,
                o.full_name as organizer_name,
                "event" as content_type
            FROM events e
            JOIN organizers o ON e.organizer_id = o.id
            WHERE e.is_published = 1 AND e.is_featured = 0
            ORDER BY e.created_at DESC
            LIMIT 20
        ');
        $stmt->execute();
        $events = $stmt->fetchAll();
        
        // Get travel destinations that are published but not featured
        $stmt = db()->prepare('
            SELECT 
                td.id,
                td.title,
                td.price,
                td.currency,
                ta.company_name as organizer_name,
                "travel_destination" as content_type
            FROM travel_destinations td
            JOIN travel_agencies ta ON td.agency_id = ta.id
            WHERE td.is_published = 1 AND td.is_featured = 0
            ORDER BY td.created_at DESC
            LIMIT 20
        ');
        $stmt->execute();
        $destinations = $stmt->fetchAll();
        
        return array_merge($events, $destinations);
    }

    /**
     * Log featured content action
     */
    private function logFeaturedAction(string $contentType, int $contentId, string $action, float $commission, string $notes): void
    {
        $stmt = db()->prepare('
            INSERT INTO featured_content_logs (content_type, content_id, action, admin_id, commission_rate, notes)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$contentType, $contentId, $action, $_SESSION['admin_id'], $commission, $notes ?: null]);
    }

    /**
     * Calculate featured commission for a booking
     */
    public static function calculateFeaturedCommission(string $contentType, int $contentId, float $bookingAmount): float
    {
        $table = $contentType === 'event' ? 'events' : 'travel_destinations';
        $stmt = db()->prepare("SELECT featured_commission FROM {$table} WHERE id = ? AND is_featured = 1");
        $stmt->execute([$contentId]);
        $result = $stmt->fetch();
        
        if ($result && $result['featured_commission'] > 0) {
            return ($bookingAmount * $result['featured_commission']) / 100;
        }
        
        return 0;
    }

    /**
     * Record featured commission for a booking
     */
    public static function recordFeaturedCommission(string $contentType, int $contentId, int $bookingId = null, int $orderId = null, int $agencyId = null, int $organizerId = null, float $bookingAmount, string $currency, float $baseCommission): void
    {
        $featuredCommission = self::calculateFeaturedCommission($contentType, $contentId, $bookingAmount);
        
        if ($featuredCommission > 0) {
            $stmt = db()->prepare('
                INSERT INTO featured_commissions 
                (content_type, content_id, booking_id, order_id, agency_id, organizer_id, base_commission, featured_commission, total_commission, booking_amount, currency)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            
            $totalCommission = $baseCommission + $featuredCommission;
            
            $stmt->execute([
                $contentType,
                $contentId,
                $bookingId,
                $orderId,
                $agencyId,
                $organizerId,
                $baseCommission,
                $featuredCommission,
                $totalCommission,
                $bookingAmount,
                $currency
            ]);
        }
    }

    /**
     * Send feature approval notification to organizer/agency
     */
    private function sendFeatureApprovalNotification(string $contentType, array $content): void
    {
        try {
            if ($contentType === 'event') {
                $this->sendEventFeatureNotification($content);
            } elseif ($contentType === 'travel_destination') {
                $this->sendDestinationFeatureNotification($content);
            }
        } catch (\Exception $e) {
            error_log('Error sending feature approval notification: ' . $e->getMessage());
        }
    }

    /**
     * Send event feature approval notification
     */
    private function sendEventFeatureNotification(array $event): void
    {
        // Get organizer details
        $stmt = db()->prepare('SELECT full_name, email, phone FROM organizers WHERE id = ?');
        $stmt->execute([$event['organizer_id']]);
        $organizer = $stmt->fetch();

        if (!$organizer) {
            return;
        }

        // Get SMS template
        $smsTemplate = \App\Models\Setting::get('sms.feature_approval_event', '');
        if ($smsTemplate) {
            $smsMessage = str_replace([
                '{event_title}',
                '{organizer_name}'
            ], [
                $event['title'],
                $organizer['full_name']
            ], $smsTemplate);

            // Send SMS
            if ($organizer['phone']) {
                \App\Services\SmsService::send($organizer['phone'], $smsMessage);
            }
        }

        // Get email templates
        $emailSubject = \App\Models\Setting::get('email.feature_approval_event_subject', '');
        $emailBody = \App\Models\Setting::get('email.feature_approval_event_body', '');

        if ($emailSubject && $emailBody) {
            $emailSubject = str_replace([
                '{event_title}',
                '{organizer_name}'
            ], [
                $event['title'],
                $organizer['full_name']
            ], $emailSubject);

            $emailBody = str_replace([
                '{event_title}',
                '{organizer_name}',
                '{event_url}'
            ], [
                $event['title'],
                $organizer['full_name'],
                base_url('/event?id=' . $event['id'])
            ], $emailBody);

            // Send email
            if ($organizer['email']) {
                \App\Services\EmailService::send(
                    $organizer['email'],
                    $emailSubject,
                    $emailBody
                );
            }
        }
    }

    /**
     * Send destination feature approval notification
     */
    private function sendDestinationFeatureNotification(array $destination): void
    {
        // Get travel agency details
        $stmt = db()->prepare('SELECT company_name, email, phone FROM travel_agencies WHERE id = ?');
        $stmt->execute([$destination['agency_id']]);
        $agency = $stmt->fetch();

        if (!$agency) {
            return;
        }

        // Get SMS template
        $smsTemplate = \App\Models\Setting::get('sms.feature_approval_destination', '');
        if ($smsTemplate) {
            $smsMessage = str_replace([
                '{destination_title}',
                '{agency_name}'
            ], [
                $destination['title'],
                $agency['company_name']
            ], $smsTemplate);

            // Send SMS
            if ($agency['phone']) {
                \App\Services\SmsService::send($agency['phone'], $smsMessage);
            }
        }

        // Get email templates
        $emailSubject = \App\Models\Setting::get('email.feature_approval_destination_subject', '');
        $emailBody = \App\Models\Setting::get('email.feature_approval_destination_body', '');

        if ($emailSubject && $emailBody) {
            $emailSubject = str_replace([
                '{destination_title}',
                '{agency_name}'
            ], [
                $destination['title'],
                $agency['company_name']
            ], $emailSubject);

            $emailBody = str_replace([
                '{destination_title}',
                '{agency_name}',
                '{destination_url}'
            ], [
                $destination['title'],
                $agency['company_name'],
                base_url('/travel/destination?id=' . $destination['id'])
            ], $emailBody);

            // Send email
            if ($agency['email']) {
                \App\Services\EmailService::send(
                    $agency['email'],
                    $emailSubject,
                    $emailBody
                );
            }
        }
    }

    /**
     * Update commission settings
     */
    public function updateCommissionSettings(): void
    {
        require_admin();
        verify_csrf();
        
        $eventCommission = (float)($_POST['event_commission'] ?? 5.00);
        $destinationCommission = (float)($_POST['destination_commission'] ?? 5.00);
        
        // Validate commission rates
        if ($eventCommission < 0 || $eventCommission > 50) {
            flash_set('error', 'Event commission rate must be between 0% and 50%');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        if ($destinationCommission < 0 || $destinationCommission > 50) {
            flash_set('error', 'Destination commission rate must be between 0% and 50%');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        try {
            // Update or insert commission settings
            $this->setCommissionRate('event_featured_commission', $eventCommission);
            $this->setCommissionRate('destination_featured_commission', $destinationCommission);
            
            flash_set('success', 'Commission settings updated successfully');
            
        } catch (\Exception $e) {
            flash_set('error', 'Failed to update commission settings: ' . $e->getMessage());
        }
        
        redirect(base_url('/admin/featured-content'));
    }

    /**
     * Get commission rate from settings
     */
    private function getCommissionRate(string $key): float
    {
        try {
            $stmt = db()->prepare('SELECT value FROM settings WHERE `key` = ?');
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            
            return $result ? (float)$result['value'] : 5.00; // Default to 5%
        } catch (\Exception $e) {
            return 5.00; // Default to 5% on error
        }
    }

    /**
     * Set commission rate in settings
     */
    private function setCommissionRate(string $key, float $value): void
    {
        try {
            // Check if setting exists
            $stmt = db()->prepare('SELECT `key` FROM settings WHERE `key` = ?');
            $stmt->execute([$key]);
            
            if ($stmt->fetch()) {
                // Update existing setting
                $stmt = db()->prepare('UPDATE settings SET value = ?, updated_at = NOW() WHERE `key` = ?');
                $stmt->execute([$value, $key]);
            } else {
                // Insert new setting
                $stmt = db()->prepare('INSERT INTO settings (`key`, value, updated_at) VALUES (?, ?, NOW())');
                $stmt->execute([$key, $value]);
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to save commission rate: ' . $e->getMessage());
        }
    }
}

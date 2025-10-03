<?php

namespace App\Controllers;

class FeatureRequestController
{
    /**
     * Request to feature an event (for organizers)
     */
    public function requestFeatureEvent(): void
    {
        require_organizer();
        verify_csrf();
        
        $eventId = (int)($_POST['event_id'] ?? 0);
        // Use admin-set commission rate instead of user input
        $commission = $this->getAdminCommissionRate('event_featured_commission');
        
        if ($eventId <= 0) {
            flash_set('error', 'Invalid event ID');
            redirect(base_url('/organizer/events'));
            return;
        }
        
        // Verify the event belongs to the organizer
        $stmt = db()->prepare('SELECT id, title FROM events WHERE id = ? AND organizer_id = ? AND is_published = 1');
        $stmt->execute([$eventId, $_SESSION['organizer_id']]);
        $event = $stmt->fetch();
        
        if (!$event) {
            flash_set('error', 'Event not found or not published');
            redirect(base_url('/organizer/events'));
            return;
        }
        
        // Check if already featured
        $stmt = db()->prepare('SELECT is_featured FROM events WHERE id = ?');
        $stmt->execute([$eventId]);
        $isFeatured = $stmt->fetch()['is_featured'] ?? 0;
        
        if ($isFeatured) {
            flash_set('error', 'This event is already featured');
            redirect(base_url('/organizer/events'));
            return;
        }
        
        // Check if there's already a pending request
        $stmt = db()->prepare('SELECT id FROM feature_requests WHERE content_type = ? AND content_id = ? AND status = ?');
        $stmt->execute(['event', $eventId, 'pending']);
        
        if ($stmt->fetch()) {
            flash_set('error', 'You already have a pending feature request for this event');
            redirect(base_url('/organizer/events'));
            return;
        }
        
        try {
            // Create feature request
            $stmt = db()->prepare('
                INSERT INTO feature_requests (content_type, content_id, requester_type, requester_id, requested_commission, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute(['event', $eventId, 'organizer', $_SESSION['organizer_id'], $commission, 'pending']);
            
            flash_set('success', 'Feature request submitted successfully! Admin will review and approve/reject your request.');
            
        } catch (\Exception $e) {
            error_log('Error creating feature request: ' . $e->getMessage());
            flash_set('error', 'Failed to submit feature request');
        }
        
        redirect(base_url('/organizer/events'));
    }

    /**
     * Request to feature a travel destination (for travel agencies)
     */
    public function requestFeatureDestination(): void
    {
        require_travel_agency();
        verify_csrf();
        
        $destinationId = (int)($_POST['destination_id'] ?? 0);
        // Use admin-set commission rate instead of user input
        $commission = $this->getAdminCommissionRate('destination_featured_commission');
        
        if ($destinationId <= 0) {
            flash_set('error', 'Invalid destination ID');
            redirect(base_url('/travel/destinations'));
            return;
        }
        
        // Verify the destination belongs to the travel agency
        $stmt = db()->prepare('SELECT id, title FROM travel_destinations WHERE id = ? AND agency_id = ? AND is_published = 1');
        $stmt->execute([$destinationId, $_SESSION['travel_agency_id']]);
        $destination = $stmt->fetch();
        
        if (!$destination) {
            flash_set('error', 'Destination not found or not published');
            redirect(base_url('/travel/destinations'));
            return;
        }
        
        // Check if already featured
        $stmt = db()->prepare('SELECT is_featured FROM travel_destinations WHERE id = ?');
        $stmt->execute([$destinationId]);
        $isFeatured = $stmt->fetch()['is_featured'] ?? 0;
        
        if ($isFeatured) {
            flash_set('error', 'This destination is already featured');
            redirect(base_url('/travel/destinations'));
            return;
        }
        
        // Check if there's already a pending request
        $stmt = db()->prepare('SELECT id FROM feature_requests WHERE content_type = ? AND content_id = ? AND status = ?');
        $stmt->execute(['travel_destination', $destinationId, 'pending']);
        
        if ($stmt->fetch()) {
            flash_set('error', 'You already have a pending feature request for this destination');
            redirect(base_url('/travel/destinations'));
            return;
        }
        
        try {
            // Create feature request
            $stmt = db()->prepare('
                INSERT INTO feature_requests (content_type, content_id, requester_type, requester_id, requested_commission, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute(['travel_destination', $destinationId, 'travel_agency', $_SESSION['travel_agency_id'], $commission, 'pending']);
            
            flash_set('success', 'Feature request submitted successfully! Admin will review and approve/reject your request.');
            
        } catch (\Exception $e) {
            error_log('Error creating feature request: ' . $e->getMessage());
            flash_set('error', 'Failed to submit feature request');
        }
        
        redirect(base_url('/travel/destinations'));
    }

    /**
     * Get feature requests for admin review
     */
    public static function getPendingRequests(): array
    {
        $stmt = db()->prepare('
            SELECT 
                fr.*,
                CASE 
                    WHEN fr.content_type = "event" THEN e.title
                    WHEN fr.content_type = "travel_destination" THEN td.title
                END as content_title,
                CASE 
                    WHEN fr.requester_type = "organizer" THEN o.full_name
                    WHEN fr.requester_type = "travel_agency" THEN ta.company_name
                END as requester_name,
                CASE 
                    WHEN fr.requester_type = "organizer" THEN o.email
                    WHEN fr.requester_type = "travel_agency" THEN ta.email
                END as requester_email
            FROM feature_requests fr
            LEFT JOIN events e ON fr.content_type = "event" AND fr.content_id = e.id
            LEFT JOIN travel_destinations td ON fr.content_type = "travel_destination" AND fr.content_id = td.id
            LEFT JOIN organizers o ON fr.requester_type = "organizer" AND fr.requester_id = o.id
            LEFT JOIN travel_agencies ta ON fr.requester_type = "travel_agency" AND fr.requester_id = ta.id
            WHERE fr.status = "pending"
            ORDER BY fr.created_at ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Approve a feature request (admin only)
     */
    public function approveRequest(): void
    {
        require_admin();
        verify_csrf();
        
        $requestId = (int)($_POST['request_id'] ?? 0);
        $commission = (float)($_POST['commission'] ?? 5.00);
        $notes = trim($_POST['notes'] ?? '');
        
        if ($requestId <= 0) {
            flash_set('error', 'Invalid request ID');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        try {
            // Get the request details
            $stmt = db()->prepare('SELECT * FROM feature_requests WHERE id = ? AND status = ?');
            $stmt->execute([$requestId, 'pending']);
            $request = $stmt->fetch();
            
            if (!$request) {
                flash_set('error', 'Request not found or already processed');
                redirect(base_url('/admin/featured-content'));
                return;
            }
            
            // Update the content to be featured
            if ($request['content_type'] === 'event') {
                $stmt = db()->prepare('
                    UPDATE events 
                    SET is_featured = 1, featured_commission = ?, featured_at = NOW(), featured_by = ?
                    WHERE id = ?
                ');
                $stmt->execute([$commission, $_SESSION['admin_id'], $request['content_id']]);
            } else {
                $stmt = db()->prepare('
                    UPDATE travel_destinations 
                    SET is_featured = 1, featured_commission = ?, featured_at = NOW(), featured_by = ?
                    WHERE id = ?
                ');
                $stmt->execute([$commission, $_SESSION['admin_id'], $request['content_id']]);
            }
            
            // Update the request status
            $stmt = db()->prepare('
                UPDATE feature_requests 
                SET status = ?, admin_id = ?, admin_notes = ?, processed_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute(['approved', $_SESSION['admin_id'], $notes ?: null, $requestId]);
            
            // Send notification to the requester
            $this->sendFeatureApprovalNotification($request);
            
            flash_set('success', 'Feature request approved successfully');
            
        } catch (\Exception $e) {
            error_log('Error approving feature request: ' . $e->getMessage());
            flash_set('error', 'Failed to approve feature request');
        }
        
        redirect(base_url('/admin/featured-content'));
    }

    /**
     * Reject a feature request (admin only)
     */
    public function rejectRequest(): void
    {
        require_admin();
        verify_csrf();
        
        $requestId = (int)($_POST['request_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        
        if ($requestId <= 0) {
            flash_set('error', 'Invalid request ID');
            redirect(base_url('/admin/featured-content'));
            return;
        }
        
        try {
            // Update the request status
            $stmt = db()->prepare('
                UPDATE feature_requests 
                SET status = ?, admin_id = ?, admin_notes = ?, processed_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute(['rejected', $_SESSION['admin_id'], $notes ?: null, $requestId]);
            
            flash_set('success', 'Feature request rejected');
            
        } catch (\Exception $e) {
            error_log('Error rejecting feature request: ' . $e->getMessage());
            flash_set('error', 'Failed to reject feature request');
        }
        
        redirect(base_url('/admin/featured-content'));
    }

    /**
     * Send feature approval notification to the requester
     */
    private function sendFeatureApprovalNotification(array $request): void
    {
        try {
            if ($request['content_type'] === 'event') {
                $this->sendEventFeatureApprovalNotification($request);
            } elseif ($request['content_type'] === 'travel_destination') {
                $this->sendDestinationFeatureApprovalNotification($request);
            }
        } catch (\Exception $e) {
            error_log('Error sending feature approval notification: ' . $e->getMessage());
        }
    }

    /**
     * Send event feature approval notification
     */
    private function sendEventFeatureApprovalNotification(array $request): void
    {
        // Get event details
        $stmt = db()->prepare('SELECT id, title FROM events WHERE id = ?');
        $stmt->execute([$request['content_id']]);
        $event = $stmt->fetch();

        if (!$event) {
            return;
        }

        // Get organizer details
        $stmt = db()->prepare('SELECT full_name, email, phone FROM organizers WHERE id = ?');
        $stmt->execute([$request['requester_id']]);
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
    private function sendDestinationFeatureApprovalNotification(array $request): void
    {
        // Get destination details
        $stmt = db()->prepare('SELECT id, title FROM travel_destinations WHERE id = ?');
        $stmt->execute([$request['content_id']]);
        $destination = $stmt->fetch();

        if (!$destination) {
            return;
        }

        // Get travel agency details
        $stmt = db()->prepare('SELECT company_name, email, phone FROM travel_agencies WHERE id = ?');
        $stmt->execute([$request['requester_id']]);
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
     * Get admin-set commission rate from settings
     */
    private function getAdminCommissionRate(string $key): float
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
}

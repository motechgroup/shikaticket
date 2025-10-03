<?php

namespace App\Controllers;

class NotificationTemplateController
{
    /**
     * Show notification templates management page
     */
    public function index(): void
    {
        require_admin();
        
        // Get all notification templates
        $templates = [
            'sms' => [
                'feature_approval_event' => \App\Models\Setting::get('sms.feature_approval_event', ''),
                'feature_approval_destination' => \App\Models\Setting::get('sms.feature_approval_destination', ''),
                'password_reset_user' => \App\Models\Setting::get('sms.password_reset_user', ''),
                'password_reset_organizer' => \App\Models\Setting::get('sms.password_reset_organizer', ''),
                'password_reset_travel' => \App\Models\Setting::get('sms.password_reset_travel', ''),
            ],
            'email' => [
                'feature_approval_event_subject' => \App\Models\Setting::get('email.feature_approval_event_subject', ''),
                'feature_approval_event_body' => \App\Models\Setting::get('email.feature_approval_event_body', ''),
                'feature_approval_destination_subject' => \App\Models\Setting::get('email.feature_approval_destination_subject', ''),
                'feature_approval_destination_body' => \App\Models\Setting::get('email.feature_approval_destination_body', ''),
                'password_reset_user_subject' => \App\Models\Setting::get('email.password_reset_user_subject', ''),
                'password_reset_user_body' => \App\Models\Setting::get('email.password_reset_user_body', ''),
                'password_reset_organizer_subject' => \App\Models\Setting::get('email.password_reset_organizer_subject', ''),
                'password_reset_organizer_body' => \App\Models\Setting::get('email.password_reset_organizer_body', ''),
                'password_reset_travel_subject' => \App\Models\Setting::get('email.password_reset_travel_subject', ''),
                'password_reset_travel_body' => \App\Models\Setting::get('email.password_reset_travel_body', ''),
            ]
        ];
        
        view('admin/notification-templates/index', ['templates' => $templates]);
    }

    /**
     * Update notification templates
     */
    public function update(): void
    {
        require_admin();
        verify_csrf();
        
        $templates = $_POST['templates'] ?? [];
        
        try {
            foreach ($templates as $type => $templateData) {
                foreach ($templateData as $key => $value) {
                    if ($type === 'sms') {
                        $settingKey = 'sms.' . $key;
                    } elseif ($type === 'email') {
                        $settingKey = 'email.' . $key;
                    } else {
                        continue;
                    }
                    
                    // Update the setting
                    $stmt = db()->prepare('UPDATE settings SET value = ?, updated_at = NOW() WHERE `key` = ?');
                    $stmt->execute([$value, $settingKey]);
                }
            }
            
            flash_set('success', 'Notification templates updated successfully');
            
        } catch (\Exception $e) {
            error_log('Error updating notification templates: ' . $e->getMessage());
            flash_set('error', 'Failed to update notification templates');
        }
        
        redirect(base_url('/admin/notification-templates'));
    }

    /**
     * Preview a template with sample data
     */
    public function preview(): void
    {
        require_admin();
        
        $templateKey = $_GET['key'] ?? '';
        $templateType = $_GET['type'] ?? '';
        
        if (!$templateKey || !$templateType) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing template key or type']);
            return;
        }
        
        $settingKey = $templateType . '.' . $templateKey;
        $template = \App\Models\Setting::get($settingKey, '');
        
        if (!$template) {
            http_response_code(404);
            echo json_encode(['error' => 'Template not found']);
            return;
        }
        
        // Replace placeholders with sample data
        $sampleData = $this->getSampleData($templateKey);
        $preview = str_replace(array_keys($sampleData), array_values($sampleData), $template);
        
        echo json_encode(['preview' => $preview]);
    }

    /**
     * Get sample data for template preview
     */
    private function getSampleData(string $templateKey): array
    {
        $sampleData = [
            // Feature approval templates
            'event_title' => 'Sample Music Concert',
            'organizer_name' => 'John Doe',
            'destination_title' => 'Amazing Safari Tour',
            'agency_name' => 'Wildlife Adventures Ltd',
            'event_url' => base_url('/event?id=1'),
            'destination_url' => base_url('/travel/destination?id=1'),
            
            // Password reset templates
            'reset_code' => '123456',
            'reset_url' => base_url('/password-reset/verify?token=sample_token'),
            'user_name' => 'Jane Smith',
            'organizer_company' => 'Event Masters',
            'agency_company' => 'Travel Pro',
        ];
        
        return $sampleData;
    }
}

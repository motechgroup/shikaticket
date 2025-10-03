-- Create communications table for storing bulk SMS campaigns
CREATE TABLE IF NOT EXISTS communications (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    recipient_type ENUM('users', 'organizers', 'travel_agencies', 'custom') NOT NULL,
    recipient_ids JSON DEFAULT NULL, -- Store specific IDs for custom selections
    total_recipients INT(10) UNSIGNED DEFAULT 0,
    sent_count INT(10) UNSIGNED DEFAULT 0,
    failed_count INT(10) UNSIGNED DEFAULT 0,
    status ENUM('draft', 'sending', 'completed', 'failed') DEFAULT 'draft',
    scheduled_at TIMESTAMP NULL DEFAULT NULL,
    sent_at TIMESTAMP NULL DEFAULT NULL,
    created_by INT(10) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_recipient_type (recipient_type),
    INDEX idx_created_by (created_by),
    INDEX idx_scheduled_at (scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create communication_recipients table for tracking individual recipients
CREATE TABLE IF NOT EXISTS communication_recipients (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    communication_id INT(10) UNSIGNED NOT NULL,
    recipient_type ENUM('user', 'organizer', 'travel_agency') NOT NULL,
    recipient_id INT(10) UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    name VARCHAR(255) NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    error_message TEXT DEFAULT NULL,
    sent_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_communication_id (communication_id),
    INDEX idx_recipient_type (recipient_type),
    INDEX idx_status (status),
    INDEX idx_phone_number (phone_number),
    
    FOREIGN KEY (communication_id) REFERENCES communications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create communication_templates table for reusable message templates
CREATE TABLE IF NOT EXISTS communication_templates (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    message TEXT NOT NULL,
    recipient_type ENUM('users', 'organizers', 'travel_agencies', 'all') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT(10) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_active (is_active),
    INDEX idx_recipient_type (recipient_type),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some default communication templates
INSERT INTO communication_templates (name, description, message, recipient_type, created_by) VALUES
('Event Reminder', 'Reminder for upcoming events', 'Hi {{name}}, don''t forget about the upcoming event {{event_name}} on {{event_date}}. See you there!', 'users', 1),
('Payment Reminder', 'Reminder for pending payments', 'Hi {{name}}, your payment for {{order_id}} is still pending. Please complete payment to confirm your booking.', 'users', 1),
('System Maintenance', 'System maintenance notification', 'Dear {{name}}, we will be performing system maintenance on {{maintenance_date}} from {{start_time}} to {{end_time}}. The system may be temporarily unavailable.', 'all', 1),
('Welcome Message', 'Welcome message for new users', 'Welcome to Ticko, {{name}}! Thank you for joining us. We''re excited to have you on board.', 'users', 1),
('Organizer Update', 'Important update for organizers', 'Dear {{name}}, there''s an important update for organizers. Please check your dashboard for more details.', 'organizers', 1),
('Travel Agency News', 'News for travel agencies', 'Hello {{name}}, we have exciting news for travel agencies. Check your dashboard for the latest updates.', 'travel_agencies', 1);

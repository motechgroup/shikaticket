-- Create feature requests table for organizers and travel agencies
CREATE TABLE IF NOT EXISTS feature_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content_type ENUM('event', 'travel_destination') NOT NULL,
    content_id INT UNSIGNED NOT NULL,
    requester_type ENUM('organizer', 'travel_agency') NOT NULL,
    requester_id INT UNSIGNED NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_commission DECIMAL(5,2) DEFAULT 5.00,
    admin_notes TEXT NULL,
    admin_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    INDEX idx_content_type_id (content_type, content_id),
    INDEX idx_requester_type_id (requester_type, requester_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

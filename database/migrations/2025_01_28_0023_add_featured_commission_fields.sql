-- Add commission tracking fields to existing featured columns
ALTER TABLE events ADD COLUMN featured_commission DECIMAL(5,2) DEFAULT 5.00 AFTER is_featured;
ALTER TABLE events ADD COLUMN featured_at TIMESTAMP NULL AFTER featured_commission;
ALTER TABLE events ADD COLUMN featured_by INT UNSIGNED NULL AFTER featured_at;

ALTER TABLE travel_destinations ADD COLUMN featured_commission DECIMAL(5,2) DEFAULT 5.00 AFTER is_featured;
ALTER TABLE travel_destinations ADD COLUMN featured_at TIMESTAMP NULL AFTER featured_commission;
ALTER TABLE travel_destinations ADD COLUMN featured_by INT UNSIGNED NULL AFTER featured_at;

-- Add indexes for better performance on featured content queries
CREATE INDEX idx_events_featured ON events(is_featured, event_date);
CREATE INDEX idx_travel_destinations_featured ON travel_destinations(is_featured, created_at);

-- Create featured content log table for audit trail
CREATE TABLE IF NOT EXISTS featured_content_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content_type ENUM('event', 'travel_destination') NOT NULL,
    content_id INT UNSIGNED NOT NULL,
    action ENUM('featured', 'unfeatured') NOT NULL,
    admin_id INT UNSIGNED NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_content_type_id (content_type, content_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- Create commission tracking table for featured content
CREATE TABLE IF NOT EXISTS featured_commissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content_type ENUM('event', 'travel_destination') NOT NULL,
    content_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED NULL,
    order_id INT UNSIGNED NULL,
    agency_id INT UNSIGNED NULL,
    organizer_id INT UNSIGNED NULL,
    base_commission DECIMAL(10,2) NOT NULL,
    featured_commission DECIMAL(10,2) NOT NULL,
    total_commission DECIMAL(10,2) NOT NULL,
    booking_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,
    INDEX idx_content_type_id (content_type, content_id),
    INDEX idx_booking_id (booking_id),
    INDEX idx_order_id (order_id),
    INDEX idx_agency_id (agency_id),
    INDEX idx_organizer_id (organizer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

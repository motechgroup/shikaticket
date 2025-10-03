-- Create marketing_campaign_requests table
CREATE TABLE IF NOT EXISTS marketing_campaign_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_type ENUM('organizer', 'travel_agency') NOT NULL,
    account_id INT NOT NULL,
    campaign_name VARCHAR(255) NOT NULL,
    event_id INT NULL,
    destination_id INT NULL,
    target_audience VARCHAR(100) NOT NULL,
    message_content TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    budget DECIMAL(10, 2) NOT NULL,
    tier VARCHAR(50) NOT NULL,
    notes TEXT NULL,
    payment_method ENUM('mpesa', 'flutterwave', 'paypal') NULL,
    calculated_cost DECIMAL(10, 2) NULL,
    estimated_messages INT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    payment_reference VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected', 'in_progress', 'completed') DEFAULT 'pending',
    admin_notes TEXT NULL,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_account (account_type, account_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Create marketing_pricing_settings table for admin price management
CREATE TABLE IF NOT EXISTS marketing_pricing_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tier_name VARCHAR(100) NOT NULL UNIQUE,
    account_type ENUM('organizer', 'travel_agency') NOT NULL,
    price_per_sms DECIMAL(8, 2) NOT NULL,
    max_messages INT NULL,
    features JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_account_type (account_type),
    INDEX idx_is_active (is_active)
);

-- Insert default pricing settings
INSERT INTO marketing_pricing_settings (tier_name, account_type, price_per_sms, max_messages, features, is_active) VALUES
-- Organizer pricing
('Basic Campaign', 'organizer', 2.00, 1000, '["Up to 1,000 SMS messages", "Basic audience targeting", "Standard templates", "Basic analytics"]', TRUE),
('Professional Campaign', 'organizer', 1.50, 10000, '["Up to 10,000 SMS messages", "Advanced audience targeting", "Premium templates", "Conversion tracking", "A/B testing"]', TRUE),
('Enterprise Campaign', 'organizer', 1.00, NULL, '["Unlimited SMS messages", "AI-powered audience targeting", "Custom template design", "Real-time analytics dashboard", "Priority support"]', TRUE),

-- Travel agency pricing
('Explorer Package', 'travel_agency', 2.50, 1000, '["Up to 1,000 SMS messages", "Basic destination targeting", "Travel destination templates", "Basic booking analytics"]', TRUE),
('Adventure Package', 'travel_agency', 2.00, 10000, '["Up to 10,000 SMS messages", "Advanced destination targeting", "Premium travel templates", "Booking conversion tracking", "Seasonal campaign tools"]', TRUE),
('Elite Package', 'travel_agency', 1.50, NULL, '["Unlimited SMS messages", "AI-powered destination targeting", "Custom travel template design", "Real-time booking analytics", "Multi-destination campaigns", "Priority support"]', TRUE);

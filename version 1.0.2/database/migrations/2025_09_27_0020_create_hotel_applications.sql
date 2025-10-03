-- Create hotel_applications table
CREATE TABLE IF NOT EXISTS hotel_applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    location VARCHAR(255) NOT NULL,
    rooms INT UNSIGNED NOT NULL,
    website VARCHAR(500) DEFAULT NULL,
    experience TEXT DEFAULT NULL,
    why_interested TEXT DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected', 'contacted') DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create password reset tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) NOT NULL UNIQUE,
    user_type ENUM('user', 'organizer', 'travel_agency') NOT NULL,
    user_id INT NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_user_type_id (user_type, user_id),
    INDEX idx_expires_at (expires_at)
);

-- Add indexes for better performance
CREATE INDEX idx_password_reset_email ON password_reset_tokens(email);
CREATE INDEX idx_password_reset_phone ON password_reset_tokens(phone);

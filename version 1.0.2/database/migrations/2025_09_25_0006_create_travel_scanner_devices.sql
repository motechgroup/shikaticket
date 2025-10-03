CREATE TABLE IF NOT EXISTS travel_scanner_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    travel_agency_id INT NOT NULL,
    device_name VARCHAR(255) NOT NULL,
    device_code VARCHAR(50) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (travel_agency_id) REFERENCES travel_agencies(id) ON DELETE CASCADE,
    INDEX idx_travel_agency_id (travel_agency_id),
    INDEX idx_device_code (device_code)
);

CREATE TABLE IF NOT EXISTS travel_booking_scans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    scanner_device_id INT NOT NULL,
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    scan_status ENUM('valid', 'invalid', 'already_used') DEFAULT 'valid',
    notes TEXT,
    
    FOREIGN KEY (booking_id) REFERENCES travel_bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (scanner_device_id) REFERENCES travel_scanner_devices(id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id),
    INDEX idx_scanner_device_id (scanner_device_id),
    INDEX idx_scanned_at (scanned_at)
);

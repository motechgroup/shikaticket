CREATE TABLE IF NOT EXISTS travel_tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    ticket_code VARCHAR(10) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'valid', -- valid, used, cancelled
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_travel_ticket_booking FOREIGN KEY (booking_id) REFERENCES travel_bookings(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ticket_code (ticket_code)
) ENGINE=InnoDB;

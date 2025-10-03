-- Create travel_agencies table
CREATE TABLE IF NOT EXISTS travel_agencies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(191) NOT NULL,
    contact_person VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    website VARCHAR(255),
    logo_path VARCHAR(255),
    description TEXT,
    commission_rate DECIMAL(5,2) DEFAULT 10.00, -- Admin sets commission %
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    phone_verified TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create travel_destinations table
CREATE TABLE IF NOT EXISTS travel_destinations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agency_id INT UNSIGNED NOT NULL,
    title VARCHAR(191) NOT NULL,
    destination VARCHAR(191) NOT NULL,
    description TEXT,
    duration_days INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'KES',
    max_participants INT DEFAULT 50,
    min_participants INT DEFAULT 1,
    departure_location VARCHAR(191) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE,
    image_path VARCHAR(255),
    gallery_paths TEXT, -- JSON array of image paths
    includes TEXT, -- What's included (JSON array)
    excludes TEXT, -- What's excluded (JSON array)
    itinerary TEXT, -- Day-by-day itinerary (JSON array)
    requirements TEXT, -- Requirements/notes (JSON array)
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    booking_deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_travel_destination_agency FOREIGN KEY (agency_id) REFERENCES travel_agencies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create travel_bookings table
CREATE TABLE IF NOT EXISTS travel_bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    destination_id INT UNSIGNED NOT NULL,
    booking_reference VARCHAR(20) NOT NULL UNIQUE,
    participants_count INT NOT NULL DEFAULT 1,
    participant_details TEXT, -- JSON array of participant info
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    special_requests TEXT,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    travel_date DATE NOT NULL,
    CONSTRAINT fk_travel_booking_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_travel_booking_destination FOREIGN KEY (destination_id) REFERENCES travel_destinations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create travel_payments table
CREATE TABLE IF NOT EXISTS travel_payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    agency_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    agency_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_reference VARCHAR(255),
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_travel_payment_booking FOREIGN KEY (booking_id) REFERENCES travel_bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_travel_payment_agency FOREIGN KEY (agency_id) REFERENCES travel_agencies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create travel_agency_tokens table for authentication
CREATE TABLE IF NOT EXISTS travel_agency_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agency_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_travel_agency_token FOREIGN KEY (agency_id) REFERENCES travel_agencies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

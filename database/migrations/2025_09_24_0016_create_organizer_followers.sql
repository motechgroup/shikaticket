CREATE TABLE IF NOT EXISTS organizer_followers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  organizer_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_follow (organizer_id, user_id),
  INDEX idx_follow_org (organizer_id),
  INDEX idx_follow_user (user_id),
  CONSTRAINT fk_follow_org FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE,
  CONSTRAINT fk_follow_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Travel agency followers
CREATE TABLE IF NOT EXISTS travel_agency_followers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agency_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_travel_follow (agency_id, user_id),
    CONSTRAINT fk_travel_follow_agency FOREIGN KEY (agency_id) REFERENCES travel_agencies(id) ON DELETE CASCADE,
    CONSTRAINT fk_travel_follow_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Travel agency ratings
CREATE TABLE IF NOT EXISTS travel_agency_ratings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agency_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_travel_rate (agency_id, user_id),
    CONSTRAINT fk_travel_rate_agency FOREIGN KEY (agency_id) REFERENCES travel_agencies(id) ON DELETE CASCADE,
    CONSTRAINT fk_travel_rate_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- Create travel scanner assignments table
CREATE TABLE IF NOT EXISTS `travel_scanner_assignments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scanner_device_id` int(10) unsigned NOT NULL,
  `destination_id` int(10) unsigned NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_assignment` (`scanner_device_id`, `destination_id`),
  KEY `idx_scanner_device` (`scanner_device_id`),
  KEY `idx_destination` (`destination_id`),
  KEY `idx_assigned_at` (`assigned_at`),
  CONSTRAINT `fk_travel_scanner_assignments_scanner` 
    FOREIGN KEY (`scanner_device_id`) 
    REFERENCES `travel_scanner_devices` (`id`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_travel_scanner_assignments_destination` 
    FOREIGN KEY (`destination_id`) 
    REFERENCES `travel_destinations` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create scanner_devices table for organizer-created scanner devices
CREATE TABLE IF NOT EXISTS scanner_devices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  organizer_id INT UNSIGNED NOT NULL,
  device_name VARCHAR(191) NOT NULL,
  device_code VARCHAR(32) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_scanner_devices_organizer FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create scanner_assignments table to track which scanners are assigned to which events
CREATE TABLE IF NOT EXISTS scanner_assignments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  scanner_device_id INT UNSIGNED NOT NULL,
  assigned_by INT UNSIGNED NOT NULL, -- admin user who assigned
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_scanner_assignments_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  CONSTRAINT fk_scanner_assignments_scanner FOREIGN KEY (scanner_device_id) REFERENCES scanner_devices(id) ON DELETE CASCADE,
  CONSTRAINT fk_scanner_assignments_admin FOREIGN KEY (assigned_by) REFERENCES organizers(id) ON DELETE CASCADE,
  UNIQUE KEY unique_event_scanner (event_id, scanner_device_id)
) ENGINE=InnoDB;

-- Add scanner_device_id to tickets table to track which device scanned each ticket
ALTER TABLE tickets ADD COLUMN scanner_device_id INT UNSIGNED NULL AFTER redeemed_by;
ALTER TABLE tickets ADD CONSTRAINT fk_tickets_scanner_device FOREIGN KEY (scanner_device_id) REFERENCES scanner_devices(id) ON DELETE SET NULL;

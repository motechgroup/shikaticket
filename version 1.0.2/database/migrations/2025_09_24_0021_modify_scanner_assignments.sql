-- Modify scanner assignments to be organizer-based instead of event-based
-- First, let's rename the table to be clearer
DROP TABLE IF EXISTS scanner_assignments;

-- Create new organizer_scanner_assignments table
CREATE TABLE IF NOT EXISTS organizer_scanner_assignments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  organizer_id INT UNSIGNED NOT NULL,
  scanner_device_id INT UNSIGNED NOT NULL,
  assigned_by INT UNSIGNED NOT NULL, -- admin user who assigned
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_organizer_scanner_assignments_organizer FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE,
  CONSTRAINT fk_organizer_scanner_assignments_scanner FOREIGN KEY (scanner_device_id) REFERENCES scanner_devices(id) ON DELETE CASCADE,
  CONSTRAINT fk_organizer_scanner_assignments_admin FOREIGN KEY (assigned_by) REFERENCES organizers(id) ON DELETE CASCADE,
  UNIQUE KEY unique_organizer_scanner (organizer_id, scanner_device_id)
) ENGINE=InnoDB;

-- Create event_scanner_assignments table for organizer-to-event assignments
CREATE TABLE IF NOT EXISTS event_scanner_assignments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  scanner_device_id INT UNSIGNED NOT NULL,
  organizer_id INT UNSIGNED NOT NULL, -- organizer who made the assignment
  assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_event_scanner_assignments_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  CONSTRAINT fk_event_scanner_assignments_scanner FOREIGN KEY (scanner_device_id) REFERENCES scanner_devices(id) ON DELETE CASCADE,
  CONSTRAINT fk_event_scanner_assignments_organizer FOREIGN KEY (organizer_id) REFERENCES organizers(id) ON DELETE CASCADE,
  UNIQUE KEY unique_event_scanner (event_id, scanner_device_id)
) ENGINE=InnoDB;

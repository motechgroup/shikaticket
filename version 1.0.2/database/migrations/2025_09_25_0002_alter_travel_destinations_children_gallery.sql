ALTER TABLE travel_destinations
  ADD COLUMN children_allowed TINYINT(1) NOT NULL DEFAULT 1 AFTER booking_deadline,
  ADD COLUMN gallery_paths TEXT NULL AFTER image_path;



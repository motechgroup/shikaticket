ALTER TABLE organizers
  ADD COLUMN commission_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER password_hash;



ALTER TABLE organizers
  ADD COLUMN avatar_path VARCHAR(255) NULL AFTER commission_percent;

ALTER TABLE organizer_tokens
  ADD COLUMN meta TEXT NULL AFTER type;



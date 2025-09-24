ALTER TABLE withdrawals
  ADD COLUMN event_id INT UNSIGNED NULL AFTER organizer_id,
  ADD INDEX idx_withdrawals_event_id (event_id),
  ADD CONSTRAINT fk_withdrawals_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL;

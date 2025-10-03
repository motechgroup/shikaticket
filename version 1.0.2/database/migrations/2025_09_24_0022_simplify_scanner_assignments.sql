-- Simplify scanner assignments - remove admin assignment layer
-- Organizers can directly assign their devices to their events

-- Drop the organizer_scanner_assignments table (admin assignment layer)
DROP TABLE IF EXISTS organizer_scanner_assignments;

-- Keep only event_scanner_assignments for organizer-to-event assignments
-- This table already exists, just make sure it has the right structure
-- The event_scanner_assignments table allows organizers to assign their own devices to their events

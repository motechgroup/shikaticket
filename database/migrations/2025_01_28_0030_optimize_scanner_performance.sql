-- Optimize scanner performance with better indexes

-- Add index on ticket codes for faster lookups
ALTER TABLE tickets ADD INDEX idx_tickets_code (code);

-- Add index on ticket status for filtering
ALTER TABLE tickets ADD INDEX idx_tickets_status (status);

-- Add index on scanner device assignments
ALTER TABLE event_scanner_assignments ADD INDEX idx_event_scanner_device (scanner_device_id, is_active);

-- Add index on travel booking references
ALTER TABLE travel_bookings ADD INDEX idx_travel_booking_reference (booking_reference);

-- Add index on travel booking status
ALTER TABLE travel_bookings ADD INDEX idx_travel_booking_status (status);

-- Add index on travel booking scans for faster duplicate checking
ALTER TABLE travel_booking_scans ADD INDEX idx_booking_scanner (booking_id, scanner_device_id);

-- Add composite index for travel destinations and agencies
ALTER TABLE travel_destinations ADD INDEX idx_travel_dest_agency (agency_id, id);

-- Add index on order items for event lookups
ALTER TABLE order_items ADD INDEX idx_order_items_event (event_id);

-- Add index on events organizer for permission checking
ALTER TABLE events ADD INDEX idx_events_organizer (organizer_id);

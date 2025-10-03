-- Add travel booking confirmation SMS template
INSERT INTO settings (`key`, `value`) 
VALUES ('sms.travel_booking_confirmed', 'Travel booking confirmed! Destination: {{destination}}. Ticket Code: {{ticket_code}}. View ticket: {{ticket_link}}. Contact: {{agency_name}} at {{agency_phone}}')
ON DUPLICATE KEY UPDATE 
`value` = 'Travel booking confirmed! Destination: {{destination}}. Ticket Code: {{ticket_code}}. View ticket: {{ticket_link}}. Contact: {{agency_name}} at {{agency_phone}}';

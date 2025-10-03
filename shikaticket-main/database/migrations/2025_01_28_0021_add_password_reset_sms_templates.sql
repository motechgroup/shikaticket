-- Add password reset SMS templates to settings
INSERT INTO settings (`key`, `value`) VALUES 
('sms.password_reset_user', 'Password Reset: Your reset code is {{reset_code}}. Valid for 15 minutes. If you didn\'t request this, ignore this message. - {{site_name}}'),
('sms.password_reset_organizer', 'Password Reset: Your reset code is {{reset_code}}. Valid for 15 minutes. If you didn\'t request this, ignore this message. - {{site_name}}'),
('sms.password_reset_travel', 'Password Reset: Your reset code is {{reset_code}}. Valid for 15 minutes. If you didn\'t request this, ignore this message. - {{site_name}}')
ON DUPLICATE KEY UPDATE 
`value` = VALUES(`value`);

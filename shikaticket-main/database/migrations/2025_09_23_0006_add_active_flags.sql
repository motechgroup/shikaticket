ALTER TABLE users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER password_hash;
ALTER TABLE organizers ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER commission_percent;



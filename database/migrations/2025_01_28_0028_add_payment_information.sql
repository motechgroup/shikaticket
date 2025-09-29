-- Add payment information fields to organizers table
ALTER TABLE organizers ADD COLUMN payout_method ENUM('bank_transfer', 'mpesa', 'paypal', 'other') NULL;
ALTER TABLE organizers ADD COLUMN bank_name VARCHAR(255) NULL;
ALTER TABLE organizers ADD COLUMN bank_account_name VARCHAR(255) NULL;
ALTER TABLE organizers ADD COLUMN bank_account_number VARCHAR(255) NULL;
ALTER TABLE organizers ADD COLUMN bank_code VARCHAR(50) NULL;
ALTER TABLE organizers ADD COLUMN mpesa_phone VARCHAR(20) NULL;
ALTER TABLE organizers ADD COLUMN paypal_email VARCHAR(255) NULL;
ALTER TABLE organizers ADD COLUMN other_payment_details TEXT NULL;
ALTER TABLE organizers ADD COLUMN payment_info_verified TINYINT(1) DEFAULT 0;
ALTER TABLE organizers ADD COLUMN payment_info_updated_at TIMESTAMP NULL;

-- Add payment information fields to travel_agencies table
ALTER TABLE travel_agencies ADD COLUMN payout_method ENUM('bank_transfer', 'mpesa', 'paypal', 'other') NULL;
ALTER TABLE travel_agencies ADD COLUMN bank_name VARCHAR(255) NULL;
ALTER TABLE travel_agencies ADD COLUMN bank_account_name VARCHAR(255) NULL;
ALTER TABLE travel_agencies ADD COLUMN bank_account_number VARCHAR(255) NULL;
ALTER TABLE travel_agencies ADD COLUMN bank_code VARCHAR(50) NULL;
ALTER TABLE travel_agencies ADD COLUMN mpesa_phone VARCHAR(20) NULL;
ALTER TABLE travel_agencies ADD COLUMN paypal_email VARCHAR(255) NULL;
ALTER TABLE travel_agencies ADD COLUMN other_payment_details TEXT NULL;
ALTER TABLE travel_agencies ADD COLUMN payment_info_verified TINYINT(1) DEFAULT 0;
ALTER TABLE travel_agencies ADD COLUMN payment_info_updated_at TIMESTAMP NULL;

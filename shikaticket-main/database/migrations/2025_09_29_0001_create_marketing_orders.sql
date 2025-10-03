-- marketing orders for simplified reach-based campaigns
CREATE TABLE IF NOT EXISTS marketing_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  account_type ENUM('organizer','travel_agency') NOT NULL,
  account_id INT NOT NULL,
  item_type ENUM('event','destination') NOT NULL,
  item_id INT NOT NULL,
  reach INT NOT NULL DEFAULT 0,
  unit_price_sms DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  unit_price_email DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  mix_email_percent INT NOT NULL DEFAULT 0,
  sms_sent INT NOT NULL DEFAULT 0,
  email_sent INT NOT NULL DEFAULT 0,
  total_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  last_log TEXT NULL,
  notes TEXT NULL,
  payment_method ENUM('mpesa','flutterwave','paypal') NULL,
  payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
  payment_reference VARCHAR(255) NULL,
  status ENUM('pending','approved','running','completed','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



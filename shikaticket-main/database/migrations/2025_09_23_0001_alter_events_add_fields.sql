ALTER TABLE events
  ADD COLUMN poster_path VARCHAR(255) NULL AFTER venue,
  ADD COLUMN regular_price DECIMAL(10,2) NULL AFTER price,
  ADD COLUMN early_bird_price DECIMAL(10,2) NULL AFTER regular_price,
  ADD COLUMN early_bird_until DATE NULL AFTER early_bird_price,
  ADD COLUMN vip_price DECIMAL(10,2) NULL AFTER early_bird_until,
  ADD COLUMN vvip_price DECIMAL(10,2) NULL AFTER vip_price,
  ADD COLUMN group_price DECIMAL(10,2) NULL AFTER vvip_price,
  ADD COLUMN group_size INT NULL AFTER group_price,
  ADD COLUMN dress_code VARCHAR(191) NULL AFTER currency,
  ADD COLUMN lineup TEXT NULL AFTER dress_code;



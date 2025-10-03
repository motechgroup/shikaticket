-- Add tier field to order_items table to store ticket tier information
ALTER TABLE order_items ADD COLUMN tier VARCHAR(50) NULL AFTER unit_price;

-- Update existing order items to have 'regular' as default tier
UPDATE order_items SET tier = 'regular' WHERE tier IS NULL;

-- Make tier field NOT NULL after updating existing records
ALTER TABLE order_items MODIFY COLUMN tier VARCHAR(50) NOT NULL DEFAULT 'regular';

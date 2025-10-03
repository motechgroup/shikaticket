-- Add tier field to tickets table to store ticket type information
ALTER TABLE tickets ADD COLUMN tier VARCHAR(50) NULL AFTER code;

-- Update existing tickets to have 'regular' as default tier
UPDATE tickets SET tier = 'regular' WHERE tier IS NULL;

-- Make tier field NOT NULL after updating existing records
ALTER TABLE tickets MODIFY COLUMN tier VARCHAR(50) NOT NULL DEFAULT 'regular';

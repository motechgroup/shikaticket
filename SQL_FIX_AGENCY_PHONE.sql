-- Fix Travel Agency Phone Numbers with Duplicate Country Codes
-- Run this SQL query in phpMyAdmin or MySQL console
-- This fixes phone numbers like "+254 254792758752" to "+254 792758752"

-- Preview what will be changed (run this first to see what will change)
SELECT 
    id,
    company_name,
    phone AS old_phone,
    CASE
        -- Kenya: +254 254... -> +254 ...
        WHEN phone LIKE '+254 254%' THEN CONCAT('+254 ', SUBSTRING(phone, 10))
        -- Tanzania: +255 255... -> +255 ...
        WHEN phone LIKE '+255 255%' THEN CONCAT('+255 ', SUBSTRING(phone, 10))
        -- Uganda: +256 256... -> +256 ...
        WHEN phone LIKE '+256 256%' THEN CONCAT('+256 ', SUBSTRING(phone, 10))
        -- Rwanda: +250 250... -> +250 ...
        WHEN phone LIKE '+250 250%' THEN CONCAT('+250 ', SUBSTRING(phone, 10))
        -- South Africa: +27 27... -> +27 ...
        WHEN phone LIKE '+27 27%' THEN CONCAT('+27 ', SUBSTRING(phone, 10))
        -- Zambia: +260 260... -> +260 ...
        WHEN phone LIKE '+260 260%' THEN CONCAT('+260 ', SUBSTRING(phone, 10))
        -- Malawi: +265 265... -> +265 ...
        WHEN phone LIKE '+265 265%' THEN CONCAT('+265 ', SUBSTRING(phone, 10))
        ELSE phone
    END AS new_phone,
    CASE
        WHEN phone LIKE '+254 254%' OR phone LIKE '+255 255%' OR 
             phone LIKE '+256 256%' OR phone LIKE '+250 250%' OR 
             phone LIKE '+27 27%' OR phone LIKE '+260 260%' OR 
             phone LIKE '+265 265%'
        THEN 'NEEDS FIX'
        ELSE 'OK'
    END AS status
FROM travel_agencies
WHERE 
    phone LIKE '+254 254%' OR phone LIKE '+255 255%' OR 
    phone LIKE '+256 256%' OR phone LIKE '+250 250%' OR 
    phone LIKE '+27 27%' OR phone LIKE '+260 260%' OR 
    phone LIKE '+265 265%';

-- ========================================
-- AFTER REVIEWING THE PREVIEW ABOVE,
-- RUN THE UPDATE QUERIES BELOW:
-- ========================================

-- Fix Kenya (+254)
UPDATE travel_agencies 
SET phone = CONCAT('+254 ', SUBSTRING(phone, 10))
WHERE phone LIKE '+254 254%';

-- Fix Tanzania (+255)
UPDATE travel_agencies 
SET phone = CONCAT('+255 ', SUBSTRING(phone, 10))
WHERE phone LIKE '+255 255%';

-- Fix Uganda (+256)
UPDATE travel_agencies 
SET phone = CONCAT('+256 ', SUBSTRING(phone, 10))
WHERE phone LIKE '+256 256%';

-- Fix Rwanda (+250)
UPDATE travel_agencies 
SET phone = CONCAT('+250 ', SUBSTRING(phone, 10))
WHERE phone LIKE '+250 250%';

-- Fix South Africa (+27)
UPDATE travel_agencies 
SET phone = CONCAT('+27 ', SUBSTRING(phone, 10))
WHERE phone LIKE '+27 27%';

-- Fix Zambia (+260)
UPDATE travel_agencies 
SET phone = CONCAT('+260 ', SUBSTRING(phone, 10))
WHERE phone LIKE '+260 260%';

-- Fix Malawi (+265)
UPDATE travel_agencies 
SET phone = CONCAT('+265 ', SUBSTRING(phone, 10))
WHERE phone LIKE '+265 265%';

-- Verify the fix
SELECT id, company_name, phone, country 
FROM travel_agencies 
ORDER BY id;

-- Expected result for "Kitu Safaris":
-- Before: +254 254792758752
-- After:  +254 792758752


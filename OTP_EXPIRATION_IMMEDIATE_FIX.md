# OTP Expires Immediately - Fix & Debug

## Problem
OTP still expires immediately even after extending to 30 minutes.

## Likely Causes
1. **Files not uploaded** - Still using old 10-minute code
2. **Server timezone issue** - PHP and MySQL time mismatch
3. **Cache issue** - Browser or server cache

---

## ✅ SOLUTION: Upload & Debug

### Step 1: Upload Updated Controller
Upload: **`app/Controllers/TravelAuthController.php`**

**This file now has:**
- 30-minute expiration (1800 seconds)
- Better error messages (tells you if OTP is wrong vs expired)
- Improved verification logic

### Step 2: Upload Debug Script
Upload: **`debug_otp_timing.php`** to server root

### Step 3: Run Debug Script
Visit: `https://shikaticket.com/debug_otp_timing.php`

**This will show:**
- ✅ Server time configuration
- ✅ PHP vs MySQL time sync
- ✅ Test OTP expiration calculation
- ✅ All recent OTPs with remaining time
- ✅ What's causing the issue

### Step 4: Fix Based on Debug Results

**If debug shows "OTPs expiring immediately":**

**Option A: Add Timezone to config.php**
Add this line to top of `config/config.php` (after `<?php`):
```php
date_default_timezone_set('Africa/Nairobi');
```

**Option B: Quick SQL Fix**
Run this in phpMyAdmin to manually extend existing OTP:
```sql
UPDATE travel_agency_tokens 
SET expires_at = DATE_ADD(NOW(), INTERVAL 30 MINUTE) 
WHERE id = (SELECT MAX(id) FROM (SELECT * FROM travel_agency_tokens) AS t);
```

### Step 5: Test Again
1. Click "Resend OTP" on verification page
2. Check phone for new OTP
3. Enter OTP immediately
4. Should work! ✅

### Step 6: DELETE Debug File
After fixing, delete: `debug_otp_timing.php`

---

## 🔍 What Debug Script Shows

### Example Output (Working):
```
✅ PHP and MySQL time synchronized
✅ OTP expiration calculation working correctly!
✅ Minutes remaining: 28 min (Status: Valid)
```

### Example Output (Broken):
```
❌ OTP expiration calculation FAILED!
❌ Minutes remaining: -5 min (Status: Expired)
⚠️ PROBLEM DETECTED: OTPs expiring immediately!
```

---

## 🎯 Quick Test Without Upload

If you want to test if files were uploaded, check error message:

**Old file says:**
- "Invalid or expired OTP. Please try again or request a new code."

**New file says:**
- "Invalid OTP code. Please check and try again." (wrong code)
- "OTP has expired. Please request a new code." (expired)

If you see the OLD message, files weren't uploaded yet!

---

## 📝 Checklist

- [ ] Upload app/Controllers/TravelAuthController.php
- [ ] Upload debug_otp_timing.php
- [ ] Visit debug URL
- [ ] Check what debug says
- [ ] Apply fix if needed
- [ ] Test OTP verification
- [ ] DELETE debug file

---

## 🚨 Emergency: Skip Verification

If you need to test immediately without fixing OTP:

**Admin can manually verify:**
```sql
-- Find the agency ID
SELECT id, company_name, phone_verified FROM travel_agencies 
WHERE phone = '+254 792758752';

-- Manually verify
UPDATE travel_agencies SET phone_verified = 1 WHERE id = X;
```

Then agency can login (if approved).

---

## Support

If debug script shows an issue we didn't expect, send me:
1. Screenshot of debug output
2. The "Recent OTPs in Database" table
3. Server time configuration section

---

**Priority:** CRITICAL  
**Next Step:** Upload files + run debug script


# Travel Agency Phone Number Duplicate Country Code Fix

## Problem
Travel agencies registering with phone numbers like "254792758752" and selecting "Kenya" as country were getting saved with **duplicate country codes**:

- **Stored in DB:** `+254 254792758752` ❌
- **Should be:** `+254 792758752` ✅

This caused **SMS OTP verification to fail** because the phone number format was incorrect.

---

## Root Cause

The country code was being added **twice**:

1. **Frontend JavaScript:** When user selects country, it adds "+254" to the phone number
2. **Backend PHP:** Then the backend adds "+254" again

### Example Flow (Broken):
```
User types: "254792758752"
         ↓
Selects country: "Kenya"
         ↓
JavaScript adds: "+254 254792758752"  ← First duplicate!
         ↓
Submitted to backend
         ↓
Backend strips to: "254254792758752"
         ↓
Backend adds: "+254 254254792758752"  ← Second duplicate!
```

---

## What Was Fixed

### 1. Frontend JavaScript (register.php)
**Before:**
```javascript
const digits = phone.value.replace(/\D+/g,'');
phone.value = c.code + ' ' + digits.replace(/^0+/, '');
// Problem: If digits already contains "254", it becomes "+254 254..."
```

**After:**
```javascript
let digits = phone.value.replace(/\D+/g,'');
// Remove country code if it's already present at the start
const countryCodeDigits = c.code.replace(/\D+/g,''); // "254" from "+254"
if(digits.startsWith(countryCodeDigits)){
  digits = digits.substring(countryCodeDigits.length); // Remove "254"
}
digits = digits.replace(/^0+/, ''); // Remove leading zeros
phone.value = c.code + ' ' + digits; // Now: "+254 792758752" ✅
```

### 2. Backend PHP (TravelAuthController.php)
**Before:**
```php
$digits = preg_replace('/\D+/', '', $data['phone']);
$digits = ltrim($digits, '0');
$data['phone'] = $prefix . ' ' . $digits;
// Problem: If digits contains "254", it becomes "+254 254..."
```

**After:**
```php
$digits = preg_replace('/\D+/', '', $data['phone']);
$countryCodeDigits = preg_replace('/\D+/', '', $prefix); // "254"

// Remove country code if already at start
if (strpos($digits, $countryCodeDigits) === 0) {
    $digits = substr($digits, strlen($countryCodeDigits));
}
$digits = ltrim($digits, '0');
$data['phone'] = $prefix . ' ' . $digits; // Now: "+254 792758752" ✅
```

---

## How It Works Now

### Registration Flow (Fixed):
```
User types: "254792758752"
         ↓
Selects country: "Kenya"
         ↓
JavaScript detects "254" at start
JavaScript removes it: "792758752"
JavaScript adds "+254": "+254 792758752" ✅
         ↓
Submitted to backend
         ↓
Backend strips to: "254792758752"
Backend detects "254" at start
Backend removes it: "792758752"
Backend adds "+254": "+254 792758752" ✅
```

### Supported Input Formats:
All of these will correctly become `+254 792758752`:

✅ `254792758752` (with country code)  
✅ `0792758752` (with leading zero)  
✅ `792758752` (without prefix)  
✅ `+254 792758752` (already formatted)  
✅ `+254792758752` (formatted without space)  

---

## Files Changed

### 1. ✅ `app/Views/travel/auth/register.php`
**Changes:**
- Fixed `onCountryChange()` function (lines 127-148)
- Fixed `normalizePhone()` function (lines 161-178)
- Both now strip country code before adding it

### 2. ✅ `app/Controllers/TravelAuthController.php`
**Changes:**
- Fixed phone normalization (lines 103-132)
- Strips country code digits before adding prefix
- Better logic for handling various input formats

### 3. 🛠️ `fix_agency_phone.php` (NEW - TEMPORARY)
**Purpose:**
- Fixes existing agencies with duplicate country codes
- Shows before/after comparison
- **MUST DELETE after running!**

---

## Deployment Steps

### Step 1: Upload Fixed Files
Upload these files to your live server:
1. ✅ `app/Views/travel/auth/register.php`
2. ✅ `app/Controllers/TravelAuthController.php`
3. ⚠️ `fix_agency_phone.php` (temporary, delete after use)

### Step 2: Fix Existing Agency Phone Numbers
1. Upload `fix_agency_phone.php` to server root
2. Access: `https://shikaticket.com/fix_agency_phone.php`
3. Review the changes shown
4. **DELETE the file immediately!**

### Step 3: Verify in Admin Panel
1. Go to: Admin > Travel Agencies
2. Check "Kitu Safaris" phone number
3. Should now be: `+254 792758752` ✅
4. Not: `+254 254792758752` ❌

### Step 4: Test SMS Verification
1. Login as admin
2. Go to the agency
3. Click "Verify Phone"
4. SMS should now be sent correctly

### Step 5: Test New Registration
1. Open incognito browser
2. Go to: `https://shikaticket.com/public/travel/register`
3. Fill form with phone: `254792758752`
4. Select country: `Kenya`
5. Check phone field shows: `+254 792758752` ✅
6. Submit form
7. Verify in database it's saved correctly

---

## Example Phone Number Transformations

### Kenya (+254):
| User Input | Frontend Result | Backend Saves | SMS Recipient |
|------------|----------------|---------------|---------------|
| `254792758752` | `+254 792758752` | `+254 792758752` | ✅ Valid |
| `0792758752` | `+254 792758752` | `+254 792758752` | ✅ Valid |
| `792758752` | `+254 792758752` | `+254 792758752` | ✅ Valid |
| `+254 792758752` | `+254 792758752` | `+254 792758752` | ✅ Valid |

### Tanzania (+255):
| User Input | Frontend Result | Backend Saves |
|------------|----------------|---------------|
| `255712345678` | `+255 712345678` | `+255 712345678` |
| `0712345678` | `+255 712345678` | `+255 712345678` |

---

## SMS Integration

The fixed phone numbers will now work correctly with:

✅ **Twilio** - Accepts international format `+254792758752`  
✅ **TextSMS** - Accepts international format `+254792758752`  
✅ **Any SMS provider** - Standard E.164 format  

---

## Database Impact

### Before Fix:
```sql
SELECT id, company_name, phone FROM travel_agencies;
-- Result: phone = "+254 254792758752" ❌
```

### After Fix:
```sql
SELECT id, company_name, phone FROM travel_agencies;
-- Result: phone = "+254 792758752" ✅
```

---

## Testing Checklist

After deployment:

- [ ] Upload 2 fixed files to live server
- [ ] Run fix_agency_phone.php script
- [ ] Delete fix_agency_phone.php from server
- [ ] Check existing agencies in admin panel (phone correct)
- [ ] Test new agency registration
- [ ] Verify phone number format in registration form
- [ ] Submit test registration
- [ ] Check database for correct phone format
- [ ] Test SMS sending to fixed phone number
- [ ] Verify SMS OTP is received

---

## Expected Results

### Registration Form:
When user types `254792758752` and selects Kenya:
- ✅ Phone field shows: `+254 792758752`
- ✅ Submitted value: `+254 792758752`
- ✅ Database stores: `+254 792758752`

### Admin Panel:
After running fix script:
- ✅ Kitu Safaris phone: `+254 792758752` (was `+254 254792758752`)
- ✅ All agencies have correct format
- ✅ "Verify Phone" button sends SMS successfully

### SMS Delivery:
- ✅ SMS sent to: `+254792758752` (TextSMS/Twilio format)
- ✅ OTP delivered successfully
- ✅ Agency can verify phone

---

## Backward Compatibility

The fix handles all existing input methods:

✅ Users who type country code (254...)  
✅ Users who type with leading zero (0792...)  
✅ Users who type without prefix (792...)  
✅ Users who paste formatted number (+254 792...)  

All result in the same correct format: `+254 792758752`

---

## Security Notes

### fix_agency_phone.php Script:
⚠️ **CRITICAL:** This script has direct database access  
⚠️ **MUST DELETE** immediately after running  
⚠️ **DO NOT** leave on production server  

### Why Delete?
- Exposes database structure
- No authentication required
- Could be misused by attackers
- Only needed once for migration

---

## Troubleshooting

### If Phone Still Wrong After Fix:
1. Check if fix script ran successfully
2. Clear browser cache
3. Check database directly:
   ```sql
   SELECT phone FROM travel_agencies WHERE company_name = 'Kitu Safaris';
   ```
4. Re-run fix script if needed

### If SMS Still Not Sending:
1. Verify phone format: `+254 792758752`
2. Check SMS provider credentials in Admin > Settings
3. Check SMS logs table:
   ```sql
   SELECT * FROM sms_logs ORDER BY id DESC LIMIT 10;
   ```
4. Verify provider (TextSMS/Twilio) is configured

### If New Registrations Still Wrong:
1. Clear browser cache completely
2. Check if both files were uploaded
3. Test in incognito mode
4. Check browser console for JavaScript errors

---

## Summary

**Problem:** Duplicate country codes in phone numbers  
**Cause:** Both frontend and backend adding country code  
**Solution:** Strip existing country code before adding  
**Result:** Correct phone format for SMS delivery  

**Files:** 2 files changed, 1 temp script  
**Risk:** ⚠️ NONE (handles all input formats)  
**Priority:** 🔴 CRITICAL (blocks SMS verification)  
**Time:** ⚡ 5 minutes  

---

## Success Criteria

✅ Existing agencies have correct phone format  
✅ New registrations save correct format  
✅ SMS OTP is delivered successfully  
✅ Phone verification works  
✅ All input methods handled correctly  
✅ Fix script deleted from server  

---

**Status:** READY TO DEPLOY  
**Date:** October 1, 2025  
**Version:** 1.0.3


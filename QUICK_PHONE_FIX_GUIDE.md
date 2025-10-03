# ⚡ Quick Fix: Duplicate Country Code in Agency Phone Numbers

## 🔴 The Problem
Phone numbers saved as: `+254 254792758752` ❌  
Should be: `+254 792758752` ✅  
**Result:** SMS OTP cannot be sent!

---

## 🚀 Quick Fix (5 Minutes)

### Step 1: Upload 3 Files
```
✅ app/Views/travel/auth/register.php
✅ app/Controllers/TravelAuthController.php
⚠️ fix_agency_phone.php (temp - delete after!)
```

### Step 2: Fix Existing Data
1. Go to: `https://shikaticket.com/fix_agency_phone.php`
2. Review changes
3. **DELETE the file!**

### Step 3: Verify
1. Admin > Travel Agencies
2. Check phone: `+254 792758752` ✅
3. Test SMS verification

---

## 📋 Testing Checklist
- [ ] Files uploaded
- [ ] Fix script run
- [ ] Fix script deleted (CRITICAL!)
- [ ] Phone numbers correct in admin
- [ ] Test new registration
- [ ] SMS OTP working

---

## 🎯 What Changed

**Before:**
- User types: `254792758752`
- System saves: `+254 254792758752` ❌

**After:**
- User types: `254792758752`
- System detects duplicate
- System saves: `+254 792758752` ✅

---

## ⚠️ IMPORTANT
**DELETE fix_agency_phone.php after running!**  
Security risk if left on server!

---

**Priority:** CRITICAL  
**Time:** 5 minutes  
**Risk:** NONE


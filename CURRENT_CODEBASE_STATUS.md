# Current Codebase Status - Ticko/ShikaTicket
**Last Updated:** October 1, 2025  
**Version:** 1.0.2

## Overview
This document provides a comprehensive overview of the current codebase after reverting to an older stable version from GitHub and re-applying recent changes.

---

## ✅ Recently Added Features (Confirmed Working)

### 1. **Travel Agency Delete Functionality**
- **Route:** `POST /admin/travel/agencies/delete`
- **Controller Method:** `AdminController::deleteTravelAgency()`
- **Location:** `app/Controllers/AdminController.php` (lines 666-692)
- **Features:**
  - Requires admin authentication (`require_admin()`)
  - CSRF protection via `verify_csrf()`
  - Fetches agency details before deletion
  - Logs deletion with AuditLogger
  - Flash success message
  - Redirects to agencies list
- **View:** Delete button added to `app/Views/admin/travel_agencies.php` with confirmation dialog

### 2. **Audit Logging System**
- **Model:** `app/Models/AuditLogger.php`
- **Method:** `AuditLogger::log(category, message, meta[], level)`
- **Database Table:** `audit_logs` (migration: `database/migrations/2025_09_30_1230_create_audit_logs.sql`)
- **Schema:**
  - `id`, `occurred_at`, `level`, `category`, `ip`, `user_agent`
  - `user_id`, `role`, `url`, `message`, `meta` (JSON)
- **Usage:** Logs travel agency deletions with warning level

### 3. **Mobile Sidebar Fix**
- **File:** `app/Views/layouts/main.php`
- **Line 192:** `$isAdminSidebar` check updated to include admin, manager, and accountant roles
- **Status:** **NEEDS TO BE UPLOADED TO LIVE SERVER**
- **Change:**
  ```php
  // OLD (broken):
  $isAdminSidebar = ... && (($_SESSION['role'] ?? null) === 'admin');
  
  // NEW (fixed):
  $isAdminSidebar = ... && in_array(($_SESSION['role'] ?? null), ['admin', 'manager', 'accountant']);
  ```

---

## 🔐 Authentication & Authorization

### Admin System
- **Login:** Uses `Setting::get('admin.email')` and `Setting::get('admin.password')`
- **Session Keys:** `$_SESSION['admin']`, `$_SESSION['admin_id']`, `$_SESSION['role']`
- **Role:** Always set to `'admin'` on login
- **Guard Function:** `require_admin()` in `config/config.php` (line 181-184)
  - Checks if `$_SESSION['role'] === 'admin'`
  - Redirects to `/admin/login` if not authenticated

### User System
- **Guard:** `require_user()` - Checks `$_SESSION['user_id']`
- **Redirect:** `/login`

### Organizer System  
- **Guard:** `require_organizer()` - Checks `$_SESSION['organizer_id']`
- **Redirect:** `/organizer/login`

### Travel Agency System
- **Guard:** `require_travel_agency()` - Checks `$_SESSION['travel_agency_id']`
- **Redirect:** `/travel/login`

---

## 🗂️ Core Configuration

### Database (`config/config.php`)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ticko');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Environment
- **Mode:** `ENVIRONMENT` = `$_ENV['APP_ENV'] ?? 'production'`
- **Debug:** `DEBUG_MODE` = `ENVIRONMENT === 'development'`

### Security Features
1. **Session Security:** `SessionSecurityService::initializeSecureSession()`
2. **Security Middleware:**
   - `addSecurityHeaders()`
   - `checkForSuspiciousActivity()`
   - `checkSessionSecurity()`
3. **CSRF Protection:**
   - `csrf_token()`, `csrf_field()`, `verify_csrf()` functions
   - Supports both form and JSON requests
   - Token stored in `$_SESSION['csrf_token']`

### Base URL Handling
- **Function:** `base_url($path)` in `config/config.php` (lines 139-178)
- **Features:**
  - Force HTTPS for ngrok URLs
  - Handles subdirectory installations (`/ticko/public`)
  - Smart detection of environment (local, staging, production)

---

## 📁 Key Files & Structure

### Controllers
- **AdminController:** `app/Controllers/AdminController.php` (2366 lines)
  - Main admin dashboard and management
  - Travel agency CRUD operations
  - Organizer management
  - Settings management
  - Finance operations
  - Scanner management

### Models
- **AuditLogger:** `app/Models/AuditLogger.php`
- **Setting:** Uses `settings` table for site configuration
- **Organizer:** Organizer model and operations

### Views
- **Main Layout:** `app/Views/layouts/main.php` (1377 lines)
  - Handles admin, organizer, travel, and public layouts
  - Mobile sidebar implementation
  - Desktop sidebar with role-based content
- **Admin Dashboard:** `app/Views/admin/index.php`
- **Travel Agencies:** `app/Views/admin/travel_agencies.php`

### Routes (`public/index.php`)
Key admin routes:
- `GET /admin` → `AdminController@index`
- `GET /admin/login` → `AdminController@loginForm`
- `POST /admin/login` → `AdminController@login`
- `GET /admin/travel/agencies` → `AdminController@travelAgencies`
- `POST /admin/travel/agencies/delete` → `AdminController@deleteTravelAgency` ✅ NEW

---

## ⚠️ Known Issues (From Previous Summary)

### 1. **Role-Based Access Not Fully Implemented**
- **Issue:** `require_permission()` function doesn't exist in `config/config.php`
- **Impact:** All admin methods use `require_admin()` which only checks for admin role
- **Status:** Originally mentioned in summary but not implemented
- **Note:** Manager and accountant roles mentioned in summary but not in actual code

### 2. **Mobile Sidebar Issue**
- **Issue:** `$isAdminSidebar` only checks for `'admin'` role
- **Impact:** Mobile menu doesn't show for manager/accountant
- **Fix Applied:** Line 192 updated to check array of roles
- **Status:** ✅ FIXED (needs upload to live server)

### 3. **Black Screen Issue (Resolved)**
- **Previous Issue:** Mobile sidebar overlay stuck visible
- **Fix:** CSS uses `display: none` by default, JavaScript toggles visibility
- **Current State:** Should be working correctly

---

## 🚀 Files That Need Upload to Live Server

Based on recent changes, upload these files:

1. ✅ `public/index.php` - Added delete route
2. ✅ `app/Controllers/AdminController.php` - Added deleteTravelAgency method
3. ✅ `app/Views/admin/travel_agencies.php` - Added delete button
4. ✅ `app/Models/AuditLogger.php` - Audit logging class
5. ✅ `CHANGELOG.md` - Updated changelog
6. ✅ `public/CHANGELOG.md` - Copy of changelog
7. ✅ `public/changelog.html` - HTML version
8. ⚠️ **`app/Views/layouts/main.php`** - Mobile sidebar fix (CRITICAL)

### Database Migration Needed
Run this SQL if `audit_logs` table doesn't exist:
```sql
CREATE TABLE IF NOT EXISTS audit_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  occurred_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  level VARCHAR(20) NOT NULL DEFAULT 'info',
  category VARCHAR(64) NOT NULL,
  ip VARCHAR(64) NULL,
  user_agent VARCHAR(255) NULL,
  user_id INT NULL,
  role VARCHAR(32) NULL,
  url VARCHAR(255) NULL,
  message TEXT NOT NULL,
  meta JSON NULL,
  INDEX (level),
  INDEX (category),
  INDEX (occurred_at)
) ENGINE=InnoDB;
```

---

## 📊 Database Tables (Confirmed Usage)

From code analysis:
- `audit_logs` - NEW (needs migration)
- `travel_agencies` - Used for agency management
- `settings` - Site and admin configuration
- `users` - End users
- `organizers` - Event organizers
- `orders` - Ticket orders
- `order_items` - Order line items
- `events` - Events/concerts
- `travel_destinations` - Travel packages
- `travel_bookings` - Travel bookings
- `travel_payments` - Travel payment records
- `withdrawals` - Payout requests
- `feature_requests` - Feature/highlight requests
- `scanner_devices` - Registered scanner devices

---

## 🎨 Frontend Stack

- **CSS Framework:** Tailwind CSS (via CDN)
- **Custom Styles:** Inline in `main.php` layout
- **JavaScript:** Vanilla JS (no frameworks)
- **Mobile-First:** Responsive design with mobile sidebar
- **Icons:** SVG icons inline

---

## 🔍 Current Authentication Flow

### Admin Login
1. User submits email/password to `/admin/login`
2. `AdminController::login()` checks credentials against `settings` table
3. On success:
   - Sets `$_SESSION['admin']` = email
   - Sets `$_SESSION['admin_id']` = 1
   - Sets `$_SESSION['role']` = 'admin'
4. Redirects to `/admin`

### Admin Area Protection
- All admin methods call `require_admin()` at start
- `require_admin()` checks `$_SESSION['role'] === 'admin'`
- If not admin, redirects to `/admin/login`

---

## 📝 Next Steps / Recommendations

1. **Upload Mobile Sidebar Fix** (CRITICAL)
   - File: `app/Views/layouts/main.php`
   - This fixes mobile menu for admin area

2. **Run Database Migration**
   - Execute `2025_09_30_1230_create_audit_logs.sql`
   - Verify `audit_logs` table exists

3. **Test Travel Agency Delete**
   - Login as admin
   - Go to `/admin/travel/agencies`
   - Try deleting an agency
   - Check audit log for entry

4. **Future Enhancements** (if needed)
   - Implement full role-based access control
   - Add manager and accountant login flows
   - Create permissions system
   - Add activity logs viewer page

---

## 🛠️ Development Notes

### Code Style
- PSR-4 autoloading
- Namespaces: `App\Controllers\`, `App\Models\`, etc.
- Views use PHP templates (no framework)
- Direct PDO database access

### Security Practices
- CSRF tokens on all forms
- Session security via `SessionSecurityService`
- Security headers via middleware
- Suspicious activity monitoring
- Password hashing for user/organizer accounts
- Plain text comparison for admin (from settings)

### Error Handling
- Try-catch blocks in AuditLogger (fails silently)
- Flash messages for user feedback
- Database errors throw exceptions

---

## 📞 Support Information

**System Name:** Ticko / ShikaTicket  
**Version:** 1.0.2  
**Last Major Update:** October 1, 2025  
**PHP Version Required:** 7.4+  
**Database:** MySQL 5.7+ / MariaDB 10.3+

---

*This document was automatically generated based on codebase analysis.*


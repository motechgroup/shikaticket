<div align="center">

# 🧾 Ticko Changelog

Keeping track of what changed and when.

![version](https://img.shields.io/badge/version-1.0.2-blue)
![date](https://img.shields.io/badge/released-2025--09--30-success)
![status](https://img.shields.io/badge/status-stable-green)

</div>

---

## 📚 Table of Contents
- [1.0.2 — Activity Logs & Enhanced Audit Trail](#102--activity-logs--enhanced-audit-trail)
- [1.0.1 — Admin destination editing + HTML changelog](#101--admin-destination-editing--html-changelog)
- [1.0.0 — First public release](#100--first-public-release)
  - [Highlights](#highlights)
  - [Added](#added)
  - [Changed](#changed)
  - [Removed](#removed)
  - [Fixed](#fixed)
  - [Security](#security)
  - [Modules Overview](#modules-overview)

---

## 🔍 1.0.2 — Activity Logs & Enhanced Audit Trail

**Released:** October 1, 2025

### 🎯 Highlights
- **Activity Logs Dashboard** for super admins to monitor all user actions
- **Role-based audit trail** with manager/accountant activity tracking
- **Enhanced security monitoring** with detailed action logging
- **Travel agency management** with delete capability

### ✨ Added
- **Activity Logs Page** (`/admin/logs`)
  - Filter by role (admin, manager, accountant)
  - Search by category, message, URL, or IP
  - Pagination (50 entries per page)
  - Export to CSV functionality
- **Travel Agency Management**
  - Delete travel agencies with confirmation dialog
  - Full audit logging of deletions (company name, email, ID)
  - Permission-based access control
- **Comprehensive Audit Logging**
  - Withdrawal status updates
  - Page creation, updates, and deletions
  - User management (toggle status, delete)
  - Travel agency deletions
  - Finance dashboard access and exports
  - Admin settings changes
  - Login attempts (successful and failed)
- **Role-based Activity Tracking**
  - All admin-area actions tagged with user role
  - Manager and accountant actions visible to super admin
  - Detailed context for each logged action

### 🔧 Enhanced
- **Admin Sidebar** - Added "Activity Logs" link (admin only)
- **Audit Trail** - Extended logging to cover key administrative actions
- **Security Monitoring** - Better visibility into system usage patterns

### 🛡️ Security
- **Access Control** - Activity logs restricted to super admin only
- **Audit Compliance** - Complete trail of administrative actions
- **Role Monitoring** - Track manager and accountant activities

---

## 1.0.1 — Admin destination editing + HTML changelog

> Minor feature release enabling destination management from the Admin and improving changelog visibility.

### Added
- Admin can edit Travel Destinations: new routes, controller actions, and form at `/admin/travel/destinations` (Edit, Publish/Unpublish, Feature/Unfeature).
- Admin can edit Travel Agencies: new routes, controller actions, and form at `/admin/travel/agencies` (edit profile, status flags, logo upload, commission).
- Styled HTML changelog page at `/changelog.html` for quick in-app viewing.

### Changed
- Admin dashboard footer link now points to the HTML changelog (`/changelog.html`).
- CSRF protection added to admin edit/update forms; update endpoints now require POST and redirect on direct GET.

### Fixed
- Broken QR image on booking/ticket pages (ngrok/HTTPS). Introduced local QR proxy at `/qr.php` and switched all QR renders/downloads to use it.
- Travel agency phone normalization at registration and profile update to prevent `+254 254...` duplicates (improves SMS delivery and OTP).

### Added
- Travel agency login allowed before phone verification/approval; posting remains gated. OTP screen includes "Skip for now" to reach dashboard.
- Universal Scanner quick links added to Admin Scans, Organizer Dashboard, and Travel Dashboard.
- Rich legal pages (Terms, Privacy, Refund) seeded; migration added to update existing content.

---

## 1.0.0 — First public release

> The initial stable release of Ticko featuring the full event ticketing, travel bookings, admin console, and universal scanner flows.

### Highlights
- ✅ Modular PHP MVC architecture with secure middleware and installer
- ✅ Admin, Organizer, Travel portals with finance and withdrawals
- ✅ Universal web scanner + mobile app links managed from settings
- ✅ Email/SMS templating with SMTP and SMS providers

### Added
- Core PHP MVC stack: front controller, lightweight router, autoloading.
- Security middleware: secure session initialization, headers, CSRF helpers, suspicious activity checks.
- Installer `public/install.php` and environment-driven configuration via `config/env.php`.
- Admin console: events, users, organizers, travel agencies, banners, featured content, partner logos, pages CMS, notification templates, SMS templates, communications, finance dashboards, scans, scanner assignments, settings.
- Organizer portal: dashboard, events CRUD, reports, finance, scanner devices, event scanner assignments, withdrawals.
- Travel portal: destinations, public listings, destination pages, bookings and checkout, dashboard, finance, withdrawals, profile.
- Universal scanner (web) with login and scan views.
- Mobile scanner app links (Play Store / App Store) managed in Admin settings and surfaced in Organizer Dashboard and Travel Scanner.
- Documentation under `docs/` (getting started, user/admin/organizer guides, templates, branding, scanner guides).
- Sitemap generation and basic SEO helpers.
- Backups and rate limit storage under `storage/`.

### Changed
- Database configuration loads from `config/env.php` (supports host/port/socket) with resilient PDO connection attempts.
- Admin settings: new “Apps” tab for `apps.android_scanner_url` and `apps.ios_scanner_url`.
- Admin dashboard Quick Actions: added Universal Scanner shortcut.
- Layouts and sidebars updated to remove deprecated marketing links.
- Base URL generation improved to support root/subdirectory deployments and enforced HTTPS on ngrok.

### Removed
- Marketing module (organizer/agency/admin) decommissioned:
  - Routes disabled via `DisabledController@notFound`.
  - UI links removed from admin, organizer, and travel sidebars.
  - Legacy marketing views/services/migrations deleted.

### Fixed
- More robust router flow and security hardening.
- Improved installer and environment bootstrap for production and development.

### Security
- Centralized session hardening, security headers, CSRF utilities.
- Rate limiting service added.
- Production security guide and audit report included.

### Modules Overview
- Core: Router, middleware, helpers, env/config.
- Auth: user/organizer/travel auth flows, password resets, OTP for organizers.
- Users & Orgs: models and admin management pages.
- Events & Tickets: listings, event CRUD for organizers, ticket view, featured content.
- Payments & Checkout: controllers and integration points (M-Pesa/PayPal/Flutterwave readiness).
- Communications: SMTP Mailer, SMS service (Twilio/TextSMS), templates, admin composer.
- Scanner: universal web scanner, admin scans and assignments, organizer devices, travel scanner pages.
- Travel: destinations, public listing, bookings/checkout, dashboards, finance, withdrawals.
- Finance: dashboards for admin/organizer/travel; withdrawals flows.
- Content & Partners: pages CMS, partner logos, banners.
- Admin Settings: system settings including mobile app links.
- Docs & SEO: documentation and sitemap generation.

---

[1.0.0]: https://example.com/releases/1.0.0

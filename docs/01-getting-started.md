# Getting Started

## Requirements
- PHP 8+
- MySQL 5.7+/MariaDB 10+
- Apache/Nginx serving `public/`

## Installation
1. Create database and import `database/schema.sql`.
2. Run additional migrations in `database/migrations`.
3. Configure `config/config.php` for DB.
4. Visit `http://localhost/ticko/public/`.

## Roles & Logins
- User: registers with phone/email
- Organizer: registers with email; requires approval and phone OTP
- Admin: `/admin` (default admin@example.com / admin123)

## Important URLs
- Home `/`
- Admin `/admin`
- Organizer dashboard `/organizer/dashboard`
- Scanner `/scanner`
- Organizer profile public `/organizers/show?id=ORG_ID`

# 🚀 Live Server Deployment Checklist - ShikaTicket v1.0.3

## ✅ Pre-Deployment Preparation

### **1. Code Ready for Production**
- ✅ **Version 1.0.3** - All mobile navigation improvements
- ✅ **SMTP Configuration** - Production-ready with SSL support
- ✅ **Admin Login Fix** - Sidebar properly hidden on login page
- ✅ **Mobile Navigation** - Complete mobile admin system
- ✅ **Scanner Improvements** - Universal scanner optimizations
- ✅ **PDF Generation** - Fixed ticket downloads

### **2. Database Migrations**
```sql
-- Run these migrations on live server:
1. database/migrations/2025_01_28_0030_optimize_scanner_performance.sql
2. database/migrations/2025_01_28_0031_create_travel_scanner_assignments.sql
```

### **3. File Permissions**
```bash
# Set proper permissions for uploads
chmod 755 app/public/uploads/
chmod 755 storage/
chmod 644 app/Views/layouts/main.php
chmod 644 app/Services/Mailer.php
```

## 🚀 Deployment Steps

### **Step 1: Upload Files**
1. **Upload all files** from current directory to live server
2. **Exclude development files:**
   - `email_log.txt`
   - `smtp_credentials.txt`
   - `test_*.php` files
   - `debug_*.php` files
   - `temp_*.php` files

### **Step 2: Database Updates**
1. **Run migrations:**
   ```sql
   -- Execute in phpMyAdmin or MySQL command line:
   SOURCE database/migrations/2025_01_28_0030_optimize_scanner_performance.sql;
   SOURCE database/migrations/2025_01_28_0031_create_travel_scanner_assignments.sql;
   ```

### **Step 3: SMTP Configuration**
1. **Login to Admin Panel** → Settings
2. **Configure SMTP:**
   - Host: `mail.shikaticket.com`
   - Port: `465`
   - Encryption: `SSL`
   - Username: `hello@shikaticket.com`
   - Password: `obedmokuamirera`
   - From Email: `hello@shikaticket.com`
   - From Name: `ShikaTicket`
3. **Test Email** to `othedj@gmail.com`

### **Step 4: Verify Core Functions**
- ✅ **Admin Login** - No sidebar on login page
- ✅ **Mobile Navigation** - Back arrows work on all admin pages
- ✅ **Email Sending** - Test emails work
- ✅ **Scanner Function** - Universal scanner works
- ✅ **PDF Downloads** - Ticket downloads work
- ✅ **Travel Scanner** - Destination scanning works

## 🔧 Post-Deployment Testing

### **Mobile Admin Navigation Test:**
1. Open admin panel on mobile device
2. Navigate to any admin page (e.g., Users, Events)
3. Verify back arrow appears and works
4. Test mobile menu (hamburger button)
5. Test search functionality in mobile menu
6. Test keyboard shortcuts (Alt+M)

### **Email Functionality Test:**
1. **User Registration** - Register new user
2. **Password Reset** - Request password reset
3. **Event Booking** - Book event ticket
4. **Travel Booking** - Book travel destination
5. **Admin Notifications** - Check admin receives emails

### **Scanner Functionality Test:**
1. **Universal Scanner** - Login with device code
2. **Event Ticket Scanning** - Scan event tickets
3. **Travel Booking Scanning** - Scan travel bookings
4. **Travel Scanner Assignment** - Assign scanners to destinations

### **PDF Generation Test:**
1. **Event Tickets** - Download event ticket PDF
2. **Travel Bookings** - Download travel booking PDF
3. **Server-side Generation** - Verify TCPDF works

## 🚨 Critical Configuration

### **Environment Variables (if using .env):**
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=your_database_host
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
```

### **PHP Requirements:**
- ✅ **PHP 7.4+** required
- ✅ **OpenSSL extension** enabled
- ✅ **PDO MySQL** extension enabled
- ✅ **GD extension** for image processing
- ✅ **File uploads** enabled

### **Server Configuration:**
- ✅ **SSL Certificate** installed
- ✅ **Port 465** open for SMTP
- ✅ **File permissions** set correctly
- ✅ **Error logging** enabled

## 📊 Performance Monitoring

### **After Deployment:**
1. **Monitor error logs** for 24 hours
2. **Check email delivery** rates
3. **Test scanner performance** under load
4. **Monitor database** query performance
5. **Check mobile responsiveness** on various devices

### **Key Metrics to Watch:**
- Email delivery success rate
- Scanner response times
- Mobile navigation performance
- PDF generation speed
- Database query execution times

## 🆘 Rollback Plan

### **If Issues Occur:**
1. **Revert to previous version** using git
2. **Restore database backup** if needed
3. **Check error logs** for specific issues
4. **Test core functionality** after rollback

### **Emergency Contacts:**
- **Server Admin:** [Your server admin contact]
- **Database Admin:** [Your database admin contact]
- **SMTP Provider:** [Your email provider support]

---
**Deployment Date:** [To be filled]  
**Deployed By:** [Your name]  
**Version:** 1.0.3  
**Status:** Ready for Production

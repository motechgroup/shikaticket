# Production Security Setup Guide

## Critical Security Tasks Before Production Deployment

### 1. Database Security 🔐

**Change Default Credentials:**
```sql
-- Create new admin user
CREATE USER 'ticko_admin'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON ticko.* TO 'ticko_admin'@'localhost';
FLUSH PRIVILEGES;

-- Update config/config.php
DB_USER = 'ticko_admin'
DB_PASS = 'STRONG_PASSWORD_HERE'
```

**Database Configuration:**
```sql
-- Enable binary logging for security
SET GLOBAL log_bin_trust_function_creators = 1;

-- Set secure file permissions
-- Only allow connections from localhost
-- Enable SSL connections if possible
```

### 2. File System Security 📁

**Set Proper Permissions:**
```bash
# Directories
find public/uploads -type d -exec chmod 755 {} \;

# Files
find public/uploads -type f -exec chmod 644 {} \;

# Configuration files
chmod 600 config/config.php
chmod 600 .env

# Storage directory
chmod 755 storage/
chmod 644 storage/rate_limits.json
```

**Create .htaccess Security Rules:**
```apache
# public/.htaccess
<Files "*.php">
    Order Deny,Allow
    Deny from all
</Files>

<Files "index.php">
    Order Allow,Deny
    Allow from all
</Files>

# Block access to sensitive files
<FilesMatch "\.(env|log|sql|md)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'"
</IfModule>
```

### 3. PHP Security Configuration 🔧

**php.ini Settings:**
```ini
# Disable dangerous functions
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source

# Security settings
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
file_uploads = On
upload_max_filesize = 5M
post_max_size = 10M
max_execution_time = 30
max_input_time = 30
memory_limit = 128M

# Session security
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
session.cookie_samesite = "Strict"
session.gc_maxlifetime = 7200

# Error reporting
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

### 4. Environment Configuration 🌍

**Create .env file:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_NAME=ticko
DB_USER=ticko_admin
DB_PASS=STRONG_PASSWORD_HERE

# Email Configuration
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=EMAIL_PASSWORD
MAIL_ENCRYPTION=tls

# SMS Configuration
SMS_PROVIDER=twilio
SMS_API_KEY=your_sms_api_key
SMS_API_SECRET=your_sms_api_secret

# M-Pesa Configuration
MPESA_ENVIRONMENT=sandbox
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
```

### 5. SSL/TLS Configuration 🔒

**Apache Virtual Host:**
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /path/to/ticko/public
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    SSLCertificateChainFile /path/to/chain.crt
    
    # Security headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    
    # Redirect HTTP to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>
```

### 6. Firewall Configuration 🛡️

**UFW Rules (Ubuntu):**
```bash
# Allow SSH
ufw allow ssh

# Allow HTTP and HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Allow MySQL (only from localhost)
ufw allow from 127.0.0.1 to any port 3306

# Enable firewall
ufw enable
```

### 7. Monitoring & Logging 📊

**Set up log monitoring:**
```bash
# Create log directories
mkdir -p /var/log/ticko
chown www-data:www-data /var/log/ticko

# Configure logrotate
cat > /etc/logrotate.d/ticko << EOF
/var/log/ticko/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF
```

### 8. Backup Strategy 💾

**Database Backup Script:**
```bash
#!/bin/bash
# backup_db.sh
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/ticko"
DB_NAME="ticko"

mkdir -p $BACKUP_DIR
mysqldump -u ticko_admin -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/ticko_$DATE.sql
gzip $BACKUP_DIR/ticko_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -name "ticko_*.sql.gz" -mtime +30 -delete
```

### 9. Security Testing Checklist ✅

**Before Going Live:**
- [ ] Change all default passwords
- [ ] Test file upload security
- [ ] Verify CSRF protection
- [ ] Test rate limiting
- [ ] Check SQL injection protection
- [ ] Verify HTTPS configuration
- [ ] Test session security
- [ ] Check error handling
- [ ] Verify file permissions
- [ ] Test backup and restore

### 10. Ongoing Security Maintenance 🔄

**Regular Tasks:**
- Update PHP and MySQL regularly
- Monitor security logs
- Review access logs
- Update SSL certificates
- Test backup procedures
- Review user permissions
- Monitor failed login attempts
- Update dependencies

## Emergency Response Plan 🚨

**If Security Breach Detected:**
1. Immediately change all passwords
2. Review access logs
3. Check for unauthorized file modifications
4. Verify database integrity
5. Update security measures
6. Notify stakeholders
7. Document incident

## Contact Information 📞

**Security Team:**
- Primary: security@yourdomain.com
- Emergency: +254-XXX-XXX-XXX

**System Administrator:**
- Email: admin@yourdomain.com
- Phone: +254-XXX-XXX-XXX

---

**Remember:** Security is an ongoing process, not a one-time setup. Regular monitoring, updates, and testing are essential for maintaining a secure system.

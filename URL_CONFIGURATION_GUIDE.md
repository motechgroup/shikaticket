# URL Configuration Guide - Clean URLs Setup

## 🎯 Overview

Your ShikaTicket application now uses **clean URLs** without the `/public` folder visible in the URL path.

### Before:
- ❌ Local: `http://localhost/ticko/public/`
- ❌ Production: `https://shikaticket.com/public/`

### After:
- ✅ Local: `http://localhost/ticko/`
- ✅ Production: `https://shikaticket.com/`

---

## 🔧 How It Works

### 1. **Root `.htaccess` File**
Located at: `/.htaccess`

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to the public folder
    # This allows you to use http://localhost/ticko/ instead of http://localhost/ticko/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**What it does:**
- Intercepts all requests to your root directory
- Transparently redirects them to the `public/` folder
- Users never see `/public` in the URL

### 2. **Updated `base_url()` Function**
Located at: `/config/config.php`

The `base_url()` function has been updated to generate clean URLs:

```php
// Production: https://shikaticket.com/admin/login
// Local: http://localhost/ticko/admin/login
// Ngrok: https://xxxx.ngrok-free.app/ticko/admin/login
```

**Environment Detection:**
- **Production** (`shikaticket.com`): Base = `` (empty)
- **Ngrok**: Base = `/ticko`
- **Local**: Base = `/ticko`

---

## 🚀 Setup Instructions

### For Local Development (XAMPP/WAMP):

1. **Ensure mod_rewrite is enabled** in Apache:
   - Open `httpd.conf`
   - Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Remove the `#` to uncomment it
   - Restart Apache

2. **Ensure AllowOverride is set** in your Apache config:
   ```apache
   <Directory "C:/xampp/htdocs">
       AllowOverride All
       Require all granted
   </Directory>
   ```

3. **Access your application:**
   - Old URL: ~~`http://localhost/ticko/public/`~~
   - **New URL: `http://localhost/ticko/`** ✅

### For Production Server (cPanel/Shared Hosting):

1. **Upload the root `.htaccess` file** to your public_html or root directory

2. **Your document root should point to** the project root (not the `public` folder)

3. **Access your application:**
   - Old URL: ~~`https://shikaticket.com/public/`~~
   - **New URL: `https://shikaticket.com/`** ✅

### For VPS/Cloud Servers (Nginx/Apache):

#### Option A: Using .htaccess (Apache - Current Setup)
- The current `.htaccess` configuration will work automatically

#### Option B: Configure Document Root (Recommended for VPS)
Set your web server's document root directly to the `/public` folder:

**Apache Virtual Host:**
```apache
<VirtualHost *:80>
    ServerName shikaticket.com
    DocumentRoot /path/to/ticko/public
    
    <Directory /path/to/ticko/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name shikaticket.com;
    root /path/to/ticko/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📂 File Structure

```
ticko/
├── .htaccess                    # NEW: Redirects to public/ folder
├── app/                         # Application code
├── config/                      # Configuration files
│   └── config.php              # UPDATED: base_url() function
├── public/                      # Web root (hidden from URLs now)
│   ├── .htaccess               # Routes to index.php
│   ├── index.php               # Front controller
│   └── uploads/                # User uploads
├── vendor/                      # Dependencies
└── storage/                     # Application storage
```

---

## ✅ Testing Your Setup

### 1. **Test Clean URLs:**

Visit these URLs (without `/public`):

- **Homepage:** `http://localhost/ticko/`
- **Events:** `http://localhost/ticko/events`
- **Admin Login:** `http://localhost/ticko/admin/login`
- **User Login:** `http://localhost/ticko/login`

### 2. **Test Asset Loading:**

Check that assets load correctly:
- CSS files
- JavaScript files
- Images
- Uploaded files

### 3. **Test Redirects:**

Ensure redirects work properly:
- Login redirects
- Authentication guards
- Form submissions

---

## 🔍 Troubleshooting

### Issue: 404 Error - Page Not Found

**Solution:**
1. Ensure `mod_rewrite` is enabled in Apache
2. Check that `.htaccess` file exists in the root
3. Verify `AllowOverride All` is set in Apache config
4. Restart Apache

### Issue: Assets (CSS/JS/Images) Not Loading

**Solution:**
1. Check the browser console for 404 errors
2. Verify the `base_url()` function is generating correct URLs
3. Ensure the `public/.htaccess` file exists
4. Check file permissions

### Issue: "The requested URL was not found on this server"

**Solution:**
1. Make sure the root `.htaccess` file has proper permissions (644)
2. Verify Apache has read access to the `.htaccess` file
3. Check Apache error logs for more details

### Issue: Old `/public` URLs Still Being Used

**Solution:**
1. Clear browser cache
2. Clear application cache if any
3. Check for hardcoded URLs in the codebase
4. Verify the `base_url()` function is being used everywhere

---

## 🎨 URL Examples

### All User-Facing URLs:

| Feature | Old URL | New URL ✅ |
|---------|---------|-----------|
| Homepage | `http://localhost/ticko/public/` | `http://localhost/ticko/` |
| Events | `http://localhost/ticko/public/events` | `http://localhost/ticko/events` |
| Event Details | `http://localhost/ticko/public/events/show?id=1` | `http://localhost/ticko/events/show?id=1` |
| User Login | `http://localhost/ticko/public/login` | `http://localhost/ticko/login` |
| User Register | `http://localhost/ticko/public/register` | `http://localhost/ticko/register` |
| Admin Dashboard | `http://localhost/ticko/public/admin` | `http://localhost/ticko/admin` |
| Organizer Dashboard | `http://localhost/ticko/public/organizer` | `http://localhost/ticko/organizer` |
| Travel Portal | `http://localhost/ticko/public/travel` | `http://localhost/ticko/travel` |
| Scanner | `http://localhost/ticko/public/scanner` | `http://localhost/ticko/scanner` |

### Production URLs:

| Feature | New Production URL ✅ |
|---------|---------------------|
| Homepage | `https://shikaticket.com/` |
| Events | `https://shikaticket.com/events` |
| User Login | `https://shikaticket.com/login` |
| Admin Dashboard | `https://shikaticket.com/admin` |

---

## 🔐 Security Notes

1. **The `public/` folder is still the only web-accessible directory** - this is secure!
2. **All files outside `public/` remain protected** from direct web access
3. **The `.htaccess` file simply redirects traffic internally** to the `public/` folder
4. **No security is compromised** by this configuration

---

## 📝 Important Notes

1. **Always use `base_url()` function** when generating URLs in your code
2. **Never hardcode URLs** - this ensures consistency across environments
3. **The `/public` folder still exists** - it's just hidden from URLs now
4. **All existing functionality continues to work** - only URLs changed
5. **This is a standard Laravel/modern PHP framework approach**

---

## 🎯 Benefits of Clean URLs

✅ **Professional appearance** - No technical folders in URLs  
✅ **Better SEO** - Cleaner, more memorable URLs  
✅ **Easier to share** - Simpler URLs for users  
✅ **Industry standard** - Follows best practices  
✅ **Consistent experience** - Same structure across dev and production  

---

## 📞 Support

If you encounter any issues with the URL configuration:

1. Check Apache error logs: `/xampp/apache/logs/error.log` (XAMPP)
2. Enable debugging in `config/config.php`: `define('DEBUG_MODE', true);`
3. Test with a simple HTML file first to isolate `.htaccess` issues
4. Verify file permissions (`.htaccess` should be 644)

---

**Version:** 1.0.3  
**Last Updated:** <?php echo date('Y-m-d'); ?>  
**Status:** ✅ Active and Configured


# Production SMTP Configuration for ShikaTicket

## 🚀 Live Server SMTP Settings

### **Working Configuration (Tested & Verified):**
```
Host: mail.shikaticket.com
Port: 465
Encryption: SSL
Username: hello@shikaticket.com
Password: obedmokuamirera
From Email: hello@shikaticket.com
From Name: ShikaTicket
```

### **Admin Panel Configuration Steps:**

1. **Login to Admin Panel** → Go to Settings
2. **SMTP Configuration Section:**
   - **SMTP Host:** `mail.shikaticket.com`
   - **SMTP Port:** `465`
   - **Encryption:** `SSL` (NOT TLS)
   - **Username:** `hello@shikaticket.com`
   - **Password:** `obedmokuamirera`
   - **From Email:** `hello@shikaticket.com`
   - **From Name:** `ShikaTicket`

3. **Test Email Configuration:**
   - **Test Email:** `othedj@gmail.com`
   - Click "Send Test Email"

### **⚠️ Important Notes:**

- **Port 587 with TLS** ❌ - This configuration fails with "wrong version number" error
- **Port 465 with SSL** ✅ - This is the working configuration
- The system automatically detects local development and logs emails instead of sending
- On live server, emails will be sent via SMTP automatically

### **🔧 Technical Details:**

The `Mailer.php` class has been updated to:
- ✅ **Detect local development** (localhost, 127.0.0.1, ngrok)
- ✅ **Log emails locally** for development testing
- ✅ **Send real emails** on production server
- ✅ **Support SSL encryption** with Port 465
- ✅ **Handle authentication** properly

### **📧 Email Functionality:**

- ✅ **User Registration** emails
- ✅ **Password Reset** emails
- ✅ **Event Confirmations** emails
- ✅ **Travel Booking** confirmations
- ✅ **Admin Notifications** emails
- ✅ **Organizer Approvals** emails

### **🚀 Deployment Checklist:**

1. ✅ **Upload updated code** to live server
2. ✅ **Configure SMTP settings** in admin panel (Port 465, SSL)
3. ✅ **Test email sending** with test email
4. ✅ **Verify all email functions** work properly
5. ✅ **Monitor email logs** for any issues

### **🔍 Troubleshooting:**

If emails still don't work on live server:
1. Check server PHP version (requires PHP 7.4+)
2. Verify `openssl` extension is enabled
3. Check server firewall allows Port 465
4. Verify SMTP credentials are correct
5. Check server error logs for SMTP connection issues

---
**Version:** 1.0.3  
**Last Updated:** $(date)  
**Status:** Ready for Production Deployment

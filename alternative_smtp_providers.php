<?php
/**
 * Alternative SMTP Provider Configurations
 * Use these if your hosting provider blocks SMTP authentication
 */

// Option 1: Gmail SMTP (Free, Reliable)
$gmail_config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'your-gmail@gmail.com',
    'password' => 'your-app-password', // Use App Password, not regular password
    'from_email' => 'your-gmail@gmail.com',
    'from_name' => 'ShikaTicket'
];

// Option 2: SendGrid (Free tier: 100 emails/day)
$sendgrid_config = [
    'host' => 'smtp.sendgrid.net',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'apikey',
    'password' => 'your-sendgrid-api-key',
    'from_email' => 'hello@shikaticket.com',
    'from_name' => 'ShikaTicket'
];

// Option 3: Mailgun (Free tier: 5,000 emails/month)
$mailgun_config = [
    'host' => 'smtp.mailgun.org',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'postmaster@your-domain.mailgun.org',
    'password' => 'your-mailgun-smtp-password',
    'from_email' => 'hello@shikaticket.com',
    'from_name' => 'ShikaTicket'
];

// Option 4: Amazon SES (Pay-as-you-go, very cheap)
$ses_config = [
    'host' => 'email-smtp.us-east-1.amazonaws.com', // Change region as needed
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'your-ses-smtp-username',
    'password' => 'your-ses-smtp-password',
    'from_email' => 'hello@shikaticket.com',
    'from_name' => 'ShikaTicket'
];

// Option 5: Zoho Mail (Free tier: 25 emails/day)
$zoho_config = [
    'host' => 'smtp.zoho.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'hello@shikaticket.com',
    'password' => 'your-zoho-password',
    'from_email' => 'hello@shikaticket.com',
    'from_name' => 'ShikaTicket'
];

// Option 6: Use PHP mail() function (Fallback)
$php_mail_config = [
    'host' => '',
    'port' => '',
    'encryption' => 'none',
    'username' => '',
    'password' => '',
    'from_email' => 'hello@shikaticket.com',
    'from_name' => 'ShikaTicket'
];

echo "<h2>📧 Alternative SMTP Provider Configurations</h2>";
echo "<hr>";

echo "<h3>🚀 Quick Setup Options:</h3>";
echo "<strong>1. Gmail SMTP (Recommended for testing)</strong><br>";
echo "- Create Gmail account<br>";
echo "- Enable 2-factor authentication<br>";
echo "- Generate App Password<br>";
echo "- Use App Password in configuration<br><br>";

echo "<strong>2. SendGrid (Recommended for production)</strong><br>";
echo "- Sign up at sendgrid.com<br>";
echo "- Verify your domain<br>";
echo "- Create API key<br>";
echo "- Use SMTP settings above<br><br>";

echo "<strong>3. Contact TrueHost (Recommended first step)</strong><br>";
echo "- Ask them to enable SMTP authentication<br>";
echo "- Request removal of email restrictions<br>";
echo "- Get proper SMTP settings for your plan<br><br>";

echo "<h3>🔧 How to Configure:</h3>";
echo "1. Go to Admin Panel → Settings → SMTP<br>";
echo "2. Replace current settings with one of the configs above<br>";
echo "3. Test email sending<br>";
echo "4. If it works, you're good to go!<br><br>";

echo "<h3>💡 Pro Tips:</h3>";
echo "- Start with Gmail for quick testing<br>";
echo "- Use SendGrid for production (reliable, free tier)<br>";
echo "- Always contact hosting provider first<br>";
echo "- Keep your current domain email as 'from' address<br>";

?>

<?php
/**
 * Test the Actual Mailer Class with Gmail Settings
 * This will test the Mailer class directly with your Gmail configuration
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>📧 Testing Mailer Class with Gmail Settings</h2>";
echo "<hr>";

// Include necessary files
require_once 'config/config.php';

// Function to manually set Gmail settings in database
function setGmailSettings() {
    try {
        $settings = [
            'smtp.host' => 'smtp.gmail.com',
            'smtp.port' => '465',
            'smtp.username' => 'motechgroupkenya@gmail.com',
            'smtp.password' => 'your-app-password-here', // Replace with your actual app password
            'smtp.encryption' => 'ssl',
            'smtp.from_email' => 'motechgroupkenya@gmail.com',
            'smtp.from_name' => 'ShikaTicket'
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = db()->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
            $stmt->execute([$key, $value]);
        }
        
        echo "✅ Gmail settings saved to database<br>";
        return true;
    } catch (Exception $e) {
        echo "❌ Error saving settings: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Function to test Mailer class
function testMailerClass() {
    try {
        require_once 'app/Services/Mailer.php';
        $mailer = new \App\Services\Mailer();
        
        echo "<h3>Testing Mailer Class:</h3>";
        
        // Test sending an email
        $toEmail = 'othedj@gmail.com';
        $subject = 'Test Email from ShikaTicket';
        $htmlBody = '<h2>Test Email</h2><p>This is a test email from ShikaTicket system.</p>';
        
        echo "Sending test email to: $toEmail<br>";
        echo "Subject: $subject<br><br>";
        
        $result = $mailer->send($toEmail, $subject, $htmlBody);
        
        if ($result) {
            echo "✅ Email sent successfully!<br>";
        } else {
            echo "❌ Email sending failed!<br>";
        }
        
        return $result;
        
    } catch (Exception $e) {
        echo "❌ Error testing Mailer class: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Function to check current settings
function checkCurrentSettings() {
    try {
        echo "<h3>Current SMTP Settings in Database:</h3>";
        
        $stmt = db()->query("SELECT `key`, value FROM settings WHERE `key` LIKE 'smtp.%'");
        $settings = $stmt->fetchAll();
        
        if (empty($settings)) {
            echo "❌ No SMTP settings found in database!<br>";
            return false;
        }
        
        foreach ($settings as $setting) {
            $value = $setting['value'];
            if ($setting['key'] === 'smtp.password') {
                $value = $value ? '[HIDDEN - SET]' : 'NOT SET';
            }
            echo "- {$setting['key']}: $value<br>";
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "❌ Error checking settings: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Main execution
echo "<h3>Step 1: Check Current Settings</h3>";
$hasSettings = checkCurrentSettings();

echo "<br>";

if (!$hasSettings) {
    echo "<h3>Step 2: Set Gmail Settings</h3>";
    echo "⚠️ No SMTP settings found. You need to:<br>";
    echo "1. Go to Admin Panel → Settings → SMTP<br>";
    echo "2. Enter your Gmail settings:<br>";
    echo "   - Host: smtp.gmail.com<br>";
    echo "   - Port: 465<br>";
    echo "   - Username: motechgroupkenya@gmail.com<br>";
    echo "   - Password: [Your Gmail App Password]<br>";
    echo "   - Encryption: SSL<br>";
    echo "   - From Email: motechgroupkenya@gmail.com<br>";
    echo "   - From Name: ShikaTicket<br>";
    echo "3. Click 'Save Settings'<br>";
    echo "4. Then run this script again<br>";
} else {
    echo "<h3>Step 2: Test Mailer Class</h3>";
    $result = testMailerClass();
    
    if (!$result) {
        echo "<br><h3>🔧 Troubleshooting:</h3>";
        echo "1. <strong>Check Gmail App Password:</strong> Make sure you're using the correct App Password<br>";
        echo "2. <strong>Enable 2FA:</strong> Gmail requires 2-Factor Authentication for App Passwords<br>";
        echo "3. <strong>Check Server Logs:</strong> Look at error logs for detailed error messages<br>";
        echo "4. <strong>Test Gmail Settings:</strong> Try logging into Gmail with your App Password<br>";
    }
}

echo "<br>";

echo "<h3>📋 Gmail App Password Setup:</h3>";
echo "1. Go to <a href='https://myaccount.google.com/security' target='_blank'>Google Account Security</a><br>";
echo "2. Enable 2-Factor Authentication if not already enabled<br>";
echo "3. Go to 'App passwords' section<br>";
echo "4. Generate a new App Password for 'Mail'<br>";
echo "5. Use this 16-character password (not your regular Gmail password)<br>";
echo "6. Enter this App Password in your SMTP settings<br>";

?>

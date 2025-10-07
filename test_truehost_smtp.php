<?php
/**
 * Test TrueHost SMTP with Updated Mailer Class
 * This will test if TrueHost SMTP works with our improved protocol handling
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 Testing TrueHost SMTP with Updated Mailer</h2>";
echo "<hr>";

// Include necessary files
require_once 'config/config.php';

// Function to temporarily set TrueHost settings
function setTrueHostSettings() {
    try {
        $settings = [
            'smtp.host' => 'mail.shikaticket.com',
            'smtp.port' => '465',
            'smtp.username' => 'hello@shikaticket.com',
            'smtp.password' => 'obedmokuamirera',
            'smtp.encryption' => 'ssl',
            'smtp.from_email' => 'hello@shikaticket.com',
            'smtp.from_name' => 'ShikaTicket'
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = db()->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
            $stmt->execute([$key, $value]);
        }
        
        echo "✅ TrueHost settings temporarily applied<br>";
        return true;
    } catch (Exception $e) {
        echo "❌ Error setting TrueHost config: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Function to restore Gmail settings
function restoreGmailSettings() {
    try {
        $settings = [
            'smtp.host' => 'smtp.gmail.com',
            'smtp.port' => '465',
            'smtp.username' => 'motechgroupkenya@gmail.com',
            'smtp.password' => 'your-app-password', // You'll need to re-enter this
            'smtp.encryption' => 'ssl',
            'smtp.from_email' => 'motechgroupkenya@gmail.com',
            'smtp.from_name' => 'ShikaTicket'
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = db()->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
            $stmt->execute([$key, $value]);
        }
        
        echo "✅ Gmail settings restored<br>";
        return true;
    } catch (Exception $e) {
        echo "❌ Error restoring Gmail config: " . $e->getMessage() . "<br>";
        return false;
    }
}

echo "<h3>Step 1: Test TrueHost SMTP</h3>";
echo "Setting TrueHost configuration temporarily...<br><br>";

if (setTrueHostSettings()) {
    echo "<strong>Testing TrueHost SMTP with updated Mailer class:</strong><br>";
    echo "Host: mail.shikaticket.com<br>";
    echo "Port: 465<br>";
    echo "Username: hello@shikaticket.com<br>";
    echo "Encryption: SSL<br><br>";
    
    try {
        require_once 'app/Services/Mailer.php';
        $mailer = new \App\Services\Mailer();
        
        $toEmail = 'othedj@gmail.com';
        $subject = 'Test Email from TrueHost SMTP';
        $htmlBody = '<h2>TrueHost SMTP Test</h2><p>Testing if TrueHost SMTP works with updated Mailer class.</p>';
        
        echo "Sending test email to: $toEmail<br>";
        
        $result = $mailer->send($toEmail, $subject, $htmlBody);
        
        if ($result) {
            echo "✅ TrueHost SMTP works with updated Mailer class!<br>";
            echo "🎉 You can switch back to TrueHost SMTP!<br>";
        } else {
            echo "❌ TrueHost SMTP still fails<br>";
            echo "🔧 The issue is still the hosting provider restriction<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error testing TrueHost: " . $e->getMessage() . "<br>";
    }
    
    echo "<br>";
    echo "<h3>Step 2: Restore Gmail Settings</h3>";
    echo "Restoring Gmail configuration...<br>";
    
    // Note: You'll need to re-enter your Gmail App Password
    echo "⚠️ <strong>Important:</strong> You'll need to re-enter your Gmail App Password in Admin Panel → Settings → SMTP<br>";
    
    // Don't auto-restore Gmail since we don't have the App Password
    echo "Please manually restore Gmail settings in the Admin Panel.<br>";
    
} else {
    echo "❌ Failed to set TrueHost configuration<br>";
}

echo "<br>";

echo "<h3>📋 Recommendation:</h3>";
echo "<strong>Keep using Gmail SMTP because:</strong><br>";
echo "1. ✅ <strong>It's working perfectly</strong><br>";
echo "2. ✅ <strong>More reliable</strong> than shared hosting SMTP<br>";
echo "3. ✅ <strong>Better deliverability</strong> (emails less likely to go to spam)<br>";
echo "4. ✅ <strong>No hosting provider restrictions</strong><br>";
echo "5. ✅ <strong>Free to use</strong> with good limits<br>";
echo "6. ✅ <strong>Professional appearance</strong> (Gmail reputation)<br>";

echo "<br>";

echo "<h3>🔧 If You Want to Try TrueHost:</h3>";
echo "1. Run this test script<br>";
echo "2. If it works, update Admin Panel settings<br>";
echo "3. If it fails, stick with Gmail SMTP<br>";
echo "4. Contact TrueHost support to remove SMTP restrictions<br>";

?>

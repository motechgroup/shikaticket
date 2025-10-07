<?php
/**
 * Test Gmail SMTP Fix
 * This will test the updated Mailer class with proper EHLO response handling
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 Testing Gmail SMTP Fix</h2>";
echo "<hr>";

// Include necessary files
require_once 'config/config.php';
require_once 'app/Services/Mailer.php';

echo "<h3>Testing Updated Mailer Class:</h3>";

try {
    $mailer = new \App\Services\Mailer();
    
    // Test sending an email
    $toEmail = 'othedj@gmail.com';
    $subject = 'Test Email from ShikaTicket - Fixed Version';
    $htmlBody = '<h2>Test Email - Fixed!</h2><p>This is a test email from the updated ShikaTicket system.</p><p>Time: ' . date('Y-m-d H:i:s') . '</p>';
    
    echo "Sending test email to: $toEmail<br>";
    echo "Subject: $subject<br><br>";
    
    $result = $mailer->send($toEmail, $subject, $htmlBody);
    
    if ($result) {
        echo "✅ Email sent successfully!<br>";
        echo "🎉 The SMTP fix worked!<br>";
    } else {
        echo "❌ Email sending failed!<br>";
        echo "Check the server error logs for detailed error messages.<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br>";

echo "<h3>🔧 What Was Fixed:</h3>";
echo "1. <strong>EHLO Response Handling:</strong> Gmail sends multiple lines in EHLO response<br>";
echo "2. <strong>Proper Line Reading:</strong> Now reads all 250- lines until final 250 line<br>";
echo "3. <strong>Authentication Flow:</strong> Fixed the AUTH LOGIN sequence<br>";
echo "4. <strong>Error Logging:</strong> Added detailed logging for debugging<br>";

echo "<br>";

echo "<h3>📧 Next Steps:</h3>";
echo "1. If this test works, your email system is fixed!<br>";
echo "2. Test in Admin Panel → Settings → SMTP → Send Test Email<br>";
echo "3. Test user registration emails<br>";
echo "4. Test event booking confirmations<br>";

?>

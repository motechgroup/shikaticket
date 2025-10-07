<?php
/**
 * Check What SMTP Settings Are Actually Being Used
 * This will show what the system is reading from the database
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 SMTP Settings Debug - What is the system actually using?</h2>";
echo "<hr>";

// Include the necessary files to read settings
require_once 'config/config.php';

// Function to get settings (copied from Setting model)
function getSetting($key, $default = '') {
    try {
        $stmt = db()->prepare("SELECT value FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

echo "<h3>1. Database Settings (What's stored in database)</h3>";
$db_settings = [
    'smtp.host' => getSetting('smtp.host', 'NOT SET'),
    'smtp.port' => getSetting('smtp.port', 'NOT SET'),
    'smtp.username' => getSetting('smtp.username', 'NOT SET'),
    'smtp.password' => getSetting('smtp.password', 'NOT SET') ? '[HIDDEN - SET]' : 'NOT SET',
    'smtp.encryption' => getSetting('smtp.encryption', 'NOT SET'),
    'smtp.from_email' => getSetting('smtp.from_email', 'NOT SET'),
    'smtp.from_name' => getSetting('smtp.from_name', 'NOT SET')
];

foreach ($db_settings as $key => $value) {
    echo "<strong>$key:</strong> $value<br>";
}

echo "<br>";

echo "<h3>2. Test Mailer Class Constructor</h3>";
try {
    // Create a new Mailer instance to see what it loads
    require_once 'app/Services/Mailer.php';
    $mailer = new \App\Services\Mailer();
    
    // Use reflection to get private properties
    $reflection = new ReflectionClass($mailer);
    
    $properties = ['host', 'port', 'username', 'password', 'encryption', 'fromEmail', 'fromName'];
    foreach ($properties as $prop) {
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        $value = $property->getValue($mailer);
        if ($prop === 'password') {
            $value = $value ? '[HIDDEN - SET]' : 'NOT SET';
        }
        echo "<strong>$prop:</strong> $value<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error loading Mailer class: " . $e->getMessage() . "<br>";
}

echo "<br>";

echo "<h3>3. Raw Database Query</h3>";
try {
    $stmt = db()->query("SELECT `key`, value FROM settings WHERE `key` LIKE 'smtp.%'");
    $settings = $stmt->fetchAll();
    
    if (empty($settings)) {
        echo "❌ No SMTP settings found in database!<br>";
        echo "This means you need to save your SMTP settings in the admin panel first.<br>";
    } else {
        echo "✅ Found " . count($settings) . " SMTP settings:<br>";
        foreach ($settings as $setting) {
            $value = $setting['value'];
            if ($setting['key'] === 'smtp.password') {
                $value = $value ? '[HIDDEN - SET]' : 'NOT SET';
            }
            echo "- {$setting['key']}: $value<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br>";

echo "<h3>4. Test with Actual Gmail Settings</h3>";
echo "Let's test if Gmail SMTP works with your actual settings:<br><br>";

// Test Gmail connection with the settings from database
$gmail_host = getSetting('smtp.host', '');
$gmail_port = (int)getSetting('smtp.port', '587');
$gmail_username = getSetting('smtp.username', '');
$gmail_password = getSetting('smtp.password', '');
$gmail_encryption = getSetting('smtp.encryption', 'tls');

if ($gmail_host && $gmail_username && $gmail_password) {
    echo "Testing connection with database settings:<br>";
    echo "Host: $gmail_host<br>";
    echo "Port: $gmail_port<br>";
    echo "Username: $gmail_username<br>";
    echo "Encryption: $gmail_encryption<br><br>";
    
    // Test the connection
    $result = testGmailConnection($gmail_host, $gmail_port, $gmail_username, $gmail_password, $gmail_encryption);
    echo "<strong>Connection Result:</strong> " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "<br>";
} else {
    echo "❌ SMTP settings are incomplete in database!<br>";
    echo "Missing: ";
    if (!$gmail_host) echo "Host ";
    if (!$gmail_username) echo "Username ";
    if (!$gmail_password) echo "Password ";
    echo "<br>";
}

function testGmailConnection($host, $port, $username, $password, $encryption) {
    try {
        if ($encryption === 'ssl') {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            $socket = stream_socket_client(
                "ssl://{$host}:{$port}", 
                $errno, 
                $errstr, 
                10, 
                STREAM_CLIENT_CONNECT, 
                $context
            );
        } else {
            $socket = fsockopen($host, $port, $errno, $errstr, 10);
        }
        
        if (!$socket) {
            echo "Connection failed: $errstr ($errno)<br>";
            return false;
        }
        
        echo "✅ Connection established<br>";
        
        // Read greeting
        $greeting = fgets($socket, 512);
        echo "Greeting: " . trim($greeting) . "<br>";
        
        // EHLO
        fwrite($socket, "EHLO localhost\r\n");
        $ehlo = fgets($socket, 512);
        echo "EHLO: " . trim($ehlo) . "<br>";
        
        // Handle TLS if needed
        if ($encryption === 'tls') {
            fwrite($socket, "STARTTLS\r\n");
            $starttls = fgets($socket, 512);
            echo "STARTTLS: " . trim($starttls) . "<br>";
            
            if (strpos($starttls, '220') === 0) {
                if (stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    echo "✅ TLS enabled<br>";
                    fwrite($socket, "EHLO localhost\r\n");
                    fgets($socket, 512);
                } else {
                    echo "❌ TLS failed<br>";
                    fclose($socket);
                    return false;
                }
            }
        }
        
        // Authentication
        fwrite($socket, "AUTH LOGIN\r\n");
        $auth = fgets($socket, 512);
        echo "AUTH: " . trim($auth) . "<br>";
        
        if (strpos($auth, '334') === 0) {
            fwrite($socket, base64_encode($username) . "\r\n");
            $user_resp = fgets($socket, 512);
            echo "Username: " . trim($user_resp) . "<br>";
            
            fwrite($socket, base64_encode($password) . "\r\n");
            $pass_resp = fgets($socket, 512);
            echo "Password: " . trim($pass_resp) . "<br>";
            
            if (strpos($pass_resp, '235') === 0) {
                echo "✅ Authentication successful<br>";
                fwrite($socket, "QUIT\r\n");
                fclose($socket);
                return true;
            } else {
                echo "❌ Authentication failed<br>";
                fclose($socket);
                return false;
            }
        } else {
            echo "❌ AUTH LOGIN not supported<br>";
            fclose($socket);
            return false;
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "<br>";
        if (isset($socket)) {
            fclose($socket);
        }
        return false;
    }
}

echo "<br>";

echo "<h3>🔧 Troubleshooting Steps:</h3>";
echo "1. <strong>Check Admin Panel:</strong> Go to Settings → SMTP and verify your settings are saved<br>";
echo "2. <strong>Clear Cache:</strong> If using any caching, clear it<br>";
echo "3. <strong>Check Database:</strong> Make sure settings are actually stored in the database<br>";
echo "4. <strong>Test Gmail App Password:</strong> Make sure you're using an App Password, not your regular password<br>";
echo "5. <strong>Check 2FA:</strong> Gmail requires 2-Factor Authentication enabled for App Passwords<br>";

?>

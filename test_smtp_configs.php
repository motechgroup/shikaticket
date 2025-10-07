<?php
/**
 * Test Different SMTP Configurations
 * This will test various SMTP settings to find what works
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 SMTP Configuration Tests</h2>";
echo "<hr>";

// Test configurations
$configs = [
    [
        'name' => 'Current Config (SSL 465)',
        'host' => 'mail.shikaticket.com',
        'port' => 465,
        'encryption' => 'ssl',
        'username' => 'hello@shikaticket.com',
        'password' => 'obedmokuamirera'
    ],
    [
        'name' => 'Alternative (TLS 587)',
        'host' => 'mail.shikaticket.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'hello@shikaticket.com',
        'password' => 'obedmokuamirera'
    ],
    [
        'name' => 'No Encryption (Port 25)',
        'host' => 'mail.shikaticket.com',
        'port' => 25,
        'encryption' => 'none',
        'username' => 'hello@shikaticket.com',
        'password' => 'obedmokuamirera'
    ],
    [
        'name' => 'Gmail SSL (for comparison)',
        'host' => 'smtp.gmail.com',
        'port' => 465,
        'encryption' => 'ssl',
        'username' => 'test@gmail.com',
        'password' => 'test'
    ]
];

foreach ($configs as $config) {
    echo "<h3>Testing: {$config['name']}</h3>";
    echo "Host: {$config['host']}:{$config['port']}<br>";
    echo "Encryption: {$config['encryption']}<br>";
    echo "Username: {$config['username']}<br><br>";
    
    $result = testSMTPConnection($config);
    echo "<strong>Result: </strong>" . ($result ? "✅ SUCCESS" : "❌ FAILED") . "<br>";
    echo "<hr>";
}

function testSMTPConnection($config) {
    $host = $config['host'];
    $port = $config['port'];
    $username = $config['username'];
    $password = $config['password'];
    $encryption = $config['encryption'];
    
    try {
        // Test basic connection
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
            echo "❌ Connection failed: $errstr ($errno)<br>";
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

echo "<br><h3>💡 Next Steps:</h3>";
echo "1. Upload this file to your live server<br>";
echo "2. Run it in browser: https://yoursite.com/test_smtp_configs.php<br>";
echo "3. Check which configuration works<br>";
echo "4. Update your admin panel with working settings<br>";

?>

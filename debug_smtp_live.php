<?php
/**
 * SMTP Debug Script for Live Server
 * This will help identify the exact SMTP connection issue
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 SMTP Debug Report for Live Server</h2>";
echo "<hr>";

// 1. Check PHP Extensions
echo "<h3>1. PHP Extensions Check</h3>";
$required_extensions = ['openssl', 'sockets', 'curl'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅ Loaded" : "❌ Missing";
    echo "- $ext: $status<br>";
}
echo "<br>";

// 2. Check PHP Version
echo "<h3>2. PHP Version</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Required: PHP 7.4+ " . (version_compare(PHP_VERSION, '7.4.0') >= 0 ? "✅" : "❌") . "<br><br>";

// 3. Test SMTP Connection
echo "<h3>3. SMTP Connection Test</h3>";

// SMTP Configuration
$host = 'mail.shikaticket.com';
$port = 465;
$username = 'hello@shikaticket.com';
$password = 'obedmokuamirera';

echo "Testing connection to: $host:$port<br>";
echo "Username: $username<br>";
echo "Encryption: SSL<br><br>";

// Test SSL Connection
echo "<strong>Testing SSL Connection...</strong><br>";
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
        'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT
    ]
]);

$socket = @stream_socket_client(
    "ssl://{$host}:{$port}", 
    $errno, 
    $errstr, 
    30, 
    STREAM_CLIENT_CONNECT, 
    $context
);

if ($socket) {
    echo "✅ SSL Connection successful!<br>";
    
    // Read greeting
    $greeting = fgets($socket, 512);
    echo "Server greeting: " . trim($greeting) . "<br>";
    
    // Test EHLO
    fwrite($socket, "EHLO localhost\r\n");
    $ehlo_response = fgets($socket, 512);
    echo "EHLO response: " . trim($ehlo_response) . "<br>";
    
    // Test AUTH LOGIN
    fwrite($socket, "AUTH LOGIN\r\n");
    $auth_response = fgets($socket, 512);
    echo "AUTH LOGIN response: " . trim($auth_response) . "<br>";
    
    if (strpos($auth_response, '334') === 0) {
        // Send username
        fwrite($socket, base64_encode($username) . "\r\n");
        $user_response = fgets($socket, 512);
        echo "Username response: " . trim($user_response) . "<br>";
        
        // Send password
        fwrite($socket, base64_encode($password) . "\r\n");
        $pass_response = fgets($socket, 512);
        echo "Password response: " . trim($pass_response) . "<br>";
        
        if (strpos($pass_response, '235') === 0) {
            echo "✅ Authentication successful!<br>";
        } else {
            echo "❌ Authentication failed: " . trim($pass_response) . "<br>";
        }
    }
    
    fwrite($socket, "QUIT\r\n");
    fclose($socket);
    
} else {
    echo "❌ SSL Connection failed: $errstr ($errno)<br>";
    
    // Try alternative ports
    echo "<br><strong>Trying alternative configurations...</strong><br>";
    
    // Test Port 587 with TLS
    echo "Testing Port 587 with TLS...<br>";
    $socket587 = @fsockopen($host, 587, $errno587, $errstr587, 10);
    if ($socket587) {
        echo "✅ Port 587 connection successful<br>";
        $greeting = fgets($socket587, 512);
        echo "Server greeting: " . trim($greeting) . "<br>";
        fclose($socket587);
    } else {
        echo "❌ Port 587 failed: $errstr587 ($errno587)<br>";
    }
    
    // Test Port 25
    echo "Testing Port 25...<br>";
    $socket25 = @fsockopen($host, 25, $errno25, $errstr25, 10);
    if ($socket25) {
        echo "✅ Port 25 connection successful<br>";
        $greeting = fgets($socket25, 512);
        echo "Server greeting: " . trim($greeting) . "<br>";
        fclose($socket25);
    } else {
        echo "❌ Port 25 failed: $errstr25 ($errno25)<br>";
    }
}

echo "<br>";

// 4. Test with different SSL methods
echo "<h3>4. SSL Method Test</h3>";
$ssl_methods = [
    'STREAM_CRYPTO_METHOD_TLS_CLIENT' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
    'STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
    'STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT' => STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT,
    'STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT' => STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT,
    'STREAM_CRYPTO_METHOD_SSLv3_CLIENT' => STREAM_CRYPTO_METHOD_SSLv3_CLIENT,
    'STREAM_CRYPTO_METHOD_SSLv2_CLIENT' => STREAM_CRYPTO_METHOD_SSLv2_CLIENT
];

foreach ($ssl_methods as $name => $method) {
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
            'crypto_method' => $method
        ]
    ]);
    
    $socket = @stream_socket_client(
        "ssl://{$host}:{$port}", 
        $errno, 
        $errstr, 
        10, 
        STREAM_CLIENT_CONNECT, 
        $context
    );
    
    $status = $socket ? "✅ Works" : "❌ Failed";
    echo "- $name: $status<br>";
    
    if ($socket) {
        fclose($socket);
    }
}

echo "<br>";

// 5. Server Environment Info
echo "<h3>5. Server Environment</h3>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "OS: " . PHP_OS . "<br>";
echo "OpenSSL Version: " . (extension_loaded('openssl') ? OPENSSL_VERSION_TEXT : 'Not available') . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Timezone: " . date_default_timezone_get() . "<br><br>";

// 6. Firewall/Network Test
echo "<h3>6. Network Connectivity Test</h3>";
$test_hosts = [
    'mail.shikaticket.com:465',
    'mail.shikaticket.com:587', 
    'mail.shikaticket.com:25',
    'smtp.gmail.com:587',
    'smtp.gmail.com:465'
];

foreach ($test_hosts as $test_host) {
    list($host, $port) = explode(':', $test_host);
    $socket = @fsockopen($host, $port, $errno, $errstr, 5);
    $status = $socket ? "✅ Reachable" : "❌ Unreachable ($errstr)";
    echo "- $test_host: $status<br>";
    if ($socket) {
        fclose($socket);
    }
}

echo "<br><h3>🔧 Recommended Actions:</h3>";
echo "1. Check server error logs for detailed SMTP errors<br>";
echo "2. Verify firewall allows outbound connections on ports 465/587<br>";
echo "3. Contact hosting provider about SMTP restrictions<br>";
echo "4. Try alternative SMTP providers if current one is blocked<br>";
echo "5. Check if hosting provider requires specific SMTP settings<br>";

?>

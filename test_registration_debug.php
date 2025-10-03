<?php
// Test Registration Debug Script
// Upload this to your live server root and access it to debug registration issues
// Access: https://shikaticket.com/public/../test_registration_debug.php
// After testing, DELETE this file for security!

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Registration Debug Test</h1>";
echo "<hr>";

// Test 1: Check if session works
echo "<h2>1. Session Test</h2>";
session_start();
$_SESSION['test'] = 'working';
echo "Session status: " . (isset($_SESSION['test']) ? "✅ WORKING" : "❌ FAILED") . "<br>";
echo "Session ID: " . session_id() . "<br>";

// Test 2: Check base_url generation
echo "<hr><h2>2. Base URL Test</h2>";
require_once __DIR__ . '/config/config.php';
$testUrl = base_url('/user/dashboard');
echo "Generated URL: <code>{$testUrl}</code><br>";
echo "Expected: <code>https://shikaticket.com/public/user/dashboard</code><br>";

// Test 3: Check database connection
echo "<hr><h2>3. Database Connection Test</h2>";
try {
    $pdo = db();
    echo "Database connection: ✅ WORKING<br>";
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    echo "Users table: ✅ ACCESSIBLE (Total users: {$count})<br>";
} catch (Exception $e) {
    echo "Database connection: ❌ FAILED<br>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 4: Check if User model works
echo "<hr><h2>4. User Model Test</h2>";
try {
    require_once __DIR__ . '/app/Models/User.php';
    $testUser = \App\Models\User::findById(1);
    echo "User model: ✅ WORKING<br>";
    echo "Found user: " . ($testUser ? "Yes" : "No") . "<br>";
} catch (Exception $e) {
    echo "User model: ❌ FAILED<br>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 5: Check redirect function
echo "<hr><h2>5. Redirect Function Test</h2>";
ob_start(); // Capture output
$redirectWorked = false;
try {
    // Don't actually redirect, just check if function exists
    echo "Redirect function exists: " . (function_exists('redirect') ? "✅ YES" : "❌ NO") . "<br>";
    echo "base_url function exists: " . (function_exists('base_url') ? "✅ YES" : "❌ NO") . "<br>";
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}
ob_end_flush();

// Test 6: Check SessionSecurityService
echo "<hr><h2>6. Session Security Service Test</h2>";
try {
    require_once __DIR__ . '/app/Services/SessionSecurityService.php';
    echo "SessionSecurityService: ✅ LOADED<br>";
    
    // Test if method exists
    if (method_exists('\App\Services\SessionSecurityService', 'setUserSession')) {
        echo "setUserSession method: ✅ EXISTS<br>";
    } else {
        echo "setUserSession method: ❌ MISSING<br>";
    }
} catch (Exception $e) {
    echo "SessionSecurityService: ❌ FAILED<br>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 7: Check error log permissions
echo "<hr><h2>7. Error Log Test</h2>";
$logFile = __DIR__ . '/public/error_log';
if (file_exists($logFile)) {
    echo "Error log exists: ✅ YES<br>";
    echo "Error log writable: " . (is_writable($logFile) ? "✅ YES" : "❌ NO") . "<br>";
    echo "Last 10 lines of error log:<br><pre>";
    $lines = file($logFile);
    $lastLines = array_slice($lines, -10);
    foreach ($lastLines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
} else {
    echo "Error log: ❌ NOT FOUND<br>";
}

// Test 8: Check PHP version and extensions
echo "<hr><h2>8. PHP Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO: " . (extension_loaded('pdo') ? "✅ LOADED" : "❌ MISSING") . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? "✅ LOADED" : "❌ MISSING") . "<br>";
echo "Session: " . (extension_loaded('session') ? "✅ LOADED" : "❌ MISSING") . "<br>";

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ DELETE THIS FILE AFTER TESTING FOR SECURITY!</p>";
?>


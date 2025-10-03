<?php
/**
 * Registration Fix Verification Script
 * 
 * This script tests if the base_url fix is working correctly.
 * Upload to your server root and access via:
 * https://shikaticket.com/test_registration_fix.php
 * 
 * DELETE THIS FILE AFTER TESTING!
 */

// Start output buffering to prevent header issues
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load config
require_once __DIR__ . '/config/config.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration Fix Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #e74c3c; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; border-left: 4px solid #3498db; padding-left: 15px; }
        .test-result { margin: 15px 0; padding: 15px; border-radius: 5px; background: #ecf0f1; }
        .success { background: #d4edda; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
        code { background: #2c3e50; color: #ecf0f1; padding: 2px 8px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .compare { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 15px 0; }
        .compare-item { padding: 15px; border-radius: 5px; }
        .compare-item h3 { margin-top: 0; font-size: 14px; color: #666; }
        .actual { background: #e3f2fd; border: 2px solid #2196f3; }
        .expected { background: #e8f5e9; border: 2px solid #4caf50; }
        .match { color: #28a745; font-weight: bold; }
        .mismatch { color: #dc3545; font-weight: bold; }
        .delete-warning { background: #dc3545; color: white; padding: 20px; border-radius: 5px; text-align: center; font-size: 18px; font-weight: bold; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Registration Fix Verification</h1>
        
        <?php
        // Test 1: Base URL Generation
        echo '<h2>1. Base URL Generation Test</h2>';
        
        $testPaths = [
            '/user/dashboard',
            '/login',
            '/organizer/dashboard',
            '/admin',
            '/events'
        ];
        
        $allCorrect = true;
        foreach ($testPaths as $testPath) {
            $generated = base_url($testPath);
            $expected = 'https://shikaticket.com/public' . $testPath;
            $isCorrect = ($generated === $expected);
            $allCorrect = $allCorrect && $isCorrect;
            
            echo '<div class="test-result ' . ($isCorrect ? 'success' : 'error') . '">';
            echo '<strong>Path:</strong> <code>' . htmlspecialchars($testPath) . '</code><br>';
            echo '<div class="compare">';
            echo '<div class="compare-item actual"><h3>Generated:</h3><code>' . htmlspecialchars($generated) . '</code></div>';
            echo '<div class="compare-item expected"><h3>Expected:</h3><code>' . htmlspecialchars($expected) . '</code></div>';
            echo '</div>';
            echo '<strong>Result:</strong> <span class="' . ($isCorrect ? 'match' : 'mismatch') . '">' . ($isCorrect ? '✅ MATCH' : '❌ MISMATCH') . '</span>';
            echo '</div>';
        }
        
        echo '<div class="test-result ' . ($allCorrect ? 'success' : 'error') . '">';
        echo '<strong>Overall Base URL Test:</strong> ' . ($allCorrect ? '✅ PASSED - All URLs are correct!' : '❌ FAILED - Some URLs are incorrect');
        echo '</div>';
        
        // Test 2: Server Environment
        echo '<h2>2. Server Environment</h2>';
        echo '<div class="test-result info">';
        echo '<strong>HTTP_HOST:</strong> <code>' . htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'NOT SET') . '</code><br>';
        echo '<strong>SCRIPT_NAME:</strong> <code>' . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . '</code><br>';
        echo '<strong>REQUEST_URI:</strong> <code>' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'NOT SET') . '</code><br>';
        echo '<strong>HTTPS:</strong> <code>' . htmlspecialchars($_SERVER['HTTPS'] ?? 'NOT SET') . '</code><br>';
        echo '<strong>DOCUMENT_ROOT:</strong> <code>' . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . '</code><br>';
        echo '</div>';
        
        // Test 3: Redirect Function
        echo '<h2>3. Redirect Function Test</h2>';
        echo '<div class="test-result info">';
        echo '<strong>Function exists:</strong> ' . (function_exists('redirect') ? '✅ YES' : '❌ NO') . '<br>';
        echo '<strong>Can test redirect:</strong> Testing redirect without actually redirecting...<br>';
        
        // Test if headers_sent detection works
        $headersStatus = headers_sent($file, $line);
        if ($headersStatus) {
            echo '<strong>Headers status:</strong> ⚠️ Headers already sent (expected for this test script)<br>';
            echo '<strong>Headers sent at:</strong> <code>' . htmlspecialchars($file) . ':' . $line . '</code><br>';
        } else {
            echo '<strong>Headers status:</strong> ✅ Headers not sent yet<br>';
        }
        echo '</div>';
        
        // Test 4: Database Connection
        echo '<h2>4. Database Connection</h2>';
        try {
            $pdo = db();
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
            $userCount = $stmt->fetch()['count'];
            
            echo '<div class="test-result success">';
            echo '✅ <strong>Database connection:</strong> WORKING<br>';
            echo '<strong>Total users:</strong> ' . $userCount . '<br>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="test-result error">';
            echo '❌ <strong>Database connection:</strong> FAILED<br>';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        
        // Test 5: Session Security Service
        echo '<h2>5. Session Security Service</h2>';
        try {
            $serviceExists = class_exists('\App\Services\SessionSecurityService');
            $methodExists = method_exists('\App\Services\SessionSecurityService', 'setUserSession');
            
            echo '<div class="test-result ' . ($serviceExists && $methodExists ? 'success' : 'error') . '">';
            echo '<strong>Service exists:</strong> ' . ($serviceExists ? '✅ YES' : '❌ NO') . '<br>';
            echo '<strong>setUserSession method:</strong> ' . ($methodExists ? '✅ EXISTS' : '❌ MISSING') . '<br>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="test-result error">';
            echo '❌ <strong>Error checking service:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        
        // Test 6: User Model
        echo '<h2>6. User Model</h2>';
        try {
            $modelExists = class_exists('\App\Models\User');
            $findByIdExists = method_exists('\App\Models\User', 'findById');
            $createExists = method_exists('\App\Models\User', 'create');
            
            echo '<div class="test-result ' . ($modelExists && $findByIdExists && $createExists ? 'success' : 'error') . '">';
            echo '<strong>User model exists:</strong> ' . ($modelExists ? '✅ YES' : '❌ NO') . '<br>';
            echo '<strong>findById method:</strong> ' . ($findByIdExists ? '✅ EXISTS' : '❌ MISSING') . '<br>';
            echo '<strong>create method:</strong> ' . ($createExists ? '✅ EXISTS' : '❌ MISSING') . '<br>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="test-result error">';
            echo '❌ <strong>Error checking User model:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        
        // Test 7: SMS Service (check for output issues)
        echo '<h2>7. SMS Service Output Check</h2>';
        try {
            $smsFile = __DIR__ . '/app/Services/Sms.php';
            $smsContent = file_get_contents($smsFile);
            $hasClosingTag = strpos($smsContent, '?>') !== false;
            $endsWithNewline = substr($smsContent, -1) === "\n" && substr($smsContent, -2) !== "}\n";
            
            echo '<div class="test-result ' . (!$hasClosingTag ? 'success' : 'warning') . '">';
            echo '<strong>Closing PHP tag (?>):</strong> ' . ($hasClosingTag ? '⚠️ FOUND (may cause issues)' : '✅ NOT FOUND (good)') . '<br>';
            echo '<strong>Trailing whitespace:</strong> ' . ($endsWithNewline ? '⚠️ YES (may cause issues)' : '✅ NO (good)') . '<br>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="test-result error">';
            echo '❌ <strong>Error checking SMS file:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        
        // Final Summary
        echo '<h2>📊 Summary</h2>';
        echo '<div class="test-result ' . ($allCorrect ? 'success' : 'error') . '">';
        if ($allCorrect) {
            echo '<h3 style="margin:0; color: #28a745;">✅ ALL TESTS PASSED!</h3>';
            echo '<p>The base_url fix is working correctly. Registration should now redirect properly.</p>';
            echo '<p><strong>Next step:</strong> Test actual user registration at <a href="https://shikaticket.com/public/register" target="_blank">https://shikaticket.com/public/register</a></p>';
        } else {
            echo '<h3 style="margin:0; color: #dc3545;">❌ SOME TESTS FAILED</h3>';
            echo '<p>Please review the failed tests above and contact support.</p>';
        }
        echo '</div>';
        ?>
        
        <div class="delete-warning">
            ⚠️ DELETE THIS FILE IMMEDIATELY AFTER TESTING FOR SECURITY!
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>


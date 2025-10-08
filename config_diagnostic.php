<?php
// Config Diagnostic Script - Run this on your live server
echo "<h2>Config File Diagnostic</h2>";

$configPath = __DIR__ . '/config/config.php';

echo "<p><strong>Config file path:</strong> " . $configPath . "</p>";

if (file_exists($configPath)) {
    // Read the first 50 lines of the config file
    echo "<h3>First 50 lines of config.php:</h3>";
    echo "<pre style='background:#f5f5f5; padding:10px; border:1px solid #ddd;'>";
    
    $lines = file($configPath);
    $lineCount = 0;
    foreach ($lines as $lineNum => $line) {
        $lineCount++;
        if ($lineCount > 50) break;
        
        $lineNumber = $lineNum + 1;
        $line = htmlspecialchars($line);
        echo sprintf("%3d: %s", $lineNumber, $line);
    }
    echo "</pre>";
    
    // Check for APP_VERSION definition
    echo "<h3>Searching for APP_VERSION definition:</h3>";
    $content = file_get_contents($configPath);
    
    if (strpos($content, 'APP_VERSION') !== false) {
        echo "<p style='color:green'>✅ APP_VERSION found in config file</p>";
        
        // Find the specific line
        $lines = explode("\n", $content);
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, 'APP_VERSION') !== false) {
                $lineNumber = $lineNum + 1;
                echo "<p><strong>Line $lineNumber:</strong> " . htmlspecialchars($line) . "</p>";
            }
        }
    } else {
        echo "<p style='color:red'>❌ APP_VERSION NOT found in config file</p>";
    }
    
    // Check file size and modification time
    echo "<h3>File Information:</h3>";
    echo "<p><strong>File size:</strong> " . filesize($configPath) . " bytes</p>";
    echo "<p><strong>Last modified:</strong> " . date('Y-m-d H:i:s', filemtime($configPath)) . "</p>";
    
    // Try to include the config and check for errors
    echo "<h3>Including config file:</h3>";
    
    // Capture any output/errors
    ob_start();
    $oldErrorReporting = error_reporting(E_ALL);
    $oldDisplayErrors = ini_set('display_errors', 1);
    
    try {
        include $configPath;
        echo "<p style='color:green'>✅ Config file included successfully</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Error including config: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color:red'>❌ Fatal error including config: " . $e->getMessage() . "</p>";
    }
    
    $output = ob_get_clean();
    if (!empty($output)) {
        echo "<p><strong>Output from config file:</strong></p>";
        echo "<pre style='background:#fff3cd; padding:10px; border:1px solid #ffeaa7;'>" . htmlspecialchars($output) . "</pre>";
    }
    
    error_reporting($oldErrorReporting);
    ini_set('display_errors', $oldDisplayErrors);
    
    // Check if APP_VERSION is defined after include
    if (defined('APP_VERSION')) {
        echo "<p style='color:green'>✅ APP_VERSION is now defined: " . APP_VERSION . "</p>";
    } else {
        echo "<p style='color:red'>❌ APP_VERSION still not defined after include</p>";
    }
    
} else {
    echo "<p style='color:red'>❌ Config file not found!</p>";
}

echo "<hr>";
echo "<p><strong>Current time:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

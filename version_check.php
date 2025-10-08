<?php
// Version Check Script - Run this on your live server
echo "<h2>Version Check Results</h2>";

// Check if config file exists and is readable
$configPath = __DIR__ . '/config/config.php';
echo "<p><strong>Config file path:</strong> " . $configPath . "</p>";
echo "<p><strong>Config file exists:</strong> " . (file_exists($configPath) ? 'YES' : 'NO') . "</p>";

if (file_exists($configPath)) {
    echo "<p><strong>Config file readable:</strong> " . (is_readable($configPath) ? 'YES' : 'NO') . "</p>";
    
    // Include the config file
    require_once $configPath;
    
    echo "<p><strong>APP_VERSION defined:</strong> " . (defined('APP_VERSION') ? 'YES' : 'NO') . "</p>";
    
    if (defined('APP_VERSION')) {
        echo "<p><strong>APP_VERSION value:</strong> " . APP_VERSION . "</p>";
    } else {
        echo "<p><strong>Error:</strong> APP_VERSION constant is not defined after including config.php</p>";
    }
} else {
    echo "<p><strong>Error:</strong> config.php file not found!</p>";
}

// Check file modification time
echo "<p><strong>Config file last modified:</strong> " . date('Y-m-d H:i:s', filemtime($configPath)) . "</p>";

// Check if there are any PHP errors
if (function_exists('error_get_last')) {
    $error = error_get_last();
    if ($error) {
        echo "<p><strong>Last PHP Error:</strong> " . $error['message'] . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>Current time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Server timezone:</strong> " . date_default_timezone_get() . "</p>";
?>

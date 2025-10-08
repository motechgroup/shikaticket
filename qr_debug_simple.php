<?php
// Simple QR Debug Script
echo "<h2>QR Debug - Simple Check</h2>";

echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Test config file
echo "<h3>Testing Config File...</h3>";
$configPath = __DIR__ . '/config/config.php';
echo "<p><strong>Config path:</strong> " . $configPath . "</p>";
echo "<p><strong>Config exists:</strong> " . (file_exists($configPath) ? 'YES' : 'NO') . "</p>";

if (file_exists($configPath)) {
    echo "<p style='color:green'>✅ Config file found</p>";
    
    // Test database connection
    echo "<h3>Testing Database Connection...</h3>";
    try {
        require_once $configPath;
        
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        echo "<p style='color:green'>✅ Database connected successfully</p>";
        
        // Count tickets
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tickets");
        $ticketCount = $stmt->fetch()['count'];
        echo "<p><strong>Event tickets in database:</strong> {$ticketCount}</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM travel_tickets");
        $travelCount = $stmt->fetch()['count'];
        echo "<p><strong>Travel tickets in database:</strong> {$travelCount}</p>";
        
        // Check uploads directory
        echo "<h3>Checking Uploads Directory...</h3>";
        $uploadsDir = __DIR__ . '/uploads';
        echo "<p><strong>Uploads directory:</strong> {$uploadsDir}</p>";
        echo "<p><strong>Directory exists:</strong> " . (is_dir($uploadsDir) ? 'YES' : 'NO') . "</p>";
        
        if (is_dir($uploadsDir)) {
            echo "<p><strong>Directory writable:</strong> " . (is_writable($uploadsDir) ? 'YES' : 'NO') . "</p>";
            
            // Check qrs subdirectory
            $qrsDir = $uploadsDir . '/qrs';
            echo "<p><strong>QRs directory exists:</strong> " . (is_dir($qrsDir) ? 'YES' : 'NO') . "</p>";
            
            if (is_dir($qrsDir)) {
                $files = scandir($qrsDir);
                $qrFiles = array_filter($files, function($file) {
                    return pathinfo($file, PATHINFO_EXTENSION) === 'png';
                });
                echo "<p><strong>QR files found:</strong> " . count($qrFiles) . "</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Database Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ Config file not found!</p>";
}

// Test file operations
echo "<h3>Testing File Operations...</h3>";
$testFile = __DIR__ . '/test_write.txt';
$testContent = 'Test write at ' . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "<p style='color:green'>✅ Can write files</p>";
    unlink($testFile); // Clean up
} else {
    echo "<p style='color:red'>❌ Cannot write files</p>";
}

echo "<hr>";
echo "<p><strong>Debug completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

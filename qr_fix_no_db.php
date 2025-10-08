<?php
// QR Fix Without Database - Direct file operations
echo "<h2>QR Fix - Direct File Operations</h2>";

echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Create necessary directories
$uploadsDir = __DIR__ . '/uploads';
$qrsDir = $uploadsDir . '/qrs';
$travelQrsDir = $qrsDir . '/travel';

echo "<h3>Creating Directories...</h3>";

$directories = [
    $uploadsDir,
    $qrsDir,
    $travelQrsDir
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color:green'>✅ Created: " . basename($dir) . "</p>";
        } else {
            echo "<p style='color:red'>❌ Failed to create: " . basename($dir) . "</p>";
        }
    } else {
        echo "<p style='color:blue'>ℹ️ Directory exists: " . basename($dir) . "</p>";
    }
}

// Test QR generation
echo "<h3>Testing QR Generation...</h3>";

$testCodes = ['067312', 'TEST123', 'DEMO456'];

foreach ($testCodes as $code) {
    echo "<p>Generating QR for code: {$code}...</p>";
    
    try {
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
        $qrData = @file_get_contents($qrUrl);
        
        if ($qrData !== false) {
            $qrFile = $qrsDir . '/' . $code . '.png';
            
            if (file_put_contents($qrFile, $qrData)) {
                echo "<p style='color:green'>✅ Generated QR for {$code}</p>";
                echo "<p><img src='uploads/qrs/{$code}.png' alt='QR {$code}' style='width:100px; height:100px; border:1px solid #ccc;'></p>";
            } else {
                echo "<p style='color:red'>❌ Cannot write QR file for {$code}</p>";
            }
        } else {
            echo "<p style='color:red'>❌ Cannot fetch QR from API for {$code}</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Error generating QR for {$code}: " . $e->getMessage() . "</p>";
    }
}

// Test file permissions
echo "<h3>Testing File Permissions...</h3>";

$testFile = __DIR__ . '/test_permissions.txt';
$testContent = 'Permission test at ' . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "<p style='color:green'>✅ Can write files to root directory</p>";
    unlink($testFile);
} else {
    echo "<p style='color:red'>❌ Cannot write files to root directory</p>";
}

$testFileQrs = $qrsDir . '/test_permissions.txt';
if (file_put_contents($testFileQrs, $testContent)) {
    echo "<p style='color:green'>✅ Can write files to qrs directory</p>";
    unlink($testFileQrs);
} else {
    echo "<p style='color:red'>❌ Cannot write files to qrs directory</p>";
}

// Check existing QR files
echo "<h3>Checking Existing QR Files...</h3>";

if (is_dir($qrsDir)) {
    $files = scandir($qrsDir);
    $qrFiles = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'png';
    });
    
    echo "<p><strong>Existing QR files:</strong> " . count($qrFiles) . "</p>";
    
    if (count($qrFiles) > 0) {
        echo "<p><strong>Files:</strong></p>";
        echo "<ul>";
        foreach ($qrFiles as $file) {
            $filePath = $qrsDir . '/' . $file;
            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
            echo "<li>{$file} (" . number_format($fileSize) . " bytes)</li>";
        }
        echo "</ul>";
    }
}

// Test QR endpoint
echo "<h3>Testing QR Endpoint...</h3>";
$testEndpoint = '/tickets/qr?code=067312&v=' . time();
echo "<p><strong>Test URL:</strong> <a href='{$testEndpoint}' target='_blank'>{$testEndpoint}</a></p>";

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p>This script bypasses database issues and directly:</p>";
echo "<ul>";
echo "<li>✅ Creates necessary directories</li>";
echo "<li>✅ Tests QR generation from external API</li>";
echo "<li>✅ Tests file writing permissions</li>";
echo "<li>✅ Shows existing QR files</li>";
echo "<li>✅ Provides test endpoint URL</li>";
echo "</ul>";

echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>If QR generation works, the issue is database-related</li>";
echo "<li>If QR generation fails, check external API access</li>";
echo "<li>Test the QR endpoint link above</li>";
echo "<li>Check if existing QR files can be displayed</li>";
echo "</ol>";

echo "<p><strong>Script completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

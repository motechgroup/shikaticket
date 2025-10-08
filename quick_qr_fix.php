<?php
// Quick QR Fix - Minimal version
echo "<h2>Quick QR Fix</h2>";

// Just create the directories and test one QR generation
$uploadsDir = __DIR__ . '/uploads';
$qrsDir = $uploadsDir . '/qrs';

echo "<p>Creating directories...</p>";

if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
    echo "<p style='color:green'>✅ Created uploads directory</p>";
}

if (!is_dir($qrsDir)) {
    mkdir($qrsDir, 0777, true);
    echo "<p style='color:green'>✅ Created qrs directory</p>";
}

// Test QR generation
echo "<p>Testing QR generation...</p>";
$testCode = 'TEST123';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($testCode);
$qrData = @file_get_contents($qrUrl);

if ($qrData !== false) {
    $testFile = $qrsDir . '/test.png';
    if (file_put_contents($testFile, $qrData)) {
        echo "<p style='color:green'>✅ QR generation and file writing works!</p>";
        echo "<p><img src='uploads/qrs/test.png' alt='Test QR' style='width:100px;'></p>";
        unlink($testFile); // Clean up
    } else {
        echo "<p style='color:red'>❌ Cannot write QR files</p>";
    }
} else {
    echo "<p style='color:red'>❌ Cannot generate QR codes from external API</p>";
}

echo "<p><strong>Quick fix completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

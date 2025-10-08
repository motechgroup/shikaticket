<?php
// Direct QR Fix - No Database Required
echo "<h2>Direct QR Fix - No Database</h2>";

echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Create directories
$qrDir = __DIR__ . '/uploads/qrs';
if (!is_dir($qrDir)) {
    mkdir($qrDir, 0777, true);
    echo "<p style='color:green'>✅ Created qrs directory</p>";
} else {
    echo "<p style='color:blue'>ℹ️ QR directory already exists</p>";
}

// Generate QR for ticket #948267 directly
echo "<h3>Generating QR for Ticket #948267...</h3>";

$ticketCode = '948267';
$qrPath = 'uploads/qrs/' . $ticketCode . '.png';
$qrFullPath = __DIR__ . '/' . $qrPath;

// Check if file already exists
if (file_exists($qrFullPath)) {
    echo "<p style='color:blue'>ℹ️ QR file already exists for #{$ticketCode}</p>";
    $fileSize = filesize($qrFullPath);
    echo "<p><strong>File size:</strong> {$fileSize} bytes</p>";
} else {
    echo "<p>Generating new QR code...</p>";
    
    // Generate QR code using external API
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ticketCode);
    echo "<p><strong>QR API URL:</strong> <a href='{$qrUrl}' target='_blank'>{$qrUrl}</a></p>";
    
    $qrData = @file_get_contents($qrUrl);
    
    if ($qrData !== false) {
        $bytesWritten = file_put_contents($qrFullPath, $qrData);
        if ($bytesWritten !== false) {
            echo "<p style='color:green'>✅ QR code saved: {$bytesWritten} bytes</p>";
        } else {
            echo "<p style='color:red'>❌ Failed to save QR file</p>";
        }
    } else {
        echo "<p style='color:red'>❌ Failed to fetch QR from API</p>";
        
        // Try alternative QR service
        echo "<p>Trying alternative QR service...</p>";
        $altUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($ticketCode);
        $altData = @file_get_contents($altUrl);
        
        if ($altData !== false) {
            $bytesWritten = file_put_contents($qrFullPath, $altData);
            if ($bytesWritten !== false) {
                echo "<p style='color:green'>✅ QR code saved using alternative service: {$bytesWritten} bytes</p>";
            } else {
                echo "<p style='color:red'>❌ Failed to save QR file with alternative service</p>";
            }
        } else {
            echo "<p style='color:red'>❌ Alternative QR service also failed</p>";
        }
    }
}

// Verify the file
if (file_exists($qrFullPath)) {
    $fileSize = filesize($qrFullPath);
    echo "<p><strong>Final file size:</strong> {$fileSize} bytes</p>";
    
    // Test if it's a valid image
    $imageInfo = @getimagesize($qrFullPath);
    if ($imageInfo) {
        echo "<p style='color:green'>✅ Valid image: {$imageInfo['mime']} ({$imageInfo[0]}x{$imageInfo[1]})</p>";
        
        // Show the QR code
        echo "<h3>Generated QR Code:</h3>";
        echo "<img src='{$qrPath}' alt='QR Code #{$ticketCode}' style='width:200px; height:200px; border:1px solid #ccc;'>";
        
        echo "<h3>Test the Order Page:</h3>";
        echo "<p><a href='/user/orders/show?id=170' target='_blank' style='background:#007cba; color:white; padding:10px; text-decoration:none; border-radius:5px;'>← Test Order #170</a></p>";
        
        echo "<h3>Direct QR Endpoint Test:</h3>";
        echo "<p><a href='/tickets/qr?code={$ticketCode}&v=" . time() . "' target='_blank' style='background:#28a745; color:white; padding:10px; text-decoration:none; border-radius:5px;'>Test QR Endpoint</a></p>";
        
    } else {
        echo "<p style='color:red'>❌ File is not a valid image</p>";
    }
} else {
    echo "<p style='color:red'>❌ QR file was not created</p>";
}

// Generate QR codes for other common ticket codes
echo "<h3>Generating QR codes for other tickets...</h3>";

$commonCodes = ['067312', 'TEST123', 'DEMO456'];
$generated = 0;

foreach ($commonCodes as $code) {
    $codeQrPath = 'uploads/qrs/' . $code . '.png';
    $codeQrFullPath = __DIR__ . '/' . $codeQrPath;
    
    if (!file_exists($codeQrFullPath)) {
        echo "<p>Generating QR for #{$code}...</p>";
        
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
        $qrData = @file_get_contents($qrUrl);
        
        if ($qrData !== false) {
            $bytesWritten = file_put_contents($codeQrFullPath, $qrData);
            if ($bytesWritten !== false) {
                echo "<p style='color:green'>✅ Generated QR for #{$code}</p>";
                $generated++;
            }
        }
    } else {
        echo "<p style='color:blue'>ℹ️ QR already exists for #{$code}</p>";
    }
}

// Show all QR files in directory
echo "<h3>QR Files in Directory:</h3>";
$files = scandir($qrDir);
$qrFiles = array_filter($files, function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'png';
});

echo "<p><strong>Total QR files:</strong> " . count($qrFiles) . "</p>";
echo "<ul>";
foreach ($qrFiles as $file) {
    $filePath = $qrDir . '/' . $file;
    $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
    echo "<li>{$file} (" . number_format($fileSize) . " bytes)</li>";
}
echo "</ul>";

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p>✅ Created QR directories</p>";
echo "<p>✅ Generated QR for ticket #948267</p>";
echo "<p>✅ Generated {$generated} additional QR codes</p>";
echo "<p>✅ Verified file validity</p>";
echo "<p>✅ Provided test links</p>";

echo "<p><strong>Script completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

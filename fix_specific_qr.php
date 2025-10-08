<?php
// Fix Specific QR Code for Ticket #948267
echo "<h2>Fix Specific QR Code</h2>";

// Include config
require_once __DIR__ . '/config/config.php';

try {
    // Connect to database
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color:green'>✅ Database connected successfully</p>";
    
    // Find ticket #948267
    echo "<h3>Looking for Ticket #948267...</h3>";
    
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE code = '948267' LIMIT 1");
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ticket) {
        echo "<p style='color:green'>✅ Found ticket #948267</p>";
        echo "<p><strong>Current QR path:</strong> " . ($ticket['qr_path'] ?? 'NULL') . "</p>";
        
        // Generate QR code
        echo "<h3>Generating QR Code...</h3>";
        
        $qrDir = __DIR__ . '/uploads/qrs';
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0777, true);
            echo "<p style='color:green'>✅ Created qrs directory</p>";
        }
        
        $qrPath = 'uploads/qrs/948267.png';
        $qrFullPath = __DIR__ . '/' . $qrPath;
        
        // Generate QR code using external API
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode('948267');
        echo "<p>Fetching QR from: {$qrUrl}</p>";
        
        $qrData = @file_get_contents($qrUrl);
        
        if ($qrData !== false) {
            $bytesWritten = file_put_contents($qrFullPath, $qrData);
            if ($bytesWritten !== false) {
                echo "<p style='color:green'>✅ QR code saved: {$bytesWritten} bytes</p>";
                
                // Update database
                $updateStmt = $pdo->prepare("UPDATE tickets SET qr_path = ? WHERE code = ?");
                $updateStmt->execute([$qrPath, '948267']);
                echo "<p style='color:green'>✅ Database updated with QR path: {$qrPath}</p>";
                
                // Verify file
                if (file_exists($qrFullPath)) {
                    $fileSize = filesize($qrFullPath);
                    echo "<p style='color:green'>✅ File verified: {$fileSize} bytes</p>";
                    
                    // Test if it's a valid image
                    $imageInfo = @getimagesize($qrFullPath);
                    if ($imageInfo) {
                        echo "<p style='color:green'>✅ Valid image: {$imageInfo['mime']} ({$imageInfo[0]}x{$imageInfo[1]})</p>";
                        
                        // Show the QR code
                        echo "<h3>Generated QR Code:</h3>";
                        echo "<img src='{$qrPath}' alt='QR Code #948267' style='width:200px; height:200px; border:1px solid #ccc;'>";
                        
                        echo "<h3>Test Links:</h3>";
                        echo "<p><a href='/user/orders/show?id=170' target='_blank'>← Back to Order #170</a></p>";
                        echo "<p><a href='/tickets/qr?code=948267&v=" . time() . "' target='_blank'>Direct QR Endpoint</a></p>";
                        
                    } else {
                        echo "<p style='color:red'>❌ Generated file is not a valid image</p>";
                    }
                } else {
                    echo "<p style='color:red'>❌ File was not created</p>";
                }
                
            } else {
                echo "<p style='color:red'>❌ Failed to write QR file</p>";
            }
        } else {
            echo "<p style='color:red'>❌ Failed to fetch QR from external API</p>";
            echo "<p>This might be a network connectivity issue or API blocking</p>";
        }
        
    } else {
        echo "<p style='color:red'>❌ Ticket #948267 not found in database</p>";
        
        // Let's see what tickets exist
        echo "<h3>Available Tickets:</h3>";
        $stmt = $pdo->query("SELECT code, qr_path FROM tickets ORDER BY id DESC LIMIT 10");
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($tickets) {
            echo "<ul>";
            foreach ($tickets as $t) {
                echo "<li>Ticket #{$t['code']} - QR: {$t['qr_path']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No tickets found in database</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Script completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>

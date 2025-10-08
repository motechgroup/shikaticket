<?php
// Final QR Path Fix - Update database paths to match existing files
echo "<h2>Final QR Path Fix</h2>";

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
    
    // Fix Event Tickets QR Paths
    echo "<h3>Fixing Event Tickets QR Paths...</h3>";
    
    $stmt = $pdo->prepare("SELECT id, code, qr_path FROM tickets WHERE code IS NOT NULL AND code != ''");
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updated = 0;
    $alreadyCorrect = 0;
    
    foreach ($tickets as $ticket) {
        $code = $ticket['code'];
        $currentPath = $ticket['qr_path'];
        $correctPath = 'uploads/qrs/' . $code . '.png';
        
        // Check if file exists
        $fileExists = file_exists(__DIR__ . '/' . $correctPath);
        
        if ($fileExists) {
            if ($currentPath !== $correctPath) {
                // Update the path
                $updateStmt = $pdo->prepare("UPDATE tickets SET qr_path = ? WHERE id = ?");
                $updateStmt->execute([$correctPath, $ticket['id']]);
                echo "<p style='color:green'>✅ Updated ticket #{$code}: {$currentPath} → {$correctPath}</p>";
                $updated++;
            } else {
                echo "<p style='color:blue'>ℹ️ Ticket #{$code} path already correct</p>";
                $alreadyCorrect++;
            }
        } else {
            echo "<p style='color:orange'>⚠️ QR file missing for ticket #{$code}</p>";
        }
    }
    
    // Fix Travel Tickets QR Paths
    echo "<h3>Fixing Travel Tickets QR Paths...</h3>";
    
    $stmt = $pdo->prepare("SELECT id, ticket_code, qr_path FROM travel_tickets WHERE ticket_code IS NOT NULL AND ticket_code != ''");
    $stmt->execute();
    $travelTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($travelTickets as $ticket) {
        $code = $ticket['ticket_code'];
        $currentPath = $ticket['qr_path'];
        $correctPath = 'uploads/qrs/travel/' . $code . '.png';
        
        // Check if file exists
        $fileExists = file_exists(__DIR__ . '/' . $correctPath);
        
        if ($fileExists) {
            if ($currentPath !== $correctPath) {
                // Update the path
                $updateStmt = $pdo->prepare("UPDATE travel_tickets SET qr_path = ? WHERE id = ?");
                $updateStmt->execute([$correctPath, $ticket['id']]);
                echo "<p style='color:green'>✅ Updated travel ticket #{$code}: {$currentPath} → {$correctPath}</p>";
                $updated++;
            } else {
                echo "<p style='color:blue'>ℹ️ Travel ticket #{$code} path already correct</p>";
                $alreadyCorrect++;
            }
        } else {
            echo "<p style='color:orange'>⚠️ QR file missing for travel ticket #{$code}</p>";
        }
    }
    
    // Test specific ticket #067312
    echo "<h3>Testing Ticket #067312...</h3>";
    
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE code = '067312' LIMIT 1");
    $stmt->execute();
    $testTicket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testTicket) {
        echo "<p><strong>Ticket found:</strong> #{$testTicket['code']}</p>";
        echo "<p><strong>Current QR path:</strong> {$testTicket['qr_path']}</p>";
        
        $qrPath = $testTicket['qr_path'] ?? '';
        if ($qrPath) {
            $fullPath = __DIR__ . '/' . ltrim($qrPath, '/');
            echo "<p><strong>Full file path:</strong> {$fullPath}</p>";
            echo "<p><strong>File exists:</strong> " . (file_exists($fullPath) ? 'YES' : 'NO') . "</p>";
            
            if (file_exists($fullPath)) {
                $fileSize = filesize($fullPath);
                echo "<p><strong>File size:</strong> {$fileSize} bytes</p>";
                
                // Test if it's a valid image
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo) {
                    echo "<p><strong>Image type:</strong> {$imageInfo['mime']}</p>";
                    echo "<p><strong>Image dimensions:</strong> {$imageInfo[0]}x{$imageInfo[1]}</p>";
                    echo "<p style='color:green'>✅ QR file is valid!</p>";
                    
                    // Show the QR code
                    echo "<p><strong>QR Code Preview:</strong></p>";
                    echo "<img src='{$qrPath}' alt='QR Code #067312' style='width:150px; height:150px; border:1px solid #ccc;'>";
                } else {
                    echo "<p style='color:red'>❌ File is not a valid image</p>";
                }
            }
        }
    } else {
        echo "<p style='color:red'>❌ Ticket #067312 not found in database</p>";
    }
    
    echo "<hr>";
    echo "<h3>Summary</h3>";
    echo "<p><strong>Event tickets processed:</strong> " . count($tickets) . "</p>";
    echo "<p><strong>Travel tickets processed:</strong> " . count($travelTickets) . "</p>";
    echo "<p><strong>Paths updated:</strong> {$updated}</p>";
    echo "<p><strong>Already correct:</strong> {$alreadyCorrect}</p>";
    
    if ($updated > 0) {
        echo "<p style='color:green'><strong>🎉 QR paths updated successfully!</strong></p>";
    } else {
        echo "<p style='color:blue'><strong>ℹ️ All QR paths were already correct</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Script completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='/user/orders/show?id=167'>← Test Order Page</a></p>";
?>

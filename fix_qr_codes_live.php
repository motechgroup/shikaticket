<?php
// Fix QR Codes on Live Server
// This script will regenerate missing QR codes and fix QR paths

echo "<h2>QR Code Fix Script</h2>";

// Include the config file
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
    
    // Fix Event Tickets QR Codes
    echo "<h3>Fixing Event Tickets QR Codes...</h3>";
    
    $stmt = $pdo->prepare("SELECT id, code, qr_path FROM tickets WHERE code IS NOT NULL AND code != ''");
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $fixed = 0;
    $errors = 0;
    
    foreach ($tickets as $ticket) {
        $code = $ticket['code'];
        $currentQrPath = $ticket['qr_path'];
        
        echo "<p>Processing ticket #{$code}...</p>";
        
        // Check if QR file exists
        $qrExists = false;
        if ($currentQrPath) {
            $fullPath = __DIR__ . '/' . ltrim($currentQrPath, '/');
            $qrExists = file_exists($fullPath);
        }
        
        if (!$qrExists) {
            // Generate new QR code
            try {
                $qrDir = __DIR__ . '/uploads/qrs';
                if (!is_dir($qrDir)) {
                    mkdir($qrDir, 0777, true);
                }
                
                $newQrPath = 'uploads/qrs/' . $code . '.png';
                $newQrFullPath = __DIR__ . '/' . $newQrPath;
                
                // Generate QR code using external API
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                $qrData = @file_get_contents($qrUrl);
                
                if ($qrData !== false) {
                    file_put_contents($newQrFullPath, $qrData);
                    
                    // Update database
                    $updateStmt = $pdo->prepare("UPDATE tickets SET qr_path = ? WHERE id = ?");
                    $updateStmt->execute([$newQrPath, $ticket['id']]);
                    
                    echo "<p style='color:green'>✅ Generated QR for ticket #{$code}</p>";
                    $fixed++;
                } else {
                    echo "<p style='color:red'>❌ Failed to generate QR for ticket #{$code}</p>";
                    $errors++;
                }
            } catch (Exception $e) {
                echo "<p style='color:red'>❌ Error generating QR for ticket #{$code}: " . $e->getMessage() . "</p>";
                $errors++;
            }
        } else {
            echo "<p style='color:blue'>ℹ️ QR already exists for ticket #{$code}</p>";
        }
    }
    
    // Fix Travel Tickets QR Codes
    echo "<h3>Fixing Travel Tickets QR Codes...</h3>";
    
    $stmt = $pdo->prepare("SELECT id, ticket_code, qr_path FROM travel_tickets WHERE ticket_code IS NOT NULL AND ticket_code != ''");
    $stmt->execute();
    $travelTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($travelTickets as $ticket) {
        $code = $ticket['ticket_code'];
        $currentQrPath = $ticket['qr_path'];
        
        echo "<p>Processing travel ticket #{$code}...</p>";
        
        // Check if QR file exists
        $qrExists = false;
        if ($currentQrPath) {
            $fullPath = __DIR__ . '/' . ltrim($currentQrPath, '/');
            $qrExists = file_exists($fullPath);
        }
        
        if (!$qrExists) {
            // Generate new QR code
            try {
                $qrDir = __DIR__ . '/uploads/qrs/travel';
                if (!is_dir($qrDir)) {
                    mkdir($qrDir, 0777, true);
                }
                
                $newQrPath = 'uploads/qrs/travel/' . $code . '.png';
                $newQrFullPath = __DIR__ . '/' . $newQrPath;
                
                // Generate QR code using external API
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                $qrData = @file_get_contents($qrUrl);
                
                if ($qrData !== false) {
                    file_put_contents($newQrFullPath, $qrData);
                    
                    // Update database
                    $updateStmt = $pdo->prepare("UPDATE travel_tickets SET qr_path = ? WHERE id = ?");
                    $updateStmt->execute([$newQrPath, $ticket['id']]);
                    
                    echo "<p style='color:green'>✅ Generated QR for travel ticket #{$code}</p>";
                    $fixed++;
                } else {
                    echo "<p style='color:red'>❌ Failed to generate QR for travel ticket #{$code}</p>";
                    $errors++;
                }
            } catch (Exception $e) {
                echo "<p style='color:red'>❌ Error generating QR for travel ticket #{$code}: " . $e->getMessage() . "</p>";
                $errors++;
            }
        } else {
            echo "<p style='color:blue'>ℹ️ QR already exists for travel ticket #{$code}</p>";
        }
    }
    
    // Check directory permissions
    echo "<h3>Checking Directory Permissions...</h3>";
    
    $directories = [
        'uploads',
        'uploads/qrs',
        'uploads/qrs/travel',
        'uploads/site'
    ];
    
    foreach ($directories as $dir) {
        $fullDir = __DIR__ . '/' . $dir;
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0777, true);
            echo "<p style='color:green'>✅ Created directory: {$dir}</p>";
        } else {
            echo "<p style='color:blue'>ℹ️ Directory exists: {$dir}</p>";
        }
        
        // Check permissions
        if (is_writable($fullDir)) {
            echo "<p style='color:green'>✅ Directory is writable: {$dir}</p>";
        } else {
            echo "<p style='color:red'>❌ Directory is not writable: {$dir}</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Summary</h3>";
    echo "<p><strong>Event tickets processed:</strong> " . count($tickets) . "</p>";
    echo "<p><strong>Travel tickets processed:</strong> " . count($travelTickets) . "</p>";
    echo "<p><strong>QR codes fixed:</strong> {$fixed}</p>";
    echo "<p><strong>Errors:</strong> {$errors}</p>";
    
    if ($errors === 0) {
        echo "<p style='color:green'><strong>🎉 All QR codes fixed successfully!</strong></p>";
    } else {
        echo "<p style='color:orange'><strong>⚠️ Some errors occurred. Check the output above.</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Script completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='/'>← Back to Homepage</a></p>";
?>

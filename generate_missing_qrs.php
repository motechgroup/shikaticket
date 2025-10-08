<?php
// Generate Missing QR Codes - Bulk Fix
echo "<h2>Generate Missing QR Codes</h2>";

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
    
    // Ensure QR directory exists
    $qrDir = __DIR__ . '/uploads/qrs';
    if (!is_dir($qrDir)) {
        mkdir($qrDir, 0777, true);
        echo "<p style='color:green'>✅ Created qrs directory</p>";
    }
    
    // Find tickets with missing QR codes
    echo "<h3>Finding Tickets with Missing QR Codes...</h3>";
    
    $stmt = $pdo->prepare("
        SELECT id, code, qr_path 
        FROM tickets 
        WHERE code IS NOT NULL AND code != '' 
        ORDER BY id DESC
    ");
    $stmt->execute();
    $allTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Total tickets in database:</strong> " . count($allTickets) . "</p>";
    
    $missing = [];
    $existing = [];
    
    foreach ($allTickets as $ticket) {
        $code = $ticket['code'];
        $currentPath = $ticket['qr_path'];
        $expectedPath = 'uploads/qrs/' . $code . '.png';
        $fullPath = __DIR__ . '/' . $expectedPath;
        
        if (!file_exists($fullPath) || $currentPath !== $expectedPath) {
            $missing[] = $ticket;
        } else {
            $existing[] = $ticket;
        }
    }
    
    echo "<p><strong>Tickets with existing QR:</strong> " . count($existing) . "</p>";
    echo "<p><strong>Tickets missing QR:</strong> " . count($missing) . "</p>";
    
    if (count($missing) > 0) {
        echo "<h3>Generating Missing QR Codes...</h3>";
        
        $generated = 0;
        $failed = 0;
        
        foreach ($missing as $ticket) {
            $code = $ticket['code'];
            echo "<p>Processing ticket #{$code}...</p>";
            
            try {
                $qrPath = 'uploads/qrs/' . $code . '.png';
                $qrFullPath = __DIR__ . '/' . $qrPath;
                
                // Generate QR code using external API
                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($code);
                $qrData = @file_get_contents($qrUrl);
                
                if ($qrData !== false) {
                    $bytesWritten = file_put_contents($qrFullPath, $qrData);
                    if ($bytesWritten !== false) {
                        // Update database
                        $updateStmt = $pdo->prepare("UPDATE tickets SET qr_path = ? WHERE code = ?");
                        $updateStmt->execute([$qrPath, $code]);
                        
                        echo "<p style='color:green'>✅ Generated QR for #{$code} ({$bytesWritten} bytes)</p>";
                        $generated++;
                    } else {
                        echo "<p style='color:red'>❌ Failed to write QR for #{$code}</p>";
                        $failed++;
                    }
                } else {
                    echo "<p style='color:red'>❌ Failed to fetch QR for #{$code}</p>";
                    $failed++;
                }
                
                // Small delay to avoid overwhelming the API
                usleep(100000); // 0.1 second delay
                
            } catch (Exception $e) {
                echo "<p style='color:red'>❌ Error processing #{$code}: " . $e->getMessage() . "</p>";
                $failed++;
            }
        }
        
        echo "<hr>";
        echo "<h3>Generation Summary</h3>";
        echo "<p><strong>Successfully generated:</strong> {$generated}</p>";
        echo "<p><strong>Failed:</strong> {$failed}</p>";
        
        if ($generated > 0) {
            echo "<p style='color:green'><strong>🎉 Generated {$generated} QR codes successfully!</strong></p>";
        }
        
        if ($failed > 0) {
            echo "<p style='color:orange'><strong>⚠️ {$failed} QR codes failed to generate</strong></p>";
        }
        
    } else {
        echo "<p style='color:green'>✅ All tickets already have QR codes!</p>";
    }
    
    // Show recent tickets for verification
    echo "<h3>Recent Tickets Status:</h3>";
    $stmt = $pdo->query("SELECT code, qr_path FROM tickets ORDER BY id DESC LIMIT 5");
    $recentTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Ticket Code</th><th>QR Path</th><th>File Exists</th><th>Preview</th></tr>";
    
    foreach ($recentTickets as $ticket) {
        $code = $ticket['code'];
        $qrPath = $ticket['qr_path'];
        $fileExists = file_exists(__DIR__ . '/' . $qrPath);
        $existsText = $fileExists ? '✅ Yes' : '❌ No';
        $preview = $fileExists ? "<img src='{$qrPath}' style='width:50px; height:50px;'>" : 'N/A';
        
        echo "<tr>";
        echo "<td>{$code}</td>";
        echo "<td>{$qrPath}</td>";
        echo "<td>{$existsText}</td>";
        echo "<td>{$preview}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Script completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='/user/orders/show?id=170'>← Test Order #170</a></p>";
?>

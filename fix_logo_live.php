<?php
// Fix Logo Path on Live Server
// Run this script on your live server to fix the logo path

echo "<h2>Logo Path Fix Script</h2>";

// Include the config file to get database connection
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
    
    // Check current logo setting
    $stmt = $pdo->prepare("SELECT * FROM settings WHERE `key` = 'site.logo'");
    $stmt->execute();
    $logoSetting = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($logoSetting) {
        echo "<p><strong>Current logo path:</strong> " . htmlspecialchars($logoSetting['value']) . "</p>";
        
        // Check if the logo file exists
        $logoPath = __DIR__ . '/' . $logoSetting['value'];
        echo "<p><strong>Logo file path:</strong> " . $logoPath . "</p>";
        echo "<p><strong>Logo file exists:</strong> " . (file_exists($logoPath) ? 'YES' : 'NO') . "</p>";
        
        // Update to correct path
        $correctPath = 'uploads/site/logo.png';
        $correctFullPath = __DIR__ . '/' . $correctPath;
        
        echo "<p><strong>Correct logo path should be:</strong> " . $correctPath . "</p>";
        echo "<p><strong>Correct file exists:</strong> " . (file_exists($correctFullPath) ? 'YES' : 'NO') . "</p>";
        
        if (file_exists($correctFullPath)) {
            // Update the database
            $updateStmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = 'site.logo'");
            $updateStmt->execute([$correctPath]);
            
            echo "<p style='color:green'>✅ Logo path updated successfully to: " . $correctPath . "</p>";
            
            // Verify the update
            $stmt = $pdo->prepare("SELECT * FROM settings WHERE `key` = 'site.logo'");
            $stmt->execute();
            $updatedSetting = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p><strong>New logo path:</strong> " . htmlspecialchars($updatedSetting['value']) . "</p>";
            
        } else {
            echo "<p style='color:red'>❌ Logo file not found at: " . $correctPath . "</p>";
            echo "<p>Please make sure the logo file is uploaded to the uploads/site/ directory</p>";
        }
        
    } else {
        echo "<p style='color:red'>❌ No logo setting found in database</p>";
        
        // Insert new logo setting
        $correctPath = 'uploads/site/logo.png';
        $correctFullPath = __DIR__ . '/' . $correctPath;
        
        if (file_exists($correctFullPath)) {
            $insertStmt = $pdo->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?)");
            $insertStmt->execute(['site.logo', $correctPath]);
            echo "<p style='color:green'>✅ Logo setting created with path: " . $correctPath . "</p>";
        } else {
            echo "<p style='color:red'>❌ Logo file not found at: " . $correctPath . "</p>";
        }
    }
    
    // Also check and fix other URL settings that might have /public/
    echo "<h3>Checking other URL settings...</h3>";
    
    $urlSettings = [
        'site.url' => 'https://shikaticket.com/',
        'mpesa.callback_url' => 'https://shikaticket.com/payment/mpesa/callback',
        'scanner.app_url' => 'https://shikaticket.com/scanner'
    ];
    
    foreach ($urlSettings as $key => $correctValue) {
        $stmt = $pdo->prepare("SELECT * FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($setting) {
            $currentValue = $setting['value'];
            echo "<p><strong>$key:</strong> " . htmlspecialchars($currentValue) . "</p>";
            
            if (strpos($currentValue, '/public/') !== false) {
                $newValue = str_replace('/public/', '/', $currentValue);
                $updateStmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
                $updateStmt->execute([$newValue, $key]);
                echo "<p style='color:green'>✅ Updated $key to: " . $newValue . "</p>";
            } else {
                echo "<p style='color:blue'>ℹ️ $key is already correct</p>";
            }
        } else {
            echo "<p style='color:orange'>⚠️ Setting $key not found</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Script completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='/'>← Back to Homepage</a></p>";
?>

<?php
/**
 * Fix Duplicate Country Code in Travel Agency Phone Numbers
 * 
 * This script fixes phone numbers that have duplicate country codes
 * Example: "+254 254792758752" becomes "+254 792758752"
 * 
 * Run once, then DELETE this file!
 */

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html><html><head><title>Fix Agency Phone Numbers</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".container{max-width:800px;margin:0 auto;background:white;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo "h1{color:#dc2626;border-bottom:3px solid #dc2626;padding-bottom:10px;}";
echo ".success{background:#d4edda;border-left:4px solid #28a745;padding:15px;margin:15px 0;border-radius:4px;}";
echo ".warning{background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:15px 0;border-radius:4px;}";
echo ".info{background:#d1ecf1;border-left:4px solid #17a2b8;padding:15px;margin:15px 0;border-radius:4px;}";
echo "table{width:100%;border-collapse:collapse;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:12px;text-align:left;}";
echo "th{background:#dc2626;color:white;}";
echo "tr:nth-child(even){background:#f9f9f9;}";
echo ".delete-warning{background:#dc2626;color:white;padding:20px;border-radius:5px;text-align:center;font-weight:bold;margin-top:30px;}";
echo "</style></head><body><div class='container'>";

echo "<h1>🔧 Fix Travel Agency Phone Numbers</h1>";

try {
    $db = db();
    
    // Get all travel agencies
    $stmt = $db->query("SELECT id, company_name, phone, country FROM travel_agencies ORDER BY id");
    $agencies = $stmt->fetchAll();
    
    if (empty($agencies)) {
        echo "<div class='info'>ℹ️ No travel agencies found in the database.</div>";
        echo "</div></body></html>";
        exit;
    }
    
    echo "<div class='info'><strong>Found " . count($agencies) . " travel agencies.</strong> Checking phone numbers...</div>";
    
    // Country code mapping
    $countryCodeMap = [
        'Kenya' => '254',
        'Tanzania' => '255',
        'Uganda' => '256',
        'Rwanda' => '250',
        'South Africa' => '27',
        'Zambia' => '260',
        'Malawi' => '265'
    ];
    
    $fixed = 0;
    $skipped = 0;
    $results = [];
    
    foreach ($agencies as $agency) {
        $oldPhone = $agency['phone'];
        $newPhone = $oldPhone;
        $needsFixing = false;
        
        // Extract all digits
        $digits = preg_replace('/\D+/', '', $oldPhone);
        
        // Get country code for this country
        $countryCode = $countryCodeMap[$agency['country']] ?? null;
        
        if ($countryCode && strpos($digits, $countryCode) === 0) {
            // Check if country code appears twice
            $afterFirstCode = substr($digits, strlen($countryCode));
            
            if (strpos($afterFirstCode, $countryCode) === 0) {
                // Duplicate found! Remove the second occurrence
                $cleanDigits = substr($afterFirstCode, strlen($countryCode));
                $cleanDigits = ltrim($cleanDigits, '0');
                $newPhone = '+' . $countryCode . ' ' . $cleanDigits;
                $needsFixing = true;
            }
        }
        
        if ($needsFixing) {
            // Update the database
            $updateStmt = $db->prepare("UPDATE travel_agencies SET phone = ? WHERE id = ?");
            $updateStmt->execute([$newPhone, $agency['id']]);
            
            $results[] = [
                'company' => $agency['company_name'],
                'old' => $oldPhone,
                'new' => $newPhone,
                'status' => 'fixed'
            ];
            $fixed++;
        } else {
            $results[] = [
                'company' => $agency['company_name'],
                'old' => $oldPhone,
                'new' => $oldPhone,
                'status' => 'ok'
            ];
            $skipped++;
        }
    }
    
    // Show summary
    if ($fixed > 0) {
        echo "<div class='success'>";
        echo "<strong>✅ Success!</strong><br>";
        echo "Fixed: <strong>{$fixed}</strong> phone numbers<br>";
        echo "Skipped: <strong>{$skipped}</strong> (already correct)";
        echo "</div>";
    } else {
        echo "<div class='info'>";
        echo "<strong>✅ All phone numbers are correct!</strong><br>";
        echo "No changes needed.";
        echo "</div>";
    }
    
    // Show detailed results
    echo "<h2>📊 Detailed Results</h2>";
    echo "<table>";
    echo "<thead><tr><th>Company</th><th>Old Phone</th><th>New Phone</th><th>Status</th></tr></thead>";
    echo "<tbody>";
    
    foreach ($results as $result) {
        $statusColor = $result['status'] === 'fixed' ? '#28a745' : '#6c757d';
        $statusText = $result['status'] === 'fixed' ? '✅ Fixed' : '✓ OK';
        
        echo "<tr>";
        echo "<td>{$result['company']}</td>";
        echo "<td><code>{$result['old']}</code></td>";
        echo "<td><code>{$result['new']}</code></td>";
        echo "<td style='color:{$statusColor};font-weight:bold;'>{$statusText}</td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
    
    // Show what to do next
    if ($fixed > 0) {
        echo "<div class='warning'>";
        echo "<strong>⚠️ Next Steps:</strong><br>";
        echo "1. Verify the phone numbers look correct in the admin panel<br>";
        echo "2. Test SMS sending to these agencies<br>";
        echo "3. DELETE this file from your server for security";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='warning'>";
    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<div class='delete-warning'>";
echo "⚠️ IMPORTANT: DELETE THIS FILE AFTER RUNNING! ⚠️<br>";
echo "File: fix_agency_phone.php";
echo "</div>";

echo "</div></body></html>";
?>


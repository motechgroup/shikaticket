<?php
/**
 * Quick Check: Was Phone Verified?
 * 
 * This checks if the OTP verification actually worked (even if redirect failed)
 * Access: https://shikaticket.com/check_verification_status.php
 * DELETE after checking!
 */

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html><html><head><title>Verification Status Check</title>";
echo "<style>
body{font-family:Arial;padding:20px;background:#1a1a1a;color:#fff;}
.container{max-width:800px;margin:0 auto;background:#2a2a2a;padding:30px;border-radius:8px;}
h1{color:#22c55e;border-bottom:3px solid #22c55e;padding-bottom:10px;}
table{width:100%;border-collapse:collapse;margin:20px 0;background:#333;}
th,td{border:1px solid #555;padding:12px;text-align:left;}
th{background:#22c55e;color:#000;}
.verified{color:#22c55e;font-weight:bold;}
.not-verified{color:#dc2626;font-weight:bold;}
.approved{color:#3b82f6;font-weight:bold;}
.pending{color:#eab308;font-weight:bold;}
.delete-warning{background:#dc2626;color:white;padding:20px;border-radius:5px;text-align:center;font-weight:bold;margin-top:30px;}
</style></head><body><div class='container'>";

echo "<h1>✅ Travel Agency Verification Status</h1>";

try {
    $db = db();
    
    // Get all agencies with their status
    $agencies = $db->query("
        SELECT 
            id,
            company_name,
            phone,
            phone_verified,
            is_approved,
            is_active,
            created_at,
            updated_at
        FROM travel_agencies 
        ORDER BY id DESC
    ")->fetchAll();
    
    if (empty($agencies)) {
        echo "<p>No travel agencies found.</p>";
    } else {
        echo "<table>";
        echo "<thead><tr>";
        echo "<th>ID</th>";
        echo "<th>Company</th>";
        echo "<th>Phone</th>";
        echo "<th>Phone Verified</th>";
        echo "<th>Approved</th>";
        echo "<th>Active</th>";
        echo "<th>Created</th>";
        echo "</tr></thead><tbody>";
        
        foreach ($agencies as $agency) {
            $phoneStatus = $agency['phone_verified'] ? '<span class="verified">✅ Verified</span>' : '<span class="not-verified">❌ Not Verified</span>';
            $approvalStatus = $agency['is_approved'] ? '<span class="approved">✅ Approved</span>' : '<span class="pending">⏳ Pending</span>';
            $activeStatus = $agency['is_active'] ? '✅ Active' : '❌ Inactive';
            
            echo "<tr>";
            echo "<td>{$agency['id']}</td>";
            echo "<td>" . htmlspecialchars($agency['company_name']) . "</td>";
            echo "<td>" . htmlspecialchars($agency['phone']) . "</td>";
            echo "<td>{$phoneStatus}</td>";
            echo "<td>{$approvalStatus}</td>";
            echo "<td>{$activeStatus}</td>";
            echo "<td>{$agency['created_at']}</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        
        // Check for unverified agencies
        $unverified = array_filter($agencies, fn($a) => !$a['phone_verified']);
        
        if (!empty($unverified)) {
            echo "<div style='background:#4d3800;border-left:4px solid #eab308;padding:15px;margin:20px 0;border-radius:4px;'>";
            echo "<strong>⚠️ Unverified Agencies:</strong> " . count($unverified) . "<br><br>";
            echo "<strong>To manually verify (run in phpMyAdmin):</strong><br>";
            echo "<code style='background:#1a1a1a;padding:10px;display:block;margin-top:10px;'>";
            foreach ($unverified as $u) {
                echo "UPDATE travel_agencies SET phone_verified = 1 WHERE id = {$u['id']}; -- {$u['company_name']}<br>";
            }
            echo "</code>";
            echo "</div>";
        }
    }
    
    // Check recent error logs
    echo "<h2 style='margin-top:40px;color:#dc2626;'>Recent Error Log</h2>";
    $logFile = __DIR__ . '/public/error_log';
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $recent = array_slice($lines, -15);
        echo "<pre style='background:#1a1a1a;padding:15px;border-radius:5px;font-size:12px;overflow:auto;'>";
        foreach ($recent as $line) {
            echo htmlspecialchars($line);
        }
        echo "</pre>";
    } else {
        echo "<p>Error log not found.</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='background:#4d1e1e;border-left:4px solid #dc2626;padding:15px;margin:15px 0;'>";
    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<div class='delete-warning'>";
echo "⚠️ DELETE THIS FILE AFTER CHECKING! ⚠️<br>";
echo "File: check_verification_status.php";
echo "</div>";

echo "</div></body></html>";
?>


<?php
/**
 * Debug OTP Timing Issues
 * 
 * This script checks server time, timezone, and OTP expiration
 * Access: https://shikaticket.com/debug_otp_timing.php
 * DELETE after debugging!
 */

require_once __DIR__ . '/config/config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><title>OTP Timing Debug</title>";
echo "<style>
body{font-family:Arial;padding:20px;background:#1a1a1a;color:#fff;}
.container{max-width:900px;margin:0 auto;background:#2a2a2a;padding:30px;border-radius:8px;}
h1{color:#dc2626;border-bottom:3px solid #dc2626;padding-bottom:10px;}
.info{background:#1e3a5f;border-left:4px solid #3b82f6;padding:15px;margin:15px 0;border-radius:4px;}
.success{background:#1e4d2b;border-left:4px solid #22c55e;padding:15px;margin:15px 0;border-radius:4px;}
.warning{background:#4d3800;border-left:4px solid #eab308;padding:15px;margin:15px 0;border-radius:4px;}
.error{background:#4d1e1e;border-left:4px solid #dc2626;padding:15px;margin:15px 0;border-radius:4px;}
table{width:100%;border-collapse:collapse;margin:20px 0;background:#333;}
th,td{border:1px solid #555;padding:12px;text-align:left;}
th{background:#dc2626;color:white;}
code{background:#1a1a1a;padding:2px 6px;border-radius:3px;color:#22c55e;}
.delete-warning{background:#dc2626;color:white;padding:20px;border-radius:5px;text-align:center;font-weight:bold;margin-top:30px;}
</style></head><body><div class='container'>";

echo "<h1>🔍 OTP Timing Debug</h1>";

try {
    $db = db();
    
    // 1. Server Time Info
    echo "<h2>1. Server Time Configuration</h2>";
    echo "<div class='info'>";
    echo "<strong>PHP Timezone:</strong> " . date_default_timezone_get() . "<br>";
    echo "<strong>PHP Current Time:</strong> " . date('Y-m-d H:i:s') . "<br>";
    echo "<strong>PHP Timestamp:</strong> " . time() . "<br>";
    
    // MySQL time
    $mysqlTime = $db->query("SELECT NOW() as mysql_time, UNIX_TIMESTAMP(NOW()) as mysql_timestamp")->fetch();
    echo "<strong>MySQL Current Time:</strong> " . $mysqlTime['mysql_time'] . "<br>";
    echo "<strong>MySQL Timestamp:</strong> " . $mysqlTime['mysql_timestamp'] . "<br>";
    
    $timeDiff = abs(time() - $mysqlTime['mysql_timestamp']);
    if ($timeDiff > 60) {
        echo "<div class='warning'>⚠️ <strong>WARNING:</strong> PHP and MySQL time differ by {$timeDiff} seconds!</div>";
    } else {
        echo "<div class='success'>✅ PHP and MySQL time are synchronized (diff: {$timeDiff}s)</div>";
    }
    echo "</div>";
    
    // 2. Test OTP Generation
    echo "<h2>2. Test OTP Generation</h2>";
    echo "<div class='info'>";
    
    $testExpires = date('Y-m-d H:i:s', time() + 1800); // 30 minutes
    echo "<strong>Test OTP would expire at:</strong> " . $testExpires . "<br>";
    
    $checkQuery = $db->prepare("SELECT 
        ? as expires_at,
        NOW() as current_time,
        TIMESTAMPDIFF(MINUTE, NOW(), ?) as minutes_until_expiry
    ");
    $checkQuery->execute([$testExpires, $testExpires]);
    $result = $checkQuery->fetch();
    
    echo "<strong>Minutes until expiry:</strong> " . $result['minutes_until_expiry'] . "<br>";
    
    if ($result['minutes_until_expiry'] > 25 && $result['minutes_until_expiry'] <= 30) {
        echo "<div class='success'>✅ OTP expiration calculation working correctly!</div>";
    } else {
        echo "<div class='error'>❌ OTP expiration calculation FAILED! Should be ~30 minutes.</div>";
    }
    echo "</div>";
    
    // 3. Check Existing OTPs
    echo "<h2>3. Recent OTPs in Database</h2>";
    $otps = $db->query("
        SELECT 
            tat.*,
            ta.company_name,
            ta.phone,
            NOW() as current_time,
            TIMESTAMPDIFF(MINUTE, NOW(), tat.expires_at) as minutes_remaining,
            TIMESTAMPDIFF(SECOND, tat.created_at, NOW()) as seconds_since_created
        FROM travel_agency_tokens tat
        JOIN travel_agencies ta ON ta.id = tat.agency_id
        ORDER BY tat.id DESC
        LIMIT 5
    ")->fetchAll();
    
    if (empty($otps)) {
        echo "<div class='info'>ℹ️ No OTPs in database</div>";
    } else {
        echo "<table>";
        echo "<thead><tr>";
        echo "<th>Company</th>";
        echo "<th>Phone</th>";
        echo "<th>OTP</th>";
        echo "<th>Created</th>";
        echo "<th>Expires</th>";
        echo "<th>Age (sec)</th>";
        echo "<th>Remaining (min)</th>";
        echo "<th>Status</th>";
        echo "</tr></thead><tbody>";
        
        foreach ($otps as $otp) {
            $status = $otp['minutes_remaining'] > 0 ? '✅ Valid' : '❌ Expired';
            $statusClass = $otp['minutes_remaining'] > 0 ? 'success' : 'error';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($otp['company_name']) . "</td>";
            echo "<td>" . htmlspecialchars($otp['phone']) . "</td>";
            echo "<td><code>" . htmlspecialchars($otp['token']) . "</code></td>";
            echo "<td>" . $otp['created_at'] . "</td>";
            echo "<td>" . $otp['expires_at'] . "</td>";
            echo "<td>" . $otp['seconds_since_created'] . "s</td>";
            echo "<td>" . $otp['minutes_remaining'] . " min</td>";
            echo "<td><strong>{$status}</strong></td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        
        // Check if OTPs are expiring immediately
        $immediateExpiry = false;
        foreach ($otps as $otp) {
            if ($otp['seconds_since_created'] < 300 && $otp['minutes_remaining'] < 0) {
                $immediateExpiry = true;
                break;
            }
        }
        
        if ($immediateExpiry) {
            echo "<div class='error'>";
            echo "<strong>❌ PROBLEM DETECTED:</strong> OTPs are expiring immediately!<br>";
            echo "<strong>Likely cause:</strong> Server timezone mismatch or incorrect expiration calculation.<br>";
            echo "<strong>Solution:</strong> Files may not be uploaded yet, or server needs timezone configuration.";
            echo "</div>";
        }
    }
    
    // 4. Recommendations
    echo "<h2>4. Recommendations</h2>";
    echo "<div class='info'>";
    
    if ($timeDiff > 60) {
        echo "⚠️ <strong>Action Required:</strong><br>";
        echo "1. Set PHP timezone in php.ini or config.php: <code>date_default_timezone_set('Africa/Nairobi');</code><br>";
        echo "2. Or set MySQL timezone: <code>SET time_zone = '+03:00';</code><br>";
    } else {
        echo "✅ Time synchronization looks good<br>";
    }
    
    echo "<br><strong>Checklist:</strong><br>";
    echo "☐ Uploaded app/Controllers/TravelAuthController.php with 1800 seconds?<br>";
    echo "☐ Cleared browser cache?<br>";
    echo "☐ Tested with fresh OTP (click Resend)?<br>";
    echo "</div>";
    
    // 5. Manual OTP Check
    echo "<h2>5. Manual OTP Verification Test</h2>";
    echo "<div class='info'>";
    echo "To test an OTP manually, use this SQL query:<br><br>";
    echo "<code style='display:block;padding:10px;'>";
    echo "SELECT <br>";
    echo "&nbsp;&nbsp;token as otp,<br>";
    echo "&nbsp;&nbsp;expires_at,<br>";
    echo "&nbsp;&nbsp;NOW() as current_time,<br>";
    echo "&nbsp;&nbsp;TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_remaining<br>";
    echo "FROM travel_agency_tokens<br>";
    echo "WHERE token = 'YOUR_OTP_HERE';<br>";
    echo "</code>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<div class='delete-warning'>";
echo "⚠️ DELETE THIS FILE AFTER DEBUGGING! ⚠️<br>";
echo "File: debug_otp_timing.php";
echo "</div>";

echo "</div></body></html>";
?>


<?php
// Simple test to check admin sidebar detection
session_start();

echo "<h2>Admin Sidebar Debug Test</h2>";
echo "<pre>";

echo "SESSION DATA:\n";
echo "  \$_SESSION['admin']: " . ($_SESSION['admin'] ?? 'NOT SET') . "\n";
echo "  \$_SESSION['admin_id']: " . ($_SESSION['admin_id'] ?? 'NOT SET') . "\n";
echo "  \$_SESSION['role']: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
echo "\n";

echo "REQUEST INFO:\n";
echo "  REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "  SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "\n";

$currentUri = $_SERVER['REQUEST_URI'] ?? '';
$currentRole = $_SESSION['role'] ?? 'none';
$isInAdminPath = strpos($currentUri, '/admin') !== false;
$hasAdminRole = in_array($currentRole, ['admin', 'manager', 'accountant']);
$isAdminSidebar = $isInAdminPath && $hasAdminRole;

echo "SIDEBAR DETECTION:\n";
echo "  Current URI: $currentUri\n";
echo "  Current Role: $currentRole\n";
echo "  Is in /admin path: " . ($isInAdminPath ? 'YES' : 'NO') . "\n";
echo "  Has admin role: " . ($hasAdminRole ? 'YES' : 'NO') . "\n";
echo "  Should show admin sidebar: " . ($isAdminSidebar ? 'YES ✓' : 'NO ✗') . "\n";
echo "\n";

if (!$isAdminSidebar) {
    echo "WHY NOT SHOWING:\n";
    if (!$isInAdminPath) {
        echo "  ✗ Not in /admin path\n";
    }
    if (!$hasAdminRole) {
        echo "  ✗ Role '$currentRole' is not in ['admin', 'manager', 'accountant']\n";
    }
}

echo "</pre>";

echo "<hr>";
echo "<h3>Quick Actions:</h3>";
echo "<a href='public/admin/login'>Go to Admin Login</a><br>";
echo "<a href='public/admin'>Go to Admin Dashboard</a><br>";
?>



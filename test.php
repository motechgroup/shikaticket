<?php
echo "PHP is working!";
echo "<br>";
echo "Current directory: " . __DIR__;
echo "<br>";
echo "File exists check: " . (file_exists('index.php') ? 'YES' : 'NO');
echo "<br>";
echo "Config exists: " . (file_exists('config/config.php') ? 'YES' : 'NO');
?>

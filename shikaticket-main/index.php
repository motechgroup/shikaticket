<?php
// Redirect all requests at project root to the public front controller
// This ensures visiting /ticko/ opens the app homepage instead of a directory listing

// Compute the base path for this script (e.g., /ticko)
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($basePath === '') {
	$basePath = '/';
}

$target = rtrim($basePath, '/') . '/public/';

// Issue redirect to /public/
if (!headers_sent()) {
	header('Location: ' . $target, true, 302);
	exit;
}

// Fallback: run the app directly if headers already sent
require __DIR__ . '/public/index.php';



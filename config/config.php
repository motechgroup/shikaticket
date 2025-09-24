<?php
// Update credentials for your MySQL server
define('DB_HOST', 'localhost');
define('DB_NAME', 'ticko');
define('DB_USER', 'root');
define('DB_PASS', '');

function db(): PDO {
	static $pdo = null;
	if ($pdo === null) {
		$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		];
		$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
	}
	return $pdo;
}

function view(string $path, array $data = []): void {
	extract($data);
	$viewFile = __DIR__ . '/../app/Views/' . $path . '.php';
	include __DIR__ . '/../app/Views/layouts/main.php';
}

function redirect(string $to): void {
	header('Location: ' . $to);
	exit;
}

function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

function flash_set(string $key, string $message): void {
	$_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string {
	if (!empty($_SESSION['flash'][$key])) {
		$msg = $_SESSION['flash'][$key];
		unset($_SESSION['flash'][$key]);
		return $msg;
	}
	return null;
}

function csrf_token(): string {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(16));
	}
	return $_SESSION['csrf_token'];
}

function csrf_field(): string {
	return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

function verify_csrf(): void {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$token = $_POST['_token'] ?? '';
		if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
			http_response_code(419);
			echo 'CSRF token mismatch.';
			exit;
		}
	}
}

// Base URL helper (assumes project root points to /public)
function base_url(string $path = ''): string {
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
	$base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
	return $scheme . '://' . $host . $base . '/' . ltrim($path, '/');
}

// Simple guards
function require_admin(): void {
	if (($_SESSION['role'] ?? null) !== 'admin') {
		redirect(base_url('/admin/login'));
	}
}

function require_user(): void {
	if (!isset($_SESSION['user_id'])) {
		redirect(base_url('/login'));
	}
}

function require_organizer(): void {
	if (!isset($_SESSION['organizer_id'])) {
		redirect(base_url('/organizer/login'));
	}
}



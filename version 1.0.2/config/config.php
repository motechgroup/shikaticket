<?php
// Set timezone to match MySQL server (Africa/Nairobi = UTC+3)
date_default_timezone_set('Africa/Nairobi');

// Update credentials for your MySQL server
define('DB_HOST', 'localhost');
define('DB_NAME', 'ticko');
define('DB_USER', 'root');
define('DB_PASS', '');

// Security settings
define('ENVIRONMENT', $_ENV['APP_ENV'] ?? 'production'); // development, production
define('DEBUG_MODE', ENVIRONMENT === 'development');
// Application version
if (!defined('APP_VERSION')) { define('APP_VERSION', '1.0.2'); }

// Simple autoloader for security classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize secure session
if (session_status() === PHP_SESSION_NONE) {
    \App\Services\SessionSecurityService::initializeSecureSession();
}

// Initialize security middleware
\App\Middleware\SecurityMiddleware::addSecurityHeaders();
\App\Middleware\SecurityMiddleware::checkForSuspiciousActivity();
\App\Middleware\SecurityMiddleware::checkSessionSecurity();

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

function travel_view(string $path, array $data = []): void {
	extract($data);
	$viewFile = __DIR__ . '/../app/Views/' . $path . '.php';
	
	// Capture the view content
	ob_start();
	include $viewFile;
	$content = ob_get_clean();
	
	// Include the travel layout with the content
	include __DIR__ . '/../app/Views/layouts/travel.php';
}

function standalone_view(string $path, array $data = []): void {
	extract($data);
	$viewFile = __DIR__ . '/../app/Views/' . $path . '.php';
	include $viewFile;
}

function redirect(string $to): void {
    // Ensure absolute URL to avoid redirecting to localhost in proxied environments
    $scheme = parse_url($to, PHP_URL_SCHEME);
    if ($scheme === null) {
        // Treat as path; build absolute using base_url
        $to = base_url($to);
    }
    
    // Check if headers have already been sent
    if (headers_sent($file, $line)) {
        error_log("Cannot redirect to {$to} - headers already sent in {$file} on line {$line}");
        echo "<script>window.location.href='" . htmlspecialchars($to, ENT_QUOTES) . "';</script>";
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($to, ENT_QUOTES) . '"></noscript>';
        exit;
    }
    
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
	return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

function verify_csrf(): void {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($method === 'POST' || $method === 'PUT') {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        $token = '';
        if (stripos($contentType, 'application/json') !== false) {
            // Accept token via header for JSON requests
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            $token = $headers['X-CSRF-Token'] ?? $headers['X-Csrf-Token'] ?? '';
        } else {
            // Traditional form submission
            $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        }

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
	
	// Force HTTPS for ngrok URLs and production
	if (strpos($host, 'ngrok') !== false || strpos($host, 'shikaticket.com') !== false) {
		$scheme = 'https';
	}
	
	// Fix for localhost with extra dot
	if ($host === 'localhost.') {
		$host = 'localhost';
	}
	
	// Handle different environments
	$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
	$requestUri = $_SERVER['REQUEST_URI'] ?? '';
	
	// Detect base path
	$base = '';
	
	// Production server: shikaticket.com (always use /public)
	if (strpos($host, 'shikaticket.com') !== false) {
		$base = '/public';
	}
	// Ngrok or local with /ticko/public/
	elseif (strpos($host, 'ngrok') !== false) {
		$base = '/ticko/public';
	} 
	// Local development with /ticko/public/
	elseif (strpos($requestUri, '/ticko/public/') !== false || strpos($scriptName, '/ticko/public/') !== false) {
		$base = '/ticko/public';
	} 
	// Fallback: try to detect from SCRIPT_NAME
	elseif (strpos($scriptName, '/public/') !== false) {
		$base = '/public';
	}
	// Last resort: use dirname of script
	else {
		$base = rtrim(dirname($scriptName), '/');
		// Remove extra dots and ensure clean path
		$base = str_replace('/..', '', $base);
		if ($base === '.' || $base === '') {
			$base = '';
		}
	}
	
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

function require_travel_agency(): void {
    if (!isset($_SESSION['travel_agency_id'])) {
        redirect(base_url('/travel/login'));
    }
}



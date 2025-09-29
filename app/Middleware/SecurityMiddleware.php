<?php
namespace App\Middleware;

class SecurityMiddleware
{
    public static function checkSessionSecurity(): void
    {
        // Check session timeout for authenticated users
        if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id']) || 
            isset($_SESSION['organizer_id']) || isset($_SESSION['travel_agency_id'])) {
            
            if (!\App\Services\SessionSecurityService::checkSessionTimeout()) {
                \App\Services\SessionSecurityService::clearUserSession();
                flash_set('error', 'Session expired. Please login again.');
                
                // Redirect to appropriate login page
                if (strpos($_SERVER['REQUEST_URI'], '/admin') === 0) {
                    redirect(base_url('/admin/login'));
                } elseif (strpos($_SERVER['REQUEST_URI'], '/organizer') === 0) {
                    redirect(base_url('/organizer/login'));
                } elseif (strpos($_SERVER['REQUEST_URI'], '/travel') === 0) {
                    redirect(base_url('/travel/login'));
                } else {
                    redirect(base_url('/login'));
                }
            }
        }
    }

    public static function checkRateLimiting(string $action): void
    {
        if (\App\Services\RateLimitService::isBlocked($action)) {
            $remainingTime = \App\Services\RateLimitService::getTimeUntilReset($action);
            $message = "Rate limit exceeded. Please try again in {$remainingTime} seconds.";
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                http_response_code(429);
                echo json_encode(['error' => $message, 'retry_after' => $remainingTime]);
                exit;
            } else {
                flash_set('error', $message);
                redirect(base_url('/'));
            }
        }
    }

    public static function addSecurityHeaders(): void
    {
        // Add security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data:; " .
               "connect-src 'self'";
        
        header("Content-Security-Policy: {$csp}");
        
        // HTTPS enforcement
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            if (!strpos($_SERVER['HTTP_HOST'], 'localhost') && !strpos($_SERVER['HTTP_HOST'], 'ngrok')) {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            }
        }
    }

    public static function sanitizeInput(array &$input): void
    {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace("\0", '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Escape HTML for display (but allow safe HTML in rich content)
                if (!in_array($key, ['description', 'content', 'itinerary', 'includes', 'excludes', 'requirements'])) {
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
                
                $input[$key] = $value;
            }
        }
    }

    public static function logSecurityEvent(string $event, array $context = []): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'context' => $context
        ];

        // Log to file
        $logFile = __DIR__ . '/../../storage/security.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    }

    public static function checkForSuspiciousActivity(): void
    {
        $suspiciousPatterns = [
            '/\.\.\//',  // Directory traversal
            '/<script/i', // XSS attempts
            '/union\s+select/i', // SQL injection
            '/javascript:/i', // JavaScript injection
            '/eval\s*\(/i', // Code execution
        ];

        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $postData = $_POST;
        $getData = $_GET;

        $allData = $requestUri . ' ' . serialize($postData) . ' ' . serialize($getData);

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $allData)) {
                self::logSecurityEvent('suspicious_activity_detected', [
                    'pattern' => $pattern,
                    'uri' => $requestUri,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                // Block the request
                http_response_code(403);
                echo 'Access denied';
                exit;
            }
        }
    }
}

<?php
namespace App\Services;

class SessionSecurityService
{
    public static function initializeSecureSession(): void
    {
        // Set secure session configuration
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', self::isHttps());
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Set session timeout (2 hours)
        ini_set('session.gc_maxlifetime', 7200);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function regenerateSessionId(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
        }
    }

    public static function checkSessionTimeout(): bool
    {
        $timeout = 7200; // 2 hours
        
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                self::destroySession();
                return false;
            }
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }

    public static function setUserSession(array $userData, string $userType): void
    {
        self::regenerateSessionId();
        
        // Set user session data
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_type'] = $userType;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        
        // Set user-specific session data
        switch ($userType) {
            case 'admin':
                $_SESSION['admin_id'] = $userData['id'];
                $_SESSION['admin_email'] = $userData['email'];
                break;
            case 'organizer':
                $_SESSION['organizer_id'] = $userData['id'];
                $_SESSION['organizer_email'] = $userData['email'];
                break;
            case 'travel_agency':
                $_SESSION['travel_agency_id'] = $userData['id'];
                $_SESSION['travel_agency_email'] = $userData['email'];
                break;
            case 'user':
                $_SESSION['role'] = 'user';
                break;
        }
    }

    public static function clearUserSession(): void
    {
        // Clear all user session variables
        $sessionKeys = [
            'user_id', 'user_type', 'login_time', 'last_activity',
            'admin_id', 'admin_email',
            'organizer_id', 'organizer_email',
            'travel_agency_id', 'travel_agency_email', 'travel_agency_name',
            'role', 'scanner_device_id', 'scanner_device_name', 'scanner_device_code'
        ];
        
        foreach ($sessionKeys as $key) {
            unset($_SESSION[$key]);
        }
    }

    private static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
               $_SERVER['SERVER_PORT'] == 443 ||
               (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    }
}

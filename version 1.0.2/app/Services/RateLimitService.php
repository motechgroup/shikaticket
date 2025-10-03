<?php
namespace App\Services;

class RateLimitService
{
    private const RATE_LIMIT_FILE = __DIR__ . '/../../storage/rate_limits.json';
    private const DEFAULT_LIMITS = [
        'login_attempts' => ['max_attempts' => 5, 'window' => 900], // 5 attempts per 15 minutes
        'password_reset' => ['max_attempts' => 3, 'window' => 3600], // 3 attempts per hour
        'api_requests' => ['max_attempts' => 100, 'window' => 3600], // 100 requests per hour
        'file_uploads' => ['max_attempts' => 10, 'window' => 3600], // 10 uploads per hour
    ];

    public static function checkRateLimit(string $action, string $identifier = null): bool
    {
        $identifier = $identifier ?? self::getClientIdentifier();
        $limits = self::getRateLimits();
        
        if (!isset($limits[$action])) {
            return true; // No limit defined
        }

        $limit = $limits[$action];
        $key = $action . ':' . $identifier;
        
        $attempts = self::getAttempts($key, $limit['window']);
        
        return $attempts < $limit['max_attempts'];
    }

    public static function recordAttempt(string $action, string $identifier = null): void
    {
        $identifier = $identifier ?? self::getClientIdentifier();
        $key = $action . ':' . $identifier;
        $timestamp = time();
        
        $data = self::loadRateLimitData();
        if (!isset($data[$key])) {
            $data[$key] = [];
        }
        
        $data[$key][] = $timestamp;
        
        // Clean up old attempts
        $limits = self::getRateLimits();
        if (isset($limits[$action])) {
            $window = $limits[$action]['window'];
            $data[$key] = array_filter($data[$key], function($time) use ($window) {
                return $time > (time() - $window);
            });
        }
        
        self::saveRateLimitData($data);
    }

    public static function getRemainingAttempts(string $action, string $identifier = null): int
    {
        $identifier = $identifier ?? self::getClientIdentifier();
        $limits = self::getRateLimits();
        
        if (!isset($limits[$action])) {
            return 999; // No limit
        }

        $limit = $limits[$action];
        $key = $action . ':' . $identifier;
        $attempts = self::getAttempts($key, $limit['window']);
        
        return max(0, $limit['max_attempts'] - $attempts);
    }

    public static function getTimeUntilReset(string $action, string $identifier = null): int
    {
        $identifier = $identifier ?? self::getClientIdentifier();
        $limits = self::getRateLimits();
        
        if (!isset($limits[$action])) {
            return 0;
        }

        $limit = $limits[$action];
        $key = $action . ':' . $identifier;
        $data = self::loadRateLimitData();
        
        if (!isset($data[$key]) || empty($data[$key])) {
            return 0;
        }

        $oldestAttempt = min($data[$key]);
        return max(0, ($oldestAttempt + $limit['window']) - time());
    }

    public static function isBlocked(string $action, string $identifier = null): bool
    {
        return !self::checkRateLimit($action, $identifier);
    }

    private static function getClientIdentifier(): string
    {
        // Use IP address as identifier
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Use X-Forwarded-For if available (behind proxy)
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwardedIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($forwardedIps[0]);
        }
        
        return $ip;
    }

    private static function getRateLimits(): array
    {
        // Load from database or use defaults
        try {
            $stmt = db()->prepare("SELECT value FROM settings WHERE `key` = ?");
            $stmt->execute(['rate_limits']);
            $result = $stmt->fetch();
            
            if ($result) {
                return json_decode($result['value'], true) ?? self::DEFAULT_LIMITS;
            }
        } catch (\Exception $e) {
            // Fallback to defaults
        }
        
        return self::DEFAULT_LIMITS;
    }

    private static function getAttempts(string $key, int $window): int
    {
        $data = self::loadRateLimitData();
        
        if (!isset($data[$key])) {
            return 0;
        }

        $cutoff = time() - $window;
        return count(array_filter($data[$key], function($timestamp) use ($cutoff) {
            return $timestamp > $cutoff;
        }));
    }

    private static function loadRateLimitData(): array
    {
        if (!file_exists(self::RATE_LIMIT_FILE)) {
            return [];
        }

        $content = file_get_contents(self::RATE_LIMIT_FILE);
        return json_decode($content, true) ?? [];
    }

    private static function saveRateLimitData(array $data): void
    {
        // Ensure storage directory exists
        $storageDir = dirname(self::RATE_LIMIT_FILE);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        file_put_contents(self::RATE_LIMIT_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function cleanup(): void
    {
        $data = self::loadRateLimitData();
        $limits = self::getRateLimits();
        $now = time();
        
        foreach ($data as $key => $attempts) {
            $action = explode(':', $key)[0];
            if (isset($limits[$action])) {
                $window = $limits[$action]['window'];
                $data[$key] = array_filter($attempts, function($timestamp) use ($now, $window) {
                    return $timestamp > ($now - $window);
                });
                
                // Remove empty entries
                if (empty($data[$key])) {
                    unset($data[$key]);
                }
            }
        }
        
        self::saveRateLimitData($data);
    }
}

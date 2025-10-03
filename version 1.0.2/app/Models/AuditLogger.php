<?php
namespace App\Models;

class AuditLogger
{
    public static function log(string $category, string $message, array $meta = [], string $level = 'info'): void
    {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
            $url = substr((($_SERVER['REQUEST_METHOD'] ?? '') . ' ' . ($_SERVER['REQUEST_URI'] ?? '')), 0, 255);
            $userId = $_SESSION['user_id'] ?? ($_SESSION['organizer_id'] ?? ($_SESSION['travel_agency_id'] ?? null));
            $role = $_SESSION['role'] ?? (isset($_SESSION['organizer_id']) ? 'organizer' : (isset($_SESSION['travel_agency_id']) ? 'travel_agency' : null));
            $stmt = db()->prepare('INSERT INTO audit_logs(level,category,ip,user_agent,user_id,role,url,message,meta) VALUES(?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $level,
                $category,
                $ip,
                $ua,
                $userId,
                $role,
                $url,
                $message,
                empty($meta) ? null : json_encode($meta)
            ]);
        } catch (\Throwable $e) {
            // Fail silently to avoid impacting user flow
        }
    }
}



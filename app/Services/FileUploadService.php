<?php
namespace App\Services;

class FileUploadService
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png', 
        'image/gif',
        'image/webp'
    ];

    public static function validateImageUpload(array $file, array $allowedTypes = null, int $maxSize = null): array
    {
        $errors = [];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed';
            return $errors;
        }

        // Check file size
        $maxSize = $maxSize ?? self::MAX_FILE_SIZE;
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size of ' . self::formatBytes($maxSize);
        }

        // Check file extension
        $allowedTypes = $allowedTypes ?? self::ALLOWED_IMAGE_TYPES;
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes);
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES)) {
            $errors[] = 'Invalid file content type';
        }

        // Validate image dimensions and content
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors[] = 'File is not a valid image';
        }

        // Additional security: Check for executable content
        if (self::containsExecutableContent($file['tmp_name'])) {
            $errors[] = 'File contains potentially dangerous content';
        }

        return $errors;
    }

    public static function generateSecureFilename(string $originalName, string $prefix = ''): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        return $prefix . $timestamp . '_' . $random . '.' . $extension;
    }

    public static function moveUploadedFile(string $tmpPath, string $destination): bool
    {
        // Ensure destination directory exists
        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Move file with proper permissions
        if (move_uploaded_file($tmpPath, $destination)) {
            chmod($destination, 0644); // Read-only for others
            return true;
        }

        return false;
    }

    private static function containsExecutableContent(string $filePath): bool
    {
        $content = file_get_contents($filePath, false, null, 0, 1024); // Read first 1KB
        $dangerousPatterns = [
            '<?php',
            '<script',
            'javascript:',
            'vbscript:',
            'onload=',
            'onerror=',
            'eval(',
            'exec(',
            'system('
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

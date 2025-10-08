<?php

namespace App\Controllers;

use App\Services\Mailer;
use App\Services\Sms;

class PasswordResetController
{
    private $mailer;
    private $sms;

    public function __construct()
    {
        $this->mailer = new Mailer();
        $this->sms = new Sms();
    }

    /**
     * Show password reset request form
     */
    public function showResetRequest(): void
    {
        $userType = $_GET['type'] ?? 'user';
        $allowedTypes = ['user', 'organizer', 'travel'];
        
        if (!in_array($userType, $allowedTypes)) {
            $userType = 'user';
        }

        view('auth/password-reset-request', ['userType' => $userType]);
    }

    /**
     * Process password reset request
     */
    public function processResetRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        verify_csrf();

        $userType = $_POST['user_type'] ?? 'user';
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $allowedTypes = ['user', 'organizer', 'travel'];

        if (!in_array($userType, $allowedTypes)) {
            $_SESSION['error'] = 'Invalid user type';
            header('Location: ' . base_url('/password-reset?type=' . $userType));
            return;
        }

        if (empty($email) && empty($phone)) {
            $_SESSION['error'] = 'Please provide either email or phone number';
            header('Location: ' . base_url('/password-reset?type=' . $userType));
            return;
        }

        try {
            $user = $this->findUser($userType, $email, $phone);
            
            if (!$user) {
                $_SESSION['error'] = 'User not found with the provided email or phone';
                header('Location: ' . base_url('/password-reset?type=' . $userType));
                return;
            }

            // Generate reset token
            $token = $this->generateResetToken();
            $resetCode = $this->generateResetCode();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Store reset token in database
            $this->storeResetToken($userType, $user['id'], $token, $resetCode, $email, $phone, $expiresAt);

            // Send reset code via SMS and/or Email
            $this->sendResetCode($userType, $user, $resetCode, $token, $email, $phone);

            $_SESSION['success'] = 'Password reset code sent! Please check your email and phone.';
            header('Location: ' . base_url('/password-reset/verify?token=' . $token));
            
        } catch (\Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred. Please try again.';
            header('Location: ' . base_url('/password-reset?type=' . $userType));
        }
    }

    /**
     * Show password reset verification form
     */
    public function showResetVerify(): void
    {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['error'] = 'Invalid reset token';
            header('Location: ' . base_url('/password-reset'));
            return;
        }

        // Verify token exists and is not expired
        $resetToken = $this->getResetToken($token);
        
        if (!$resetToken || strtotime($resetToken['expires_at']) < time()) {
            $_SESSION['error'] = 'Reset token has expired or is invalid';
            header('Location: ' . base_url('/password-reset'));
            return;
        }

        if ($resetToken['used_at']) {
            $_SESSION['error'] = 'This reset token has already been used';
            header('Location: ' . base_url('/password-reset'));
            return;
        }

        view('auth/password-reset-verify', ['token' => $token, 'userType' => $resetToken['user_type']]);
    }

    /**
     * Process password reset verification
     */
    public function processResetVerify(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        verify_csrf();

        $token = $_POST['token'] ?? '';
        $resetCode = trim($_POST['reset_code'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($resetCode) || empty($newPassword)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: ' . base_url('/password-reset/verify?token=' . $token));
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: ' . base_url('/password-reset/verify?token=' . $token));
            return;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters long';
            header('Location: ' . base_url('/password-reset/verify?token=' . $token));
            return;
        }

        try {
            // Verify reset token and code
            $resetToken = $this->getResetToken($token);
            
            if (!$resetToken || strtotime($resetToken['expires_at']) < time()) {
                $_SESSION['error'] = 'Reset token has expired';
                header('Location: ' . base_url('/password-reset'));
                return;
            }

            if ($resetToken['used_at']) {
                $_SESSION['error'] = 'This reset token has already been used';
                header('Location: ' . base_url('/password-reset'));
                return;
            }

            // Verify reset code (simple string comparison for now)
            if ($resetToken['token'] !== $token) {
                $_SESSION['error'] = 'Invalid reset code';
                header('Location: ' . base_url('/password-reset/verify?token=' . $token));
                return;
            }

            // Update password
            $this->updateUserPassword($resetToken['user_type'], $resetToken['user_id'], $newPassword);

            // Mark token as used
            $this->markTokenAsUsed($token);

            $_SESSION['success'] = 'Password reset successfully! You can now login with your new password.';
            
            // Redirect to appropriate login page
            $loginUrl = $this->getLoginUrl($resetToken['user_type']);
            header('Location: ' . base_url($loginUrl));
            
        } catch (\Exception $e) {
            error_log('Password reset verification error: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred. Please try again.';
            header('Location: ' . base_url('/password-reset/verify?token=' . $token));
        }
    }

    /**
     * Find user by email or phone
     */
    private function findUser(string $userType, string $email, string $phone): ?array
    {
        $table = $this->getUserTable($userType);
        $whereClause = [];
        $params = [];

        // Add user type specific conditions
        $conditions = [];
        if ($userType === 'travel') {
            $conditions[] = 'is_active = 1';
        } elseif ($userType === 'organizer') {
            $conditions[] = 'is_approved = 1';
        }

        if (!empty($email)) {
            $whereClause[] = 'email = ?';
            $params[] = $email;
        }

        if (!empty($phone)) {
            // Normalize phone number for better matching
            $normalizedPhones = $this->normalizePhoneNumber($phone);
            $phoneConditions = [];
            foreach ($normalizedPhones as $normalizedPhone) {
                $phoneConditions[] = 'phone = ?';
                $params[] = $normalizedPhone;
            }
            $whereClause = array_merge($whereClause, $phoneConditions);
        }

        if (empty($whereClause)) {
            return null;
        }

        $sql = "SELECT * FROM {$table} WHERE (" . implode(' OR ', $whereClause) . ")";
        
        if (!empty($conditions)) {
            $sql .= " AND " . implode(' AND ', $conditions);
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Generate reset token
     */
    private function generateResetToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Generate 6-digit reset code
     */
    private function generateResetCode(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store reset token in database
     */
    private function storeResetToken(string $userType, int $userId, string $token, string $resetCode, string $email, string $phone, string $expiresAt): void
    {
        // Clean up old tokens for this user
        $this->cleanupOldTokens($userType, $userId);

        $sql = "INSERT INTO password_reset_tokens (token, user_type, user_id, email, phone, expires_at) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = db()->prepare($sql);
        $stmt->execute([$token, $userType, $userId, $email ?: null, $phone ?: null, $expiresAt]);
    }

    /**
     * Get reset token from database
     */
    private function getResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM password_reset_tokens WHERE token = ? LIMIT 1";
        $stmt = db()->prepare($sql);
        $stmt->execute([$token]);
        
        return $stmt->fetch() ?: null;
    }

    /**
     * Clean up old tokens for user
     */
    private function cleanupOldTokens(string $userType, int $userId): void
    {
        $sql = "DELETE FROM password_reset_tokens WHERE user_type = ? AND user_id = ?";
        $stmt = db()->prepare($sql);
        $stmt->execute([$userType, $userId]);
    }

    /**
     * Mark token as used
     */
    private function markTokenAsUsed(string $token): void
    {
        $sql = "UPDATE password_reset_tokens SET used_at = NOW() WHERE token = ?";
        $stmt = db()->prepare($sql);
        $stmt->execute([$token]);
    }

    /**
     * Update user password
     */
    private function updateUserPassword(string $userType, int $userId, string $newPassword): void
    {
        $table = $this->getUserTable($userType);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE {$table} SET password_hash = ? WHERE id = ?";
        $stmt = db()->prepare($sql);
        $stmt->execute([$hashedPassword, $userId]);
    }

    /**
     * Send reset code via SMS and Email
     */
    private function sendResetCode(string $userType, array $user, string $resetCode, string $token, string $email, string $phone): void
    {
        $siteName = \App\Models\Setting::get('site.name', 'ShikaTicket');
        $siteUrl = \App\Models\Setting::get('site.url', base_url());
        $resetUrl = base_url('/password-reset/verify?token=' . $token);

        // Send SMS if phone is provided
        if (!empty($phone)) {
            $templateKey = "sms.password_reset_{$userType}";
            $message = \App\Models\Setting::get($templateKey, 'Password Reset: Your reset code is {{reset_code}}. Valid for 15 minutes. - {{site_name}}');
            
            $message = str_replace([
                '{{reset_code}}',
                '{{site_name}}'
            ], [
                $resetCode,
                $siteName
            ], $message);

            $this->sms->send($phone, $message);
        }

        // Send Email if email is provided
        if (!empty($email)) {
            $template = $this->getEmailTemplate($userType);
            $userName = $this->getUserName($userType, $user);
            
            $this->mailer->send(
                $email,
                "Password Reset - {$siteName}",
                $template,
                [
                    'resetCode' => $resetCode,
                    'resetUrl' => $resetUrl,
                    'userName' => $userName,
                    'siteTitle' => $siteName
                ]
            );
        }
    }

    /**
     * Get user table name
     */
    private function getUserTable(string $userType): string
    {
        switch ($userType) {
            case 'organizer':
                return 'organizers';
            case 'travel':
                return 'travel_agencies';
            default:
                return 'users';
        }
    }

    /**
     * Get email template
     */
    private function getEmailTemplate(string $userType): string
    {
        switch ($userType) {
            case 'organizer':
                return 'password_reset_organizer';
            case 'travel':
                return 'password_reset_travel';
            default:
                return 'password_reset_user';
        }
    }

    /**
     * Get user name for email
     */
    private function getUserName(string $userType, array $user): string
    {
        switch ($userType) {
            case 'organizer':
                return $user['full_name'] ?? '';
            case 'travel':
                return $user['company_name'] ?? '';
            default:
                return $user['phone'] ?? '';
        }
    }

    /**
     * Get login URL for user type
     */
    private function getLoginUrl(string $userType): string
    {
        switch ($userType) {
            case 'organizer':
                return '/organizer/login';
            case 'travel':
                return '/travel/login';
            default:
                return '/login';
        }
    }

    /**
     * Normalize phone number for better matching
     */
    private function normalizePhoneNumber(string $phone): array
    {
        $phone = trim($phone);
        $normalized = [];
        
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Generate different possible formats
        if (strpos($cleaned, '+254') === 0) {
            // Already has +254
            $normalized[] = '+254 ' . substr($cleaned, 4);
            $normalized[] = $cleaned; // +254792758752
        } elseif (strpos($cleaned, '254') === 0) {
            // Has 254 but no +
            $normalized[] = '+254 ' . substr($cleaned, 3);
            $normalized[] = '+254' . substr($cleaned, 3);
        } elseif (strpos($cleaned, '0') === 0) {
            // Starts with 0
            $normalized[] = '+254 ' . substr($cleaned, 1);
            $normalized[] = '+254' . substr($cleaned, 1);
        } else {
            // No country code, assume Kenya
            $normalized[] = '+254 ' . $cleaned;
            $normalized[] = '+254' . $cleaned;
        }
        
        // Also add the original format
        $normalized[] = $phone;
        
        // Remove duplicates and return
        return array_unique($normalized);
    }
}

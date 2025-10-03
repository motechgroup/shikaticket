<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\Organizer;
use App\Services\Mailer;
use App\Services\EmailTemplates;

class AuthController
{
	public function loginUserForm(): void
	{
        redirect(base_url('/login-otp'));
	}

	public function registerUserForm(): void
	{
		view('auth/user-register');
	}

    public function loginUserOtpForm(): void
    {
        view('auth/user-login-otp');
    }

    public function loginUserOtpRequest(): void
    {
        \App\Middleware\SecurityMiddleware::checkRateLimiting('user_login_otp');
        $phone = trim($_POST['phone'] ?? '');
        if ($phone === '') { flash_set('error', 'Enter phone number'); redirect(base_url('/login-otp')); }
        // Normalize to international 254 format
        $norm = preg_replace('/\D+/', '', $phone);
        if ($norm !== '') {
            if (strpos($norm, '254') === 0) {
                // already normalized
            } elseif ($norm[0] === '0') {
                $norm = '254' . substr($norm, 1);
            } elseif (strlen($norm) === 9 && ($norm[0] === '7' || $norm[0] === '1')) {
                $norm = '254' . $norm;
            }
        }
        if ($norm === '') { flash_set('error', 'Invalid phone'); redirect(base_url('/login-otp')); }

        // Ensure user exists (auto-provision if not)
        $user = User::findByPhone($norm);
        if (!$user) {
            try {
                $randPass = bin2hex(random_bytes(6));
                $id = User::create($norm, '', $randPass);
                $user = User::findById($id);
            } catch (\Throwable $e) {
                flash_set('error', 'Could not create account.');
                redirect(base_url('/login-otp'));
            }
        }

        // Generate OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 600);
        try {
            db()->prepare("DELETE FROM user_tokens WHERE user_id = ? AND type = 'phone_otp'")->execute([(int)$user['id']]);
        } catch (\Throwable $e) {}
        try {
            db()->prepare("INSERT INTO user_tokens (user_id, token, type, expires_at) VALUES (?, ?, 'phone_otp', ?)")
                ->execute([(int)$user['id'], $otp, $expires]);
        } catch (\Throwable $e) {}

        // Send SMS
        $sent = false;
        try {
            $sms = new \App\Services\Sms();
            if ($sms->isConfigured()) {
                $msg = \App\Services\SmsTemplates::render('user_otp', ['otp' => $otp]);
                if ($msg === '') { $msg = 'Your ShikaTicket login code: ' . $otp . ' (10 min)'; }
                // Use dedicated OTP endpoint for TextSMS
                $sent = $sms->sendOtp($norm, $msg);
            }
        } catch (\Throwable $e) { $sent = false; }

        $_SESSION['tmp_user_phone'] = $norm;
        $_SESSION['pending_user_otp'] = $otp;
        $_SESSION['pending_user_otp_time'] = time();
        if ($sent) {
            flash_set('success', 'OTP sent to your phone.');
        } else {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                flash_set('warning', 'SMS not configured or sending failed. Use this OTP for testing: ' . $otp);
            } else {
                flash_set('warning', 'We could not send the OTP SMS right now. Please check your number and try again.');
            }
        }
        redirect(base_url('/login-otp/verify'));
    }

    public function loginUserOtpVerifyForm(): void
    {
        view('auth/user-verify-otp');
    }

    public function loginUserOtpVerify(): void
    {
        $otp = preg_replace('/\D+/', '', (string)($_POST['otp'] ?? ''));
        $phone = preg_replace('/\D+/', '', ($_SESSION['tmp_user_phone'] ?? (string)($_POST['phone'] ?? '')));
        if ($otp === '' || $phone === '') { redirect(base_url('/login-otp/verify')); }
        // Primary: verify by OTP only (latest unused), then bind to its user; avoids mismatched phone/user edge cases
        $stmt = db()->prepare("SELECT * FROM user_tokens WHERE token = ? AND type = 'phone_otp' AND used_at IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->execute([$otp]);
        $row = $stmt->fetch();
        // If not found, try scoped to the user's id as a secondary check (rare race conditions)
        if (!$row && $phone !== '') {
            $uTmp = User::findByPhone($phone);
            if ($uTmp) {
                $stmt2 = db()->prepare("SELECT * FROM user_tokens WHERE user_id = ? AND token = ? AND type = 'phone_otp' AND used_at IS NULL ORDER BY id DESC LIMIT 1");
                $stmt2->execute([(int)$uTmp['id'], $otp]);
                $row = $stmt2->fetch();
            }
        }
        if ($row) {
            $user = User::findById((int)$row['user_id']);
        } else {
            $user = null;
        }
        if (!$row) {
            // Last-resort fallback: accept session OTP if within 10 minutes
            $sessOtp = (string)($_SESSION['pending_user_otp'] ?? '');
            $sessTs = (int)($_SESSION['pending_user_otp_time'] ?? 0);
            if ($sessOtp !== '' && hash_equals($sessOtp, $otp) && (time() - $sessTs) <= 600) {
                // Bind to user by phone; create if missing
                $user = User::findByPhone($phone);
                if (!$user) {
                    $id = \App\Models\User::create($phone, '', bin2hex(random_bytes(6)));
                    $user = \App\Models\User::findById($id);
                }
                // proceed without token row
            } else {
                flash_set('error', 'Invalid code.');
                redirect(base_url('/login-otp/verify'));
            }
        }
        if (!$user) { flash_set('error', 'Invalid session.'); redirect(base_url('/login-otp')); }
        if (!empty($row['expires_at']) && strtotime($row['expires_at']) <= time()) {
            // Mark expired (do not mark used) and prompt resend
            flash_set('error', 'Code expired. Request a new one.');
            redirect(base_url('/login-otp'));
        }

        // Success: mark used and log user in
        db()->prepare('UPDATE user_tokens SET used_at = NOW() WHERE id = ?')->execute([(int)$row['id']]);
        \App\Services\SessionSecurityService::setUserSession($user, 'user');
        unset($_SESSION['tmp_user_phone']);
        // Award login points (configurable, once per day)
        try {
            $loginPts = (int)\App\Models\Setting::get('loyalty.points.login', '0');
            if ($loginPts > 0) {
                // Only once per calendar day per user
                $chk = db()->prepare('SELECT id FROM user_points WHERE user_id = ? AND reason = ? AND DATE(created_at) = CURDATE() LIMIT 1');
                $chk->execute([(int)$user['id'], 'Daily login']);
                if (!$chk->fetch()) {
                    db()->prepare('INSERT INTO user_points (user_id, points, reason, reference_type, reference_id, created_at) VALUES (?, ?, ?, NULL, NULL, NOW())')
                        ->execute([(int)$user['id'], $loginPts, 'Daily login']);
                }
            }
        } catch (\Throwable $e) { /* ignore */ }
        flash_set('success', 'Logged in.');
        redirect(base_url('/user/dashboard'));
    }

    public function loginUserOtpResend(): void
    {
        $phone = preg_replace('/\D+/', '', ($_SESSION['tmp_user_phone'] ?? (string)($_POST['phone'] ?? '')));
        if ($phone === '') { redirect(base_url('/login-otp')); }
        $user = User::findByPhone($phone);
        if (!$user) { redirect(base_url('/login-otp')); }
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 600);
        try { db()->prepare("DELETE FROM user_tokens WHERE user_id = ? AND type = 'phone_otp'")->execute([(int)$user['id']]); } catch (\Throwable $e) {}
        db()->prepare("INSERT INTO user_tokens (user_id, token, type, expires_at) VALUES (?, ?, 'phone_otp', ?)")
            ->execute([(int)$user['id'], $otp, $expires]);
        try {
            $sms = new \App\Services\Sms();
            if ($sms->isConfigured()) {
                $msg = \App\Services\SmsTemplates::render('user_otp', ['otp' => $otp]);
                if ($msg === '') { $msg = 'Your ShikaTicket login code: ' . $otp . ' (10 min)'; }
                $sms->send($phone, $msg);
            }
        } catch (\Throwable $e) {}
        flash_set('success', 'A new code was sent.');
        redirect(base_url('/login-otp/verify'));
    }

	public function forgotPasswordForm(): void
	{
		view('auth/forgot-password');
	}

	public function sendPasswordReset(): void
	{
		$email = trim($_POST['email'] ?? '');
		if ($email === '') { redirect(base_url('/password/forgot')); }
		$uStmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
		$uStmt->execute([$email]);
		$user = $uStmt->fetch();
		if ($user) {
			$token = bin2hex(random_bytes(16));
			$expires = date('Y-m-d H:i:s', time() + 3600);
			$stmt = db()->prepare('INSERT INTO user_tokens (user_id, token, type, expires_at) VALUES (?, ?, ?, ?)');
			$stmt->execute([$user['id'], $token, 'password_reset', $expires]);
			$link = base_url('/password/reset?token=' . urlencode($token));
			$mailer = new Mailer();
			$html = \App\Services\EmailTemplates::render('password_reset', ['reset_link' => $link]);
			if ($html !== '') { $mailer->send($email, 'Password Reset', $html); }
		}
		flash_set('success', 'If your email exists, a reset link has been sent.');
		redirect(base_url('/password/forgot'));
	}

	public function resetPasswordForm(): void
	{
		$token = $_GET['token'] ?? '';
		view('auth/reset-password', compact('token'));
	}

	public function resetPassword(): void
	{
		$token = trim($_POST['token'] ?? '');
		$password = $_POST['password'] ?? '';
		if ($token === '' || $password === '') { redirect(base_url('/password/forgot')); }
		$stmt = db()->prepare("SELECT * FROM user_tokens WHERE token = ? AND type = 'password_reset' AND used_at IS NULL AND expires_at > NOW() LIMIT 1");
		$stmt->execute([$token]);
		$row = $stmt->fetch();
		if (!$row) { flash_set('error', 'Invalid or expired token.'); redirect(base_url('/password/forgot')); }
		$hash = password_hash($password, PASSWORD_DEFAULT);
		db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $row['user_id']]);
		db()->prepare('UPDATE user_tokens SET used_at = NOW() WHERE id = ?')->execute([$row['id']]);
		flash_set('success', 'Password updated. You can now log in.');
		redirect(base_url('/login'));
	}

	public function verifyEmail(): void
	{
		$token = $_GET['token'] ?? '';
		$stmt = db()->prepare("SELECT * FROM user_tokens WHERE token = ? AND type = 'verify_email' AND used_at IS NULL AND expires_at > NOW() LIMIT 1");
		$stmt->execute([$token]);
		$row = $stmt->fetch();
		if ($row) {
			db()->prepare('UPDATE users SET verified_at = NOW() WHERE id = ?')->execute([$row['user_id']]);
			db()->prepare('UPDATE user_tokens SET used_at = NOW() WHERE id = ?')->execute([$row['id']]);
			flash_set('success', 'Email verified.');
		} else {
			flash_set('error', 'Invalid or expired verification link.');
		}
		redirect(base_url('/login'));
	}

	public function loginOrganizerForm(): void
	{
		view('auth/organizer-login');
	}

    public function organizerOtpForm(): void
    {
        view('auth/organizer-verify-otp');
    }

    public function organizerOtpVerify(): void
    {
        $otp = trim($_POST['otp'] ?? '');
        if ($otp === '') { redirect(base_url('/organizer/verify-otp')); }
        // Validate token and ensure it's not used
        $stmt = db()->prepare("SELECT * FROM organizer_tokens ot JOIN organizers o ON o.id = ot.organizer_id WHERE ot.token = ? AND ot.type='phone_otp' AND ot.used_at IS NULL ORDER BY ot.id DESC LIMIT 1");
        $stmt->execute([$otp]);
        $row = $stmt->fetch();
        if ($row) {
            // Mark OTP used and phone verified
            db()->prepare('UPDATE organizer_tokens SET used_at = NOW() WHERE id = ?')->execute([$row['id']]);
            db()->prepare('UPDATE organizers SET phone_verified_at = NOW() WHERE id = ?')->execute([$row['organizer_id']]);

            // Auto login organizer for best UX
            $_SESSION['organizer_id'] = (int)$row['organizer_id'];
            $_SESSION['role'] = 'organizer';

            // If not approved, show a warning but allow access
            if ((int)($row['is_approved'] ?? 0) !== 1) {
                flash_set('warning', 'Phone verified. Your account is pending approval, but you can access the dashboard with limited features.');
            } else {
                flash_set('success', 'Phone verified.');
            }

            redirect(base_url('/organizer/dashboard'));
        }
        flash_set('error', 'Invalid or expired code.');
        redirect(base_url('/organizer/verify-otp'));
    }

    public function organizerOtpResend(): void
    {
        $orgId = (int)($_SESSION['tmp_org_id'] ?? 0);
        if ($orgId === 0 && isset($_SESSION['organizer_id'])) { $orgId = (int)$_SESSION['organizer_id']; }
        $row = db()->prepare('SELECT phone FROM organizers WHERE id = ?');
        $row->execute([$orgId]);
        $phone = $row->fetch()['phone'] ?? '';
        if ($orgId === 0 || $phone === '') { redirect(base_url('/organizer/verify-otp')); }
        $otp = str_pad((string)rand(0,999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time()+600);
        db()->prepare("INSERT INTO organizer_tokens (organizer_id, token, type, expires_at) VALUES (?, ?, 'phone_otp', ?)")->execute([$orgId, $otp, $expires]);
        try {
            $sms = new \App\Services\Sms();
            if ($sms->isConfigured()) {
                $msg = \App\Services\SmsTemplates::render('organizer_otp', ['otp' => $otp]);
                if ($msg === '') { $msg = 'Your ShikaTicket OTP: ' . $otp; }
                $sms->send($phone, $msg);
            }
        } catch (\Throwable $e) {}
        flash_set('success', 'A new OTP was sent to your phone.');
        redirect(base_url('/organizer/verify-otp'));
    }

	public function registerOrganizerForm(): void
	{
		view('auth/organizer-register');
	}

	public function loginUser(): void
	{
        // Check rate limit
        if (\App\Services\RateLimitService::isBlocked('login_attempts')) {
            flash_set('error', 'Too many login attempts. Please try again later.');
            redirect(base_url('/login'));
        }

        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($phone === '' || $password === '') { 
            \App\Services\RateLimitService::recordAttempt('login_attempts');
            redirect(base_url('/login')); 
        }
        
        $user = User::findByPhone($phone);
        if ($user && password_verify($password, $user['password_hash'])) {
            // Check session timeout
            if (!\App\Services\SessionSecurityService::checkSessionTimeout()) {
                flash_set('error', 'Session expired. Please login again.');
                redirect(base_url('/login'));
            }
            
            // Set secure user session
            \App\Services\SessionSecurityService::setUserSession($user, 'user');
            redirect(base_url('/user/dashboard'));
        }
        
        // Record failed attempt
        \App\Services\RateLimitService::recordAttempt('login_attempts');
        flash_set('error', 'Invalid credentials');
        redirect(base_url('/login'));
	}

	public function registerUser(): void
	{
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if ($phone === '' || $email === '' || $password === '') { 
            flash_set('error', 'All fields are required.');
            redirect(base_url('/register')); 
        }
        
        try {
            // Create user
            $id = User::create($phone, $email, $password);
            
            // Fetch user data for proper session setup
            $userData = User::findById($id);
            
            // Set secure session using SessionSecurityService
            \App\Services\SessionSecurityService::setUserSession($userData, 'user');
            
            // Send welcome email (wrapped in try-catch to prevent blocking)
            try {
                $mailer = new Mailer();
                $html = EmailTemplates::render('user_welcome', ['userEmail' => $email, 'userPhone' => $phone]);
                if ($html !== '') { 
                    $mailer->send($email, 'Welcome to ShikaTicket', $html); 
                }
            } catch (\Throwable $e) {
                // Log but don't block registration
                error_log('Welcome email failed: ' . $e->getMessage());
            }
            
            // Send welcome SMS (wrapped in try-catch to prevent blocking)
            try { 
                $sms = new \App\Services\Sms(); 
                if ($sms->isConfigured()) { 
                    $body = \App\Services\SmsTemplates::render('welcome_user');
                    if ($body === '') { $body = 'Welcome to ShikaTicket!'; }
                    $sms->send($phone, $body); 
                } 
            } catch (\Throwable $e) {
                // Log but don't block registration
                error_log('Welcome SMS failed: ' . $e->getMessage());
            }
            
            // Set success message
            flash_set('success', 'Registration successful! Welcome to ShikaTicket.');
            
            // Redirect to dashboard
            redirect(base_url('/user/dashboard'));
        } catch (\PDOException $e) {
            // Handle duplicate phone/email error
            flash_set('error', 'Registration failed. Phone or email might already exist.');
            redirect(base_url('/register'));
        } catch (\Throwable $e) {
            // Catch any other errors
            error_log('Registration error: ' . $e->getMessage());
            flash_set('error', 'Registration failed. Please try again.');
            redirect(base_url('/register'));
        }
	}

	public function loginOrganizer(): void
	{
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email === '' || $password === '') { redirect(base_url('/organizer/login')); }
        $org = Organizer::findByEmail($email);
        if ($org && password_verify($password, $org['password_hash'])) {
            // Allow login even if not approved; show a warning
            if ((int)($org['is_approved'] ?? 0) !== 1) {
                flash_set('warning', 'Your account is pending approval. You can access the dashboard with limited features.');
            }
            $_SESSION['organizer_id'] = $org['id'];
            $_SESSION['role'] = 'organizer';
            redirect(base_url('/organizer/dashboard'));
        }
        flash_set('error', 'Invalid credentials');
        redirect(base_url('/organizer/login'));
	}

	public function registerOrganizer(): void
	{
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($fullName === '' || $phone === '' || $email === '' || $password === '') { redirect(base_url('/organizer/register')); }
        try {
            $id = Organizer::create($fullName, $phone, $email, $password);
            $_SESSION['tmp_org_id'] = $id;
            // Create phone OTP
            try {
                $otp = str_pad((string)rand(0,999999), 6, '0', STR_PAD_LEFT);
                $expires = date('Y-m-d H:i:s', time()+600);
                $stmt = db()->prepare('INSERT INTO organizer_tokens (organizer_id, token, type, expires_at) VALUES (?, ?, ?, ?)');
                $stmt->execute([$id, $otp, 'phone_otp', $expires]);
                $sms = new \App\Services\Sms();
                if ($sms->isConfigured()) {
                    $msg = \App\Services\SmsTemplates::render('organizer_otp', ['otp' => $otp]);
                    if ($msg === '') { $msg = 'Your ShikaTicket OTP: ' . $otp; }
                    $sms->send($phone, $msg);
                }
            } catch (\Throwable $e) {}
            flash_set('success', 'Registration successful. Enter the OTP sent to your phone to verify.');
            redirect(base_url('/organizer/verify-otp'));
        } catch (\PDOException $e) {
            echo 'Registration failed. Email or phone might already exist.';
        }
	}

	public function logout(): void
	{
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
		redirect(base_url('/'));
	}
}



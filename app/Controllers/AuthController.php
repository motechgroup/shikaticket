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
		view('auth/user-login');
	}

	public function registerUserForm(): void
	{
		view('auth/user-register');
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
        // Some environments have clock drift; don't hard fail on server time issues. We validate token and ensure it's not used
        $stmt = db()->prepare("SELECT * FROM organizer_tokens ot JOIN organizers o ON o.id = ot.organizer_id WHERE ot.token = ? AND ot.type='phone_otp' AND ot.used_at IS NULL ORDER BY ot.id DESC LIMIT 1");
        $stmt->execute([$otp]);
        $row = $stmt->fetch();
        if ($row) {
            db()->prepare('UPDATE organizer_tokens SET used_at = NOW() WHERE id = ?')->execute([$row['id']]);
            db()->prepare('UPDATE organizers SET phone_verified_at = NOW() WHERE id = ?')->execute([$row['organizer_id']]);
            flash_set('success', 'Phone verified.');
            redirect(base_url('/organizer/login'));
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
                if ($msg === '') { $msg = 'Your Ticko OTP: ' . $otp; }
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
        if ($phone === '' || $email === '' || $password === '') { redirect(base_url('/register')); }
        try {
            $id = User::create($phone, $email, $password);
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = 'user';
            // Send welcome email
            $mailer = new Mailer();
            $html = EmailTemplates::render('user_welcome', ['userEmail' => $email, 'userPhone' => $phone]);
            if ($html !== '') { $mailer->send($email, 'Welcome to Ticko', $html); }
            // Send welcome SMS
            try { 
                $sms = new \App\Services\Sms(); 
                if ($sms->isConfigured()) { 
                    $body = \App\Services\SmsTemplates::render('welcome_user');
                    if ($body === '') { $body = 'Welcome to Ticko!'; }
                    $sms->send($phone, $body); 
                } 
            } catch (\Throwable $e) {}
            redirect(base_url('/user/dashboard'));
        } catch (\PDOException $e) {
            echo 'Registration failed. Phone might already exist.';
        }
	}

	public function loginOrganizer(): void
	{
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email === '' || $password === '') { redirect(base_url('/organizer/login')); }
        $org = Organizer::findByEmail($email);
        if ($org && password_verify($password, $org['password_hash'])) {
            if ((int)$org['is_approved'] !== 1) {
                echo 'Your account is pending approval.';
                return;
            }
            $_SESSION['organizer_id'] = $org['id'];
            $_SESSION['role'] = 'organizer';
            redirect(base_url('/organizer/dashboard'));
        }
        echo 'Invalid credentials';
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
                    if ($msg === '') { $msg = 'Your Ticko OTP: ' . $otp; }
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



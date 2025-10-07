<?php
namespace App\Controllers;

use App\Models\TravelAgency;

class TravelAuthController
{
    public function login(): void
    {
        // Clear any existing travel agency session to ensure clean login state
        if (isset($_SESSION['travel_agency_id'])) {
            // If there's a travel agency session, check if it's valid
            $agency = \App\Models\TravelAgency::findById($_SESSION['travel_agency_id']);
            if ($agency) {
                // Valid session, redirect to dashboard
                redirect(base_url('/travel/dashboard'));
                return;
            } else {
                // Invalid session, clear it
                unset($_SESSION['travel_agency_id']);
                unset($_SESSION['travel_agency_name']);
                unset($_SESSION['travel_agency_email']);
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                flash_set('error', 'Email and password are required.');
                redirect(base_url('/travel/login'));
            }

            $agency = TravelAgency::findByEmail($email);
            if (!$agency) {
                flash_set('error', 'Invalid credentials.');
                redirect(base_url('/travel/login'));
            }

            if (!TravelAgency::verifyPassword($password, $agency['password_hash'])) {
                flash_set('error', 'Invalid credentials.');
                redirect(base_url('/travel/login'));
            }

            // If phone is not verified, start OTP flow
            if (!(int)($agency['phone_verified'] ?? 0)) {
                // Set temp session to allow OTP verification
                $_SESSION['temp_travel_agency_id'] = $agency['id'];
                $_SESSION['temp_travel_agency_phone'] = $agency['phone'];
                $_SESSION['temp_travel_agency_email'] = $agency['email'];
                
                // Generate and send new OTP
                $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expires = date('Y-m-d H:i:s', time() + 1800); // 30 minutes
                
                // Delete old OTPs
                db()->prepare('DELETE FROM travel_agency_tokens WHERE agency_id = ?')->execute([$agency['id']]);
                
                // Store new OTP
                db()->prepare('INSERT INTO travel_agency_tokens (agency_id, token, expires_at) VALUES (?, ?, ?)')->execute([$agency['id'], $otp, $expires]);
                
                // Send SMS
                try {
                    $sms = new \App\Services\Sms();
                    if ($sms->isConfigured()) {
                        $msg = \App\Services\SmsTemplates::render('travel_agency_otp', ['otp' => $otp]);
                        if ($msg === '') {
                            $msg = 'Your ShikaTicket verification code is: ' . $otp . '. Valid for 30 minutes.';
                        }
                        $sms->send($agency['phone'], $msg);
                    }
                } catch (\Throwable $e) {
                    error_log('Login OTP SMS error: ' . $e->getMessage());
                }
                
                flash_set('warning', 'Please verify your phone number. An OTP has been sent to ' . $agency['phone']);
                redirect(base_url('/travel/verify-otp'));
            }

            // Do not block unapproved agencies; allow login but show a banner
            if (!(int)($agency['is_approved'] ?? 0)) {
                flash_set('warning', 'Your account is pending approval. You can access the dashboard with limited features.');
            }

            // Set session
            $_SESSION['travel_agency_id'] = $agency['id'];
            $_SESSION['travel_agency_name'] = $agency['company_name'];
            $_SESSION['travel_agency_email'] = $agency['email'];

            flash_set('success', 'Welcome back, ' . $agency['company_name']);
            redirect(base_url('/travel/dashboard'));
        }

        travel_view('travel/auth/login');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'company_name' => trim($_POST['company_name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'address' => trim($_POST['address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'country' => trim($_POST['country'] ?? ''),
                'website' => trim($_POST['website'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];

            // Validation
            if ($data['company_name'] === '' || $data['contact_person'] === '' || 
                $data['email'] === '' || $data['phone'] === '' || $data['password'] === '') {
                flash_set('error', 'All required fields must be filled.');
                redirect(base_url('/travel/register'));
            }

            if (strlen($data['password']) < 6) {
                flash_set('error', 'Password must be at least 6 characters.');
                redirect(base_url('/travel/register'));
            }

            // Check if email already exists
            if (TravelAgency::findByEmail($data['email'])) {
                flash_set('error', 'Email already registered.');
                redirect(base_url('/travel/register'));
            }

            // Normalize website to https scheme if missing
            if ($data['website'] !== '' && !preg_match('~^https?://~i', $data['website'])) {
                $data['website'] = 'https://' . $data['website'];
            }
            
            // Normalize phone by country code if provided
            $dialByCountry = [
                'Kenya' => '+254', 'Tanzania' => '+255', 'Uganda' => '+256', 'Rwanda' => '+250',
                'South Africa' => '+27', 'Zambia' => '+260', 'Malawi' => '+265'
            ];
            
            // Get all digits from phone
            $digits = preg_replace('/\D+/', '', $data['phone']);
            
            if ($digits !== '') {
                $prefix = $dialByCountry[$data['country']] ?? '';
                
                if ($prefix !== '') {
                    // Remove the country code digits if they're already at the start
                    $countryCodeDigits = preg_replace('/\D+/', '', $prefix); // e.g., "254" from "+254"
                    if (strpos($digits, $countryCodeDigits) === 0) {
                        $digits = substr($digits, strlen($countryCodeDigits));
                    }
                    // Remove leading zeros
                    $digits = ltrim($digits, '0');
                    // Format: +254 792758752
                    $data['phone'] = $prefix . ' ' . $digits;
                } else {
                    // No country prefix available, just clean up the number
                    $digits = ltrim($digits, '0');
                    if ($data['phone'][0] !== '+') {
                        $data['phone'] = '+' . $digits;
                    }
                }
            }

            try {
                $agencyId = TravelAgency::create($data);
                if ($agencyId) {
                    // Store agency ID in session for OTP verification
                    $_SESSION['temp_travel_agency_id'] = $agencyId;
                    $_SESSION['temp_travel_agency_phone'] = $data['phone'];
                    $_SESSION['temp_travel_agency_email'] = $data['email'];
                    
                    // Generate 6-digit OTP
                    $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expires = date('Y-m-d H:i:s', time() + 1800); // 30 minutes
                    
                    // Store OTP in travel_agency_tokens table
                    $stmt = db()->prepare('INSERT INTO travel_agency_tokens (agency_id, token, expires_at) VALUES (?, ?, ?)');
                    $stmt->execute([$agencyId, $otp, $expires]);
                    
                    // Send OTP via SMS
                    try {
                        $sms = new \App\Services\Sms();
                        if ($sms->isConfigured()) {
                            $msg = \App\Services\SmsTemplates::render('travel_agency_otp', ['otp' => $otp]);
                            if ($msg === '') {
                                $msg = 'Your ShikaTicket verification code is: ' . $otp . '. Valid for 30 minutes.';
                            }
                            $smsSent = $sms->send($data['phone'], $msg);
                            
                            if ($smsSent) {
                                error_log('OTP SMS sent successfully to: ' . $data['phone']);
                            } else {
                                error_log('OTP SMS failed to send to: ' . $data['phone']);
                            }
                        } else {
                            error_log('SMS service not configured');
                        }
                    } catch (\Throwable $e) {
                        error_log('SMS sending error: ' . $e->getMessage());
                    }
                    
                    // Send welcome email
                    try {
                        $siteName = \App\Models\Setting::get('site.name', 'ShikaTicket');
                        $loginUrl = base_url('/travel/login');
                        
                        $html = \App\Services\EmailTemplates::render('travel_agency_welcome', [
                            'name' => $data['contact_person'],
                            'company_name' => $data['company_name'],
                            'email' => $data['email'],
                            'site_name' => $siteName,
                            'login_url' => $loginUrl
                        ]);
                        
                        if ($html !== '') {
                            $subject = "Welcome to {$siteName} - Travel Agency Registration Successful";
                            $mailer = new \App\Services\Mailer();
                            if ($mailer->isConfigured()) {
                                $emailSent = $mailer->send($data['email'], $subject, $html);
                                if ($emailSent) {
                                    error_log('Welcome email sent successfully to: ' . $data['email']);
                                } else {
                                    error_log('Welcome email failed to send to: ' . $data['email']);
                                }
                            } else {
                                error_log('Email service not configured');
                            }
                        }
                    } catch (\Throwable $e) {
                        error_log('Welcome email sending error: ' . $e->getMessage());
                    }
                    
                    flash_set('success', 'Registration successful! Please enter the OTP sent to your phone.');
                    redirect(base_url('/travel/verify-otp'));
                } else {
                    flash_set('error', 'Registration failed. Please try again.');
                    redirect(base_url('/travel/register'));
                }
            } catch (\PDOException $e) {
                error_log('Travel agency registration error: ' . $e->getMessage());
                flash_set('error', 'Registration failed. Email or phone might already exist.');
                redirect(base_url('/travel/register'));
            }
        }

        travel_view('travel/auth/register');
    }

    public function logout(): void
    {
        // CSRF verification is handled by the Router automatically
        
        // Clear all travel agency session variables
        unset($_SESSION['travel_agency_id']);
        unset($_SESSION['travel_agency_name']);
        unset($_SESSION['travel_agency_email']);
        
        // Also clear any other travel-related session data
        unset($_SESSION['travel_agency_token']);
        unset($_SESSION['travel_agency_approved']);
        
        // Destroy the entire session to ensure clean logout
        session_destroy();
        session_start();
        
        flash_set('success', 'Logged out successfully.');
        redirect(base_url('/travel/login'));
    }

    public function clearSession(): void
    {
        // Clear all travel agency session variables
        unset($_SESSION['travel_agency_id']);
        unset($_SESSION['travel_agency_name']);
        unset($_SESSION['travel_agency_email']);
        unset($_SESSION['travel_agency_token']);
        unset($_SESSION['travel_agency_approved']);
        
        // Destroy the entire session to ensure clean logout
        session_destroy();
        session_start();
        
        flash_set('success', 'Session cleared successfully.');
        redirect(base_url('/travel/login'));
    }

    public function verifyOtp(): void
    {
        try {
            // If no temp session, redirect to register
            if (!isset($_SESSION['temp_travel_agency_id'])) {
                flash_set('error', 'Session expired. Please register again.');
                redirect(base_url('/travel/register'));
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $otp = trim($_POST['otp'] ?? '');
                
                if ($otp === '') {
                    flash_set('error', 'Please enter the OTP code.');
                    redirect(base_url('/travel/verify-otp'));
                    return;
                }

                $agencyId = $_SESSION['temp_travel_agency_id'];

                // First, check if OTP exists and get its details (PHP-side expiry logic)
                $checkStmt = db()->prepare('SELECT id, expires_at FROM travel_agency_tokens WHERE agency_id = ? AND token = ? ORDER BY id DESC LIMIT 1');
                $checkStmt->execute([$agencyId, $otp]);
                $tokenRow = $checkStmt->fetch();

                if (!$tokenRow) {
                    // OTP doesn't exist or wrong code
                    flash_set('error', 'Invalid OTP code. Please check and try again.');
                    redirect(base_url('/travel/verify-otp'));
                    return;
                }

                // Check if expired in PHP
                $expiresTs = isset($tokenRow['expires_at']) ? strtotime($tokenRow['expires_at']) : 0;
                if ($expiresTs === false || $expiresTs <= time()) {
                    // OTP expired - delete it
                    db()->prepare('DELETE FROM travel_agency_tokens WHERE id = ?')->execute([$tokenRow['id']]);
                    flash_set('error', 'OTP has expired. Please request a new code.');
                    redirect(base_url('/travel/verify-otp'));
                    return;
                }

                // OTP is valid and not expired – mark phone as verified
                $updateStmt = db()->prepare('UPDATE travel_agencies SET phone_verified = 1 WHERE id = ?');
                $updateStmt->execute([$agencyId]);

                // Delete used token
                $deleteStmt = db()->prepare('DELETE FROM travel_agency_tokens WHERE id = ?');
                $deleteStmt->execute([$tokenRow['id']]);

                // Load agency details for session
                $agencyRow = \App\Models\TravelAgency::findById((int)$agencyId);

                // Clear temp session
                unset($_SESSION['temp_travel_agency_id']);
                unset($_SESSION['temp_travel_agency_phone']);
                unset($_SESSION['temp_travel_agency_email']);

                // Set logged-in session immediately after OTP verification
                if ($agencyRow) {
                    $_SESSION['travel_agency_id'] = $agencyRow['id'];
                    $_SESSION['travel_agency_name'] = $agencyRow['company_name'] ?? '';
                    $_SESSION['travel_agency_email'] = $agencyRow['email'] ?? '';
                }

                // Warn if still pending approval, but allow access
                if (!($agencyRow['is_approved'] ?? 0)) {
                    flash_set('warning', 'Your phone is verified. Your account is pending approval, but you can access the dashboard with limited features.');
                } else {
                    flash_set('success', 'Phone verified successfully!');
                }
                
                redirect(base_url('/travel/dashboard'));
                return;
            }

            // Show verification form
            $phone = $_SESSION['temp_travel_agency_phone'] ?? '';
            $email = $_SESSION['temp_travel_agency_email'] ?? '';
            travel_view('travel/auth/verify-otp', compact('phone', 'email'));
            
        } catch (\Throwable $e) {
            error_log('OTP Verification Error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            flash_set('error', 'Verification failed. Please try again or contact support.');
            redirect(base_url('/travel/verify-otp'));
        }
    }

    public function resendOtp(): void
    {
        if (!isset($_SESSION['temp_travel_agency_id'])) {
            flash_set('error', 'Session expired. Please register again.');
            redirect(base_url('/travel/register'));
        }

        $agencyId = $_SESSION['temp_travel_agency_id'];
        $phone = $_SESSION['temp_travel_agency_phone'] ?? '';

        if ($phone === '') {
            flash_set('error', 'Phone number not found. Please register again.');
            redirect(base_url('/travel/register'));
        }

        // Generate new OTP
        $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 1800); // 30 minutes

        // Delete old OTPs for this agency
        db()->prepare('DELETE FROM travel_agency_tokens WHERE agency_id = ?')->execute([$agencyId]);

        // Store new OTP
        $stmt = db()->prepare('INSERT INTO travel_agency_tokens (agency_id, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$agencyId, $otp, $expires]);

        // Send SMS
        try {
            $sms = new \App\Services\Sms();
            if ($sms->isConfigured()) {
                $msg = \App\Services\SmsTemplates::render('travel_agency_otp', ['otp' => $otp]);
                if ($msg === '') {
                    $msg = 'Your ShikaTicket verification code is: ' . $otp . '. Valid for 30 minutes.';
                }
                $smsSent = $sms->send($phone, $msg);
                
                if ($smsSent) {
                    flash_set('success', 'A new OTP has been sent to your phone.');
                } else {
                    flash_set('warning', 'OTP generated but SMS sending failed. Please contact support.');
                }
            } else {
                flash_set('warning', 'SMS service is not configured. Please contact support with your OTP: ' . $otp);
            }
        } catch (\Throwable $e) {
            error_log('Resend OTP SMS error: ' . $e->getMessage());
            flash_set('warning', 'Could not send SMS. Please contact support.');
        }

        redirect(base_url('/travel/verify-otp'));
    }
}

<?php /** @var string $token */ ?>
<?php /** @var string $userType */ ?>
<?php 
$userTypeLabels = [
    'user' => 'User',
    'organizer' => 'Organizer', 
    'travel' => 'Travel Agency'
];
$userTypeLabel = $userTypeLabels[$userType] ?? 'User';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Reset Code - <?php echo htmlspecialchars(\App\Models\Setting::get('site.name', 'ShikaTicket')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{ 
            --bg:#0a0a0a; 
            --card:#111111; 
            --text:#e5e7eb; 
            --muted:#9ca3af; 
            --accent:#ef4444; 
            --accent-600:#dc2626;
            --success:#10b981;
            --warning:#f59e0b;
            --info:#3b82f6;
        }
        
        * { font-family: 'Inter', sans-serif; }
        
        body{ 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            color:var(--text); 
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(239, 68, 68, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .card{ 
            background: rgba(17, 17, 17, 0.8); 
            border: 1px solid rgba(55, 65, 81, 0.3); 
            border-radius: 1rem; 
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .card:hover {
            border-color: rgba(239, 68, 68, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .input,.select,.textarea{ 
            background: rgba(15, 15, 16, 0.8); 
            border: 2px solid rgba(55, 65, 81, 0.5); 
            color:var(--text); 
            border-radius: 0.75rem; 
            padding: 0.875rem 1rem; 
            width:100%; 
            transition: all 0.2s ease;
            font-size: 1rem;
        }
        
        .input:focus,.select:focus,.textarea:focus{ 
            outline:none; 
            border-color:var(--accent); 
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
            background: rgba(15, 15, 16, 0.95);
        }
        
        .btn{ 
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-600) 100%);
            color:white; 
            padding: 0.875rem 2rem; 
            border-radius: 0.75rem; 
            font-weight:600; 
            transition: all 0.3s ease; 
            border:none; 
            cursor:pointer; 
            display:inline-flex; 
            align-items:center; 
            gap:0.5rem;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover{ 
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        }
        
        .btn-secondary{ 
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%); 
            color:var(--text); 
        }
        .btn-secondary:hover{ 
            background: linear-gradient(135deg, #4b5563 0%, #6b7280 100%);
            transform: translateY(-2px);
        }
        
        .link{ 
            color:var(--accent); 
            text-decoration:none; 
            transition: all 0.2s ease; 
            position: relative;
        }
        
        .link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: var(--accent);
            transition: width 0.3s ease;
        }
        
        .link:hover::after {
            width: 100%;
        }
        
        .link:hover{ 
            color:var(--accent-600); 
        }
        
        .code-input{ 
            text-align:center; 
            font-size: 2rem; 
            letter-spacing: 1rem; 
            font-weight:700; 
            background: rgba(239, 68, 68, 0.05);
            border: 2px solid rgba(239, 68, 68, 0.2);
        }
        
        .code-input:focus {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--accent);
        }
        
        .badge {
            background: linear-gradient(135deg, var(--info) 0%, #1d4ed8 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }
        
        .alert {
            border-radius: 0.75rem;
            padding: 1rem;
            margin: 1rem 0;
            border: 1px solid;
            backdrop-filter: blur(10px);
            animation: slideIn 0.3s ease-out;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }
        
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="<?php echo base_url('/'); ?>" class="flex items-center justify-center mb-6">
                <img src="<?php echo base_url(\App\Models\Setting::get('site.logo', 'logo.png')); ?>" alt="logo" class="h-12 w-auto">
            </a>
            <h2 class="text-3xl font-bold text-white">Verify Reset Code</h2>
            <p class="mt-2 text-gray-400">Enter the 6-digit code sent to your email and phone</p>
            <div class="mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <?php echo htmlspecialchars($userTypeLabel); ?> Account
                </span>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 pulse" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Password Reset Verification Form -->
        <div class="card p-8">
            <form method="POST" action="<?php echo base_url('/password-reset/verify'); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div>
                    <label for="reset_code" class="block text-sm font-medium text-gray-300 mb-2">
                        6-Digit Reset Code
                    </label>
                    <input type="text" id="reset_code" name="reset_code" 
                           class="input code-input" 
                           placeholder="123456"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required
                           autocomplete="off">
                    <p class="mt-1 text-xs text-gray-400">Enter the 6-digit code sent to your email and phone</p>
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-300 mb-2">
                        New Password
                    </label>
                    <input type="password" id="new_password" name="new_password" 
                           class="input" 
                           placeholder="Enter your new password"
                           minlength="6"
                           required>
                    <p class="mt-1 text-xs text-gray-400">Minimum 6 characters</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-2">
                        Confirm New Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="input" 
                           placeholder="Confirm your new password"
                           minlength="6"
                           required>
                </div>

                <div class="alert alert-info">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 floating" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">
                                <strong>Tip:</strong> The reset code is valid for 15 minutes. 
                                If you don't receive it, check your spam folder or request a new code.
                            </p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn w-full justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reset Password
                </button>
            </form>
        </div>

        <!-- Back to Request New Code -->
        <div class="text-center">
            <p class="text-gray-400 text-sm">
                Didn't receive the code? 
                <a href="<?php echo base_url('/password-reset?type=' . $userType); ?>" class="link font-medium">Request new code</a>
            </p>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <p class="text-gray-400 text-sm">
                Remember your password? 
                <?php
                $loginUrl = '/login';
                if ($userType === 'organizer') $loginUrl = '/organizer/login';
                elseif ($userType === 'travel') $loginUrl = '/travel/login';
                ?>
                <a href="<?php echo base_url($loginUrl); ?>" class="link font-medium">Sign in</a>
            </p>
        </div>
    </div>

    <script>
        // Auto-format reset code input
        document.getElementById('reset_code').addEventListener('input', function(e) {
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 6 digits
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('new_password').addEventListener('input', function(e) {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value) {
                confirmPassword.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>

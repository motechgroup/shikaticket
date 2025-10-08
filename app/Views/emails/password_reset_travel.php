<?php /** @var string $resetCode */ ?>
<?php /** @var string $resetUrl */ ?>
<?php /** @var string $agencyName */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - <?php echo htmlspecialchars($siteTitle ?? 'ShikaTicket'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .code-box { background: #f8f9fa; border: 2px solid #ef4444; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
        .reset-code { font-size: 32px; font-weight: bold; color: #ef4444; letter-spacing: 5px; margin: 10px 0; }
        .button { display: inline-block; background: #ef4444; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 14px; color: #666; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="<?php echo htmlspecialchars($siteTitle ?? 'ShikaTicket'); ?>" style="height: 50px; margin-bottom: 15px;">
            <h1>Travel Agency Password Reset</h1>
        </div>
        
        <div class="content">
            <h2>Hello <?php echo htmlspecialchars($agencyName ?? 'Travel Agency'); ?>!</h2>
            
            <p>We received a request to reset your password for your <?php echo htmlspecialchars($siteTitle ?? 'ShikaTicket'); ?> travel agency account.</p>
            
            <div class="code-box">
                <p><strong>Your Password Reset Code:</strong></p>
                <div class="reset-code"><?php echo htmlspecialchars($resetCode); ?></div>
                <p><small>This code is valid for 15 minutes</small></p>
            </div>
            
            <p>Alternatively, you can click the button below to reset your password directly:</p>
            <p style="text-align: center;">
                <a href="<?php echo htmlspecialchars($resetUrl); ?>" class="button">Reset Password</a>
            </p>
            
            <div class="warning">
                <strong>Security Notice:</strong> If you didn't request this password reset, please ignore this email. Your travel agency account remains secure.
            </div>
            
            <p>For security reasons, this reset code will expire in 15 minutes. If you need a new code, please request another password reset.</p>
            
            <p>If you have any questions about your travel agency account, please contact our support team.</p>
            
            <p>Best regards,<br>
            The <?php echo htmlspecialchars($siteTitle ?? 'ShikaTicket'); ?> Team</p>
        </div>
        
        <div class="footer">
            <p>This email was sent from <?php echo htmlspecialchars($siteTitle ?? 'ShikaTicket'); ?><br>
            If you have any questions, please contact us.</p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteTitle ?? 'ShikaTicket'); ?>. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</body>
</html>

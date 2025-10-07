<?php
/**
 * Email Verification Template
 * Variables: {{name}}, {{email}}, {{verification_url}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #dc2626; margin: 0; font-size: 28px;">Verify Your Email Address</h1>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>Thank you for registering with {{site_name}}! To complete your registration and activate your account, please verify your email address.</p>
            
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #92400e;"><strong>⚠️ Important:</strong> Your account is not active until you verify your email address.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{verification_url}}" style="background-color: #dc2626; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 16px;">
                    ✅ Verify Email Address
                </a>
            </div>
            
            <p><strong>What happens after verification?</strong></p>
            <ul style="color: #555;">
                <li>Your account will be fully activated</li>
                <li>You can start booking events and travel</li>
                <li>You'll receive important notifications</li>
                <li>You can access all platform features</li>
            </ul>
            
            <p><strong>Can't click the button?</strong><br>
            Copy and paste this link into your browser:<br>
            <span style="color: #dc2626; word-break: break-all;">{{verification_url}}</span></p>
            
            <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #0369a1;"><strong>🔒 Security Note:</strong> This verification link will expire in 24 hours for your security.</p>
            </div>
            
            <p>If you didn't create an account with {{site_name}}, please ignore this email.</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This email was sent to {{email}} because you registered an account.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

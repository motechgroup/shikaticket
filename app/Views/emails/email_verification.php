<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #0b0b0b; color: #e5e7eb;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #0b0b0b; padding: 24px;">
        <tr>
            <td>
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #111111; border: 1px solid #1f2937; border-radius: 8px;">
                    <!-- Header with Logo -->
                    <tr>
                        <td style="padding: 16px 20px; border-bottom: 1px solid #1f2937;">
                            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="<?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>" style="height: 40px; display: block;">
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h1 style="margin: 0 0 16px 0; color: #3b82f6; font-size: 28px; font-weight: bold; text-align: center;">
                                📧 Verify Your Email Address
                            </h1>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Dear <strong><?php echo htmlspecialchars($name ?? 'User'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Thank you for registering with <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>! To complete your registration and activate your account, please verify your email address.
                            </p>
                            
                            <!-- Warning Badge -->
                            <div style="background-color: #1f2937; border: 2px solid #fbbf24; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #fbbf24; font-weight: bold; font-size: 14px;">
                                    ⚠️ Important Notice:
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    Your account is not active until you verify your email address. Please click the button below to complete verification.
                                </p>
                            </div>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo htmlspecialchars($verification_url ?? '#'); ?>" 
                                           style="display: inline-block; padding: 16px 40px; background-color: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            ✅ Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Benefits Section -->
                            <div style="background-color: #1f2937; border-left: 4px solid #3b82f6; padding: 20px; margin: 24px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 12px 0; color: #3b82f6; font-size: 16px; font-weight: bold;">
                                    What Happens After Verification?
                                </h2>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8;">
                                    <li>Your account will be fully activated</li>
                                    <li>You can start booking events and travel destinations</li>
                                    <li>You'll receive important notifications and updates</li>
                                    <li>Access all platform features without restrictions</li>
                                    <li>Manage your bookings and tickets</li>
                                </ul>
                            </div>
                            
                            <!-- Alternative Link -->
                            <div style="background-color: #1f2937; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0 0 8px 0; color: #d1d5db; font-size: 14px; font-weight: bold;">
                                    Can't Click the Button?
                                </p>
                                <p style="margin: 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                    Copy and paste this link into your browser:
                                </p>
                                <p style="margin: 8px 0 0 0; color: #3b82f6; font-size: 12px; word-break: break-all;">
                                    <?php echo htmlspecialchars($verification_url ?? ''); ?>
                                </p>
                            </div>
                            
                            <!-- Security Info -->
                            <div style="background-color: #1f2937; border: 1px solid #3b82f6; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #3b82f6; font-weight: bold; font-size: 14px;">
                                    🔒 Security Note:
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    This verification link will expire in 24 hours for your security. If you didn't create an account with <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>, please ignore this email.
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Thank you for choosing <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>!
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best regards,<br>
                                <strong style="color: #3b82f6;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This email was sent to <?php echo htmlspecialchars($email ?? 'your email'); ?> because you registered an account.
                            </p>
                            <p style="margin: 0; color: #6b7280; font-size: 12px; text-align: center;">
                                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>. All rights reserved. 
                                • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

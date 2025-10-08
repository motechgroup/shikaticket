<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?></title>
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
                            <h1 style="margin: 0 0 16px 0; color: #ef4444; font-size: 28px; font-weight: bold;">
                                🎉 Welcome to <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>!
                            </h1>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Hi <strong><?php echo htmlspecialchars($name ?? 'there'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Thank you for registering with <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>! Your account has been created successfully, and you're now ready to explore amazing events, travel destinations, and experiences across Kenya.
                            </p>
                            
                            <!-- What You Can Do Section -->
                            <div style="background-color: #1f2937; border-left: 4px solid #ef4444; padding: 20px; margin: 24px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 12px 0; color: #ef4444; font-size: 18px; font-weight: bold;">
                                    What You Can Do Now:
                                </h2>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8;">
                                    <li>Browse and discover upcoming events</li>
                                    <li>Purchase tickets securely with M-Pesa or card</li>
                                    <li>Book travel destinations and experiences</li>
                                    <li>Manage your bookings and tickets</li>
                                    <li>Receive QR codes for easy check-in</li>
                                    <li>Track your order history</li>
                                </ul>
                            </div>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo htmlspecialchars($login_url ?? base_url('/login')); ?>" 
                                           style="display: inline-block; padding: 14px 32px; background-color: #ef4444; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            Start Exploring Events
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Tips Section -->
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #1f2937;">
                                <h3 style="margin: 0 0 12px 0; color: #ef4444; font-size: 16px; font-weight: bold;">
                                    💡 Quick Tips:
                                </h3>
                                <p style="margin: 0 0 12px 0; color: #9ca3af; font-size: 14px; line-height: 1.6;">
                                    • <strong>Download Your Tickets:</strong> After purchase, download your tickets as PDF with QR codes<br>
                                    • <strong>Mobile Friendly:</strong> Access your tickets from any device<br>
                                    • <strong>Secure Payments:</strong> We use M-Pesa STK and secure card processing<br>
                                    • <strong>Customer Support:</strong> Contact us anytime for assistance
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                If you have any questions or need assistance, feel free to reach out to our support team.
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best regards,<br>
                                <strong style="color: #ef4444;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This email was sent from <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>
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

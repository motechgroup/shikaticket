<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> - Travel Agency</title>
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
                            <h1 style="margin: 0 0 16px 0; color: #8b5cf6; font-size: 28px; font-weight: bold;">
                                🌍 Welcome to <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>!
                            </h1>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Dear <strong><?php echo htmlspecialchars($name ?? 'Travel Partner'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Congratulations! Your travel agency account for <strong style="color: #8b5cf6;"><?php echo htmlspecialchars($company_name ?? 'your company'); ?></strong> has been successfully registered on <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>.
                            </p>
                            
                            <!-- Account Details -->
                            <div style="background-color: #1f2937; border-left: 4px solid #8b5cf6; padding: 20px; margin: 24px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 12px 0; color: #8b5cf6; font-size: 16px; font-weight: bold;">
                                    📋 Your Account Details:
                                </h2>
                                <table style="width: 100%; color: #d1d5db; font-size: 14px;">
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>Company Name:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($company_name ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>Contact Person:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($name ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>Email:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($email ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>Status:</strong></td>
                                        <td style="padding: 6px 0;"><span style="background-color: #fbbf24; color: #000; padding: 2px 8px; border-radius: 4px; font-weight: bold;">Pending Approval</span></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Next Steps -->
                            <div style="background-color: #1f2937; padding: 20px; margin: 24px 0; border-radius: 6px;">
                                <h2 style="margin: 0 0 12px 0; color: #8b5cf6; font-size: 18px; font-weight: bold;">
                                    🚀 Next Steps:
                                </h2>
                                <ol style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8;">
                                    <li><strong>Verify Your Phone:</strong> Complete phone verification with the OTP sent to your mobile</li>
                                    <li><strong>Wait for Approval:</strong> Our admin team will review your application (usually within 24 hours)</li>
                                    <li><strong>Get Notified:</strong> You'll receive an email once your account is approved</li>
                                    <li><strong>Start Creating:</strong> Once approved, you can start adding travel destinations and packages</li>
                                </ol>
                            </div>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo htmlspecialchars($login_url ?? base_url('/travel/login')); ?>" 
                                           style="display: inline-block; padding: 14px 32px; background-color: #8b5cf6; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            Access Your Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Features Section -->
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #1f2937;">
                                <h3 style="margin: 0 0 12px 0; color: #8b5cf6; font-size: 16px; font-weight: bold;">
                                    ✨ What You Can Do as a Travel Agency:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8;">
                                    <li>Create and manage travel destinations across Kenya</li>
                                    <li>Set custom pricing and booking policies</li>
                                    <li>Upload stunning destination photos and detailed itineraries</li>
                                    <li>Manage customer bookings in real-time</li>
                                    <li>Generate booking confirmations with QR codes</li>
                                    <li>Track earnings and request withdrawals</li>
                                    <li>Use our scanner system for ticket verification at destinations</li>
                                    <li>View comprehensive analytics and reports</li>
                                </ul>
                            </div>
                            
                            <!-- Support Info -->
                            <div style="background-color: #1f2937; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    <strong style="color: #8b5cf6;">💬 Need Help?</strong><br>
                                    Our support team is here to assist you! If you have any questions during the setup process, feel free to reach out to us anytime.
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                We're excited to partner with you and help you grow your travel business. Welcome aboard!
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best regards,<br>
                                <strong style="color: #8b5cf6;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This email was sent to <?php echo htmlspecialchars($email ?? 'your email'); ?> because you registered a travel agency account.
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

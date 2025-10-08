<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations! Your Organizer Account is Approved</title>
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
                            <h1 style="margin: 0 0 16px 0; color: #10b981; font-size: 28px; font-weight: bold;">
                                🎉 Congratulations! You're Approved!
                            </h1>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Hi <strong><?php echo htmlspecialchars($name ?? 'there'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Great news! Your event organizer account on <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> has been <strong style="color: #10b981;">approved by our admin team</strong>. You can now start creating and managing events, selling tickets, and growing your audience!
                            </p>
                            
                            <!-- Success Badge -->
                            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 20px; margin: 24px 0; border-radius: 8px; text-align: center;">
                                <p style="margin: 0; color: #ffffff; font-size: 18px; font-weight: bold;">
                                    ✓ Account Status: <span style="background-color: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 4px;">APPROVED</span>
                                </p>
                            </div>
                            
                            <!-- What You Can Do Section -->
                            <div style="background-color: #1f2937; border-left: 4px solid #10b981; padding: 20px; margin: 24px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 12px 0; color: #10b981; font-size: 18px; font-weight: bold;">
                                    What You Can Do Now:
                                </h2>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8;">
                                    <li>Create and publish unlimited events</li>
                                    <li>Set custom ticket prices and categories</li>
                                    <li>Track ticket sales in real-time</li>
                                    <li>Manage scanner devices for event check-in</li>
                                    <li>View comprehensive analytics and reports</li>
                                    <li>Request withdrawals of your earnings</li>
                                    <li>Upload event banners and promotional materials</li>
                                </ul>
                            </div>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo htmlspecialchars($login_url ?? base_url('/organizer/login')); ?>" 
                                           style="display: inline-block; padding: 14px 32px; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            Go to Organizer Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Getting Started Tips -->
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #1f2937;">
                                <h3 style="margin: 0 0 12px 0; color: #10b981; font-size: 16px; font-weight: bold;">
                                    🚀 Getting Started Tips:
                                </h3>
                                <p style="margin: 0 0 12px 0; color: #9ca3af; font-size: 14px; line-height: 1.6;">
                                    <strong>1. Create Your First Event:</strong> Click "Create Event" and fill in event details<br>
                                    <strong>2. Set Up Ticket Types:</strong> Define VIP, regular, early bird tickets, etc.<br>
                                    <strong>3. Add Scanner Devices:</strong> Register devices for ticket verification<br>
                                    <strong>4. Promote Your Event:</strong> Share your event URL on social media<br>
                                    <strong>5. Monitor Sales:</strong> Track sales and attendees from your dashboard
                                </p>
                            </div>
                            
                            <!-- Revenue Info -->
                            <div style="background-color: #1f2937; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    <strong style="color: #10b981;">💰 Revenue & Payments:</strong><br>
                                    Your earnings will be available in your organizer wallet. You can request withdrawals once you reach the minimum threshold. Payments are processed securely and quickly.
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                We're excited to have you on board! If you have any questions or need assistance setting up your first event, our support team is here to help.
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best of luck with your events!<br>
                                <strong style="color: #10b981;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
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

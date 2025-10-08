<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations! Your Travel Agency is Approved</title>
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
                            <h1 style="margin: 0 0 8px 0; color: #10b981; font-size: 32px; font-weight: bold; text-align: center;">
                                🎉 Congratulations!
                            </h1>
                            <h2 style="margin: 0 0 20px 0; color: #8b5cf6; font-size: 22px; font-weight: bold; text-align: center;">
                                Your Travel Agency Has Been Approved!
                            </h2>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Dear <strong><?php echo htmlspecialchars($name ?? 'Travel Partner'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Great news! Your travel agency account for <strong style="color: #8b5cf6;"><?php echo htmlspecialchars($company_name ?? 'your company'); ?></strong> has been <strong style="color: #10b981;">approved</strong> by our admin team and is now fully active on <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>!
                            </p>
                            
                            <!-- Success Badge -->
                            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 20px; margin: 24px 0; border-radius: 8px; text-align: center;">
                                <p style="margin: 0; color: #ffffff; font-size: 18px; font-weight: bold;">
                                    ✅ Account Status: <span style="background-color: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 4px;">ACTIVE & APPROVED</span>
                                </p>
                                <p style="margin: 8px 0 0 0; color: #ffffff; font-size: 14px;">
                                    You can now start creating destinations and accepting bookings!
                                </p>
                            </div>
                            
                            <!-- What You Can Do Section -->
                            <div style="background-color: #1f2937; border-left: 4px solid #10b981; padding: 20px; margin: 24px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 12px 0; color: #10b981; font-size: 18px; font-weight: bold;">
                                    What You Can Do Now:
                                </h2>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8;">
                                    <li>✅ Create and publish travel destinations across Kenya</li>
                                    <li>✅ Set custom pricing and booking policies</li>
                                    <li>✅ Upload destination photos and detailed itineraries</li>
                                    <li>✅ Manage customer bookings and inquiries in real-time</li>
                                    <li>✅ Track earnings and request withdrawals</li>
                                    <li>✅ Use our scanner system for ticket verification</li>
                                    <li>✅ Access comprehensive analytics and sales reports</li>
                                    <li>✅ Promote your destinations to thousands of users</li>
                                </ul>
                            </div>
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo htmlspecialchars($login_url ?? base_url('/travel/login')); ?>" 
                                           style="display: inline-block; padding: 14px 32px; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            🚀 Start Creating Destinations Now
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Pro Tip -->
                            <div style="background-color: #1f2937; border: 1px solid #fbbf24; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #fbbf24; font-weight: bold; font-size: 14px;">
                                    💡 Pro Tip:
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    Make sure your phone verification is complete to unlock all features. Add high-quality photos and detailed descriptions to your destinations for better visibility and bookings!
                                </p>
                            </div>
                            
                            <!-- Getting Started Tips -->
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #1f2937;">
                                <h3 style="margin: 0 0 12px 0; color: #8b5cf6; font-size: 16px; font-weight: bold;">
                                    📚 Getting Started Resources:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #9ca3af; font-size: 14px; line-height: 1.8;">
                                    <li>Review the <strong style="color: #d1d5db;">Travel Agency Guide</strong> in your dashboard</li>
                                    <li>Learn about <strong style="color: #d1d5db;">Booking Management</strong> best practices</li>
                                    <li>Set up <strong style="color: #d1d5db;">Scanner Devices</strong> for ticket verification at destinations</li>
                                    <li>Explore the <strong style="color: #d1d5db;">Analytics Dashboard</strong> to track performance</li>
                                    <li>Contact our support team for personalized onboarding assistance</li>
                                </ul>
                            </div>
                            
                            <!-- Support Info -->
                            <div style="background-color: #1f2937; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    <strong style="color: #10b981;">🤝 Need Help?</strong><br>
                                    Our dedicated support team is here to help you succeed! Whether you need technical assistance or business advice, we're just a message away.
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                We're thrilled to have you on board and can't wait to see the amazing travel experiences you'll offer to our community. Here's to your success!
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Happy traveling!<br>
                                <strong style="color: #10b981;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This email was sent to <?php echo htmlspecialchars($email ?? 'your email'); ?> because your travel agency account was approved.
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

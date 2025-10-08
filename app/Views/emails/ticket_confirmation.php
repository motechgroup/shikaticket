<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Confirmation</title>
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
                                🎫 Ticket Confirmed!
                            </h1>
                            <p style="margin: 0 0 24px 0; color: #9ca3af; font-size: 16px; text-align: center;">
                                Your ticket has been successfully purchased
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Dear <strong><?php echo htmlspecialchars($name ?? 'Guest'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Congratulations! Your ticket purchase has been confirmed. Get ready for an amazing experience!
                            </p>
                            
                            <!-- Event Details Card -->
                            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 24px; margin: 24px 0; border-radius: 8px;">
                                <h2 style="margin: 0 0 16px 0; color: #ffffff; font-size: 22px; font-weight: bold;">
                                    🎉 <?php echo htmlspecialchars($event_title ?? 'Event Name'); ?>
                                </h2>
                                <table style="width: 100%; color: #ffffff; font-size: 14px;">
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>📅 Date & Time:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($event_date ?? 'TBD'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>📍 Venue:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($venue ?? 'TBD'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>🎫 Ticket Code:</strong></td>
                                        <td style="padding: 6px 0;">
                                            <span style="font-family: monospace; background-color: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; font-weight: bold;">
                                                <?php echo htmlspecialchars($ticket_code ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- QR Code Section -->
                            <?php if (!empty($qr_code_url)): ?>
                            <div style="background-color: #1f2937; padding: 24px; margin: 24px 0; border-radius: 6px; text-align: center;">
                                <h3 style="margin: 0 0 16px 0; color: #10b981; font-size: 16px; font-weight: bold;">
                                    Your QR Code for Entry:
                                </h3>
                                <img src="<?php echo htmlspecialchars($qr_code_url); ?>" alt="Ticket QR Code" style="max-width: 200px; border: 3px solid #10b981; border-radius: 8px; background-color: #ffffff; padding: 8px;">
                                <p style="margin: 12px 0 0 0; color: #9ca3af; font-size: 13px;">
                                    Show this QR code at the event entrance for quick check-in
                                </p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Download Button -->
                            <?php if (!empty($download_url)): ?>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo htmlspecialchars($download_url); ?>" 
                                           style="display: inline-block; padding: 14px 32px; background-color: #ef4444; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            📱 Download Your Ticket PDF
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>
                            
                            <!-- Important Instructions -->
                            <div style="background-color: #1f2937; border: 2px solid #fbbf24; padding: 20px; margin: 24px 0; border-radius: 6px;">
                                <h3 style="margin: 0 0 12px 0; color: #fbbf24; font-size: 16px; font-weight: bold;">
                                    📋 Important Instructions:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8; font-size: 14px;">
                                    <li>Bring a valid ID matching your ticket name</li>
                                    <li>Arrive at least 30 minutes before the event starts</li>
                                    <li>Keep your ticket code and QR code accessible</li>
                                    <li>Show your QR code or ticket code at the entrance for scanning</li>
                                    <li>Contact the event organizer if you have any special requirements</li>
                                    <li>Save this email or download your ticket for offline access</li>
                                </ul>
                            </div>
                            
                            <!-- Quick Tips -->
                            <div style="background-color: #1f2937; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #3b82f6; font-weight: bold; font-size: 14px;">
                                    💡 Quick Tips:
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    • Download your ticket to your phone for easy access<br>
                                    • Screenshot the QR code as a backup<br>
                                    • Check the event details carefully before arriving<br>
                                    • Follow the event organizer's social media for updates
                                </p>
                            </div>
                            
                            <!-- Support Info -->
                            <div style="background-color: #1f2937; border-left: 4px solid #10b981; padding: 16px; margin: 24px 0; border-radius: 4px;">
                                <p style="margin: 0; color: #10b981; font-weight: bold; font-size: 14px;">
                                    Need Help?
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    If you have any questions about your ticket or the event, please contact the event organizer or our support team. We're here to help!
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                We hope you enjoy the event! See you there! 🎉
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best regards,<br>
                                <strong style="color: #10b981;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This ticket confirmation was sent to your registered email address.
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

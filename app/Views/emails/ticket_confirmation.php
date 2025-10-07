<?php
/**
 * Ticket Confirmation Template
 * Variables: {{name}}, {{event_title}}, {{event_date}}, {{venue}}, {{ticket_code}}, {{qr_code_url}}, {{download_url}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #10b981; margin: 0; font-size: 28px;">🎫 Ticket Confirmation</h1>
            <p style="color: #666; margin: 10px 0 0 0;">Your ticket has been successfully purchased!</p>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>Congratulations! Your ticket purchase has been confirmed. Here are your event details:</p>
            
            <!-- Event Details Card -->
            <div style="background-color: #f0fdf4; border: 2px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #10b981; margin-top: 0;">🎉 {{event_title}}</h3>
                <p style="margin: 5px 0;"><strong>📅 Date:</strong> {{event_date}}</p>
                <p style="margin: 5px 0;"><strong>📍 Venue:</strong> {{venue}}</p>
                <p style="margin: 5px 0;"><strong>🎫 Ticket Code:</strong> <span style="font-family: monospace; background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">{{ticket_code}}</span></p>
            </div>
            
            <!-- QR Code -->
            <?php if (isset($qr_code_url) && $qr_code_url): ?>
            <div style="text-align: center; margin: 20px 0;">
                <p style="margin-bottom: 10px;"><strong>Your QR Code:</strong></p>
                <img src="{{qr_code_url}}" alt="QR Code" style="max-width: 200px; border: 2px solid #10b981; border-radius: 8px;">
                <p style="color: #666; font-size: 14px; margin-top: 10px;">Show this QR code at the event entrance</p>
            </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{download_url}}" style="background-color: #dc2626; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 5px;">
                    📱 Download Ticket
                </a>
            </div>
            
            <!-- Important Instructions -->
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #92400e; margin-top: 0;">📋 Important Instructions:</h4>
                <ul style="color: #92400e; margin: 0;">
                    <li>Bring a valid ID to the event</li>
                    <li>Arrive at least 30 minutes before the event starts</li>
                    <li>Keep your ticket code safe and accessible</li>
                    <li>Show your QR code or ticket code at the entrance</li>
                    <li>Contact the organizer if you have any questions</li>
                </ul>
            </div>
            
            <p><strong>Need Help?</strong><br>
            If you have any questions about your ticket or the event, please contact the event organizer or our support team.</p>
            
            <p>We hope you enjoy the event!</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This ticket confirmation was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

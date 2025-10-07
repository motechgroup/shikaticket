<?php
/**
 * Event Reminder Template
 * Variables: {{name}}, {{event_title}}, {{event_date}}, {{event_time}}, {{venue}}, {{event_url}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #dc2626; margin: 0; font-size: 28px;">⏰ Event Reminder</h1>
            <p style="color: #666; margin: 10px 0 0 0;">Don't forget about your upcoming event!</p>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>This is a friendly reminder about your upcoming event. We can't wait to see you there!</p>
            
            <!-- Event Details Card -->
            <div style="background-color: #fef2f2; border: 2px solid #dc2626; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #dc2626; margin-top: 0;">🎉 {{event_title}}</h3>
                <p style="margin: 5px 0;"><strong>📅 Date:</strong> {{event_date}}</p>
                <p style="margin: 5px 0;"><strong>🕐 Time:</strong> {{event_time}}</p>
                <p style="margin: 5px 0;"><strong>📍 Venue:</strong> {{venue}}</p>
            </div>
            
            <!-- Important Reminders -->
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #92400e; margin-top: 0;">📋 Important Reminders</h4>
                <ul style="color: #92400e; margin: 0;">
                    <li>Arrive at least 30 minutes before the event starts</li>
                    <li>Bring a valid ID for verification</li>
                    <li>Have your ticket ready (digital or printed)</li>
                    <li>Check the event page for any last-minute updates</li>
                    <li>Contact the organizer if you have any questions</li>
                </ul>
            </div>
            
            <!-- Weather Check -->
            <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0369a1; margin-top: 0;">🌤️ Weather Check</h4>
                <p style="margin: 0; color: #0369a1;">Don't forget to check the weather forecast and dress appropriately for the event!</p>
            </div>
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{event_url}}" style="background-color: #dc2626; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 5px;">
                    📱 View Event Details
                </a>
            </div>
            
            <!-- Contact Information -->
            <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #475569; margin-top: 0;">📞 Need Help?</h4>
                <p style="margin: 0; color: #475569;">If you have any questions about the event, please contact the event organizer or visit the event page for more information.</p>
            </div>
            
            <p>We look forward to seeing you at the event!</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This event reminder was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

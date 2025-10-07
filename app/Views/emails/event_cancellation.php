<?php
/**
 * Event Cancellation Template
 * Variables: {{name}}, {{event_title}}, {{event_date}}, {{venue}}, {{refund_amount}}, {{currency}}, {{refund_status}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #dc2626; margin: 0; font-size: 28px;">❌ Event Cancelled</h1>
            <p style="color: #666; margin: 10px 0 0 0;">We sincerely apologize for the inconvenience</p>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>We regret to inform you that the following event has been cancelled:</p>
            
            <!-- Event Details -->
            <div style="background-color: #fef2f2; border: 2px solid #dc2626; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #dc2626; margin-top: 0;">❌ {{event_title}}</h3>
                <p style="margin: 5px 0;"><strong>📅 Date:</strong> {{event_date}}</p>
                <p style="margin: 5px 0;"><strong>📍 Venue:</strong> {{venue}}</p>
            </div>
            
            <!-- Apology Message -->
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #92400e; margin-top: 0;">😔 We Apologize</h4>
                <p style="margin: 0; color: #92400e;">We understand this cancellation is disappointing and we sincerely apologize for any inconvenience caused. The event organizer had to cancel due to unforeseen circumstances.</p>
            </div>
            
            <!-- Refund Information -->
            <div style="background-color: #f0fdf4; border: 2px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #10b981; margin-top: 0;">💰 Refund Information</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Refund Amount:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right; font-size: 18px; font-weight: bold; color: #10b981;">{{currency}} {{refund_amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Refund Status:</strong></td>
                        <td style="padding: 8px 0; text-align: right; text-transform: capitalize;">{{refund_status}}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Refund Timeline -->
            <?php if ($refund_status === 'processing'): ?>
                <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #0369a1; margin-top: 0;">⏳ Refund Processing</h4>
                    <p style="margin: 0; color: #0369a1;">Your refund is being processed and will be credited back to your original payment method within 3-5 business days.</p>
                </div>
            <?php elseif ($refund_status === 'completed'): ?>
                <div style="background-color: #f0fdf4; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #059669; margin-top: 0;">✅ Refund Completed</h4>
                    <p style="margin: 0; color: #059669;">Your refund has been processed and should appear in your account within 1-2 business days.</p>
                </div>
            <?php endif; ?>
            
            <!-- Alternative Events -->
            <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #475569; margin-top: 0;">🎉 Don't Miss Out!</h4>
                <p style="margin: 0; color: #475569;">Check out our other exciting events happening soon. We have many great experiences waiting for you!</p>
            </div>
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{events_url}}" style="background-color: #dc2626; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 5px;">
                    🎫 Browse Other Events
                </a>
            </div>
            
            <!-- Support Information -->
            <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0369a1; margin-top: 0;">📞 Questions About Your Refund?</h4>
                <p style="margin: 0; color: #0369a1;">If you have any questions about your refund or need assistance, please contact our support team. We're here to help!</p>
            </div>
            
            <p>Thank you for your understanding and continued support.</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This cancellation notice was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

<?php
/**
 * Payment Reminder Template
 * Variables: {{name}}, {{order_id}}, {{amount}}, {{currency}}, {{due_date}}, {{payment_url}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #f59e0b; margin: 0; font-size: 28px;">💳 Payment Reminder</h1>
            <p style="color: #666; margin: 10px 0 0 0;">Complete your payment to secure your booking</p>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>This is a friendly reminder that you have a pending payment for your booking. Complete your payment to secure your tickets.</p>
            
            <!-- Payment Details -->
            <div style="background-color: #fffbeb; border: 2px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #f59e0b; margin-top: 0;">💸 Payment Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7;"><strong>Order ID:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7; text-align: right; font-family: monospace;">{{order_id}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7;"><strong>Amount Due:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7; text-align: right; font-size: 18px; font-weight: bold; color: #f59e0b;">{{currency}} {{amount}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Due Date:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">{{due_date}}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Urgency Notice -->
            <div style="background-color: #fef2f2; border: 1px solid #ef4444; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #dc2626; margin-top: 0;">⚠️ Important Notice</h4>
                <p style="margin: 0; color: #dc2626;">Your booking is not confirmed until payment is completed. Please complete your payment before the due date to avoid cancellation.</p>
            </div>
            
            <!-- Payment Methods -->
            <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0369a1; margin-top: 0;">💳 Accepted Payment Methods</h4>
                <ul style="color: #0369a1; margin: 0;">
                    <li>Mobile Money (M-Pesa, Airtel Money)</li>
                    <li>Credit/Debit Cards</li>
                    <li>Bank Transfer</li>
                    <li>PayPal (where available)</li>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{payment_url}}" style="background-color: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 16px; margin: 5px;">
                    💳 Complete Payment Now
                </a>
            </div>
            
            <!-- Benefits of Completing Payment -->
            <div style="background-color: #f0fdf4; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #059669; margin-top: 0;">✅ What Happens After Payment?</h4>
                <ul style="color: #059669; margin: 0;">
                    <li>Your booking will be immediately confirmed</li>
                    <li>You'll receive your tickets via email</li>
                    <li>Your seats will be reserved</li>
                    <li>You'll get event reminders and updates</li>
                </ul>
            </div>
            
            <!-- Support Information -->
            <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #475569; margin-top: 0;">📞 Need Help with Payment?</h4>
                <p style="margin: 0; color: #475569;">If you're experiencing any issues with payment or have questions, please contact our support team immediately.</p>
            </div>
            
            <p>Don't miss out on your booking - complete your payment today!</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This payment reminder was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

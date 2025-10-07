<?php
/**
 * Withdrawal Request Template
 * Variables: {{name}}, {{amount}}, {{currency}}, {{request_date}}, {{account_type}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #f59e0b; margin: 0; font-size: 28px;">💰 Withdrawal Request Received</h1>
            <p style="color: #666; margin: 10px 0 0 0;">Your withdrawal request has been submitted successfully</p>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>We have received your withdrawal request. Here are the details:</p>
            
            <!-- Withdrawal Details -->
            <div style="background-color: #fffbeb; border: 2px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #f59e0b; margin-top: 0;">💸 Withdrawal Request Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7;"><strong>Request Date:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7; text-align: right;">{{request_date}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7;"><strong>Account Type:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7; text-align: right;">{{account_type}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Withdrawal Amount:</strong></td>
                        <td style="padding: 8px 0; text-align: right; font-size: 18px; font-weight: bold; color: #f59e0b;">{{currency}} {{amount}}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Processing Information -->
            <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0369a1; margin-top: 0;">⏳ Processing Information</h4>
                <p style="margin: 0; color: #0369a1;">Your withdrawal request is currently being reviewed by our admin team. You will receive an email notification once the status is updated.</p>
            </div>
            
            <!-- Timeline -->
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #92400e; margin-top: 0;">📅 Expected Timeline</h4>
                <ul style="color: #92400e; margin: 0;">
                    <li><strong>Review Period:</strong> 1-3 business days</li>
                    <li><strong>Approval:</strong> You'll receive confirmation email</li>
                    <li><strong>Processing:</strong> 2-5 business days after approval</li>
                    <li><strong>Completion:</strong> Funds will be transferred to your account</li>
                </ul>
            </div>
            
            <!-- Important Notes -->
            <div style="background-color: #fef2f2; border: 1px solid #ef4444; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #dc2626; margin-top: 0;">⚠️ Important Notes</h4>
                <ul style="color: #dc2626; margin: 0;">
                    <li>Withdrawal requests are subject to admin approval</li>
                    <li>Please ensure your account information is accurate</li>
                    <li>Processing times may vary during peak periods</li>
                    <li>Contact support if you have any questions</li>
                </ul>
            </div>
            
            <!-- Next Steps -->
            <div style="background-color: #f0fdf4; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #059669; margin-top: 0;">📋 What Happens Next?</h4>
                <ol style="color: #059669; margin: 0;">
                    <li>Our admin team will review your request</li>
                    <li>You'll receive an email with the decision</li>
                    <li>If approved, funds will be processed</li>
                    <li>You'll get a final confirmation email</li>
                </ol>
            </div>
            
            <p><strong>Questions?</strong><br>
            If you have any questions about your withdrawal request, please contact our support team.</p>
            
            <p>Thank you for your patience!</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This withdrawal request confirmation was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

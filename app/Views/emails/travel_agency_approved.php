<?php
/**
 * Travel Agency Approval Email Template
 * Variables: {{name}}, {{company_name}}, {{email}}, {{site_name}}, {{login_url}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #10b981; margin: 0; font-size: 28px;">🎉 Congratulations!</h1>
            <h2 style="color: #333; margin: 10px 0 0 0; font-size: 24px;">Your Travel Agency Has Been Approved</h2>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>Great news! Your travel agency account for <strong>{{company_name}}</strong> has been approved and is now active.</p>
            
            <div style="background-color: #f0fdf4; border: 2px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #10b981; margin-top: 0;">✅ Your Account Status: ACTIVE</h3>
                <p style="margin-bottom: 0;">You can now start creating travel destinations and accepting bookings!</p>
            </div>
            
            <p><strong>What you can do now:</strong></p>
            <ul style="color: #555; margin: 20px 0;">
                <li>✅ Create and publish travel destinations</li>
                <li>✅ Set pricing and booking policies</li>
                <li>✅ Upload destination photos and detailed descriptions</li>
                <li>✅ Manage customer bookings and inquiries</li>
                <li>✅ Track your earnings and commissions</li>
                <li>✅ Use our scanner system for ticket verification</li>
                <li>✅ Access detailed analytics and reports</li>
            </ul>
            
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #92400e;"><strong>💡 Pro Tip:</strong> Make sure to complete your phone verification if you haven't already. This is required to create destinations.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{login_url}}" style="background-color: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 16px;">
                    🚀 Start Creating Destinations
                </a>
            </div>
            
            <p><strong>Need Help Getting Started?</strong></p>
            <p>Here are some resources to help you make the most of your travel agency account:</p>
            <ul style="color: #555;">
                <li>Check out our <strong>Travel Agency Guide</strong> in your dashboard</li>
                <li>Review the <strong>Booking Management</strong> section</li>
                <li>Learn about our <strong>Scanner System</strong> for ticket verification</li>
                <li>Contact our support team if you have any questions</li>
            </ul>
            
            <p>We're excited to have you on board and look forward to seeing your amazing travel destinations!</p>
            
            <p>Happy traveling!</p>
            <p><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This email was sent to {{email}} because your travel agency account was approved.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved.</p>
        </div>
    </div>
</div>

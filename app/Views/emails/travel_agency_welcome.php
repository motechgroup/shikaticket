<?php
/**
 * Travel Agency Welcome Email Template
 * Variables: {{name}}, {{company_name}}, {{email}}, {{site_name}}, {{login_url}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #dc2626; margin: 0; font-size: 28px;">Welcome to {{site_name}}!</h1>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>Congratulations! Your travel agency account for <strong>{{company_name}}</strong> has been successfully created.</p>
            
            <p>Your account details:</p>
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 5px 0;"><strong>Company:</strong> {{company_name}}</p>
                <p style="margin: 5px 0;"><strong>Contact Person:</strong> {{name}}</p>
                <p style="margin: 5px 0;"><strong>Email:</strong> {{email}}</p>
                <p style="margin: 5px 0;"><strong>Status:</strong> Pending Approval</p>
            </div>
            
            <p><strong>Next Steps:</strong></p>
            <ol style="color: #555;">
                <li>Complete phone verification using the OTP sent to your phone</li>
                <li>Wait for admin approval (you'll be notified via email)</li>
                <li>Once approved, you can start creating travel destinations</li>
                <li>Manage bookings and track your earnings</li>
            </ol>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{login_url}}" style="background-color: #dc2626; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                    Access Your Dashboard
                </a>
            </div>
            
            <p><strong>What you can do as a Travel Agency:</strong></p>
            <ul style="color: #555;">
                <li>Create and manage travel destinations</li>
                <li>Set pricing and booking policies</li>
                <li>Upload destination photos and details</li>
                <li>Manage customer bookings</li>
                <li>Track earnings and commissions</li>
                <li>Use our scanner system for ticket verification</li>
            </ul>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            
            <p>Welcome aboard!</p>
            <p><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This email was sent to {{email}} because you registered a travel agency account.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved.</p>
        </div>
    </div>
</div>

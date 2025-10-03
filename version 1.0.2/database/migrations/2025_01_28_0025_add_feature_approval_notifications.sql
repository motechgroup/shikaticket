-- Add feature approval notification templates to settings
INSERT INTO settings (`key`, value, updated_at) VALUES
('sms.feature_approval_event', 'Congratulations! Your event "{event_title}" has been approved for featuring on our platform. It will now appear prominently with increased visibility. A 5% additional commission will apply to ticket sales. Thank you for choosing our platform!', NOW()),

('email.feature_approval_event_subject', 'Your Event "{event_title}" is Now Featured!', NOW()),

('email.feature_approval_event_body', '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Featured - ShikaTicket</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #e74c3c; }
        .logo { font-size: 24px; font-weight: bold; color: #e74c3c; }
        .content { padding: 30px 0; }
        .highlight { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .feature-badge { background-color: #f39c12; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .commission-info { background-color: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .btn { display: inline-block; background-color: #e74c3c; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .benefits { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .benefit-item { margin: 10px 0; padding: 5px 0; }
        .icon { color: #e74c3c; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎫 ShikaTicket</div>
            <p style="margin: 10px 0 0 0; color: #666;">Your Event is Now Featured!</p>
        </div>
        
        <div class="content">
            <h2>Congratulations, {organizer_name}!</h2>
            
            <div class="highlight">
                <span class="feature-badge">⭐ FEATURED EVENT</span>
                <h3 style="margin: 15px 0 10px 0;">{event_title}</h3>
                <p style="margin: 0; font-size: 16px; color: #2c3e50;">Your event has been approved for featuring on our platform!</p>
            </div>
            
            <div class="benefits">
                <h3 style="color: #e74c3c; margin-top: 0;">🚀 Featured Benefits:</h3>
                <div class="benefit-item">
                    <span class="icon">📈</span>
                    <strong>Increased Visibility</strong> - Your event appears at the top of search results and featured sections
                </div>
                <div class="benefit-item">
                    <span class="icon">👥</span>
                    <strong>More Attendees</strong> - Featured events typically see 3x more ticket sales
                </div>
                <div class="benefit-item">
                    <span class="icon">🎯</span>
                    <strong>Premium Placement</strong> - Your event gets priority in our marketing materials
                </div>
                <div class="benefit-item">
                    <span class="icon">⭐</span>
                    <strong>Featured Badge</strong> - Special "Featured" badge displayed on your event listing
                </div>
            </div>
            
            <div class="commission-info">
                <h4 style="color: #2e7d32; margin-top: 0;">💰 Commission Information</h4>
                <p style="margin: 10px 0;">A <strong>5% additional commission</strong> will be applied to all ticket sales for this featured event.</p>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #555;">This commission helps us maintain the premium featuring service and ensures continued high visibility for your event.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{event_url}" class="btn">View Your Featured Event</a>
            </div>
            
            <p style="font-size: 16px; color: #2c3e50; text-align: center; margin: 30px 0;">
                <strong>Thank you for choosing ShikaTicket!</strong><br>
                We''re excited to help make your event a huge success.
            </p>
            
            <p style="color: #666; font-size: 14px;">
                If you have any questions about your featured event or need assistance, please don''t hesitate to contact our support team.
            </p>
        </div>
        
        <div class="footer">
            <p>© 2025 ShikaTicket. All rights reserved.</p>
            <p>Nairobi, Kenya | info@example.com | +254 700 000 000</p>
        </div>
    </div>
</body>
</html>', NOW()),

('sms.feature_approval_destination', 'Congratulations! Your travel destination "{destination_title}" has been approved for featuring on our platform. It will now appear prominently with increased visibility. A 5% additional commission will apply to bookings. Thank you for choosing our platform!', NOW()),

('email.feature_approval_destination_subject', 'Your Travel Destination "{destination_title}" is Now Featured!', NOW()),

('email.feature_approval_destination_body', '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destination Featured - ShikaTicket</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #e74c3c; }
        .logo { font-size: 24px; font-weight: bold; color: #e74c3c; }
        .content { padding: 30px 0; }
        .highlight { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .feature-badge { background-color: #f39c12; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .commission-info { background-color: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .btn { display: inline-block; background-color: #e74c3c; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .benefits { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .benefit-item { margin: 10px 0; padding: 5px 0; }
        .icon { color: #e74c3c; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🌍 ShikaTicket Travel</div>
            <p style="margin: 10px 0 0 0; color: #666;">Your Destination is Now Featured!</p>
        </div>
        
        <div class="content">
            <h2>Congratulations, {agency_name}!</h2>
            
            <div class="highlight">
                <span class="feature-badge">⭐ FEATURED DESTINATION</span>
                <h3 style="margin: 15px 0 10px 0;">{destination_title}</h3>
                <p style="margin: 0; font-size: 16px; color: #2c3e50;">Your travel destination has been approved for featuring on our platform!</p>
            </div>
            
            <div class="benefits">
                <h3 style="color: #e74c3c; margin-top: 0;">🚀 Featured Benefits:</h3>
                <div class="benefit-item">
                    <span class="icon">📈</span>
                    <strong>Increased Visibility</strong> - Your destination appears at the top of search results and featured sections
                </div>
                <div class="benefit-item">
                    <span class="icon">👥</span>
                    <strong>More Bookings</strong> - Featured destinations typically see 3x more bookings
                </div>
                <div class="benefit-item">
                    <span class="icon">🎯</span>
                    <strong>Premium Placement</strong> - Your destination gets priority in our marketing materials
                </div>
                <div class="benefit-item">
                    <span class="icon">⭐</span>
                    <strong>Featured Badge</strong> - Special "Featured" badge displayed on your destination listing
                </div>
            </div>
            
            <div class="commission-info">
                <h4 style="color: #2e7d32; margin-top: 0;">💰 Commission Information</h4>
                <p style="margin: 10px 0;">A <strong>5% additional commission</strong> will be applied to all bookings for this featured destination.</p>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #555;">This commission helps us maintain the premium featuring service and ensures continued high visibility for your destination.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{destination_url}" class="btn">View Your Featured Destination</a>
            </div>
            
            <p style="font-size: 16px; color: #2c3e50; text-align: center; margin: 30px 0;">
                <strong>Thank you for choosing ShikaTicket Travel!</strong><br>
                We''re excited to help make your destination bookings soar.
            </p>
            
            <p style="color: #666; font-size: 14px;">
                If you have any questions about your featured destination or need assistance, please don''t hesitate to contact our support team.
            </p>
        </div>
        
        <div class="footer">
            <p>© 2025 ShikaTicket Travel. All rights reserved.</p>
            <p>Nairobi, Kenya | info@example.com | +254 700 000 000</p>
        </div>
    </div>
</body>
</html>', NOW());

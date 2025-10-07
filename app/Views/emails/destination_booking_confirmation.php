<?php
/**
 * Destination Booking Confirmation Template
 * Variables: {{name}}, {{destination_title}}, {{destination}}, {{departure_date}}, {{return_date}}, {{booking_reference}}, {{ticket_code}}, {{agency_name}}, {{agency_phone}}, {{agency_email}}, {{total_amount}}, {{currency}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #7c3aed; margin: 0; font-size: 28px;">✈️ Travel Booking Confirmed</h1>
            <p style="color: #666; margin: 10px 0 0 0;">Your travel destination has been successfully booked!</p>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>Congratulations! Your travel booking has been confirmed. Here are your travel details:</p>
            
            <!-- Travel Details Card -->
            <div style="background-color: #faf5ff; border: 2px solid #7c3aed; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #7c3aed; margin-top: 0;">🏝️ {{destination_title}}</h3>
                <p style="margin: 5px 0;"><strong>📍 Destination:</strong> {{destination}}</p>
                <p style="margin: 5px 0;"><strong>📅 Departure Date:</strong> {{departure_date}}</p>
                <p style="margin: 5px 0;"><strong>📅 Return Date:</strong> {{return_date}}</p>
                <p style="margin: 5px 0;"><strong>🎫 Booking Reference:</strong> <span style="font-family: monospace; background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">{{booking_reference}}</span></p>
                <p style="margin: 5px 0;"><strong>🎫 Ticket Code:</strong> <span style="font-family: monospace; background: #e5e7eb; padding: 2px 6px; border-radius: 4px;">{{ticket_code}}</span></p>
                <p style="margin: 5px 0;"><strong>💰 Total Amount:</strong> <span style="font-weight: bold; color: #7c3aed;">{{currency}} {{total_amount}}</span></p>
            </div>
            
            <!-- Travel Agency Contact -->
            <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0369a1; margin-top: 0;">🏢 Travel Agency Contact</h4>
                <p style="margin: 5px 0;"><strong>Company:</strong> {{agency_name}}</p>
                <p style="margin: 5px 0;"><strong>Phone:</strong> {{agency_phone}}</p>
                <p style="margin: 5px 0;"><strong>Email:</strong> {{agency_email}}</p>
                <p style="color: #0369a1; font-size: 14px; margin: 10px 0 0 0;">Contact your travel agency for any questions about your booking or itinerary details.</p>
            </div>
            
            <!-- Important Instructions -->
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #92400e; margin-top: 0;">📋 Important Instructions:</h4>
                <ul style="color: #92400e; margin: 0;">
                    <li>Save your booking reference and ticket code</li>
                    <li>Contact your travel agency for detailed itinerary</li>
                    <li>Arrive at departure location at least 2 hours early</li>
                    <li>Bring valid ID and travel documents</li>
                    <li>Check weather conditions and pack accordingly</li>
                    <li>Confirm any special requirements with the agency</li>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ticket_download_url}}" style="background-color: #7c3aed; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; margin: 5px;">
                    📱 Download Travel Ticket
                </a>
            </div>
            
            <!-- Travel Tips -->
            <div style="background-color: #f0fdf4; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #059669; margin-top: 0;">💡 Travel Tips:</h4>
                <ul style="color: #059669; margin: 0;">
                    <li>Keep copies of important documents</li>
                    <li>Inform your bank about travel plans</li>
                    <li>Pack essentials in carry-on luggage</li>
                    <li>Download offline maps of your destination</li>
                    <li>Check local customs and regulations</li>
                </ul>
            </div>
            
            <p><strong>Need Help?</strong><br>
            If you have any questions about your travel booking, please contact {{agency_name}} directly at {{agency_phone}} or {{agency_email}}.</p>
            
            <p>Have a wonderful trip!</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This travel booking confirmation was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

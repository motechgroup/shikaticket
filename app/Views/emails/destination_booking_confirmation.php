<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Booking Confirmation</title>
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
                            <h1 style="margin: 0 0 8px 0; color: #8b5cf6; font-size: 32px; font-weight: bold; text-align: center;">
                                ✈️ Travel Booking Confirmed!
                            </h1>
                            <p style="margin: 0 0 24px 0; color: #9ca3af; font-size: 16px; text-align: center;">
                                Your travel destination has been successfully booked
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Dear <strong><?php echo htmlspecialchars($name ?? 'Traveler'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Congratulations! Your travel booking has been confirmed. Get ready for an incredible journey! Here are your complete travel details:
                            </p>
                            
                            <!-- Travel Details Card -->
                            <div style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); padding: 24px; margin: 24px 0; border-radius: 8px;">
                                <h2 style="margin: 0 0 16px 0; color: #ffffff; font-size: 22px; font-weight: bold;">
                                    🏝️ <?php echo htmlspecialchars($destination_title ?? 'Travel Destination'); ?>
                                </h2>
                                <table style="width: 100%; color: #ffffff; font-size: 14px;">
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>📍 Destination:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($destination ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>📅 Departure Date:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($departure_date ?? 'TBD'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>📅 Return Date:</strong></td>
                                        <td style="padding: 6px 0;"><?php echo htmlspecialchars($return_date ?? 'TBD'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>🎫 Booking Reference:</strong></td>
                                        <td style="padding: 6px 0;">
                                            <span style="font-family: monospace; background-color: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; font-weight: bold;">
                                                <?php echo htmlspecialchars($booking_reference ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0;"><strong>🎫 Ticket Code:</strong></td>
                                        <td style="padding: 6px 0;">
                                            <span style="font-family: monospace; background-color: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; font-weight: bold;">
                                                <?php echo htmlspecialchars($ticket_code ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; font-size: 16px;"><strong>💰 Total Amount:</strong></td>
                                        <td style="padding: 6px 0; font-size: 18px; font-weight: bold;">
                                            <?php echo htmlspecialchars($currency ?? 'KES'); ?> <?php echo number_format((float)($total_amount ?? 0), 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Travel Agency Contact -->
                            <div style="background-color: #1f2937; border: 2px solid #3b82f6; padding: 20px; margin: 24px 0; border-radius: 6px;">
                                <h3 style="margin: 0 0 12px 0; color: #3b82f6; font-size: 16px; font-weight: bold;">
                                    🏢 Your Travel Agency:
                                </h3>
                                <table style="width: 100%; color: #d1d5db; font-size: 14px;">
                                    <tr>
                                        <td style="padding: 4px 0;"><strong>Company:</strong></td>
                                        <td style="padding: 4px 0;"><?php echo htmlspecialchars($agency_name ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px 0;"><strong>Phone:</strong></td>
                                        <td style="padding: 4px 0;"><?php echo htmlspecialchars($agency_phone ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px 0;"><strong>Email:</strong></td>
                                        <td style="padding: 4px 0;"><?php echo htmlspecialchars($agency_email ?? 'N/A'); ?></td>
                                    </tr>
                                </table>
                                <p style="margin: 12px 0 0 0; color: #9ca3af; font-size: 13px;">
                                    Contact your travel agency for any questions about your booking, itinerary details, or special requests.
                                </p>
                            </div>
                            
                            <!-- Download Button -->
                            <?php if (!empty($ticket_download_url)): ?>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo htmlspecialchars($ticket_download_url); ?>" 
                                           style="display: inline-block; padding: 14px 32px; background-color: #8b5cf6; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                            📱 Download Travel Ticket PDF
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
                                    <li>Save your booking reference and ticket code in a secure place</li>
                                    <li>Contact your travel agency for detailed itinerary and schedule</li>
                                    <li>Arrive at departure location at least 2 hours before departure time</li>
                                    <li>Bring valid government-issued ID and all required travel documents</li>
                                    <li>Check weather conditions and pack appropriate clothing</li>
                                    <li>Confirm any special requirements or dietary restrictions with the agency</li>
                                    <li>Save the travel agency contact information for emergencies</li>
                                </ul>
                            </div>
                            
                            <!-- Travel Tips -->
                            <div style="background-color: #1f2937; border-left: 4px solid #10b981; padding: 20px; margin: 24px 0; border-radius: 4px;">
                                <h3 style="margin: 0 0 12px 0; color: #10b981; font-size: 16px; font-weight: bold;">
                                    💡 Travel Tips for a Great Experience:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8; font-size: 14px;">
                                    <li>Make photocopies of all important documents (ID, passport, tickets)</li>
                                    <li>Inform your bank about travel plans to avoid card issues</li>
                                    <li>Pack essential items and medications in your carry-on luggage</li>
                                    <li>Download offline maps of your destination for easy navigation</li>
                                    <li>Check local customs, regulations, and cultural norms</li>
                                    <li>Keep emergency contacts handy at all times</li>
                                    <li>Travel insurance is highly recommended for peace of mind</li>
                                </ul>
                            </div>
                            
                            <!-- Pre-Travel Checklist -->
                            <div style="background-color: #1f2937; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #8b5cf6; font-weight: bold; font-size: 14px;">
                                    ✅ Pre-Travel Checklist:
                                </p>
                                <p style="margin: 8px 0 0 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                    □ Confirm travel dates with agency<br>
                                    □ Pack all necessary documents<br>
                                    □ Arrange transportation to departure point<br>
                                    □ Charge all electronic devices<br>
                                    □ Inform family/friends of travel plans
                                </p>
                            </div>
                            
                            <!-- Support Info -->
                            <div style="background-color: #1f2937; border: 1px solid #3b82f6; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #3b82f6; font-weight: bold; font-size: 14px;">
                                    💬 Need Help?
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    For questions about your travel booking, itinerary, or special requests, please contact <strong><?php echo htmlspecialchars($agency_name ?? 'your travel agency'); ?></strong> directly at 
                                    <strong><?php echo htmlspecialchars($agency_phone ?? 'N/A'); ?></strong> or 
                                    <strong><?php echo htmlspecialchars($agency_email ?? 'N/A'); ?></strong>.
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Have a wonderful and safe trip! We hope you create amazing memories on your journey! 🌍✨
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best regards,<br>
                                <strong style="color: #8b5cf6;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This travel booking confirmation was sent to your registered email address.
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

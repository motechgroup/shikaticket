<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Request Received</title>
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
                            <h1 style="margin: 0 0 8px 0; color: #fbbf24; font-size: 32px; font-weight: bold; text-align: center;">
                                💰 Withdrawal Request Received
                            </h1>
                            <p style="margin: 0 0 24px 0; color: #9ca3af; font-size: 16px; text-align: center;">
                                Your withdrawal request has been submitted successfully
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Dear <strong><?php echo htmlspecialchars($name ?? 'User'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                We have successfully received your withdrawal request. Here are the complete details:
                            </p>
                            
                            <!-- Withdrawal Details Card -->
                            <div style="background-color: #1f2937; border-left: 4px solid #fbbf24; padding: 24px; margin: 24px 0; border-radius: 6px;">
                                <h2 style="margin: 0 0 16px 0; color: #fbbf24; font-size: 18px; font-weight: bold;">
                                    💸 Withdrawal Request Details
                                </h2>
                                <table style="width: 100%; color: #d1d5db; font-size: 14px; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151;"><strong>Request Date:</strong></td>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151; text-align: right;">
                                            <?php echo htmlspecialchars($request_date ?? date('Y-m-d H:i:s')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151;"><strong>Account Type:</strong></td>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151; text-align: right;">
                                            <?php echo htmlspecialchars($account_type ?? 'N/A'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; font-size: 16px;"><strong>Withdrawal Amount:</strong></td>
                                        <td style="padding: 10px 0; text-align: right; font-size: 22px; font-weight: bold; color: #fbbf24;">
                                            <?php echo htmlspecialchars($currency ?? 'KES'); ?> <?php echo number_format((float)($amount ?? 0), 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Status Badge -->
                            <div style="background-color: #1f2937; border: 2px solid #3b82f6; padding: 16px; margin: 24px 0; border-radius: 6px; text-align: center;">
                                <p style="margin: 0; color: #3b82f6; font-weight: bold; font-size: 16px;">
                                    ⏳ Status: <span style="background-color: rgba(59, 130, 246, 0.2); padding: 4px 12px; border-radius: 4px;">PENDING REVIEW</span>
                                </p>
                            </div>
                            
                            <!-- Processing Timeline -->
                            <div style="background-color: #1f2937; border: 2px solid #fbbf24; padding: 20px; margin: 24px 0; border-radius: 6px;">
                                <h3 style="margin: 0 0 12px 0; color: #fbbf24; font-size: 16px; font-weight: bold;">
                                    📅 Expected Processing Timeline:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8; font-size: 14px;">
                                    <li><strong style="color: #fbbf24;">Review Period:</strong> 1-3 business days for admin evaluation</li>
                                    <li><strong style="color: #fbbf24;">Approval Notification:</strong> You'll receive a confirmation email</li>
                                    <li><strong style="color: #fbbf24;">Processing Time:</strong> 2-5 business days after approval</li>
                                    <li><strong style="color: #fbbf24;">Completion:</strong> Funds will be transferred to your account</li>
                                </ul>
                            </div>
                            
                            <!-- What Happens Next -->
                            <div style="background-color: #1f2937; border-left: 4px solid #10b981; padding: 20px; margin: 24px 0; border-radius: 4px;">
                                <h3 style="margin: 0 0 12px 0; color: #10b981; font-size: 16px; font-weight: bold;">
                                    📋 What Happens Next?
                                </h3>
                                <ol style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8; font-size: 14px;">
                                    <li>Our admin team will thoroughly review your withdrawal request</li>
                                    <li>You'll receive an email notification with the approval decision</li>
                                    <li>If approved, your funds will be processed immediately</li>
                                    <li>You'll receive a final confirmation email once transferred</li>
                                    <li>Check your account within the specified timeline</li>
                                </ol>
                            </div>
                            
                            <!-- Important Notes -->
                            <div style="background-color: #1f2937; border: 2px solid #ef4444; padding: 20px; margin: 24px 0; border-radius: 6px;">
                                <h3 style="margin: 0 0 12px 0; color: #ef4444; font-size: 16px; font-weight: bold;">
                                    ⚠️ Important Notes:
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8; font-size: 14px;">
                                    <li>All withdrawal requests are subject to admin approval and verification</li>
                                    <li>Please ensure your registered account information is accurate and up-to-date</li>
                                    <li>Processing times may vary during peak periods or holidays</li>
                                    <li>Withdrawal requests cannot be cancelled once approved</li>
                                    <li>Contact support immediately if you notice any discrepancies</li>
                                </ul>
                            </div>
                            
                            <!-- Support Info -->
                            <div style="background-color: #1f2937; border: 1px solid #3b82f6; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #3b82f6; font-weight: bold; font-size: 14px;">
                                    💬 Questions About Your Withdrawal?
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    If you have any questions or concerns about your withdrawal request, please don't hesitate to contact our support team. We're here to assist you throughout the process.
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Thank you for your patience while we process your withdrawal request.
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best regards,<br>
                                <strong style="color: #fbbf24;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This withdrawal request confirmation was sent to your registered email address.
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

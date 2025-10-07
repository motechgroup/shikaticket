<?php
/**
 * Withdrawal Status Update Template
 * Variables: {{name}}, {{amount}}, {{currency}}, {{status}}, {{admin_notes}}, {{update_date}}, {{account_type}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <?php if ($status === 'approved'): ?>
                <h1 style="color: #10b981; margin: 0; font-size: 28px;">✅ Withdrawal Approved</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Great news! Your withdrawal has been approved</p>
            <?php elseif ($status === 'paid'): ?>
                <h1 style="color: #10b981; margin: 0; font-size: 28px;">💰 Payment Completed</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Your withdrawal has been processed and paid</p>
            <?php elseif ($status === 'rejected'): ?>
                <h1 style="color: #dc2626; margin: 0; font-size: 28px;">❌ Withdrawal Rejected</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Your withdrawal request has been rejected</p>
            <?php else: ?>
                <h1 style="color: #f59e0b; margin: 0; font-size: 28px;">📋 Withdrawal Status Update</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Your withdrawal request status has been updated</p>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>Your withdrawal request status has been updated. Here are the details:</p>
            
            <!-- Status Details -->
            <?php if ($status === 'approved'): ?>
                <div style="background-color: #f0fdf4; border: 2px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #10b981; margin-top: 0;">✅ Withdrawal Approved</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Amount:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right; font-size: 18px; font-weight: bold; color: #10b981;">{{currency}} {{amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Account Type:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right;">{{account_type}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Approved Date:</strong></td>
                            <td style="padding: 8px 0; text-align: right;">{{update_date}}</td>
                        </tr>
                    </table>
                </div>
                
                <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #0369a1; margin-top: 0;">⏳ Next Steps</h4>
                    <p style="margin: 0; color: #0369a1;">Your withdrawal has been approved and is now being processed. You should receive your funds within 2-5 business days.</p>
                </div>
                
            <?php elseif ($status === 'paid'): ?>
                <div style="background-color: #f0fdf4; border: 2px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #10b981; margin-top: 0;">💰 Payment Completed</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Amount Paid:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right; font-size: 18px; font-weight: bold; color: #10b981;">{{currency}} {{amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Account Type:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right;">{{account_type}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Payment Date:</strong></td>
                            <td style="padding: 8px 0; text-align: right;">{{update_date}}</td>
                        </tr>
                    </table>
                </div>
                
                <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #0369a1; margin-top: 0;">✅ Transaction Complete</h4>
                    <p style="margin: 0; color: #0369a1;">Your withdrawal has been successfully processed and the funds have been transferred to your account.</p>
                </div>
                
            <?php elseif ($status === 'rejected'): ?>
                <div style="background-color: #fef2f2; border: 2px solid #dc2626; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #dc2626; margin-top: 0;">❌ Withdrawal Rejected</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fecaca;"><strong>Requested Amount:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fecaca; text-align: right;">{{currency}} {{amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fecaca;"><strong>Account Type:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fecaca; text-align: right;">{{account_type}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Rejected Date:</strong></td>
                            <td style="padding: 8px 0; text-align: right;">{{update_date}}</td>
                        </tr>
                    </table>
                </div>
                
                <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #92400e; margin-top: 0;">📝 Reason for Rejection</h4>
                    <?php if (!empty($admin_notes)): ?>
                        <p style="margin: 0; color: #92400e;">{{admin_notes}}</p>
                    <?php else: ?>
                        <p style="margin: 0; color: #92400e;">Please contact support for more information about the rejection reason.</p>
                    <?php endif; ?>
                </div>
                
            <?php else: ?>
                <div style="background-color: #fffbeb; border: 2px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #f59e0b; margin-top: 0;">📋 Status Update</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7;"><strong>Amount:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7; text-align: right;">{{currency}} {{amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7;"><strong>Status:</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #fef3c7; text-align: right; text-transform: capitalize;">{{status}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Updated Date:</strong></td>
                            <td style="padding: 8px 0; text-align: right;">{{update_date}}</td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>
            
            <!-- Admin Notes -->
            <?php if (!empty($admin_notes) && $status !== 'rejected'): ?>
                <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #475569; margin-top: 0;">📝 Admin Notes</h4>
                    <p style="margin: 0; color: #475569;">{{admin_notes}}</p>
                </div>
            <?php endif; ?>
            
            <!-- Contact Information -->
            <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #0369a1; margin-top: 0;">📞 Need Help?</h4>
                <p style="margin: 0; color: #0369a1;">If you have any questions about your withdrawal request or need assistance, please contact our support team.</p>
            </div>
            
            <p>Thank you for using {{site_name}}!</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This withdrawal status update was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

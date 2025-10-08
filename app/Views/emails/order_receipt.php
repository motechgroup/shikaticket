<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
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
                            <h1 style="margin: 0 0 8px 0; color: #10b981; font-size: 32px; font-weight: bold; text-align: center;">
                                🧾 Order Receipt
                            </h1>
                            <p style="margin: 0 0 24px 0; color: #9ca3af; font-size: 16px; text-align: center;">
                                Thank you for your purchase!
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Dear <strong><?php echo htmlspecialchars($name ?? 'Customer'); ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Thank you for your purchase! Your payment has been processed successfully. Here's your official receipt:
                            </p>
                            
                            <!-- Order Summary Card -->
                            <div style="background-color: #1f2937; border-left: 4px solid #10b981; padding: 24px; margin: 24px 0; border-radius: 6px;">
                                <h2 style="margin: 0 0 16px 0; color: #10b981; font-size: 18px; font-weight: bold;">
                                    📋 Order Summary
                                </h2>
                                <table style="width: 100%; color: #d1d5db; font-size: 14px; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151;"><strong>Order ID:</strong></td>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151; text-align: right; font-family: monospace; color: #10b981;">
                                            <?php echo htmlspecialchars($order_id ?? 'N/A'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151;"><strong>Order Date:</strong></td>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151; text-align: right;">
                                            <?php echo htmlspecialchars($order_date ?? date('Y-m-d H:i:s')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151;"><strong>Payment Method:</strong></td>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #374151; text-align: right;">
                                            <?php echo htmlspecialchars($payment_method ?? 'M-Pesa'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; font-size: 16px;"><strong>Total Amount:</strong></td>
                                        <td style="padding: 10px 0; text-align: right; font-size: 22px; font-weight: bold; color: #10b981;">
                                            <?php echo htmlspecialchars($currency ?? 'KES'); ?> <?php echo number_format((float)($total_amount ?? 0), 2); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Payment Status Badge -->
                            <div style="background-color: #1f2937; border: 2px solid #10b981; padding: 16px; margin: 24px 0; border-radius: 6px; text-align: center;">
                                <p style="margin: 0; color: #10b981; font-weight: bold; font-size: 16px;">
                                    ✅ Payment Status: <span style="background-color: rgba(16, 185, 129, 0.2); padding: 4px 12px; border-radius: 4px;">COMPLETED SUCCESSFULLY</span>
                                </p>
                            </div>
                            
                            <!-- Order Items Section (Optional - can be customized based on order details) -->
                            <div style="margin: 24px 0;">
                                <h3 style="margin: 0 0 12px 0; color: #d1d5db; font-size: 16px; font-weight: bold;">
                                    🛒 Order Details:
                                </h3>
                                <div style="background-color: #1f2937; border-radius: 6px; overflow: hidden;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background-color: #374151;">
                                                <th style="padding: 12px; text-align: left; color: #d1d5db; font-size: 13px; border-bottom: 1px solid #4b5563;">Item</th>
                                                <th style="padding: 12px; text-align: center; color: #d1d5db; font-size: 13px; border-bottom: 1px solid #4b5563;">Qty</th>
                                                <th style="padding: 12px; text-align: right; color: #d1d5db; font-size: 13px; border-bottom: 1px solid #4b5563;">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding: 12px; color: #9ca3af; font-size: 13px; border-bottom: 1px solid #374151;">
                                                    <?php echo htmlspecialchars($item_name ?? 'Event Ticket(s)'); ?>
                                                </td>
                                                <td style="padding: 12px; text-align: center; color: #9ca3af; font-size: 13px; border-bottom: 1px solid #374151;">
                                                    <?php echo htmlspecialchars($item_quantity ?? '1'); ?>
                                                </td>
                                                <td style="padding: 12px; text-align: right; color: #9ca3af; font-size: 13px; border-bottom: 1px solid #374151;">
                                                    <?php echo htmlspecialchars($currency ?? 'KES'); ?> <?php echo number_format((float)($item_price ?? $total_amount ?? 0), 2); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- What's Next Section -->
                            <div style="background-color: #1f2937; border: 2px solid #fbbf24; padding: 20px; margin: 24px 0; border-radius: 6px;">
                                <h3 style="margin: 0 0 12px 0; color: #fbbf24; font-size: 16px; font-weight: bold;">
                                    📋 What's Next?
                                </h3>
                                <ul style="margin: 0; padding-left: 20px; color: #d1d5db; line-height: 1.8; font-size: 14px;">
                                    <li>Check your email for individual ticket confirmations</li>
                                    <li>Download your tickets from the confirmation emails</li>
                                    <li>Save your tickets to your phone or print them</li>
                                    <li>Bring valid ID matching your ticket name to the events</li>
                                    <li>Show your QR code at the entrance for quick check-in</li>
                                    <li>Contact support if you have any questions</li>
                                </ul>
                            </div>
                            
                            <!-- Transaction Info -->
                            <div style="background-color: #1f2937; padding: 16px; margin: 24px 0; border-radius: 6px;">
                                <p style="margin: 0; color: #3b82f6; font-weight: bold; font-size: 14px;">
                                    📄 Transaction Details:
                                </p>
                                <p style="margin: 8px 0 0 0; color: #9ca3af; font-size: 13px; line-height: 1.6;">
                                    This is your official receipt for order #<?php echo htmlspecialchars($order_id ?? 'N/A'); ?>. Please keep this for your records. If you need an invoice or have any questions, please contact our support team.
                                </p>
                            </div>
                            
                            <!-- Support Info -->
                            <div style="background-color: #1f2937; border-left: 4px solid #3b82f6; padding: 16px; margin: 24px 0; border-radius: 4px;">
                                <p style="margin: 0; color: #3b82f6; font-weight: bold; font-size: 14px;">
                                    💬 Customer Support
                                </p>
                                <p style="margin: 8px 0 0 0; color: #d1d5db; font-size: 14px; line-height: 1.6;">
                                    If you have any questions about your order, payment, or tickets, please don't hesitate to contact our support team. We're here to help make your experience amazing!
                                </p>
                            </div>
                            
                            <p style="margin: 24px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Thank you for choosing <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?>! We appreciate your business.
                            </p>
                            
                            <p style="margin: 16px 0 0 0; color: #d1d5db; font-size: 16px; line-height: 1.6;">
                                Best regards,<br>
                                <strong style="color: #10b981;">The <?php echo htmlspecialchars($site_name ?? 'ShikaTicket'); ?> Team</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #0b0b0b; border-top: 1px solid #1f2937; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; text-align: center;">
                                This receipt was sent to your registered email address.
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

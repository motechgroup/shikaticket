<?php
/**
 * Order Receipt Template
 * Variables: {{name}}, {{order_id}}, {{order_date}}, {{total_amount}}, {{currency}}, {{payment_method}}, {{site_name}}
 */
?>
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="{{site_name}}" style="height: 60px; margin-bottom: 20px;">
            <h1 style="color: #059669; margin: 0; font-size: 28px;">🧾 Order Receipt</h1>
            <p style="color: #666; margin: 10px 0 0 0;">Thank you for your purchase!</p>
        </div>

        <!-- Content -->
        <div style="color: #333; line-height: 1.6;">
            <p>Dear {{name}},</p>
            
            <p>Thank you for your purchase! Here's your order receipt:</p>
            
            <!-- Order Summary -->
            <div style="background-color: #f0fdf4; border: 2px solid #059669; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #059669; margin-top: 0;">📋 Order Summary</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Order ID:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right; font-family: monospace;">{{order_id}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Order Date:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right;">{{order_date}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5;"><strong>Payment Method:</strong></td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #d1fae5; text-align: right;">{{payment_method}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>Total Amount:</strong></td>
                        <td style="padding: 8px 0; text-align: right; font-size: 18px; font-weight: bold; color: #059669;">{{currency}} {{total_amount}}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Items Table -->
            <div style="margin: 20px 0;">
                <h4 style="color: #333; margin-bottom: 15px;">🛒 Order Items</h4>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f9fafb;">
                                <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Item</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 1px solid #e5e7eb;">Quantity</th>
                                <th style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">Price</th>
                                <th style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- This will be populated with order items -->
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">{{item_name}}</td>
                                <td style="padding: 12px; text-align: center; border-bottom: 1px solid #f3f4f6;">{{item_quantity}}</td>
                                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #f3f4f6;">{{currency}} {{item_price}}</td>
                                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #f3f4f6;">{{currency}} {{item_total}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Payment Status -->
            <div style="background-color: #dbeafe; border: 1px solid #3b82f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #1e40af;"><strong>✅ Payment Status:</strong> Completed Successfully</p>
            </div>
            
            <!-- Next Steps -->
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #92400e; margin-top: 0;">📋 What's Next?</h4>
                <ul style="color: #92400e; margin: 0;">
                    <li>Check your email for individual ticket confirmations</li>
                    <li>Download your tickets from the confirmation emails</li>
                    <li>Save your tickets to your phone or print them</li>
                    <li>Bring valid ID to the events</li>
                    <li>Contact support if you have any questions</li>
                </ul>
            </div>
            
            <p><strong>Customer Support</strong><br>
            If you have any questions about your order, please contact our support team or refer to your ticket confirmation emails.</p>
            
            <p>Thank you for choosing {{site_name}}!</p>
            
            <p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #666; font-size: 14px;">
            <p>This receipt was sent to your registered email address.</p>
            <p>&copy; <?php echo date('Y'); ?> {{site_name}}. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.3'; ?></p>
        </div>
    </div>
</div>

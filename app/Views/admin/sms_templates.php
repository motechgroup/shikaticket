<?php /** @var array $templates */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
	<?php $pageTitle = 'SMS Templates'; include __DIR__ . '/../components/mobile_nav_simple.php'; ?>
    <h1 class="text-2xl font-semibold mb-6">SMS Templates</h1>
    <form method="post" action="<?php echo base_url('/admin/sms-templates'); ?>" class="space-y-6 card p-6">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm mb-1">Welcome User</label>
            <textarea class="textarea" name="templates[welcome_user]" rows="2"><?php echo htmlspecialchars($templates['welcome_user'] ?? 'Welcome to ShikaTicket!'); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: none</div>
        </div>
        <div>
            <label class="block text-sm mb-1">Payment Success</label>
            <textarea class="textarea" name="templates[payment_success]" rows="2"><?php echo htmlspecialchars($templates['payment_success'] ?? ''); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: {{order_id}}, {{tickets}}</div>
        </div>
        <div>
            <label class="block text-sm mb-1">User Login OTP</label>
            <textarea class="textarea" name="templates[user_otp]" rows="2"><?php echo htmlspecialchars($templates['user_otp'] ?? 'Your ShikaTicket login code is {{otp}}'); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: {{otp}}</div>
        </div>
        <div>
            <label class="block text-sm mb-1">Organizer OTP</label>
            <textarea class="textarea" name="templates[organizer_otp]" rows="2"><?php echo htmlspecialchars($templates['organizer_otp'] ?? 'Your ShikaTicket OTP: {{otp}}'); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: {{otp}}</div>
        </div>
        <div>
            <label class="block text-sm mb-1">Withdrawal Request Confirmation</label>
            <textarea class="textarea" name="templates[withdrawal_request]" rows="2"><?php echo htmlspecialchars($templates['withdrawal_request'] ?? ''); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: {{amount}}</div>
        </div>
        <div>
            <label class="block text-sm mb-1">Travel Booking Confirmation</label>
            <textarea class="textarea" name="templates[travel_booking_confirmed]" rows="3"><?php echo htmlspecialchars($templates['travel_booking_confirmed'] ?? 'Travel booking confirmed! Destination: {{destination}}. Ticket Code: {{ticket_code}}. Booking Reference: {{booking_reference}}. View ticket: {{ticket_link}}. Contact: {{agency_name}} at {{agency_phone}}'); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: {{destination}}, {{ticket_code}}, {{booking_reference}}, {{ticket_link}}, {{agency_name}}, {{agency_phone}}</div>
        </div>
        <button class="btn btn-primary">Save Templates</button>
    </form>

    <!-- SMS Test Section -->
    <div class="card p-6 mt-8">
        <h2 class="text-xl font-semibold mb-4">Test SMS Gateway</h2>
        <p class="text-gray-400 mb-4">Send a test SMS to verify that your SMS gateway is working correctly.</p>
        
        <form method="post" action="<?php echo base_url('/admin/sms-test'); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm mb-1">Phone Number</label>
                <input type="text" name="phone_number" class="input" placeholder="254700000000" required>
                <div class="text-xs text-gray-400 mt-1">Enter phone number in international format (e.g., 254700000000)</div>
            </div>
            <div>
                <label class="block text-sm mb-1">Test Message</label>
                <textarea name="message" class="textarea" rows="3" placeholder="Test SMS from ShikaTicket Admin Panel">Test SMS from ShikaTicket Admin Panel - Gateway is working correctly!</textarea>
            </div>
            <button type="submit" class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Send Test SMS
            </button>
        </form>
        
        <div class="mt-4 p-4 bg-blue-900/20 border border-blue-600/30 rounded-lg">
            <h3 class="font-semibold text-blue-300 mb-2">SMS Gateway Status</h3>
            <div class="text-sm text-blue-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                    <span>Provider: <?php echo \App\Models\Setting::get('sms.provider', 'Not configured'); ?></span>
                </div>
                <div class="text-xs text-blue-300">
                    Check the SMS logs below to see recent SMS activity and verify delivery status.
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Logs Section -->
    <div class="card p-6 mt-8">
        <h2 class="text-xl font-semibold mb-4">Recent SMS Logs</h2>
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2">Date/Time</th>
                        <th class="text-left py-2">Provider</th>
                        <th class="text-left py-2">Recipient</th>
                        <th class="text-left py-2">Status</th>
                        <th class="text-left py-2">Message Preview</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = \db()->prepare('SELECT * FROM sms_logs ORDER BY created_at DESC LIMIT 10');
                        $stmt->execute();
                        $logs = $stmt->fetchAll();
                        
                        if (empty($logs)) {
                            echo '<tr><td colspan="5" class="py-4 text-center text-gray-400">No SMS logs found</td></tr>';
                        } else {
                            foreach ($logs as $log) {
                                $statusColor = $log['status'] === 'sent' ? 'text-green-400' : 'text-red-400';
                                $statusIcon = $log['status'] === 'sent' ? '✓' : '✗';
                                $messagePreview = strlen($log['message']) > 50 ? substr($log['message'], 0, 50) . '...' : $log['message'];
                                
                                echo '<tr class="border-b border-gray-800">';
                                echo '<td class="py-2 text-sm">' . date('M j, Y H:i', strtotime($log['created_at'])) . '</td>';
                                echo '<td class="py-2 text-sm">' . htmlspecialchars($log['provider']) . '</td>';
                                echo '<td class="py-2 text-sm">' . htmlspecialchars($log['recipient']) . '</td>';
                                echo '<td class="py-2 text-sm ' . $statusColor . '">' . $statusIcon . ' ' . htmlspecialchars($log['status']) . '</td>';
                                echo '<td class="py-2 text-sm text-gray-400">' . htmlspecialchars($messagePreview) . '</td>';
                                echo '</tr>';
                            }
                        }
                    } catch (\Throwable $e) {
                        echo '<tr><td colspan="5" class="py-4 text-center text-red-400">Error loading SMS logs</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



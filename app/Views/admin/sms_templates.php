<?php /** @var array $templates */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">SMS Templates</h1>
    <form method="post" action="<?php echo base_url('/admin/sms-templates'); ?>" class="space-y-6 card p-6">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm mb-1">Welcome User</label>
            <textarea class="textarea" name="templates[welcome_user]" rows="2"><?php echo htmlspecialchars($templates['welcome_user'] ?? ''); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: none</div>
        </div>
        <div>
            <label class="block text-sm mb-1">Payment Success</label>
            <textarea class="textarea" name="templates[payment_success]" rows="2"><?php echo htmlspecialchars($templates['payment_success'] ?? ''); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Placeholders: {{order_id}}, {{tickets}}</div>
        </div>
        <div>
            <label class="block text-sm mb-1">Organizer OTP</label>
            <textarea class="textarea" name="templates[organizer_otp]" rows="2"><?php echo htmlspecialchars($templates['organizer_otp'] ?? ''); ?></textarea>
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
</div>



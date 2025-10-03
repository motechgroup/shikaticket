<?php /** @var array $templates */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">Email Templates</h1>
		<a class="btn btn-secondary" href="<?php echo base_url('/admin'); ?>">Back</a>
	</div>
    <form method="post" action="<?php echo base_url('/admin/email-templates'); ?>" class="card p-6 space-y-6">
		<?php echo csrf_field(); ?>
        <div class="grid gap-6">
            <div>
                <label class="block font-semibold mb-2">User Welcome (HTML)</label>
                <textarea name="user_welcome" rows="10" class="textarea" style="min-height:220px"><?php echo htmlspecialchars($templates['user_welcome'] ?: file_get_contents(__DIR__ . '/../emails/user_welcome.php')); ?></textarea>
            </div>
            <div>
                <label class="block font-semibold mb-2">Organizer Approved (HTML)</label>
                <textarea name="organizer_approved" rows="10" class="textarea" style="min-height:220px"><?php echo htmlspecialchars($templates['organizer_approved'] ?: file_get_contents(__DIR__ . '/../emails/organizer_approved.php')); ?></textarea>
            </div>
            <div>
                <label class="block font-semibold mb-2">Password Reset (HTML)</label>
                <textarea name="password_reset" rows="10" class="textarea" style="min-height:220px"><?php echo htmlspecialchars($templates['password_reset'] ?? '<h1>Password reset</h1><p>Click the link to reset your password: {{reset_link}}</p>'); ?></textarea>
            </div>
            <div>
                <label class="block font-semibold mb-2">Email Verification (HTML)</label>
                <textarea name="verify_email" rows="10" class="textarea" style="min-height:220px"><?php echo htmlspecialchars($templates['verify_email'] ?? '<h1>Verify your email</h1><p>Enter this code or click the link: {{code}} {{verify_link}}</p>'); ?></textarea>
            </div>
            <div>
                <label class="block font-semibold mb-2">Ticket Confirmation (HTML)</label>
                <textarea name="ticket_confirmation" rows="10" class="textarea" style="min-height:220px"><?php echo htmlspecialchars($templates['ticket_confirmation'] ?? '<h1>Your ticket is confirmed</h1><p>Event: {{event_title}} on {{event_date}} at {{venue}}. Order #{{order_id}}</p>'); ?></textarea>
            </div>
            <div>
                <label class="block font-semibold mb-2">Order Receipt (HTML)</label>
                <textarea name="order_receipt" rows="10" class="textarea" style="min-height:220px"><?php echo htmlspecialchars($templates['order_receipt'] ?? '<h1>Receipt</h1><p>Amount: {{amount}} {{currency}}. Order #{{order_id}}</p>'); ?></textarea>
            </div>
        </div>
		<button class="btn btn-primary">Save Templates</button>
	</form>
</div>



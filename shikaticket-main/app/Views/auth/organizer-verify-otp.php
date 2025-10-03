<div class="max-w-md mx-auto px-4 py-12">
	<h1 class="text-2xl font-semibold mb-6">Verify Phone</h1>
	<form method="post" action="<?php echo base_url('/organizer/verify-otp'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<div>
			<label class="block text-sm mb-1">Enter OTP sent to your phone</label>
			<input name="otp" maxlength="6" class="input" placeholder="6-digit code" required>
		</div>
		<button class="btn btn-primary w-full">Verify</button>
	</form>
	<form method="post" action="<?php echo base_url('/organizer/verify-otp/resend'); ?>" class="mt-4 card p-6">
		<?php echo csrf_field(); ?>
		<div class="flex items-center gap-2">
			<input class="input" type="email" name="email" placeholder="Organizer email (if asked)">
			<button class="btn btn-secondary">Resend OTP</button>
		</div>
		<p class="text-xs text-gray-400 mt-2">If you just registered, you may not need to fill the email field.</p>
	</form>
</div>



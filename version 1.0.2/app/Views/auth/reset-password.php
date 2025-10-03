<?php /** @var string $token */ ?>
<div class="max-w-md mx-auto px-4 py-12">
	<h1 class="text-2xl font-semibold mb-6">Reset Password</h1>
	<form method="post" action="<?php echo base_url('/password/reset'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
		<div>
			<label class="block text-sm mb-1">New Password</label>
			<input name="password" type="password" required class="input">
		</div>
		<button class="btn btn-primary w-full">Update password</button>
	</form>
</div>



<div class="max-w-md mx-auto px-4 py-12">
	<h1 class="text-2xl font-semibold mb-6">Forgot Password</h1>
	<form method="post" action="<?php echo base_url('/password/forgot'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<div>
			<label class="block text-sm mb-1">Email</label>
			<input name="email" type="email" required class="input" placeholder="you@example.com">
		</div>
		<button class="btn btn-primary w-full">Send reset link</button>
	</form>
</div>



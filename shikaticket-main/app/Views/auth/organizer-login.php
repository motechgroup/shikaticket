<div class="max-w-md mx-auto px-4 py-12">
	<h1 class="text-2xl font-semibold mb-6">Organizer Login</h1>
	<form method="post" action="<?php echo base_url('/organizer/login'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<div>
			<label class="block text-sm mb-1">Email</label>
			<input name="email" type="email" required class="input">
		</div>
		<div>
			<label class="block text-sm mb-1">Password</label>
			<input name="password" type="password" required class="input">
		</div>
		<button class="w-full btn btn-primary">Login</button>
		<div class="flex justify-between items-center text-sm text-gray-400">
			<a class="link" href="<?php echo base_url('/password-reset?type=organizer'); ?>">Forgot Password?</a>
			<a class="link" href="<?php echo base_url('/organizer/register'); ?>">New organizer?</a>
		</div>
	</form>
</div>



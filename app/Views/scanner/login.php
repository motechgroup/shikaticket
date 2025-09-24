<div class="max-w-md mx-auto px-4 py-12">
	<h1 class="text-2xl font-semibold mb-6">Scanner Login</h1>
	<form method="post" action="<?php echo base_url('/scanner/login'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<div>
			<label class="block text-sm mb-1">Organizer Email</label>
			<input name="email" type="email" required class="input">
		</div>
		<div>
			<label class="block text-sm mb-1">Password</label>
			<input name="password" type="password" required class="input">
		</div>
		<button class="btn btn-primary w-full">Login</button>
	</form>
</div>



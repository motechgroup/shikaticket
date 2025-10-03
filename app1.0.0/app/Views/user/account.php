<?php /** @var array $user */ ?>
<div class="max-w-6xl mx-auto px-4 py-10 grid md:grid-cols-3 gap-6">
	<aside class="card p-4 h-max">
		<h2 class="font-semibold mb-3">Menu</h2>
		<nav class="space-y-2 text-sm">
			<a class="block link" href="<?php echo base_url('/user/dashboard'); ?>">Overview</a>
			<a class="block link" href="<?php echo base_url('/user/orders'); ?>">My Orders</a>
			<a class="block link" href="<?php echo base_url('/user/account'); ?>">Account</a>
			<a class="block link" href="<?php echo base_url('/logout'); ?>">Logout</a>
		</nav>
	</aside>
	<section class="md:col-span-2">
		<h1 class="text-2xl font-semibold mb-6">Account</h1>
		<form method="post" action="<?php echo base_url('/user/account'); ?>" class="card p-6 space-y-4">
			<?php echo csrf_field(); ?>
			<div>
				<label class="block text-sm mb-1">Phone</label>
				<input name="phone" class="input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
			</div>
			<div>
				<label class="block text-sm mb-1">Email</label>
				<input name="email" type="email" class="input" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
			</div>
			<button class="btn btn-primary">Save</button>
		</form>
	</section>
</div>



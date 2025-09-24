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
		<h1 class="text-2xl font-semibold mb-6">User Dashboard</h1>
		<div class="card p-6">
			<p class="text-gray-300">Welcome to your dashboard. Browse and manage your tickets here.</p>
			<div class="mt-4 flex gap-3">
				<a href="<?php echo base_url('/'); ?>" class="btn btn-secondary">Browse Events</a>
				<a href="<?php echo base_url('/user/orders'); ?>" class="btn btn-primary">My Orders</a>
			</div>
		</div>
	</section>
</div>



<div class="max-w-6xl mx-auto px-4 py-10 grid md:grid-cols-3 gap-6">
	<aside class="card p-4 h-max">
		<h2 class="font-semibold mb-3">Menu</h2>
		<nav class="space-y-2 text-sm">
			<a class="block link" href="<?php echo base_url('/user/dashboard'); ?>">Overview</a>
			<a class="block link" href="<?php echo base_url('/user/orders'); ?>">My Orders</a>
			<a class="block link" href="<?php echo base_url('/user/travel-bookings'); ?>">Travel Bookings</a>
			<a class="block link" href="<?php echo base_url('/user/account'); ?>">Account</a>
			<a class="block link" href="<?php echo base_url('/logout'); ?>">Logout</a>
		</nav>
	</aside>
	<section class="md:col-span-2">
		<h1 class="text-2xl font-semibold mb-6">User Dashboard</h1>
		<div class="card p-6">
			<p class="text-gray-300">Welcome to your dashboard. Browse and manage your tickets here.</p>
			
			<?php if (!empty($pendingBooking)): ?>
			<div class="mt-4 p-4 bg-blue-900/20 border border-blue-600/30 rounded-lg">
				<div class="flex items-center gap-3 mb-4">
					<div class="text-2xl">💳</div>
					<div>
						<div class="font-semibold text-blue-300">M-Pesa Payment Pending</div>
						<div class="text-sm text-blue-400">
							Complete your payment on your phone by entering your PIN.
						</div>
					</div>
				</div>
				
				<div class="mt-4">
					<a href="<?php echo base_url('/travel/checkout?booking_id=' . $pendingBooking['id']); ?>" class="w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors text-center block">
						Go to Payment Page
					</a>
					
					<div class="mt-3 p-3 bg-blue-900/20 border border-blue-600/30 rounded-lg">
						<div class="text-sm text-blue-300">
							💡 <strong>Tip:</strong> Click above to go to the payment page where you can confirm your payment after entering your PIN.
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<div class="mt-4 flex gap-3 flex-wrap">
				<a href="<?php echo base_url('/'); ?>" class="btn btn-secondary">Browse Events</a>
				<a href="<?php echo base_url('/travel'); ?>" class="btn btn-secondary">Browse Travel</a>
				<a href="<?php echo base_url('/user/orders'); ?>" class="btn btn-primary">My Orders</a>
			</div>
		</div>
	</section>
</div>



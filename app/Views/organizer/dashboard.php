<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Organizer Dashboard</h1>
	<?php $followers = 0; try{ $r = db()->prepare('SELECT COUNT(*) AS c FROM organizer_followers WHERE organizer_id = ?'); $r->execute([$_SESSION['organizer_id']]); $followers = (int)($r->fetch()['c'] ?? 0);} catch (\Throwable $e) {} ?>
	<div class="grid md:grid-cols-4 gap-4 mb-6">
		<div class="card p-4">
			<div class="text-sm text-gray-400">Events</div>
			<div class="text-2xl font-bold mt-1"><?php echo (int)($eventsCount ?? 0); ?></div>
		</div>
		<div class="card p-4">
			<div class="text-sm text-gray-400">Orders</div>
			<div class="text-2xl font-bold mt-1"><?php echo (int)($ordersCount ?? 0); ?></div>
		</div>
		<div class="card p-4">
			<div class="text-sm text-gray-400">Revenue</div>
			<div class="text-2xl font-bold mt-1">KES <?php echo number_format((float)($revenue ?? 0), 2); ?></div>
		</div>
		<div class="card p-4">
			<div class="text-sm text-gray-400">Followers</div>
			<div class="text-2xl font-bold mt-1"><?php echo $followers; ?></div>
		</div>
	</div>
	<div class="grid md:grid-cols-3 gap-6">
		<div class="md:col-span-2 card p-4">
			<div class="flex items-center justify-between mb-3">
				<h2 class="font-semibold">Recent Orders</h2>
				<a href="<?php echo base_url('/organizer/reports'); ?>" class="link">View reports</a>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm table">
					<thead>
						<tr>
							<th class="px-3 py-2 text-left">Order</th>
							<th class="px-3 py-2 text-left">Event</th>
							<th class="px-3 py-2 text-left">Amount</th>
							<th class="px-3 py-2 text-left">Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach (($recentOrders ?? []) as $ro): ?>
						<tr>
							<td class="px-3 py-2">#<?php echo (int)$ro['id']; ?></td>
							<td class="px-3 py-2"><?php echo htmlspecialchars($ro['title']); ?></td>
							<td class="px-3 py-2">KES <?php echo number_format((float)$ro['total_amount'], 2); ?></td>
							<td class="px-3 py-2 text-gray-400"><?php echo htmlspecialchars($ro['created_at']); ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="card p-4">
			<h2 class="font-semibold mb-3">Quick Actions</h2>
			<div class="grid gap-2">
				<a href="<?php echo base_url('/organizer/events/create'); ?>" class="btn btn-primary">Create Event</a>
				<a href="<?php echo base_url('/organizer/events'); ?>" class="btn btn-secondary">Manage Events</a>
				<a href="<?php echo base_url('/organizer/withdrawals'); ?>" class="btn btn-secondary">Request Withdrawal</a>
			</div>
		</div>
	</div>
</div>



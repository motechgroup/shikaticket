<?php /** @var array $orders */ /** @var array $byCurrency */ /** @var array $pendingWithdrawals */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Admin Dashboard</h1>
	<div class="grid sm:grid-cols-4 gap-6 mb-6">
		<div class="card p-6">
			<div class="text-sm text-gray-400">Paid Orders</div>
			<div class="text-2xl font-semibold"><?php echo (int)($orders['total_orders'] ?? 0); ?></div>
		</div>
		<div class="card p-6">
			<div class="text-sm text-gray-400">Gross Revenue</div>
			<div class="text-2xl font-semibold">KES <?php echo number_format((float)($orders['gross'] ?? 0), 2); ?></div>
		</div>
		<div class="card p-6">
			<div class="text-sm text-gray-400">Currencies</div>
			<ul class="text-sm">
				<?php foreach ($byCurrency as $row): ?>
				<li><?php echo htmlspecialchars($row['currency']); ?>: <?php echo number_format((float)$row['gross'], 2); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="card p-6">
			<div class="text-sm text-gray-400">Platform Commission</div>
			<div class="text-2xl font-semibold">KES <?php echo number_format((float)($commissionRevenue ?? 0), 2); ?></div>
		</div>
	</div>

	<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
		<a href="<?php echo base_url('/admin/users'); ?>" class="card p-6 hover:border-red-600">Users</a>
		<a href="<?php echo base_url('/admin/organizers'); ?>" class="card p-6 hover:border-red-600">Organizers</a>
		<a href="<?php echo base_url('/admin/events'); ?>" class="card p-6 hover:border-red-600">Events</a>
		<a href="<?php echo base_url('/admin/banners'); ?>" class="card p-6 hover:border-red-600">Banners</a>
		<a href="<?php echo base_url('/admin/travel/agencies'); ?>" class="card p-6 hover:border-red-600">Travel Agencies</a>
		<a href="<?php echo base_url('/admin/travel/destinations'); ?>" class="card p-6 hover:border-red-600">Travel Destinations</a>
	</div>

	<div class="card p-4">
		<h2 class="font-semibold mb-3">Pending Withdrawals</h2>
		<?php if (empty($pendingWithdrawals)): ?>
			<div class="p-6 text-gray-400">No pending requests.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Organizer</th>
						<th class="p-3 text-left">Amount</th>
						<th class="p-3 text-left">Date</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($pendingWithdrawals as $w): ?>
					<tr>
						<td class="p-3"><?php echo htmlspecialchars($w['full_name']); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($w['currency']); ?> <?php echo number_format((float)$w['amount'], 2); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($w['created_at']); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<div class="card p-4 mt-6">
		<h2 class="font-semibold mb-3">Recent SMS Logs</h2>
		<?php
		$logs = [];
		try { $logs = db()->query('SELECT * FROM sms_logs ORDER BY id DESC LIMIT 10')->fetchAll(); }
		catch (\PDOException $e) { echo '<div class="text-gray-400">SMS logs table not found. Run migrations to enable logging.</div>'; }
		?>
		<?php if (!empty($logs)): ?>
		<table class="min-w-full text-sm table">
			<thead><tr><th class="p-3 text-left">When</th><th class="p-3 text-left">Provider</th><th class="p-3 text-left">To</th><th class="p-3 text-left">Status</th></tr></thead>
			<tbody>
				<?php foreach ($logs as $l): ?>
				<tr>
					<td class="p-3"><?php echo htmlspecialchars($l['created_at']); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($l['provider']); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($l['recipient']); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($l['status']); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
</div>



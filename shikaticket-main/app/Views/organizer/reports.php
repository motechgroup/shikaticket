<?php /** @var array $summary */ /** @var array $perEvent */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Revenue & Reports</h1>
	<div class="grid sm:grid-cols-3 gap-4 mb-6">
		<div class="card p-4">
			<div class="text-sm text-gray-400">Total Orders</div>
			<div class="text-2xl font-semibold"><?php echo (int)($summary['orders'] ?? 0); ?></div>
		</div>
		<div class="card p-4">
			<div class="text-sm text-gray-400">Total Revenue</div>
			<div class="text-2xl font-semibold">KES <?php echo number_format((float)($summary['revenue'] ?? 0), 2); ?></div>
		</div>
		<div class="card p-4">
			<div class="text-sm text-gray-400">Currency</div>
			<div class="text-2xl font-semibold">KES</div>
		</div>
	</div>
	<div class="card p-4">
		<h2 class="font-semibold mb-3">Per Event</h2>
		<table class="min-w-full text-sm table">
			<thead>
				<tr>
					<th class="p-3 text-left">Event</th>
					<th class="p-3 text-left">Orders</th>
					<th class="p-3 text-left">Revenue</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($perEvent as $row): ?>
				<tr>
					<td class="p-3"><?php echo htmlspecialchars($row['title']); ?></td>
					<td class="p-3"><?php echo (int)($row['orders'] ?? 0); ?></td>
					<td class="p-3">KES <?php echo number_format((float)($row['revenue'] ?? 0), 2); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>



<?php /** @var array $scans */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Scan Activity</h1>
	<div class="card p-0">
		<?php if (empty($scans)): ?>
			<div class="p-6 text-gray-400">No scans yet.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Ticket Code</th>
						<th class="p-3 text-left">Event</th>
						<th class="p-3 text-left">Ticket Type</th>
						<th class="p-3 text-left">Scanner Device</th>
						<th class="p-3 text-left">Organizer</th>
						<th class="p-3 text-left">Redeemed At</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($scans as $s): ?>
					<tr>
						<td class="p-3"><?php echo htmlspecialchars($s['code']); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($s['event_title']); ?></td>
						<td class="p-3">
							<span class="badge"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $s['tier'] ?? 'regular'))); ?></span>
						</td>
						<td class="p-3">
							<?php if (!empty($s['device_name'])): ?>
								<div class="font-medium"><?php echo htmlspecialchars($s['device_name']); ?></div>
								<div class="text-xs text-gray-400 font-mono uppercase"><?php echo htmlspecialchars(strtoupper($s['device_code'])); ?></div>
							<?php else: ?>
								<span class="text-gray-400">Manual</span>
							<?php endif; ?>
						</td>
						<td class="p-3"><?php echo htmlspecialchars($s['organizer'] ?? ''); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($s['redeemed_at']); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>



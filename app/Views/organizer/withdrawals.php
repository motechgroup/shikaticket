<?php /** @var array $withdrawals */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-2">Withdrawal Requests</h1>
	<p class="text-sm text-gray-400 mb-6">Overall available: <span class="font-semibold">KES <?php echo number_format((float)($overallAvailable ?? 0), 2); ?></span></p>
	<form method="post" action="<?php echo base_url('/organizer/withdrawals'); ?>" class="card p-6 space-y-4 mb-6">
		<?php echo csrf_field(); ?>
		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">From</label>
				<select name="event_id" class="select">
					<option value="">All Events (available: KES <?php echo number_format((float)($overallAvailable ?? 0), 2); ?>)</option>
					<?php foreach (($events ?? []) as $ev): $eid=(int)$ev['id']; $avail=$balances[$eid] ?? 0; ?>
					<option value="<?php echo $eid; ?>">Event: <?php echo htmlspecialchars($ev['title']); ?> (KES <?php echo number_format($avail,2); ?>)</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label class="block text-sm mb-1">Amount (KES)</label>
				<input name="amount" type="number" step="0.01" class="input" required>
			</div>
			<div>
				<label class="block text-sm mb-1">Notes (optional)</label>
				<input name="notes" class="input" placeholder="Mobile money/bank details">
			</div>
		</div>
		<button class="btn btn-primary">Request Withdrawal</button>
	</form>
	<div class="card p-4">
		<table class="min-w-full text-sm table">
			<thead>
				<tr>
					<th class="p-3 text-left">Date</th>
					<th class="p-3 text-left">Event</th>
					<th class="p-3 text-left">Amount</th>
					<th class="p-3 text-left">Status</th>
					<th class="p-3 text-left">Notes</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($withdrawals as $w): ?>
				<tr>
					<td class="p-3"><?php echo htmlspecialchars($w['created_at']); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($w['event_title'] ?? 'All Events'); ?></td>
					<td class="p-3">KES <?php echo number_format((float)$w['amount'], 2); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($w['status']); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($w['notes'] ?? ''); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>



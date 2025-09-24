<?php /** @var array $withdrawals */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Withdrawal Requests</h1>
	<form method="post" action="<?php echo base_url('/organizer/withdrawals'); ?>" class="card p-6 space-y-4 mb-6">
		<?php echo csrf_field(); ?>
		<div class="grid sm:grid-cols-3 gap-4">
			<div class="sm:col-span-2">
				<label class="block text-sm mb-1">Amount (KES)</label>
				<input name="amount" type="number" step="0.01" class="input" required>
			</div>
			<div>
				<label class="block text-sm mb-1">Notes (optional)</label>
				<input name="notes" class="input" placeholder="M-Pesa/BanK details as needed">
			</div>
		</div>
		<button class="btn btn-primary">Request Withdrawal</button>
	</form>
	<div class="card p-4">
		<table class="min-w-full text-sm table">
			<thead>
				<tr>
					<th class="p-3 text-left">Date</th>
					<th class="p-3 text-left">Amount</th>
					<th class="p-3 text-left">Status</th>
					<th class="p-3 text-left">Notes</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($withdrawals as $w): ?>
				<tr>
					<td class="p-3"><?php echo htmlspecialchars($w['created_at']); ?></td>
					<td class="p-3">KES <?php echo number_format((float)$w['amount'], 2); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($w['status']); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($w['notes'] ?? ''); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>



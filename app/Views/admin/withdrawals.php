<div class="max-w-6xl mx-auto px-4 py-6">
	<h1 class="text-2xl font-semibold mb-4">Withdrawals</h1>
	<form method="get" class="mb-3 flex gap-2">
		<input class="input max-w-sm" type="text" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Search by status, organizer, or agency">
		<button class="btn btn-secondary">Search</button>
	</form>
	<div class="card overflow-x-auto">
		<table class="min-w-full table">
			<thead>
				<tr>
					<th class="px-4 py-2 text-left">Requestor</th>
					<th class="px-4 py-2 text-left">Type</th>
					<th class="px-4 py-2 text-left">Amount</th>
					<th class="px-4 py-2 text-left">Status</th>
					<th class="px-4 py-2 text-left">Requested</th>
					<th class="px-4 py-2 text-left">Processed</th>
					<th class="px-4 py-2">Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach (($withdrawals ?? []) as $w): ?>
				<tr>
					<td class="px-4 py-2 text-sm">
						<?php if ($w['organizer_name']): ?>
							<div class="font-medium"><?php echo htmlspecialchars($w['organizer_name']); ?></div>
							<div class="text-xs text-gray-400">Event Organizer</div>
						<?php elseif ($w['agency_name']): ?>
							<div class="font-medium"><?php echo htmlspecialchars($w['agency_name']); ?></div>
							<div class="text-xs text-gray-400">Travel Agency</div>
						<?php else: ?>
							<span class="text-gray-400">Unknown</span>
						<?php endif; ?>
					</td>
					<td class="px-4 py-2 text-sm">
						<?php if ($w['event_title']): ?>
							<span class="badge bg-blue-600">Event</span>
							<div class="text-xs text-gray-400"><?php echo htmlspecialchars($w['event_title']); ?></div>
						<?php elseif ($w['destination_title']): ?>
							<span class="badge bg-green-600">Travel</span>
							<div class="text-xs text-gray-400"><?php echo htmlspecialchars($w['destination_title']); ?></div>
						<?php else: ?>
							<span class="badge bg-gray-600">General</span>
						<?php endif; ?>
					</td>
					<td class="px-4 py-2 text-sm font-medium">KES <?php echo number_format((float)($w['amount'] ?? 0), 2); ?></td>
					<td class="px-4 py-2 text-sm">
						<span class="badge <?php echo ($w['status']==='paid'?'bg-green-700 border-green-800':''); ?>"><?php echo htmlspecialchars($w['status'] ?? ''); ?></span>
					</td>
					<td class="px-4 py-2 text-xs text-gray-400"><?php echo htmlspecialchars($w['created_at'] ?? ''); ?></td>
					<td class="px-4 py-2 text-xs text-gray-400"><?php echo htmlspecialchars($w['updated_at'] ?? ''); ?></td>
					<td class="px-4 py-2">
						<form method="post" action="<?php echo base_url('/admin/withdrawals/update'); ?>" class="inline">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="id" value="<?php echo (int)$w['id']; ?>">
							<select name="status" class="select">
								<option value="approved" <?php echo ($w['status']==='approved'?'selected':''); ?>>Approve</option>
								<option value="paid" <?php echo ($w['status']==='paid'?'selected':''); ?>>Mark Paid</option>
								<option value="rejected" <?php echo ($w['status']==='rejected'?'selected':''); ?>>Reject</option>
							</select>
							<button class="btn btn-primary ml-2">Update</button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="max-w-6xl mx-auto px-4 py-6 md:py-10">
	<!-- Mobile-friendly header -->
	<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4 md:mb-6">
		<h1 class="text-xl md:text-2xl font-semibold">Withdrawals Management</h1>
		<a href="<?php echo base_url('/admin'); ?>" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
			← Back to Dashboard
		</a>
	</div>
	
	<!-- Search form -->
	<form method="get" class="mb-4 md:mb-6">
		<div class="flex flex-col sm:flex-row gap-2">
			<input class="input flex-1" type="text" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Search by status, organizer, or agency">
			<button class="btn btn-secondary touch-target">Search</button>
		</div>
	</form>

	<?php if (empty($withdrawals)): ?>
		<div class="card p-8 text-center">
			<div class="text-gray-400 mb-4">
				<svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
				</svg>
				<h3 class="text-lg font-medium mb-2">No Withdrawals Found</h3>
				<p class="text-gray-400"><?php echo !empty($q) ? 'No withdrawals match your search criteria.' : 'No withdrawal requests have been submitted yet.'; ?></p>
			</div>
		</div>
	<?php else: ?>
		<!-- Mobile-friendly withdrawals list -->
		<div class="space-y-4">
			<?php foreach ($withdrawals as $w): ?>
				<div class="card p-4 md:p-6">
					<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
						<!-- Main Info -->
						<div class="flex-1 min-w-0">
							<div class="flex items-start justify-between mb-3">
								<div class="min-w-0 flex-1">
									<h3 class="text-lg font-semibold text-white truncate">
										<?php echo htmlspecialchars($w['organizer_name'] ?? $w['agency_name'] ?? 'Unknown'); ?>
									</h3>
									<p class="text-sm text-gray-400 mt-1">
										<?php echo $w['organizer_name'] ? 'Event Organizer' : 'Travel Agency'; ?>
									</p>
								</div>
								<div class="ml-4 flex-shrink-0">
									<?php
									$statusColors = [
										'requested' => 'bg-yellow-600 text-yellow-200',
										'approved' => 'bg-blue-600 text-blue-200',
										'paid' => 'bg-green-600 text-green-200',
										'rejected' => 'bg-red-600 text-red-200'
									];
									$statusColor = $statusColors[$w['status']] ?? 'bg-gray-600 text-gray-200';
									?>
									<span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
										<?php echo ucfirst($w['status'] ?? 'Unknown'); ?>
									</span>
								</div>
							</div>

							<!-- Details -->
							<div class="flex flex-wrap items-center gap-4 text-sm text-gray-400 mb-3">
								<div class="flex items-center gap-1">
									<?php if ($w['event_title']): ?>
										<span class="px-2 py-1 bg-blue-600/20 text-blue-300 text-xs rounded-full">Event</span>
										<span class="text-xs"><?php echo htmlspecialchars($w['event_title']); ?></span>
									<?php elseif ($w['destination_title']): ?>
										<span class="px-2 py-1 bg-green-600/20 text-green-300 text-xs rounded-full">Travel</span>
										<span class="text-xs"><?php echo htmlspecialchars($w['destination_title']); ?></span>
									<?php else: ?>
										<span class="px-2 py-1 bg-gray-600/20 text-gray-300 text-xs rounded-full">General</span>
									<?php endif; ?>
								</div>
								<div class="flex items-center gap-1">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
									</svg>
									<span class="font-medium text-white">KES <?php echo number_format((float)($w['amount'] ?? 0), 2); ?></span>
								</div>
							</div>

							<!-- Dates -->
							<div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
								<div>
									<span class="font-medium">Requested:</span> 
									<?php echo date('M j, Y H:i', strtotime($w['created_at'] ?? '')); ?>
								</div>
								<?php if ($w['updated_at'] && $w['updated_at'] !== $w['created_at']): ?>
									<div>
										<span class="font-medium">Updated:</span> 
										<?php echo date('M j, Y H:i', strtotime($w['updated_at'])); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<!-- Actions -->
						<div class="flex flex-col sm:flex-row gap-2 lg:flex-col lg:min-w-0">
							<form method="post" action="<?php echo base_url('/admin/withdrawals/update'); ?>" class="inline">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="id" value="<?php echo (int)$w['id']; ?>">
								<select name="status" class="select mb-2 lg:mb-0">
									<option value="approved" <?php echo ($w['status']==='approved'?'selected':''); ?>>Approve</option>
									<option value="paid" <?php echo ($w['status']==='paid'?'selected':''); ?>>Mark Paid</option>
									<option value="rejected" <?php echo ($w['status']==='rejected'?'selected':''); ?>>Reject</option>
								</select>
								<button class="btn btn-primary touch-target w-full sm:w-auto lg:w-full" 
										onclick="return confirm('Update withdrawal status to \'' + this.form.status.value + '\'?')">
									Update Status
								</button>
							</form>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<?php /** @var array $users */ ?>
<div class="max-w-6xl mx-auto px-4 py-6 md:py-10">
	<!-- Mobile Back Navigation -->
	<div class="md:hidden mb-4">
		<div class="flex items-center gap-3">
			<button 
				class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-gray-600 bg-gray-800/60 text-white cursor-pointer hover:bg-gray-700/60 transition-colors touch-target"
				onclick="history.back()"
				aria-label="Go back"
				title="Go back">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
				</svg>
			</button>
			<div class="flex-1">
				<span class="text-white text-sm font-medium">Users</span>
			</div>
			<button 
				class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-red-500 bg-red-900/60 text-white cursor-pointer hover:bg-red-800/60 transition-colors touch-target"
				onclick="toggleMobileMenu()"
				aria-label="Open admin menu"
				title="Open admin menu">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
			</button>
		</div>
	</div>
	
	<h1 class="text-xl md:text-2xl font-semibold mb-4 md:mb-6">Users</h1>
    <div class="card p-0">
		<?php if (empty($users)): ?>
			<div class="p-4 md:p-6 text-gray-400 text-sm">No users yet.</div>
		<?php else: ?>
			<div class="table-responsive">
				<table class="min-w-full text-xs md:text-sm table">
					<thead>
						<tr>
							<th class="p-2 md:p-3 text-left">Email</th>
							<th class="p-2 md:p-3 text-left hidden sm:table-cell">Phone</th>
							<th class="p-2 md:p-3 text-left">Status</th>
							<th class="p-2 md:p-3 text-left hidden md:table-cell">Joined</th>
							<th class="p-2 md:p-3 text-left">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $u): ?>
						<tr>
							<td class="p-2 md:p-3">
								<div class="font-medium text-xs md:text-sm"><?php echo htmlspecialchars($u['email'] ?? ''); ?></div>
								<div class="sm:hidden text-xs text-gray-400 mt-1">
									<?php echo htmlspecialchars($u['phone'] ?? ''); ?>
								</div>
								<div class="md:hidden text-xs text-gray-400 mt-1">
									<?php echo htmlspecialchars($u['created_at'] ?? ''); ?>
								</div>
							</td>
							<td class="p-2 md:p-3 hidden sm:table-cell"><?php echo htmlspecialchars($u['phone'] ?? ''); ?></td>
							<td class="p-2 md:p-3">
								<?php if ((int)($u['is_active'] ?? 1) === 1): ?>
									<span class="px-2 py-1 text-xs rounded-full bg-green-900 text-green-300">Active</span>
								<?php else: ?>
									<span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">Inactive</span>
								<?php endif; ?>
							</td>
							<td class="p-2 md:p-3 hidden md:table-cell"><?php echo htmlspecialchars($u['created_at'] ?? ''); ?></td>
							<td class="p-2 md:p-3">
								<div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
									<form method="post" action="<?php echo base_url('/admin/users/toggle'); ?>">
										<?php echo csrf_field(); ?>
										<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
										<input type="hidden" name="is_active" value="<?php echo ((int)($u['is_active'] ?? 1) === 1) ? 0 : 1; ?>">
										<button class="btn btn-secondary btn-sm touch-target w-full sm:w-auto">
											<?php echo ((int)($u['is_active'] ?? 1) === 1) ? 'Deactivate' : 'Activate'; ?>
										</button>
									</form>
									<form method="post" action="<?php echo base_url('/admin/users/delete'); ?>" onsubmit="return confirm('Delete user?');">
										<?php echo csrf_field(); ?>
										<input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
										<button class="btn btn-primary btn-sm touch-target w-full sm:w-auto">Delete</button>
									</form>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>



<?php /** @var array $organizers */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">Organizers</h1>
		<a class="btn btn-primary" href="<?php echo base_url('/admin/organizers/create'); ?>">Create Organizer</a>
	</div>
	<div class="card p-0">
		<?php if (empty($organizers)): ?>
			<div class="p-6 text-gray-400">No organizers yet.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Name</th>
						<th class="p-3 text-left">Email</th>
						<th class="p-3 text-left">Phone</th>
						<th class="p-3 text-left">Commission %</th>
						<th class="p-3 text-left">Approved</th>
						<th class="p-3 text-left"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($organizers as $org): ?>
					<tr>
					<td class="p-3"><a class="link" href="<?php echo base_url('/admin/organizers/edit?id='.(int)$org['id']); ?>"><?php echo htmlspecialchars($org['full_name']); ?></a></td>
						<td class="p-3"><?php echo htmlspecialchars($org['email']); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($org['phone']); ?></td>
						<td class="p-3">
							<form method="post" action="<?php echo base_url('/admin/organizers/commission'); ?>" class="flex items-center gap-2">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="id" value="<?php echo (int)$org['id']; ?>">
								<input name="commission_percent" value="<?php echo htmlspecialchars($org['commission_percent'] ?? '0.00'); ?>" class="input" style="width:90px" />
								<button class="btn btn-secondary">Save</button>
							</form>
						</td>
						<td class="p-3"><?php echo (int)$org['is_approved'] === 1 ? 'Yes' : 'No'; ?></td>
                    <td class="p-3 text-right flex items-center gap-2 justify-end">
                        <a class="btn btn-secondary" href="<?php echo base_url('/admin/organizers/show?id='.(int)$org['id']); ?>">View</a>
                        <a class="btn btn-secondary" href="<?php echo base_url('/admin/organizers/edit?id='.(int)$org['id']); ?>">Edit</a>
							<?php if ((int)$org['is_approved'] !== 1): ?>
							<form method="post" action="<?php echo base_url('/admin/organizers/approve'); ?>">
									<?php echo csrf_field(); ?>
									<input type="hidden" name="id" value="<?php echo (int)$org['id']; ?>">
									<button class="btn btn-primary">Approve</button>
								</form>
							<?php endif; ?>
                        <button onclick="confirmDeleteOrganizer(<?php echo (int)$org['id']; ?>, '<?php echo htmlspecialchars($org['full_name']); ?>')" class="btn btn-danger">Delete</button>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Organizer</h3>
                <p class="text-sm text-gray-600">This action cannot be undone</p>
            </div>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-700">Are you sure you want to delete organizer <strong id="organizerName"></strong>?</p>
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <p class="text-sm text-red-800 font-medium">Warning:</p>
                        <ul class="text-sm text-red-700 mt-1 space-y-1">
                            <li>• All events created by this organizer will be deleted</li>
                            <li>• All tickets and orders will be permanently removed</li>
                            <li>• All associated payments will be lost</li>
                            <li>• This action cannot be reversed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="closeDeleteModal()" class="btn btn-secondary flex-1">Cancel</button>
            <form id="deleteForm" method="post" action="<?php echo base_url('/admin/organizers/delete'); ?>" class="flex-1">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="deleteOrganizerId" name="id" value="">
                <button type="submit" class="btn btn-danger w-full">Delete Organizer</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDeleteOrganizer(organizerId, organizerName) {
    document.getElementById('organizerName').textContent = organizerName;
    document.getElementById('deleteOrganizerId').value = organizerId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>



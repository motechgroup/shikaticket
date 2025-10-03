<?php /** @var array $devices */ /** @var array $scanReports */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Scanner Devices & Reports</h1>
	
	<!-- Create New Device -->
	<div class="card p-6 mb-6">
		<h2 class="text-lg font-semibold mb-4">Create New Scanner Device</h2>
		<form method="post" action="<?php echo base_url('/organizer/scanner-devices'); ?>" class="flex gap-4">
			<?php echo csrf_field(); ?>
			<input type="text" name="device_name" placeholder="Device name (e.g., Main Entrance Scanner)" class="input flex-1" required>
			<button type="submit" class="btn btn-primary">Create Device</button>
		</form>
	</div>

	<!-- Mobile Scanner Apps CTA -->
	<?php 
	$androidUrl = \App\Models\Setting::get('apps.android_scanner_url', '');
	$iosUrl = \App\Models\Setting::get('apps.ios_scanner_url', '');
	$appstoreImg = base_url('/uploads/assets/appstore.png');
	$playstoreImg = base_url('/uploads/assets/playstore.png');
	?>
	<div class="card p-4 mb-6">
		<div class="flex items-center justify-between flex-wrap gap-3">
			<div>
				<h3 class="text-white font-semibold mb-1">Get the Mobile Scanner App</h3>
				<p class="text-sm text-gray-400">Install on your devices for fast check-ins.</p>
			</div>
			<div class="flex items-center gap-3">
				<?php if ($iosUrl): ?>
					<a href="<?php echo htmlspecialchars($iosUrl); ?>" target="_blank" rel="noopener"><img src="<?php echo $appstoreImg; ?>" class="h-10" alt="App Store"></a>
				<?php else: ?>
					<span title="Coming soon" class="opacity-60 cursor-not-allowed"><img src="<?php echo $appstoreImg; ?>" class="h-10" alt="App Store (coming soon)"></span>
				<?php endif; ?>
				<?php if ($androidUrl): ?>
					<a href="<?php echo htmlspecialchars($androidUrl); ?>" target="_blank" rel="noopener"><img src="<?php echo $playstoreImg; ?>" class="h-10" alt="Google Play"></a>
				<?php else: ?>
					<span title="Coming soon" class="opacity-60 cursor-not-allowed"><img src="<?php echo $playstoreImg; ?>" class="h-10" alt="Google Play (coming soon)"></span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Device List -->
	<div class="card p-0">
		<?php if (empty($devices)): ?>
			<div class="p-6 text-gray-400">No scanner devices created yet.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Device Name</th>
						<th class="p-3 text-left">Device Code</th>
						<th class="p-3 text-left">Status</th>
						<th class="p-3 text-left">Created</th>
						<th class="p-3 text-left">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($devices as $device): ?>
					<tr>
						<td class="p-3 font-medium"><?php echo htmlspecialchars($device['device_name']); ?></td>
						<td class="p-3">
							<span class="font-mono text-sm bg-gray-800 px-2 py-1 rounded uppercase"><?php echo htmlspecialchars(strtoupper($device['device_code'])); ?></span>
						</td>
						<td class="p-3">
							<?php if ($device['is_active']): ?>
								<span class="badge bg-green-900 text-green-300 border-green-700">Active</span>
							<?php else: ?>
								<span class="badge bg-gray-700 text-gray-300 border-gray-600">Inactive</span>
							<?php endif; ?>
						</td>
						<td class="p-3 text-gray-400"><?php echo date('M j, Y', strtotime($device['created_at'])); ?></td>
						<td class="p-3">
							<button onclick="editDevice(<?php echo htmlspecialchars(json_encode($device)); ?>)" class="btn btn-secondary btn-sm">Edit</button>
							<button onclick="deleteDevice(<?php echo $device['id']; ?>)" class="btn btn-secondary btn-sm ml-2">Delete</button>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<!-- Scan Reports -->
	<?php if (!empty($scanReports)): ?>
	<div class="card p-0 mt-6">
		<h2 class="text-lg font-semibold p-6 pb-0">Recent Scan Activity</h2>
		<div class="p-6">
			<div class="mb-4 flex items-center justify-between">
				<p class="text-sm text-gray-400">Showing last 100 scans from your devices</p>
				<div class="flex items-center gap-2">
					<span class="text-sm text-gray-400">Total Scans:</span>
					<span class="badge bg-blue-900 text-blue-300 border-blue-700"><?php echo count($scanReports); ?></span>
				</div>
			</div>
			
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm table">
					<thead>
						<tr>
							<th class="p-3 text-left">Ticket Code</th>
							<th class="p-3 text-left">Event</th>
							<th class="p-3 text-left">Ticket Type</th>
							<th class="p-3 text-left">Scanner Device</th>
							<th class="p-3 text-left">Scanned At</th>
							<th class="p-3 text-left">Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($scanReports as $scan): ?>
						<tr>
							<td class="p-3 font-mono text-sm">
								<span class="bg-gray-800 px-2 py-1 rounded"><?php echo htmlspecialchars($scan['ticket_code']); ?></span>
							</td>
							<td class="p-3">
								<div class="font-medium"><?php echo htmlspecialchars($scan['event_title']); ?></div>
								<div class="text-xs text-gray-400"><?php echo date('M j, Y', strtotime($scan['event_date'])); ?></div>
							</td>
							<td class="p-3">
								<span class="badge bg-green-900 text-green-300 border-green-700">
									<?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $scan['tier'] ?? 'regular'))); ?>
								</span>
							</td>
							<td class="p-3">
								<div class="font-medium"><?php echo htmlspecialchars($scan['device_name']); ?></div>
								<div class="text-xs text-gray-400 font-mono uppercase"><?php echo htmlspecialchars($scan['device_code']); ?></div>
							</td>
							<td class="p-3 text-gray-400">
								<?php echo date('M j, Y g:i A', strtotime($scan['redeemed_at'])); ?>
							</td>
							<td class="p-3">
								<span class="font-medium">KSh <?php echo number_format((float)$scan['unit_price'], 2); ?></span>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php elseif (!empty($devices)): ?>
	<div class="card p-6 mt-6">
		<div class="text-center">
			<div class="text-gray-400 mb-4">
				<svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
			</div>
			<h3 class="text-lg font-semibold mb-2">No Scan Activity Yet</h3>
			<p class="text-gray-400">Your scanner devices haven't been used to scan any tickets yet.</p>
		</div>
	</div>
	<?php endif; ?>

	<!-- Instructions -->
	<div class="card p-6 mt-6">
		<h3 class="text-lg font-semibold mb-3">How to Use Scanner Devices</h3>
		<div class="text-sm text-gray-300 space-y-2">
			<p>1. Create scanner devices for each physical scanner/tablet you'll use at events</p>
			<p>2. Each device gets a unique code (like <code class="bg-gray-800 px-1 rounded">ABC12345</code>)</p>
			<p>3. Assign your devices to your events in "Event Scanner Assignments"</p>
			<p>4. Staff scan tickets using the device code instead of organizer login</p>
			<p>5. View scan reports above to track which device scanned each ticket</p>
		</div>
	</div>
</div>

<!-- Edit Device Modal -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
	<div class="bg-[#0f0f10] border border-gray-800 rounded-xl p-6 w-[90%] max-w-md">
		<h3 class="text-lg font-semibold mb-4">Edit Scanner Device</h3>
		<form id="editForm" method="post" action="<?php echo base_url('/organizer/scanner-devices/update'); ?>">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="device_id" id="edit_device_id">
			<div class="mb-4">
				<label class="block text-sm mb-1">Device Name</label>
				<input type="text" name="device_name" id="edit_device_name" class="input" required>
			</div>
			<div class="mb-4">
				<label class="flex items-center gap-2">
					<input type="checkbox" name="is_active" id="edit_is_active" class="rounded">
					<span class="text-sm">Active</span>
				</label>
			</div>
			<div class="flex gap-3">
				<button type="submit" class="btn btn-primary">Update Device</button>
				<button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
			</div>
		</form>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
	<div class="bg-[#0f0f10] border border-gray-800 rounded-xl p-6 w-[90%] max-w-md text-center">
		<h3 class="text-lg font-semibold mb-4">Delete Scanner Device</h3>
		<p class="text-gray-300 mb-6">Are you sure you want to delete this scanner device? This action cannot be undone.</p>
		<form id="deleteForm" method="post" action="<?php echo base_url('/organizer/scanner-devices/delete'); ?>">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="device_id" id="delete_device_id">
			<div class="flex gap-3">
				<button type="submit" class="btn btn-primary">Delete</button>
				<button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
			</div>
		</form>
	</div>
</div>

<script>
function editDevice(device) {
	document.getElementById('edit_device_id').value = device.id;
	document.getElementById('edit_device_name').value = device.device_name;
	document.getElementById('edit_is_active').checked = device.is_active == 1;
	document.getElementById('editModal').classList.remove('hidden');
	document.getElementById('editModal').classList.add('flex');
}

function closeEditModal() {
	document.getElementById('editModal').classList.add('hidden');
	document.getElementById('editModal').classList.remove('flex');
}

function deleteDevice(deviceId) {
	document.getElementById('delete_device_id').value = deviceId;
	document.getElementById('deleteModal').classList.remove('hidden');
	document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
	document.getElementById('deleteModal').classList.add('hidden');
	document.getElementById('deleteModal').classList.remove('flex');
}

// Close modals when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
	if (e.target === this) closeEditModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
	if (e.target === this) closeDeleteModal();
});
</script>

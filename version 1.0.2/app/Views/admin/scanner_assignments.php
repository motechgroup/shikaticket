<?php /** @var array $organizers */ /** @var array $scannerDevices */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Scanner Device Assignments</h1>
	
	<!-- Assign Scanner to Organizer -->
	<div class="card p-6 mb-6">
		<h2 class="text-lg font-semibold mb-4">Assign Scanner Device to Organizer</h2>
		<form method="post" action="<?php echo base_url('/admin/scanner-assignments/assign'); ?>" class="grid md:grid-cols-3 gap-4">
			<?php echo csrf_field(); ?>
			<div>
				<label class="block text-sm mb-1">Organizer</label>
				<select name="organizer_id" class="select" required>
					<option value="">Select Organizer</option>
					<?php foreach ($organizers as $organizer): ?>
						<option value="<?php echo $organizer['id']; ?>">
							<?php echo htmlspecialchars($organizer['full_name']); ?> (<?php echo htmlspecialchars($organizer['email']); ?>)
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label class="block text-sm mb-1">Scanner Device</label>
				<select name="scanner_device_id" class="select" required>
					<option value="">Select Scanner Device</option>
					<?php foreach ($scannerDevices as $device): ?>
						<option value="<?php echo $device['id']; ?>">
							<?php echo htmlspecialchars($device['device_name']); ?> (<?php echo htmlspecialchars($device['organizer_name']); ?>)
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="flex items-end">
				<button type="submit" class="btn btn-primary w-full">Assign Scanner</button>
			</div>
		</form>
	</div>

	<!-- Current Assignments -->
	<div class="card p-0">
		<h2 class="text-lg font-semibold p-6 pb-0">Current Assignments</h2>
		<?php 
		$assignments = db()->query('
			SELECT osa.*, o.full_name as organizer_name, o.email as organizer_email,
				   sd.device_name, sd.device_code, sd.organizer_id as device_organizer_id,
				   sd_owner.full_name as device_owner_name
			FROM organizer_scanner_assignments osa
			JOIN organizers o ON o.id = osa.organizer_id
			JOIN scanner_devices sd ON sd.id = osa.scanner_device_id
			JOIN organizers sd_owner ON sd_owner.id = sd.organizer_id
			WHERE osa.is_active = 1
			ORDER BY osa.assigned_at DESC
		')->fetchAll();
		?>
		<?php if (empty($assignments)): ?>
			<div class="p-6 text-gray-400">No scanner devices assigned to organizers yet.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Organizer</th>
						<th class="p-3 text-left">Scanner Device</th>
						<th class="p-3 text-left">Device Owner</th>
						<th class="p-3 text-left">Device Code</th>
						<th class="p-3 text-left">Assigned At</th>
						<th class="p-3 text-left">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($assignments as $assignment): ?>
					<tr>
						<td class="p-3">
							<div class="font-medium"><?php echo htmlspecialchars($assignment['organizer_name']); ?></div>
							<div class="text-xs text-gray-400"><?php echo htmlspecialchars($assignment['organizer_email']); ?></div>
						</td>
						<td class="p-3">
							<div class="font-medium"><?php echo htmlspecialchars($assignment['device_name']); ?></div>
						</td>
						<td class="p-3">
							<div class="text-sm"><?php echo htmlspecialchars($assignment['device_owner_name']); ?></div>
						</td>
						<td class="p-3">
							<span class="font-mono text-sm bg-gray-800 px-2 py-1 rounded"><?php echo htmlspecialchars($assignment['device_code']); ?></span>
						</td>
						<td class="p-3 text-gray-400"><?php echo date('M j, Y H:i', strtotime($assignment['assigned_at'])); ?></td>
						<td class="p-3">
							<form method="post" action="<?php echo base_url('/admin/scanner-assignments/unassign'); ?>" class="inline">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
								<button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to unassign this scanner device?')">Unassign</button>
							</form>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<!-- Instructions -->
	<div class="card p-6 mt-6">
		<h3 class="text-lg font-semibold mb-3">Scanner Device Assignment Process</h3>
		<div class="text-sm text-gray-300 space-y-2">
			<p>1. Organizers create scanner devices in their dashboard</p>
			<p>2. Admin assigns scanner devices to organizers using this interface</p>
			<p>3. Organizers assign their assigned devices to their events</p>
			<p>4. Event staff use the device code to log into scanner (not organizer credentials)</p>
			<p>5. Scanner can only validate tickets for events assigned by the organizer</p>
			<p>6. Admin can view detailed scan history with device information</p>
		</div>
	</div>
</div>

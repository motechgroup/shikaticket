<?php /** @var array $events */ /** @var array $assignedDevices */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Event Scanner Assignments</h1>
	
	<!-- Assign Scanner to Event -->
	<div class="card p-6 mb-6">
		<h2 class="text-lg font-semibold mb-4">Assign Scanner Device to Event</h2>
		<form method="post" action="<?php echo base_url('/organizer/event-scanner-assignments/assign'); ?>" class="grid md:grid-cols-3 gap-4">
			<?php echo csrf_field(); ?>
			<div>
				<label class="block text-sm mb-1">Event</label>
				<select name="event_id" class="select" required>
					<option value="">Select Event</option>
					<?php foreach ($events as $event): ?>
						<option value="<?php echo $event['id']; ?>">
							<?php echo htmlspecialchars($event['title']); ?> - <?php echo date('M j, Y', strtotime($event['event_date'])); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label class="block text-sm mb-1">Scanner Device</label>
				<select name="scanner_device_id" class="select" required>
					<option value="">Select Scanner Device</option>
					<?php foreach ($assignedDevices as $device): ?>
						<option value="<?php echo $device['id']; ?>">
							<?php echo htmlspecialchars($device['device_name']); ?> (<?php echo htmlspecialchars($device['device_code']); ?>)
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="flex items-end">
				<button type="submit" class="btn btn-primary w-full">Assign Scanner</button>
			</div>
		</form>
	</div>

	<!-- Your Scanner Devices -->
	<div class="card p-6 mb-6">
		<h2 class="text-lg font-semibold mb-4">Your Scanner Devices</h2>
		<?php if (empty($assignedDevices)): ?>
			<div class="text-gray-400">
				No scanner devices created yet. 
				<a href="<?php echo base_url('/organizer/scanner-devices'); ?>" class="text-blue-400 hover:text-blue-300">Create your first scanner device</a>
			</div>
		<?php else: ?>
			<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
				<?php foreach ($assignedDevices as $device): ?>
					<div class="bg-gray-800 p-4 rounded-lg">
						<div class="font-medium"><?php echo htmlspecialchars($device['device_name']); ?></div>
						<div class="text-sm text-gray-400 font-mono uppercase">Code: <?php echo htmlspecialchars(strtoupper($device['device_code'])); ?></div>
						<div class="text-xs text-gray-500 mt-1">Created: <?php echo date('M j, Y', strtotime($device['created_at'])); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<!-- Current Event Assignments -->
	<div class="card p-0">
		<h2 class="text-lg font-semibold p-6 pb-0">Current Event Assignments</h2>
		<?php 
		$assignments = db()->prepare('
			SELECT esa.*, e.title as event_title, e.event_date,
				   sd.device_name, sd.device_code
			FROM event_scanner_assignments esa
			JOIN events e ON e.id = esa.event_id
			JOIN scanner_devices sd ON sd.id = esa.scanner_device_id
			WHERE esa.organizer_id = ? AND esa.is_active = 1
			ORDER BY e.event_date DESC, esa.assigned_at DESC
		');
		$assignments->execute([$_SESSION['organizer_id']]);
		$assignments = $assignments->fetchAll();
		?>
		<?php if (empty($assignments)): ?>
			<div class="p-6 text-gray-400">No scanner devices assigned to events yet.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Event</th>
						<th class="p-3 text-left">Scanner Device</th>
						<th class="p-3 text-left">Device Code</th>
						<th class="p-3 text-left">Assigned At</th>
						<th class="p-3 text-left">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($assignments as $assignment): ?>
					<tr>
						<td class="p-3">
							<div class="font-medium"><?php echo htmlspecialchars($assignment['event_title']); ?></div>
							<div class="text-xs text-gray-400"><?php echo date('M j, Y', strtotime($assignment['event_date'])); ?></div>
						</td>
						<td class="p-3">
							<div class="font-medium"><?php echo htmlspecialchars($assignment['device_name']); ?></div>
						</td>
						<td class="p-3">
							<span class="font-mono text-sm bg-gray-800 px-2 py-1 rounded uppercase"><?php echo htmlspecialchars(strtoupper($assignment['device_code'])); ?></span>
						</td>
						<td class="p-3 text-gray-400"><?php echo date('M j, Y H:i', strtotime($assignment['assigned_at'])); ?></td>
						<td class="p-3">
							<form method="post" action="<?php echo base_url('/organizer/event-scanner-assignments/unassign'); ?>" class="inline">
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
		<h3 class="text-lg font-semibold mb-3">Event Scanner Assignment Process</h3>
		<div class="text-sm text-gray-300 space-y-2">
			<p>1. Create scanner devices in "Scanner Devices" section</p>
			<p>2. Assign your devices to your events using this interface</p>
			<p>3. Event staff use the device code to log into the scanner</p>
			<p>4. Scanner can only validate tickets for events you've assigned it to</p>
			<p>5. Admin can view detailed scan history with device information</p>
		</div>
	</div>
</div>

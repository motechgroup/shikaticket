<?php /** @var array $events */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">My Events</h1>
		<a href="<?php echo base_url('/organizer/events/create'); ?>" class="px-4 py-2 bg-green-600 text-white rounded">Create Event</a>
	</div>
    <div class="card">
		<?php if (empty($events)): ?>
			<div class="p-6 text-gray-500">No events yet.</div>
		<?php else: ?>
            <table class="min-w-full text-sm table">
				<thead>
					<tr class="bg-gray-50 text-left">
						<th class="p-3">Title</th>
						<th class="p-3">Date</th>
						<th class="p-3">Venue</th>
						<th class="p-3">Price</th>
                        <th class="p-3"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($events as $event): ?>
					<tr class="border-t">
						<td class="p-3"><?php echo htmlspecialchars($event['title']); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($event['event_date'] . ' ' . $event['event_time']); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($event['venue']); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($event['currency']); ?> <?php echo number_format((float)$event['price'], 2); ?></td>
                        <td class="p-3 text-right flex gap-2 justify-end">
                            <a class="btn btn-secondary" href="<?php echo base_url('/organizer/events/report?id=' . (int)$event['id']); ?>">Report</a>
                            <a class="btn btn-secondary" href="<?php echo base_url('/organizer/events/edit?id=' . (int)$event['id']); ?>">Edit</a>
                            <form method="post" action="<?php echo base_url('/organizer/events/delete'); ?>">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="id" value="<?php echo (int)$event['id']; ?>">
                                <button class="btn btn-primary" onclick="return confirm('Delete this event?');">Delete</button>
							</form>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>



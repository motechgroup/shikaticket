<?php /** @var array $events */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Events</h1>
	<div class="card">
		<?php if (empty($events)): ?>
			<div class="p-6 text-gray-400">No events yet.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Poster</th>
						<th class="p-3 text-left">Title</th>
						<th class="p-3 text-left">Organizer</th>
						<th class="p-3 text-left">Date</th>
						<th class="p-3 text-left">Venue</th>
						<th class="p-3 text-left">Featured</th>
						<th class="p-3 text-left">Published</th>
						<th class="p-3 text-left"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($events as $e): ?>
					<tr>
						<td class="p-3">
                            <?php if (!empty($e['poster_path'])): ?>
                                <img src="<?php echo base_url($e['poster_path']); ?>" alt="Poster" class="h-10 w-10 object-cover rounded">
							<?php else: ?>
								<span class="text-gray-500 text-xs">No poster</span>
							<?php endif; ?>
						</td>
						<td class="p-3"><a class="link" href="<?php echo base_url('/admin/events/show?id='.(int)$e['id']); ?>"><?php echo htmlspecialchars($e['title'] ?? ''); ?></a></td>
						<td class="p-3"><?php echo htmlspecialchars($e['organizer_name'] ?? ('#' . ($e['organizer_id'] ?? ''))); ?></td>
						<td class="p-3"><?php echo htmlspecialchars(($e['event_date'] ?? '') . ' ' . ($e['event_time'] ?? '')); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($e['venue'] ?? ''); ?></td>
						<td class="p-3"><?php echo ((int)($e['is_featured'] ?? 0) === 1) ? 'Yes' : 'No'; ?></td>
						<td class="p-3"><?php echo ((int)($e['is_published'] ?? 0) === 1) ? 'Yes' : 'No'; ?></td>
						<td class="p-3">
							<div class="flex gap-2">
								<a class="btn btn-primary" target="_blank" href="<?php echo base_url('/events/show?id='.(int)$e['id']); ?>">Preview</a>
								<a class="btn btn-secondary" href="<?php echo base_url('/admin/events/edit?id='.(int)$e['id']); ?>">Edit</a>
								<form method="post" action="<?php echo base_url('/admin/events/publish'); ?>">
									<?php echo csrf_field(); ?>
									<input type="hidden" name="id" value="<?php echo (int)$e['id']; ?>">
									<input type="hidden" name="published" value="<?php echo ((int)$e['is_published'] === 1) ? 0 : 1; ?>">
									<button class="btn btn-primary"><?php echo ((int)$e['is_published'] === 1) ? 'Unpublish' : 'Publish'; ?></button>
								</form>
								<form method="post" action="<?php echo base_url('/admin/events/feature'); ?>">
									<?php echo csrf_field(); ?>
									<input type="hidden" name="id" value="<?php echo (int)$e['id']; ?>">
									<input type="hidden" name="featured" value="<?php echo ((int)$e['is_featured'] === 1) ? 0 : 1; ?>">
									<button class="btn btn-secondary"><?php echo ((int)$e['is_featured'] === 1) ? 'Unfeature' : 'Feature'; ?></button>
								</form>
								<form method="post" action="<?php echo base_url('/admin/events/delete'); ?>" onsubmit="return confirm('Delete event?');">
									<?php echo csrf_field(); ?>
									<input type="hidden" name="id" value="<?php echo (int)$e['id']; ?>">
									<button class="btn btn-primary">Delete</button>
								</form>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>



<?php /** @var array $events */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
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
				<span class="text-white text-sm font-medium">Events</span>
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
	
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Events</h1>
        <a class="btn btn-primary" href="<?php echo base_url('/admin/events/create'); ?>">Create Event</a>
    </div>
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



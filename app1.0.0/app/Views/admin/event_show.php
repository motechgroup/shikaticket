<?php /** @var array $event */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold"><?php echo htmlspecialchars($event['title']); ?></h1>
		<a class="btn btn-secondary" href="<?php echo base_url('/admin/events'); ?>">Back</a>
	</div>
	<div class="grid md:grid-cols-3 gap-8">
		<div class="md:col-span-1">
			<div class="card overflow-hidden">
                <?php if (!empty($event['poster_path'])): ?>
                    <img src="<?php echo base_url($event['poster_path']); ?>" alt="Poster" class="w-full object-cover">
				<?php else: ?>
					<div class="p-6 text-gray-400">No poster</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="md:col-span-2 card p-6">
			<p class="text-gray-400">Organizer: <?php echo htmlspecialchars($event['organizer_name'] ?? ('#' . ($event['organizer_id'] ?? ''))); ?></p>
			<p class="text-gray-400 mt-1">Date: <?php echo htmlspecialchars(($event['event_date'] ?? '') . ' ' . ($event['event_time'] ?? '')); ?></p>
			<p class="text-gray-400 mt-1">Venue: <?php echo htmlspecialchars($event['venue'] ?? ''); ?></p>
			<p class="text-gray-400 mt-1">Category: <?php echo htmlspecialchars($event['category'] ?? ''); ?></p>
			<p class="text-gray-300 mt-4 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($event['description'] ?? '')); ?></p>
			<div class="mt-4 flex gap-2">
				<a class="btn btn-secondary" href="<?php echo base_url('/admin/events/edit?id='.(int)$event['id']); ?>">Edit</a>
			</div>
		</div>
	</div>
</div>



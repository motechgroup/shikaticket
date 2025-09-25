<?php /** @var array $events */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">All Events</h1>
	
	<?php if (empty($events)): ?>
		<div class="card p-6 text-center">
			<div class="text-gray-400 mb-4">
				<svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V7m6 0v4m-6 0h6m-6 0H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2M8 7H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
				</svg>
			</div>
			<h3 class="text-lg font-semibold mb-2">No Events Available</h3>
			<p class="text-gray-400">There are currently no published events. Check back soon!</p>
		</div>
	<?php else: ?>
		<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php foreach ($events as $event): ?>
				<div class="card p-0 overflow-hidden hover:shadow-lg transition-shadow">
					<?php if ($event['poster_path']): ?>
						<div class="aspect-square">
							<img src="<?php echo base_url('/' . $event['poster_path']); ?>" 
								 alt="<?php echo htmlspecialchars($event['title']); ?>" 
								 class="w-full h-full object-cover">
						</div>
					<?php else: ?>
						<div class="aspect-square bg-gray-800 flex items-center justify-center">
							<svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
							</svg>
						</div>
					<?php endif; ?>
					
					<div class="p-6">
						<div class="flex items-start justify-between mb-3">
							<h3 class="text-lg font-semibold line-clamp-2"><?php echo htmlspecialchars($event['title']); ?></h3>
							<?php if ($event['is_featured']): ?>
								<span class="badge bg-yellow-900 text-yellow-300 border-yellow-700 text-xs ml-2">Featured</span>
							<?php endif; ?>
						</div>
						
						<div class="text-sm text-gray-400 mb-3">
							<div class="flex items-center mb-1">
								<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
								</svg>
								<?php echo htmlspecialchars($event['venue']); ?>
							</div>
							<div class="flex items-center mb-1">
								<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V7m6 0v4m-6 0h6m-6 0H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2M8 7H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
								</svg>
								<?php echo date('M j, Y', strtotime($event['event_date'])); ?>
								<?php if ($event['event_time']): ?>
									at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
								<?php endif; ?>
							</div>
							<div class="flex items-center">
								<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
								</svg>
								<?php echo htmlspecialchars($event['organizer_name']); ?>
							</div>
						</div>
						
						<?php if ($event['description']): ?>
							<p class="text-sm text-gray-300 mb-4 line-clamp-3"><?php echo htmlspecialchars(substr($event['description'], 0, 150)) . (strlen($event['description']) > 150 ? '...' : ''); ?></p>
						<?php endif; ?>
						
						<div class="flex items-center justify-between">
							<?php if ($event['category']): ?>
								<span class="badge bg-blue-900 text-blue-300 border-blue-700 text-xs"><?php echo htmlspecialchars($event['category']); ?></span>
							<?php endif; ?>
							<a href="<?php echo base_url('/events/show?id=' . $event['id']); ?>" class="btn btn-primary btn-sm">
								View Details
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<style>
.line-clamp-2 {
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
}

.line-clamp-3 {
	display: -webkit-box;
	-webkit-line-clamp: 3;
	-webkit-box-orient: vertical;
	overflow: hidden;
}
</style>

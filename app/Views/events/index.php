<?php /** @var array $events */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="text-center mb-8">
		<h1 class="text-3xl md:text-4xl font-bold text-white mb-2">All Events</h1>
		<p class="text-gray-400">Discover amazing events and get your tickets today</p>
	</div>
	
	<?php if (empty($events)): ?>
		<div class="card p-8 text-center">
			<div class="text-gray-400 mb-6">
				<svg class="w-20 h-20 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V7m6 0v4m-6 0h6m-6 0H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2M8 7H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
				</svg>
			</div>
			<h3 class="text-xl font-semibold text-white mb-3">No Events Available</h3>
			<p class="text-gray-400 mb-6">There are currently no published events. Check back soon for exciting events!</p>
			<div class="flex justify-center">
				<a href="<?php echo base_url('/'); ?>" class="btn bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 transform hover:scale-105">
					Back to Home
				</a>
			</div>
		</div>
	<?php else: ?>
		<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php foreach ($events as $event): ?>
				<a href="<?php echo base_url('/events/show?id=' . $event['id']); ?>" class="card p-0 overflow-hidden hover:shadow-lg hover:scale-105 transition-all duration-300 border border-gray-700 hover:border-green-500/50 block cursor-pointer">
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
					
					<div class="p-4">
						<div class="flex items-start justify-between mb-2">
							<h3 class="text-lg font-semibold line-clamp-2 text-white"><?php echo htmlspecialchars($event['title']); ?></h3>
							<?php if ($event['is_featured']): ?>
								<span class="badge bg-gradient-to-r from-yellow-600 to-orange-500 text-white border-0 text-xs ml-2 px-2 py-1 rounded-full font-semibold shadow-lg">⭐</span>
							<?php endif; ?>
						</div>
						
						<div class="text-sm text-gray-400 mb-3 space-y-1">
							<div class="flex items-center">
								<svg class="w-3 h-3 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
								</svg>
								<span class="truncate"><?php echo htmlspecialchars($event['venue']); ?></span>
							</div>
							<div class="flex items-center">
								<svg class="w-3 h-3 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V7m6 0v4m-6 0h6m-6 0H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2M8 7H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
								</svg>
								<span class="truncate"><?php echo date('M j, Y', strtotime($event['event_date'])); ?>
								<?php if ($event['event_time']): ?>
									at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
								<?php endif; ?></span>
							</div>
							<div class="flex items-center">
								<svg class="w-3 h-3 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
								</svg>
								<span class="truncate"><?php echo htmlspecialchars($event['organizer_name']); ?></span>
							</div>
						</div>
						
						
						<div class="flex items-center justify-between">
							<?php if ($event['category']): ?>
								<span class="badge bg-gradient-to-r from-blue-600 to-purple-600 text-white border-0 text-xs px-3 py-1 rounded-full font-medium"><?php echo htmlspecialchars($event['category']); ?></span>
							<?php endif; ?>
							<div class="flex items-center gap-1 text-green-400 text-sm font-medium">
								<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
								</svg>
								Buy Ticket
							</div>
						</div>
					</div>
				</a>
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

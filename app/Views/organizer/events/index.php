<?php /** @var array $events */ ?>
<div class="max-w-7xl mx-auto px-4 py-6">
	<!-- Header Section -->
	<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
		<div>
			<h1 class="text-3xl font-bold text-white mb-2">My Events</h1>
			<p class="text-gray-400">Manage and track your event performance.</p>
		</div>
		<div class="mt-4 md:mt-0">
			<a href="<?php echo base_url('/organizer/events/create'); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
				</svg>
				Create Event
			</a>
		</div>
	</div>

	<?php if (empty($events)): ?>
		<!-- Empty State -->
		<div class="text-center py-16">
			<div class="p-6 bg-gray-800/50 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
				<svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
				</svg>
			</div>
			<h3 class="text-2xl font-bold text-white mb-4">No Events Yet</h3>
			<p class="text-gray-400 mb-8 max-w-md mx-auto">Start your event organizing journey by creating your first event. It's easy and takes just a few minutes!</p>
			<a href="<?php echo base_url('/organizer/events/create'); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-4 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
				</svg>
				Create Your First Event
			</a>
		</div>
	<?php else: ?>
		<!-- Desktop Table View -->
		<div class="hidden md:block">
			<div class="bg-gray-900/50 border border-gray-700 rounded-xl overflow-hidden">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-800/50">
						<tr>
							<th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Event Details</th>
							<th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date & Time</th>
							<th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Venue</th>
							<th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
							<th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-700">
						<?php foreach ($events as $event): ?>
						<tr class="hover:bg-gray-800/30 transition-colors">
							<td class="px-6 py-4">
								<div class="font-semibold text-white"><?php echo htmlspecialchars($event['title']); ?></div>
								<?php if (!empty($event['description'])): ?>
								<div class="text-sm text-gray-400 mt-1 line-clamp-2"><?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?>...</div>
								<?php endif; ?>
							</td>
							<td class="px-6 py-4">
								<div class="text-white"><?php echo date('M j, Y', strtotime($event['event_date'])); ?></div>
								<?php if (!empty($event['event_time'])): ?>
								<div class="text-sm text-gray-400"><?php echo date('g:i A', strtotime($event['event_time'])); ?></div>
								<?php endif; ?>
							</td>
							<td class="px-6 py-4 text-gray-300"><?php echo htmlspecialchars($event['venue']); ?></td>
							<td class="px-6 py-4">
								<span class="font-semibold text-green-400"><?php echo htmlspecialchars($event['currency']); ?> <?php echo number_format((float)$event['price'], 0); ?></span>
							</td>
							<td class="px-6 py-4">
								<div class="flex items-center gap-2 flex-wrap">
									<?php if ($event['is_featured']): ?>
										<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-600/20 to-orange-500/20 text-yellow-400 rounded-lg text-sm font-medium">
											<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
												<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
											</svg>
											Featured
										</span>
									<?php else: ?>
										<button onclick="openFeatureModal(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars($event['title']); ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-600/20 to-orange-500/20 text-yellow-400 hover:from-yellow-600/30 hover:to-orange-500/30 rounded-lg text-sm font-medium transition-colors">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
											</svg>
											Request Feature
										</button>
									<?php endif; ?>
									<a href="<?php echo base_url('/organizer/events/report?id=' . (int)$event['id']); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 rounded-lg text-sm font-medium transition-colors">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
										</svg>
										Report
									</a>
									<a href="<?php echo base_url('/organizer/events/edit?id=' . (int)$event['id']); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-600/20 text-gray-300 hover:bg-gray-600/30 rounded-lg text-sm font-medium transition-colors">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
										</svg>
										Edit
									</a>
									<form method="post" action="<?php echo base_url('/organizer/events/delete'); ?>" class="inline">
										<?php echo csrf_field(); ?>
										<input type="hidden" name="id" value="<?php echo (int)$event['id']; ?>">
										<button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600/20 text-red-400 hover:bg-red-600/30 rounded-lg text-sm font-medium transition-colors" onclick="return confirm('Delete this event? This action cannot be undone.');">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
											</svg>
											Delete
										</button>
									</form>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Mobile Card View -->
		<div class="md:hidden space-y-4">
			<?php foreach ($events as $event): ?>
			<div class="bg-gray-900/50 border border-gray-700 rounded-xl p-6">
				<div class="flex items-start justify-between mb-4">
					<div class="flex-1">
						<h3 class="text-lg font-semibold text-white mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
						<?php if (!empty($event['description'])): ?>
						<p class="text-sm text-gray-400 line-clamp-2"><?php echo htmlspecialchars(substr($event['description'], 0, 120)); ?>...</p>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="grid grid-cols-2 gap-4 mb-4">
					<div>
						<div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Date</div>
						<div class="text-white font-medium"><?php echo date('M j, Y', strtotime($event['event_date'])); ?></div>
						<?php if (!empty($event['event_time'])): ?>
						<div class="text-sm text-gray-400"><?php echo date('g:i A', strtotime($event['event_time'])); ?></div>
						<?php endif; ?>
					</div>
					<div>
						<div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Price</div>
						<div class="text-green-400 font-semibold text-lg"><?php echo htmlspecialchars($event['currency']); ?> <?php echo number_format((float)$event['price'], 0); ?></div>
					</div>
				</div>
				
				<div class="mb-4">
					<div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Venue</div>
					<div class="text-gray-300"><?php echo htmlspecialchars($event['venue']); ?></div>
				</div>
				
				<?php if ($event['is_featured']): ?>
					<div class="mb-4">
						<span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-yellow-600/20 to-orange-500/20 text-yellow-400 rounded-lg text-sm font-medium">
							<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
								<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
							</svg>
							This event is featured
						</span>
					</div>
				<?php else: ?>
					<div class="mb-4">
						<button onclick="openFeatureModal(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars($event['title']); ?>')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-yellow-600/20 to-orange-500/20 text-yellow-400 hover:from-yellow-600/30 hover:to-orange-500/30 rounded-lg text-sm font-medium transition-colors">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
							</svg>
							Request Feature (+5% Commission)
						</button>
					</div>
				<?php endif; ?>
				
				<div class="flex gap-2">
					<a href="<?php echo base_url('/organizer/events/report?id=' . (int)$event['id']); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 rounded-lg text-sm font-medium transition-colors">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
						</svg>
						Report
					</a>
					<a href="<?php echo base_url('/organizer/events/edit?id=' . (int)$event['id']); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-600/20 text-gray-300 hover:bg-gray-600/30 rounded-lg text-sm font-medium transition-colors">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
						</svg>
						Edit
					</a>
					<form method="post" action="<?php echo base_url('/organizer/events/delete'); ?>" class="flex-1">
						<?php echo csrf_field(); ?>
						<input type="hidden" name="id" value="<?php echo (int)$event['id']; ?>">
						<button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600/20 text-red-400 hover:bg-red-600/30 rounded-lg text-sm font-medium transition-colors" onclick="return confirm('Delete this event? This action cannot be undone.');">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
							</svg>
							Delete
						</button>
					</form>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<!-- Feature Request Modal -->
<div id="featureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
	<div class="bg-gray-900 border border-gray-700 rounded-xl p-6 max-w-md w-full mx-4">
		<div class="flex items-center justify-between mb-4">
			<h3 class="text-lg font-semibold text-white">Request Event Feature</h3>
			<button onclick="closeFeatureModal()" class="text-gray-400 hover:text-white">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
		
		<div class="mb-4">
			<div class="bg-yellow-600/20 border border-yellow-600/30 rounded-lg p-4 mb-4">
				<div class="flex items-center gap-2 text-yellow-400 font-medium mb-2">
					<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
						<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
					</svg>
					Featured Content Benefits
				</div>
				<ul class="text-sm text-gray-300 space-y-1">
					<li>• Your event appears at the top of event listings</li>
					<li>• Featured badge displayed on your event</li>
					<li>• Increased visibility and ticket sales</li>
					<li>• Additional <?php echo number_format($commissionRate, 1); ?>% commission on ticket sales</li>
				</ul>
			</div>
			
			<p class="text-gray-300 text-sm mb-4">
				<strong id="modalEventTitle"></strong> will be reviewed by our admin team. 
				If approved, it will be featured with increased visibility and a <strong><?php echo number_format($commissionRate, 1); ?>% additional commission</strong> will be applied to ticket sales.
			</p>
		</div>
		
		<form id="featureRequestForm" method="POST" action="<?php echo base_url('/organizer/events/request-feature'); ?>">
			<?php echo csrf_field(); ?>
			<input type="hidden" id="modalEventId" name="event_id" value="">
			
			<input type="hidden" name="commission" value="<?php echo number_format($commissionRate, 2); ?>">
			
			<div class="flex gap-3">
				<button type="button" onclick="closeFeatureModal()" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
					Cancel
				</button>
				<button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-500 hover:from-yellow-700 hover:to-orange-600 text-white rounded-lg transition-colors font-medium">
					Submit Request
				</button>
			</div>
		</form>
	</div>
</div>

<style>
.line-clamp-2 {
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
}
</style>

<script>
function openFeatureModal(eventId, eventTitle) {
	document.getElementById('modalEventId').value = eventId;
	document.getElementById('modalEventTitle').textContent = eventTitle;
	document.getElementById('featureModal').classList.remove('hidden');
	document.body.style.overflow = 'hidden';
}

function closeFeatureModal() {
	document.getElementById('featureModal').classList.add('hidden');
	document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('featureModal').addEventListener('click', function(e) {
	if (e.target === this) {
		closeFeatureModal();
	}
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeFeatureModal();
	}
});
</script>



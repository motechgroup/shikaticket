<?php /** @var array $organizer */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center gap-4">
		<img src="<?php echo base_url($organizer['avatar_path'] ?? ''); ?>" alt="avatar" class="w-16 h-16 rounded-full object-cover bg-gray-800">
		<div>
			<h1 class="text-2xl font-semibold"><?php echo htmlspecialchars($organizer['full_name']); ?></h1>
			<div class="text-gray-400 text-sm">Followers: <?php echo (int)($followersCount ?? 0); ?></div>
		</div>
		<div class="ml-auto">
			<?php if (isset($_SESSION['user_id'])): ?>
				<form method="post" action="<?php echo base_url($isFollowing?'/organizers/unfollow':'/organizers/follow'); ?>">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="organizer_id" value="<?php echo (int)$organizer['id']; ?>">
					<button class="btn <?php echo $isFollowing?'btn-secondary':'btn-primary'; ?>"><?php echo $isFollowing?'Unfollow':'Follow'; ?></button>
				</form>
			<?php else: ?>
				<a class="btn btn-primary" href="<?php echo base_url('/login'); ?>">Login to Follow</a>
			<?php endif; ?>
		</div>
	</div>

	<!-- Upcoming Events -->
	<?php if (!empty($upcomingEventsList)): ?>
	<h2 class="font-semibold mt-8 mb-3">Upcoming Events</h2>
	<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
		<?php foreach ($upcomingEventsList as $ev): ?>
		<a href="<?php echo base_url('/events/show?id='.(int)$ev['id']); ?>" class="card card-hover overflow-hidden">
			<?php if (!empty($ev['poster_path'])): ?>
				<img src="<?php echo base_url($ev['poster_path']); ?>" class="w-full h-44 object-cover" alt="poster">
			<?php endif; ?>
			<div class="p-3">
				<div class="font-semibold truncate"><?php echo htmlspecialchars($ev['title']); ?></div>
				<div class="text-xs text-gray-400"><?php echo htmlspecialchars($ev['event_date'] ?? ''); ?></div>
			</div>
		</a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<!-- Past Events -->
	<?php if (!empty($pastEventsList)): ?>
	<h2 class="font-semibold mt-8 mb-3">Past Events</h2>
	<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
		<?php foreach ($pastEventsList as $ev): ?>
		<div class="card overflow-hidden opacity-75 cursor-not-allowed" title="Past Event - <?php echo htmlspecialchars($ev['title']); ?>">
			<?php if (!empty($ev['poster_path'])): ?>
				<img src="<?php echo base_url($ev['poster_path']); ?>" class="w-full h-24 object-cover" alt="poster">
			<?php else: ?>
				<div class="w-full h-24 bg-gray-700 flex items-center justify-center">
					<svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
				</div>
			<?php endif; ?>
			<div class="p-2">
				<div class="font-semibold text-xs truncate"><?php echo htmlspecialchars($ev['title']); ?></div>
				<div class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($ev['event_date'])); ?></div>
				<div class="text-xs text-red-400 font-medium">Past Event</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<!-- No Events Message -->
	<?php if (empty($upcomingEventsList) && empty($pastEventsList)): ?>
	<div class="text-center py-8 text-gray-400">
		<svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V7m6 0v4m-6 0h6m-6 0H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2M8 7H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
		</svg>
		<h3 class="text-lg font-semibold text-white mb-2">No Events Yet</h3>
		<p class="text-gray-400">This organizer hasn't created any events yet.</p>
	</div>
	<?php endif; ?>
</div>

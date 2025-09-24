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

	<h2 class="font-semibold mt-8 mb-3">Events</h2>
	<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
		<?php foreach (($eventsList ?? []) as $ev): ?>
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
</div>

<?php /** @var array $banners */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">Banners</h1>
		<a href="<?php echo base_url('/admin/banners/create'); ?>" class="btn btn-primary">Add Banner</a>
	</div>
	<div class="card p-4">
		<?php if (empty($banners)): ?>
			<div class="p-6 text-gray-400">No banners yet.</div>
		<?php else: ?>
			<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
				<?php foreach ($banners as $b): ?>
					<div class="card overflow-hidden">
						<a href="<?php echo base_url('/admin/banners/edit?id='.(int)$b['id']); ?>">
							<img src="<?php echo base_url($b['image_path']); ?>" class="w-full h-40 object-cover" alt="banner">
						</a>
						<div class="p-3 flex items-center justify-between">
							<div>
								<div class="font-semibold text-sm"><?php echo htmlspecialchars($b['title']); ?></div>
								<div class="text-xs text-gray-400">Order: <?php echo (int)$b['sort_order']; ?></div>
							</div>
							<form method="post" action="<?php echo base_url('/admin/banners/delete'); ?>" onsubmit="return confirm('Delete banner?');" class="flex items-center gap-2">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
								<button class="btn btn-primary">Delete</button>
							</form>
							<form method="post" action="<?php echo base_url('/admin/banners/toggle'); ?>">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
								<input type="hidden" name="is_active" value="<?php echo (int)($b['is_active'] ? 0 : 1); ?>">
								<button class="btn btn-secondary"><?php echo $b['is_active'] ? 'Deactivate' : 'Activate'; ?></button>
							</form>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>



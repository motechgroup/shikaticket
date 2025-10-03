<div class="max-w-3xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Edit Partner Logo</h1>
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url('/admin/partner-logos/update'); ?>" class="card p-6 space-y-4">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="id" value="<?php echo (int)$logo['id']; ?>">
		<div>
			<label class="block text-sm mb-1">Title</label>
			<input name="title" class="input" value="<?php echo htmlspecialchars($logo['title']); ?>" required>
		</div>
		<div>
			<label class="block text-sm mb-1">Link URL (optional)</label>
			<input name="link_url" class="input" value="<?php echo htmlspecialchars($logo['link_url']); ?>" placeholder="https://...">
		</div>
		<div>
			<label class="block text-sm mb-1">Image</label>
			<input type="file" name="image" class="input" accept="image/*">
			<?php if (!empty($logo['image_path'])): ?><div class="mt-2"><img src="<?php echo base_url($logo['image_path']); ?>" class="h-8"></div><?php endif; ?>
		</div>
		<div>
			<label class="block text-sm mb-1">Sort Order</label>
			<input type="number" name="sort_order" class="input" value="<?php echo (int)$logo['sort_order']; ?>">
		</div>
		<button class="btn btn-primary">Update</button>
	</form>
</div>

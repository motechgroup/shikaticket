<div class="max-w-4xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Add Banner</h1>
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url('/admin/banners'); ?>" class="card p-6 space-y-4">
		<?php echo csrf_field(); ?>
		<div>
			<label class="block text-sm mb-1">Title</label>
			<input name="title" class="input" required>
		</div>
		<div>
			<label class="block text-sm mb-1">Link URL (optional)</label>
			<input name="link_url" class="input" placeholder="https://...">
		</div>
		<div>
			<label class="block text-sm mb-1">Sort Order</label>
			<input type="number" name="sort_order" class="input" value="0">
		</div>
		<div>
			<label class="block text-sm mb-1">Image</label>
			<input type="file" name="image" accept="image/*" class="input" required>
			<div class="text-xs text-gray-400 mt-1">Recommended size: 1600x500 (minimum 1200x400). Use JPG or PNG, under 2MB. Images are displayed full-width on the homepage carousel.</div>
		</div>
		<button class="btn btn-primary">Save</button>
	</form>
</div>



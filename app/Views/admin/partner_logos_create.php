<div class="max-w-3xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Add Partner Logo</h1>
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url('/admin/partner-logos'); ?>" class="card p-6 space-y-4">
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
			<label class="block text-sm mb-1">Image</label>
			<input type="file" name="image" class="input" accept="image/*" required>
			<div class="text-xs text-gray-400 mt-1">Recommended height ~40px, SVG/PNG preferred.</div>
		</div>
		<div>
			<label class="block text-sm mb-1">Sort Order</label>
			<input type="number" name="sort_order" class="input" value="0">
		</div>
		<button class="btn btn-primary">Save</button>
	</form>
</div>

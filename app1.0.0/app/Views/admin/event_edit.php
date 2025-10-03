<?php /** @var array $event */ ?>
<div class="max-w-3xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Edit Event (Admin)</h1>
	<form method="post" action="<?php echo base_url('/admin/events/update'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="id" value="<?php echo (int)$event['id']; ?>">
		<div>
			<label class="block text-sm mb-1">Title</label>
			<input name="title" value="<?php echo htmlspecialchars($event['title']); ?>" class="input" required>
		</div>
		<div class="grid sm:grid-cols-2 gap-4">
			<label class="inline-flex items-center gap-2">
				<input type="checkbox" name="is_featured" <?php echo ((int)$event['is_featured'] === 1) ? 'checked' : ''; ?>> Featured
			</label>
			<label class="inline-flex items-center gap-2">
				<input type="checkbox" name="is_published" <?php echo ((int)$event['is_published'] === 1) ? 'checked' : ''; ?>> Published
			</label>
		</div>
		<button class="btn btn-primary">Save</button>
		<a class="btn btn-secondary" href="<?php echo base_url('/admin/events'); ?>">Cancel</a>
	</form>
</div>



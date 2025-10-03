<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Partner Logos</h1>
	<div class="mb-4">
		<a class="btn btn-primary" href="<?php echo base_url('/admin/partner-logos/create'); ?>">Add Logo</a>
	</div>
	<div class="card overflow-x-auto">
		<table class="min-w-full text-sm table">
			<thead><tr><th class="px-3 py-2 text-left">Logo</th><th class="px-3 py-2 text-left">Title</th><th class="px-3 py-2 text-left">Link</th><th class="px-3 py-2 text-left">Status</th><th class="px-3 py-2">Actions</th></tr></thead>
			<tbody>
				<?php foreach (($logos ?? []) as $l): ?>
				<tr>
					<td class="px-3 py-2"><?php if ($l['image_path']): ?><img src="<?php echo base_url($l['image_path']); ?>" class="h-8"><?php endif; ?></td>
					<td class="px-3 py-2"><?php echo htmlspecialchars($l['title']); ?></td>
					<td class="px-3 py-2"><a class="link" target="_blank" href="<?php echo htmlspecialchars($l['link_url']); ?>"><?php echo htmlspecialchars($l['link_url']); ?></a></td>
					<td class="px-3 py-2"><?php echo ((int)$l['is_active']===1)?'Active':'Inactive'; ?></td>
					<td class="px-3 py-2 flex gap-2">
						<a class="btn btn-secondary" href="<?php echo base_url('/admin/partner-logos/edit?id='.(int)$l['id']); ?>">Edit</a>
						<form method="post" action="<?php echo base_url('/admin/partner-logos/toggle'); ?>">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="id" value="<?php echo (int)$l['id']; ?>">
							<input type="hidden" name="is_active" value="<?php echo (int)$l['is_active']===1?0:1; ?>">
							<button class="btn btn-secondary"><?php echo (int)$l['is_active']===1?'Deactivate':'Activate'; ?></button>
						</form>
						<form method="post" action="<?php echo base_url('/admin/partner-logos/delete'); ?>" onsubmit="return confirm('Delete logo?');">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="id" value="<?php echo (int)$l['id']; ?>">
							<button class="btn btn-secondary">Delete</button>
						</form>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

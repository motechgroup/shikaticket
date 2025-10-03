<div class="max-w-4xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Help & Documentation</h1>
	<div class="grid md:grid-cols-2 gap-4">
		<?php foreach (($items ?? []) as $it): ?>
			<a href="<?php echo base_url('/help/show?slug=' . urlencode($it['slug'])); ?>" class="card p-4 hover:border-red-600">
				<div class="font-semibold"><?php echo htmlspecialchars($it['title']); ?></div>
				<div class="text-sm text-gray-400 mt-1"><?php echo htmlspecialchars($it['slug']); ?></div>
			</a>
		<?php endforeach; ?>
	</div>
</div>

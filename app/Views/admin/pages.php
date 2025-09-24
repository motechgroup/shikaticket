<?php /** @var array $pages */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Pages</h1>
        <a href="<?php echo base_url('/admin/pages/edit'); ?>" class="btn btn-primary">New Page</a>
    </div>
    <div class="card p-4">
        <?php if (empty($pages)): ?>
            <div class="p-6 text-gray-400">No pages yet.</div>
        <?php else: ?>
            <table class="min-w-full text-sm table">
                <thead><tr><th class="p-3 text-left">Title</th><th class="p-3 text-left">Slug</th><th class="p-3 text-left">Published</th><th class="p-3 text-left"></th></tr></thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td class="p-3"><?php echo htmlspecialchars($p['title']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($p['slug']); ?></td>
                        <td class="p-3"><?php echo (int)$p['is_published'] ? 'Yes' : 'No'; ?></td>
                        <td class="p-3 flex gap-2">
                            <a class="btn btn-secondary" href="<?php echo base_url('/admin/pages/edit?id='.(int)$p['id']); ?>">Edit</a>
                            <form method="post" action="<?php echo base_url('/admin/pages/delete'); ?>" onsubmit="return confirm('Delete page?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                                <button class="btn btn-primary">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>



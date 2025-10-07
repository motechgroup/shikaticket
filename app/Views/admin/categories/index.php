<?php /** @var array $categories */ ?>
<div class="max-w-6xl mx-auto px-4 py-8">
	<?php $pageTitle = 'Categories'; include __DIR__ . '/../../components/mobile_nav_simple.php'; ?>
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Event Categories</h1>
            <a class="btn btn-primary" href="<?php echo base_url('/admin/categories/create'); ?>">New Category</a>
        </div>
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-2">ID</th>
                    <th class="text-left py-2">Name</th>
                    <th class="text-left py-2">Slug</th>
                    <th class="text-left py-2">Active</th>
                    <th class="text-left py-2">Sort</th>
                    <th class="text-left py-2"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr class="border-b border-gray-800">
                        <td class="py-2 text-sm"><?php echo (int)$c['id']; ?></td>
                        <td class="py-2 text-sm"><?php echo htmlspecialchars($c['name']); ?></td>
                        <td class="py-2 text-sm"><?php echo htmlspecialchars($c['slug']); ?></td>
                        <td class="py-2 text-sm"><?php echo (int)$c['is_active'] ? 'Yes' : 'No'; ?></td>
                        <td class="py-2 text-sm"><?php echo (int)$c['sort_order']; ?></td>
                        <td class="py-2 text-sm flex gap-2">
                            <a class="btn btn-secondary" href="<?php echo base_url('/admin/categories/edit?id='.(int)$c['id']); ?>">Edit</a>
                            <form method="post" action="<?php echo base_url('/admin/categories/toggle'); ?>" onsubmit="return confirm('Toggle active?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                                <button class="btn btn-secondary">Toggle</button>
                            </form>
                            <form method="post" action="<?php echo base_url('/admin/categories/delete'); ?>" onsubmit="return confirm('Delete category?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                                <button class="btn btn-secondary">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php /** @var array|null $category */ ?>
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="card p-6">
        <h1 class="text-xl font-semibold mb-4"><?php echo $category ? 'Edit Category' : 'New Category'; ?></h1>
        <form method="post" action="<?php echo base_url('/admin/categories/save'); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int)($category['id'] ?? 0); ?>">
            <div>
                <label class="block text-sm mb-1">Name</label>
                <input class="input" type="text" name="name" value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Slug</label>
                <input class="input" type="text" name="slug" value="<?php echo htmlspecialchars($category['slug'] ?? ''); ?>" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Sort Order</label>
                    <input class="input" type="number" name="sort_order" value="<?php echo (int)($category['sort_order'] ?? 0); ?>">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" <?php echo ((int)($category['is_active'] ?? 1)) ? 'checked' : ''; ?>>
                    <label for="is_active">Active</label>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-primary">Save</button>
                <a class="btn btn-secondary" href="<?php echo base_url('/admin/categories'); ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>



<?php /** @var array $banner */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Edit Banner</h1>
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('/admin/banners/update'); ?>" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$banner['id']; ?>">
        <div>
            <label class="block text-sm mb-1">Title</label>
            <input name="title" class="input" value="<?php echo htmlspecialchars($banner['title']); ?>" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Link URL (optional)</label>
            <input name="link_url" class="input" placeholder="https://..." value="<?php echo htmlspecialchars($banner['link_url'] ?? ''); ?>">
        </div>
        <div>
            <label class="block text-sm mb-1">Sort Order</label>
            <input type="number" name="sort_order" class="input" value="<?php echo (int)$banner['sort_order']; ?>">
        </div>
        <div>
            <label class="block text-sm mb-1">Replace Image (optional)</label>
            <input type="file" name="image" accept="image/*" class="input">
            <div class="text-xs text-gray-400 mt-1">Recommended size: 1600x500 (min 1200x400)</div>
            <img src="<?php echo base_url($banner['image_path']); ?>" class="mt-3 w-full h-40 object-cover rounded border border-gray-800" alt="banner current">
        </div>
        <button class="btn btn-primary">Update</button>
    </form>
</div>



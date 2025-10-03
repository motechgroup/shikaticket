<?php /** @var array $banner */ ?>
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo base_url('/admin/travel-banners'); ?>" class="link">← Back to Travel Banners</a>
    </div>

    <h1 class="text-2xl font-bold mb-6">Edit Travel Banner</h1>

    <div class="card p-6">
        <form method="POST" action="<?php echo base_url('/admin/travel-banners/update'); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Title *</label>
                    <input type="text" name="title" required class="input" value="<?php echo htmlspecialchars($banner['title']); ?>" placeholder="Banner title">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Subtitle</label>
                    <input type="text" name="subtitle" class="input" value="<?php echo htmlspecialchars($banner['subtitle'] ?? ''); ?>" placeholder="Banner subtitle">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" rows="3" class="textarea" placeholder="Banner description"><?php echo htmlspecialchars($banner['description'] ?? ''); ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Current Image</label>
                    <?php if (!empty($banner['image_path'])): ?>
                    <img src="<?php echo base_url($banner['image_path']); ?>" alt="Current banner" class="w-full max-w-md h-32 object-cover rounded mb-2">
                    <?php endif; ?>
                    <label class="block text-sm font-medium mb-2">New Image (optional)</label>
                    <input type="file" name="image" accept="image/*" class="input">
                    <div class="text-sm text-gray-400 mt-1">Leave empty to keep current image. Recommended: 1200x400px or similar aspect ratio</div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Button Text</label>
                    <input type="text" name="button_text" class="input" value="<?php echo htmlspecialchars($banner['button_text'] ?? ''); ?>" placeholder="e.g., Explore Now">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Button URL</label>
                    <input type="url" name="button_url" class="input" value="<?php echo htmlspecialchars($banner['button_url'] ?? ''); ?>" placeholder="https://example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Sort Order</label>
                    <input type="number" name="sort_order" class="input" value="<?php echo (int)$banner['sort_order']; ?>" placeholder="0">
                    <div class="text-sm text-gray-400 mt-1">Lower numbers appear first</div>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="btn btn-primary">Update Banner</button>
                <a href="<?php echo base_url('/admin/travel-banners'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

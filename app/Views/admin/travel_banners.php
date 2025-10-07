<?php /** @var array $banners */ ?>
<div class="max-w-7xl mx-auto px-4 py-8">
	<?php $pageTitle = 'Travel Banners'; include __DIR__ . '/../components/mobile_nav_simple.php'; ?>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Travel Banners</h1>
        <a href="<?php echo base_url('/admin/travel-banners/create'); ?>" class="btn btn-primary">Create Banner</a>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Button</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($banners)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">No travel banners found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td>
                            <?php if (!empty($banner['image_path'])): ?>
                            <img src="<?php echo base_url($banner['image_path']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" class="w-16 h-12 object-cover rounded">
                            <?php else: ?>
                            <div class="w-16 h-12 bg-gray-700 rounded flex items-center justify-center text-xs">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td class="font-medium"><?php echo htmlspecialchars($banner['title']); ?></td>
                        <td class="text-gray-400"><?php echo htmlspecialchars($banner['subtitle'] ?? ''); ?></td>
                        <td>
                            <?php if (!empty($banner['button_text'])): ?>
                            <span class="badge"><?php echo htmlspecialchars($banner['button_text']); ?></span>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int)$banner['sort_order']; ?></td>
                        <td>
                            <form method="POST" action="<?php echo base_url('/admin/travel-banners/toggle'); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
                                <input type="hidden" name="active" value="<?php echo (int)$banner['is_active'] ? 0 : 1; ?>">
                                <button type="submit" class="badge <?php echo (int)$banner['is_active'] ? 'bg-green-600' : 'bg-gray-600'; ?>">
                                    <?php echo (int)$banner['is_active'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="<?php echo base_url('/admin/travel-banners/edit?id=' . $banner['id']); ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <form method="POST" action="<?php echo base_url('/admin/travel-banners/delete'); ?>" class="inline" onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

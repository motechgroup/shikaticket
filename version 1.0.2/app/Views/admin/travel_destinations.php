<?php /** @var array $destinations */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Travel Destinations</h1>
        <a class="btn btn-secondary" href="<?php echo base_url('/admin'); ?>">Back</a>
    </div>
    <div class="card p-4">
        <?php if (empty($destinations)): ?>
            <div class="p-6 text-gray-400">No destinations yet.</div>
        <?php else: ?>
            <table class="min-w-full text-sm table">
                <thead>
                    <tr>
                        <th class="p-3 text-left">Image</th>
                        <th class="p-3 text-left">Title</th>
                        <th class="p-3 text-left">Agency</th>
                        <th class="p-3 text-left">Departure</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-left">Published</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($destinations as $d): ?>
                    <tr>
                        <td class="p-3">
                            <?php if (!empty($d['image_path'])): ?>
                                <img src="<?php echo base_url($d['image_path']); ?>" alt="Image" class="h-10 w-10 object-cover rounded">
                            <?php else: ?>
                                <span class="text-gray-500 text-xs">No image</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3"><?php echo htmlspecialchars($d['title']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($d['company_name']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($d['departure_date']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></td>
                        <td class="p-3"><?php echo (int)$d['is_published'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>



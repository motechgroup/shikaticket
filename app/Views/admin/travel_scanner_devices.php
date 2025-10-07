<?php /** @var array $devices */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
	<?php $pageTitle = 'Travel Scanners'; include __DIR__ . '/../components/mobile_nav_simple.php'; ?>
    <h1 class="text-2xl font-semibold mb-6">Admin Travel Scanners</h1>
    <form method="post" action="<?php echo base_url('/admin/travel/scanners/create'); ?>" class="card p-4 mb-4">
        <?php echo csrf_field(); ?>
        <div class="flex gap-2 items-end">
            <div class="flex-1">
                <label class="block text-sm mb-1">Device Name</label>
                <input class="input" name="device_name" required>
            </div>
            <button class="btn btn-primary">Add</button>
        </div>
    </form>
    <div class="card">
        <table class="min-w-full text-sm table">
            <thead>
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Code</th>
                    <th class="p-3 text-left">Active</th>
                    <th class="p-3 text-left"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devices as $d): ?>
                <tr>
                    <td class="p-3"><?php echo htmlspecialchars($d['device_name']); ?></td>
                    <td class="p-3 font-mono text-xs"><?php echo htmlspecialchars($d['device_code']); ?></td>
                    <td class="p-3"><?php echo ((int)($d['is_active'] ?? 1)) ? 'Yes' : 'No'; ?></td>
                    <td class="p-3">
                        <form method="post" action="<?php echo base_url('/admin/travel/scanners/update'); ?>" class="inline-block">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="device_id" value="<?php echo (int)$d['id']; ?>">
                            <input type="text" name="device_name" value="<?php echo htmlspecialchars($d['device_name']); ?>" class="input w-40">
                            <label class="ml-2 text-xs inline-flex items-center gap-1"><input type="checkbox" name="is_active" <?php echo ((int)($d['is_active'] ?? 1)) ? 'checked' : ''; ?>> Active</label>
                            <button class="btn btn-secondary ml-2">Save</button>
                        </form>
                        <form method="post" action="<?php echo base_url('/admin/travel/scanners/delete'); ?>" class="inline-block ml-2" onsubmit="return confirm('Delete scanner?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="device_id" value="<?php echo (int)$d['id']; ?>">
                            <button class="btn btn-primary">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>



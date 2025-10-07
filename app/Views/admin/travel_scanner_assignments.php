<?php /** @var array $destinations */ /** @var array $devices */ ?>
<div class="max-w-5xl mx-auto px-4 py-10">
	<?php $pageTitle = 'Scanner Assignments'; include __DIR__ . '/../components/mobile_nav_simple.php'; ?>
    <h1 class="text-2xl font-semibold mb-6">Assign Travel Scanners to Destinations</h1>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="card p-4">
            <h2 class="font-semibold mb-3">Destinations</h2>
            <form method="post" action="<?php echo base_url('/admin/travel/scanners/assign'); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm mb-1">Destination</label>
                    <select name="destination_id" class="input" required>
                        <?php foreach ($destinations as $d): ?>
                        <option value="<?php echo (int)$d['id']; ?>"><?php echo htmlspecialchars($d['title']); ?> (<?php echo htmlspecialchars($d['departure_date']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Scanner</label>
                    <select name="scanner_id" class="input" required>
                        <?php foreach ($devices as $dev): ?>
                        <option value="<?php echo (int)$dev['id']; ?>"><?php echo htmlspecialchars($dev['device_name']); ?> (<?php echo htmlspecialchars($dev['device_code']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary">Assign</button>
            </form>
        </div>
        <div class="card p-4">
            <h2 class="font-semibold mb-3">Current Assignments</h2>
            <p class="text-sm text-gray-400">Use universal scanner; assignments restrict which destination codes are valid per device.</p>
        </div>
    </div>
</div>



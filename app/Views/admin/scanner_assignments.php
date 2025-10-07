<?php /** @var array $events */ /** @var array $devices */ ?>
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Assign Scanners to Events</h1>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="card p-4">
            <h2 class="font-semibold mb-3">Events</h2>
            <form method="post" action="<?php echo base_url('/admin/scanners/assign'); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm mb-1">Event</label>
                    <select name="event_id" class="input" required>
                        <?php foreach ($events as $e): ?>
                        <option value="<?php echo (int)$e['id']; ?>"><?php echo htmlspecialchars($e['title']); ?> (<?php echo htmlspecialchars($e['event_date']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Scanner</label>
                    <select name="scanner_id" class="input" required>
                        <?php foreach ($devices as $d): ?>
                        <option value="<?php echo (int)$d['id']; ?>"><?php echo htmlspecialchars($d['device_name']); ?> (<?php echo htmlspecialchars($d['device_code']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary">Assign</button>
            </form>
        </div>
        <div class="card p-4">
            <h2 class="font-semibold mb-3">Current Assignments</h2>
            <p class="text-sm text-gray-400">Use the organizer scanner reports to view scans; this page focuses on assignments.</p>
        </div>
    </div>
</div>

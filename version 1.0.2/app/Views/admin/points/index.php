<?php /** @var array $entries */ ?>
<?php /** @var array $config */ ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="card p-6 mb-6">
        <h1 class="text-xl font-semibold mb-4">Loyalty Points</h1>
        <div class="mb-6">
            <h2 class="font-semibold mb-3">Configuration</h2>
            <form method="post" action="<?php echo base_url('/admin/points/save-config'); ?>" class="grid md:grid-cols-4 gap-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="text-sm text-gray-400">Order: points per 100 KES</label>
                    <input class="input" type="number" min="0" name="order_per_100_kes" value="<?php echo (int)($config['order_per_100_kes'] ?? 1); ?>" required>
                </div>
                <div>
                    <label class="text-sm text-gray-400">Travel: points per 100 KES</label>
                    <input class="input" type="number" min="0" name="travel_per_100_kes" value="<?php echo (int)($config['travel_per_100_kes'] ?? 1); ?>" required>
                </div>
                <div>
                    <label class="text-sm text-gray-400">Daily login points</label>
                    <input class="input" type="number" min="0" name="login" value="<?php echo (int)($config['login'] ?? 0); ?>" required>
                </div>
                <div>
                    <label class="text-sm text-gray-400">Add email (first-time)</label>
                    <input class="input" type="number" min="0" name="add_email" value="<?php echo (int)($config['add_email'] ?? 0); ?>" required>
                </div>
                <div class="md:col-span-4">
                    <button class="btn btn-primary">Save Configuration</button>
                </div>
            </form>
        </div>
        <div class="mb-4 text-sm text-gray-400">
            Points are awarded automatically by the system based on configured rules.
        </div>
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2">Date</th>
                        <th class="text-left py-2">User</th>
                        <th class="text-left py-2">Points</th>
                        <th class="text-left py-2">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $e): ?>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 text-sm"><?php echo htmlspecialchars($e['created_at']); ?></td>
                            <td class="py-2 text-sm"><?php echo htmlspecialchars($e['email'] ?? ('User #' . $e['user_id'])); ?></td>
                            <td class="py-2 text-sm <?php echo ((int)$e['points']>=0)?'text-green-400':'text-red-400'; ?>"><?php echo (int)$e['points']; ?></td>
                            <td class="py-2 text-sm"><?php echo htmlspecialchars($e['reason']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



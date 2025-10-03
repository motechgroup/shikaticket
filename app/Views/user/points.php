<?php /** @var array $entries */ /** @var int $total */ ?>
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="card p-6 mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">My Loyalty Points</h1>
            <div class="text-2xl font-bold text-yellow-400"><?php echo (int)$total; ?> pts</div>
        </div>
    </div>
    <div class="card p-6">
        <h2 class="font-semibold mb-3">History</h2>
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2">Date</th>
                        <th class="text-left py-2">Points</th>
                        <th class="text-left py-2">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $e): ?>
                        <tr class="border-b border-gray-800">
                            <td class="py-2 text-sm"><?php echo htmlspecialchars($e['created_at']); ?></td>
                            <td class="py-2 text-sm <?php echo ((int)$e['points']>=0)?'text-green-400':'text-red-400'; ?>"><?php echo (int)$e['points']; ?></td>
                            <td class="py-2 text-sm"><?php echo htmlspecialchars($e['reason']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



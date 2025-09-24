<?php /** @var array $event */ /** @var array $stats */ /** @var array $orders */ /** @var array $tickets */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-2"><?php echo htmlspecialchars($event['title']); ?></h1>
	<p class="text-gray-400 mb-6"><?php echo htmlspecialchars(($event['event_date'] ?? '') . ' ' . ($event['event_time'] ?? '') . ' • ' . ($event['venue'] ?? '')); ?></p>
	<div class="grid sm:grid-cols-3 gap-4 mb-6">
		<div class="card p-4"><div class="text-sm text-gray-400">Tickets Sold</div><div class="text-2xl font-semibold"><?php echo (int)($stats['tickets'] ?? 0); ?></div></div>
		<div class="card p-4"><div class="text-sm text-gray-400">Revenue</div><div class="text-2xl font-semibold">KES <?php echo number_format((float)($stats['revenue'] ?? 0), 2); ?></div></div>
		<div class="card p-4"><div class="text-sm text-gray-400">Price</div><div class="text-2xl font-semibold">KES <?php echo number_format((float)($event['price'] ?? 0), 2); ?></div></div>
	</div>
    <div class="card p-4">
		<h2 class="font-semibold mb-3">Paid Orders</h2>
		<table class="min-w-full text-sm table">
			<thead>
				<tr>
					<th class="p-3 text-left">Order #</th>
					<th class="p-3 text-left">Amount</th>
					<th class="p-3 text-left">Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($orders as $o): ?>
				<tr>
					<td class="p-3">#<?php echo (int)$o['id']; ?></td>
					<td class="p-3"><?php echo htmlspecialchars($o['currency']); ?> <?php echo number_format((float)$o['total_amount'], 2); ?></td>
					<td class="p-3"><?php echo htmlspecialchars($o['created_at']); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
    <div class="card p-4 mt-6">
        <h2 class="font-semibold mb-3">Redemption Summary</h2>
        <?php
            $redeemed = db()->prepare('SELECT COUNT(*) AS redeemed FROM tickets t JOIN order_items oi ON oi.id=t.order_item_id WHERE oi.event_id = ? AND t.status="redeemed"');
            $redeemed->execute([$event['id']]);
            $redeemed = $redeemed->fetch()['redeemed'] ?? 0;
            $total = db()->prepare('SELECT COUNT(*) AS total FROM tickets t JOIN order_items oi ON oi.id=t.order_item_id WHERE oi.event_id = ?');
            $total->execute([$event['id']]);
            $total = $total->fetch()['total'] ?? 0;
        ?>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="card p-4"><div class="text-sm text-gray-400">Redeemed</div><div class="text-2xl font-semibold"><?php echo (int)$redeemed; ?></div></div>
            <div class="card p-4"><div class="text-sm text-gray-400">Remaining</div><div class="text-2xl font-semibold"><?php echo max(0, (int)$total - (int)$redeemed); ?></div></div>
        </div>
    </div>

    <div class="card p-4 mt-6">
        <h2 class="font-semibold mb-3">Tickets</h2>
        <?php if (empty($tickets)): ?>
            <div class="text-gray-400">No tickets yet.</div>
        <?php else: ?>
        <table class="min-w-full text-sm table">
            <thead>
                <tr>
                    <th class="p-3 text-left">Code</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Redeemed At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): $isRedeemed = strtolower($t['status']) === 'redeemed'; ?>
                <tr>
                    <td class="p-3 tracking-widest">#<?php echo htmlspecialchars($t['code']); ?></td>
                    <td class="p-3">
                        <?php if ($isRedeemed): ?>
                            <span class="badge" style="background:#052e16;border-color:#14532d;color:#86efac">Redeemed</span>
                        <?php else: ?>
                            <span class="badge" style="background:#450a0a;border-color:#7f1d1d;color:#fecaca">Not Redeemed</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3"><?php echo htmlspecialchars($t['redeemed_at'] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>



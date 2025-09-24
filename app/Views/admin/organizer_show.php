<?php /** @var array $organizer */ /** @var array $events */ /** @var array $payments */ /** @var array $withdrawals */ /** @var array $summary */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Organizer: <?php echo htmlspecialchars($organizer['full_name']); ?></h1>
        <div class="flex items-center gap-2">
            <a class="btn btn-secondary" href="<?php echo base_url('/admin/organizers'); ?>">Back</a>
            <a class="btn btn-secondary" href="<?php echo base_url('/admin/organizers/edit?id='.(int)$organizer['id']); ?>">Edit</a>
            <form method="post" action="<?php echo base_url('/admin/organizers/toggle'); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$organizer['id']; ?>">
                <input type="hidden" name="is_active" value="<?php echo (int)$organizer['is_active'] ? 0 : 1; ?>">
                <button class="btn btn-primary"><?php echo (int)$organizer['is_active'] ? 'Deactivate' : 'Activate'; ?></button>
            </form>
            <?php if (!(int)$organizer['is_approved']): ?>
            <form method="post" action="<?php echo base_url('/admin/organizers/approve'); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$organizer['id']; ?>">
                <button class="btn btn-primary">Approve</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="grid md:grid-cols-4 gap-6 mb-6">
        <div class="card p-4">
            <div class="text-sm text-gray-400">Email</div>
            <div class="font-semibold"><?php echo htmlspecialchars($organizer['email']); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Phone</div>
            <div class="font-semibold"><?php echo htmlspecialchars($organizer['phone']); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Approved</div>
            <div class="font-semibold"><?php echo (int)$organizer['is_approved'] ? 'Yes' : 'No'; ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Joined</div>
            <div class="font-semibold"><?php echo htmlspecialchars($organizer['created_at'] ?? ''); ?></div>
        </div>
    </div>
    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <div class="card p-4">
            <div class="text-sm text-gray-400">Events</div>
            <div class="text-2xl font-semibold"><?php echo (int)$summary['events_count']; ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Revenue</div>
            <div class="text-2xl font-semibold">KES <?php echo number_format($summary['revenue'], 2); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Commission %</div>
            <form method="post" action="<?php echo base_url('/admin/organizers/commission'); ?>" class="flex items-center gap-2 mt-1">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$organizer['id']; ?>">
                <input class="input" style="width:120px" name="commission_percent" value="<?php echo number_format((float)($organizer['commission_percent'] ?? 0), 2); ?>">
                <button class="btn btn-secondary">Save</button>
            </form>
        </div>
    </div>
    <div class="card p-4 mb-6">
        <h2 class="font-semibold mb-3">Events</h2>
        <form class="mb-3 flex items-center gap-2" method="get" action="">
            <input type="hidden" name="id" value="<?php echo (int)$organizer['id']; ?>">
            <input class="input" placeholder="Search events" name="e_q" value="<?php echo htmlspecialchars($eQ); ?>" style="max-width:240px">
            <button class="btn btn-secondary">Search</button>
        </form>
        <?php if (empty($events)): ?><div class="text-gray-400">No events.</div><?php else: ?>
        <table class="min-w-full text-sm table">
            <thead><tr><th class="p-3 text-left">Title</th><th class="p-3 text-left">Date</th><th class="p-3 text-left">Venue</th></tr></thead>
            <tbody>
                <?php foreach ($events as $e): ?>
                <tr>
                    <td class="p-3"><?php echo htmlspecialchars($e['title']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars(($e['event_date'] ?? '') . ' ' . ($e['event_time'] ?? '')); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($e['venue']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-3 text-sm"><a class="link" href="?id=<?php echo (int)$organizer['id']; ?>&e_page=<?php echo max(1,$ePage-1); ?>&e_q=<?php echo urlencode($eQ); ?>">Prev</a> • <a class="link" href="?id=<?php echo (int)$organizer['id']; ?>&e_page=<?php echo $ePage+1; ?>&e_q=<?php echo urlencode($eQ); ?>">Next</a></div>
        <?php endif; ?>
    </div>
    <div class="card p-4 mb-6">
        <h2 class="font-semibold mb-3">Recent Payments</h2>
        <form class="mb-3 flex items-center gap-2" method="get" action="">
            <input type="hidden" name="id" value="<?php echo (int)$organizer['id']; ?>">
            <input class="input" placeholder="Search payments" name="p_q" value="<?php echo htmlspecialchars($pQ); ?>" style="max-width:240px">
            <button class="btn btn-secondary">Search</button>
        </form>
        <?php if (empty($payments)): ?><div class="text-gray-400">No payments.</div><?php else: ?>
        <table class="min-w-full text-sm table">
            <thead><tr><th class="p-3 text-left">Order</th><th class="p-3 text-left">Provider</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Amount</th><th class="p-3 text-left">Date</th></tr></thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td class="p-3">#<?php echo (int)$p['order_id']; ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['provider']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['status']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['currency']); ?> <?php echo number_format((float)$p['amount'],2); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['created_at'] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-3 text-sm"><a class="link" href="?id=<?php echo (int)$organizer['id']; ?>&p_page=<?php echo max(1,$pPage-1); ?>&p_q=<?php echo urlencode($pQ); ?>">Prev</a> • <a class="link" href="?id=<?php echo (int)$organizer['id']; ?>&p_page=<?php echo $pPage+1; ?>&p_q=<?php echo urlencode($pQ); ?>">Next</a></div>
        <?php endif; ?>
    </div>
    <div class="card p-4">
        <h2 class="font-semibold mb-3">Withdrawals</h2>
        <form class="mb-3 flex items-center gap-2" method="get" action="">
            <input type="hidden" name="id" value="<?php echo (int)$organizer['id']; ?>">
            <input class="input" placeholder="Search status" name="w_q" value="<?php echo htmlspecialchars($wQ); ?>" style="max-width:240px">
            <button class="btn btn-secondary">Search</button>
        </form>
        <?php if (empty($withdrawals)): ?><div class="text-gray-400">No withdrawals.</div><?php else: ?>
        <table class="min-w-full text-sm table">
            <thead><tr><th class="p-3 text-left">Amount</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Requested</th><th class="p-3 text-left">Processed</th></tr></thead>
            <tbody>
                <?php foreach ($withdrawals as $w): ?>
                <tr>
                    <td class="p-3">KES <?php echo number_format((float)$w['amount'], 2); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($w['status']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($w['created_at'] ?? $w['requested_at'] ?? ''); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($w['processed_at'] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-3 text-sm"><a class="link" href="?id=<?php echo (int)$organizer['id']; ?>&w_page=<?php echo max(1,$wPage-1); ?>&w_q=<?php echo urlencode($wQ); ?>">Prev</a> • <a class="link" href="?id=<?php echo (int)$organizer['id']; ?>&w_page=<?php echo $wPage+1; ?>&w_q=<?php echo urlencode($wQ); ?>">Next</a></div>
        <?php endif; ?>
    </div>
</div>



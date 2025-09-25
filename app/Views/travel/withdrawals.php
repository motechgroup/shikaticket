<?php /** @var array $withdrawals */ /** @var array $destinations */ /** @var array $balances */ /** @var float $overallAvailable */ /** @var float $commission */ ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold mb-2">💰 Revenue & Withdrawals</h1>
        <p class="text-gray-400">Manage your travel booking revenue and withdrawal requests</p>
    </div>

    <!-- Revenue Summary -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <h3 class="font-semibold text-blue-300 mb-2">📊 Available Balance</h3>
            <div class="text-2xl font-bold text-green-400">KES <?php echo number_format($overallAvailable, 2); ?></div>
            <div class="text-sm text-gray-400 mt-1">After commission (<?php echo number_format($commission, 1); ?>%)</div>
        </div>
        
        <div class="card p-6">
            <h3 class="font-semibold text-blue-300 mb-2">🎯 Commission Rate</h3>
            <div class="text-2xl font-bold text-blue-400"><?php echo number_format($commission, 1); ?>%</div>
            <div class="text-sm text-gray-400 mt-1">Automatically deducted</div>
        </div>
        
        <div class="card p-6">
            <h3 class="font-semibold text-blue-300 mb-2">📈 Total Destinations</h3>
            <div class="text-2xl font-bold text-purple-400"><?php echo count($destinations); ?></div>
            <div class="text-sm text-gray-400 mt-1">Active destinations</div>
        </div>
    </div>

    <!-- Withdrawal Request Form -->
    <div class="card p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">💸 Request Withdrawal</h2>
        
        <form method="post" action="<?php echo base_url('/travel/withdrawals/request'); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Destination (Optional)</label>
                    <select name="destination_id" class="select">
                        <option value="">All Destinations (Overall)</option>
                        <?php foreach ($destinations as $dest): ?>
                            <?php if ($balances[$dest['id']] > 0): ?>
                            <option value="<?php echo $dest['id']; ?>">
                                <?php echo htmlspecialchars($dest['title']); ?> 
                                (KES <?php echo number_format($balances[$dest['id']], 2); ?>)
                            </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm mb-1">Amount (KES)</label>
                    <input type="number" name="amount" step="0.01" min="0" max="<?php echo $overallAvailable; ?>" 
                           class="input" placeholder="Enter amount to withdraw" required>
                    <div class="text-xs text-gray-400 mt-1">
                        Max: KES <?php echo number_format($overallAvailable, 2); ?>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm mb-1">Notes (Optional)</label>
                <textarea name="notes" class="textarea" rows="3" placeholder="Add any notes for this withdrawal request..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" <?php echo $overallAvailable <= 0 ? 'disabled' : ''; ?>>
                <?php echo $overallAvailable <= 0 ? 'No Available Balance' : 'Request Withdrawal'; ?>
            </button>
        </form>
    </div>

    <!-- Revenue by Destination -->
    <div class="card p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">📊 Revenue by Destination</h2>
        
        <?php if (!empty($destinations)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left">Destination</th>
                        <th class="px-4 py-2 text-left">Gross Revenue</th>
                        <th class="px-4 py-2 text-left">Commission</th>
                        <th class="px-4 py-2 text-left">Withdrawn</th>
                        <th class="px-4 py-2 text-left">Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($destinations as $dest): ?>
                        <?php 
                        $gross = (float)($dest['gross'] ?? 0);
                        $commissionAmount = $gross * ($commission / 100);
                        $withdrawn = (float)(db()->query('SELECT COALESCE(SUM(amount),0) AS wsum FROM withdrawals WHERE travel_agency_id='.(int)$_SESSION['travel_agency_id'].' AND (destination_id='.(int)$dest['id'].' OR destination_id IS NULL) AND status IN ("approved","paid")')->fetch()['wsum'] ?? 0);
                        $available = $balances[$dest['id']];
                        ?>
                        <tr>
                            <td class="px-4 py-2">
                                <div class="font-medium"><?php echo htmlspecialchars($dest['title']); ?></div>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-green-400 font-medium">KES <?php echo number_format($gross, 2); ?></span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-red-400">-KES <?php echo number_format($commissionAmount, 2); ?></span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-orange-400">-KES <?php echo number_format($withdrawn, 2); ?></span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-blue-400 font-bold">KES <?php echo number_format($available, 2); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8">
            <div class="text-gray-400 mb-2">📊</div>
            <p class="text-gray-400">No destinations with revenue yet</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Withdrawal History -->
    <div class="card p-6">
        <h2 class="text-xl font-semibold mb-4">📋 Withdrawal History</h2>
        
        <?php if (!empty($withdrawals)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Destination</th>
                        <th class="px-4 py-2 text-left">Amount</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($withdrawals as $withdrawal): ?>
                    <tr>
                        <td class="px-4 py-2 text-sm">
                            <?php echo date('M j, Y H:i', strtotime($withdrawal['created_at'])); ?>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <?php echo $withdrawal['destination_title'] ? htmlspecialchars($withdrawal['destination_title']) : '<span class="text-gray-400">All Destinations</span>'; ?>
                        </td>
                        <td class="px-4 py-2 text-sm font-medium">
                            KES <?php echo number_format((float)$withdrawal['amount'], 2); ?>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <?php
                            $status = $withdrawal['status'];
                            $badgeClass = match($status) {
                                'paid' => 'bg-green-600',
                                'approved' => 'bg-blue-600',
                                'rejected' => 'bg-red-600',
                                default => 'bg-yellow-600'
                            };
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-400">
                            <?php echo $withdrawal['notes'] ? htmlspecialchars($withdrawal['notes']) : '-'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-8">
            <div class="text-gray-400 mb-2">💸</div>
            <p class="text-gray-400">No withdrawal requests yet</p>
        </div>
        <?php endif; ?>
    </div>
</div>

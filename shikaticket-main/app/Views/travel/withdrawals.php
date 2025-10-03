<?php 
/** @var array $withdrawals */ 
/** @var array $destinations */ 
/** @var array $balances */ 
/** @var float $overallAvailable */ 
/** @var float $commission */
$pageTitle = 'Withdrawals';
$currentPage = 'withdrawals';
?>
<div class="min-h-screen bg-gray-900 flex">
    <?php include __DIR__ . '/shared/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-0">
        <div class="px-4 lg:px-6 py-6 lg:py-8">
            <!-- Header -->
            <div class="mb-6 lg:mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">💰 Revenue & Withdrawals</h1>
                        <p class="text-gray-400">Manage your travel booking revenue and withdrawal requests</p>
                    </div>
                </div>
            </div>

            <!-- Revenue Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-green-600/20 rounded-lg p-2">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl lg:text-3xl font-bold text-white">KES <?php echo number_format($overallAvailable, 2); ?></p>
                        <p class="text-gray-400 text-sm">Available Balance</p>
                        <p class="text-gray-500 text-xs mt-1">After commission (<?php echo number_format($commission, 1); ?>%)</p>
                    </div>
                </div>
                
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-blue-600/20 rounded-lg p-2">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo number_format($commission, 1); ?>%</p>
                        <p class="text-gray-400 text-sm">Commission Rate</p>
                        <p class="text-gray-500 text-xs mt-1">Automatically deducted</p>
                    </div>
                </div>
                
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="bg-purple-600/20 rounded-lg p-2">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo count($destinations); ?></p>
                        <p class="text-gray-400 text-sm">Total Destinations</p>
                        <p class="text-gray-500 text-xs mt-1">Active destinations</p>
                    </div>
                </div>
            </div>

            <!-- Withdrawal Request Form -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6 mb-6 lg:mb-8">
                <h2 class="text-xl font-semibold text-white mb-4">💸 Request Withdrawal</h2>
                
                <form method="post" action="<?php echo base_url('/travel/withdrawals/request'); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Destination (Optional)</label>
                            <select name="destination_id" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
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
                            <label class="block text-sm text-gray-400 mb-2">Amount (KES)</label>
                            <input type="number" name="amount" step="0.01" min="0.01" max="<?php echo $overallAvailable; ?>" required class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                            <p class="text-xs text-gray-500 mt-1">Maximum: KES <?php echo number_format($overallAvailable, 2); ?></p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Bank Details</label>
                        <textarea name="bank_details" rows="3" required class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" placeholder="Bank Name, Account Number, Account Name, etc."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" placeholder="Additional information..."></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            Request Withdrawal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Withdrawal History -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <h2 class="text-xl font-semibold text-white mb-4">📋 Withdrawal History</h2>
                
                <?php if (empty($withdrawals)): ?>
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white mb-2">No withdrawals yet</h3>
                        <p class="text-gray-400">Your withdrawal requests will appear here</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Date</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Amount</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Status</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Destination</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($withdrawals as $w): ?>
                                <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 px-3 text-gray-300"><?php echo date('M j, Y g:i A', strtotime($w['created_at'])); ?></td>
                                    <td class="py-3 px-3 text-gray-300">KES <?php echo number_format($w['amount'], 2); ?></td>
                                    <td class="py-3 px-3">
                                        <?php
                                        $status = $w['status'];
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-900 text-yellow-200',
                                            'approved' => 'bg-green-900 text-green-200',
                                            'rejected' => 'bg-red-900 text-red-200',
                                            'completed' => 'bg-blue-900 text-blue-200'
                                        ];
                                        $statusClass = $statusClasses[$status] ?? 'bg-gray-900 text-gray-200';
                                        ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo htmlspecialchars($w['destination_title'] ?? 'All Destinations'); ?></td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo htmlspecialchars($w['notes'] ?? '-'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>
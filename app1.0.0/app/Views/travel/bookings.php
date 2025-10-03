<?php 
/** @var array $bookings */
$pageTitle = 'Bookings';
$currentPage = 'bookings';
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
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Travel Bookings</h1>
                        <p class="text-gray-400">View and manage customer bookings</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo base_url('/travel/dashboard'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <?php if (empty($bookings)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white mb-2">No bookings yet</h3>
                        <p class="text-gray-400 mb-4">Customer bookings will appear here once they start booking your destinations</p>
                        <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Destination
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Customer</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Destination</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Participants</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Amount</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Status</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Booked</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b): ?>
                                <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 px-3">
                                        <div class="font-medium text-white"><?php echo htmlspecialchars($b['customer_name'] ?? ''); ?></div>
                                        <div class="text-gray-400 text-xs"><?php echo htmlspecialchars($b['customer_email'] ?? ''); ?></div>
                                        <?php if (!empty($b['customer_phone'])): ?>
                                            <div class="text-gray-400 text-xs"><?php echo htmlspecialchars($b['customer_phone']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo htmlspecialchars($b['destination_title'] ?? ''); ?></td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo (int)($b['participants_count'] ?? 1); ?></td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo htmlspecialchars(($b['currency'] ?? 'KES') . ' ' . number_format((float)($b['total_amount'] ?? 0), 2)); ?></td>
                                    <td class="py-3 px-3">
                                        <?php
                                        $status = $b['status'] ?? 'pending';
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-900 text-yellow-200',
                                            'confirmed' => 'bg-green-900 text-green-200',
                                            'cancelled' => 'bg-red-900 text-red-200',
                                            'completed' => 'bg-blue-900 text-blue-200'
                                        ];
                                        $statusClass = $statusClasses[$status] ?? 'bg-gray-900 text-gray-200';
                                        ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo date('M j, Y g:i A', strtotime($b['booking_date'] ?? 'now')); ?></td>
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
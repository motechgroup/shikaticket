<?php 
/** @var array $stats */
/** @var array $destinationStats */
/** @var array $bookingStats */
/** @var string $startDate */
/** @var string $endDate */
$pageTitle = 'Finance Dashboard';
$currentPage = 'finance';
?>

<div class="min-h-screen bg-gray-900 flex">
    <?php include __DIR__ . '/../shared/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-0">
        <div class="px-4 lg:px-6 py-6 lg:py-8">
            <!-- Header -->
            <div class="mb-6 lg:mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Finance Dashboard</h1>
                        <p class="text-gray-400">Track your revenue and booking performance</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Date Range Filter -->
                        <div class="flex items-center gap-2">
                            <input type="date" id="startDate" value="<?php echo htmlspecialchars($startDate); ?>" 
                                   class="px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                            <span class="text-gray-400">to</span>
                            <input type="date" id="endDate" value="<?php echo htmlspecialchars($endDate); ?>" 
                                   class="px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                            <button onclick="updateDateRange()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                                Update
                            </button>
                        </div>
                        
                        <!-- Export Options -->
                        <div class="flex items-center gap-2">
                            <button onclick="exportReport('csv')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export CSV
                            </button>
                            <button onclick="exportReport('pdf')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Bookings -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-blue-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-blue-400"><?php echo number_format($stats['total_bookings']); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-1">Total Bookings</h3>
                    <p class="text-gray-400 text-sm">All bookings in period</p>
                </div>

                <!-- Total Revenue -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-green-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-green-400">KES <?php echo number_format($stats['total_revenue'], 2); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-1">Total Revenue</h3>
                    <p class="text-gray-400 text-sm">Gross revenue from bookings</p>
                </div>

                <!-- Commissions -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-yellow-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-yellow-400">KES <?php echo number_format($stats['total_commissions'], 2); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-1">Commissions</h3>
                    <p class="text-gray-400 text-sm">Platform commissions</p>
                </div>

                <!-- Net Revenue -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-purple-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-purple-400">KES <?php echo number_format($stats['net_revenue'], 2); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-1">Net Revenue</h3>
                    <p class="text-gray-400 text-sm">Revenue after commissions</p>
                </div>
            </div>

            <!-- Revenue Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Summary -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Revenue Summary</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                            <span class="text-gray-300">Total Revenue</span>
                            <span class="text-white font-semibold">KES <?php echo number_format($stats['total_revenue'], 2); ?></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                            <span class="text-gray-300">Platform Commissions</span>
                            <span class="text-red-400 font-semibold">-KES <?php echo number_format($stats['total_commissions'], 2); ?></span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-900/20 border border-green-600/30 rounded-lg">
                            <span class="text-green-300 font-medium">Net Revenue</span>
                            <span class="text-green-400 font-bold">KES <?php echo number_format($stats['net_revenue'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Performance Metrics</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Average Booking Value</span>
                            <span class="text-white font-semibold">
                                KES <?php echo $stats['total_bookings'] > 0 ? number_format($stats['total_revenue'] / $stats['total_bookings'], 2) : '0.00'; ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Commission Rate</span>
                            <span class="text-white font-semibold">
                                <?php echo $stats['total_revenue'] > 0 ? number_format(($stats['total_commissions'] / $stats['total_revenue']) * 100, 1) : '0.0'; ?>%
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Net Margin</span>
                            <span class="text-green-400 font-semibold">
                                <?php echo $stats['total_revenue'] > 0 ? number_format(($stats['net_revenue'] / $stats['total_revenue']) * 100, 1) : '0.0'; ?>%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Destination Performance -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Destination Performance</h3>
                <div class="space-y-3">
                    <?php if (!empty($destinationStats)): ?>
                        <?php foreach ($destinationStats as $destination): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-white font-medium"><?php echo htmlspecialchars($destination['title']); ?></h4>
                                <p class="text-gray-400 text-sm"><?php echo $destination['bookings']; ?> bookings</p>
                            </div>
                            <div class="text-right">
                                <span class="text-green-400 font-semibold">KES <?php echo number_format($destination['revenue'], 2); ?></span>
                                <p class="text-gray-400 text-sm"><?php echo $destination['net_revenue']; ?> net</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <p class="text-gray-400">No destination data available for the selected period</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        window.location.href = `<?php echo base_url('/travel/finance'); ?>?start_date=${startDate}&end_date=${endDate}`;
    }
}

function exportReport(format) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    window.open(`<?php echo base_url('/finance/export'); ?>?type=travel&format=${format}&start_date=${startDate}&end_date=${endDate}`, '_blank');
}
</script>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>

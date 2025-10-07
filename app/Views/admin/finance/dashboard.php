<?php 
/** @var array $stats */
/** @var array $revenueBreakdown */
/** @var array $topEvents */
/** @var array $topDestinations */
/** @var array $commissionStats */
/** @var string $startDate */
/** @var string $endDate */
$pageTitle = 'Finance';
include __DIR__ . '/../../components/mobile_nav_simple.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Finance Dashboard</h1>
                <p class="text-gray-400">Comprehensive financial analytics and revenue reports</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Date Range Filter -->
                <div class="flex items-center gap-2">
                    <input type="date" id="startDate" value="<?php echo htmlspecialchars($startDate); ?>" 
                           class="px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    <span class="text-gray-400">to</span>
                    <input type="date" id="endDate" value="<?php echo htmlspecialchars($endDate); ?>" 
                           class="px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                    <button onclick="updateDateRange()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
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
        <!-- Total Revenue -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-green-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-green-400">KES <?php echo number_format($stats['total_revenue'], 2); ?></span>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">Total Revenue</h3>
            <p class="text-gray-400 text-sm">All revenue sources combined</p>
        </div>

        <!-- Event Revenue -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-blue-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-blue-400">KES <?php echo number_format($stats['event_revenue'], 2); ?></span>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">Event Revenue</h3>
            <p class="text-gray-400 text-sm">Revenue from event ticket sales</p>
        </div>

        <!-- Travel Revenue -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-purple-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-purple-400">KES <?php echo number_format($stats['travel_revenue'], 2); ?></span>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">Travel Revenue</h3>
            <p class="text-gray-400 text-sm">Revenue from travel bookings</p>
        </div>

        <!-- Net Revenue -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-yellow-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-yellow-400">KES <?php echo number_format($stats['net_revenue'], 2); ?></span>
            </div>
            <h3 class="text-lg font-semibold text-white mb-1">Net Revenue</h3>
            <p class="text-gray-400 text-sm">Revenue after commissions</p>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Sources -->
        <div class="card p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Revenue Sources</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                        <span class="text-gray-300">Event Revenue</span>
                    </div>
                    <span class="text-white font-semibold">KES <?php echo number_format($stats['event_revenue'], 2); ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-purple-500 rounded"></div>
                        <span class="text-gray-300">Travel Revenue</span>
                    </div>
                    <span class="text-white font-semibold">KES <?php echo number_format($stats['travel_revenue'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Commission Breakdown -->
        <div class="card p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Commission Breakdown</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span class="text-gray-300">Event Commissions</span>
                    </div>
                    <span class="text-white font-semibold">KES <?php echo number_format($stats['event_commissions'], 2); ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 bg-orange-500 rounded"></div>
                        <span class="text-gray-300">Travel Commissions</span>
                    </div>
                    <span class="text-white font-semibold">KES <?php echo number_format($stats['travel_commissions'], 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Events -->
        <div class="card p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Top Performing Events</h3>
            <div class="space-y-3">
                <?php if (!empty($topEvents)): ?>
                    <?php foreach ($topEvents as $event): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                        <div>
                            <h4 class="text-white font-medium"><?php echo htmlspecialchars($event['title']); ?></h4>
                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($event['organizer_name']); ?></p>
                        </div>
                        <div class="text-right">
                            <span class="text-green-400 font-semibold">KES <?php echo number_format($event['revenue'], 2); ?></span>
                            <p class="text-gray-400 text-sm"><?php echo $event['tickets_sold']; ?> tickets</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-400">No event data available for the selected period</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Destinations -->
        <div class="card p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Top Performing Destinations</h3>
            <div class="space-y-3">
                <?php if (!empty($topDestinations)): ?>
                    <?php foreach ($topDestinations as $destination): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                        <div>
                            <h4 class="text-white font-medium"><?php echo htmlspecialchars($destination['title']); ?></h4>
                            <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($destination['agency_name']); ?></p>
                        </div>
                        <div class="text-right">
                            <span class="text-green-400 font-semibold">KES <?php echo number_format($destination['revenue'], 2); ?></span>
                            <p class="text-gray-400 text-sm"><?php echo $destination['bookings']; ?> bookings</p>
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

<script>
function updateDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        window.location.href = `<?php echo base_url('/admin/finance'); ?>?start_date=${startDate}&end_date=${endDate}`;
    }
}

function exportReport(format) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    window.open(`<?php echo base_url('/finance/export'); ?>?type=admin&format=${format}&start_date=${startDate}&end_date=${endDate}`, '_blank');
}
</script>

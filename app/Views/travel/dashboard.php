<?php
/** @var array $agency */
/** @var int $destinationsCount */
/** @var int $featuredCount */
/** @var array $bookings */
/** @var array $recentBookings */
$pageTitle = 'Travel Agency Dashboard';
?>
<div class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-10">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-white">Welcome, <?php echo htmlspecialchars($agency['company_name']); ?></h1>
        <p class="text-sm md:text-base text-gray-300 mt-2">Manage your travel destinations and bookings</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-dark-card overflow-hidden shadow-lg rounded-lg border border-gray-800">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-300 truncate">Total Destinations</dt>
                            <dd class="text-base md:text-lg font-medium text-white"><?php echo $destinationsCount; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-dark-card overflow-hidden shadow-lg rounded-lg border border-gray-800">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-300 truncate">Featured Destinations</dt>
                            <dd class="text-base md:text-lg font-medium text-white"><?php echo $featuredCount; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-dark-card overflow-hidden shadow-lg rounded-lg border border-gray-800">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-300 truncate">Total Bookings</dt>
                            <dd class="text-base md:text-lg font-medium text-white"><?php echo $bookings['total_bookings']; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-dark-card overflow-hidden shadow-lg rounded-lg border border-gray-800">
            <div class="p-4 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-300 truncate">Pending Bookings</dt>
                            <dd class="text-base md:text-lg font-medium text-white"><?php echo $bookings['pending_bookings']; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
        <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="bg-dark-card overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
            <div class="p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <h3 class="text-base md:text-lg font-medium text-white">Add Destination</h3>
                        <p class="text-sm text-gray-300">Create a new travel destination</p>
                    </div>
                </div>
            </div>
        </a>

        <a href="<?php echo base_url('/travel/destinations'); ?>" class="bg-dark-card overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
            <div class="p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <h3 class="text-base md:text-lg font-medium text-white">Manage Destinations</h3>
                        <p class="text-sm text-gray-300">View and edit your destinations</p>
                    </div>
                </div>
            </div>
        </a>

        <a href="<?php echo base_url('/travel/bookings'); ?>" class="bg-dark-card overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
            <div class="p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <h3 class="text-base md:text-lg font-medium text-white">View Bookings</h3>
                        <p class="text-sm text-gray-300">Manage customer bookings</p>
                    </div>
                </div>
            </div>
        </a>

        <a href="<?php echo base_url('/travel/withdrawals'); ?>" class="bg-dark-card overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
            <div class="p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <h3 class="text-base md:text-lg font-medium text-white">Revenue & Withdrawals</h3>
                        <p class="text-sm text-gray-300">Manage earnings and withdrawal requests</p>
                    </div>
                </div>
            </div>
        </a>

        <a href="<?php echo base_url('/travel/scanner'); ?>" class="bg-dark-card overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow">
            <div class="p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <h3 class="text-base md:text-lg font-medium text-white">Scanner Devices</h3>
                        <p class="text-sm text-gray-300">Manage booking verification scanners</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-dark-card shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-white mb-4">Recent Bookings</h3>
            <?php if (empty($recentBookings)): ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-white">No bookings yet</h3>
                    <p class="mt-1 text-sm text-gray-300">Get started by creating your first destination.</p>
                </div>
            <?php else: ?>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Destination</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Participants</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-dark-card divide-y divide-gray-200">
                            <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                                        <div class="text-sm text-gray-300"><?php echo htmlspecialchars($booking['customer_email']); ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white"><?php echo htmlspecialchars($booking['destination_title']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    <?php echo $booking['participants_count']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    <?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format($booking['total_amount'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                   ($booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- User Dashboard -->
<div class="min-h-screen bg-gray-900">
    <!-- Header Section -->
    <div class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Welcome back, <?php echo htmlspecialchars($user['first_name'] ?? 'User'); ?>! 👋</h1>
                    <p class="text-gray-400">Here's your activity overview and quick actions</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?php echo base_url('/events'); ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Browse Events
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Pending Payment Alert -->
        <?php if (!empty($pendingBooking)): ?>
        <div class="mb-6 p-4 bg-amber-900/50 border border-amber-600/50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="text-2xl">💳</div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-amber-200 mb-1">M-Pesa Payment Pending</h3>
                    <p class="text-amber-100 text-sm mb-3">Complete your payment on your phone by entering your PIN to confirm your travel booking.</p>
                    <a href="<?php echo base_url('/travel/checkout?booking_id=' . $pendingBooking['id']); ?>" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Complete Payment
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
            <!-- Total Orders -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-blue-600/20 rounded-lg p-2">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo number_format($orderStats['total_orders'] ?? 0); ?></p>
                    <p class="text-gray-400 text-sm">Event Orders</p>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-green-600/20 rounded-lg p-2">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-bold text-white">KES <?php echo number_format($orderStats['total_spent'] ?? 0); ?></p>
                    <p class="text-gray-400 text-sm">Total Spent</p>
                </div>
            </div>

            <!-- Total Tickets -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-purple-600/20 rounded-lg p-2">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo number_format($ticketStats['total_tickets'] ?? 0); ?></p>
                    <p class="text-gray-400 text-sm"><?php echo number_format($ticketStats['redeemed_tickets'] ?? 0); ?> used</p>
                </div>
            </div>

            <!-- Travel Bookings -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-cyan-600/20 rounded-lg p-2">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo number_format($travelStats['total_bookings'] ?? 0); ?></p>
                    <p class="text-gray-400 text-sm">KES <?php echo number_format($travelStats['total_travel_spent'] ?? 0); ?> spent</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-6">
            <a href="<?php echo base_url('/user/orders'); ?>" class="group bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-blue-500/50 rounded-lg p-4 lg:p-6 transition-all duration-200">
                <div class="flex items-center gap-3 lg:gap-4">
                    <div class="bg-blue-600/20 group-hover:bg-blue-600/30 rounded-lg p-2 lg:p-3 transition-colors">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base lg:text-lg font-semibold text-white mb-1">My Orders</h3>
                        <p class="text-gray-400 text-xs lg:text-sm">View your event ticket purchases</p>
                    </div>
                </div>
            </a>

            <a href="<?php echo base_url('/user/travel-bookings'); ?>" class="group bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-green-500/50 rounded-lg p-4 lg:p-6 transition-all duration-200">
                <div class="flex items-center gap-3 lg:gap-4">
                    <div class="bg-green-600/20 group-hover:bg-green-600/30 rounded-lg p-2 lg:p-3 transition-colors">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base lg:text-lg font-semibold text-white mb-1">Travel Bookings</h3>
                        <p class="text-gray-400 text-xs lg:text-sm">Manage your travel reservations</p>
                    </div>
                </div>
            </a>

            <a href="<?php echo base_url('/user/account'); ?>" class="group bg-gray-800 hover:bg-gray-700 border border-gray-700 hover:border-purple-500/50 rounded-lg p-4 lg:p-6 transition-all duration-200">
                <div class="flex items-center gap-3 lg:gap-4">
                    <div class="bg-purple-600/20 group-hover:bg-purple-600/30 rounded-lg p-2 lg:p-3 transition-colors">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base lg:text-lg font-semibold text-white mb-1">Account Settings</h3>
                        <p class="text-gray-400 text-xs lg:text-sm">Update your profile information</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Recent Orders -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <h2 class="text-lg lg:text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Recent Orders
                    </h2>
                    <a href="<?php echo base_url('/user/orders'); ?>" class="text-blue-400 hover:text-blue-300 text-sm font-medium">View All</a>
                </div>
                
                <?php if (!empty($recentOrders)): ?>
                <div class="space-y-3">
                    <?php foreach ($recentOrders as $order): ?>
                    <div class="bg-gray-700/50 rounded-lg p-3 lg:p-4 border border-gray-600">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-white text-sm lg:text-base"><?php echo htmlspecialchars($order['event_title']); ?></h3>
                            <span class="text-green-400 text-xs lg:text-sm font-medium">KES <?php echo number_format($order['total_amount']); ?></span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-xs lg:text-sm text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo date('M j, Y', strtotime($order['event_date'])); ?>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <?php echo htmlspecialchars($order['venue']); ?>
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Ordered on <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p class="text-gray-400 mb-4">No orders yet</p>
                    <a href="<?php echo base_url('/events'); ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Browse Events
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Recent Travel Bookings -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <h2 class="text-lg lg:text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Recent Travel
                    </h2>
                    <a href="<?php echo base_url('/user/travel-bookings'); ?>" class="text-green-400 hover:text-green-300 text-sm font-medium">View All</a>
                </div>
                
                <?php if (!empty($recentBookings)): ?>
                <div class="space-y-3">
                    <?php foreach ($recentBookings as $booking): ?>
                    <div class="bg-gray-700/50 rounded-lg p-3 lg:p-4 border border-gray-600">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-white text-sm lg:text-base"><?php echo htmlspecialchars($booking['destination_title']); ?></h3>
                            <span class="text-green-400 text-xs lg:text-sm font-medium"><?php echo $booking['currency']; ?> <?php echo number_format($booking['total_amount']); ?></span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-xs lg:text-sm text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo date('M j, Y', strtotime($booking['departure_date'])); ?>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <?php echo htmlspecialchars($booking['company_name']); ?>
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <?php echo $booking['participants_count']; ?> participant<?php echo $booking['participants_count'] > 1 ? 's' : ''; ?> • Booked <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-400 mb-4">No travel bookings yet</p>
                    <a href="<?php echo base_url('/travel'); ?>" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Browse Travel
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

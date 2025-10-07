<?php
/** @var array $agency */
/** @var int $destinationsCount */
/** @var int $featuredCount */
/** @var array $bookings */
/** @var array $recentBookings */
$pageTitle = 'Travel Agency Dashboard';
?>
<div class="min-h-screen bg-gray-900 flex">
    <!-- Mobile Header -->
    <div class="lg:hidden bg-gray-800 border-b border-gray-700 px-4 py-3 flex items-center justify-between fixed top-0 left-0 right-0 z-30 h-16">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-white">Travel Portal</h2>
                <p class="text-xs text-gray-400">Agency Dashboard</p>
            </div>
        </div>
        <button id="mobileMenuToggle" class="text-gray-300 hover:text-white focus:outline-none focus:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="mobileSidebarOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-72 bg-gray-800 border-r border-gray-700 z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-300 ease-in-out shadow-2xl">
        <div class="h-full flex flex-col bg-gray-800">
            <!-- Sidebar Header -->
            <div class="px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Travel Portal</h2>
                            <p class="text-xs text-gray-400">Agency Dashboard</p>
                        </div>
                    </div>
                    <!-- Mobile Close Button -->
                    <button id="mobileSidebarClose" class="lg:hidden text-gray-300 hover:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Welcome Message -->
                <?php if (isset($_SESSION['travel_agency_id'])): ?>
                <div class="mt-4 px-3 py-2 bg-gray-700/50 rounded-lg">
                    <div class="text-sm text-gray-300">Welcome,</div>
                    <div class="text-white font-medium"><?php echo htmlspecialchars($_SESSION['travel_agency_name'] ?? 'Agency'); ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <!-- Dashboard -->
                <a href="<?php echo base_url('/travel/dashboard'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-red-600 text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Destinations Section -->
                <div class="pt-4">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Destinations</h3>
                    </div>
                    <a href="<?php echo base_url('/travel/destinations'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>All Destinations</span>
                    </a>
                    <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Create Destination</span>
                    </a>
                </div>

                <!-- Bookings Section -->
                <div class="pt-4">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bookings</h3>
                    </div>
                    <a href="<?php echo base_url('/travel/bookings'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>View Bookings</span>
                    </a>
                </div>

                <!-- Revenue Section -->
                <div class="pt-4">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue</h3>
                    </div>
                    <a href="<?php echo base_url('/travel/withdrawals'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span>Withdrawals</span>
                    </a>
                </div>

                <!-- Tools Section -->
                <div class="pt-4">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tools</h3>
                    </div>
                    <a href="<?php echo base_url('/travel/scanner'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <span>Scanner Devices</span>
                    </a>
                    <a href="<?php echo base_url('/travel/finance'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Finance</span>
                    </a>
                </div>

                <!-- Account Section -->
                <div class="pt-4 border-t border-gray-700">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</h3>
                    </div>
                    <a href="<?php echo base_url('/travel/profile'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profile Settings</span>
                    </a>
                    <form method="POST" action="<?php echo base_url('/travel/logout'); ?>" class="block">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-0">
        <div class="px-4 lg:px-6 py-6 lg:py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Welcome back, <?php echo htmlspecialchars($agency['company_name']); ?>! 👋</h1>
                        <p class="text-gray-400 text-lg">Here's your travel agency overview and quick actions</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Destination
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enhanced Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <!-- Total Destinations -->
                <div class="bg-gray-800 rounded-xl p-4 lg:p-6 border border-gray-700 hover:border-red-500/50 transition-colors">
                    <div class="flex items-center justify-between mb-3 lg:mb-4">
                        <div class="bg-red-500/20 rounded-lg p-2 lg:p-3">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo $destinationsCount; ?></p>
                            <p class="text-xs lg:text-sm text-gray-400">Total</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm lg:text-lg font-semibold text-white mb-1">Destinations</h3>
                        <p class="text-gray-400 text-xs lg:text-sm">Active travel listings</p>
                    </div>
                </div>

                <!-- Featured Destinations -->
                <div class="bg-gray-800 rounded-xl p-4 lg:p-6 border border-gray-700 hover:border-yellow-500/50 transition-colors">
                    <div class="flex items-center justify-between mb-3 lg:mb-4">
                        <div class="bg-yellow-500/20 rounded-lg p-2 lg:p-3">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo $featuredCount; ?></p>
                            <p class="text-xs lg:text-sm text-gray-400">Featured</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm lg:text-lg font-semibold text-white mb-1">Premium Listings</h3>
                        <p class="text-gray-400 text-xs lg:text-sm">Highlighted destinations</p>
                    </div>
                </div>

                <!-- Total Bookings -->
                <div class="bg-gray-800 rounded-xl p-4 lg:p-6 border border-gray-700 hover:border-blue-500/50 transition-colors">
                    <div class="flex items-center justify-between mb-3 lg:mb-4">
                        <div class="bg-blue-500/20 rounded-lg p-2 lg:p-3">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo $bookings['total_bookings']; ?></p>
                            <p class="text-xs lg:text-sm text-gray-400">Total</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm lg:text-lg font-semibold text-white mb-1">Bookings</h3>
                        <p class="text-gray-400 text-xs lg:text-sm">All time reservations</p>
                    </div>
                </div>

                <!-- Pending Bookings -->
                <div class="bg-gray-800 rounded-xl p-4 lg:p-6 border border-gray-700 hover:border-orange-500/50 transition-colors">
                    <div class="flex items-center justify-between mb-3 lg:mb-4">
                        <div class="bg-orange-500/20 rounded-lg p-2 lg:p-3">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl lg:text-3xl font-bold text-white"><?php echo $bookings['pending_bookings']; ?></p>
                            <p class="text-xs lg:text-sm text-gray-400">Pending</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm lg:text-lg font-semibold text-white mb-1">Awaiting Confirmation</h3>
                        <p class="text-gray-400 text-xs lg:text-sm">Need your attention</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <!-- Add Destination -->
                <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="group bg-gray-800 hover:bg-gray-700 rounded-xl p-4 lg:p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-200">
                    <div class="flex items-center gap-3 lg:gap-4">
                        <div class="bg-green-500/20 group-hover:bg-green-500/30 rounded-lg p-2 lg:p-3 transition-colors">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base lg:text-lg font-semibold text-white mb-1">Add Destination</h3>
                            <p class="text-gray-400 text-xs lg:text-sm">Create a new travel destination</p>
                        </div>
                    </div>
                </a>

                <!-- Manage Destinations -->
                <a href="<?php echo base_url('/travel/destinations'); ?>" class="group bg-gray-800 hover:bg-gray-700 rounded-xl p-4 lg:p-6 border border-gray-700 hover:border-red-500/50 transition-all duration-200">
                    <div class="flex items-center gap-3 lg:gap-4">
                        <div class="bg-red-500/20 group-hover:bg-red-500/30 rounded-lg p-2 lg:p-3 transition-colors">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base lg:text-lg font-semibold text-white mb-1">Manage Destinations</h3>
                            <p class="text-gray-400 text-xs lg:text-sm">View and edit your destinations</p>
                        </div>
                    </div>
                </a>

                <!-- View Bookings -->
                <a href="<?php echo base_url('/travel/bookings'); ?>" class="group bg-gray-800 hover:bg-gray-700 rounded-xl p-4 lg:p-6 border border-gray-700 hover:border-purple-500/50 transition-all duration-200">
                    <div class="flex items-center gap-3 lg:gap-4">
                        <div class="bg-purple-500/20 group-hover:bg-purple-500/30 rounded-lg p-2 lg:p-3 transition-colors">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base lg:text-lg font-semibold text-white mb-1">View Bookings</h3>
                            <p class="text-gray-400 text-xs lg:text-sm">Manage customer bookings</p>
                        </div>
                    </div>
                </a>

                <!-- Revenue & Withdrawals -->
                <a href="<?php echo base_url('/travel/withdrawals'); ?>" class="group bg-gray-800 hover:bg-gray-700 rounded-xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-200">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-500/20 group-hover:bg-green-500/30 rounded-lg p-3 transition-colors">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-1">Revenue & Withdrawals</h3>
                            <p class="text-gray-400 text-sm">Manage earnings and withdrawal requests</p>
                        </div>
                    </div>
                </a>

                <!-- Scanner Devices -->
                <a href="<?php echo base_url('/travel/scanner'); ?>" class="group bg-gray-800 hover:bg-gray-700 rounded-xl p-6 border border-gray-700 hover:border-indigo-500/50 transition-all duration-200">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-500/20 group-hover:bg-indigo-500/30 rounded-lg p-3 transition-colors">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-1">Scanner Devices</h3>
                            <p class="text-gray-400 text-sm">Manage booking verification scanners</p>
                        </div>
                    </div>
                </a>

                <!-- Universal Scanner (public link, only linked here) -->
                <a href="<?php echo base_url('/scanner'); ?>" target="_blank" rel="noopener" class="group bg-gray-800 hover:bg-gray-700 rounded-xl p-6 border border-gray-700 hover:border-cyan-500/50 transition-all duration-200">
                    <div class="flex items-center gap-4">
                        <div class="bg-cyan-500/20 group-hover:bg-cyan-500/30 rounded-lg p-3 transition-colors">
                            <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-1">Open Universal Scanner</h3>
                            <p class="text-gray-400 text-sm">Verify bookings with device code</p>
                        </div>
                    </div>
                </a>

                <!-- Profile -->
                <a href="<?php echo base_url('/travel/profile'); ?>" class="group bg-gray-800 hover:bg-gray-700 rounded-xl p-6 border border-gray-700 hover:border-gray-500/50 transition-all duration-200">
                    <div class="flex items-center gap-4">
                        <div class="bg-gray-500/20 group-hover:bg-gray-500/30 rounded-lg p-3 transition-colors">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-1">Profile Settings</h3>
                            <p class="text-gray-400 text-sm">Update your agency information</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-gray-800 rounded-xl border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Recent Bookings
                        </h3>
                        <a href="<?php echo base_url('/travel/bookings'); ?>" class="text-blue-400 hover:text-blue-300 text-sm font-medium">View All</a>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php if (empty($recentBookings)): ?>
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-white mb-2">No bookings yet</h3>
                            <p class="text-gray-400 mb-6">Get started by creating your first destination and attracting customers.</p>
                            <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Destination
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Destination</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Participants</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    <?php foreach ($recentBookings as $booking): ?>
                                    <tr class="hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                                                <div class="text-sm text-gray-400"><?php echo htmlspecialchars($booking['customer_email']); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($booking['destination_title']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <?php echo $booking['participants_count']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                            <?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format($booking['total_amount'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                <?php echo $booking['status'] === 'confirmed' ? 'bg-green-900 text-green-200' : 
                                                           ($booking['status'] === 'pending' ? 'bg-yellow-900 text-yellow-200' : 'bg-gray-900 text-gray-200'); ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
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
    </div>
</div>

<script>
    // Mobile sidebar functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileSidebarOverlay');
        const closeButton = document.getElementById('mobileSidebarClose');
        
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Prevent body scroll
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
        }
        
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            // Restore body scroll
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
        }
        
        // Open sidebar
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openSidebar();
            });
        }
        
        // Close sidebar
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
        
        if (closeButton) {
            closeButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeSidebar();
            });
        }
        
        // Close sidebar when clicking on navigation links
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                setTimeout(closeSidebar, 150);
            });
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                closeSidebar();
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) { // lg breakpoint
                closeSidebar();
            }
        });
        
        // Prevent sidebar from hanging by ensuring it's properly hidden on page load
        if (window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
        }
    });
</script>
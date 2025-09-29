<?php
// Shared sidebar template for travel portal pages
$currentPage = $currentPage ?? '';
?>

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
            <a href="<?php echo base_url('/travel/dashboard'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'dashboard' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Destinations Section -->
            <div class="space-y-1">
                <div class="px-3 py-1">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Destinations</h3>
                </div>
                <a href="<?php echo base_url('/travel/destinations'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'destinations' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>All Destinations</span>
                </a>
                <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'create-destination' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Create Destination</span>
                </a>
            </div>

            <!-- Bookings Section -->
            <div class="space-y-1">
                <div class="px-3 py-1">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bookings</h3>
                </div>
                <a href="<?php echo base_url('/travel/bookings'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'bookings' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>View Bookings</span>
                </a>
            </div>

            <!-- Revenue Section -->
            <div class="space-y-1">
                <div class="px-3 py-1">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue</h3>
                </div>
                <a href="<?php echo base_url('/travel/withdrawals'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'withdrawals' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <span>Withdrawals</span>
                </a>
            </div>

            <!-- Tools Section -->
            <div class="space-y-1">
                <div class="px-3 py-1">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tools</h3>
                </div>
                <a href="<?php echo base_url('/travel/scanner'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'scanner' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Scanner Devices</span>
                </a>
                <a href="<?php echo base_url('/travel/finance'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'finance' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Finance</span>
                </a>
                <!-- Marketing links removed -->
            </div>

            <!-- Account Section -->
            <div class="pt-4 border-t border-gray-700">
                <div class="px-3 py-1">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</h3>
                </div>
                <a href="<?php echo base_url('/travel/profile'); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo $currentPage === 'profile' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?> transition-colors">
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

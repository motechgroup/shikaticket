<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Travel Agency Portal'; ?> - ShikaTicket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#dc2626',
                        secondary: '#374151',
                        brand: { red: '#ef4444', red600: '#dc2626' },
                        dark: { bg: '#0b0b0b', card: '#111111', mute: '#9ca3af' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-bg text-white">
    <!-- Navigation -->
    <nav class="bg-dark-card shadow-lg border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo base_url('/travel/dashboard'); ?>" class="flex items-center">
                        <svg class="h-6 w-6 md:h-8 md:w-8 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-lg md:text-xl font-bold text-white">Travel Portal</span>
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-300 hover:text-white focus:outline-none focus:text-white" id="mobile-menu-button">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Desktop navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if (isset($_SESSION['travel_agency_id'])): ?>
                        <span class="text-sm text-gray-300">Welcome, <?php echo htmlspecialchars($_SESSION['travel_agency_name'] ?? ''); ?></span>
                        <a href="<?php echo base_url('/travel/dashboard'); ?>" class="text-gray-300 hover:text-white">Dashboard</a>
                        <a href="<?php echo base_url('/travel/destinations'); ?>" class="text-gray-300 hover:text-white">Destinations</a>
                        <a href="<?php echo base_url('/travel/bookings'); ?>" class="text-gray-300 hover:text-white">Bookings</a>
                        <a href="<?php echo base_url('/travel/profile'); ?>" class="text-gray-300 hover:text-white">Profile</a>
                        <form method="POST" action="<?php echo base_url('/travel/logout'); ?>" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <button type="submit" class="text-gray-300 hover:text-white">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo base_url('/travel/login'); ?>" class="text-gray-300 hover:text-white">Login</a>
                        <a href="<?php echo base_url('/travel/register'); ?>" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mobile navigation menu -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-dark-card border-t border-gray-800">
                    <?php if (isset($_SESSION['travel_agency_id'])): ?>
                        <div class="px-3 py-2 text-sm text-gray-300">Welcome, <?php echo htmlspecialchars($_SESSION['travel_agency_name'] ?? ''); ?></div>
                        <a href="<?php echo base_url('/travel/dashboard'); ?>" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-md">Dashboard</a>
                        <a href="<?php echo base_url('/travel/destinations'); ?>" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-md">Destinations</a>
                        <a href="<?php echo base_url('/travel/bookings'); ?>" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-md">Bookings</a>
                        <a href="<?php echo base_url('/travel/profile'); ?>" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-md">Profile</a>
                        <form method="POST" action="<?php echo base_url('/travel/logout'); ?>" class="block">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <button type="submit" class="block w-full text-left px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-md">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo base_url('/travel/login'); ?>" class="block px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 rounded-md">Login</a>
                        <a href="<?php echo base_url('/travel/register'); ?>" class="block px-3 py-2 bg-red-600 text-white hover:bg-red-700 rounded-md">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (flash_get('success')): ?>
        <div class="bg-green-900 border border-green-600 text-green-300 px-4 py-3 mx-4 mt-4 rounded">
            <?php echo htmlspecialchars(flash_get('success')); ?>
        </div>
    <?php endif; ?>

    <?php if (flash_get('error')): ?>
        <div class="bg-red-900 border border-red-600 text-red-300 px-4 py-3 mx-4 mt-4 rounded">
            <?php echo htmlspecialchars(flash_get('error')); ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark-card border-t border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-400 text-sm">
                <p>&copy; 2025 ShikaTicket Travel Portal. All rights reserved.</p>
                <p class="mt-2">
                    <a href="<?php echo base_url('/'); ?>" class="text-red-400 hover:text-red-300">← Back to main site</a>
                </p>
            </div>
        </div>
    </footer>
    
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>

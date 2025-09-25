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
                        secondary: '#374151'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo base_url('/travel/dashboard'); ?>" class="flex items-center">
                        <svg class="h-8 w-8 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-xl font-bold text-gray-900">Travel Portal</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['travel_agency_id'])): ?>
                        <span class="text-sm text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['travel_agency_name'] ?? ''); ?></span>
                        <a href="<?php echo base_url('/travel/dashboard'); ?>" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        <a href="<?php echo base_url('/travel/destinations'); ?>" class="text-gray-600 hover:text-gray-900">Destinations</a>
                        <a href="<?php echo base_url('/travel/bookings'); ?>" class="text-gray-600 hover:text-gray-900">Bookings</a>
                        <a href="<?php echo base_url('/travel/profile'); ?>" class="text-gray-600 hover:text-gray-900">Profile</a>
                        <form method="POST" action="<?php echo base_url('/travel/logout'); ?>" class="inline">
                            <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo base_url('/travel/login'); ?>" class="text-gray-600 hover:text-gray-900">Login</a>
                        <a href="<?php echo base_url('/travel/register'); ?>" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (flash_get('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-4 mt-4 rounded">
            <?php echo htmlspecialchars(flash_get('success')); ?>
        </div>
    <?php endif; ?>

    <?php if (flash_get('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded">
            <?php echo htmlspecialchars(flash_get('error')); ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; 2025 ShikaTicket Travel Portal. All rights reserved.</p>
                <p class="mt-2">
                    <a href="<?php echo base_url('/'); ?>" class="text-red-600 hover:text-red-700">← Back to main site</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>

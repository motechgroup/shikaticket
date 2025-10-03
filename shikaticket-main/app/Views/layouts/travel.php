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
    <!-- Navigation removed - integrated into sidebar -->

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
    
    <!-- Mobile menu script removed -->
</body>
</html>

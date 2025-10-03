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
    <?php 
    $successMsg = flash_get('success');
    $errorMsg = flash_get('error');
    $warningMsg = flash_get('warning');
    ?>
    
    <?php if ($successMsg): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 px-6 py-4 mx-4 mt-4 rounded-r shadow-lg">
            <div class="flex items-center">
                <svg class="h-6 w-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <strong><?php echo htmlspecialchars($successMsg); ?></strong>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-800 px-6 py-4 mx-4 mt-4 rounded-r shadow-lg">
            <div class="flex items-center">
                <svg class="h-6 w-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <strong><?php echo htmlspecialchars($errorMsg); ?></strong>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($warningMsg): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 px-6 py-4 mx-4 mt-4 rounded-r shadow-lg">
            <div class="flex items-center">
                <svg class="h-6 w-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <strong><?php echo htmlspecialchars($warningMsg); ?></strong>
            </div>
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

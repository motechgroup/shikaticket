<?php
/** @var array $devices */
$pageTitle = 'Scanner Devices';
$currentPage = 'scanner';
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
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Scanner Devices</h1>
                        <p class="text-gray-400">Manage your travel booking scanner devices</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo base_url('/travel/scanner/create'); ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Scanner Device
                        </a>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mb-6 p-4 bg-blue-900/50 border border-blue-600/50 rounded-lg">
                <h3 class="font-semibold text-blue-300 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Scanner Usage Instructions
                </h3>
                <ul class="text-sm text-blue-200 space-y-1">
                    <li>• <strong>Login to Scanner:</strong> Opens the universal scanner login with device code pre-filled</li>
                    <li>• <strong>Direct Scan:</strong> Direct access to scanner interface (requires device authentication)</li>
                    <li>• <strong>Device Code:</strong> Share this code with your scanning staff for login</li>
                    <li>• <strong>Universal Scanner:</strong> Can scan both event tickets and travel booking references</li>
                </ul>
            </div>

            <!-- Content -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <?php if (empty($devices)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white mb-2">No scanner devices yet</h3>
                        <p class="text-gray-400 mb-4">Create your first scanner device to start verifying travel bookings</p>
                        <a href="<?php echo base_url('/travel/scanner/create'); ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Your First Device
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                        <?php foreach ($devices as $device): ?>
                            <div class="bg-gray-700/50 border border-gray-600 rounded-lg p-4 lg:p-6 hover:border-gray-500 transition-colors">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-red-600/20 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-white text-base lg:text-lg"><?php echo htmlspecialchars($device['device_name'] ?? 'Unnamed Device'); ?></h3>
                                            <p class="text-sm text-gray-400">Scanner Device</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo ($device['is_active'] ?? false) ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200'; ?>">
                                        <?php echo ($device['is_active'] ?? false) ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Device Code:</span>
                                        <span class="text-white font-mono bg-gray-800 px-2 py-1 rounded"><?php echo htmlspecialchars($device['device_code'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Device ID:</span>
                                        <span class="text-gray-300 font-mono text-xs"><?php echo htmlspecialchars($device['id'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Created:</span>
                                        <span class="text-gray-300"><?php echo date('M j, Y', strtotime($device['created_at'] ?? 'now')); ?></span>
                                    </div>
                                    <?php if (!empty($device['last_used_at'])): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Last Used:</span>
                                        <span class="text-gray-300"><?php echo date('M j, Y g:i A', strtotime($device['last_used_at'] ?? 'now')); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="<?php echo base_url('/scanner/login?device_code=' . urlencode($device['device_code'] ?? '')); ?>" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        Login to Scanner
                                    </a>
                                    <a href="<?php echo base_url('/travel/scanner/scan?device_id=' . ($device['id'] ?? '')); ?>" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                        Direct Scan
                                    </a>
                                    <a href="<?php echo base_url('/travel/scanner/edit?id=' . ($device['id'] ?? '')); ?>" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>
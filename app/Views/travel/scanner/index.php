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

            <?php 
            $androidUrl = \App\Models\Setting::get('apps.android_scanner_url', '');
            $iosUrl = \App\Models\Setting::get('apps.ios_scanner_url', '');
            $appstoreImg = base_url('/uploads/assets/appstore.png');
            $playstoreImg = base_url('/uploads/assets/playstore.png');
            ?>
            <div class="mb-6 p-4 bg-gray-800/70 border border-gray-700 rounded-lg">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="font-semibold text-white mb-1">Get the Mobile Scanner App</h3>
                        <p class="text-sm text-gray-400">Install our Android or iOS scanner for faster check-ins.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <?php if ($iosUrl): ?>
                            <a href="<?php echo htmlspecialchars($iosUrl); ?>" target="_blank" rel="noopener" class="inline-block">
                                <img src="<?php echo $appstoreImg; ?>" alt="Download on the App Store" class="h-10">
                            </a>
                        <?php else: ?>
                            <span title="Coming soon" class="inline-block opacity-60 cursor-not-allowed">
                                <img src="<?php echo $appstoreImg; ?>" alt="App Store (coming soon)" class="h-10">
                            </span>
                        <?php endif; ?>

                        <?php if ($androidUrl): ?>
                            <a href="<?php echo htmlspecialchars($androidUrl); ?>" target="_blank" rel="noopener" class="inline-block">
                                <img src="<?php echo $playstoreImg; ?>" alt="Get it on Google Play" class="h-10">
                            </a>
                        <?php else: ?>
                            <span title="Coming soon" class="inline-block opacity-60 cursor-not-allowed">
                                <img src="<?php echo $playstoreImg; ?>" alt="Google Play (coming soon)" class="h-10">
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <!-- CSRF Token for JavaScript -->
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
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
                                    <button onclick="confirmDeleteScanner(<?php echo $device['id']; ?>, '<?php echo htmlspecialchars($device['device_name'] ?? 'Unnamed Device'); ?>')" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Delete Scanner Device</h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 bg-red-600/20 rounded-lg flex items-center justify-center mr-3">
                    <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-white font-medium" id="deleteScannerName">Scanner Device</h4>
                    <p class="text-gray-400 text-sm">This action cannot be undone</p>
                </div>
            </div>
            
            <div class="bg-yellow-900/20 border border-yellow-600/50 rounded-lg p-3 mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="text-yellow-200 font-medium mb-1">Important:</p>
                        <ul class="text-yellow-200/80 space-y-1">
                            <li>• If this scanner has been used to verify bookings, it will be <strong>deactivated</strong> (scan history preserved)</li>
                            <li>• If no bookings have been scanned, it will be <strong>permanently deleted</strong></li>
                            <li>• All destination assignments will be removed</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <p class="text-gray-300 text-sm">Are you sure you want to delete this scanner device?</p>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                Cancel
            </button>
            <button onclick="deleteScanner()" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                Delete Scanner
            </button>
        </div>
    </div>
</div>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>

<script>
let deleteScannerId = null;

function confirmDeleteScanner(scannerId, scannerName) {
    deleteScannerId = scannerId;
    document.getElementById('deleteScannerName').textContent = scannerName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteScannerId = null;
}

function deleteScanner() {
    if (!deleteScannerId) return;
    
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
    
    fetch('<?php echo base_url('/travel/scanner/delete'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `scanner_id=${deleteScannerId}&csrf_token=${csrfToken}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message);
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete scanner'));
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('Error: ' + error.message);
    })
    .finally(() => {
        closeDeleteModal();
    });
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
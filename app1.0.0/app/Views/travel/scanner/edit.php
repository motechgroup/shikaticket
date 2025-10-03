<?php
/** @var array $device */
$pageTitle = 'Edit Scanner Device';
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
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Edit Scanner Device</h1>
                        <p class="text-gray-400">Update your scanner device settings</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo base_url('/travel/scanner'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Devices
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <form method="POST" action="<?php echo base_url('/travel/scanner/edit'); ?>" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="device_id" value="<?php echo htmlspecialchars($device['id']); ?>">
                    
                    <!-- Device Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Device Name -->
                        <div>
                            <label for="device_name" class="block text-sm font-medium text-gray-300 mb-2">
                                Device Name
                            </label>
                            <input type="text" id="device_name" name="device_name" 
                                   value="<?php echo htmlspecialchars($device['device_name']); ?>"
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                   placeholder="Enter device name" required>
                        </div>
                        
                        <!-- Device Code (Read-only) -->
                        <div>
                            <label for="device_code" class="block text-sm font-medium text-gray-300 mb-2">
                                Device Code
                            </label>
                            <input type="text" id="device_code" 
                                   value="<?php echo htmlspecialchars($device['device_code']); ?>"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-gray-400 font-mono"
                                   readonly>
                            <p class="text-xs text-gray-500 mt-1">This code is used for scanner login and cannot be changed</p>
                        </div>
                    </div>
                    
                    <!-- Device Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Device Status
                        </label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="1" 
                                       <?php echo $device['is_active'] ? 'checked' : ''; ?>
                                       class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 focus:ring-red-500">
                                <span class="text-sm text-gray-300">Active</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="0" 
                                       <?php echo !$device['is_active'] ? 'checked' : ''; ?>
                                       class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 focus:ring-red-500">
                                <span class="text-sm text-gray-300">Inactive</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Inactive devices cannot be used for scanning</p>
                    </div>
                    
                    <!-- Device Information Display -->
                    <div class="bg-gray-700/50 border border-gray-600 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-300 mb-3">Device Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-400">Device ID:</span>
                                <span class="text-white font-mono"><?php echo htmlspecialchars($device['id']); ?></span>
                            </div>
                            <div>
                                <span class="text-gray-400">Created:</span>
                                <span class="text-gray-300"><?php echo date('M j, Y g:i A', strtotime($device['created_at'])); ?></span>
                            </div>
                            <?php if (!empty($device['updated_at'])): ?>
                            <div>
                                <span class="text-gray-400">Last Updated:</span>
                                <span class="text-gray-300"><?php echo date('M j, Y g:i A', strtotime($device['updated_at'])); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($device['last_used_at'])): ?>
                            <div>
                                <span class="text-gray-400">Last Used:</span>
                                <span class="text-gray-300"><?php echo date('M j, Y g:i A', strtotime($device['last_used_at'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Device
                        </button>
                        <a href="<?php echo base_url('/travel/scanner'); ?>" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>

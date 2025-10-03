<?php 
/** @var array|null $dest */
$pageTitle = isset($dest) ? 'Edit Destination' : 'Create Destination';
$currentPage = 'create-destination';
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
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2"><?php echo htmlspecialchars($pageTitle); ?></h1>
                        <p class="text-gray-400"><?php echo isset($dest) ? 'Update destination information' : 'Create a new travel destination'; ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo base_url('/travel/destinations'); ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Destinations
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <form method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php if (isset($dest['id'])): ?><input type="hidden" name="id" value="<?php echo (int)$dest['id']; ?>"><?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Title *</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['title'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Destination *</label>
                            <input type="text" name="destination" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['destination'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Duration (days)</label>
                            <input type="number" name="duration_days" min="1" value="<?php echo htmlspecialchars($dest['duration_days'] ?? '1'); ?>" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Price *</label>
                            <input type="number" step="0.01" name="price" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['price'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Currency</label>
                            <input type="text" name="currency" value="<?php echo htmlspecialchars($dest['currency'] ?? 'KES'); ?>" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Departure Location *</label>
                            <input type="text" name="departure_location" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['departure_location'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Departure Date *</label>
                            <input type="date" name="departure_date" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['departure_date'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Return Date</label>
                            <input type="date" name="return_date" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($dest['return_date'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Booking Deadline</label>
                            <input type="date" name="booking_deadline" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($dest['booking_deadline'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Max Participants</label>
                            <input type="number" name="max_participants" min="1" value="<?php echo htmlspecialchars($dest['max_participants'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                            <?php if (!empty($dest['image_path'])): ?>
                                <p class="text-xs text-gray-500 mt-1">Current: <?php echo basename($dest['image_path']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-400 mb-2">Description *</label>
                            <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required><?php echo htmlspecialchars($dest['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-400 mb-2">Itinerary</label>
                            <textarea name="itinerary" rows="6" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo htmlspecialchars($dest['itinerary'] ?? ''); ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-400 mb-2">Inclusions</label>
                            <textarea name="inclusions" rows="4" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo htmlspecialchars($dest['inclusions'] ?? ''); ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-400 mb-2">Exclusions</label>
                            <textarea name="exclusions" rows="4" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo htmlspecialchars($dest['exclusions'] ?? ''); ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_published" value="1" <?php echo isset($dest['is_published']) && $dest['is_published'] ? 'checked' : ''; ?> class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500 focus:ring-2">
                                <span class="text-sm text-gray-400">Published (visible to customers)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-8">
                        <a href="<?php echo base_url('/travel/destinations'); ?>" class="px-6 py-2 border border-gray-600 text-gray-300 hover:bg-gray-700 rounded-lg font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            <?php echo isset($dest) ? 'Update Destination' : 'Create Destination'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>
<?php 
/** @var array $destinations */
$pageTitle = 'My Destinations';
$currentPage = 'destinations';
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
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">My Destinations</h1>
                        <p class="text-gray-400">Manage your travel destinations</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Destination
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <?php if (empty($destinations)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-white mb-2">No destinations yet</h3>
                        <p class="text-gray-400 mb-4">Create your first travel destination to get started</p>
                        <a href="<?php echo base_url('/travel/destinations/create'); ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Destination
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Image</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Title</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Destination</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Departure</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Price</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Status</th>
                                    <th class="text-left py-3 px-3 text-gray-400 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($destinations as $d): ?>
                                <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors">
                                    <td class="py-3 px-3">
                                        <?php if (!empty($d['image_path'])): ?>
                                            <img src="<?php echo base_url($d['image_path']); ?>" class="h-10 w-10 object-cover rounded" alt="img">
                                        <?php else: ?>
                                            <div class="h-10 w-10 bg-gray-700 rounded flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 font-medium text-white"><?php echo htmlspecialchars($d['title']); ?></td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo htmlspecialchars($d['destination']); ?></td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo date('M j, Y', strtotime($d['departure_date'])); ?></td>
                                    <td class="py-3 px-3 text-gray-300"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></td>
                                    <td class="py-3 px-3">
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo (int)$d['is_published'] ? 'bg-green-900 text-green-200' : 'bg-gray-900 text-gray-200'; ?>">
                                                <?php echo (int)$d['is_published'] ? 'Published' : 'Draft'; ?>
                                            </span>
                                            <?php if ($d['is_featured']): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-yellow-600/20 to-orange-500/20 text-yellow-400 rounded-full text-xs font-medium">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    Featured
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <?php if ($d['is_featured']): ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-600/20 to-orange-500/20 text-yellow-400 rounded-lg text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    Featured
                                                </span>
                                            <?php else: ?>
                                                <button onclick="openFeatureModal(<?php echo $d['id']; ?>, '<?php echo htmlspecialchars($d['title']); ?>')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-yellow-600/20 to-orange-500/20 text-yellow-400 hover:from-yellow-600/30 hover:to-orange-500/30 rounded-lg text-sm font-medium transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                    </svg>
                                                    Request Feature
                                                </button>
                                            <?php endif; ?>
                                            <a href="<?php echo base_url('/travel/destinations/edit?id=' . $d['id']); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-600/20 text-gray-300 hover:bg-gray-600/30 rounded-lg text-sm font-medium transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        </div>
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

<!-- Feature Request Modal -->
<div id="featureModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Request Destination Feature</h3>
            <button onclick="closeFeatureModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4">
            <div class="bg-yellow-600/20 border border-yellow-600/30 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 text-yellow-400 font-medium mb-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Featured Content Benefits
                </div>
                <ul class="text-sm text-gray-300 space-y-1">
                    <li>• Your destination appears at the top of travel listings</li>
                    <li>• Featured badge displayed on your destination</li>
                    <li>• Increased visibility and booking potential</li>
                    <li>• Additional <?php echo number_format($commissionRate, 1); ?>% commission on bookings</li>
                </ul>
            </div>
            
            <p class="text-gray-300 text-sm mb-4">
                <strong id="modalDestinationTitle"></strong> will be reviewed by our admin team. 
                If approved, it will be featured with increased visibility and a <strong><?php echo number_format($commissionRate, 1); ?>% additional commission</strong> will be applied to bookings.
            </p>
        </div>
        
        <form id="featureRequestForm" method="POST" action="<?php echo base_url('/travel/destinations/request-feature'); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="modalDestinationId" name="destination_id" value="">
            
            <input type="hidden" name="commission" value="<?php echo number_format($commissionRate, 2); ?>">
            
            <div class="flex gap-3">
                <button type="button" onclick="closeFeatureModal()" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-500 hover:from-yellow-700 hover:to-orange-600 text-white rounded-lg transition-colors font-medium">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openFeatureModal(destinationId, destinationTitle) {
    document.getElementById('modalDestinationId').value = destinationId;
    document.getElementById('modalDestinationTitle').textContent = destinationTitle;
    document.getElementById('featureModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFeatureModal() {
    document.getElementById('featureModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('featureModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFeatureModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFeatureModal();
    }
});
</script>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>
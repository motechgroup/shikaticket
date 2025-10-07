<?php /** @var array $featuredEvents */ ?>
<?php /** @var array $featuredDestinations */ ?>
<?php /** @var array $pendingRequests */ ?>
<?php /** @var array $featureRequests */ ?>
<?php $pageTitle = 'Featured Content'; ?>

<div class="max-w-7xl mx-auto px-4 py-8">
	<?php include __DIR__ . '/../../components/mobile_nav_simple.php'; ?>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Featured Content Management</h1>
        <p class="text-gray-400">Manage featured events and travel destinations with commission tracking</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Featured Events</p>
                    <p class="text-2xl font-bold text-white"><?php echo count($featuredEvents); ?></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Featured Destinations</p>
                    <p class="text-2xl font-bold text-white"><?php echo count($featuredDestinations); ?></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Pending Requests</p>
                    <p class="text-2xl font-bold text-white"><?php echo count($pendingRequests); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Settings Section -->
    <div class="card p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Default Commission Rates</h2>
            <span class="badge bg-blue-600">Admin Settings</span>
        </div>
        
        <form method="POST" action="<?php echo base_url('/admin/featured-content/update-commission-settings'); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php echo csrf_field(); ?>
            
            <!-- Event Commission Settings -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Event Featured Commission
                </h3>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Default Commission Rate (%)</label>
                    <input type="number" name="event_commission" value="<?php echo htmlspecialchars($eventCommissionRate ?? '5.00'); ?>" step="0.01" min="0" max="50" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <p class="text-xs text-gray-400 mt-1">Commission rate applied to featured events</p>
                </div>
            </div>
            
            <!-- Destination Commission Settings -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    Destination Featured Commission
                </h3>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Default Commission Rate (%)</label>
                    <input type="number" name="destination_commission" value="<?php echo htmlspecialchars($destinationCommissionRate ?? '5.00'); ?>" step="0.01" min="0" max="50" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500" required>
                    <p class="text-xs text-gray-400 mt-1">Commission rate applied to featured destinations</p>
                </div>
            </div>
            
            <div class="md:col-span-2 pt-4">
                <button type="submit" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Commission Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Featured Events Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Featured Events</h2>
            <span class="badge bg-blue-600"><?php echo count($featuredEvents); ?> Featured</span>
        </div>

        <?php if (empty($featuredEvents)): ?>
        <div class="card p-8 text-center">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">No Featured Events</h3>
            <p class="text-gray-400">No events are currently featured. Use the pending requests section to feature events.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php foreach ($featuredEvents as $event): ?>
            <div class="card p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="text-gray-400 text-sm mb-1">Organizer: <?php echo htmlspecialchars($event['organizer_name']); ?></p>
                        <p class="text-gray-400 text-sm mb-1">Date: <?php echo date('M j, Y', strtotime($event['event_date'])); ?></p>
                        <p class="text-gray-400 text-sm">Price: <?php echo htmlspecialchars($event['currency']); ?> <?php echo number_format($event['price'], 2); ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="badge bg-blue-600 mb-2">Featured</span>
                        <span class="text-sm text-yellow-400 font-semibold"><?php echo $event['featured_commission']; ?>% Commission</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                    <div class="text-xs text-gray-500">
                        Featured: <?php echo date('M j, Y H:i', strtotime($event['featured_at'])); ?>
                    </div>
                    <form method="POST" action="<?php echo base_url('/admin/featured-content/unfeature-event'); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" class="btn btn-secondary text-sm" onclick="return confirm('Are you sure you want to unfeature this event?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Unfeature
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Featured Travel Destinations Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Featured Travel Destinations</h2>
            <span class="badge bg-green-600"><?php echo count($featuredDestinations); ?> Featured</span>
        </div>

        <?php if (empty($featuredDestinations)): ?>
        <div class="card p-8 text-center">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">No Featured Destinations</h3>
            <p class="text-gray-400">No travel destinations are currently featured. Use the pending requests section to feature destinations.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php foreach ($featuredDestinations as $destination): ?>
            <div class="card p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2"><?php echo htmlspecialchars($destination['title']); ?></h3>
                        <p class="text-gray-400 text-sm mb-1">Agency: <?php echo htmlspecialchars($destination['company_name']); ?></p>
                        <p class="text-gray-400 text-sm mb-1">Destination: <?php echo htmlspecialchars($destination['destination']); ?></p>
                        <p class="text-gray-400 text-sm">Price: <?php echo htmlspecialchars($destination['currency']); ?> <?php echo number_format($destination['price'], 2); ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="badge bg-green-600 mb-2">Featured</span>
                        <span class="text-sm text-yellow-400 font-semibold"><?php echo $destination['featured_commission']; ?>% Commission</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                    <div class="text-xs text-gray-500">
                        Featured: <?php echo date('M j, Y H:i', strtotime($destination['featured_at'])); ?>
                    </div>
                    <form method="POST" action="<?php echo base_url('/admin/featured-content/unfeature-destination'); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="destination_id" value="<?php echo $destination['id']; ?>">
                        <button type="submit" class="btn btn-secondary text-sm" onclick="return confirm('Are you sure you want to unfeature this destination?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Unfeature
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Feature Requests Section -->
    <?php if (!empty($featureRequests)): ?>
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Feature Requests</h2>
            <span class="badge bg-purple-600"><?php echo count($featureRequests); ?> Pending</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php foreach ($featureRequests as $request): ?>
            <div class="card p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2"><?php echo htmlspecialchars($request['content_title']); ?></h3>
                        <p class="text-gray-400 text-sm mb-1"><?php echo ucfirst(str_replace('_', ' ', $request['content_type'])); ?></p>
                        <p class="text-gray-400 text-sm mb-1">Requested by: <?php echo htmlspecialchars($request['requester_name']); ?></p>
                        <p class="text-gray-400 text-sm mb-1">Email: <?php echo htmlspecialchars($request['requester_email']); ?></p>
                        <p class="text-gray-400 text-sm">Requested Commission: <?php echo $request['requested_commission']; ?>%</p>
                        <p class="text-xs text-gray-500 mt-2">Requested: <?php echo date('M j, Y H:i', strtotime($request['created_at'])); ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="badge bg-purple-600 mb-2">Pending</span>
                        <span class="text-sm text-yellow-400 font-semibold"><?php echo $request['requested_commission']; ?>% Commission</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 pt-4 border-t border-gray-700">
                    <form method="POST" action="<?php echo base_url('/admin/featured-content/approve-request'); ?>" class="flex-1">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        
                        <div class="flex gap-2 mb-2">
                            <input type="number" name="commission" value="<?php echo $request['requested_commission']; ?>" step="0.01" min="0" max="20" 
                                   class="flex-1 input text-sm" placeholder="Commission %">
                            <button type="submit" class="btn btn-primary text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve
                            </button>
                        </div>
                        
                        <input type="text" name="notes" placeholder="Approval notes (optional)" 
                               class="input text-sm w-full">
                    </form>
                    
                    <form method="POST" action="<?php echo base_url('/admin/featured-content/reject-request'); ?>" class="flex flex-col">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <input type="text" name="notes" placeholder="Rejection reason" 
                               class="input text-sm mb-2" required>
                        <button type="submit" class="btn btn-secondary text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pending Requests Section -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Available Content</h2>
            <span class="badge bg-yellow-600"><?php echo count($pendingRequests); ?> Available</span>
        </div>

        <?php if (empty($pendingRequests)): ?>
        <div class="card p-8 text-center">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">All Content Featured</h3>
            <p class="text-gray-400">All published events and travel destinations are currently featured.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php foreach ($pendingRequests as $request): ?>
            <div class="card p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2"><?php echo htmlspecialchars($request['title']); ?></h3>
                        <p class="text-gray-400 text-sm mb-1"><?php echo ucfirst(str_replace('_', ' ', $request['content_type'])); ?></p>
                        <p class="text-gray-400 text-sm mb-1">By: <?php echo htmlspecialchars($request['organizer_name']); ?></p>
                        <p class="text-gray-400 text-sm">Price: <?php echo htmlspecialchars($request['currency']); ?> <?php echo number_format($request['price'], 2); ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="badge bg-gray-600 mb-2">Available</span>
                        <span class="text-sm text-blue-400 font-semibold">Default: 5%</span>
                    </div>
                </div>
                
                <form method="POST" action="<?php echo base_url('/admin/featured-content/feature-' . $request['content_type']); ?>" class="flex items-center gap-3">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="<?php echo $request['content_type'] === 'event' ? 'event_id' : 'destination_id'; ?>" value="<?php echo $request['id']; ?>">
                    
                    <div class="flex-1">
                        <input type="number" name="commission" value="5.00" step="0.01" min="0" max="20" 
                               class="input text-sm" placeholder="Commission %">
                    </div>
                    
                    <div class="flex-1">
                        <input type="text" name="notes" placeholder="Notes (optional)" 
                               class="input text-sm">
                    </div>
                    
                    <button type="submit" class="btn btn-primary text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Feature
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

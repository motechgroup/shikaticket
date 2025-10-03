<div class="max-w-4xl mx-auto px-4 py-6 md:py-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
                <?php echo htmlspecialchars($hotel['hotel_name']); ?>
            </h1>
            <p class="text-gray-400">Hotel Application Details</p>
        </div>
        <a href="<?php echo base_url('/admin/hotels'); ?>" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
            ← Back to Applications
        </a>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <?php
        $statusColors = [
            'pending' => 'bg-yellow-600 text-yellow-200',
            'contacted' => 'bg-blue-600 text-blue-200',
            'approved' => 'bg-green-600 text-green-200',
            'rejected' => 'bg-red-600 text-red-200'
        ];
        $statusColor = $statusColors[$hotel['status']] ?? 'bg-gray-600 text-gray-200';
        ?>
        <span class="px-4 py-2 rounded-full text-sm font-medium <?php echo $statusColor; ?>">
            Status: <?php echo ucfirst($hotel['status']); ?>
        </span>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Hotel Details -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Hotel Information
                </h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Hotel Name</label>
                        <p class="text-white font-medium"><?php echo htmlspecialchars($hotel['hotel_name']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Location</label>
                        <p class="text-white"><?php echo htmlspecialchars($hotel['location']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Number of Rooms</label>
                        <p class="text-white"><?php echo (int)$hotel['rooms']; ?> rooms</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Website</label>
                        <?php if ($hotel['website']): ?>
                            <a href="<?php echo htmlspecialchars($hotel['website']); ?>" 
                               target="_blank" 
                               class="text-blue-400 hover:text-blue-300 transition-colors">
                                <?php echo htmlspecialchars($hotel['website']); ?>
                                <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        <?php else: ?>
                            <p class="text-gray-500">Not provided</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Contact Information
                </h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Contact Person</label>
                        <p class="text-white font-medium"><?php echo htmlspecialchars($hotel['contact_person']); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Email</label>
                        <a href="mailto:<?php echo htmlspecialchars($hotel['email']); ?>" 
                           class="text-blue-400 hover:text-blue-300 transition-colors">
                            <?php echo htmlspecialchars($hotel['email']); ?>
                        </a>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Phone</label>
                        <a href="tel:<?php echo htmlspecialchars($hotel['phone']); ?>" 
                           class="text-blue-400 hover:text-blue-300 transition-colors">
                            <?php echo htmlspecialchars($hotel['phone']); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hotel Experience -->
            <?php if ($hotel['experience']): ?>
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Hotel Experience & Facilities
                </h2>
                <div class="text-gray-300 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($hotel['experience'])); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Why Interested -->
            <?php if ($hotel['why_interested']): ?>
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Why Interested in Our Platform
                </h2>
                <div class="text-gray-300 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($hotel['why_interested'])); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Update -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Update Status</h3>
                
                <form method="POST" action="<?php echo base_url('/admin/hotels/update-status'); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$hotel['id']; ?>">
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                        <select name="status" id="status" class="select w-full">
                            <option value="pending" <?php echo ($hotel['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="contacted" <?php echo ($hotel['status'] === 'contacted') ? 'selected' : ''; ?>>Contacted</option>
                            <option value="approved" <?php echo ($hotel['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo ($hotel['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="admin_notes" class="block text-sm font-medium text-gray-400 mb-2">Admin Notes</label>
                        <textarea name="admin_notes" id="admin_notes" rows="4" 
                                  class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                  placeholder="Add notes about this application..."><?php echo htmlspecialchars($hotel['admin_notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Application Timeline -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Application Timeline</h3>
                
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <p class="text-white text-sm font-medium">Application Submitted</p>
                            <p class="text-gray-400 text-xs">
                                <?php echo date('M j, Y \a\t g:i A', strtotime($hotel['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($hotel['updated_at'] && $hotel['updated_at'] !== $hotel['created_at']): ?>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-green-400 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <p class="text-white text-sm font-medium">Last Updated</p>
                            <p class="text-gray-400 text-xs">
                                <?php echo date('M j, Y \a\t g:i A', strtotime($hotel['updated_at'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="mailto:<?php echo htmlspecialchars($hotel['email']); ?>" 
                       class="block w-full text-center py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Send Email
                    </a>
                    
                    <a href="tel:<?php echo htmlspecialchars($hotel['phone']); ?>" 
                       class="block w-full text-center py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Call Hotel
                    </a>
                    
                    <?php if ($hotel['website']): ?>
                    <a href="<?php echo htmlspecialchars($hotel['website']); ?>" 
                       target="_blank"
                       class="block w-full text-center py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        Visit Website
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

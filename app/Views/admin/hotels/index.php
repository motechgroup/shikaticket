<div class="max-w-7xl mx-auto px-4 py-6 md:py-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Hotel Applications</h1>
            <p class="text-gray-400">Manage hotel applications and onboarding requests</p>
        </div>
        <a href="<?php echo base_url('/admin'); ?>" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-yellow-400 mb-1"><?php echo $stats['pending'] ?? 0; ?></div>
            <div class="text-sm text-gray-400">Pending</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-blue-400 mb-1"><?php echo $stats['contacted'] ?? 0; ?></div>
            <div class="text-sm text-gray-400">Contacted</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-green-400 mb-1"><?php echo $stats['approved'] ?? 0; ?></div>
            <div class="text-sm text-gray-400">Approved</div>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-red-400 mb-1"><?php echo $stats['rejected'] ?? 0; ?></div>
            <div class="text-sm text-gray-400">Rejected</div>
        </div>
    </div>

    <!-- Search and Filter -->
    <form method="get" class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" 
                   placeholder="Search hotels, contacts, emails..." 
                   class="input flex-1">
            <select name="status" class="select">
                <option value="">All Status</option>
                <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="contacted" <?php echo ($status === 'contacted') ? 'selected' : ''; ?>>Contacted</option>
                <option value="approved" <?php echo ($status === 'approved') ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo ($status === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
            </select>
            <button type="submit" class="btn btn-secondary">Search</button>
        </div>
    </form>

    <?php if (empty($hotels)): ?>
        <!-- Empty State -->
        <div class="card p-8 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-lg font-medium mb-2">No Hotel Applications Found</h3>
                <p class="text-gray-400"><?php echo !empty($q) ? 'No applications match your search criteria.' : 'No hotel applications have been submitted yet.'; ?></p>
            </div>
        </div>
    <?php else: ?>
        <!-- Applications List -->
        <div class="space-y-4">
            <?php foreach ($hotels as $hotel): ?>
                <div class="card p-4 md:p-6 hover:border-gray-600 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Main Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-semibold text-white truncate">
                                        <?php echo htmlspecialchars($hotel['hotel_name']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-400 mt-1">
                                        <?php echo htmlspecialchars($hotel['contact_person']); ?> • <?php echo htmlspecialchars($hotel['location']); ?>
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-600 text-yellow-200',
                                        'contacted' => 'bg-blue-600 text-blue-200',
                                        'approved' => 'bg-green-600 text-green-200',
                                        'rejected' => 'bg-red-600 text-red-200'
                                    ];
                                    $statusColor = $statusColors[$hotel['status']] ?? 'bg-gray-600 text-gray-200';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                        <?php echo ucfirst($hotel['status']); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400 mb-3">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($hotel['email']); ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span><?php echo htmlspecialchars($hotel['phone']); ?></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span><?php echo (int)$hotel['rooms']; ?> rooms</span>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
                                <div>
                                    <span class="font-medium">Applied:</span> 
                                    <?php echo date('M j, Y H:i', strtotime($hotel['created_at'])); ?>
                                </div>
                                <?php if ($hotel['updated_at'] && $hotel['updated_at'] !== $hotel['created_at']): ?>
                                    <div>
                                        <span class="font-medium">Updated:</span> 
                                        <?php echo date('M j, Y H:i', strtotime($hotel['updated_at'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-2 lg:flex-col lg:min-w-0">
                            <a href="<?php echo base_url('/admin/hotels/show?id=' . $hotel['id']); ?>" 
                               class="btn btn-primary touch-target text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

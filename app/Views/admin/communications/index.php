<?php /** @var array $communications */ ?>
<div class="max-w-6xl mx-auto px-4 py-6 md:py-10">
	<?php $pageTitle = 'Communications'; include __DIR__ . '/../../components/mobile_nav_simple.php'; ?>
    <!-- Mobile-friendly header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-semibold">Communications</h1>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="<?php echo base_url('/admin/communications/direct-sms'); ?>" class="btn btn-secondary touch-target">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Direct SMS
            </a>
            <a href="<?php echo base_url('/admin/communications/create'); ?>" class="btn btn-primary touch-target">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Communication
            </a>
        </div>
    </div>

    <!-- Communications List -->
    <?php if (empty($communications)): ?>
        <div class="card p-8 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-lg font-medium mb-2">No Communications Yet</h3>
                <p class="text-gray-400 mb-6">Start by sending your first communication to users, organizers, or travel agencies.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?php echo base_url('/admin/communications/create'); ?>" class="btn btn-primary touch-target">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Communication
                </a>
                <a href="<?php echo base_url('/admin/communications/direct-sms'); ?>" class="btn btn-secondary touch-target">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Direct SMS
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Mobile-friendly communications grid -->
        <div class="space-y-4">
            <?php foreach ($communications as $comm): ?>
                <div class="card p-4 md:p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Main Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-semibold text-white truncate">
                                        <?php echo htmlspecialchars($comm['title']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-400 mt-1">
                                        <?php echo ucfirst(str_replace('_', ' ', $comm['recipient_type'])); ?>
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <?php
                                    $statusColors = [
                                        'draft' => 'bg-gray-600 text-gray-200',
                                        'sending' => 'bg-yellow-600 text-yellow-200',
                                        'completed' => 'bg-green-600 text-green-200',
                                        'failed' => 'bg-red-600 text-red-200'
                                    ];
                                    $statusColor = $statusColors[$comm['status']] ?? 'bg-gray-600 text-gray-200';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                        <?php echo ucfirst($comm['status']); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Recipients Info -->
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400 mb-3">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="font-medium text-white"><?php echo $comm['total_recipients']; ?></span> recipients
                                </div>
                                <?php if ($comm['sent_count'] > 0 || $comm['failed_count'] > 0): ?>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span><?php echo $comm['sent_count']; ?> sent</span>
                                    </div>
                                    <?php if ($comm['failed_count'] > 0): ?>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span><?php echo $comm['failed_count']; ?> failed</span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Dates -->
                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
                                <div>
                                    <span class="font-medium">Created:</span> 
                                    <?php echo date('M j, Y', strtotime($comm['created_at'])); ?> by <?php echo htmlspecialchars($comm['created_by_name'] ?? 'Unknown'); ?>
                                </div>
                                <?php if ($comm['sent_at']): ?>
                                    <div>
                                        <span class="font-medium">Sent:</span> 
                                        <?php echo date('M j, Y H:i', strtotime($comm['sent_at'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-2 lg:flex-col lg:min-w-0">
                            <a href="<?php echo base_url('/admin/communications/show?id=' . $comm['id']); ?>" 
                               class="btn btn-sm btn-secondary touch-target text-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            </a>
                            <?php if ($comm['status'] === 'draft'): ?>
                                <form method="post" action="<?php echo base_url('/admin/communications/send'); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="communication_id" value="<?php echo $comm['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-primary touch-target w-full" 
                                            onclick="return confirm('Send this communication to <?php echo $comm['total_recipients']; ?> recipients?')">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        Send
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

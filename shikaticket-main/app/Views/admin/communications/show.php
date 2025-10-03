<?php /** @var array $communication */ ?>
<?php /** @var array $recipients */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center gap-4 mb-6">
        <a href="<?php echo base_url('/admin/communications'); ?>" class="text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold"><?php echo htmlspecialchars($communication['title']); ?></h1>
        
        <?php if ($communication['status'] === 'draft'): ?>
            <form method="post" action="<?php echo base_url('/admin/communications/send'); ?>" class="ml-auto">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="communication_id" value="<?php echo $communication['id']; ?>">
                <button type="submit" class="btn btn-primary" 
                        onclick="return confirm('Send this communication to <?php echo $communication['total_recipients']; ?> recipients?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Communication
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Communication Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Message</h2>
                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <pre class="whitespace-pre-wrap text-sm"><?php echo htmlspecialchars($communication['message']); ?></pre>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Statistics</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-400"><?php echo $communication['total_recipients']; ?></div>
                        <div class="text-sm text-gray-400">Total Recipients</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400"><?php echo $communication['sent_count']; ?></div>
                        <div class="text-sm text-gray-400">Sent</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-400"><?php echo $communication['failed_count']; ?></div>
                        <div class="text-sm text-gray-400">Failed</div>
                    </div>
                    <div class="text-center">
                        <?php 
                        $successRate = $communication['total_recipients'] > 0 ? 
                            round(($communication['sent_count'] / $communication['total_recipients']) * 100, 1) : 0;
                        ?>
                        <div class="text-2xl font-bold text-yellow-400"><?php echo $successRate; ?>%</div>
                        <div class="text-sm text-gray-400">Success Rate</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="card p-6">
                <h3 class="font-semibold mb-4">Status</h3>
                <?php
                $statusColors = [
                    'draft' => 'bg-gray-600 text-gray-200',
                    'sending' => 'bg-yellow-600 text-yellow-200',
                    'completed' => 'bg-green-600 text-green-200',
                    'failed' => 'bg-red-600 text-red-200'
                ];
                $statusColor = $statusColors[$communication['status']] ?? 'bg-gray-600 text-gray-200';
                ?>
                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColor; ?>">
                    <?php echo ucfirst($communication['status']); ?>
                </span>
            </div>
            
            <!-- Details -->
            <div class="card p-6">
                <h3 class="font-semibold mb-4">Details</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-400">Recipient Type:</span>
                        <span class="ml-2"><?php echo ucfirst(str_replace('_', ' ', $communication['recipient_type'])); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-400">Created:</span>
                        <span class="ml-2"><?php echo date('M j, Y H:i', strtotime($communication['created_at'])); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-400">Created By:</span>
                        <span class="ml-2"><?php echo htmlspecialchars($communication['created_by_name'] ?? 'Unknown'); ?></span>
                    </div>
                    <?php if ($communication['sent_at']): ?>
                        <div>
                            <span class="text-gray-400">Sent:</span>
                            <span class="ml-2"><?php echo date('M j, Y H:i', strtotime($communication['sent_at'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipients -->
    <div class="card">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Recipients (<?php echo count($recipients); ?>)</h2>
                <div class="flex gap-2">
                    <button id="filter_all" class="btn btn-sm btn-secondary filter-btn active">All</button>
                    <button id="filter_sent" class="btn btn-sm btn-secondary filter-btn">Sent</button>
                    <button id="filter_failed" class="btn btn-sm btn-secondary filter-btn">Failed</button>
                    <button id="filter_pending" class="btn btn-sm btn-secondary filter-btn">Pending</button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-left py-3">Name</th>
                            <th class="text-left py-3">Phone</th>
                            <th class="text-left py-3">Type</th>
                            <th class="text-left py-3">Status</th>
                            <th class="text-left py-3">Sent</th>
                            <th class="text-left py-3">Error</th>
                        </tr>
                    </thead>
                    <tbody id="recipients_table">
                        <?php foreach ($recipients as $recipient): ?>
                            <tr class="border-b border-gray-800 recipient-row" data-status="<?php echo $recipient['status']; ?>">
                                <td class="py-3 font-medium"><?php echo htmlspecialchars($recipient['name']); ?></td>
                                <td class="py-3"><?php echo htmlspecialchars($recipient['phone_number']); ?></td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded text-xs bg-gray-700">
                                        <?php echo ucfirst(str_replace('_', ' ', $recipient['recipient_type'])); ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-600 text-yellow-200',
                                        'sent' => 'bg-green-600 text-green-200',
                                        'failed' => 'bg-red-600 text-red-200'
                                    ];
                                    $statusColor = $statusColors[$recipient['status']] ?? 'bg-gray-600 text-gray-200';
                                    $statusIcon = $recipient['status'] === 'sent' ? '✓' : ($recipient['status'] === 'failed' ? '✗' : '⏳');
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                        <?php echo $statusIcon; ?> <?php echo ucfirst($recipient['status']); ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <?php if ($recipient['sent_at']): ?>
                                        <div class="text-sm"><?php echo date('M j, Y', strtotime($recipient['sent_at'])); ?></div>
                                        <div class="text-xs text-gray-400"><?php echo date('H:i:s', strtotime($recipient['sent_at'])); ?></div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <?php if ($recipient['error_message']): ?>
                                        <div class="text-sm text-red-400" title="<?php echo htmlspecialchars($recipient['error_message']); ?>">
                                            <?php echo htmlspecialchars(substr($recipient['error_message'], 0, 50)); ?>
                                            <?php if (strlen($recipient['error_message']) > 50): ?>...<?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const recipientRows = document.querySelectorAll('.recipient-row');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter rows
            const filter = this.id.replace('filter_', '');
            
            recipientRows.forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
.filter-btn.active {
    background-color: #3b82f6;
    color: white;
}
</style>

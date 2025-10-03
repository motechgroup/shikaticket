<?php /** @var array $conversations */ ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="card p-6">
        <h1 class="text-xl font-semibold mb-4">Support Conversations</h1>
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-2">ID</th>
                    <th class="text-left py-2">User Email</th>
                    <th class="text-left py-2">Subject</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-left py-2">Last Message</th>
                    <th class="text-left py-2"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($conversations as $c): ?>
                    <tr class="border-b border-gray-800">
                        <td class="py-2 text-sm"><?php echo (int)$c['id']; ?></td>
                        <td class="py-2 text-sm"><?php echo htmlspecialchars($c['email'] ?? ''); ?></td>
                        <td class="py-2 text-sm"><?php echo htmlspecialchars($c['subject'] ?? ''); ?></td>
                        <td class="py-2 text-sm"><?php echo htmlspecialchars($c['status']); ?></td>
                        <td class="py-2 text-sm"><?php echo htmlspecialchars($c['last_message_at'] ?? ''); ?></td>
                        <td class="py-2 text-sm"><a class="btn btn-secondary" href="<?php echo base_url('/admin/support/conversation?id='.(int)$c['id']); ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



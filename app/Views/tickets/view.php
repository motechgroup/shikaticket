<?php /** @var array $ticket */ ?>
<div class="max-w-md mx-auto px-4 py-10">
    <div class="card p-6 text-center">
        <h1 class="text-xl font-semibold mb-1"><?php echo htmlspecialchars($ticket['title'] ?? ''); ?></h1>
        <div class="text-sm text-gray-400 mb-4"><?php echo htmlspecialchars(($ticket['event_date'] ?? '') . ' • ' . ($ticket['venue'] ?? '')); ?></div>
        <div class="bg-gray-900 rounded flex items-center justify-center mb-4">
            <img src="<?php echo htmlspecialchars($qr); ?>" alt="QR" class="w-full h-64 object-contain">
        </div>
        <div class="text-lg font-semibold tracking-widest mb-2">#<?php echo htmlspecialchars($ticket['code']); ?></div>
        <?php $status = strtolower($ticket['status'] ?? 'valid'); ?>
        <?php if ($status === 'redeemed'): ?>
            <span class="badge" style="background:#052e16;border-color:#14532d;color:#86efac">Redeemed</span>
        <?php else: ?>
            <span class="badge" style="background:#1f2937;border-color:#374151;color:#e5e7eb">Valid</span>
        <?php endif; ?>
    </div>
    <div class="text-center mt-4">
        <a class="btn btn-secondary" href="<?php echo base_url('/scanner'); ?>">Open Scanner</a>
    </div>
</div>



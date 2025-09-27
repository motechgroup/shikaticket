<?php /** @var array $bookings */ ?>
<?php $pageTitle = 'Bookings'; ?>
<div class="max-w-6xl mx-auto px-4 md:px-6 py-6 md:py-10">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
    <h1 class="text-xl md:text-2xl font-semibold text-white">Bookings</h1>
    <a class="btn btn-secondary w-full sm:w-auto" href="<?php echo base_url('/travel/dashboard'); ?>">Back to Dashboard</a>
  </div>

  <div class="card p-4">
    <?php if (empty($bookings)): ?>
      <div class="p-6 text-gray-300">No bookings yet.</div>
    <?php else: ?>
      <table class="min-w-full text-sm table">
        <thead>
          <tr>
            <th class="p-3 text-left">Customer</th>
            <th class="p-3 text-left">Destination</th>
            <th class="p-3 text-left">Participants</th>
            <th class="p-3 text-left">Amount</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Booked</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($bookings as $b): ?>
          <tr>
            <td class="p-3">
              <div class="font-medium"><?php echo htmlspecialchars($b['customer_name'] ?? ''); ?></div>
              <div class="text-gray-400 text-xs"><?php echo htmlspecialchars($b['customer_email'] ?? ''); ?></div>
              <?php if (!empty($b['customer_phone'])): ?><div class="text-gray-400 text-xs"><?php echo htmlspecialchars($b['customer_phone']); ?></div><?php endif; ?>
            </td>
            <td class="p-3"><?php echo htmlspecialchars($b['destination_title'] ?? ''); ?></td>
            <td class="p-3"><?php echo (int)($b['participants_count'] ?? 1); ?></td>
            <td class="p-3"><?php echo htmlspecialchars(($b['currency'] ?? 'KES') . ' ' . number_format((float)($b['total_amount'] ?? 0), 2)); ?></td>
            <td class="p-3"><?php echo htmlspecialchars(ucfirst($b['status'] ?? 'pending')); ?></td>
            <td class="p-3"><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($b['booking_date'] ?? 'now'))); ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>



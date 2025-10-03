<?php /** @var array $bookings */ ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Travel Bookings</h1>
        <a href="<?php echo base_url('/travel'); ?>" class="btn btn-primary">Browse Travel</a>
    </div>

    <?php if (empty($bookings)): ?>
    <div class="card p-8 text-center">
        <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold mb-2">No travel bookings yet</h3>
        <p class="text-gray-400 mb-4">Start exploring amazing travel destinations and create your first booking!</p>
        <a href="<?php echo base_url('/travel'); ?>" class="btn btn-primary">Explore Travel</a>
    </div>
    <?php else: ?>
    <div class="grid gap-6">
        <?php foreach ($bookings as $booking): ?>
        <div class="card p-6" data-booking-status="<?php echo $booking['status']; ?>">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($booking['destination_title']); ?></h3>
                        <span class="badge <?php echo $booking['status'] === 'confirmed' ? 'bg-green-600' : ($booking['status'] === 'pending' ? 'bg-yellow-600' : 'bg-red-600'); ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-400">
                        <div>
                            <span class="block">Travel Agency</span>
                            <span class="font-medium text-white"><?php echo htmlspecialchars($booking['company_name']); ?></span>
                        </div>
                        <div>
                            <span class="block">Departure Date</span>
                            <span class="font-medium text-white"><?php echo date('M j, Y', strtotime($booking['departure_date'])); ?></span>
                        </div>
                        <div>
                            <span class="block">Participants</span>
                            <span class="font-medium text-white"><?php echo (int)$booking['participants_count']; ?> person(s)</span>
                        </div>
                        <div>
                            <span class="block">Total Amount</span>
                            <span class="font-medium text-white"><?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format((float)$booking['total_amount'], 2); ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-400">
                        <span>Booking Date: <?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?></span>
                        <?php if (!empty($booking['payment_status'])): ?>
                        <span class="ml-4">Payment: 
                            <span class="<?php echo $booking['payment_status'] === 'successful' ? 'text-green-400' : ($booking['payment_status'] === 'pending' ? 'text-yellow-400' : 'text-red-400'); ?>">
                                <?php echo ucfirst($booking['payment_status']); ?>
                            </span>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="<?php echo base_url('/user/travel-bookings/show?id=' . $booking['id']); ?>" class="btn btn-secondary btn-sm">View Details</a>
                    <?php if ($booking['status'] === 'pending' && ($booking['payment_status'] === 'pending' || $booking['payment_status'] === 'initiated')): ?>
                    <a href="<?php echo base_url('/travel/checkout?booking_id=' . $booking['id']); ?>" class="btn btn-primary btn-sm">Complete Payment</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Auto-refresh removed - using floating confirm button on detail pages instead -->

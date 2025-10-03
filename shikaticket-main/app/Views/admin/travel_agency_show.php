<?php /** @var array $agency */ /** @var array $destinations */ /** @var array $summary */ /** @var array $withdrawals */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Travel Agency: <?php echo htmlspecialchars($agency['company_name']); ?></h1>
        <div class="flex items-center gap-2">
            <a class="btn btn-secondary" href="<?php echo base_url('/admin/travel/agencies'); ?>">Back</a>
            <form method="post" action="<?php echo base_url('/admin/travel/agencies/toggle'); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$agency['id']; ?>">
                <input type="hidden" name="is_active" value="<?php echo (int)$agency['is_active'] ? 0 : 1; ?>">
                <button class="btn btn-primary"><?php echo (int)$agency['is_active'] ? 'Deactivate' : 'Activate'; ?></button>
            </form>
            <?php if (!(int)$agency['is_approved']): ?>
            <form method="post" action="<?php echo base_url('/admin/travel/agencies/approve'); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$agency['id']; ?>">
                <button class="btn btn-primary">Approve</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Basic Information -->
    <div class="grid md:grid-cols-4 gap-6 mb-6">
        <div class="card p-4">
            <div class="text-sm text-gray-400">Company Name</div>
            <div class="font-semibold"><?php echo htmlspecialchars($agency['company_name']); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Contact Person</div>
            <div class="font-semibold"><?php echo htmlspecialchars($agency['contact_person']); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Phone</div>
            <div class="font-semibold"><?php echo htmlspecialchars($agency['phone']); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Email</div>
            <div class="font-semibold"><?php echo htmlspecialchars($agency['email']); ?></div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <div class="card p-4">
            <div class="text-sm text-gray-400">Approved</div>
            <div class="font-semibold"><?php echo (int)$agency['is_approved'] ? 'Yes' : 'No'; ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Phone Verified</div>
            <div class="font-semibold"><?php echo (int)$agency['phone_verified'] ? 'Yes' : 'No'; ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Joined</div>
            <div class="font-semibold"><?php echo date('M j, Y', strtotime($agency['created_at'])); ?></div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid md:grid-cols-4 gap-6 mb-6">
        <div class="card p-4">
            <div class="text-sm text-gray-400">Destinations</div>
            <div class="text-2xl font-semibold"><?php echo count($destinations); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Total Bookings</div>
            <div class="text-2xl font-semibold"><?php echo (int)$summary['total_bookings']; ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Revenue</div>
            <div class="text-2xl font-semibold">KES <?php echo number_format($summary['total_revenue'], 2); ?></div>
        </div>
        <div class="card p-4">
            <div class="text-sm text-gray-400">Commission %</div>
            <div class="text-2xl font-semibold">
                <form method="post" action="<?php echo base_url('/admin/travel/agencies/commission'); ?>" class="flex items-center gap-2 mt-1">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$agency['id']; ?>">
                    <input class="input" style="width:80px" name="commission_rate" value="<?php echo number_format((float)($agency['commission_rate'] ?? 10.0), 2); ?>">
                    <button class="btn btn-secondary">Save</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div class="card p-4">
            <h3 class="font-semibold mb-3">Company Information</h3>
            <div class="space-y-2 text-sm">
                <div><span class="text-gray-400">Website:</span> 
                    <?php if (!empty($agency['website'])): ?>
                        <a href="<?php echo htmlspecialchars($agency['website']); ?>" target="_blank" class="link"><?php echo htmlspecialchars($agency['website']); ?></a>
                    <?php else: ?>
                        <span class="text-gray-500">Not provided</span>
                    <?php endif; ?>
                </div>
                <div><span class="text-gray-400">City:</span> <?php echo htmlspecialchars($agency['city'] ?? 'Not provided'); ?></div>
                <div><span class="text-gray-400">Country:</span> <?php echo htmlspecialchars($agency['country'] ?? 'Not provided'); ?></div>
                <div><span class="text-gray-400">Description:</span></div>
                <div class="text-gray-300"><?php echo nl2br(htmlspecialchars($agency['description'] ?? 'No description provided')); ?></div>
            </div>
        </div>

        <div class="card p-4">
            <h3 class="font-semibold mb-3">Logo</h3>
            <?php if (!empty($agency['logo_path'])): ?>
                <img src="<?php echo base_url($agency['logo_path']); ?>" alt="Agency Logo" class="w-32 h-32 object-cover rounded-lg">
            <?php else: ?>
                <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                    No Logo
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Information Section -->
    <div class="card p-4 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Payment Information
            </h2>
            <?php if ($agency['payment_info_verified'] ?? 0): ?>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Verified</span>
            <?php elseif (!empty($agency['payout_method'])): ?>
                <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded-full">Pending Verification</span>
            <?php else: ?>
                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Not Set</span>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($agency['payout_method'])): ?>
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Payment Method</label>
                    <div class="text-lg font-semibold capitalize"><?php echo str_replace('_', ' ', htmlspecialchars($agency['payout_method'])); ?></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Last Updated</label>
                    <div class="text-lg font-semibold"><?php echo $agency['payment_info_updated_at'] ? date('M j, Y', strtotime($agency['payment_info_updated_at'])) : 'Never'; ?></div>
                </div>
            </div>

            <?php if ($agency['payout_method'] === 'bank_transfer'): ?>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Bank Name</label>
                        <div class="text-lg font-semibold"><?php echo htmlspecialchars($agency['bank_name'] ?? ''); ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Account Name</label>
                        <div class="text-lg font-semibold"><?php echo htmlspecialchars($agency['bank_account_name'] ?? ''); ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Account Number</label>
                        <div class="text-lg font-semibold"><?php echo htmlspecialchars($agency['bank_account_number'] ?? ''); ?></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Bank Code</label>
                        <div class="text-lg font-semibold"><?php echo htmlspecialchars($agency['bank_code'] ?? ''); ?></div>
                    </div>
                </div>
            <?php elseif ($agency['payout_method'] === 'mpesa'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">M-Pesa Phone</label>
                    <div class="text-lg font-semibold"><?php echo htmlspecialchars($agency['mpesa_phone'] ?? ''); ?></div>
                </div>
            <?php elseif ($agency['payout_method'] === 'paypal'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">PayPal Email</label>
                    <div class="text-lg font-semibold"><?php echo htmlspecialchars($agency['paypal_email'] ?? ''); ?></div>
                </div>
            <?php elseif ($agency['payout_method'] === 'other'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Payment Details</label>
                    <div class="text-lg font-semibold"><?php echo nl2br(htmlspecialchars($agency['other_payment_details'] ?? '')); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!($agency['payment_info_verified'] ?? 0)): ?>
                <div class="mt-4 pt-4 border-t">
                    <form method="post" action="<?php echo base_url('/admin/travel/agencies/verify-payment'); ?>" class="flex items-center gap-3">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?php echo (int)$agency['id']; ?>">
                        <button type="submit" class="btn btn-primary" name="action" value="verify">Verify Payment Info</button>
                        <button type="submit" class="btn btn-secondary" name="action" value="reject">Reject Payment Info</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-lg font-medium">No payment information set</p>
                <p class="text-sm">Travel agency has not set up payment details yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Destinations -->
    <div class="card p-4 mb-6">
        <h2 class="font-semibold mb-3">Destinations</h2>
        <?php if (empty($destinations)): ?>
            <div class="text-gray-400">No destinations created yet.</div>
        <?php else: ?>
            <table class="min-w-full text-sm table">
                <thead>
                    <tr>
                        <th class="p-3 text-left">Title</th>
                        <th class="p-3 text-left">Location</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-left">Featured</th>
                        <th class="p-3 text-left">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($destinations as $dest): ?>
                    <tr>
                        <td class="p-3">
                            <a href="<?php echo base_url('/travel/destination?id='.(int)$dest['id']); ?>" target="_blank" class="link">
                                <?php echo htmlspecialchars($dest['title']); ?>
                            </a>
                        </td>
                        <td class="p-3"><?php echo htmlspecialchars($dest['destination']); ?></td>
                        <td class="p-3">KES <?php echo number_format((float)$dest['price'], 2); ?></td>
                        <td class="p-3">
                            <?php if ($dest['is_featured']): ?>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Featured</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Regular</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3"><?php echo date('M j, Y', strtotime($dest['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Withdrawals -->
    <div class="card p-4">
        <h2 class="font-semibold mb-3">Withdrawals</h2>
        <?php if (empty($withdrawals)): ?>
            <div class="text-gray-400">No withdrawal requests yet.</div>
        <?php else: ?>
            <table class="min-w-full text-sm table">
                <thead>
                    <tr>
                        <th class="p-3 text-left">Amount</th>
                        <th class="p-3 text-left">Destination</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Requested</th>
                        <th class="p-3 text-left">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($withdrawals as $w): ?>
                    <tr>
                        <td class="p-3">KES <?php echo number_format((float)$w['amount'], 2); ?></td>
                        <td class="p-3">
                            <?php if ($w['destination_title']): ?>
                                <?php echo htmlspecialchars($w['destination_title']); ?>
                            <?php else: ?>
                                <span class="text-gray-500">All destinations</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <?php
                            $statusColors = [
                                'requested' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                                'rejected' => 'bg-red-100 text-red-800'
                            ];
                            $colorClass = $statusColors[$w['status']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="px-2 py-1 <?php echo $colorClass; ?> text-xs rounded-full"><?php echo ucfirst($w['status']); ?></span>
                        </td>
                        <td class="p-3"><?php echo date('M j, Y', strtotime($w['created_at'])); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($w['notes'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

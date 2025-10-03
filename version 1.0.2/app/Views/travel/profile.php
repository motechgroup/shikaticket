<?php 
/** @var array $agency */
$pageTitle = 'Agency Profile';
$currentPage = 'profile';
?>
<div class="min-h-screen bg-gray-900 flex">
    <?php include __DIR__ . '/shared/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-0">
        <div class="px-4 lg:px-6 py-6 lg:py-8">
            <!-- Header -->
            <div class="mb-6 lg:mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">Agency Profile</h1>
                        <p class="text-gray-400">Manage your travel agency information</p>
                    </div>
                </div>
            </div>

            <!-- Phone Verification Alert -->
            <?php if (!(int)($agency['phone_verified'] ?? 0)): ?>
            <div class="mb-6 p-4 border border-amber-600/50 bg-amber-900/50 rounded-lg">
                <div class="flex items-center gap-3 mb-3">
                    <div class="text-2xl">📱</div>
                    <div>
                        <div class="font-semibold text-amber-200">Phone verification required</div>
                        <p class="text-sm text-amber-100 mt-1">Verify your phone to unlock destination creation.</p>
                    </div>
                </div>
                <form method="post" action="<?php echo base_url('/travel/profile/verify-phone'); ?>" class="mb-3">
                    <?php echo csrf_field(); ?>
                    <button class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">Send Verification Code</button>
                </form>
                <form method="post" action="<?php echo base_url('/travel/profile/verify-phone/confirm'); ?>" class="flex items-center gap-2">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" class="w-40 px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required>
                    <button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">Confirm</button>
                </form>
            </div>
            <?php else: ?>
            <div class="mb-6 p-4 border border-green-600/50 bg-green-900/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="text-2xl">✅</div>
                    <div class="text-green-200">Phone verified.</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Profile Form -->
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                <form method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Company Name</label>
                            <input type="text" name="company_name" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['company_name'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Contact Person</label>
                            <input type="text" name="contact_person" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['contact_person'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Phone</label>
                            <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['phone'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Website</label>
                            <input type="text" name="website" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['website'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">City</label>
                            <input type="text" name="city" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['city'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Country</label>
                            <input type="text" name="country" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['country'] ?? ''); ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-400 mb-2">Description</label>
                            <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo htmlspecialchars($agency['description'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Logo</label>
                            <input type="file" name="logo" accept="image/*" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                            <?php if (!empty($agency['logo_path'])): ?>
                                <p class="text-xs text-gray-500 mt-1">Current: <?php echo basename($agency['logo_path']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-8">
                        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Payment Information Section -->
            <div class="mt-8">
                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 lg:p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-white">Payment Information</h2>
                        <?php if ($agency['payment_info_verified'] ?? 0): ?>
                            <span class="px-2 py-1 bg-green-900 text-green-200 text-xs rounded-full">Verified</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-amber-900 text-amber-200 text-xs rounded-full">Pending Verification</span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-sm text-gray-400 mb-4">Set up your payment details to receive withdrawals. This information is required before you can request payouts.</p>
                    
                    <?php if (!($agency['payment_info_verified'] ?? 0)): ?>
                    <div class="mb-4 p-3 bg-amber-900/50 border border-amber-600/50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="text-amber-200 text-sm font-medium">Payment verification required for withdrawals</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo base_url('/travel/profile/payment-info'); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Preferred Payment Method</label>
                            <select name="payout_method" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required onchange="togglePaymentFields(this.value)">
                                <option value="">Select payment method</option>
                                <option value="bank_transfer" <?php echo ($agency['payout_method'] ?? '') === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                <option value="mpesa" <?php echo ($agency['payout_method'] ?? '') === 'mpesa' ? 'selected' : ''; ?>>M-Pesa</option>
                                <option value="paypal" <?php echo ($agency['payout_method'] ?? '') === 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                                <option value="other" <?php echo ($agency['payout_method'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <!-- Bank Transfer Fields -->
                        <div id="bank_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: <?php echo ($agency['payout_method'] ?? '') === 'bank_transfer' ? 'grid' : 'none'; ?>;">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Bank Name</label>
                                <input type="text" name="bank_name" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['bank_name'] ?? ''); ?>" placeholder="e.g., Equity Bank">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Account Name</label>
                                <input type="text" name="bank_account_name" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['bank_account_name'] ?? ''); ?>" placeholder="Account holder name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Account Number</label>
                                <input type="text" name="bank_account_number" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['bank_account_number'] ?? ''); ?>" placeholder="Account number">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Bank Code</label>
                                <input type="text" name="bank_code" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['bank_code'] ?? ''); ?>" placeholder="e.g., 068">
                            </div>
                        </div>

                        <!-- M-Pesa Fields -->
                        <div id="mpesa_fields" style="display: <?php echo ($agency['payout_method'] ?? '') === 'mpesa' ? 'block' : 'none'; ?>;">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">M-Pesa Phone Number</label>
                                <input type="text" name="mpesa_phone" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['mpesa_phone'] ?? ''); ?>" placeholder="+254 700 000 000">
                                <div class="text-xs text-gray-500 mt-1">Include country code (e.g., +254 for Kenya)</div>
                            </div>
                        </div>

                        <!-- PayPal Fields -->
                        <div id="paypal_fields" style="display: <?php echo ($agency['payout_method'] ?? '') === 'paypal' ? 'block' : 'none'; ?>;">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">PayPal Email</label>
                                <input type="email" name="paypal_email" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['paypal_email'] ?? ''); ?>" placeholder="your@email.com">
                            </div>
                        </div>

                        <!-- Other Payment Fields -->
                        <div id="other_fields" style="display: <?php echo ($agency['payout_method'] ?? '') === 'other' ? 'block' : 'none'; ?>;">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Payment Details</label>
                                <textarea name="other_payment_details" class="w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Provide detailed payment instructions..."><?php echo htmlspecialchars($agency['other_payment_details'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                            <div class="text-sm text-gray-400">
                                Last updated: <?php echo $agency['payment_info_updated_at'] ? date('M j, Y', strtotime($agency['payment_info_updated_at'])) : 'Never'; ?>
                            </div>
                            <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                                Save Payment Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePaymentFields(method) {
    // Hide all fields
    document.getElementById('bank_fields').style.display = 'none';
    document.getElementById('mpesa_fields').style.display = 'none';
    document.getElementById('paypal_fields').style.display = 'none';
    document.getElementById('other_fields').style.display = 'none';
    
    // Show relevant fields
    switch(method) {
        case 'bank_transfer':
            document.getElementById('bank_fields').style.display = 'grid';
            break;
        case 'mpesa':
            document.getElementById('mpesa_fields').style.display = 'block';
            break;
        case 'paypal':
            document.getElementById('paypal_fields').style.display = 'block';
            break;
        case 'other':
            document.getElementById('other_fields').style.display = 'block';
            break;
    }
}
</script>

<script src="<?php echo base_url('/app/Views/travel/shared/mobile_sidebar.js'); ?>"></script>
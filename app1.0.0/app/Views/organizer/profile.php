<?php /** @var array $organizer */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">My Profile</h1>
    <?php if ($msg = flash_get('success')): ?><div class="alert-success px-4 py-3 mb-3"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <?php if ($msg = flash_get('error')): ?><div class="alert-error px-4 py-3 mb-3"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('/organizer/profile'); ?>" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <div class="flex items-center gap-4">
            <img src="<?php echo base_url($organizer['avatar_path'] ?? '../logo.png'); ?>" class="h-16 w-16 rounded-full object-cover" alt="avatar">
            <input type="file" name="avatar" accept="image/*" class="input">
        </div>
        <div>
            <label class="block text-sm mb-1">Full Name</label>
            <input class="input" name="full_name" value="<?php echo htmlspecialchars($organizer['full_name']); ?>" required>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($organizer['email']); ?>" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Phone</label>
                <div class="flex items-center gap-2">
                    <input class="input" name="phone" value="<?php echo htmlspecialchars($organizer['phone']); ?>">
                    <button formaction="<?php echo base_url('/organizer/profile/verify-phone'); ?>" class="btn btn-secondary">Send OTP</button>
                </div>
                <div class="text-xs text-gray-400 mt-1">Current verification: <?php echo empty($organizer['phone_verified_at']) ? 'Not verified' : 'Verified'; ?></div>
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1">New Password (leave blank to keep)</label>
            <input class="input" type="password" name="password" placeholder="••••••••">
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
    <form method="post" action="<?php echo base_url('/organizer/profile/verify-phone/confirm'); ?>" class="mt-4 card p-6">
        <?php echo csrf_field(); ?>
        <label class="block text-sm mb-1">Enter OTP to verify phone</label>
        <div class="flex items-center gap-2">
            <input class="input" name="otp" maxlength="6" placeholder="6-digit code">
            <button class="btn btn-secondary">Verify</button>
        </div>
    </form>

    <!-- Payment Information Section -->
    <div class="mt-8">
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <h2 class="text-xl font-semibold">Payment Information</h2>
                <?php if ($organizer['payment_info_verified'] ?? 0): ?>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Verified</span>
                <?php else: ?>
                    <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded-full">Pending Verification</span>
                <?php endif; ?>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">Set up your payment details to receive withdrawals. This information is required before you can request payouts.</p>
            
            <?php if (!($organizer['payment_info_verified'] ?? 0)): ?>
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="text-amber-800 text-sm font-medium">Payment verification required for withdrawals</span>
                </div>
            </div>
            <?php endif; ?>

            <form method="post" action="<?php echo base_url('/organizer/profile/payment-info'); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Preferred Payment Method</label>
                    <select name="payout_method" class="input" required onchange="togglePaymentFields(this.value)">
                        <option value="">Select payment method</option>
                        <option value="bank_transfer" <?php echo ($organizer['payout_method'] ?? '') === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="mpesa" <?php echo ($organizer['payout_method'] ?? '') === 'mpesa' ? 'selected' : ''; ?>>M-Pesa</option>
                        <option value="paypal" <?php echo ($organizer['payout_method'] ?? '') === 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                        <option value="other" <?php echo ($organizer['payout_method'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <!-- Bank Transfer Fields -->
                <div id="bank_fields" class="grid md:grid-cols-2 gap-4" style="display: <?php echo ($organizer['payout_method'] ?? '') === 'bank_transfer' ? 'grid' : 'none'; ?>;">
                    <div>
                        <label class="block text-sm font-medium mb-2">Bank Name</label>
                        <input type="text" name="bank_name" class="input" value="<?php echo htmlspecialchars($organizer['bank_name'] ?? ''); ?>" placeholder="e.g., Equity Bank">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Account Name</label>
                        <input type="text" name="bank_account_name" class="input" value="<?php echo htmlspecialchars($organizer['bank_account_name'] ?? ''); ?>" placeholder="Account holder name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Account Number</label>
                        <input type="text" name="bank_account_number" class="input" value="<?php echo htmlspecialchars($organizer['bank_account_number'] ?? ''); ?>" placeholder="Account number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Bank Code</label>
                        <input type="text" name="bank_code" class="input" value="<?php echo htmlspecialchars($organizer['bank_code'] ?? ''); ?>" placeholder="e.g., 068">
                    </div>
                </div>

                <!-- M-Pesa Fields -->
                <div id="mpesa_fields" style="display: <?php echo ($organizer['payout_method'] ?? '') === 'mpesa' ? 'block' : 'none'; ?>;">
                    <div>
                        <label class="block text-sm font-medium mb-2">M-Pesa Phone Number</label>
                        <input type="text" name="mpesa_phone" class="input" value="<?php echo htmlspecialchars($organizer['mpesa_phone'] ?? ''); ?>" placeholder="+254 700 000 000">
                        <div class="text-xs text-gray-500 mt-1">Include country code (e.g., +254 for Kenya)</div>
                    </div>
                </div>

                <!-- PayPal Fields -->
                <div id="paypal_fields" style="display: <?php echo ($organizer['payout_method'] ?? '') === 'paypal' ? 'block' : 'none'; ?>;">
                    <div>
                        <label class="block text-sm font-medium mb-2">PayPal Email</label>
                        <input type="email" name="paypal_email" class="input" value="<?php echo htmlspecialchars($organizer['paypal_email'] ?? ''); ?>" placeholder="your@email.com">
                    </div>
                </div>

                <!-- Other Payment Fields -->
                <div id="other_fields" style="display: <?php echo ($organizer['payout_method'] ?? '') === 'other' ? 'block' : 'none'; ?>;">
                    <div>
                        <label class="block text-sm font-medium mb-2">Payment Details</label>
                        <textarea name="other_payment_details" class="textarea" rows="3" placeholder="Provide detailed payment instructions..."><?php echo htmlspecialchars($organizer['other_payment_details'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        Last updated: <?php echo $organizer['payment_info_updated_at'] ? date('M j, Y', strtotime($organizer['payment_info_updated_at'])) : 'Never'; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Payment Information</button>
                </div>
            </form>
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
</div>



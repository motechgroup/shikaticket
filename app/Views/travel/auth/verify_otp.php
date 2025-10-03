<?php $pageTitle = 'Verify Phone - OTP'; ?>
<div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <h2 class="text-center text-2xl font-extrabold text-white">Verify Your Phone</h2>
        <?php if ($msg = flash_get('error')): ?>
            <div class="p-3 rounded bg-red-900 text-red-200 text-sm"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($msg = flash_get('success')): ?>
            <div class="p-3 rounded bg-green-900 text-green-200 text-sm"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo base_url('/travel/verify-otp'); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <label class="block text-sm text-gray-300">Enter the 6-digit code sent to your phone</label>
            <input name="otp" maxlength="6" pattern="\d{6}" required class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-600 bg-gray-900 placeholder-gray-400 text-white rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" placeholder="123456">
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">Verify</button>
                <a href="<?php echo base_url('/travel/dashboard'); ?>" class="btn btn-secondary flex-1 text-center">Skip for now</a>
            </div>
        </form>
        <div class="text-center">
            <p class="text-sm text-gray-400">You can verify later. Posting/listing destinations requires a verified phone.</p>
            <a href="<?php echo base_url('/travel/login'); ?>" class="inline-block mt-2 text-sm text-gray-300 hover:text-gray-400">Back to login</a>
        </div>
    </div>
</div>


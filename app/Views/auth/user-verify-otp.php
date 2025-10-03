<div class="max-w-md mx-auto px-4 py-10">
    <div class="card p-6">
        <h1 class="text-2xl font-semibold mb-4">Enter OTP</h1>
        <form method="post" action="<?php echo base_url('/login-otp/verify'); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <label class="block text-sm font-medium">Code</label>
            <input type="text" name="otp" maxlength="6" pattern="\d{6}" placeholder="6-digit code" class="w-full px-3 py-2 border border-gray-600 bg-gray-800 text-white rounded" required>
            <button type="submit" class="btn btn-primary w-full">Verify & Login</button>
        </form>
        <form method="post" action="<?php echo base_url('/login-otp/resend'); ?>" class="mt-4">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-secondary w-full">Resend Code</button>
        </form>
    </div>
    <p class="text-center text-sm text-gray-400 mt-3">Sent to: <?php echo htmlspecialchars($_SESSION['tmp_user_phone'] ?? ''); ?></p>
</div>



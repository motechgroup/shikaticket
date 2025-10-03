<div class="max-w-md mx-auto px-4 py-10">
    <div class="card p-6">
        <h1 class="text-2xl font-semibold mb-4">Login with Phone</h1>
        <form method="post" action="<?php echo base_url('/login-otp'); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <label class="block text-sm font-medium">Phone Number</label>
            <input type="tel" name="phone" placeholder="2547XXXXXXXX" class="w-full px-3 py-2 border border-gray-600 bg-gray-800 text-white rounded" required>
            <button type="submit" class="btn btn-primary w-full">Send OTP</button>
        </form>
    </div>
</div>



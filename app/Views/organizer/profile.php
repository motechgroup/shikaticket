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
</div>



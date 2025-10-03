<?php /** @var string $email */ ?>
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Admin Profile</h1>
    <?php if ($msg = flash_get('success')): ?><div class="alert-success px-4 py-3 mb-3"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <form method="post" action="<?php echo base_url('/admin/profile'); ?>" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
        </div>
        <div>
            <label class="block text-sm mb-1">New Password</label>
            <input class="input" type="password" name="password" placeholder="••••••••">
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>



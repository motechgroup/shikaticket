<?php /** @var array $agency */ ?>
<?php $pageTitle = 'Agency Profile'; ob_start(); ?>
<div class="max-w-3xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-semibold mb-6">Agency Profile</h1>

  <?php if (!(int)($agency['phone_verified'] ?? 0)): ?>
  <div class="mb-6 p-4 border border-yellow-600 bg-yellow-900/20 rounded">
    <div class="font-semibold text-yellow-300">Phone verification required</div>
    <p class="text-sm text-yellow-200 mt-1">Verify your phone to unlock destination creation.</p>
    <form method="post" action="<?php echo base_url('/travel/profile/verify-phone'); ?>" class="mt-3">
      <?php echo csrf_field(); ?>
      <button class="btn btn-primary">Send Verification Code</button>
    </form>
    <form method="post" action="<?php echo base_url('/travel/profile/verify-phone/confirm'); ?>" class="mt-3 flex items-center gap-2">
      <?php echo csrf_field(); ?>
      <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" class="input w-40" required>
      <button class="btn btn-secondary">Confirm</button>
    </form>
  </div>
  <?php else: ?>
  <div class="mb-6 p-4 border border-green-700 bg-green-900/20 rounded">
    <div class="text-green-300">Phone verified.</div>
  </div>
  <?php endif; ?>

  <div class="card p-6">
    <form method="post" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm text-gray-400">Company Name</label>
          <input type="text" name="company_name" class="input w-full" value="<?php echo htmlspecialchars($agency['company_name'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Contact Person</label>
          <input type="text" name="contact_person" class="input w-full" value="<?php echo htmlspecialchars($agency['contact_person'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Phone</label>
          <input type="text" name="phone" class="input w-full" value="<?php echo htmlspecialchars($agency['phone'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Website</label>
          <input type="text" name="website" class="input w-full" value="<?php echo htmlspecialchars($agency['website'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">City</label>
          <input type="text" name="city" class="input w-full" value="<?php echo htmlspecialchars($agency['city'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Country</label>
          <input type="text" name="country" class="input w-full" value="<?php echo htmlspecialchars($agency['country'] ?? ''); ?>">
        </div>
      </div>
      <div class="mt-6">
        <label class="block text-sm text-gray-400">Address</label>
        <textarea name="address" class="input w-full" rows="2"><?php echo htmlspecialchars($agency['address'] ?? ''); ?></textarea>
      </div>
      <div class="mt-6">
        <label class="block text-sm text-gray-400">Company Description</label>
        <textarea name="description" class="input w-full" rows="3"><?php echo htmlspecialchars($agency['description'] ?? ''); ?></textarea>
      </div>
      <div class="mt-6">
        <label class="block text-sm text-gray-400">Logo</label>
        <input type="file" name="logo" class="input w-full">
      </div>
      <div class="mt-6">
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/travel.php'; ?>



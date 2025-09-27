<?php /** @var array $agency */ ?>
<?php $pageTitle = 'Agency Profile'; ?>
<div class="max-w-3xl mx-auto px-4 md:px-6 py-6 md:py-10">
  <h1 class="text-xl md:text-2xl font-semibold mb-6 text-white">Agency Profile</h1>

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
      <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" class="w-40 px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required>
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
          <input type="text" name="company_name" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['company_name'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Contact Person</label>
          <input type="text" name="contact_person" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['contact_person'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Phone</label>
          <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['phone'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Website</label>
          <input type="text" name="website" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['website'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">City</label>
          <input type="text" name="city" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['city'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Country</label>
          <input type="text" name="country" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($agency['country'] ?? ''); ?>">
        </div>
      </div>
      <div class="mt-6">
        <label class="block text-sm text-gray-400">Address</label>
        <textarea name="address" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" rows="2"><?php echo htmlspecialchars($agency['address'] ?? ''); ?></textarea>
      </div>
      <div class="mt-6">
        <label class="block text-sm text-gray-400">Company Description</label>
        <textarea name="description" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" rows="3"><?php echo htmlspecialchars($agency['description'] ?? ''); ?></textarea>
      </div>
      <div class="mt-6">
        <label class="block text-sm text-gray-400">Logo</label>
        <input type="file" name="logo" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-600 file:text-white hover:file:bg-red-700 focus:outline-none focus:ring-red-500 focus:border-red-500">
      </div>
      <div class="mt-6">
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>



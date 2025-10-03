<?php
$pageTitle = 'Create Scanner Device';
?>
<div class="max-w-2xl mx-auto px-4 md:px-6 py-6 md:py-10">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
    <h1 class="text-xl md:text-2xl font-semibold text-white">Create Scanner Device</h1>
    <a class="btn btn-secondary w-full sm:w-auto" href="<?php echo base_url('/travel/scanner'); ?>">Back to Devices</a>
  </div>

  <div class="card p-6">
    <form method="post">
      <?php echo csrf_field(); ?>
      
      <div class="mb-6">
        <label class="block text-sm text-gray-400 mb-2">Device Name *</label>
        <input type="text" name="device_name" required 
               class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" 
               placeholder="e.g., Main Entrance Scanner, Check-in Counter">
        <p class="text-xs text-gray-400 mt-1">Give your scanner device a descriptive name</p>
      </div>
      
      <div class="mb-6 p-4 bg-yellow-900 border border-yellow-600 rounded-lg">
        <h3 class="font-semibold text-yellow-300 mb-2">Important:</h3>
        <ul class="text-sm text-yellow-200 space-y-1">
          <li>• A unique device code will be generated automatically</li>
          <li>• Keep the device code secure - it's used to access the scanner</li>
          <li>• You can activate/deactivate devices as needed</li>
          <li>• Each device can scan bookings for your travel agency</li>
        </ul>
      </div>
      
      <div class="flex gap-4">
        <button type="submit" class="btn btn-primary">Create Device</button>
        <a href="<?php echo base_url('/travel/scanner'); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

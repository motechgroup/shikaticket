<?php
/** @var array $devices */
$pageTitle = 'Scanner Devices';
?>
<div class="max-w-6xl mx-auto px-4 md:px-6 py-6 md:py-10">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
    <h1 class="text-xl md:text-2xl font-semibold text-white">Scanner Devices</h1>
    <a href="<?php echo base_url('/travel/scanner/create'); ?>" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto justify-center">
      <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      Add Scanner Device
    </a>
  </div>

  <div class="bg-dark-card border border-gray-800 rounded-lg p-6">
    <?php if (empty($devices)): ?>
      <div class="text-center py-12">
        <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-gray-800 mb-4">
          <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-white mb-2">No scanner devices yet</h3>
        <p class="text-gray-400 mb-6">Create your first scanner device to start verifying travel bookings</p>
        <a href="<?php echo base_url('/travel/scanner/create'); ?>" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
          <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Create Your First Device
        </a>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($devices as $device): ?>
          <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 hover:border-gray-600 transition-colors">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center">
                <div class="h-10 w-10 bg-red-900 rounded-lg flex items-center justify-center mr-3">
                  <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-white text-lg"><?php echo htmlspecialchars($device['device_name']); ?></h3>
                  <p class="text-sm text-gray-400">Scanner Device</p>
                </div>
              </div>
              <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $device['is_active'] ? 'bg-green-900 text-green-300 border border-green-700' : 'bg-red-900 text-red-300 border border-red-700'; ?>">
                <?php echo $device['is_active'] ? 'Active' : 'Inactive'; ?>
              </span>
            </div>
            
            <div class="space-y-3 mb-6">
              <div>
                <label class="text-xs font-medium text-gray-400 uppercase tracking-wide">Device Code</label>
                <div class="mt-1 flex items-center">
                  <div class="font-mono text-sm text-white bg-gray-800 px-3 py-2 rounded-lg border border-gray-700 flex-1">
                    <?php echo htmlspecialchars($device['device_code']); ?>
                  </div>
                  <button onclick="copyToClipboard('<?php echo htmlspecialchars($device['device_code']); ?>')" 
                          class="ml-2 p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
                          title="Copy device code">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                  </button>
                </div>
              </div>
              
              <div>
                <label class="text-xs font-medium text-gray-400 uppercase tracking-wide">Created</label>
                <div class="mt-1 text-sm text-gray-300">
                  <?php echo date('M j, Y \a\t g:i A', strtotime($device['created_at'])); ?>
                </div>
              </div>
            </div>
            
            <div class="flex gap-2">
              <form method="POST" action="<?php echo base_url('/travel/scanner/toggle'); ?>" class="flex-1">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="device_id" value="<?php echo $device['id']; ?>">
                <button type="submit" class="w-full px-3 py-2 text-sm font-medium rounded-lg border transition-colors <?php echo $device['is_active'] ? 'border-yellow-600 text-yellow-300 hover:bg-yellow-900' : 'border-green-600 text-green-300 hover:bg-green-900'; ?>">
                  <?php echo $device['is_active'] ? 'Deactivate' : 'Activate'; ?>
                </button>
              </form>
              
              <form method="POST" action="<?php echo base_url('/travel/scanner/delete'); ?>" class="flex-1" 
                    onsubmit="return confirm('Are you sure you want to delete this scanner device? This action cannot be undone.')">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="device_id" value="<?php echo $device['id']; ?>">
                <button type="submit" class="w-full px-3 py-2 text-sm font-medium rounded-lg border border-red-600 text-red-300 hover:bg-red-900 transition-colors">
                  Delete
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  
  <div class="mt-6 bg-gradient-to-r from-blue-900 to-purple-900 border border-blue-600 rounded-xl p-6">
    <div class="flex items-start">
      <div class="flex-shrink-0">
        <div class="h-10 w-10 bg-blue-800 rounded-lg flex items-center justify-center">
          <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
      <div class="ml-4 flex-1">
        <h3 class="text-lg font-semibold text-blue-200 mb-3">How to use Scanner Devices</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-3">
            <div class="flex items-start">
              <div class="flex-shrink-0 h-6 w-6 bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                <span class="text-xs font-bold text-blue-200">1</span>
              </div>
              <div>
                <p class="text-sm text-blue-100">Create a scanner device and note the device code</p>
              </div>
            </div>
            <div class="flex items-start">
              <div class="flex-shrink-0 h-6 w-6 bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                <span class="text-xs font-bold text-blue-200">2</span>
              </div>
              <div>
                <p class="text-sm text-blue-100">Go to <a href="<?php echo base_url('/scanner/login'); ?>" class="text-blue-300 hover:text-blue-200 font-medium underline">Universal Scanner Login</a></p>
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <div class="flex items-start">
              <div class="flex-shrink-0 h-6 w-6 bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                <span class="text-xs font-bold text-blue-200">3</span>
              </div>
              <div>
                <p class="text-sm text-blue-100">Enter the device code to access the scanner interface</p>
              </div>
            </div>
            <div class="flex items-start">
              <div class="flex-shrink-0 h-6 w-6 bg-blue-800 rounded-full flex items-center justify-center mr-3 mt-0.5">
                <span class="text-xs font-bold text-blue-200">4</span>
              </div>
              <div>
                <p class="text-sm text-blue-100">Scan or enter booking references to verify travel bookings</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = `
            <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        `;
        button.classList.add('text-green-400');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('text-green-400');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
    });
}
</script>

<?php /** @var array $orders */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">My Orders</h1>
		<div class="flex items-center gap-2">
			<div class="relative">
				<button id="confirmPayBtn" class="btn btn-primary relative" title="Click here after completing your payment to retrieve your tickets">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
					</svg>
					Get My Ticket
				</button>
				<div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity duration-300" id="btnTooltip">
					Click after payment
				</div>
			</div>
			<a class="btn btn-secondary" href="<?php echo base_url('/user/dashboard'); ?>">Back to Dashboard</a>
		</div>
	</div>
	
	<!-- Payment Success Instructions -->
	<?php if (flash_get('success') && (strpos(flash_get('success'), 'M-Pesa') !== false || strpos(flash_get('success'), 'Success') !== false)): ?>
	<div class="card p-4 mb-6 bg-blue-900/20 border border-blue-700">
		<div class="flex items-start gap-3">
			<div class="text-blue-400 mt-1">
				<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
				</svg>
			</div>
			<div>
				<h3 class="font-semibold text-blue-300 mb-1">Payment Successful!</h3>
				<p class="text-sm text-blue-200">
					Your payment has been processed successfully. 
					<strong class="text-blue-100">Please click the "Get My Ticket" button above</strong> 
					to retrieve your event tickets. This may take a few moments to process.
				</p>
			</div>
		</div>
	</div>
	<?php endif; ?>
    <div class="card" id="ordersCard">
		<?php if (empty($orders)): ?>
			<div class="p-6 text-gray-400">No orders yet.</div>
		<?php else: ?>
			<?php 
			// Check if there are any pending orders
			$hasPendingOrders = false;
			foreach ($orders as $order) {
				if (strtolower($order['status']) === 'pending') {
					$hasPendingOrders = true;
					break;
				}
			}
			?>
			
			<?php if ($hasPendingOrders): ?>
			<div class="p-4 bg-yellow-900/20 border-l-4 border-yellow-500 mb-4">
				<div class="flex items-start gap-3">
					<div class="text-yellow-400 mt-1">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
						</svg>
					</div>
					<div>
						<h4 class="font-semibold text-yellow-300 mb-1">Action Required</h4>
						<p class="text-sm text-yellow-200">
							You have pending orders that need attention. 
							<strong class="text-yellow-100">Click "Get My Ticket" button above</strong> 
							to complete your ticket purchase and receive your event tickets.
						</p>
					</div>
				</div>
			</div>
			<?php endif; ?>
            <!-- Desktop Table View -->
			<div class="hidden md:block overflow-x-auto">
				<table class="min-w-full text-sm table" id="ordersTable">
					<thead>
						<tr>
							<th class="p-3 text-left">Order #</th>
							<th class="p-3 text-left">Amount</th>
							<th class="p-3 text-left">Currency</th>
							<th class="p-3 text-left">Order Status</th>
							<th class="p-3 text-left">Payment Status</th>
							<th class="p-3 text-left">Date</th>
							<th class="p-3 text-left">Actions</th>
						</tr>
					</thead>
					<tbody>
	                    <?php foreach ($orders as $o): ?>
	                    <tr class="cursor-pointer hover:bg-gray-800/40" data-order-id="<?php echo (int)$o['id']; ?>" data-order-link="<?php echo base_url('/user/orders/show?id='.(int)$o['id']); ?>" data-order-status="<?php echo htmlspecialchars($o['status']); ?>">
	                        <td class="p-3"><a class="link" href="<?php echo base_url('/user/orders/show?id='.(int)$o['id']); ?>">#<?php echo (int)$o['id']; ?></a></td>
							<td class="p-3"><?php echo number_format((float)$o['total_amount'], 2); ?></td>
							<td class="p-3"><?php echo htmlspecialchars($o['currency']); ?></td>
							<td class="p-3"><?php echo htmlspecialchars($o['status']); ?></td>
							<td class="p-3"><?php echo htmlspecialchars($o['payment_status'] ?? 'initiated'); ?></td>
							<td class="p-3"><?php echo htmlspecialchars($o['created_at'] ?? ''); ?></td>
							<td class="p-3">
								<a href="<?php echo base_url('/user/orders/show?id='.(int)$o['id']); ?>" class="btn btn-sm btn-primary">
									<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
									</svg>
									View Ticket
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- Mobile Card View -->
			<div class="md:hidden space-y-4">
				<?php foreach ($orders as $o): ?>
				<div class="card p-4">
					<div class="flex items-center justify-between mb-3">
						<div>
							<h3 class="font-semibold text-lg">
								<a class="link" href="<?php echo base_url('/user/orders/show?id='.(int)$o['id']); ?>">
									Order #<?php echo (int)$o['id']; ?>
								</a>
							</h3>
							<p class="text-sm text-gray-400"><?php echo htmlspecialchars($o['created_at'] ?? ''); ?></p>
						</div>
						<div class="text-right">
							<p class="font-semibold text-lg"><?php echo htmlspecialchars($o['currency']); ?> <?php echo number_format((float)$o['total_amount'], 2); ?></p>
							<div class="flex items-center gap-2 mt-1">
								<span class="text-xs bg-green-900 text-green-300 px-2 py-1 rounded"><?php echo htmlspecialchars($o['status']); ?></span>
								<span class="text-xs bg-blue-900 text-blue-300 px-2 py-1 rounded"><?php echo htmlspecialchars($o['payment_status'] ?? 'initiated'); ?></span>
							</div>
						</div>
					</div>
					<div class="flex justify-end">
						<a href="<?php echo base_url('/user/orders/show?id='.(int)$o['id']); ?>" class="btn btn-primary btn-sm">
							<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
							</svg>
							View Ticket
						</a>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
(function(){
  const table = document.getElementById('ordersTable');
  if(table) {
    // Make rows clickable (delegate only on tbody rows)
    table.addEventListener('click', function(e){
      const row = e.target.closest('tbody tr[data-order-link]');
      if(row){ window.location.href = row.getAttribute('data-order-link'); }
    });
  }
  
  // Get first order ID for polling (works for both desktop and mobile)
  const urlId = new URLSearchParams(window.location.search).get('order_id');
  let focusId = urlId;
  
  if(!focusId) {
    // Try to get from desktop table first
    const rows = table?.querySelectorAll('tbody tr');
    if(rows && rows.length > 0) {
      const newest = rows[0];
      const idText = newest.querySelector('td a')?.textContent || '';
      focusId = idText.replace('#','').trim();
    }
    
    // If no desktop table, try mobile cards
    if(!focusId) {
      const mobileCards = document.querySelectorAll('.md\\:hidden .card a[href*="order_id="]');
      if(mobileCards.length > 0) {
        const href = mobileCards[0].getAttribute('href');
        const match = href.match(/order_id=(\d+)/);
        if(match) focusId = match[1];
      }
    }
  }
  
  if(!focusId) return;
  
  let attempts=0;
  let isPolling = true;
  let pollInterval;
  
  // Mobile-optimized polling function
  function pollPaymentStatus() {
    if (!isPolling) return;
    
    attempts++;
    console.log('Orders page payment status check attempt:', attempts);
    
    // Use fetch with timeout for mobile compatibility
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
    
    fetch('<?php echo base_url('/orders/status'); ?>?id=' + encodeURIComponent(focusId) + '&_t=' + Date.now(), {
      signal: controller.signal,
      cache: 'no-cache',
      headers: {
        'Cache-Control': 'no-cache',
        'Pragma': 'no-cache'
      }
    })
      .then(r => {
        clearTimeout(timeoutId);
        if (!r.ok) throw new Error('Network error: ' + r.status);
        return r.json();
      })
      .then(j => {
        console.log('Orders page payment status response:', j);
        if(j.status === 'paid'){
          isPolling = false;
          clearInterval(pollInterval);
          window.location.href = '<?php echo base_url('/user/orders/show'); ?>?id=' + focusId + '&payment_success=1';
        }
        if(attempts >= 30){
          isPolling = false;
          clearInterval(pollInterval);
          // Fallback reconciliation if callback missed
          fetch('<?php echo base_url('/pay/mpesa/reconcile'); ?>?order_id=' + encodeURIComponent(focusId))
            .then(()=>window.location.reload());
        }
      })
      .catch(error => {
        clearTimeout(timeoutId);
        console.log('Orders page status check failed, attempt:', attempts, 'Error:', error.message);
        
        if(attempts >= 30){
          isPolling = false;
          clearInterval(pollInterval);
          // Fallback reconciliation if callback missed
          fetch('<?php echo base_url('/pay/mpesa/reconcile'); ?>?order_id=' + encodeURIComponent(focusId))
            .then(()=>window.location.reload());
        }
      });
  }
  
  // Start polling with mobile-optimized interval
  pollInterval = setInterval(pollPaymentStatus, 3000); // 3 seconds for mobile
  
  // Handle page visibility changes (mobile browser tab switching)
  document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible' && isPolling) {
      // Page became visible, poll immediately
      setTimeout(pollPaymentStatus, 500);
    }
  });
  
  // Handle online/offline events
  window.addEventListener('online', function() {
    if (isPolling) {
      console.log('Network back online, resuming polling on orders page');
      setTimeout(pollPaymentStatus, 1000);
    }
  });
  
  window.addEventListener('offline', function() {
    console.log('Network offline, polling will continue when back online');
  });
})();
</script>

<script>
// Manual confirmation action + delayed appearance
(function(){
  const btn = document.getElementById('confirmPayBtn');
  const rows = Array.from(document.querySelectorAll('#ordersTable tbody tr'));
  const urlId = new URLSearchParams(window.location.search).get('order_id');
  const newestPending = rows.find(r => (r.getAttribute('data-order-status')||'').toLowerCase() === 'pending');
  const newestAny = rows[0];
  const guessId = (el)=>{ const t = el?.querySelector('td a')?.textContent || ''; return t.replace('#','').trim(); };
  const targetId = urlId || guessId(newestPending) || guessId(newestAny);

  // Show after 6 seconds
  if(btn){ btn.style.display = 'none'; setTimeout(()=>{ btn.style.display='inline-flex'; }, 6000); }

  btn?.addEventListener('click', function(){
    if(!targetId) return;
    btn.disabled = true;
    btn.textContent = 'Checking...';
    const statusUrl = '<?php echo base_url('/orders/status'); ?>?id=' + encodeURIComponent(targetId);
    // Force reconciliation then hard reload to fetch updated status
    fetch('<?php echo base_url('/pay/mpesa/reconcile'); ?>?order_id=' + encodeURIComponent(targetId))
      .then(()=>{
        // Small delay to allow DB update, then redirect to order show page with success
        setTimeout(()=>{ 
          window.location.href = '<?php echo base_url('/user/orders/show'); ?>?id=' + targetId + '&payment_success=1';
        }, 1200);
      })
      .catch(()=>{ window.location.reload(); })
      .finally(()=>{ btn.disabled=false; btn.textContent='Get My Ticket'; });
  });
})();
</script>

<script>
// Enhanced button tooltip and interaction
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('confirmPayBtn');
  const tooltip = document.getElementById('btnTooltip');
  
  // Show tooltip on hover
  if(btn && tooltip){
    btn.addEventListener('mouseenter', () => {
      tooltip.classList.remove('opacity-0');
      tooltip.classList.add('opacity-100');
    });
    btn.addEventListener('mouseleave', () => {
      tooltip.classList.add('opacity-0');
      tooltip.classList.remove('opacity-100');
    });
  }
  
  // Add pulse animation for pending orders
  if(btn) {
    const rows = Array.from(document.querySelectorAll('#ordersTable tbody tr'));
    const hasPending = rows.some(r => (r.getAttribute('data-order-status')||'').toLowerCase() === 'pending');
    
    if(hasPending) {
      // Add pulse effect after button appears
      setTimeout(() => {
        btn.classList.add('animate-pulse');
        setTimeout(() => btn.classList.remove('animate-pulse'), 3000);
      }, 6500); // After button appears (6s + 0.5s)
    }
  }
});
</script>



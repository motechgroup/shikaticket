<?php /** @var array $event */ /** @var array $settings */ /** @var string $userPhone */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="grid md:grid-cols-3 gap-8">
		<div class="md:col-span-1">
			<div class="card overflow-hidden">
				<?php if (!empty($event['poster_path'])): ?>
					<img src="<?php echo base_url($event['poster_path']); ?>" alt="Poster" class="w-full max-h-[28rem] object-contain bg-black">
				<?php else: ?>
					<div class="p-6 text-gray-400">No poster</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="md:col-span-2">
			<h1 class="text-3xl font-semibold"><?php echo htmlspecialchars($event['title']); ?></h1>
			<?php 
			$orgRow = null; 
			try { if (!empty($event['organizer_id'])) { $q=db()->prepare('SELECT id, full_name FROM organizers WHERE id = ?'); $q->execute([(int)$event['organizer_id']]); $orgRow=$q->fetch(); } } catch (\Throwable $e) {}
			?>
			<p class="text-gray-400 mt-1">
				<?php echo htmlspecialchars($event['venue']); ?> • <?php echo htmlspecialchars(($event['event_date'] ?? '') . ' ' . ($event['event_time'] ?? '')); ?>
				<?php if ($orgRow): ?>
					• by <a class="link" href="<?php echo base_url('/organizers/show?id='.(int)$orgRow['id']); ?>"><?php echo htmlspecialchars($orgRow['full_name']); ?></a>
				<?php endif; ?>
			</p>
			<div class="flex flex-wrap gap-2 mt-3">
				<?php $shareUrl = base_url('events/show?id='.(int)$event['id']); $shareText = $event['title'].' - '.$event['venue']; ?>
				<button id="btnShare" class="btn btn-secondary">Share</button>
				<a class="btn btn-secondary" target="_blank" href="https://wa.me/?text=<?php echo rawurlencode($shareText.' '.$shareUrl); ?>">WhatsApp</a>
				<a class="btn btn-secondary" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode($shareUrl); ?>">Facebook</a>
				<a class="btn btn-secondary" target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode($shareUrl); ?>&text=<?php echo rawurlencode($shareText); ?>">X</a>
				<button id="btnCopy" class="btn btn-secondary">Copy link</button>
			</div>
			<script>
			(function(){
				var shareData = { title: <?php echo json_encode($event['title']); ?>, text: <?php echo json_encode($shareText); ?>, url: <?php echo json_encode($shareUrl); ?> };
				var b = document.getElementById('btnShare');
				if(b){ b.addEventListener('click', function(){ if(navigator.share){ navigator.share(shareData).catch(function(){}); } else { navigator.clipboard && navigator.clipboard.writeText(shareData.url); alert('Link copied'); } }); }
				var c = document.getElementById('btnCopy'); if(c){ c.addEventListener('click', function(){ navigator.clipboard && navigator.clipboard.writeText(shareData.url); alert('Link copied'); }); }
			})();
			</script>
			<div class="card p-4 mt-4">
				<h2 class="font-semibold mb-2">About</h2>
				<p class="text-gray-300 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($event['description'] ?? '')); ?></p>
				<?php if (!empty($event['dress_code'])): ?>
					<p class="mt-3"><span class="badge">Dress code</span> <?php echo htmlspecialchars($event['dress_code']); ?></p>
				<?php endif; ?>
				<?php if (!empty($event['lineup'])): ?>
					<p class="mt-2"><span class="badge">Lineup</span> <?php echo htmlspecialchars($event['lineup']); ?></p>
				<?php endif; ?>
			</div>
			<!-- Ticket Selection Section (Clean & Simple) -->
			<div class="card p-6 mt-4">
				<h2 class="text-xl font-semibold text-white mb-4">Select Ticket Type</h2>
				<form method="post" action="<?php echo base_url('/orders'); ?>" class="space-y-4">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
					<?php
					$tiers = [
						['name' => 'Early Bird', 'price' => $event['early_bird_price'] ?? null, 'subtitle' => $event['early_bird_until'] ? ('Until ' . $event['early_bird_until']) : ''],
						['name' => 'Regular', 'price' => $event['regular_price'] ?? null, 'subtitle' => ''],
						['name' => 'VIP', 'price' => $event['vip_price'] ?? null, 'subtitle' => ''],
						['name' => 'VVIP', 'price' => $event['vvip_price'] ?? null, 'subtitle' => ''],
						['name' => 'Group', 'price' => $event['group_price'] ?? null, 'subtitle' => $event['group_size'] ? ($event['group_size'] . ' people') : ''],
					];
					foreach ($tiers as $key => $t) {
						if ($t['price'] === null || $t['price'] === '') continue;
						$val = strtolower(str_replace(' ', '_', $t['name']));
						echo '<label class="flex items-center p-3 border border-gray-600 rounded-lg cursor-pointer hover:border-red-500">';
						echo '<input type="radio" name="tier" value="' . $val . '" required class="mr-3">';
						echo '<div class="flex-1">';
						echo '<div class="font-medium text-white">' . htmlspecialchars($t['name']) . '</div>';
						if (!empty($t['subtitle'])) echo '<div class="text-sm text-gray-400">' . htmlspecialchars($t['subtitle']) . '</div>';
						echo '</div>';
						echo '<div class="text-red-400 font-bold text-lg">' . htmlspecialchars($event['currency']) . ' ' . number_format((float)$t['price'], 2) . '</div>';
						echo '</label>';
					}
					?>
					<!-- Payment Information Section (Clean & Simple) -->
					<div class="mt-6">
						<h3 class="text-lg font-semibold text-white mb-4">Payment Method</h3>
						
						<!-- Payment Status Section (Auto-checking) - MOVED TO TOP FOR MOBILE VISIBILITY -->
						<div id="paymentStatusSection" class="mb-6 hidden">
							<div class="bg-blue-900/20 border border-blue-600/30 rounded-lg p-4">
								<div class="flex items-center gap-3 mb-4">
									<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-400"></div>
									<div>
										<h3 class="font-semibold text-blue-300">Processing Payment</h3>
										<p class="text-sm text-blue-200">Please complete your M-Pesa payment on your phone...</p>
									</div>
								</div>
								<div class="text-xs text-blue-300 mb-2">
									<i>Waiting for payment confirmation. This may take up to 2 minutes.</i>
								</div>
								<!-- Mobile-specific notice -->
								<div class="bg-yellow-900/20 border border-yellow-600/30 rounded p-2 text-xs text-yellow-200">
									<strong>📱 Mobile Users:</strong> Keep this page open and visible. Don't switch to other apps or minimize the browser.
								</div>
							</div>
						</div>

						<!-- Manual Confirmation Section - ALSO MOVED TO TOP -->
						<div id="manualConfirmSection" class="mb-6 hidden">
							<div class="bg-yellow-900/20 border border-yellow-600/30 rounded-lg p-4">
								<div class="flex items-center gap-3 mb-4">
									<svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
									</svg>
									<div>
										<h3 class="font-semibold text-yellow-300">Payment Taking Longer?</h3>
										<p class="text-sm text-yellow-200">If you've completed payment but still see this message, click below to confirm manually.</p>
									</div>
								</div>
								<button id="manualConfirmBtn" class="btn btn-primary text-sm">
									<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
									</svg>
									Confirm Payment & View Tickets
								</button>
							</div>
						</div>
						
						<div class="space-y-3">
							<?php if (!empty($settings['mpesa'])): ?>
							<label class="flex items-center p-3 border border-gray-600 rounded-lg cursor-pointer hover:border-red-500">
								<input type="radio" name="gateway" value="mpesa" required class="mr-3" checked>
								<div class="flex items-center gap-3">
									<div class="w-8 h-8 bg-green-600 rounded flex items-center justify-center text-white font-bold">M</div>
									<div>
										<div class="font-medium text-white">M-Pesa</div>
										<div class="text-sm text-gray-400">Pay with your M-Pesa mobile money</div>
									</div>
								</div>
							</label>
							<?php endif; ?>
							
							<?php if (!empty($settings['flutterwave'])): ?>
							<label class="flex items-center p-3 border border-gray-600 rounded-lg cursor-pointer hover:border-red-500">
								<input type="radio" name="gateway" value="flutterwave" required class="mr-3">
								<div class="flex items-center gap-3">
									<div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold">F</div>
									<div>
										<div class="font-medium text-white">Flutterwave</div>
										<div class="text-sm text-gray-400">Pay with card, bank transfer, or mobile money</div>
									</div>
								</div>
							</label>
							<?php endif; ?>
							
							<?php if (!empty($settings['paypal'])): ?>
							<label class="flex items-center p-3 border border-gray-600 rounded-lg cursor-pointer hover:border-red-500">
								<input type="radio" name="gateway" value="paypal" required class="mr-3">
								<div class="flex items-center gap-3">
									<div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center text-white font-bold">P</div>
									<div>
										<div class="font-medium text-white">PayPal</div>
										<div class="text-sm text-gray-400">Pay with PayPal account</div>
									</div>
								</div>
							</label>
							<?php endif; ?>
						</div>
					</div>
					
					<!-- M-Pesa Phone Number (shown when M-Pesa is selected) -->
					<div id="mpesa-phone" class="mt-4">
						<label class="block text-sm font-medium text-white mb-2">M-Pesa Phone Number</label>
						<input type="tel" name="msisdn" id="phoneInput" placeholder="254712345678" class="w-full px-3 py-2 border border-gray-600 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" style="color: white !important; background-color: #1f2937 !important;" value="<?php echo htmlspecialchars($userPhone); ?>" autocomplete="tel">
						<div class="text-sm text-gray-400 mt-1">Enter your M-Pesa registered phone number (automatically converts to 254 format)</div>
					</div>
					
					<!-- Quantity Selection (Simple) -->
					<div class="mt-4">
						<label class="block text-sm font-medium text-white mb-2">Number of Tickets</label>
						<div class="flex items-center gap-3">
							<button type="button" onclick="decreaseQuantity()" class="w-10 h-10 bg-gray-600 hover:bg-red-600 rounded flex items-center justify-center text-white font-bold transition-colors">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
								</svg>
							</button>
							<input type="number" name="quantity" id="quantityInput" value="1" min="1" class="w-20 text-center border border-gray-600 bg-gray-800 text-white rounded-md px-3 py-2" style="color: white !important; background-color: #1f2937 !important;">
							<button type="button" onclick="increaseQuantity()" class="w-10 h-10 bg-gray-600 hover:bg-red-600 rounded flex items-center justify-center text-white font-bold transition-colors">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
								</svg>
							</button>
						</div>
					</div>

					<!-- Terms and Conditions -->
					<div class="flex items-start gap-3 mt-4">
						<input type="checkbox" id="terms" required class="mt-1">
						<label for="terms" class="text-sm text-gray-300">
							I agree to the <a href="<?php echo base_url('/page?slug=terms-and-conditions'); ?>" class="link" target="_blank">Terms and Conditions</a> 
							and <a href="<?php echo base_url('/page?slug=privacy-policy'); ?>" class="link" target="_blank">Privacy Policy</a>
						</label>
					</div>

					<!-- Submit Button -->
					<button type="submit" class="btn btn-primary w-full text-lg py-3 mt-4" <?php echo (!empty($settings['mpesa']) || !empty($settings['paypal']) || !empty($settings['flutterwave'])) ? '' : 'disabled title="Enable gateways in Admin > Settings"'; ?> onclick="formatPhoneBeforeSubmit()">
						Proceed to Payment
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const paymentMethods = document.querySelectorAll('input[name="gateway"]');
  const mpesaPhone = document.getElementById('mpesa-phone');
  
  // Set initial state based on checked payment method
  const checkedMethod = document.querySelector('input[name="gateway"]:checked');
  if (checkedMethod && checkedMethod.value === 'mpesa') {
    mpesaPhone.style.display = 'block';
    document.querySelector('input[name="msisdn"]').required = true;
  }
  
  paymentMethods.forEach(method => {
    method.addEventListener('change', function() {
      if (this.value === 'mpesa') {
        mpesaPhone.style.display = 'block';
        document.querySelector('input[name="msisdn"]').required = true;
      } else {
        mpesaPhone.style.display = 'none';
        document.querySelector('input[name="msisdn"]').required = false;
      }
    });
  });
  
  // Phone number formatting for M-Pesa (automatic 254 conversion)
  const phoneInput = document.getElementById('phoneInput');
  if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
      
      // Auto-format Kenyan phone numbers for M-Pesa
      if (value.length > 0) {
        if (value.startsWith('254')) {
          // Already has country code - keep as is
          value = value;
        } else if (value.startsWith('0')) {
          // Local format starting with 0, convert to 254
          value = '254' + value.substring(1);
        } else if (value.length >= 9) {
          // Assume it's a local number without 0, add 254
          value = '254' + value;
        }
      }
      
      e.target.value = value;
    });
    
    // Format on blur - ensure it's in 254 format
    phoneInput.addEventListener('blur', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      
      // Convert to 254 format for M-Pesa
      if (value.length === 9 && !value.startsWith('254')) {
        e.target.value = '254' + value;
      } else if (value.length === 10 && value.startsWith('0')) {
        e.target.value = '254' + value.substring(1);
      } else if (value.length === 12 && value.startsWith('254')) {
        e.target.value = value; // Already correct
      }
    });
    
    // Format initial value if it exists
    if (phoneInput.value) {
      let initialValue = phoneInput.value.replace(/\D/g, '');
      if (initialValue.length === 9 && !initialValue.startsWith('254')) {
        phoneInput.value = '254' + initialValue;
      } else if (initialValue.length === 10 && initialValue.startsWith('0')) {
        phoneInput.value = '254' + initialValue.substring(1);
      }
    }
  }
});

// Quantity control functions
function increaseQuantity() {
  const input = document.getElementById('quantityInput');
  const currentValue = parseInt(input.value) || 1;
  input.value = currentValue + 1;
}

function decreaseQuantity() {
  const input = document.getElementById('quantityInput');
  const currentValue = parseInt(input.value) || 1;
  if (currentValue > 1) {
    input.value = currentValue - 1;
  }
}

// Format phone number before form submission
function formatPhoneBeforeSubmit() {
  const phoneInput = document.getElementById('phoneInput');
  if (phoneInput && phoneInput.value) {
    let value = phoneInput.value.replace(/\D/g, '');
    
    // Ensure it's in 254 format for M-Pesa
    if (value.length === 9 && !value.startsWith('254')) {
      phoneInput.value = '254' + value;
    } else if (value.length === 10 && value.startsWith('0')) {
      phoneInput.value = '254' + value.substring(1);
    } else if (value.length === 12 && value.startsWith('254')) {
      phoneInput.value = value; // Already correct
    }
    
    console.log('Phone number formatted for submission:', phoneInput.value);
  }
}

// Auto-payment status checking (mobile-optimized with multiple strategies)
function startPaymentStatusPolling() {
  // Get the most recent pending order ID from the current session
  let orderId = null;
  
  // Try to get order ID from URL parameters first
  const urlParams = new URLSearchParams(window.location.search);
  orderId = urlParams.get('order_id');
  
  // If no order ID in URL, we'll need to get it from the orders page
  if (!orderId) {
    // Redirect to orders page to start polling
    window.location.href = '<?php echo base_url('/user/orders'); ?>';
    return;
  }
  
  const statusSection = document.getElementById('paymentStatusSection');
  const manualSection = document.getElementById('manualConfirmSection');

  if (statusSection) {
    statusSection.classList.remove('hidden');
  }

  // Show manual confirmation button immediately for mobile users
  setTimeout(() => {
    if (manualSection) {
      manualSection.classList.remove('hidden');
      console.log('Manual confirmation button shown for mobile users (events)');
    }
  }, 3000); // Show after 3 seconds

  let attempts = 0;
  let pollInterval;
  let isPolling = true;
  let lastPollTime = Date.now();
  
  // Aggressive mobile-optimized polling function
  function pollPaymentStatus() {
    if (!isPolling) return;
    
    attempts++;
    lastPollTime = Date.now();
    console.log('Events payment status check attempt:', attempts, 'Time:', new Date().toLocaleTimeString());
    
    // Use multiple strategies for mobile compatibility
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 8000); // Shorter timeout for mobile
    
    // Add more aggressive cache busting for mobile
    const cacheBuster = Date.now() + '_' + Math.random();
    
    fetch('<?php echo base_url('/orders/status'); ?>?id=' + orderId + '&_t=' + cacheBuster, {
      signal: controller.signal,
      cache: 'no-cache',
      headers: {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0'
      }
    })
      .then(r => {
        clearTimeout(timeoutId);
        if (!r.ok) throw new Error('Network error: ' + r.status);
        return r.json();
      })
      .then(j => {
        console.log('Events payment status response:', j);
        if (j.status === 'paid') {
          isPolling = false;
          clearInterval(pollInterval);
          console.log('Events payment confirmed! Redirecting...');
          window.location.href = '<?php echo base_url('/user/orders/show'); ?>?id=' + orderId + '&payment_success=1';
        } else if (attempts >= 20) { // Reduced from 30 to 20 for mobile
          isPolling = false;
          clearInterval(pollInterval);
          console.log('Events polling timeout reached, showing manual confirmation');
          showManualConfirmation();
        }
      })
      .catch(error => {
        clearTimeout(timeoutId);
        console.log('Events status check failed, attempt:', attempts, 'Error:', error.message);
        
        // If too many failures, show manual confirmation
        if (attempts >= 20) {
          isPolling = false;
          clearInterval(pollInterval);
          showManualConfirmation();
        }
      });
  }
  
  // Start polling with shorter intervals for mobile (2 seconds)
  pollInterval = setInterval(pollPaymentStatus, 2000);
  
  // Poll immediately
  setTimeout(pollPaymentStatus, 500);
  
  // Handle page visibility changes (mobile browser tab switching)
  document.addEventListener('visibilitychange', function() {
    console.log('Events page visibility changed:', document.visibilityState);
    if (document.visibilityState === 'visible' && isPolling) {
      // Page became visible, poll immediately and check if we missed anything
      const timeSinceLastPoll = Date.now() - lastPollTime;
      console.log('Events page became visible, time since last poll:', timeSinceLastPoll);
      if (timeSinceLastPoll > 5000) {
        console.log('Long gap detected, polling immediately');
        setTimeout(pollPaymentStatus, 100);
      }
    }
  });
  
  // Handle online/offline events
  window.addEventListener('online', function() {
    if (isPolling) {
      console.log('Network back online, resuming polling (events)');
      setTimeout(pollPaymentStatus, 500);
    }
  });
  
  window.addEventListener('offline', function() {
    console.log('Network offline, polling will continue when back online');
  });
  
  // Additional mobile-specific event listeners
  window.addEventListener('focus', function() {
    if (isPolling) {
      console.log('Window focused, checking payment status (events)');
      setTimeout(pollPaymentStatus, 500);
    }
  });
  
  // Keep polling active on mobile by using multiple timers
  setInterval(() => {
    if (isPolling && (Date.now() - lastPollTime) > 10000) {
      console.log('Backup poll triggered (mobile safety - events)');
      pollPaymentStatus();
    }
  }, 5000);
}

function showManualConfirmation() {
  const statusSection = document.getElementById('paymentStatusSection');
  const manualSection = document.getElementById('manualConfirmSection');

  if (statusSection) {
    statusSection.classList.add('hidden');
  }
  if (manualSection) {
    manualSection.classList.remove('hidden');
  }
}

// Check if we should start payment status polling
document.addEventListener('DOMContentLoaded', function() {
  // Check if this is a return from M-Pesa payment (URL parameter)
  const urlParams = new URLSearchParams(window.location.search);
  const mpesaPayment = urlParams.get('mpesa_payment');
  
  console.log('Events checkout page loaded');
  console.log('URL params:', window.location.search);
  console.log('mpesa_payment parameter:', mpesaPayment);
  
  if (mpesaPayment === '1') {
    console.log('Starting automatic payment status polling for events...');
    // Show automatic payment status checking
    startPaymentStatusPolling();
  } else {
    console.log('No mpesa_payment parameter found, not starting automatic polling');
  }
  
  // Manual confirmation button handler
  const manualBtn = document.getElementById('manualConfirmBtn');
  if (manualBtn) {
    manualBtn.addEventListener('click', function() {
      // Get the most recent order ID
      const urlParams = new URLSearchParams(window.location.search);
      let orderId = urlParams.get('order_id');
      
      if (!orderId) {
        // Redirect to orders page to get the order ID
        window.location.href = '<?php echo base_url('/user/orders'); ?>';
        return;
      }
      
      // Show loading state
      manualBtn.disabled = true;
      manualBtn.innerHTML = `
        <div class="flex items-center justify-center gap-2">
          <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
          <span>Checking...</span>
        </div>
      `;
      
      // Force reconciliation (same as orders page)
      fetch('<?php echo base_url('/pay/mpesa/reconcile'); ?>?order_id=' + encodeURIComponent(orderId))
        .then(() => {
          // Small delay to allow DB update, then redirect to order show page with success
          setTimeout(() => { 
            window.location.href = '<?php echo base_url('/user/orders/show'); ?>?id=' + orderId + '&payment_success=1';
          }, 1200);
        })
        .catch(() => {
          // On error, just reload to show updated status
          window.location.reload();
        })
        .finally(() => {
          manualBtn.disabled = false;
          manualBtn.innerHTML = 'Confirm Payment & View Tickets';
        });
    });
  }
});
</script>

<?php
// JSON-LD Event schema for SEO (minimal but valid)
try {
    $ev = $event ?? [];
    $name = $ev['title'] ?? '';
    $startDate = trim(($ev['event_date'] ?? '') . (empty($ev['event_time']) ? '' : ('T' . $ev['event_time'])));
    $locationName = $ev['venue'] ?? '';
    // Pick a representative price (lowest available)
    $prices = [];
    foreach (['early_bird_price','regular_price','price','vip_price','vvip_price','group_price'] as $k) {
        if (isset($ev[$k]) && $ev[$k] !== null && $ev[$k] !== '') { $prices[] = (float)$ev[$k]; }
    }
    $price = !empty($prices) ? min($prices) : 0.0;
    $currency = $ev['currency'] ?? 'KES';
    $image = !empty($ev['poster_path']) ? base_url($ev['poster_path']) : base_url('logo.png');
    $url = base_url('/events/show?id='.(int)($ev['id'] ?? 0));
    $orgName = '';
    try { if (!empty($ev['organizer_id'])) { $q=db()->prepare('SELECT full_name FROM organizers WHERE id = ?'); $q->execute([(int)$ev['organizer_id']]); $orgName = $q->fetch()['full_name'] ?? ''; } } catch (\Throwable $e) {}
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $name,
        'startDate' => $startDate,
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'eventStatus' => 'https://schema.org/EventScheduled',
        'url' => $url,
        'image' => [$image],
        'location' => [
            '@type' => 'Place',
            'name' => $locationName
        ],
        'organizer' => [
            '@type' => 'Organization',
            'name' => $orgName
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => number_format($price, 2, '.', ''),
            'priceCurrency' => $currency,
            'availability' => 'https://schema.org/InStock',
            'url' => $url
        ]
    ];
    echo "\n<script type=\"application/ld+json\">" . json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . "</script>\n";
} catch (\Throwable $e) {}
?>

<?php
// Breadcrumbs JSON-LD for event page
try {
    $crumbs = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => base_url('/')
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Events',
                'item' => base_url('/events')
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $event['title'] ?? 'Event',
                'item' => base_url('/events/show?id='.(int)($event['id'] ?? 0))
            ]
        ]
    ];
    echo "\n<script type=\"application/ld+json\">" . json_encode($crumbs, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . "</script>\n";
} catch (\Throwable $e) {}
?>



<?php /** @var array $booking */ /** @var array $user */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
  <!-- Breadcrumb -->
  <nav class="mb-6">
    <a href="<?php echo base_url('/travel'); ?>" class="link">Travel</a>
    <span class="text-gray-400 mx-2">/</span>
    <a href="<?php echo base_url('/travel/destination?id=' . $booking['destination_id']); ?>" class="link"><?php echo htmlspecialchars($booking['destination_title']); ?></a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-400">Checkout</span>
  </nav>

  <h1 class="text-3xl font-bold mb-8">Complete Your Booking</h1>

  <div class="grid lg:grid-cols-2 gap-8">
    <!-- Booking Summary -->
    <div class="card p-6">
      <h2 class="text-xl font-semibold mb-4">Booking Summary</h2>
      
      <div class="space-y-4">
        <div>
          <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($booking['destination_title']); ?></h3>
          <div class="text-gray-400"><?php echo htmlspecialchars($booking['destination']); ?></div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-gray-400">Departure Date:</span>
            <div class="font-medium"><?php echo date('M j, Y', strtotime($booking['departure_date'])); ?></div>
          </div>
          <div>
            <span class="text-gray-400">Departure Location:</span>
            <div class="font-medium"><?php echo htmlspecialchars($booking['departure_location'] ?? 'TBA'); ?></div>
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-gray-400">Travel Agency:</span>
            <div class="font-medium"><?php echo htmlspecialchars($booking['company_name']); ?></div>
          </div>
          <div>
            <span class="text-gray-400">Participants:</span>
            <div class="font-medium"><?php echo (int)$booking['participants_count']; ?> person(s)</div>
          </div>
        </div>
        
        <hr class="border-gray-700">
        
        <div class="flex justify-between items-center">
          <span class="text-gray-400">Price per person:</span>
          <span><?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format((float)$booking['total_amount'] / (int)$booking['participants_count'], 2); ?></span>
        </div>
        
        <div class="flex justify-between items-center">
          <span class="text-gray-400">Participants:</span>
          <span><?php echo (int)$booking['participants_count']; ?></span>
        </div>
        
        <div class="flex justify-between items-center text-lg font-semibold">
          <span>Total Amount:</span>
          <span><?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format((float)$booking['total_amount'], 2); ?></span>
        </div>
      </div>
    </div>

    <!-- Payment Form -->
    <div class="card p-6">
      <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
      
      <!-- Payment Status Section (Auto-checking like events) - MOVED TO TOP FOR MOBILE VISIBILITY -->
      <div id="paymentStatusSection" class="mb-6 hidden">
        <div class="bg-blue-900/20 border border-blue-600/30 rounded-lg p-4">
          <div class="flex items-center gap-3 mb-4">
            <div class="text-2xl">⏳</div>
            <div>
              <div class="font-semibold text-blue-300">Checking Payment Status...</div>
              <div class="text-sm text-blue-400">
                Please wait while we verify your payment. This may take a few moments.
              </div>
            </div>
          </div>
          <div class="flex items-center justify-center mb-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-400"></div>
            <span class="ml-2 text-blue-300">Verifying payment...</span>
          </div>
          <!-- Mobile-specific notice -->
          <div class="bg-yellow-900/20 border border-yellow-600/30 rounded p-2 text-xs text-yellow-200">
            <strong>📱 Mobile Users:</strong> Keep this page open and visible. Don't switch to other apps or minimize the browser.
          </div>
          <!-- Server-side fallback for mobile -->
          <div id="serverFallback" class="hidden">
            <div class="mt-3 text-center">
              <div class="text-xs text-gray-400">
                If automatic checking fails, this page will refresh automatically in <span id="countdown">30</span> seconds
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Manual Confirmation Button (shows after timeout) - ALSO MOVED TO TOP -->
      <div id="manualConfirmSection" class="mb-6 hidden">
        <div class="bg-green-900/20 border border-green-600/30 rounded-lg p-4">
          <div class="flex items-center gap-3 mb-4">
            <div class="text-2xl">💳</div>
            <div>
              <div class="font-semibold text-green-300">Payment Taking Longer?</div>
              <div class="text-sm text-green-400">
                If you've completed payment but still see this message, click below to confirm manually.
              </div>
            </div>
          </div>
          <button id="manualConfirmBtn" class="btn btn-primary text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Confirm Payment & View Booking
          </button>
        </div>
      </div>
      
      <form method="POST" action="<?php echo base_url('/travel/payment'); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
        
        <!-- Payment Method Selection -->
        <div>
          <label class="block text-sm font-medium mb-3">Payment Method</label>
          <div class="space-y-3">
            <?php if (\App\Models\Setting::get('payments.mpesa.enabled', '0') === '1'): ?>
            <label class="flex items-center p-3 border border-gray-600 rounded-lg cursor-pointer hover:border-red-500">
              <input type="radio" name="provider" value="mpesa" required class="mr-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600 rounded flex items-center justify-center text-white font-bold">M</div>
                <div>
                  <div class="font-medium">M-Pesa</div>
                  <div class="text-sm text-gray-400">Pay with your M-Pesa mobile money</div>
                </div>
              </div>
            </label>
            <?php endif; ?>
            
            <?php if (\App\Models\Setting::get('payments.flutterwave.enabled', '0') === '1'): ?>
            <label class="flex items-center p-3 border border-gray-600 rounded-lg cursor-pointer hover:border-red-500">
              <input type="radio" name="provider" value="flutterwave" required class="mr-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold">F</div>
                <div>
                  <div class="font-medium">Flutterwave</div>
                  <div class="text-sm text-gray-400">Pay with card, bank transfer, or mobile money</div>
                </div>
              </div>
            </label>
            <?php endif; ?>
            
            <?php if (\App\Models\Setting::get('payments.paypal.enabled', '0') === '1'): ?>
            <label class="flex items-center p-3 border border-gray-600 rounded-lg cursor-pointer hover:border-red-500">
              <input type="radio" name="provider" value="paypal" required class="mr-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center text-white font-bold">P</div>
                <div>
                  <div class="font-medium">PayPal</div>
                  <div class="text-sm text-gray-400">Pay with PayPal account</div>
                </div>
              </div>
            </label>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- M-Pesa Phone Number (shown when M-Pesa is selected) -->
        <div id="mpesa-phone" class="hidden">
          <label class="block text-sm font-medium text-white mb-2">M-Pesa Phone Number</label>
          <input type="tel" name="phone" id="phoneInput" placeholder="254712345678" class="w-full px-3 py-2 border border-gray-600 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" style="color: white !important; background-color: #1f2937 !important;" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" autocomplete="tel">
          <div class="text-sm text-gray-400 mt-1">Enter your M-Pesa registered phone number (automatically converts to 254 format)</div>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="flex items-start gap-3">
          <input type="checkbox" id="terms" required class="mt-1">
          <label for="terms" class="text-sm text-gray-300">
            I agree to the <a href="<?php echo base_url('/page?slug=terms-and-conditions'); ?>" class="link" target="_blank">Terms and Conditions</a> 
            and <a href="<?php echo base_url('/page?slug=privacy-policy'); ?>" class="link" target="_blank">Privacy Policy</a>
          </label>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-full text-lg py-3" onclick="formatPhoneBeforeSubmit()">
          Pay <?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format((float)$booking['total_amount'], 2); ?>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const paymentMethods = document.querySelectorAll('input[name="provider"]');
  const mpesaPhone = document.getElementById('mpesa-phone');
  
  paymentMethods.forEach(method => {
    method.addEventListener('change', function() {
      if (this.value === 'mpesa') {
        mpesaPhone.classList.remove('hidden');
        document.querySelector('input[name="phone"]').required = true;
      } else {
        mpesaPhone.classList.add('hidden');
        document.querySelector('input[name="phone"]').required = false;
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
  
  // Check if this is a return from M-Pesa payment (URL parameter)
  const urlParams = new URLSearchParams(window.location.search);
  const mpesaPayment = urlParams.get('mpesa_payment');
  
  console.log('Travel checkout page loaded');
  console.log('URL params:', window.location.search);
  console.log('mpesa_payment parameter:', mpesaPayment);
  
  if (mpesaPayment === '1') {
    console.log('Starting automatic payment status polling...');
    // Show automatic payment status checking (like events)
    startPaymentStatusPolling();
  } else {
    console.log('No mpesa_payment parameter found, not starting automatic polling');
  }
});

// Auto-payment status checking (mobile-optimized with multiple strategies)
function startPaymentStatusPolling() {
  const bookingId = '<?php echo $booking['id']; ?>';
  const statusSection = document.getElementById('paymentStatusSection');
  const manualSection = document.getElementById('manualConfirmSection');
  
  // Show status checking section
  if (statusSection) {
    statusSection.classList.remove('hidden');
  }
  
  // Show manual confirmation button immediately for mobile users
  setTimeout(() => {
    if (manualSection) {
      manualSection.classList.remove('hidden');
      console.log('Manual confirmation button shown for mobile users');
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
    console.log('Payment status check attempt:', attempts, 'Time:', new Date().toLocaleTimeString());
    
    // Use multiple strategies for mobile compatibility
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 8000); // Shorter timeout for mobile
    
    // Add more aggressive cache busting for mobile
    const cacheBuster = Date.now() + '_' + Math.random();
    
    fetch('<?php echo base_url('/travel/payment/status'); ?>?booking_id=' + bookingId + '&_t=' + cacheBuster, {
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
        console.log('Payment status response:', j);
        if (j.booking_status === 'confirmed' || j.payment_status === 'paid') {
          isPolling = false;
          clearInterval(pollInterval);
          console.log('Payment confirmed! Redirecting...');
          // Redirect to booking page
          window.location.href = '<?php echo base_url('/user/travel-bookings/show'); ?>?id=' + bookingId + '&payment_success=1';
        } else if (attempts >= 20) { // Reduced from 30 to 20 for mobile
          isPolling = false;
          clearInterval(pollInterval);
          console.log('Polling timeout reached, showing manual confirmation');
          // Show manual confirmation after timeout
          showManualConfirmation();
        }
      })
      .catch(error => {
        clearTimeout(timeoutId);
        console.log('Status check failed, attempt:', attempts, 'Error:', error.message);
        
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
    console.log('Page visibility changed:', document.visibilityState);
    if (document.visibilityState === 'visible' && isPolling) {
      // Page became visible, poll immediately and check if we missed anything
      const timeSinceLastPoll = Date.now() - lastPollTime;
      console.log('Page became visible, time since last poll:', timeSinceLastPoll);
      if (timeSinceLastPoll > 5000) {
        console.log('Long gap detected, polling immediately');
        setTimeout(pollPaymentStatus, 100);
      }
    }
  });
  
  // Handle online/offline events
  window.addEventListener('online', function() {
    if (isPolling) {
      console.log('Network back online, resuming polling');
      setTimeout(pollPaymentStatus, 500);
    }
  });
  
  window.addEventListener('offline', function() {
    console.log('Network offline, polling will continue when back online');
  });
  
  // Additional mobile-specific event listeners
  window.addEventListener('focus', function() {
    if (isPolling) {
      console.log('Window focused, checking payment status');
      setTimeout(pollPaymentStatus, 500);
    }
  });
  
  // Keep polling active on mobile by using multiple timers
  setInterval(() => {
    if (isPolling && (Date.now() - lastPollTime) > 10000) {
      console.log('Backup poll triggered (mobile safety)');
      pollPaymentStatus();
    }
  }, 5000);
  
  // Server-side fallback for mobile (show after 25 seconds)
  setTimeout(() => {
    if (isPolling) {
      const fallbackDiv = document.getElementById('serverFallback');
      if (fallbackDiv) {
        fallbackDiv.classList.remove('hidden');
        console.log('Showing server-side fallback for mobile');
        
        // Start countdown
        let countdown = 30;
        const countdownSpan = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
          countdown--;
          if (countdownSpan) {
            countdownSpan.textContent = countdown;
          }
          if (countdown <= 0) {
            clearInterval(countdownInterval);
            console.log('Server-side fallback triggered - refreshing page');
            window.location.reload();
          }
        }, 1000);
      }
    }
  }, 25000); // Show fallback after 25 seconds
}

// Show manual confirmation section (like events)
function showManualConfirmation() {
  const statusSection = document.getElementById('paymentStatusSection');
  const manualSection = document.getElementById('manualConfirmSection');
  
  if (statusSection) {
    statusSection.classList.add('hidden');
  }
  
  if (manualSection) {
    manualSection.classList.remove('hidden');
    
    // Add smooth animation
    manualSection.style.transform = 'translateY(-20px)';
    manualSection.style.opacity = '0';
    manualSection.style.transition = 'all 0.5s ease-out';
    
    setTimeout(() => {
      manualSection.style.transform = 'translateY(0)';
      manualSection.style.opacity = '1';
    }, 100);
  }
}

// Manual confirmation button handler
document.addEventListener('DOMContentLoaded', function() {
  const manualBtn = document.getElementById('manualConfirmBtn');
  if (manualBtn) {
    manualBtn.addEventListener('click', function() {
      const bookingId = '<?php echo $booking['id']; ?>';
      
      // Show loading state
      manualBtn.disabled = true;
      manualBtn.innerHTML = `
        <div class="flex items-center justify-center gap-2">
          <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
          <span>Checking...</span>
        </div>
      `;
      
      // Force reconciliation (same as events)
      fetch('<?php echo base_url('/travel/payment/reconcile'); ?>?booking_id=' + bookingId + '&payment_method=mpesa')
        .then(() => {
          // Small delay to allow DB update
          setTimeout(() => {
            window.location.reload();
          }, 1200);
        })
        .catch(() => {
          window.location.reload();
        })
        .finally(() => {
          manualBtn.disabled = false;
          manualBtn.innerHTML = 'Confirm Payment & View Booking';
        });
    });
  }
});

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
</script>

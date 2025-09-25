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
          <label class="block text-sm font-medium mb-2">M-Pesa Phone Number</label>
          <input type="tel" name="phone" placeholder="0712345678" class="input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
          <div class="text-sm text-gray-400 mt-1">Enter your M-Pesa registered phone number</div>
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
        <button type="submit" class="btn btn-primary w-full text-lg py-3">
          Pay <?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format((float)$booking['total_amount'], 2); ?>
        </button>
      </form>
      
      <!-- Payment Confirmation Section (for M-Pesa) -->
      <div id="paymentConfirmSection" class="mt-6 hidden">
        <div class="bg-green-900/20 border border-green-600/30 rounded-lg p-4">
          <div class="flex items-center gap-3 mb-4">
            <div class="text-2xl">💳</div>
            <div>
              <div class="font-semibold text-green-300">Payment Complete?</div>
              <div class="text-sm text-green-400">
                If you've entered your PIN successfully on your phone, click below to confirm your payment.
              </div>
            </div>
          </div>
          <button onclick="confirmPaymentNow()" class="w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            Confirm Payment & View Booking
          </button>
        </div>
      </div>
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
  
  // Check if this is a return from M-Pesa payment (URL parameter)
  const urlParams = new URLSearchParams(window.location.search);
  const mpesaPayment = urlParams.get('mpesa_payment');
  
  if (mpesaPayment === '1') {
    // Show payment confirmation section after 6 seconds
    setTimeout(() => {
      showPaymentConfirmSection();
    }, 6000);
  }
});

// Show payment confirmation section
function showPaymentConfirmSection() {
  const confirmSection = document.getElementById('paymentConfirmSection');
  if (confirmSection) {
    confirmSection.classList.remove('hidden');
    
    // Add smooth slide-down animation
    confirmSection.style.transform = 'translateY(-20px)';
    confirmSection.style.opacity = '0';
    confirmSection.style.transition = 'all 0.5s ease-out';
    
    // Trigger animation
    setTimeout(() => {
      confirmSection.style.transform = 'translateY(0)';
      confirmSection.style.opacity = '1';
    }, 100);
  }
}

// Function to handle payment confirmation
function confirmPaymentNow() {
  const bookingId = '<?php echo $booking['id']; ?>';
  
  // Get the confirmation section and button
  const confirmSection = document.getElementById('paymentConfirmSection');
  const confirmButton = confirmSection.querySelector('button');
  
  // Show loading state on the button
  if (confirmButton) {
    confirmButton.disabled = true;
    confirmButton.innerHTML = `
      <div class="flex items-center justify-center gap-2">
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
        <span>Confirming Payment...</span>
      </div>
    `;
  }
  
  // Submit reconciliation request
  const currentUrl = window.location.origin + window.location.pathname.replace('/travel/checkout', '/travel/payment/reconcile');
  fetch(currentUrl + '?booking_id=' + bookingId + '&payment_method=mpesa', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'csrf_token=<?php echo csrf_token(); ?>'
  })
  .then(response => {
    if (response.ok) {
      // Redirect directly to booking page with success parameter
      const bookingUrl = window.location.origin + '/user/travel-bookings/show?id=' + bookingId + '&payment_success=1';
      window.location.href = bookingUrl;
    } else {
      // Handle error - reset button
      if (confirmButton) {
        confirmButton.disabled = false;
        confirmButton.innerHTML = 'Confirm Payment & View Booking';
      }
      console.error('Payment confirmation failed. Response status:', response.status);
      alert('Payment confirmation failed. Please try again.');
    }
  })
  .catch(error => {
    console.error('Network error during payment confirmation:', error);
    // Reset button on error
    if (confirmButton) {
      confirmButton.disabled = false;
      confirmButton.innerHTML = 'Confirm Payment & View Booking';
    }
    alert('Network error occurred. Please check your connection and try again.');
  });
}
</script>

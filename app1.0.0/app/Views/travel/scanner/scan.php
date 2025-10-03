<?php
$pageTitle = 'Travel Booking Scanner';
?>
<div class="min-h-screen bg-dark-bg text-white">
    <!-- Header -->
    <div class="bg-dark-card border-b border-gray-800">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold">Travel Booking Scanner</h1>
                    <p class="text-sm text-gray-300">
                        Device: <?php echo htmlspecialchars($_SESSION['travel_scanner_device_name'] ?? ''); ?> | 
                        Agency: <?php echo htmlspecialchars($_SESSION['travel_scanner_agency_name'] ?? ''); ?>
                    </p>
                </div>
                <form method="POST" action="<?php echo base_url('/travel/scanner/logout'); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Scanner Interface -->
        <div class="bg-dark-card border border-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-6">Scan Booking Reference</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Manual Entry -->
                <div>
                    <h3 class="text-md font-medium text-gray-300 mb-4">Manual Entry</h3>
                    <form id="scan-form" class="space-y-4">
                        <div>
                            <label for="booking_reference" class="block text-sm text-gray-400 mb-2">
                                Booking Reference
                            </label>
                            <input type="text" id="booking_reference" name="booking_reference" 
                                   class="w-full px-4 py-3 border border-gray-600 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-lg uppercase"
                                   placeholder="Enter booking reference (e.g., A1B2C3)" autofocus maxlength="6" 
                                   style="text-transform: uppercase !important; font-family: 'Courier New', monospace; letter-spacing: 2px;"
                                   autocapitalize="characters" autocorrect="off" spellcheck="false" autocomplete="off">
                        </div>
                        
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            Verify Booking
                        </button>
                    </form>
                </div>

                <!-- QR Scanner -->
                <div>
                    <h3 class="text-md font-medium text-gray-300 mb-4">QR Code Scanner</h3>
                    <div class="space-y-4">
                        <div class="relative">
                            <video id="qr-video" class="w-full h-48 bg-gray-900 rounded-lg border border-gray-700" style="display: none;"></video>
                            <canvas id="qr-canvas" class="w-full h-48 bg-gray-900 rounded-lg border border-gray-700" style="display: none;"></canvas>
                            <div id="qr-placeholder" class="w-full h-48 bg-gray-900 rounded-lg border border-gray-700 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="h-10 w-10 text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                    <p class="text-gray-400 text-sm">QR Scanner Ready</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button id="start-qr-scanner" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Start Scanner
                            </button>
                            <button id="stop-qr-scanner" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors" style="display: none;">
                                <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Stop Scanner
                            </button>
                        </div>
                        <div id="qr-status" class="text-sm text-gray-400"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Results Popup -->
        <div id="scan-results" class="hidden">
            <div class="bg-dark-card border border-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Verification Result</h3>
                <div id="result-content"></div>
            </div>
        </div>
        
        <!-- Enhanced Popup Modal -->
        <div id="travelScanPopup" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
            <div class="transform transition-all scale-95 opacity-0 bg-[#0f0f10] border border-gray-800 rounded-xl p-6 w-[90%] max-w-lg text-center shadow-2xl" id="travelScanPopupCard">
                <!-- Status Badge -->
                <div id="travelScanPopupBadge" class="inline-block px-4 py-2 rounded-full text-sm font-semibold mb-4">Status</div>
                
                <!-- Main Message -->
                <div id="travelScanPopupMsg" class="text-xl font-bold mb-4">Message</div>
                
                <!-- Detailed Information -->
                <div id="travelScanPopupDetails" class="text-sm text-gray-300 mb-6 hidden">
                    <div class="bg-gray-800/50 rounded-lg p-4 space-y-2">
                        <div id="travelScanPopupBookingRef" class="text-lg font-semibold text-white mb-2"></div>
                        <div id="travelScanPopupDestination" class="font-bold text-red-400 mb-2"></div>
                        <div id="travelScanPopupBookingInfo" class="text-gray-300 space-y-1">
                            <div id="travelScanPopupTravelDate" class="mb-1"></div>
                            <div id="travelScanPopupParticipants" class="mb-1"></div>
                            <div id="travelScanPopupCustomer" class="mb-1"></div>
                            <div id="travelScanPopupStatus" class="mb-1"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div id="travelScanPopupActions" class="space-y-3">
                    <!-- Confirm/Reject buttons for successful scans -->
                    <div id="travelScanPopupConfirmActions" class="hidden space-y-2">
                        <div class="text-sm text-gray-400 mb-3">Please confirm the booking details above before proceeding:</div>
                        <div class="flex gap-3">
                            <button id="travelScanPopupConfirm" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Confirm Entry
                            </button>
                            <button id="travelScanPopupReject" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Entry
                            </button>
                        </div>
                    </div>
                    
                    <!-- Next/Close buttons -->
                    <div id="travelScanPopupNextActions" class="hidden space-y-2">
                        <div class="flex gap-3">
                            <button id="travelScanPopupScanNext" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                Scan Next
                            </button>
                            <button id="travelScanPopupClose" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                                <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Close
                            </button>
                        </div>
                    </div>
                    
                    <!-- Single close button for errors -->
                    <div id="travelScanPopupErrorActions" class="hidden">
                        <button id="travelScanPopupErrorClose" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 p-4 bg-blue-900 border border-blue-600 rounded-lg">
            <h3 class="font-semibold text-blue-300 mb-2">Scanner Instructions:</h3>
            <ul class="text-sm text-blue-200 space-y-1">
                <li>• Enter the booking reference manually or scan the QR code</li>
                <li>• The system will verify the booking and show customer details</li>
                <li>• Each booking can only be scanned once per device</li>
                <li>• Invalid or already scanned bookings will be clearly marked</li>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
// QR Scanner functionality
let qrStream = null;
let qrScanning = false;

document.getElementById('start-qr-scanner').addEventListener('click', startQRScanner);
document.getElementById('stop-qr-scanner').addEventListener('click', stopQRScanner);

async function startQRScanner() {
    try {
        const video = document.getElementById('qr-video');
        const canvas = document.getElementById('qr-canvas');
        const placeholder = document.getElementById('qr-placeholder');
        const status = document.getElementById('qr-status');
        const startBtn = document.getElementById('start-qr-scanner');
        const stopBtn = document.getElementById('stop-qr-scanner');

        qrStream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 640 },
                height: { ideal: 480 }
            } 
        });
        
        video.srcObject = qrStream;
        video.style.display = 'block';
        placeholder.style.display = 'none';
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-flex';
        
        video.play();
        qrScanning = true;
        status.textContent = 'Scanning for QR codes...';
        
        // Start QR detection loop
        detectQR();
        
    } catch (error) {
        console.error('Error accessing camera:', error);
        document.getElementById('qr-status').textContent = 'Camera access denied or not available';
    }
}

function stopQRScanner() {
    if (qrStream) {
        qrStream.getTracks().forEach(track => track.stop());
        qrStream = null;
    }
    
    qrScanning = false;
    document.getElementById('qr-video').style.display = 'none';
    document.getElementById('qr-placeholder').style.display = 'flex';
    document.getElementById('start-qr-scanner').style.display = 'inline-flex';
    document.getElementById('stop-qr-scanner').style.display = 'none';
    document.getElementById('qr-status').textContent = '';
}

function detectQR() {
    if (!qrScanning) return;
    
    const video = document.getElementById('qr-video');
    const canvas = document.getElementById('qr-canvas');
    const context = canvas.getContext('2d');
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            console.log('QR Code detected:', code.data);
            document.getElementById('booking_reference').value = code.data.toUpperCase();
            stopQRScanner();
            document.getElementById('scan-form').dispatchEvent(new Event('submit'));
        }
    }
    
    if (qrScanning) {
        requestAnimationFrame(detectQR);
    }
}

// Enhanced caps lock enforcement
document.addEventListener('DOMContentLoaded', function() {
    const bookingRefInput = document.getElementById('booking_reference');
    if (bookingRefInput) {
        // Force focus and uppercase on mobile
        bookingRefInput.addEventListener('focus', function() {
            this.style.textTransform = 'uppercase';
            // Prevent lowercase input
            this.addEventListener('keydown', function(e) {
                // Allow backspace, delete, tab, escape, enter
                if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true)) {
                    return;
                }
                // Ensure uppercase
                if (e.key && e.key.length === 1) {
                    e.preventDefault();
                    this.value += e.key.toUpperCase();
                }
            });
        });
        
        bookingRefInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
        
        // Also convert on paste
        bookingRefInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                e.target.value = e.target.value.toUpperCase();
            }, 10);
        });
        
        // Prevent lowercase on mobile keyboards
        bookingRefInput.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (char && char !== char.toUpperCase()) {
                e.preventDefault();
                this.value += char.toUpperCase();
            }
        });
    }
});

// Manual form submission
document.getElementById('scan-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const bookingReference = document.getElementById('booking_reference').value.trim().toUpperCase();
    if (!bookingReference || bookingReference.length !== 6) {
        displayResult({
            success: false,
            message: 'Please enter a valid 6-character booking reference.'
        });
        return;
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Verifying...';
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('booking_reference', bookingReference);
        formData.append('csrf_token', '<?php echo csrf_token(); ?>');
        
        const response = await fetch('<?php echo base_url('/travel/scanner/verify'); ?>', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        displayResult(result);
        
    } catch (error) {
        console.error('Verification error:', error);
        displayResult({
            success: false,
            message: 'Network error. Please check your connection and try again.'
        });
    } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        document.getElementById('booking_reference').value = '';
        document.getElementById('booking_reference').focus();
    }
});

// Enhanced popup system for travel scanner
let currentTravelScanData = null;

function displayResult(result) {
    // Use the new popup instead of inline results
    showTravelScanPopup(result);
}

function showTravelScanPopup(result) {
    const modal = document.getElementById('travelScanPopup');
    const badge = document.getElementById('travelScanPopupBadge');
    const msg = document.getElementById('travelScanPopupMsg');
    const details = document.getElementById('travelScanPopupDetails');
    const card = document.getElementById('travelScanPopupCard');
    
    // Store current scan data
    currentTravelScanData = result;
    
    // Hide all action sections initially
    document.getElementById('travelScanPopupConfirmActions').classList.add('hidden');
    document.getElementById('travelScanPopupNextActions').classList.add('hidden');
    document.getElementById('travelScanPopupErrorActions').classList.add('hidden');
    
    if (result.success) {
        // Valid booking
        badge.textContent = 'Valid Booking';
        badge.setAttribute('style', 'background:#052e16;border:1px solid #14532d;color:#86efac');
        msg.textContent = 'Booking Verified Successfully!';
        
        // Show detailed information
        if (result.booking) {
            details.classList.remove('hidden');
            document.getElementById('travelScanPopupBookingRef').textContent = `Reference: ${result.booking.booking_reference}`;
            document.getElementById('travelScanPopupDestination').textContent = result.booking.destination_title;
            document.getElementById('travelScanPopupTravelDate').textContent = `Travel Date: ${result.booking.travel_date}`;
            document.getElementById('travelScanPopupParticipants').textContent = `Participants: ${result.booking.participants_count}`;
            document.getElementById('travelScanPopupCustomer').textContent = `Customer: ${result.booking.customer_name || 'N/A'}`;
            document.getElementById('travelScanPopupStatus').textContent = `Status: ${result.booking.status}`;
        }
        
        // Show confirm/reject buttons
        document.getElementById('travelScanPopupConfirmActions').classList.remove('hidden');
    } else {
        // Invalid booking
        badge.textContent = 'Invalid Booking';
        badge.setAttribute('style', 'background:#450a0a;border:1px solid #7f1d1d;color:#fecaca');
        msg.textContent = result.message || 'Verification Failed';
        details.classList.add('hidden');
        
        // Show error actions
        document.getElementById('travelScanPopupErrorActions').classList.remove('hidden');
    }
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Animate in
    requestAnimationFrame(() => {
        card.style.transform = 'scale(1)';
        card.style.opacity = '1';
    });
}

// Event handlers for travel scanner popup
document.addEventListener('DOMContentLoaded', function() {
    // Confirm entry
    document.getElementById('travelScanPopupConfirm').addEventListener('click', function() {
        if (currentTravelScanData && currentTravelScanData.success) {
            // Show success message and next actions
            document.getElementById('travelScanPopupConfirmActions').classList.add('hidden');
            document.getElementById('travelScanPopupNextActions').classList.remove('hidden');
            document.getElementById('travelScanPopupMsg').textContent = 'Entry Confirmed Successfully!';
            document.getElementById('travelScanPopupBadge').textContent = 'Confirmed';
            document.getElementById('travelScanPopupBadge').setAttribute('style', 'background:#065f46;border:1px solid #047857;color:#10b981');
            
            // Add vibration for mobile
            try { navigator.vibrate && navigator.vibrate([100, 50, 100]); } catch(e) {}
        }
    });
    
    // Reject entry
    document.getElementById('travelScanPopupReject').addEventListener('click', function() {
        hideTravelPopup();
        // Clear the input for next scan
        document.getElementById('booking_reference').value = '';
        document.getElementById('booking_reference').focus();
    });
    
    // Scan next
    document.getElementById('travelScanPopupScanNext').addEventListener('click', function() {
        hideTravelPopup();
        // Clear the input for next scan
        document.getElementById('booking_reference').value = '';
        document.getElementById('booking_reference').focus();
    });
    
    // Close popup
    document.getElementById('travelScanPopupClose').addEventListener('click', hideTravelPopup);
    document.getElementById('travelScanPopupErrorClose').addEventListener('click', hideTravelPopup);
    
    // Click outside to close
    document.getElementById('travelScanPopup').addEventListener('click', function(e) {
        if (e.target === this) {
            hideTravelPopup();
        }
    });
});

function hideTravelPopup() {
    const modal = document.getElementById('travelScanPopup');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Reset all sections
    document.getElementById('travelScanPopupConfirmActions').classList.add('hidden');
    document.getElementById('travelScanPopupNextActions').classList.add('hidden');
    document.getElementById('travelScanPopupErrorActions').classList.add('hidden');
    document.getElementById('travelScanPopupDetails').classList.add('hidden');
    
    currentTravelScanData = null;
}
</script>

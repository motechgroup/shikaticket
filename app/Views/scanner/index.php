<?php /** @var array $assignedEvents */ ?>
<div class="max-w-xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-2">Universal Scanner</h1>
    <div class="mb-4 p-3 bg-gray-800 rounded-lg">
        <div class="text-sm text-gray-400">Device: <?php echo htmlspecialchars($_SESSION['scanner_device_name'] ?? 'Unknown'); ?></div>
        <div class="text-xs text-gray-500 font-mono uppercase">Code: <?php echo htmlspecialchars(strtoupper($_SESSION['scanner_device_code'] ?? '')); ?></div>
        <?php if (!empty($assignedEvents)): ?>
        <div class="text-sm text-gray-400 mt-1">Assigned Events: <?php echo count($assignedEvents); ?></div>
        <?php endif; ?>
    </div>
    <div class="card p-6 space-y-4">
<form id="manualForm" method="get" action="/scanner/verify" onsubmit="return verifyManual(event)">
            <?php echo csrf_field(); ?>
            <label class="block text-sm mb-1">Enter Ticket Code or Booking Reference</label>
            <div class="flex gap-2">
                <input name="code" id="codeInput" class="input" placeholder="Ticket code or booking reference" required>
                <button class="btn btn-primary" id="verifyBtn" type="submit">Verify</button>
            </div>
        </form>
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-400">QR Scanner:</div>
            <div class="flex gap-2">
                <button id="toggleFacing" class="btn btn-secondary btn-sm" type="button">Switch Camera</button>
                <button id="torchBtn" class="btn btn-secondary btn-sm" type="button">Toggle Torch</button>
            </div>
        </div>
        <div class="relative">
            <video id="qrVideo" class="w-full h-64 bg-black rounded border border-gray-800" playsinline muted style="display:none"></video>
            <canvas id="qrCanvas" class="w-full h-64 bg-black rounded border border-gray-800" style="display:none"></canvas>
            <div id="qrPlaceholder" class="w-full h-64 bg-gray-900 rounded border border-gray-800 flex items-center justify-center">
                <div class="text-center">
                    <svg class="h-10 w-10 text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <p class="text-gray-400 text-sm">Initializing camera…</p>
                </div>
            </div>
        </div>
        <div id="cameraError" class="text-xs text-red-400"></div>
        <div id="result" class="text-sm"></div>
        <div id="lastScanInfo" class="mt-3 p-3 bg-gray-800 rounded-lg hidden">
            <div class="text-xs text-gray-400 mb-1">Last Scan:</div>
            <div id="lastScanEvent" class="font-semibold"></div>
            <div id="lastScanType" class="text-red-400 font-bold"></div>
        </div>
    </div>
    <!-- Enhanced Popup modal -->
    <div id="scanPopup" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="transform transition-all scale-95 opacity-0 bg-[#0f0f10] border border-gray-800 rounded-xl p-6 w-[90%] max-w-lg text-center shadow-2xl" id="scanPopupCard">
            <!-- Status Badge -->
            <div id="scanPopupBadge" class="inline-block px-4 py-2 rounded-full text-sm font-semibold mb-4">Status</div>
            <!-- Main Message -->
            <div id="scanPopupMsg" class="text-xl font-bold mb-4">Message</div>
            <!-- Detailed Information -->
            <div id="scanPopupDetails" class="text-sm text-gray-300 mb-6 hidden">
                <div class="bg-gray-800/50 rounded-lg p-4 space-y-2">
                    <div id="scanPopupEvent" class="text-lg font-semibold text-white mb-2"></div>
                    <div id="scanPopupTicketType" class="font-bold text-red-400 mb-2"></div>
                    <div id="scanPopupCustomerInfo" class="text-gray-300 hidden">
                        <div id="scanPopupCustomerName" class="mb-1"></div>
                        <div id="scanPopupCustomerPhone" class="mb-1"></div>
                        <div id="scanPopupTicketCode" class="font-mono text-green-400"></div>
                    </div>
                    <div id="scanPopupBookingInfo" class="text-gray-300 hidden">
                        <div id="scanPopupBookingRef" class="mb-1"></div>
                        <div id="scanPopupDestination" class="mb-1"></div>
                        <div id="scanPopupTravelDate" class="mb-1"></div>
                        <div id="scanPopupParticipants" class="mb-1"></div>
                    </div>
                </div>
            </div>
            <!-- Action Buttons -->
            <div id="scanPopupActions" class="space-y-3">
                <div id="scanPopupConfirmActions" class="hidden space-y-2">
                    <div class="text-sm text-gray-400 mb-3">Please confirm the details above before proceeding:</div>
                    <div class="flex gap-3">
                        <button id="scanPopupConfirm" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirm Entry
                        </button>
                        <button id="scanPopupReject" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject Entry
                        </button>
                    </div>
                </div>
                <div id="scanPopupNextActions" class="hidden space-y-2">
                    <div class="flex gap-3">
                        <button id="scanPopupScanNext" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            Scan Next
                        </button>
                        <button id="scanPopupClose" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Close
                        </button>
                    </div>
                </div>
                <div id="scanPopupErrorActions" class="hidden">
                    <button id="scanPopupErrorClose" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js" defer></script>
<script>
// Multiple CDN fallbacks for jsQR library
document.addEventListener('DOMContentLoaded', function(){
  if (typeof window.jsQR === 'undefined') {
    const fallbacks = [
      'https://unpkg.com/jsqr@1.4.0/dist/jsQR.js',
      'https://cdnjs.cloudflare.com/ajax/libs/jsqr/1.4.0/jsQR.min.js',
      'https://cdn.skypack.dev/jsqr@1.4.0'
    ];
    
    let currentFallback = 0;
    function tryNextFallback() {
      if (currentFallback < fallbacks.length) {
        const script = document.createElement('script');
        script.src = fallbacks[currentFallback];
        script.onerror = () => {
          currentFallback++;
          tryNextFallback();
        };
        script.onload = () => {
          console.log('jsQR loaded from:', fallbacks[currentFallback]);
        };
        document.head.appendChild(script);
        currentFallback++;
      } else {
        console.error('Failed to load jsQR from all CDNs');
      }
    }
    
    setTimeout(() => {
      if (typeof window.jsQR === 'undefined') {
        tryNextFallback();
      }
    }, 500);
  }
});
</script>
<script>
function verifyManual(e){
  e.preventDefault();
  const form=e.target; const fd=new FormData(form);
  const btn=document.getElementById('verifyBtn'); btn.disabled=true; btn.textContent='Checking...';
  const basePrefix = (function(){ const p=window.location.pathname; const i=p.indexOf('/scanner'); return i>0 ? p.slice(0,i) : ''; })();
  const url = basePrefix + '/scanner/verify?code=' + encodeURIComponent(fd.get('code'));
  fetch(url,{method:'GET',credentials:'include',headers:{'Accept':'application/json'}})
    .then(async r=>{ try { return await r.json(); } catch(e){ return { ok:false, msg:'Invalid server response' }; } })
    .then(j=>{
    const res=document.getElementById('result');
    res.innerText=j.msg;
    res.className='text-sm mt-2 ' + (j.ok ? 'text-green-400' : 'text-red-400');
      showScanPopup(j);
  }).finally(()=>{ const b=document.getElementById('verifyBtn'); b.disabled=false; b.textContent='Verify'; });
  return false;
}

window.addEventListener('load', async function(){
  const video = document.getElementById('qrVideo');
  const canvas = document.getElementById('qrCanvas');
  const placeholder = document.getElementById('qrPlaceholder');
  if (!video || !canvas) return;
  
  const ctx = canvas.getContext('2d');
  let currentCamIndex = 0; 
  let camsCache = []; 
  let currentCamId = null; 
  let streamTrack = null; 
  let torchOn = false;
  let scanning = false; 
  let lastDecodeAt = 0; 
  let stream = null;
  let barcodeDetector = null; 
  let useBarcodeAPI = false;
  let frameCount = 0;
  let scanInterval = null;
  
  // Performance optimization: scan every 3rd frame instead of every frame
  const SCAN_INTERVAL = 3;
  const DECODE_THROTTLE = 1500; // Increased throttle time

  function showCamError(msg){
    const e = document.getElementById('cameraError');
    if(e){ e.textContent = msg; }
  }

  async function initCams(){
    try {
      // Request permission first
      const tempStream = await navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: { ideal: 'environment' } }, 
        audio: false 
      });
      tempStream.getTracks().forEach(t => t.stop());
      
      const devices = await navigator.mediaDevices.enumerateDevices();
      camsCache = devices.filter(d => d.kind === 'videoinput').map(d => ({ 
        id: d.deviceId, 
        label: d.label || `Camera ${d.deviceId.slice(0, 8)}`
      }));
      
      if(!camsCache.length){ 
        showCamError('No camera found.'); 
        return; 
      }
      
      // Prefer back camera
      const prefersBack = camsCache.findIndex(c => 
        /back|rear|environment/i.test(c.label)
      );
      currentCamIndex = prefersBack >= 0 ? prefersBack : 0;
      currentCamId = camsCache[currentCamIndex].id;
    } catch(err){
      showCamError('Camera permission required. Please allow camera access.');
      console.error('Camera initialization error:', err);
    }
  }

  async function startScanner(){
    try {
      // Stop any existing stream
      if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
      }
      
      // Clear scanning state
      scanning = false;
      if (scanInterval) {
        clearInterval(scanInterval);
        scanInterval = null;
      }
      
      // Initialize cameras if needed
      if (!currentCamId) {
        await initCams();
        if (!currentCamId) return;
      }
      
      // Request camera with optimized settings
      const constraints = {
        video: {
          deviceId: { exact: currentCamId },
          facingMode: 'environment',
          width: { ideal: 1280, max: 1920 },
          height: { ideal: 720, max: 1080 },
          frameRate: { ideal: 15, max: 30 } // Lower frame rate for better performance
        },
        audio: false
      };
      
      stream = await navigator.mediaDevices.getUserMedia(constraints);
      streamTrack = stream.getVideoTracks()[0];
      
      // Setup video
      video.srcObject = stream;
      video.setAttribute('autoplay', '');
      video.style.display = 'block';
      placeholder.style.display = 'none';
      
      await video.play();
      
      // Clear errors
      showCamError('');
      
      // Optimize camera settings
      try {
        const caps = streamTrack.getCapabilities();
        if (caps.focusMode && caps.focusMode.includes('continuous')) {
          await streamTrack.applyConstraints({ 
            advanced: [{ focusMode: 'continuous' }] 
          });
        }
        
        // Enable torch if available
        if (caps.torch) {
          document.getElementById('torchBtn').style.display = 'inline-block';
        } else {
          document.getElementById('torchBtn').style.display = 'none';
        }
      } catch(e) {
        console.warn('Camera optimization failed:', e);
      }
      
      // Start scanning with throttled approach
      scanning = true;
      lastDecodeAt = 0;
      frameCount = 0;
      
      // Use interval instead of requestAnimationFrame for better control
      scanInterval = setInterval(scanFrame, 100); // 10 FPS scanning
      
    } catch(err){
      const msg = err.message || 'Failed to start camera.';
      showCamError(`Camera error: ${msg}`);
      console.error('Scanner start error:', err);
    }
  }

  // Optimized frame scanning
  function scanFrame(){
    if (!scanning || !video || video.readyState !== video.HAVE_ENOUGH_DATA) return;
    
    frameCount++;
    
    // Only process every SCAN_INTERVAL frames
    if (frameCount % SCAN_INTERVAL !== 0) return;
    
    // Check if we have valid video dimensions
    if (!video.videoWidth || !video.videoHeight) return;
    
    // Optimize canvas size for processing
    const scale = 0.5; // Process at half resolution for speed
    canvas.width = video.videoWidth * scale;
    canvas.height = video.videoHeight * scale;
    
    // Draw video to canvas at reduced resolution
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Process QR detection
    if (useBarcodeAPI && barcodeDetector) {
      barcodeDetector.detect(video).then(codes => {
        if (codes && codes.length > 0) {
          const now = Date.now();
          if (now - lastDecodeAt < DECODE_THROTTLE) return;
          lastDecodeAt = now;
          
          const text = codes[0].rawValue?.trim();
          if (text) handleDecodedText(text);
        }
      }).catch(() => {}); // Silent catch for performance
    } else if (typeof window.jsQR !== 'undefined') {
      try {
        const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = window.jsQR(img.data, img.width, img.height, {
          inversionAttempts: 'dontInvert' // Skip inversion attempts for speed
        });
        
        if (code) {
          const now = Date.now();
          if (now - lastDecodeAt < DECODE_THROTTLE) return;
          lastDecodeAt = now;
          
          const text = code.data?.trim();
          if (text) handleDecodedText(text);
        }
      } catch(e) {
        // Silent catch for performance
      }
    }
  }

  // Initialize BarcodeDetector API if available
  async function initBarcodeDetector() {
    try {
      if ('BarcodeDetector' in window) {
        const supported = await window.BarcodeDetector.getSupportedFormats();
        if (supported && supported.includes('qr_code')) {
          barcodeDetector = new window.BarcodeDetector({ formats: ['qr_code'] });
          useBarcodeAPI = true;
          console.log('Using native BarcodeDetector API');
          return true;
        }
      }
    } catch(e) {
      console.warn('BarcodeDetector not available:', e);
    }
    return false;
  }

  // Wait for jsQR library if not using BarcodeDetector
  async function waitForJsQR() {
    if (useBarcodeAPI) return true;
    
    const start = Date.now();
    while (typeof window.jsQR === 'undefined' && Date.now() - start < 3000) {
      await new Promise(r => setTimeout(r, 100));
    }
    
    if (typeof window.jsQR !== 'undefined') {
      console.log('jsQR library loaded');
      return true;
    }
    
    console.error('jsQR library failed to load');
    showCamError('QR scanning library failed to load. Please refresh the page.');
    return false;
  }

  // Initialize scanner
  async function initScanner() {
    const isSecure = location.protocol === 'https:' || 
                     /^(localhost|127\.0\.0\.1)$/i.test(location.hostname);
    
    if (!isSecure) {
      showCamError('Tip: Use HTTPS or localhost to enable camera on mobile.');
    }
    
    await initBarcodeDetector();
    await waitForJsQR();
    
    if (useBarcodeAPI || typeof window.jsQR !== 'undefined') {
      await startScanner();
    }
  }

  // Event listeners
  document.getElementById('toggleFacing').addEventListener('click', async function(){
    if(!camsCache.length) return;
    
    currentCamIndex = (currentCamIndex + 1) % camsCache.length;
    const nextId = camsCache[currentCamIndex].id;
    
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
    
    currentCamId = nextId;
    await startScanner();
  });

  document.getElementById('torchBtn').addEventListener('click', async function(){
    if (!streamTrack) return;
    
    const caps = streamTrack.getCapabilities();
    if (!caps.torch) return;
    
    try {
      torchOn = !torchOn;
      await streamTrack.applyConstraints({ 
        advanced: [{ torch: torchOn }] 
      });
      
      this.textContent = torchOn ? 'Turn Off Torch' : 'Toggle Torch';
    } catch(e) {
      console.warn('Torch toggle failed:', e);
    }
  });

  // Cleanup on page unload
  window.addEventListener('beforeunload', function() {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
    if (scanInterval) {
      clearInterval(scanInterval);
    }
  });

  // Start the scanner
  initScanner();

  function handleDecodedText(text){
    // Fill the input field
    document.getElementById('codeInput').value = text;
    
    // Get base URL
    const basePrefix = (() => {
      const p = window.location.pathname;
      const i = p.indexOf('/scanner');
      return i > 0 ? p.slice(0, i) : '';
    })();
    
    // Verify the code
    const url = basePrefix + '/scanner/verify?code=' + encodeURIComponent(text);
    
    fetch(url, { 
      method: 'GET', 
      credentials: 'include', 
      headers: { 'Accept': 'application/json' } 
    })
    .then(async r => {
      try { 
        return await r.json(); 
      } catch(e) { 
        return { ok: false, msg: 'Invalid server response' }; 
      }
    })
    .then(j => {
      const res = document.getElementById('result');
      res.innerText = j.msg;
      res.className = 'text-sm mt-2 ' + (j.ok ? 'text-green-400' : 'text-red-400');
      
      if (j.ok) { 
        try { 
          navigator.vibrate && navigator.vibrate([50, 50, 50]); 
        } catch(e) {} 
      }
      
      showScanPopup(j);
    })
    .catch(err => {
      console.error('Verification error:', err);
      const res = document.getElementById('result');
      res.innerText = 'Network error';
      res.className = 'text-sm mt-2 text-red-400';
    });
  }
});

// Enhanced Popup helpers
let currentScanData = null;

function showScanPopup(resp){
  const modal=document.getElementById('scanPopup');
  const badge=document.getElementById('scanPopupBadge');
  const msg=document.getElementById('scanPopupMsg');
  const details=document.getElementById('scanPopupDetails');
  const event=document.getElementById('scanPopupEvent');
  const ticketType=document.getElementById('scanPopupTicketType');
  const card=document.getElementById('scanPopupCard');
  const text=(resp?.msg||'').toLowerCase();
  let theme=''; let label='';
  currentScanData = resp;
  document.getElementById('scanPopupConfirmActions').classList.add('hidden');
  document.getElementById('scanPopupNextActions').classList.add('hidden');
  document.getElementById('scanPopupErrorActions').classList.add('hidden');
  if(resp.ok){ 
    label='Valid Entry'; 
    theme='background:#052e16;border:1px solid #14532d;color:#86efac'; 
    if(resp.ticket_type && resp.event_title){
      details.classList.remove('hidden');
      event.textContent = resp.event_title;
      ticketType.textContent = resp.ticket_type + ' Ticket';
    } else if(resp.destination && resp.participants){
      details.classList.remove('hidden');
      event.textContent = resp.destination;
      ticketType.textContent = resp.participants + ' participant(s) - ' + resp.travel_date;
    }
    document.getElementById('scanPopupConfirmActions').classList.remove('hidden');
  }
  else if(text.includes('redeemed')){ 
    label='Already Redeemed'; 
    theme='background:#1f2937;border:1px solid #374151;color:#e5e7eb'; 
    details.classList.add('hidden');
    document.getElementById('scanPopupErrorActions').classList.remove('hidden');
  }
  else { 
    label='Invalid Entry'; 
    theme='background:#450a0a;border:1px solid #7f1d1d;color:#fecaca'; 
    details.classList.add('hidden');
    document.getElementById('scanPopupErrorActions').classList.remove('hidden');
  }
  badge.setAttribute('style', theme);
  badge.textContent=label;
  msg.textContent=resp.msg || '';
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  requestAnimationFrame(()=>{ 
    card.style.transform='scale(1)'; 
    card.style.opacity='1'; 
  });
}

document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('scanPopupConfirm').addEventListener('click', function() {
    if (currentScanData && currentScanData.ok) {
      document.getElementById('scanPopupConfirmActions').classList.add('hidden');
      document.getElementById('scanPopupNextActions').classList.remove('hidden');
      document.getElementById('scanPopupMsg').textContent = 'Entry Confirmed Successfully!';
      document.getElementById('scanPopupBadge').textContent = 'Confirmed';
      document.getElementById('scanPopupBadge').setAttribute('style', 'background:#065f46;border:1px solid #047857;color:#10b981');
      try { navigator.vibrate && navigator.vibrate([100, 50, 100]); } catch(e) {}
    }
  });
  document.getElementById('scanPopupReject').addEventListener('click', function() { hidePopup(); document.getElementById('codeInput').value = ''; document.getElementById('codeInput').focus(); });
  document.getElementById('scanPopupScanNext').addEventListener('click', function() { hidePopup(); document.getElementById('codeInput').value = ''; document.getElementById('codeInput').focus(); });
  document.getElementById('scanPopupClose').addEventListener('click', hidePopup);
  document.getElementById('scanPopupErrorClose').addEventListener('click', hidePopup);
  document.getElementById('scanPopup').addEventListener('click', function(e) { if (e.target === this) { hidePopup(); } });
});

function hidePopup() {
  const modal = document.getElementById('scanPopup');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.getElementById('scanPopupConfirmActions').classList.add('hidden');
  document.getElementById('scanPopupNextActions').classList.add('hidden');
  document.getElementById('scanPopupErrorActions').classList.add('hidden');
  document.getElementById('scanPopupDetails').classList.add('hidden');
  document.getElementById('scanPopupCustomerInfo').classList.add('hidden');
  document.getElementById('scanPopupBookingInfo').classList.add('hidden');
  currentScanData = null;
}
</script>



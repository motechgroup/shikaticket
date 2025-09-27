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
			<div class="text-sm text-gray-400">Or scan QR using your camera:</div>
			<div class="flex gap-2">
				<button id="toggleFacing" class="btn btn-secondary btn-sm" type="button">Switch Camera</button>
				<button id="torchBtn" class="btn btn-secondary btn-sm" type="button">Toggle Torch</button>
			</div>
		</div>
		<video id="preview" class="w-full rounded border border-gray-800"></video>
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
                <!-- Confirm/Reject buttons for successful scans -->
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
                
                <!-- Next/Close buttons -->
                <div id="scanPopupNextActions" class="hidden space-y-2">
                    <div class="flex gap-3">
                        <button id="scanPopupScanNext" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
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
                
                <!-- Single close button for errors -->
                <div id="scanPopupErrorActions" class="hidden">
                    <button id="scanPopupErrorClose" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
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

// QR Scanner
window.addEventListener('load', function(){
  const videoElem=document.getElementById('preview');
  if (!videoElem) return;
  const qrRegionId = 'qr-region';
  const div=document.createElement('div'); div.id=qrRegionId; videoElem.replaceWith(div);
  const qr = new Html5Qrcode(qrRegionId);
  let currentCamIndex = 0; let camsCache=[]; let currentCamId=null; let streamTrack=null; let torchOn=false;
  let lastDecodeAt = 0;
  Html5Qrcode.getCameras().then(cams=>{
    camsCache = cams || [];
    const prefersBack = camsCache.findIndex(c=>/back|rear/i.test(c.label))
    currentCamIndex = prefersBack >=0 ? prefersBack : 0;
    const camId = camsCache.length ? camsCache[currentCamIndex].id : null;
    if(!camId) return;
    currentCamId=camId;
    qr.start(camId, { fps: 10, qrbox: 250 }, (decodedText)=>{
      const now=Date.now(); if (now - lastDecodeAt < 1500) return; lastDecodeAt = now;
      document.getElementById('codeInput').value = decodedText.trim();
      const code = document.getElementById('codeInput').value.trim();
      const basePrefix = (function(){ const p=window.location.pathname; const i=p.indexOf('/scanner'); return i>0 ? p.slice(0,i) : ''; })();
      const url = basePrefix + '/scanner/verify?code=' + encodeURIComponent(code);
      fetch(url, { method:'GET', credentials:'include', headers:{'Accept':'application/json'} })
        .then(async r=>{ try { return await r.json(); } catch(e){ return { ok:false, msg:'Invalid server response' }; } })
        .then(j=>{
          const res=document.getElementById('result');
          res.innerText=j.msg;
          res.className='text-sm mt-2 ' + (j.ok ? 'text-green-400' : 'text-red-400');
          if (j.ok) { try { navigator.vibrate && navigator.vibrate([50,50,50]); } catch(e) {} }
          showScanPopup(j);
        }).catch(()=>{
          const res=document.getElementById('result');
          res.innerText='Network error';
          res.className='text-sm mt-2 text-red-400';
        });
    }).then(()=>{
      // capture track for torch if supported
      try {
        const el = document.querySelector('#'+qrRegionId+' video');
        streamTrack = el && el.srcObject ? el.srcObject.getVideoTracks()[0] : null;
      } catch(e) {}
    });
  });

  document.getElementById('toggleFacing').addEventListener('click', async function(){
    if(!camsCache.length) return;
    currentCamIndex = (currentCamIndex + 1) % camsCache.length;
    const nextId = camsCache[currentCamIndex].id;
    try { await qr.stop(); } catch(e) {}
    qr.start(nextId, { fps: 10, qrbox: 250 }, (decodedText)=>{
      const now=Date.now(); if (now - lastDecodeAt < 1500) return; lastDecodeAt = now;
      document.getElementById('codeInput').value = decodedText.trim();
      fetch(document.getElementById('manualForm').action, {method:'POST', body:new FormData(document.getElementById('manualForm'))})
        .then(r=>r.json()).then(j=>{
          const res=document.getElementById('result');
          res.innerText=j.msg;
          res.className='text-sm mt-2 ' + (j.ok ? 'text-green-400' : 'text-red-400');
        });
    }).then(()=>{
      try { const el = document.querySelector('#'+qrRegionId+' video'); streamTrack = el && el.srcObject ? el.srcObject.getVideoTracks()[0] : null; } catch(e) {}
    });
  });

  document.getElementById('torchBtn').addEventListener('click', async function(){
    if(!streamTrack) return;
    const caps = streamTrack.getCapabilities ? streamTrack.getCapabilities() : {};
    if(!caps.torch) return;
    torchOn = !torchOn;
    try { await streamTrack.applyConstraints({ advanced: [{ torch: torchOn }] }); } catch(e) {}
  });
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
  
  // Store current scan data
  currentScanData = resp;
  
  // Hide all action sections initially
  document.getElementById('scanPopupConfirmActions').classList.add('hidden');
  document.getElementById('scanPopupNextActions').classList.add('hidden');
  document.getElementById('scanPopupErrorActions').classList.add('hidden');
  
  if(resp.ok){ 
    label='Valid Entry'; 
    theme='background:#052e16;border:1px solid #14532d;color:#86efac'; 
    
    // Show detailed information for successful scans
    if(resp.ticket_type && resp.event_title){
      details.classList.remove('hidden');
      event.textContent = resp.event_title;
      ticketType.textContent = resp.ticket_type + ' Ticket';
      
      // Show customer info if available
      const customerInfo = document.getElementById('scanPopupCustomerInfo');
      if (resp.customer_name || resp.customer_phone || resp.ticket_code) {
        customerInfo.classList.remove('hidden');
        document.getElementById('scanPopupCustomerName').textContent = resp.customer_name ? `Customer: ${resp.customer_name}` : '';
        document.getElementById('scanPopupCustomerPhone').textContent = resp.customer_phone ? `Phone: ${resp.customer_phone}` : '';
        document.getElementById('scanPopupTicketCode').textContent = resp.ticket_code ? `Ticket Code: ${resp.ticket_code}` : '';
      }
      
      // Update last scan info
      const lastScanInfo = document.getElementById('lastScanInfo');
      const lastScanEvent = document.getElementById('lastScanEvent');
      const lastScanType = document.getElementById('lastScanType');
      if(lastScanInfo && lastScanEvent && lastScanType){
        lastScanInfo.classList.remove('hidden');
        lastScanEvent.textContent = resp.event_title;
        lastScanType.textContent = resp.ticket_type + ' Ticket';
      }
    } else if(resp.destination && resp.participants){
      // Travel booking details
      details.classList.remove('hidden');
      event.textContent = resp.destination;
      ticketType.textContent = resp.participants + ' participant(s) - ' + resp.travel_date;
      
      // Show booking info
      const bookingInfo = document.getElementById('scanPopupBookingInfo');
      bookingInfo.classList.remove('hidden');
      document.getElementById('scanPopupBookingRef').textContent = `Reference: ${resp.booking_reference || resp.code}`;
      document.getElementById('scanPopupDestination').textContent = `Destination: ${resp.destination}`;
      document.getElementById('scanPopupTravelDate').textContent = `Travel Date: ${resp.travel_date}`;
      document.getElementById('scanPopupParticipants').textContent = `Participants: ${resp.participants}`;
      
      // Update last scan info
      const lastScanInfo = document.getElementById('lastScanInfo');
      const lastScanEvent = document.getElementById('lastScanEvent');
      const lastScanType = document.getElementById('lastScanType');
      if(lastScanInfo && lastScanEvent && lastScanType){
        lastScanInfo.classList.remove('hidden');
        lastScanEvent.textContent = resp.destination;
        lastScanType.textContent = 'Travel Booking';
      }
    }
    
    // Show confirm/reject buttons for successful scans
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
  
  // animate in
  requestAnimationFrame(()=>{ 
    card.style.transform='scale(1)'; 
    card.style.opacity='1'; 
  });
}

// Event handlers for popup actions
document.addEventListener('DOMContentLoaded', function() {
  // Confirm entry
  document.getElementById('scanPopupConfirm').addEventListener('click', function() {
    if (currentScanData && currentScanData.ok) {
      // Show success message and next actions
      document.getElementById('scanPopupConfirmActions').classList.add('hidden');
      document.getElementById('scanPopupNextActions').classList.remove('hidden');
      document.getElementById('scanPopupMsg').textContent = 'Entry Confirmed Successfully!';
      document.getElementById('scanPopupBadge').textContent = 'Confirmed';
      document.getElementById('scanPopupBadge').setAttribute('style', 'background:#065f46;border:1px solid #047857;color:#10b981');
      
      // Add vibration for mobile
      try { navigator.vibrate && navigator.vibrate([100, 50, 100]); } catch(e) {}
    }
  });
  
  // Reject entry
  document.getElementById('scanPopupReject').addEventListener('click', function() {
    hidePopup();
    // Clear the input for next scan
    document.getElementById('codeInput').value = '';
    document.getElementById('codeInput').focus();
  });
  
  // Scan next
  document.getElementById('scanPopupScanNext').addEventListener('click', function() {
    hidePopup();
    // Clear the input for next scan
    document.getElementById('codeInput').value = '';
    document.getElementById('codeInput').focus();
  });
  
  // Close popup
  document.getElementById('scanPopupClose').addEventListener('click', hidePopup);
  document.getElementById('scanPopupErrorClose').addEventListener('click', hidePopup);
  
  // Click outside to close
  document.getElementById('scanPopup').addEventListener('click', function(e) {
    if (e.target === this) {
      hidePopup();
    }
  });
});

function hidePopup() {
  const modal = document.getElementById('scanPopup');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  
  // Reset all sections
  document.getElementById('scanPopupConfirmActions').classList.add('hidden');
  document.getElementById('scanPopupNextActions').classList.add('hidden');
  document.getElementById('scanPopupErrorActions').classList.add('hidden');
  document.getElementById('scanPopupDetails').classList.add('hidden');
  document.getElementById('scanPopupCustomerInfo').classList.add('hidden');
  document.getElementById('scanPopupBookingInfo').classList.add('hidden');
  
  currentScanData = null;
}
</script>



<?php /** @var array $assignedEvents */ ?>
<div class="max-w-xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-2">Ticket Scanner</h1>
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
			<label class="block text-sm mb-1">Enter Ticket Code</label>
			<div class="flex gap-2">
				<input name="code" id="codeInput" class="input" placeholder="6-digit code" required>
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
    <!-- Popup modal -->
    <div id="scanPopup" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="transform transition-all scale-95 opacity-0 bg-[#0f0f10] border border-gray-800 rounded-xl p-6 w-[90%] max-w-sm text-center shadow-2xl" id="scanPopupCard">
            <div id="scanPopupBadge" class="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-3">Status</div>
            <div id="scanPopupMsg" class="text-lg font-semibold mb-2">Message</div>
            <div id="scanPopupDetails" class="text-sm text-gray-300 mb-4 hidden">
                <div id="scanPopupEvent" class="mb-1"></div>
                <div id="scanPopupTicketType" class="font-semibold text-red-400"></div>
            </div>
            <button id="scanPopupClose" class="btn btn-primary w-full">Close</button>
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

// Popup helpers
function showScanPopup(resp){
  const modal=document.getElementById('scanPopup');
  const badge=document.getElementById('scanPopupBadge');
  const msg=document.getElementById('scanPopupMsg');
  const details=document.getElementById('scanPopupDetails');
  const event=document.getElementById('scanPopupEvent');
  const ticketType=document.getElementById('scanPopupTicketType');
  const card=document.getElementById('scanPopupCard');
  const close=document.getElementById('scanPopupClose');
  const text=(resp?.msg||'').toLowerCase();
  let theme=''; let label='';
  
  if(resp.ok){ 
    label='Confirmed'; 
    theme='background:#052e16;border:1px solid #14532d;color:#86efac'; 
    // Show ticket details for successful scans
    if(resp.ticket_type && resp.event_title){
      details.classList.remove('hidden');
      event.textContent = resp.event_title;
      ticketType.textContent = resp.ticket_type + ' Ticket';
      
      // Update last scan info
      const lastScanInfo = document.getElementById('lastScanInfo');
      const lastScanEvent = document.getElementById('lastScanEvent');
      const lastScanType = document.getElementById('lastScanType');
      if(lastScanInfo && lastScanEvent && lastScanType){
        lastScanInfo.classList.remove('hidden');
        lastScanEvent.textContent = resp.event_title;
        lastScanType.textContent = resp.ticket_type + ' Ticket';
      }
    }
  }
  else if(text.includes('redeemed')){ 
    label='Already Redeemed'; 
    theme='background:#1f2937;border:1px solid #374151;color:#e5e7eb'; 
    details.classList.add('hidden');
  }
  else { 
    label='Rejected'; 
    theme='background:#450a0a;border:1px solid #7f1d1d;color:#fecaca'; 
    details.classList.add('hidden');
  }
  
  badge.setAttribute('style', theme);
  badge.textContent=label;
  msg.textContent=resp.msg || '';
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  // animate in
  requestAnimationFrame(()=>{ card.style.transform='scale(1)'; card.style.opacity='1'; });
  function hide(){ modal.classList.add('hidden'); modal.classList.remove('flex'); window.location.reload(); }
  close.onclick=hide;
  modal.onclick=function(e){ if(e.target===modal) hide(); };
  setTimeout(hide, 1800);
}
</script>



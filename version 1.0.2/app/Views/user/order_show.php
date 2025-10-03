<?php /** @var array $order */ /** @var array $items */ /** @var array $payments */ /** @var array $tickets */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">Order #<?php echo (int)$order['id']; ?></h1>
		<a class="btn btn-secondary" href="<?php echo base_url('/user/orders'); ?>">Back</a>
	</div>
	<div class="grid md:grid-cols-3 gap-6">
		<div class="md:col-span-2 card p-6">
			<h2 class="font-semibold mb-3">Items</h2>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Event</th>
						<th class="p-3 text-left">Qty</th>
						<th class="p-3 text-left">Unit Price</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($items as $it): ?>
					<tr>
						<td class="p-3"><?php echo htmlspecialchars($it['title']); ?></td>
						<td class="p-3"><?php echo (int)$it['quantity']; ?></td>
						<td class="p-3"><?php echo number_format((float)$it['unit_price'], 2); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<h2 class="font-semibold mt-8 mb-3">Tickets</h2>
			<?php if (empty($tickets)): ?>
				<p class="text-gray-400">Tickets will appear here after payment is confirmed.</p>
			<?php else: ?>
				<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
					<?php foreach ($tickets as $t): ?>
                        <div class="border border-gray-700 rounded-lg p-4 bg-black/40">
							<div class="text-sm text-gray-400 mb-1"><?php echo htmlspecialchars($t['title'] ?? ''); ?></div>
							<div class="text-xs text-gray-500 mb-2"><?php echo htmlspecialchars(($t['event_date'] ?? '').' • '.($t['venue'] ?? '')); ?></div>
							<div class="bg-gray-900 rounded flex items-center justify-center">
								<img class="w-full h-48 object-contain" src="<?php echo base_url('/'.($t['qr_path'] ?? '')); ?>" alt="QR">
							</div>
							<div class="mt-3 flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-semibold tracking-widest">#<?php echo htmlspecialchars($t['code']); ?></div>
                                    <?php $status = strtolower($t['status'] ?? 'valid'); ?>
                                    <?php if ($status === 'redeemed'): ?>
                                        <span class="badge mt-1" style="background:#052e16;border-color:#14532d;color:#86efac">Verified</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button class="btn btn-secondary text-xs" data-ticket-code="<?php echo htmlspecialchars($t['code']); ?>" data-ticket-qr="<?php echo base_url('/'.($t['qr_path'] ?? '')); ?>">Download PDF</button>
                                    <a class="btn btn-secondary text-xs" href="<?php echo base_url('/tickets/download?code=' . urlencode($t['code'])); ?>">Server download</a>
                                </div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="card p-6">
			<h2 class="font-semibold mb-3">Summary</h2>
			<p><span class="text-gray-400">Total:</span> <span class="text-red-400 font-semibold"><?php echo htmlspecialchars($order['currency']); ?> <?php echo number_format((float)$order['total_amount'], 2); ?></span></p>
			<p class="mt-1"><span class="text-gray-400">Order status:</span> <?php echo htmlspecialchars($order['status']); ?></p>
			<h3 class="font-semibold mt-4 mb-2">Payments</h3>
			<?php if (empty($payments)): ?>
				<p class="text-gray-400">No payments yet.</p>
			<?php else: ?>
				<ul class="space-y-2 text-sm">
					<?php foreach ($payments as $p): ?>
					<li class="flex items-center justify-between"><span><?php echo strtoupper($p['provider']); ?> • <?php echo htmlspecialchars($p['provider_ref'] ?? ''); ?></span><span><?php echo htmlspecialchars($p['status']); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
(function(){
  const { jsPDF } = window.jspdf || {};
  function bySelAll(sel){ return Array.prototype.slice.call(document.querySelectorAll(sel)); }
  function isMobile(){ return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent); }
  bySelAll('[data-ticket-code]').forEach(function(btn){
    btn.addEventListener('click', async function(){
      if(!jsPDF) return;
      const code = this.getAttribute('data-ticket-code');
      const qr = this.getAttribute('data-ticket-qr');
      const doc = new jsPDF({ unit:'pt', format:'a4' });
      const pageW = doc.internal.pageSize.getWidth();
      const margin = 40;
      doc.setFont('helvetica','bold');
      doc.setFontSize(18);
      doc.text('Ticket #'+code, margin, 60);
      // QR
      try {
        const img = await fetch(qr).then(r=>r.blob()).then(b=>new Promise(res=>{ const fr=new FileReader(); fr.onload=()=>res(fr.result); fr.readAsDataURL(b); }));
        const imgW = 300, imgH = 300;
        doc.addImage(img, 'PNG', margin, 90, imgW, imgH);
      } catch(e) {}
      doc.setFont('helvetica','');
      doc.setFontSize(12);
      doc.text('Present this code and QR at entry.', margin, 420);
      const fileName = 'ticket-'+code+'.pdf';
      try {
        // Desktop browsers
        if(!isMobile()) {
          doc.save(fileName);
        } else {
          // Mobile-friendly: open in new tab or trigger download via anchor
          const blob = doc.output('blob');
          const url = URL.createObjectURL(blob);
          // Try opening in a new tab (lets user download from viewer)
          const win = window.open(url, '_blank');
          if (!win) {
            // Fallback: programmatic download
            const a = document.createElement('a');
            a.href = url; a.download = fileName; a.style.display='none';
            document.body.appendChild(a); a.click(); a.remove();
          }
          // Revoke after a while
          setTimeout(function(){ URL.revokeObjectURL(url); }, 10000);
        }
      } catch(e) {
        try { doc.save(fileName); } catch(_) {}
      }
    });
  });
})();

// Payment success popup for regular orders
function showPaymentSuccessPopup() {
    const popup = document.createElement('div');
    popup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    popup.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="text-green-500 text-6xl mb-4">✓</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Payment Successful!</h2>
            <p class="text-gray-600 mb-6">Your order has been confirmed. You will receive your tickets via email shortly.</p>
            <button onclick="closePaymentSuccessPopup()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium">
                Continue
            </button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Auto close after 5 seconds
    setTimeout(() => {
        closePaymentSuccessPopup();
    }, 5000);
}

function closePaymentSuccessPopup() {
    const popup = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
    if (popup) {
        popup.remove();
    }
}

// Check if we should show success popup
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const paymentSuccess = urlParams.get('payment_success');
    
    if (paymentSuccess === '1') {
        showPaymentSuccessPopup();
        // Clean up URL
        const newUrl = window.location.pathname + window.location.search.replace(/[?&]payment_success=1/, '');
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>



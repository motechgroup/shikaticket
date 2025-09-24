<?php /** @var array $orders */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">My Orders</h1>
		<div class="flex items-center gap-2">
			<button id="confirmPayBtn" class="btn btn-primary">Get My Ticket</button>
			<a class="btn btn-secondary" href="<?php echo base_url('/user/dashboard'); ?>">Back to Dashboard</a>
		</div>
	</div>
    <div class="card" id="ordersCard">
		<?php if (empty($orders)): ?>
			<div class="p-6 text-gray-400">No orders yet.</div>
		<?php else: ?>
            <table class="min-w-full text-sm table" id="ordersTable">
				<thead>
					<tr>
						<th class="p-3 text-left">Order #</th>
						<th class="p-3 text-left">Amount</th>
						<th class="p-3 text-left">Currency</th>
						<th class="p-3 text-left">Order Status</th>
						<th class="p-3 text-left">Payment Status</th>
						<th class="p-3 text-left">Date</th>
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
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>

<script>
(function(){
  const table = document.getElementById('ordersTable');
  if(!table) return;
  // Make rows clickable (delegate only on tbody rows)
  table.addEventListener('click', function(e){
    const row = e.target.closest('tbody tr[data-order-link]');
    if(row){ window.location.href = row.getAttribute('data-order-link'); }
  });
  const rows = table.querySelectorAll('tbody tr');
  if(rows.length === 0) return;
  const newest = rows[0];
  const idText = newest.querySelector('td a')?.textContent || '';
  const id = idText.replace('#','').trim();
  const urlId = new URLSearchParams(window.location.search).get('order_id');
  const focusId = urlId || id;
  if(!focusId) return;
  let attempts=0;
  const poll = setInterval(function(){
    attempts++;
    fetch('<?php echo base_url('/orders/status'); ?>?id=' + encodeURIComponent(focusId))
      .then(r=>r.json()).then(j=>{
        if(j.status === 'paid'){
          clearInterval(poll);
          window.location.href = '<?php echo base_url('/user/orders/show'); ?>?id=' + focusId;
        }
        if(attempts>30){
          clearInterval(poll);
          // Fallback reconciliation if callback missed
          fetch('<?php echo base_url('/pay/mpesa/reconcile'); ?>?order_id=' + encodeURIComponent(focusId))
            .then(()=>window.location.reload());
        }
      }).catch(()=>{});
  }, 2000);
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
        // Small delay to allow DB update
        setTimeout(()=>{ window.location.reload(); }, 1200);
      })
      .catch(()=>{ window.location.reload(); })
      .finally(()=>{ btn.disabled=false; btn.textContent='Get My Ticket'; });
  });
})();
</script>



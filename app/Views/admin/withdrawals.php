<div class="max-w-6xl mx-auto px-4 py-6 md:py-10">
	<?php $pageTitle = 'Withdrawals'; include __DIR__ . '/../components/mobile_nav_simple.php'; ?>
	<!-- Mobile-friendly header -->
	<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4 md:mb-6">
		<h1 class="text-xl md:text-2xl font-semibold">Withdrawals Management</h1>
		<a href="<?php echo base_url('/admin'); ?>" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
			← Back to Dashboard
		</a>
	</div>
	
	<!-- Search form -->
	<form method="get" class="mb-4 md:mb-6">
		<div class="flex flex-col sm:flex-row gap-2">
			<input class="input flex-1" type="text" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Search by status, organizer, or agency">
			<button class="btn btn-secondary touch-target">Search</button>
		</div>
	</form>

	<?php if (empty($withdrawals)): ?>
		<div class="card p-8 text-center">
			<div class="text-gray-400 mb-4">
				<svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
				</svg>
				<h3 class="text-lg font-medium mb-2">No Withdrawals Found</h3>
				<p class="text-gray-400"><?php echo !empty($q) ? 'No withdrawals match your search criteria.' : 'No withdrawal requests have been submitted yet.'; ?></p>
			</div>
		</div>
	<?php else: ?>
		<!-- Mobile-friendly withdrawals list -->
		<div class="space-y-4">
			<?php foreach ($withdrawals as $w): ?>
				<div class="card p-4 md:p-6">
					<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
						<!-- Main Info -->
						<div class="flex-1 min-w-0">
							<div class="flex items-start justify-between mb-3">
								<div class="min-w-0 flex-1">
									<h3 class="text-lg font-semibold text-white truncate">
										<?php echo htmlspecialchars($w['organizer_name'] ?? $w['agency_name'] ?? 'Unknown'); ?>
									</h3>
									<p class="text-sm text-gray-400 mt-1">
										<?php echo $w['organizer_name'] ? 'Event Organizer' : 'Travel Agency'; ?>
									</p>
								</div>
								<div class="ml-4 flex-shrink-0">
									<?php
									$statusColors = [
										'requested' => 'bg-yellow-600 text-yellow-200',
										'approved' => 'bg-blue-600 text-blue-200',
										'paid' => 'bg-green-600 text-green-200',
										'rejected' => 'bg-red-600 text-red-200'
									];
									$statusColor = $statusColors[$w['status']] ?? 'bg-gray-600 text-gray-200';
									?>
									<span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
										<?php echo ucfirst($w['status'] ?? 'Unknown'); ?>
									</span>
								</div>
							</div>

							<!-- Details -->
							<div class="flex flex-wrap items-center gap-4 text-sm text-gray-400 mb-3">
								<div class="flex items-center gap-1">
									<?php if ($w['event_title']): ?>
										<span class="px-2 py-1 bg-blue-600/20 text-blue-300 text-xs rounded-full">Event</span>
										<span class="text-xs"><?php echo htmlspecialchars($w['event_title']); ?></span>
									<?php elseif ($w['destination_title']): ?>
										<span class="px-2 py-1 bg-green-600/20 text-green-300 text-xs rounded-full">Travel</span>
										<span class="text-xs"><?php echo htmlspecialchars($w['destination_title']); ?></span>
									<?php else: ?>
										<span class="px-2 py-1 bg-gray-600/20 text-gray-300 text-xs rounded-full">General</span>
									<?php endif; ?>
								</div>
								<div class="flex items-center gap-1">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
									</svg>
									<span class="font-medium text-white">KES <?php echo number_format((float)($w['amount'] ?? 0), 2); ?></span>
								</div>
							</div>

							<!-- Dates -->
							<div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
								<div>
									<span class="font-medium">Requested:</span> 
									<?php echo date('M j, Y H:i', strtotime($w['created_at'] ?? '')); ?>
								</div>
								<?php if ($w['updated_at'] && $w['updated_at'] !== $w['created_at']): ?>
									<div>
										<span class="font-medium">Updated:</span> 
										<?php echo date('M j, Y H:i', strtotime($w['updated_at'])); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<!-- Actions -->
						<div class="flex flex-col sm:flex-row gap-2 lg:flex-col lg:min-w-0">
							<button onclick="openWithdrawalModal(<?php echo (int)$w['id']; ?>, '<?php echo htmlspecialchars($w['organizer_name'] ?? $w['agency_name'] ?? 'Unknown'); ?>', '<?php echo htmlspecialchars($w['status']); ?>', <?php echo (float)($w['amount'] ?? 0); ?>, '<?php echo htmlspecialchars($w['event_title'] ?? $w['destination_title'] ?? ''); ?>')" 
									class="btn btn-primary touch-target w-full sm:w-auto lg:w-full group">
								<svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
								</svg>
								Update Status
							</button>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<!-- Stylish Withdrawal Status Update Modal -->
<div id="withdrawalModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
	<div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
		<!-- Backdrop -->
		<div class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" onclick="closeWithdrawalModal()"></div>
		
		<!-- Modal Container -->
		<div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-gray-900 border border-gray-700 shadow-2xl rounded-2xl sm:my-8 sm:align-middle">
			<!-- Header -->
			<div class="flex items-center justify-between mb-6">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-blue-500/20 rounded-lg">
						<svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
						</svg>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-white">Update Withdrawal Status</h3>
						<p class="text-sm text-gray-400">Modify withdrawal request status</p>
					</div>
				</div>
				<button onclick="closeWithdrawalModal()" class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>
			
			<!-- Withdrawal Info -->
			<div class="mb-6 p-4 bg-gray-800/50 rounded-xl border border-gray-700">
				<div class="flex items-center justify-between mb-3">
					<div>
						<h4 class="font-medium text-white" id="modalWithdrawalName">-</h4>
						<p class="text-sm text-gray-400" id="modalWithdrawalType">-</p>
					</div>
					<div class="text-right">
						<div class="text-lg font-bold text-green-400" id="modalWithdrawalAmount">KES 0.00</div>
						<div class="text-xs text-gray-400">Current Status: <span id="modalCurrentStatus" class="font-medium">-</span></div>
					</div>
				</div>
				<div class="text-sm text-gray-400" id="modalWithdrawalDetails">-</div>
			</div>
			
			<!-- Status Update Form -->
			<form id="withdrawalUpdateForm" method="post" action="<?php echo base_url('/admin/withdrawals/update'); ?>" class="space-y-4">
				<?php echo csrf_field(); ?>
				<input type="hidden" name="id" id="modalWithdrawalId">
				
				<div>
					<label class="block text-sm font-medium text-gray-300 mb-2">New Status</label>
					<select name="status" id="modalStatusSelect" class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
						<option value="approved">✅ Approve</option>
						<option value="paid">💰 Mark as Paid</option>
						<option value="rejected">❌ Reject</option>
					</select>
				</div>
				
				<div>
					<label class="block text-sm font-medium text-gray-300 mb-2">Admin Notes (Optional)</label>
					<textarea name="admin_notes" id="modalAdminNotes" rows="3" 
							  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
							  placeholder="Add any notes about this status change..."></textarea>
				</div>
				
				<!-- Action Buttons -->
				<div class="flex gap-3 pt-4">
					<button type="button" onclick="closeWithdrawalModal()" 
							class="flex-1 px-4 py-3 text-gray-300 bg-gray-800 hover:bg-gray-700 rounded-lg font-medium transition-all duration-200 hover:scale-105">
						Cancel
					</button>
					<button type="submit" id="modalSubmitBtn"
							class="flex-1 px-4 py-3 text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg font-medium transition-all duration-200 hover:scale-105 flex items-center justify-center gap-2">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
						</svg>
						Update Status
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Success/Error Toast Notifications -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
// Modal state
let currentWithdrawalId = null;

// Open withdrawal modal
function openWithdrawalModal(id, name, currentStatus, amount, details) {
	currentWithdrawalId = id;
	
	// Update modal content
	document.getElementById('modalWithdrawalId').value = id;
	document.getElementById('modalWithdrawalName').textContent = name;
	document.getElementById('modalWithdrawalAmount').textContent = `KES ${amount.toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
	document.getElementById('modalCurrentStatus').textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
	document.getElementById('modalWithdrawalDetails').textContent = details || 'General withdrawal';
	document.getElementById('modalWithdrawalType').textContent = details ? 'Specific Event/Travel' : 'General Withdrawal';
	
	// Reset form
	document.getElementById('modalStatusSelect').value = currentStatus === 'requested' ? 'approved' : currentStatus;
	document.getElementById('modalAdminNotes').value = '';
	
	// Show modal with animation
	const modal = document.getElementById('withdrawalModal');
	modal.classList.remove('hidden');
	modal.style.display = 'block';
	
	// Animate in
	setTimeout(() => {
		modal.querySelector('.bg-gray-900').style.transform = 'scale(1)';
		modal.querySelector('.bg-gray-900').style.opacity = '1';
	}, 10);
}

// Close withdrawal modal
function closeWithdrawalModal() {
	const modal = document.getElementById('withdrawalModal');
	const modalContent = modal.querySelector('.bg-gray-900');
	
	// Animate out
	modalContent.style.transform = 'scale(0.95)';
	modalContent.style.opacity = '0';
	
	setTimeout(() => {
		modal.classList.add('hidden');
		modal.style.display = 'none';
		modalContent.style.transform = 'scale(1)';
		modalContent.style.opacity = '1';
	}, 200);
}

// Handle form submission
document.getElementById('withdrawalUpdateForm').addEventListener('submit', function(e) {
	e.preventDefault();
	
	const formData = new FormData(this);
	const status = formData.get('status');
	const notes = formData.get('admin_notes');
	
	// Show loading state
	const submitBtn = document.getElementById('modalSubmitBtn');
	const originalText = submitBtn.innerHTML;
	submitBtn.innerHTML = `
		<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
		</svg>
		Updating...
	`;
	submitBtn.disabled = true;
	
	// Submit form
	fetch(this.action, {
		method: 'POST',
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		},
		body: formData
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			showToast('success', `Withdrawal status updated to "${status}" successfully!`);
			closeWithdrawalModal();
			// Reload page after a short delay
			setTimeout(() => {
				window.location.reload();
			}, 1500);
		} else {
			showToast('error', data.message || 'Failed to update withdrawal status');
			submitBtn.innerHTML = originalText;
			submitBtn.disabled = false;
		}
	})
	.catch(error => {
		console.error('Error:', error);
		showToast('error', 'An error occurred while updating the withdrawal status');
		submitBtn.innerHTML = originalText;
		submitBtn.disabled = false;
	});
});

// Toast notification system
function showToast(type, message) {
	const container = document.getElementById('toastContainer');
	const toast = document.createElement('div');
	
	const colors = {
		success: 'bg-green-600 border-green-500',
		error: 'bg-red-600 border-red-500',
		warning: 'bg-yellow-600 border-yellow-500',
		info: 'bg-blue-600 border-blue-500'
	};
	
	const icons = {
		success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
		error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
		warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>',
		info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
	};
	
	toast.className = `flex items-center gap-3 p-4 rounded-lg border ${colors[type]} text-white shadow-lg transform translate-x-full transition-all duration-300`;
	toast.innerHTML = `
		<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			${icons[type]}
		</svg>
		<span class="flex-1">${message}</span>
		<button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">
			<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
			</svg>
		</button>
	`;
	
	container.appendChild(toast);
	
	// Animate in
	setTimeout(() => {
		toast.classList.remove('translate-x-full');
		toast.classList.add('translate-x-0');
	}, 10);
	
	// Auto remove after 5 seconds
	setTimeout(() => {
		toast.classList.add('translate-x-full');
		setTimeout(() => {
			if (toast.parentElement) {
				toast.remove();
			}
		}, 300);
	}, 5000);
}

// Close modal when clicking outside
document.getElementById('withdrawalModal').addEventListener('click', function(e) {
	if (e.target === this) {
		closeWithdrawalModal();
	}
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape' && !document.getElementById('withdrawalModal').classList.contains('hidden')) {
		closeWithdrawalModal();
	}
});
</script>

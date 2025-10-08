<div class="max-w-md mx-auto px-4 py-12">
	<h1 class="text-2xl font-semibold mb-6">Organizer Registration</h1>
	<!-- Cache buster: Updated with modern notifications - <?php echo date('Y-m-d H:i:s'); ?> -->
	
	<!-- Success Notification -->
	<?php if ($msg = flash_get('success')): ?>
		<div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg flex items-start gap-3">
			<div class="flex-shrink-0 w-5 h-5 mt-0.5">
				<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
				</svg>
			</div>
			<div>
				<h3 class="text-green-400 font-semibold text-sm">Success!</h3>
				<p class="text-green-300 text-sm mt-1"><?php echo htmlspecialchars($msg); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<!-- Error Notification -->
	<?php if ($msg = flash_get('error')): ?>
		<div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg flex items-start gap-3">
			<div class="flex-shrink-0 w-5 h-5 mt-0.5">
				<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
			</div>
			<div>
				<h3 class="text-red-400 font-semibold text-sm">Registration Failed</h3>
				<p class="text-red-300 text-sm mt-1"><?php echo htmlspecialchars($msg); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<!-- Warning Notification -->
	<?php if ($msg = flash_get('warning')): ?>
		<div class="mb-6 p-4 bg-yellow-500/20 border border-yellow-500/50 rounded-lg flex items-start gap-3">
			<div class="flex-shrink-0 w-5 h-5 mt-0.5">
				<svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
				</svg>
			</div>
			<div>
				<h3 class="text-yellow-400 font-semibold text-sm">Warning</h3>
				<p class="text-yellow-300 text-sm mt-1"><?php echo htmlspecialchars($msg); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<!-- Info Notification -->
	<?php if ($msg = flash_get('info')): ?>
		<div class="mb-6 p-4 bg-blue-500/20 border border-blue-500/50 rounded-lg flex items-start gap-3">
			<div class="flex-shrink-0 w-5 h-5 mt-0.5">
				<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
			</div>
			<div>
				<h3 class="text-blue-400 font-semibold text-sm">Information</h3>
				<p class="text-blue-300 text-sm mt-1"><?php echo htmlspecialchars($msg); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo base_url('/organizer/register'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<div>
			<label class="block text-sm mb-1">Full Names</label>
			<input name="full_name" type="text" required class="input">
		</div>
		<div>
			<label class="block text-sm mb-1">Phone Number</label>
			<input name="phone" type="tel" required class="input">
		</div>
		<div>
			<label class="block text-sm mb-1">Email</label>
			<input name="email" type="email" required class="input">
		</div>
		<div>
			<label class="block text-sm mb-1">Password</label>
			<input name="password" type="password" required class="input">
		</div>
		<button class="w-full btn btn-primary">Create organizer</button>
	</form>
</div>

<script>
// Auto-dismiss notifications after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('[class*="bg-green-500/20"], [class*="bg-red-500/20"], [class*="bg-yellow-500/20"], [class*="bg-blue-500/20"]');
    
    notifications.forEach(notification => {
        // Add entrance animation
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-10px)';
        notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 100);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
        
        // Add close button functionality
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        `;
        closeBtn.className = 'ml-auto text-gray-400 hover:text-white transition-colors cursor-pointer';
        closeBtn.onclick = () => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        };
        
        notification.appendChild(closeBtn);
    });
});

// Form validation enhancement
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('input[required]');
    let hasErrors = false;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            hasErrors = true;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    if (hasErrors) {
        e.preventDefault();
        // Show a temporary error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm';
        errorDiv.textContent = 'Please fill in all required fields.';
        
        const form = this;
        form.insertBefore(errorDiv, form.firstChild);
        
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 3000);
    }
});

// Remove error styling on input
document.querySelectorAll('input[required]').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('border-red-500');
    });
});
</script>



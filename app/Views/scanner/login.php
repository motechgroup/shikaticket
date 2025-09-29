<style>
.uppercase-input {
    text-transform: uppercase !important;
    font-family: 'Courier New', monospace !important;
    letter-spacing: 2px;
}

.uppercase-input:focus {
    text-transform: uppercase !important;
}

/* Mobile-specific styles */
@media (max-width: 768px) {
    .uppercase-input {
        font-size: 18px !important; /* Prevents zoom on iOS */
        text-transform: uppercase !important;
    }
}
</style>

<div class="max-w-md mx-auto px-4 py-12">
	<h1 class="text-2xl font-semibold mb-6">Scanner Device Login</h1>
	<form method="post" action="<?php echo base_url('/scanner/login'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<div>
			<label class="block text-sm mb-1">Device Code</label>
			<input name="device_code" type="text" required class="input uppercase-input" placeholder="T123456" autocapitalize="characters" autocorrect="off" spellcheck="false" autocomplete="off" value="<?php echo htmlspecialchars($_GET['device_code'] ?? ''); ?>">
		</div>
		<button class="btn btn-primary w-full">Login</button>
	</form>
	<div class="mt-4 text-sm text-gray-400 text-center">
		<p>Enter the device code provided by the organizer or travel agency.</p>
		<p class="mt-2">Device must be active and properly configured.</p>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deviceCodeInput = document.querySelector('input[name="device_code"]');
    if (deviceCodeInput) {
        // Force focus and uppercase on mobile
        deviceCodeInput.addEventListener('focus', function() {
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
        
        deviceCodeInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
        
        // Also convert on paste
        deviceCodeInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                e.target.value = e.target.value.toUpperCase();
            }, 10);
        });
        
        // Prevent lowercase on mobile keyboards
        deviceCodeInput.addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (char && char !== char.toUpperCase()) {
                e.preventDefault();
                this.value += char.toUpperCase();
            }
        });
    }
});
</script>



<?php
$pageTitle = 'Verify Phone Number';
?>
<div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-red-100">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="mt-4 text-center text-2xl md:text-3xl font-extrabold text-white">
                Verify Your Phone Number
            </h2>
            <p class="mt-2 text-center text-sm text-gray-300">
                Enter the 6-digit code sent to<br>
                <strong class="text-white"><?php echo htmlspecialchars($phone ?? ''); ?></strong>
            </p>
        </div>

        <?php if ($error = flash_get('error')): ?>
            <div class="rounded-md bg-red-50 border border-red-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success = flash_get('success')): ?>
            <div class="rounded-md bg-green-50 border border-green-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($warning = flash_get('warning')): ?>
            <div class="rounded-md bg-yellow-50 border border-yellow-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800"><?php echo htmlspecialchars($warning); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form class="mt-6 space-y-6" method="POST" action="<?php echo base_url('/travel/verify-otp'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-200">Verification Code</label>
                <input id="otp" name="otp" type="text" required autofocus maxlength="6" pattern="[0-9]{6}"
                       class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-600 placeholder-gray-400 text-gray-900 bg-white rounded-md text-center text-2xl tracking-widest font-mono focus:outline-none focus:ring-red-500 focus:border-red-500" 
                       placeholder="000000"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <p class="mt-2 text-xs text-gray-400">Enter the 6-digit code sent to your phone</p>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Verify Phone Number
                </button>
            </div>
        </form>

        <div class="flex items-center justify-between text-sm mt-6">
            <div class="text-center w-full">
                <p class="text-gray-300">
                    Didn't receive the code?
                </p>
                <form method="POST" action="<?php echo base_url('/travel/resend-otp'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="mt-2 text-red-600 hover:text-red-500 font-medium underline">
                        Resend OTP
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center space-y-2">
            <p class="text-xs text-gray-400">
                Having trouble? Contact support at<br>
                <a href="mailto:contact@shikaticket.com" class="text-red-600 hover:text-red-500">contact@shikaticket.com</a>
            </p>
            <div class="pt-4">
                <a href="<?php echo base_url('/'); ?>" class="text-sm text-gray-300 hover:text-gray-400">
                    ← Back to home
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-submit form when 6 digits are entered
document.getElementById('otp').addEventListener('input', function(e) {
    if (this.value.length === 6) {
        // Small delay to ensure user sees the complete code
        setTimeout(() => {
            this.form.submit();
        }, 300);
    }
});
</script>


<?php
$pageTitle = 'Travel Scanner Login';
?>
<div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-red-900">
                <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
            </div>
            <h2 class="mt-4 text-center text-2xl md:text-3xl font-extrabold text-white">
                Travel Scanner Login
            </h2>
            <p class="mt-2 text-center text-sm text-gray-300">
                Enter your scanner device code to access the verification system
            </p>
        </div>
        <form class="mt-6 space-y-4" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div>
                <label for="device_code" class="sr-only">Device Code</label>
                <input id="device_code" name="device_code" type="text" required 
                       class="appearance-none relative block w-full px-3 py-2 border border-gray-600 bg-dark-card placeholder-gray-400 text-white rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm" 
                       placeholder="Enter device code (e.g., TRAVEL_ABC12345)">
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Access Scanner
                </button>
            </div>

            <div class="text-center">
                <a href="<?php echo base_url('/'); ?>" class="text-sm text-gray-400 hover:text-gray-300">
                    ← Back to main site
                </a>
            </div>
        </form>
        
        <div class="mt-6 p-4 bg-blue-900 border border-blue-600 rounded-lg">
            <h3 class="font-semibold text-blue-300 mb-2">Need a device code?</h3>
            <p class="text-sm text-blue-200">
                Contact your travel agency administrator to get a scanner device code, or 
                <a href="<?php echo base_url('/travel/login'); ?>" class="text-blue-400 hover:text-blue-300">login to your travel portal</a> 
                to create scanner devices.
            </p>
        </div>
    </div>
</div>

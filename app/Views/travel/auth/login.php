<?php
$pageTitle = 'Travel Agency Login';
?>
<div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-red-900">
                <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h2 class="mt-4 text-center text-2xl md:text-3xl font-extrabold text-white">
                Travel Agency Portal
            </h2>
            <p class="mt-2 text-center text-sm text-gray-300">
                Sign in to your travel agency account
            </p>
        </div>
        <form class="mt-6 space-y-4" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-600 bg-dark-card placeholder-gray-400 text-white rounded-t-md focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-600 bg-dark-card placeholder-gray-400 text-white rounded-b-md focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm" 
                           placeholder="Password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Sign in
                </button>
            </div>

            <div class="text-center">
                <a href="<?php echo base_url('/travel/register'); ?>" class="text-sm text-red-400 hover:text-red-300">
                    Don't have an account? Register your travel agency
                </a>
            </div>

            <div class="text-center">
                <a href="<?php echo base_url('/'); ?>" class="text-sm text-gray-400 hover:text-gray-300">
                    ← Back to main site
                </a>
            </div>
            
            <?php if (isset($_SESSION['travel_agency_id'])): ?>
            <div class="text-center mt-4">
                <a href="<?php echo base_url('/travel/clear-session'); ?>" class="text-xs text-red-400 hover:text-red-300">
                    Clear existing session
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

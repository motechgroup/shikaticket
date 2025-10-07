<?php
/**
 * Mobile Back Navigation Component
 * Reusable component for admin pages to provide back navigation
 */
?>

<!-- Mobile Back Navigation Component -->
<div class="md:hidden mb-4 mobile-back-nav">
	<div class="flex items-center gap-3">
		<!-- Back Button -->
		<button 
			id="mobileBackButton" 
			class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-gray-600 bg-gray-800/60 text-white cursor-pointer hover:bg-gray-700/60 transition-colors touch-target"
			onclick="goBack()"
			aria-label="Go back"
			title="Go back">
			<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
			</svg>
		</button>
		
		<!-- Page Title -->
		<div class="flex-1">
			<span class="text-white text-sm font-medium" id="mobilePageTitle">
				<?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Page'; ?>
			</span>
			<?php if (isset($showBreadcrumb) && $showBreadcrumb): ?>
				<div class="text-xs text-gray-400 mt-1">
					<span>Admin</span>
					<?php if (isset($breadcrumbItems)): ?>
						<?php foreach ($breadcrumbItems as $item): ?>
							<span> › </span>
							<span><?php echo htmlspecialchars($item); ?></span>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		
		<!-- Menu Button -->
		<button 
			class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-red-500 bg-red-900/60 text-white cursor-pointer hover:bg-red-800/60 transition-colors touch-target"
			onclick="toggleMobileMenu()"
			aria-label="Open admin menu"
			title="Open admin menu (Alt+M)">
			<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
			</svg>
		</button>
	</div>
</div>

<script>
// Mobile back navigation functionality (only load once)
if (typeof window.mobileBackNavLoaded === 'undefined') {
	window.mobileBackNavLoaded = true;
	
	// Mobile back navigation functionality
	function goBack() {
		const lastUrl = sessionStorage.getItem('mobileAdminLastUrl');
		if (lastUrl && lastUrl !== window.location.href) {
			window.location.href = lastUrl;
		} else {
			// Fallback to dashboard if no previous page
			window.location.href = '<?php echo base_url('/admin'); ?>';
		}
	}
	
	// Check if we should show back button
	function checkMobileNavigationState() {
		const isMobile = window.innerWidth <= 768;
		const currentUrl = window.location.href;
		const isDashboard = currentUrl.includes('/admin') && !currentUrl.includes('/admin/') || currentUrl.endsWith('/admin');
		
		if (isMobile) {
			const backButton = document.getElementById('mobileBackButton');
			const pageTitle = document.getElementById('mobilePageTitle');
			
			if (!isDashboard) {
				// Show back button and update page title
				if (backButton) backButton.classList.remove('hidden');
				const lastPage = sessionStorage.getItem('mobileAdminLastPage') || 'Dashboard';
				if (pageTitle && !pageTitle.textContent.includes('Admin Page')) {
					pageTitle.textContent = getCurrentPageTitle();
				}
			} else {
				// Hide back button and show menu title
				if (backButton) backButton.classList.add('hidden');
				if (pageTitle) {
					pageTitle.textContent = 'Admin Menu';
				}
			}
		}
	}
	
	// Get current page title based on URL
	function getCurrentPageTitle() {
		const currentUrl = window.location.href;
		const urlParts = currentUrl.split('/');
		const lastPart = urlParts[urlParts.length - 1];
		
		// Map URLs to page titles
		const pageTitles = {
			'users': 'Users',
			'organizers': 'Organizers',
			'events': 'Events',
			'categories': 'Categories',
			'banners': 'Banners',
			'pages': 'Pages',
			'featured-content': 'Featured Content',
			'notification-templates': 'Notification Templates',
			'partners': 'Partners',
			'partner-logos': 'Partner Logos',
			'travel': 'Travel',
			'agencies': 'Travel Agencies',
			'destinations': 'Travel Destinations',
			'scanners': 'Travel Scanners',
			'assignments': 'Scanner Assignments',
			'travel-banners': 'Travel Banners',
			'hotels': 'Hotel Applications',
			'email-templates': 'Email Templates',
			'sms-templates': 'SMS Templates',
			'communications': 'Communications',
			'points': 'Loyalty Points',
			'scans': 'Scans',
			'withdrawals': 'Withdrawals',
			'finance': 'Finance',
			'settings': 'Settings',
			'profile': 'My Profile'
		};
		
		// Check for specific patterns
		if (currentUrl.includes('/admin/travel/agencies')) return 'Travel Agencies';
		if (currentUrl.includes('/admin/travel/destinations')) return 'Travel Destinations';
		if (currentUrl.includes('/admin/travel/scanners')) return 'Travel Scanners';
		if (currentUrl.includes('/admin/travel/scanners/assignments')) return 'Scanner Assignments';
		if (currentUrl.includes('/admin/accounts/create')) return 'Create Accounts';
		
		return pageTitles[lastPart] || 'Admin Page';
	}
	
	// Initialize mobile navigation state
	document.addEventListener('DOMContentLoaded', function() {
		checkMobileNavigationState();
		
		// Update on window resize
		window.addEventListener('resize', checkMobileNavigationState);
		
		// Update page title when navigating
		window.addEventListener('popstate', checkMobileNavigationState);
	});
}
</script>

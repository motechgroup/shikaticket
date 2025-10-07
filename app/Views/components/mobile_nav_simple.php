<?php
/**
 * Simple Mobile Navigation Component
 * Usage: include with $pageTitle parameter
 */
$pageTitle = $pageTitle ?? 'Admin Page';
?>

<!-- Mobile Back Navigation -->
<div class="md:hidden mb-4">
	<div class="flex items-center gap-3">
		<button 
			class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-gray-600 bg-gray-800/60 text-white cursor-pointer hover:bg-gray-700/60 transition-colors touch-target"
			onclick="history.back()"
			aria-label="Go back"
			title="Go back">
			<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
			</svg>
		</button>
		<div class="flex-1">
			<span class="text-white text-sm font-medium"><?php echo htmlspecialchars($pageTitle); ?></span>
		</div>
		<button 
			class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-red-500 bg-red-900/60 text-white cursor-pointer hover:bg-red-800/60 transition-colors touch-target"
			onclick="toggleMobileMenu()"
			aria-label="Open admin menu"
			title="Open admin menu">
			<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
			</svg>
		</button>
	</div>
</div>

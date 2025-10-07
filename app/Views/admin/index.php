<?php /** @var array $orders */ /** @var array $byCurrency */ /** @var array $pendingWithdrawals */ ?>
<div class="max-w-6xl mx-auto px-4 py-6 md:py-10">
		<!-- Mobile Navigation Header -->
	<div class="md:hidden mb-4">
		<div class="flex items-center gap-3">
			<!-- Back Button (hidden by default, shown via JavaScript) -->
			<button 
				id="mobileBackButton" 
				class="hidden inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-gray-600 bg-gray-800/60 text-white cursor-pointer hover:bg-gray-700/60 transition-colors touch-target"
				onclick="goBack()"
				aria-label="Go back"
				title="Go back">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
				</svg>
			</button>
			
			<!-- Menu Button -->
			<button 
				id="adminMobileToggleFallback" 
				class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 border-red-500 bg-red-900/60 text-white cursor-pointer hover:bg-red-800/60 transition-colors touch-target"
				onclick="toggleMobileMenu()"
				aria-label="Open admin menu"
				title="Open admin menu (Alt+M)">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
			</button>
			
			<div class="flex-1">
				<span class="text-white text-sm font-medium" id="mobilePageTitle">Admin Menu</span>
				<kbd class="ml-2 px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded">Alt+M</kbd>
			</div>
		</div>
	</div>
	
	<div class="flex items-center justify-between mb-4 md:mb-6">
		<h1 class="text-xl md:text-2xl font-semibold text-white">Admin Dashboard</h1>
	</div>
	
	<!-- Mobile-friendly stats grid with icons -->
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 text-center md:text-left group hover:border-green-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition-colors">
					<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">System Paid Orders</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-green-400"><?php echo (int)($orders['total_orders'] ?? 0); ?></div>
		</div>
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 text-center md:text-left group hover:border-blue-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition-colors">
					<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">System Events Revenue</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-blue-400">KES <?php echo number_format((float)($orders['gross'] ?? 0), 2); ?></div>
		</div>
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 group hover:border-purple-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
					<svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">System Travel Revenue</div>
			</div>
			<div class="text-sm md:text-base space-y-1 text-purple-400">
				<div class="font-semibold">Bookings: <?php echo (int)($systemTravel['bookings'] ?? 0); ?></div>
				<div class="font-semibold">KES <?php echo number_format((float)($systemTravel['revenue'] ?? 0), 2); ?></div>
			</div>
		</div>
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 text-center md:text-left group hover:border-orange-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-orange-500/20 rounded-lg group-hover:bg-orange-500/30 transition-colors">
					<svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Platform Commission</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-orange-400">KES <?php echo number_format((float)($commissionRevenue ?? 0), 2); ?></div>
		</div>
	</div>

	<!-- Additional Site Statistics -->
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
		<?php
		// Get additional statistics
		$totalUsers = 0;
		$totalOrganizers = 0;
		$totalEvents = 0;
		$activeEvents = 0;
		
		try {
			$stmt = db()->query('SELECT COUNT(*) as count FROM users');
			$totalUsers = $stmt->fetch()['count'] ?? 0;
			
			$stmt = db()->query('SELECT COUNT(*) as count FROM organizers');
			$totalOrganizers = $stmt->fetch()['count'] ?? 0;
			
			$stmt = db()->query('SELECT COUNT(*) as count FROM events');
			$totalEvents = $stmt->fetch()['count'] ?? 0;
			
			$stmt = db()->query('SELECT COUNT(*) as count FROM events WHERE status = "active"');
			$activeEvents = $stmt->fetch()['count'] ?? 0;
		} catch (\PDOException $e) {
			// Handle database errors gracefully
		}
		?>
		
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 text-center md:text-left group hover:border-blue-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition-colors">
					<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Total Users</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-blue-400"><?php echo number_format($totalUsers); ?></div>
		</div>
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 text-center md:text-left group hover:border-green-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition-colors">
					<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Total Organizers</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-green-400"><?php echo number_format($totalOrganizers); ?></div>
		</div>
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 text-center md:text-left group hover:border-purple-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
					<svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Total Events</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-purple-400"><?php echo number_format($totalEvents); ?></div>
		</div>
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 text-center md:text-left group hover:border-orange-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-orange-500/20 rounded-lg group-hover:bg-orange-500/30 transition-colors">
					<svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Active Events</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-orange-400"><?php echo number_format($activeEvents); ?></div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="mb-4 md:mb-6">
		<h3 class="text-sm font-semibold text-gray-400 mb-3 uppercase tracking-wider">Quick Actions</h3>
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
			<a href="<?php echo base_url('/admin/categories'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-yellow-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-yellow-500/20 rounded-lg group-hover:bg-yellow-500/30 transition-colors">
						<svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h7a2 2 0 012 2v8a2 2 0 01-2 2H3zM14 7h7M14 12h7M14 17h7"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Categories</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/points'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-pink-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-pink-500/20 rounded-lg group-hover:bg-pink-500/30 transition-colors">
						<svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2 0 1.657 1.343 3 3 3s3 .895 3 2-1.343 2-3 2m0-9V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Loyalty Points</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/users'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-blue-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition-colors">
						<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Manage Users</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/events'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-purple-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
						<svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Manage Events</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/events/create'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-purple-500/70 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
						<svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Create Event</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/communications'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-green-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition-colors">
						<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Send Messages</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/settings'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-gray-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-gray-500/20 rounded-lg group-hover:bg-gray-500/30 transition-colors">
						<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Settings</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/scanners'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-teal-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-teal-500/20 rounded-lg group-hover:bg-teal-500/30 transition-colors">
						<svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A2 2 0 0122 9.528V14.5a2 2 0 01-1.106 1.789L15 18m0-8l-6 3m6-3V18m-6-3l-4.553 2.276A2 2 0 012 14.472V9.5A2 2 0 013.106 7.711L9 5m0 10V5"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Admin Scanners</span>
				</div>
			</a>
			<a href="<?php echo base_url('/scanner'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-lime-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-lime-500/20 rounded-lg group-hover:bg-lime-500/30 transition-colors">
						<svg class="w-5 h-5 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M3 12h18M3 19h18"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">System Scanner</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/travel/destinations/create'); ?>" class="bg-gray-800 border border-gray-700 rounded-lg p-4 md:p-6 hover:border-cyan-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-cyan-500/20 rounded-lg group-hover:bg-cyan-500/30 transition-colors">
						<svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Create Destination</span>
				</div>
			</a>
		</div>
	</div>

	<!-- System Overview -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
		<!-- Pending Withdrawals -->
		<div class="card p-4">
			<div class="flex items-center justify-between mb-3">
				<h2 class="font-semibold text-sm md:text-base">Pending Withdrawals</h2>
				<?php if (!empty($pendingWithdrawals)): ?>
					<a href="<?php echo base_url('/admin/withdrawals'); ?>" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
						View All →
					</a>
				<?php endif; ?>
			</div>
			<?php if (empty($pendingWithdrawals)): ?>
				<div class="p-4 md:p-6 text-gray-400 text-sm">No pending requests.</div>
			<?php else: ?>
				<div class="space-y-2">
					<?php foreach (array_slice($pendingWithdrawals, 0, 3) as $w): ?>
						<a href="<?php echo base_url('/admin/withdrawals'); ?>" class="block p-3 rounded-lg bg-gray-800/30 hover:bg-gray-800/50 transition-all duration-200 group">
							<div class="flex items-center justify-between">
								<div class="flex-1 min-w-0">
									<div class="flex items-center gap-2 mb-1">
										<div class="font-medium text-xs md:text-sm text-white group-hover:text-blue-300 transition-colors">
											<?php echo htmlspecialchars($w['organizer_name'] ?? $w['agency_name'] ?? 'Unknown'); ?>
										</div>
										<div class="px-2 py-1 bg-yellow-600/20 text-yellow-300 text-xs rounded-full">
											<?php echo $w['organizer_name'] ? 'Event' : 'Travel'; ?>
										</div>
									</div>
									<div class="text-xs text-gray-400">
										<?php echo date('M j, Y H:i', strtotime($w['created_at'])); ?>
									</div>
								</div>
								<div class="text-right">
									<div class="text-sm font-semibold text-green-400">
										<?php echo htmlspecialchars($w['currency']); ?> <?php echo number_format((float)$w['amount'], 2); ?>
									</div>
									<div class="text-xs text-gray-400">
										Click to manage
									</div>
								</div>
							</div>
						</a>
					<?php endforeach; ?>
					
					<?php if (count($pendingWithdrawals) > 3): ?>
						<div class="text-center pt-2">
							<a href="<?php echo base_url('/admin/withdrawals'); ?>" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
								+<?php echo count($pendingWithdrawals) - 3; ?> more pending withdrawals →
							</a>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Recent Activity -->
		<div class="card p-4">
			<h2 class="font-semibold mb-3 text-sm md:text-base">Recent Activity</h2>
			<?php
			// Get recent activity data
			$recentActivity = [];
			try {
				// Recent orders
				$stmt = db()->query('SELECT "Order" as type, id, created_at FROM orders ORDER BY created_at DESC LIMIT 3');
				$recentOrders = $stmt->fetchAll();
				
				// Recent events
				$stmt = db()->query('SELECT "Event" as type, id, title as name, created_at FROM events ORDER BY created_at DESC LIMIT 3');
				$recentEvents = $stmt->fetchAll();
				
				// Recent users
				$stmt = db()->query('SELECT "User" as type, id, email as name, created_at FROM users ORDER BY created_at DESC LIMIT 3');
				$recentUsers = $stmt->fetchAll();
				
				// Combine and sort by date
				$recentActivity = array_merge($recentOrders, $recentEvents, $recentUsers);
				usort($recentActivity, function($a, $b) {
					return strtotime($b['created_at']) - strtotime($a['created_at']);
				});
				
				$recentActivity = array_slice($recentActivity, 0, 5);
			} catch (\PDOException $e) {
				// Handle database errors gracefully
			}
			?>
			
			<?php if (empty($recentActivity)): ?>
				<div class="p-4 md:p-6 text-gray-400 text-sm">No recent activity.</div>
			<?php else: ?>
				<div class="space-y-3">
					<?php foreach ($recentActivity as $activity): ?>
						<div class="flex items-center justify-between py-2 border-b border-gray-800/30 last:border-b-0">
							<div class="flex items-center gap-3">
								<div class="w-2 h-2 rounded-full <?php 
									echo $activity['type'] === 'Order' ? 'bg-green-500' : 
										($activity['type'] === 'Event' ? 'bg-purple-500' : 'bg-blue-500'); 
								?>"></div>
								<div>
									<div class="text-xs md:text-sm font-medium">
										New <?php echo htmlspecialchars($activity['type']); ?>
									</div>
									<div class="text-xs text-gray-400">
										<?php echo isset($activity['name']) ? htmlspecialchars($activity['name']) : 'ID: ' . $activity['id']; ?>
									</div>
								</div>
							</div>
							<div class="text-xs text-gray-400">
								<?php echo date('M j', strtotime($activity['created_at'])); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Feature Requests -->
		<div class="card p-4">
			<div class="flex items-center justify-between mb-3">
				<h2 class="font-semibold text-sm md:text-base">Feature Requests</h2>
				<?php if (!empty($featureRequests)): ?>
					<a href="<?php echo base_url('/admin/featured-content'); ?>" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
						View All →
					</a>
				<?php endif; ?>
			</div>
			<?php if (empty($featureRequests)): ?>
				<div class="p-4 md:p-6 text-gray-400 text-sm">No pending feature requests.</div>
			<?php else: ?>
				<div class="space-y-2">
					<?php foreach (array_slice($featureRequests, 0, 3) as $request): ?>
						<a href="<?php echo base_url('/admin/featured-content'); ?>" class="block p-3 rounded-lg bg-gray-800/30 hover:bg-gray-800/50 transition-all duration-200 group">
							<div class="flex items-center justify-between">
								<div class="flex-1 min-w-0">
									<div class="flex items-center gap-2 mb-1">
										<div class="font-medium text-xs md:text-sm text-white group-hover:text-blue-300 transition-colors">
											<?php echo htmlspecialchars($request['content_title']); ?>
										</div>
										<div class="px-2 py-1 bg-yellow-600/20 text-yellow-300 text-xs rounded-full">
											<?php echo ucfirst(str_replace('_', ' ', $request['content_type'])); ?>
										</div>
									</div>
									<div class="text-xs text-gray-400">
										by <?php echo htmlspecialchars($request['requester_name']); ?>
									</div>
									<div class="text-xs text-gray-400">
										<?php echo date('M j, Y H:i', strtotime($request['created_at'])); ?>
									</div>
								</div>
								<div class="text-right">
									<div class="text-sm font-semibold text-yellow-400">
										<?php echo $request['requested_commission']; ?>% Commission
									</div>
									<div class="text-xs text-gray-400">
										Click to review
									</div>
								</div>
							</div>
						</a>
					<?php endforeach; ?>
					
					<?php if (count($featureRequests) > 3): ?>
						<div class="text-center pt-2">
							<a href="<?php echo base_url('/admin/featured-content'); ?>" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">
								+<?php echo count($featureRequests) - 3; ?> more feature requests →
							</a>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Trending Events & Top Destinations -->
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
		<!-- Trending Events by Ticket Sales -->
		<div class="card p-4">
			<div class="flex items-center justify-between mb-4">
				<h2 class="font-semibold text-sm md:text-base">Trending Events</h2>
				<div class="flex items-center gap-1 text-xs text-gray-400">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
					</svg>
					By ticket sales
				</div>
			</div>
			
			<?php
			$trendingEvents = [];
			try {
				$stmt = db()->query('
					SELECT e.id, e.title, e.location, e.event_date, 
						   COUNT(t.id) as ticket_count,
						   SUM(t.price) as total_revenue
					FROM events e
					LEFT JOIN tickets t ON e.id = t.event_id AND t.status = "confirmed"
					WHERE e.status = "active"
					GROUP BY e.id
					ORDER BY ticket_count DESC, total_revenue DESC
					LIMIT 5
				');
				$trendingEvents = $stmt->fetchAll();
			} catch (\PDOException $e) {
				// Handle database errors gracefully
			}
			?>
			
			<?php if (empty($trendingEvents)): ?>
				<div class="text-center py-8">
					<svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
					<p class="text-gray-400 text-sm">No trending events yet</p>
				</div>
			<?php else: ?>
				<div class="space-y-3">
					<?php foreach ($trendingEvents as $index => $event): ?>
						<div class="flex items-center justify-between p-3 rounded-lg bg-gray-800/30 hover:bg-gray-800/50 transition-colors">
							<div class="flex items-center gap-3">
								<div class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-600 text-white text-sm font-semibold">
									<?php echo $index + 1; ?>
								</div>
								<div class="min-w-0 flex-1">
									<h4 class="text-sm font-medium text-white truncate">
										<?php echo htmlspecialchars($event['title']); ?>
									</h4>
									<div class="flex items-center gap-2 text-xs text-gray-400">
										<span><?php echo htmlspecialchars($event['location']); ?></span>
										<span>•</span>
										<span><?php echo date('M j', strtotime($event['event_date'])); ?></span>
									</div>
								</div>
							</div>
							<div class="text-right">
								<div class="text-sm font-semibold text-green-400">
									<?php echo number_format($event['ticket_count'] ?? 0); ?> tickets
								</div>
								<div class="text-xs text-gray-400">
									KES <?php echo number_format($event['total_revenue'] ?? 0); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Top Travel Destinations -->
		<div class="card p-4">
			<div class="flex items-center justify-between mb-4">
				<h2 class="font-semibold text-sm md:text-base">Top Destinations</h2>
				<div class="flex items-center gap-1 text-xs text-gray-400">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
					</svg>
					By bookings
				</div>
			</div>
			
			<?php
			$topDestinations = [];
			try {
				$stmt = db()->query('
					SELECT d.id, d.name, d.location, d.country,
						   COUNT(tb.id) as booking_count,
						   SUM(tb.total_amount) as total_revenue
					FROM travel_destinations d
					LEFT JOIN travel_bookings tb ON d.id = tb.destination_id AND tb.status = "confirmed"
					WHERE d.status = "active"
					GROUP BY d.id
					ORDER BY booking_count DESC, total_revenue DESC
					LIMIT 5
				');
				$topDestinations = $stmt->fetchAll();
			} catch (\PDOException $e) {
				// Handle database errors gracefully
			}
			?>
			
			<?php if (empty($topDestinations)): ?>
				<div class="text-center py-8">
					<svg class="w-12 h-12 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
					</svg>
					<p class="text-gray-400 text-sm">No travel destinations yet</p>
				</div>
			<?php else: ?>
				<div class="space-y-3">
					<?php foreach ($topDestinations as $index => $destination): ?>
						<div class="flex items-center justify-between p-3 rounded-lg bg-gray-800/30 hover:bg-gray-800/50 transition-colors">
							<div class="flex items-center gap-3">
								<div class="flex items-center justify-center w-8 h-8 rounded-full bg-cyan-600 text-white text-sm font-semibold">
									<?php echo $index + 1; ?>
								</div>
								<div class="min-w-0 flex-1">
									<h4 class="text-sm font-medium text-white truncate">
										<?php echo htmlspecialchars($destination['name']); ?>
									</h4>
									<div class="flex items-center gap-2 text-xs text-gray-400">
										<span><?php echo htmlspecialchars($destination['location']); ?></span>
										<span>•</span>
										<span><?php echo htmlspecialchars($destination['country']); ?></span>
									</div>
								</div>
							</div>
							<div class="text-right">
								<div class="text-sm font-semibold text-cyan-400">
									<?php echo number_format($destination['booking_count'] ?? 0); ?> bookings
								</div>
								<div class="text-xs text-gray-400">
									KES <?php echo number_format($destination['total_revenue'] ?? 0); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Mobile Menu Overlay -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
	<div class="fixed inset-y-0 left-0 w-80 bg-gray-800 shadow-xl flex flex-col">
		<!-- Header with logo and close button -->
		<div class="p-4 border-b border-gray-700 flex-shrink-0">
			<div class="flex items-center justify-between mb-3">
				<div class="flex items-center gap-3">
					<img src="<?php echo base_url('/uploads/site/logo.png'); ?>" alt="Ticko Logo" class="w-8 h-8 rounded-lg">
					<h2 class="text-lg font-semibold text-white">Admin Panel</h2>
				</div>
				<button onclick="toggleMobileMenu()" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors" aria-label="Close menu">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>
			<!-- Quick search/filter -->
			<div class="relative">
				<input type="text" id="menuSearch" placeholder="Search menu..." class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
				<svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
				</svg>
			</div>
		</div>
		<nav class="flex-1 overflow-y-auto p-4 space-y-4" id="mobileNav">
			<!-- Dashboard -->
			<div class="space-y-1 menu-section" data-section="dashboard">
				<a href="<?php echo base_url('/admin'); ?>" class="flex items-center gap-3 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" data-menu-text="dashboard">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
					</svg>
					<span class="font-medium">Dashboard</span>
				</a>
			</div>

			<!-- Users & Partners -->
			<div class="space-y-1 menu-section" data-section="users-partners">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Users & Partners</h3>
				</div>
				<a href="<?php echo base_url('/admin/users'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="users">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
					</svg>
					<span>Users</span>
				</a>
				<a href="<?php echo base_url('/admin/organizers'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="organizers">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
					</svg>
					<span>Organizers</span>
				</a>
				<a href="<?php echo base_url('/admin/accounts/create'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="create accounts">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
					</svg>
					<span>Create Accounts</span>
				</a>
			</div>

			<!-- Events & Content -->
			<div class="space-y-1 menu-section" data-section="events-content">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Events & Content</h3>
				</div>
				<a href="<?php echo base_url('/admin/events'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="events">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
					<span>Events</span>
				</a>
				<a href="<?php echo base_url('/admin/categories'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="categories">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
					</svg>
					<span>Categories</span>
				</a>
				<a href="<?php echo base_url('/admin/banners'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="banners">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
					<span>Banners</span>
				</a>
				<a href="<?php echo base_url('/admin/pages'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
					</svg>
					<span>Pages</span>
				</a>
				<a href="<?php echo base_url('/admin/featured-content'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
					</svg>
					<span>Featured Content</span>
				</a>
				<a href="<?php echo base_url('/admin/notification-templates'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM5 7h6V2H5a2 2 0 00-2 2v3a2 2 0 002 2zM5 7v10a2 2 0 002 2h3M5 7l5 5m0 0l-5 5m5-5h5"></path>
					</svg>
					<span>Notification Templates</span>
				</a>
			</div>

			<!-- Partners & Branding -->
			<div class="space-y-1">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Partners & Branding</h3>
				</div>
				<a href="<?php echo base_url('/admin/partners'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
					</svg>
					<span>Partners</span>
				</a>
				<a href="<?php echo base_url('/admin/partner-logos'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
					<span>Partner Logos</span>
				</a>
			</div>

			<!-- Travel -->
			<div class="space-y-1 menu-section" data-section="travel">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Travel</h3>
				</div>
				<a href="<?php echo base_url('/admin/travel/agencies'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="travel agencies">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<span>Travel Agencies</span>
				</a>
				<a href="<?php echo base_url('/admin/travel/destinations'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="travel destinations">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
					</svg>
					<span>Travel Destinations</span>
				</a>
				<a href="<?php echo base_url('/admin/travel/scanners'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="travel scanners">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
					</svg>
					<span>Travel Scanners</span>
				</a>
				<a href="<?php echo base_url('/admin/travel/scanners/assignments'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="assign travel scanners">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
					</svg>
					<span>Assign Travel Scanners</span>
				</a>
				<a href="<?php echo base_url('/admin/travel-banners'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors" data-menu-text="travel banners">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
					</svg>
					<span>Travel Banners</span>
				</a>
			</div>

			<!-- Hotels -->
			<div class="space-y-1">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Hotels</h3>
				</div>
				<a href="<?php echo base_url('/admin/hotels'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
					</svg>
					<span>Hotel Applications</span>
				</a>
			</div>

			<!-- Communications -->
			<div class="space-y-1">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Communications</h3>
				</div>
				<a href="<?php echo base_url('/admin/email-templates'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
					</svg>
					<span>Email Templates</span>
				</a>
				<a href="<?php echo base_url('/admin/sms-templates'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
					</svg>
					<span>SMS Templates</span>
				</a>
				<a href="<?php echo base_url('/admin/communications'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
					</svg>
					<span>Communications</span>
				</a>
			</div>

			<!-- Operations -->
			<div class="space-y-1">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Operations</h3>
				</div>
				<a href="<?php echo base_url('/admin/points'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
					</svg>
					<span>Loyalty Points</span>
				</a>
				<a href="<?php echo base_url('/admin/scans'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
					</svg>
					<span>Scans</span>
				</a>
				<a href="<?php echo base_url('/admin/withdrawals'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
					</svg>
					<span>Withdrawals</span>
				</a>
				<a href="<?php echo base_url('/admin/finance'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
					</svg>
					<span>Finance</span>
				</a>
				<a href="<?php echo base_url('/admin/scanners'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
					</svg>
					<span>Admin Scanners</span>
				</a>
			</div>

			<!-- Account -->
			<div class="space-y-1 pt-4 border-t border-gray-700">
				<div class="px-3 py-1">
					<h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</h3>
				</div>
				<a href="<?php echo base_url('/admin/settings'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
					</svg>
					<span>Settings</span>
				</a>
				<a href="<?php echo base_url('/admin/profile'); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
					</svg>
					<span>My Profile</span>
				</a>
				<a href="<?php echo base_url('/admin/logout'); ?>" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-gray-700 rounded-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
					</svg>
					<span>Logout</span>
				</a>
			</div>
		</nav>
	</div>
</div>

<style>
/* Enhanced mobile navigation styles */
.touch-target {
	min-height: 44px;
	min-width: 44px;
}

#mobileMenuOverlay {
	backdrop-filter: blur(4px);
}

#menuSearch:focus {
	transform: scale(1.02);
	transition: transform 0.2s ease;
}

[data-menu-text]:focus {
	outline: 2px solid #ef4444;
	outline-offset: 2px;
}

/* Smooth transitions for menu items */
[data-menu-text] {
	transition: all 0.2s ease;
}

[data-menu-text]:hover {
	transform: translateX(4px);
}

/* Keyboard shortcut styling */
kbd {
	font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
	font-size: 0.75rem;
	font-weight: 500;
}

/* Search input enhancements */
#menuSearch::placeholder {
	transition: opacity 0.2s ease;
}

#menuSearch:focus::placeholder {
	opacity: 0.5;
}

/* Mobile swipe indicator */
@media (max-width: 767px) {
	#mobileMenuOverlay::after {
		content: '← Swipe left to close';
		position: absolute;
		top: 50%;
		right: 20px;
		transform: translateY(-50%);
		background: rgba(0, 0, 0, 0.8);
		color: white;
		padding: 8px 12px;
		border-radius: 8px;
		font-size: 12px;
		opacity: 0.7;
		pointer-events: none;
		animation: fadeInOut 3s ease-in-out;
	}
}

@keyframes fadeInOut {
	0%, 100% { opacity: 0; }
	20%, 80% { opacity: 0.7; }
}

/* Back navigation enhancements */
#mobileBackButton {
	transition: all 0.3s ease;
}

#mobileBackButton:hover {
	transform: translateX(-2px);
}

#mobileBackButton:active {
	transform: scale(0.95);
}

/* Mobile navigation header */
.mobile-back-nav {
	background: linear-gradient(135deg, rgba(31, 41, 55, 0.8), rgba(17, 24, 39, 0.9));
	backdrop-filter: blur(10px);
	border: 1px solid rgba(75, 85, 99, 0.3);
	border-radius: 12px;
	padding: 8px 12px;
	margin-bottom: 16px;
}

/* Smooth transitions for page title */
#mobilePageTitle {
	transition: all 0.2s ease;
}

/* Enhanced keyboard shortcut styling */
kbd {
	background: linear-gradient(145deg, #374151, #1f2937);
	border: 1px solid #4b5563;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}
</style>

<script>
function toggleMobileMenu() {
	const overlay = document.getElementById('mobileMenuOverlay');
	const searchInput = document.getElementById('menuSearch');
	
	if (overlay.classList.contains('hidden')) {
		overlay.classList.remove('hidden');
		document.body.style.overflow = 'hidden';
		
		// Focus search input when menu opens
		setTimeout(() => {
			if (searchInput) {
				searchInput.focus();
			}
		}, 100);
	} else {
		overlay.classList.add('hidden');
		document.body.style.overflow = '';
		
		// Clear search when closing
		if (searchInput) {
			searchInput.value = '';
			filterMenuItems('');
		}
	}
}

// Enhanced search functionality
function filterMenuItems(searchTerm) {
	const menuItems = document.querySelectorAll('[data-menu-text]');
	const sections = document.querySelectorAll('.menu-section');
	
	const term = searchTerm.toLowerCase().trim();
	
	menuItems.forEach(item => {
		const text = item.getAttribute('data-menu-text').toLowerCase();
		const parentSection = item.closest('.menu-section');
		
		if (term === '' || text.includes(term)) {
			item.style.display = 'flex';
			if (parentSection) {
				parentSection.style.display = 'block';
			}
		} else {
			item.style.display = 'none';
			// Hide section if no visible items
			if (parentSection) {
				const visibleItems = parentSection.querySelectorAll('[data-menu-text]:not([style*="display: none"])');
				if (visibleItems.length === 0) {
					parentSection.style.display = 'none';
				}
			}
		}
	});
	
	// Show/hide section headers based on search
	const sectionHeaders = document.querySelectorAll('h3');
	sectionHeaders.forEach(header => {
		const section = header.closest('.menu-section');
		if (section && section.style.display === 'none') {
			header.style.display = 'none';
		} else {
			header.style.display = 'block';
		}
	});
}

// Keyboard navigation
function handleKeyboardNavigation(e) {
	const menuItems = document.querySelectorAll('[data-menu-text]:not([style*="display: none"])');
	const currentIndex = Array.from(menuItems).indexOf(document.activeElement);
	
	switch(e.key) {
		case 'ArrowDown':
			e.preventDefault();
			const nextIndex = (currentIndex + 1) % menuItems.length;
			menuItems[nextIndex]?.focus();
			break;
		case 'ArrowUp':
			e.preventDefault();
			const prevIndex = currentIndex <= 0 ? menuItems.length - 1 : currentIndex - 1;
			menuItems[prevIndex]?.focus();
			break;
		case 'Enter':
		case ' ':
			if (e.target.tagName === 'A') {
				e.target.click();
			}
			break;
		case 'Escape':
			toggleMobileMenu();
			break;
	}
}

// Initialize enhanced navigation
document.addEventListener('DOMContentLoaded', function() {
	const overlay = document.getElementById('mobileMenuOverlay');
	const searchInput = document.getElementById('menuSearch');
	const menuItems = document.querySelectorAll('[data-menu-text]');
	
	// Add data attributes for search
	menuItems.forEach(item => {
		const text = item.querySelector('span')?.textContent?.toLowerCase() || '';
		item.setAttribute('data-menu-text', text);
		item.setAttribute('tabindex', '0');
		item.setAttribute('role', 'menuitem');
		
		// Add click tracking for back navigation
		item.addEventListener('click', function() {
			const pageTitle = this.querySelector('span')?.textContent || 'Admin';
			sessionStorage.setItem('mobileAdminLastPage', pageTitle);
			sessionStorage.setItem('mobileAdminLastUrl', window.location.href);
		});
	});
	
	// Search functionality
	if (searchInput) {
		searchInput.addEventListener('input', function(e) {
			filterMenuItems(e.target.value);
		});
		
		searchInput.addEventListener('keydown', function(e) {
			if (e.key === 'ArrowDown') {
				e.preventDefault();
				const firstMenuItem = document.querySelector('[data-menu-text]:not([style*="display: none"])');
				firstMenuItem?.focus();
			}
		});
	}
	
	// Keyboard navigation for menu items
	menuItems.forEach(item => {
		item.addEventListener('keydown', handleKeyboardNavigation);
	});
	
	// Close menu when clicking overlay
	overlay.addEventListener('click', function(e) {
		if (e.target === this) {
			toggleMobileMenu();
		}
	});
	
	// Global keyboard shortcuts
	document.addEventListener('keydown', function(e) {
		// Alt + M to toggle menu
		if (e.altKey && e.key === 'm') {
			e.preventDefault();
			toggleMobileMenu();
		}
		
		// Escape to close menu
		if (e.key === 'Escape') {
			if (!overlay.classList.contains('hidden')) {
				toggleMobileMenu();
			}
		}
	});
	
	// Touch/swipe gestures for mobile
	let startX = 0;
	let startY = 0;
	
	overlay.addEventListener('touchstart', function(e) {
		startX = e.touches[0].clientX;
		startY = e.touches[0].clientY;
	});
	
	overlay.addEventListener('touchend', function(e) {
		const endX = e.changedTouches[0].clientX;
		const endY = e.changedTouches[0].clientY;
		const diffX = startX - endX;
		const diffY = startY - endY;
		
		// Swipe left to close menu
		if (Math.abs(diffX) > Math.abs(diffY) && diffX > 50) {
			toggleMobileMenu();
		}
	});
	
	// Auto-close menu when clicking outside
	document.addEventListener('click', function(e) {
		if (!overlay.classList.contains('hidden') && 
			!overlay.contains(e.target) && 
			!document.getElementById('adminMobileToggleFallback')?.contains(e.target)) {
			toggleMobileMenu();
		}
	});
});

// Accessibility improvements
document.addEventListener('DOMContentLoaded', function() {
	// Add ARIA attributes
	const overlay = document.getElementById('mobileMenuOverlay');
	const nav = document.getElementById('mobileNav');
	
	if (overlay) {
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.setAttribute('aria-label', 'Admin navigation menu');
	}
	
	if (nav) {
		nav.setAttribute('role', 'menu');
		nav.setAttribute('aria-label', 'Admin menu items');
	}
	
	// Announce menu state changes
	const observer = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
				const isHidden = overlay.classList.contains('hidden');
				// You could add screen reader announcements here
				console.log('Menu state changed:', isHidden ? 'closed' : 'open');
			}
		});
	});
	
	observer.observe(overlay, { attributes: true, attributeFilter: ['class'] });
});

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
			backButton.classList.remove('hidden');
			const lastPage = sessionStorage.getItem('mobileAdminLastPage') || 'Dashboard';
			if (pageTitle) {
				pageTitle.textContent = getCurrentPageTitle();
			}
		} else {
			// Hide back button and show menu title
			backButton.classList.add('hidden');
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

// Enhanced mobile menu links to track navigation
document.addEventListener('DOMContentLoaded', function() {
	const menuLinks = document.querySelectorAll('#mobileNav a[href]');
	
	menuLinks.forEach(link => {
		link.addEventListener('click', function() {
			const pageTitle = this.querySelector('span')?.textContent || 'Admin';
			sessionStorage.setItem('mobileAdminLastPage', pageTitle);
			sessionStorage.setItem('mobileAdminLastUrl', this.href);
		});
	});
});
</script>



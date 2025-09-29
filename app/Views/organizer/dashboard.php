<div class="max-w-7xl mx-auto px-4 py-6">
	<!-- Header Section -->
	<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
		<div>
			<h1 class="text-3xl font-bold text-white mb-2">Organizer Dashboard</h1>
			<p class="text-gray-400">Welcome back! Here's an overview of your event performance.</p>
		</div>
		<div class="mt-4 md:mt-0">
			<a href="<?php echo base_url('/organizer/events/create'); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
				</svg>
				Create New Event
			</a>
		</div>
	</div>

	<?php 
	$followers = 0; 
	$totalTicketsSold = 0;
	$totalTicketsRedeemed = 0;
	$upcomingEvents = 0;
	$completedEvents = 0;
	
	try { 
		$r = db()->prepare('SELECT COUNT(*) AS c FROM organizer_followers WHERE organizer_id = ?'); 
		$r->execute([$_SESSION['organizer_id']]); 
		$followers = (int)($r->fetch()['c'] ?? 0);
		
		// Get additional metrics
		$upcoming = db()->prepare('SELECT COUNT(*) AS c FROM events WHERE organizer_id = ? AND event_date >= CURDATE()');
		$upcoming->execute([$_SESSION['organizer_id']]);
		$upcomingEvents = (int)($upcoming->fetch()['c'] ?? 0);
		
		$completed = db()->prepare('SELECT COUNT(*) AS c FROM events WHERE organizer_id = ? AND event_date < CURDATE()');
		$completed->execute([$_SESSION['organizer_id']]);
		$completedEvents = (int)($completed->fetch()['c'] ?? 0);
		
		$totalTicketsSold = (int)($tix['sold'] ?? 0);
		$totalTicketsRedeemed = (int)($tix['redeemed'] ?? 0);
	} catch (\Throwable $e) {} 
	?>

	<!-- Key Metrics Grid -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
		<!-- Total Events Card -->
		<div class="bg-gradient-to-br from-blue-600/20 to-blue-800/20 border border-blue-500/30 rounded-xl p-6 hover:border-blue-400/50 transition-all duration-300">
			<div class="flex items-center justify-between">
				<div>
					<div class="flex items-center gap-3 mb-2">
						<div class="p-2 bg-blue-600/20 rounded-lg">
							<svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
							</svg>
						</div>
						<div class="text-sm font-medium text-blue-300">Total Events</div>
					</div>
					<div class="text-3xl font-bold text-white"><?php echo (int)($eventsCount ?? 0); ?></div>
					<div class="text-xs text-blue-200 mt-1">
						<span class="text-green-400"><?php echo $upcomingEvents; ?></span> upcoming • 
						<span class="text-gray-400"><?php echo $completedEvents; ?></span> completed
					</div>
				</div>
			</div>
		</div>

		<!-- Total Orders Card -->
		<div class="bg-gradient-to-br from-green-600/20 to-green-800/20 border border-green-500/30 rounded-xl p-6 hover:border-green-400/50 transition-all duration-300">
			<div class="flex items-center justify-between">
				<div>
					<div class="flex items-center gap-3 mb-2">
						<div class="p-2 bg-green-600/20 rounded-lg">
							<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
							</svg>
						</div>
						<div class="text-sm font-medium text-green-300">Total Orders</div>
					</div>
					<div class="text-3xl font-bold text-white"><?php echo (int)($ordersCount ?? 0); ?></div>
					<div class="text-xs text-green-200 mt-1">
						<span class="text-green-400"><?php echo $totalTicketsSold; ?></span> tickets sold
					</div>
				</div>
			</div>
		</div>

		<!-- Revenue Card -->
		<div class="bg-gradient-to-br from-purple-600/20 to-purple-800/20 border border-purple-500/30 rounded-xl p-6 hover:border-purple-400/50 transition-all duration-300">
			<div class="flex items-center justify-between">
				<div>
					<div class="flex items-center gap-3 mb-2">
						<div class="p-2 bg-purple-600/20 rounded-lg">
							<svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
							</svg>
						</div>
						<div class="text-sm font-medium text-purple-300">Total Revenue</div>
					</div>
					<div class="text-3xl font-bold text-white">KES <?php echo number_format((float)($revenue ?? 0), 0); ?></div>
					<div class="text-xs text-purple-200 mt-1">
						From <?php echo (int)($ordersCount ?? 0); ?> orders
					</div>
				</div>
			</div>
		</div>

		<!-- Followers Card -->
		<div class="bg-gradient-to-br from-orange-600/20 to-orange-800/20 border border-orange-500/30 rounded-xl p-6 hover:border-orange-400/50 transition-all duration-300">
			<div class="flex items-center justify-between">
				<div>
					<div class="flex items-center gap-3 mb-2">
						<div class="p-2 bg-orange-600/20 rounded-lg">
							<svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
							</svg>
						</div>
						<div class="text-sm font-medium text-orange-300">Followers</div>
					</div>
					<div class="text-3xl font-bold text-white"><?php echo $followers; ?></div>
					<div class="text-xs text-orange-200 mt-1">
						Event enthusiasts
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Additional Metrics Row -->
	<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
		<!-- Tickets Sold -->
		<div class="bg-gradient-to-br from-cyan-600/20 to-cyan-800/20 border border-cyan-500/30 rounded-xl p-6">
			<div class="flex items-center gap-4">
				<div class="p-3 bg-cyan-600/20 rounded-xl">
					<svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
					</svg>
				</div>
				<div>
					<div class="text-sm font-medium text-cyan-300">Tickets Sold</div>
					<div class="text-2xl font-bold text-white"><?php echo $totalTicketsSold; ?></div>
				</div>
			</div>
		</div>

		<!-- Tickets Redeemed -->
		<div class="bg-gradient-to-br from-emerald-600/20 to-emerald-800/20 border border-emerald-500/30 rounded-xl p-6">
			<div class="flex items-center gap-4">
				<div class="p-3 bg-emerald-600/20 rounded-xl">
					<svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
				<div>
					<div class="text-sm font-medium text-emerald-300">Tickets Redeemed</div>
					<div class="text-2xl font-bold text-white"><?php echo $totalTicketsRedeemed; ?></div>
				</div>
			</div>
		</div>

		<!-- Average Order Value -->
		<div class="bg-gradient-to-br from-amber-600/20 to-amber-800/20 border border-amber-500/30 rounded-xl p-6">
			<div class="flex items-center gap-4">
				<div class="p-3 bg-amber-600/20 rounded-xl">
					<svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
					</svg>
				</div>
				<div>
					<div class="text-sm font-medium text-amber-300">Avg Order Value</div>
					<div class="text-2xl font-bold text-white">
						KES <?php echo $ordersCount > 0 ? number_format($revenue / $ordersCount, 0) : '0'; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Main Content Grid -->
	<div class="grid lg:grid-cols-3 gap-8">
		<!-- Recent Orders -->
		<div class="lg:col-span-2">
			<div class="bg-gray-900/50 border border-gray-700 rounded-xl p-6">
				<div class="flex items-center justify-between mb-6">
					<div class="flex items-center gap-3">
						<div class="p-2 bg-blue-600/20 rounded-lg">
							<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
							</svg>
						</div>
						<h2 class="text-xl font-semibold text-white">Recent Orders</h2>
					</div>
					<a href="<?php echo base_url('/organizer/reports'); ?>" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 font-medium transition-colors">
						<span>View All Reports</span>
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
						</svg>
					</a>
				</div>
				
				<?php if (!empty($recentOrders)): ?>
					<div class="space-y-3">
						<?php foreach (array_slice($recentOrders, 0, 5) as $ro): ?>
						<div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors">
							<div class="flex items-center gap-4">
								<div class="p-2 bg-green-600/20 rounded-lg">
									<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
									</svg>
								</div>
								<div>
									<div class="font-semibold text-white">#<?php echo (int)$ro['id']; ?></div>
									<div class="text-sm text-gray-400"><?php echo htmlspecialchars($ro['title']); ?></div>
								</div>
							</div>
							<div class="text-right">
								<div class="font-bold text-white">KES <?php echo number_format((float)$ro['total_amount'], 0); ?></div>
								<div class="text-sm text-gray-400"><?php echo date('M j, Y', strtotime($ro['created_at'])); ?></div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="text-center py-12">
						<div class="p-4 bg-gray-800/50 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
							<svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
							</svg>
						</div>
						<h3 class="text-lg font-semibold text-white mb-2">No Orders Yet</h3>
						<p class="text-gray-400 mb-4">Your recent orders will appear here once customers start buying tickets.</p>
						<a href="<?php echo base_url('/organizer/events/create'); ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
							</svg>
							Create Your First Event
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Quick Actions & Stats -->
		<div class="space-y-6">
			<!-- Quick Actions -->
			<div class="bg-gray-900/50 border border-gray-700 rounded-xl p-6">
				<div class="flex items-center gap-3 mb-6">
					<div class="p-2 bg-purple-600/20 rounded-lg">
						<svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
						</svg>
					</div>
					<h2 class="text-xl font-semibold text-white">Quick Actions</h2>
				</div>
				<div class="space-y-3">
					<a href="<?php echo base_url('/organizer/events/create'); ?>" class="flex items-center gap-3 p-4 bg-gradient-to-r from-red-600/20 to-red-700/20 border border-red-500/30 rounded-lg hover:border-red-400/50 transition-all duration-200 group">
						<div class="p-2 bg-red-600/20 rounded-lg group-hover:bg-red-600/30 transition-colors">
							<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
							</svg>
						</div>
						<div>
							<div class="font-semibold text-white">Create Event</div>
							<div class="text-sm text-gray-400">Set up a new event</div>
						</div>
					</a>
					
					<a href="<?php echo base_url('/organizer/events'); ?>" class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-600/20 to-blue-700/20 border border-blue-500/30 rounded-lg hover:border-blue-400/50 transition-all duration-200 group">
						<div class="p-2 bg-blue-600/20 rounded-lg group-hover:bg-blue-600/30 transition-colors">
							<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
							</svg>
						</div>
						<div>
							<div class="font-semibold text-white">Manage Events</div>
							<div class="text-sm text-gray-400">View and edit events</div>
						</div>
					</a>
					
					<a href="<?php echo base_url('/organizer/withdrawals'); ?>" class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-600/20 to-green-700/20 border border-green-500/30 rounded-lg hover:border-green-400/50 transition-all duration-200 group">
						<div class="p-2 bg-green-600/20 rounded-lg group-hover:bg-green-600/30 transition-colors">
							<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
							</svg>
						</div>
						<div>
							<div class="font-semibold text-white">Request Withdrawal</div>
							<div class="text-sm text-gray-400">Withdraw your earnings</div>
						</div>
					</a>
				</div>
			</div>

			<!-- Performance Summary -->
			<div class="bg-gray-900/50 border border-gray-700 rounded-xl p-6">
				<div class="flex items-center gap-3 mb-6">
					<div class="p-2 bg-emerald-600/20 rounded-lg">
						<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
						</svg>
					</div>
					<h2 class="text-xl font-semibold text-white">Performance</h2>
				</div>
				<div class="space-y-4">
					<div class="flex justify-between items-center">
						<span class="text-gray-400">Conversion Rate</span>
						<span class="font-semibold text-white">
							<?php echo $totalTicketsSold > 0 ? round(($totalTicketsRedeemed / $totalTicketsSold) * 100, 1) : '0'; ?>%
						</span>
					</div>
					<div class="flex justify-between items-center">
						<span class="text-gray-400">Active Events</span>
						<span class="font-semibold text-white"><?php echo $upcomingEvents; ?></span>
					</div>
					<div class="flex justify-between items-center">
						<span class="text-gray-400">Completed Events</span>
						<span class="font-semibold text-white"><?php echo $completedEvents; ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



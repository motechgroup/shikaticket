<?php /** @var array $orders */ /** @var array $byCurrency */ /** @var array $pendingWithdrawals */ ?>
<div class="max-w-6xl mx-auto px-4 py-6 md:py-10">
	<div class="flex items-center justify-between mb-4 md:mb-6">
		<h1 class="text-xl md:text-2xl font-semibold">Admin Dashboard</h1>
		<div class="md:hidden">
			<div class="flex items-center gap-2 text-xs text-gray-400">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
				<span>Tap menu icon above</span>
			</div>
		</div>
	</div>
	
	<!-- Mobile-friendly stats grid with icons -->
	<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
		<div class="card p-4 md:p-6 text-center md:text-left group hover:border-green-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition-colors">
					<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Paid Orders</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-green-400"><?php echo (int)($orders['total_orders'] ?? 0); ?></div>
		</div>
		<div class="card p-4 md:p-6 text-center md:text-left group hover:border-blue-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition-colors">
					<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Gross Revenue</div>
			</div>
			<div class="text-xl md:text-3xl font-bold text-blue-400">KES <?php echo number_format((float)($orders['gross'] ?? 0), 2); ?></div>
		</div>
		<div class="card p-4 md:p-6 group hover:border-purple-500/50 transition-all duration-300">
			<div class="flex items-center justify-center md:justify-start gap-3 mb-2">
				<div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
					<svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
					</svg>
				</div>
				<div class="text-xs md:text-sm text-gray-400 font-medium">Currencies</div>
			</div>
			<div class="text-sm md:text-base space-y-1 text-purple-400">
				<?php foreach ($byCurrency as $row): ?>
				<div class="font-semibold"><?php echo htmlspecialchars($row['currency']); ?>: <?php echo number_format((float)$row['gross'], 2); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="card p-4 md:p-6 text-center md:text-left group hover:border-orange-500/50 transition-all duration-300">
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
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">
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
		
		<div class="card p-4 md:p-6 text-center md:text-left group hover:border-blue-500/50 transition-all duration-300">
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
		<div class="card p-4 md:p-6 text-center md:text-left group hover:border-green-500/50 transition-all duration-300">
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
		<div class="card p-4 md:p-6 text-center md:text-left group hover:border-purple-500/50 transition-all duration-300">
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
		<div class="card p-4 md:p-6 text-center md:text-left group hover:border-orange-500/50 transition-all duration-300">
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
		<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
			<a href="<?php echo base_url('/admin/users'); ?>" class="card p-4 md:p-6 hover:border-blue-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-blue-500/20 rounded-lg group-hover:bg-blue-500/30 transition-colors">
						<svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Manage Users</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/events'); ?>" class="card p-4 md:p-6 hover:border-purple-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-purple-500/20 rounded-lg group-hover:bg-purple-500/30 transition-colors">
						<svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Manage Events</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/communications'); ?>" class="card p-4 md:p-6 hover:border-green-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
				<div class="flex items-center gap-3">
					<div class="p-2 bg-green-500/20 rounded-lg group-hover:bg-green-500/30 transition-colors">
						<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
						</svg>
					</div>
					<span class="text-sm md:text-base font-medium">Send Messages</span>
				</div>
			</a>
			<a href="<?php echo base_url('/admin/settings'); ?>" class="card p-4 md:p-6 hover:border-gray-500 touch-target flex items-center justify-center md:justify-start transition-all duration-200 group">
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
		</div>
	</div>

	<!-- System Overview -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
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



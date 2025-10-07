<?php /** @var array $templates */ ?>
<div class="max-w-7xl mx-auto px-4 py-6 md:py-10">
	<?php $pageTitle = 'Email Templates'; include __DIR__ . '/../components/mobile_nav_simple.php'; ?>
	
	<div class="flex items-center justify-between mb-6">
		<div>
			<h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Email Templates Management</h1>
			<p class="text-gray-400">Manage and customize all email templates with professional branding</p>
		</div>
		<a href="<?php echo base_url('/admin'); ?>" class="btn-secondary">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
			</svg>
			Back to Admin
		</a>
	</div>

	<form method="POST" action="<?php echo base_url('/admin/email-templates'); ?>" class="space-y-8">
		<?php echo csrf_field(); ?>
		
		<!-- User Registration & Welcome Templates -->
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
			<h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
				<svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
				</svg>
				User Registration & Welcome
			</h2>
			<div class="grid gap-6">
				<div>
					<label class="block font-semibold mb-2 text-gray-300">User Welcome Email</label>
					<textarea name="user_welcome" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="User welcome email template..."><?php echo htmlspecialchars($templates['user_welcome'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{site_name}}, {{login_url}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Email Verification</label>
					<textarea name="email_verification" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Email verification template..."><?php echo htmlspecialchars($templates['email_verification'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{email}}, {{verification_url}}, {{site_name}}</div>
				</div>
			</div>
		</div>

		<!-- Organizer Templates -->
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
			<h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
				<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
				</svg>
				Organizer Management
			</h2>
			<div class="grid gap-6">
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Organizer Approval Notification</label>
					<textarea name="organizer_approved" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" placeholder="Organizer approval template..."><?php echo htmlspecialchars($templates['organizer_approved'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{site_name}}, {{login_url}}</div>
				</div>
			</div>
		</div>

		<!-- Travel Agency Templates -->
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
			<h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
				<svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				Travel Agency Management
			</h2>
			<div class="grid gap-6">
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Travel Agency Welcome Email</label>
					<textarea name="travel_agency_welcome" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Travel agency welcome template..."><?php echo htmlspecialchars($templates['travel_agency_welcome'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{company_name}}, {{email}}, {{site_name}}, {{login_url}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Travel Agency Approval Notification</label>
					<textarea name="travel_agency_approved" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Travel agency approval template..."><?php echo htmlspecialchars($templates['travel_agency_approved'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{company_name}}, {{email}}, {{site_name}}, {{login_url}}</div>
				</div>
			</div>
		</div>

		<!-- Password Reset Templates -->
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
			<h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
				<svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
				</svg>
				Password Reset Templates
			</h2>
			<div class="grid gap-6">
				<div>
					<label class="block font-semibold mb-2 text-gray-300">User Password Reset</label>
					<textarea name="password_reset_user" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500" placeholder="User password reset template..."><?php echo htmlspecialchars($templates['password_reset_user'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{reset_url}}, {{site_name}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Travel Agency Password Reset</label>
					<textarea name="password_reset_travel" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500" placeholder="Travel agency password reset template..."><?php echo htmlspecialchars($templates['password_reset_travel'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{reset_url}}, {{site_name}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Organizer Password Reset</label>
					<textarea name="password_reset_organizer" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500" placeholder="Organizer password reset template..."><?php echo htmlspecialchars($templates['password_reset_organizer'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{reset_url}}, {{site_name}}</div>
				</div>
			</div>
		</div>

		<!-- Event & Ticket Templates -->
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
			<h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
				<svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
				</svg>
				Event & Ticket Management
			</h2>
			<div class="grid gap-6">
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Ticket Confirmation</label>
					<textarea name="ticket_confirmation" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Ticket confirmation template..."><?php echo htmlspecialchars($templates['ticket_confirmation'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{event_title}}, {{event_date}}, {{venue}}, {{ticket_code}}, {{qr_code_url}}, {{download_url}}, {{site_name}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Order Receipt</label>
					<textarea name="order_receipt" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Order receipt template..."><?php echo htmlspecialchars($templates['order_receipt'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{order_id}}, {{order_date}}, {{total_amount}}, {{currency}}, {{payment_method}}, {{site_name}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Event Reminder</label>
					<textarea name="event_reminder" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Event reminder template..."><?php echo htmlspecialchars($templates['event_reminder'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{event_title}}, {{event_date}}, {{event_time}}, {{venue}}, {{event_url}}, {{site_name}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Event Cancellation</label>
					<textarea name="event_cancellation" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" placeholder="Event cancellation template..."><?php echo htmlspecialchars($templates['event_cancellation'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{event_title}}, {{event_date}}, {{venue}}, {{refund_amount}}, {{currency}}, {{refund_status}}, {{site_name}}</div>
				</div>
			</div>
		</div>

		<!-- Travel Booking Templates -->
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
			<h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
				<svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
				</svg>
				Travel Booking Templates
			</h2>
			<div class="grid gap-6">
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Destination Booking Confirmation</label>
					<textarea name="destination_booking_confirmation" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Travel booking confirmation template..."><?php echo htmlspecialchars($templates['destination_booking_confirmation'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{destination_title}}, {{destination}}, {{departure_date}}, {{return_date}}, {{booking_reference}}, {{ticket_code}}, {{agency_name}}, {{agency_phone}}, {{agency_email}}, {{total_amount}}, {{currency}}, {{ticket_download_url}}, {{site_name}}</div>
				</div>
			</div>
		</div>

		<!-- Payment & Withdrawal Templates -->
		<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
			<h2 class="text-xl font-semibold text-white mb-4 flex items-center gap-2">
				<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
				</svg>
				Payment & Withdrawal Templates
			</h2>
			<div class="grid gap-6">
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Payment Reminder</label>
					<textarea name="payment_reminder" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" placeholder="Payment reminder template..."><?php echo htmlspecialchars($templates['payment_reminder'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{order_id}}, {{amount}}, {{currency}}, {{due_date}}, {{payment_url}}, {{site_name}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Withdrawal Request</label>
					<textarea name="withdrawal_request" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" placeholder="Withdrawal request template..."><?php echo htmlspecialchars($templates['withdrawal_request'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{amount}}, {{currency}}, {{request_date}}, {{account_type}}, {{site_name}}</div>
				</div>
				<div>
					<label class="block font-semibold mb-2 text-gray-300">Withdrawal Status Update</label>
					<textarea name="withdrawal_status_update" rows="12" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" placeholder="Withdrawal status update template..."><?php echo htmlspecialchars($templates['withdrawal_status_update'] ?? ''); ?></textarea>
					<div class="text-xs text-gray-400 mt-2">Variables: {{name}}, {{amount}}, {{currency}}, {{status}}, {{admin_notes}}, {{update_date}}, {{account_type}}, {{site_name}}</div>
				</div>
			</div>
		</div>

		<!-- Save Button -->
		<div class="flex justify-center pt-6">
			<button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition-colors duration-200 flex items-center gap-2">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
				</svg>
				Save All Email Templates
			</button>
		</div>
	</form>

	<!-- Template Variables Reference -->
	<div class="mt-12 bg-gray-800 border border-gray-700 rounded-lg p-6">
		<h3 class="text-lg font-semibold text-white mb-4">📋 Template Variables Reference</h3>
		<div class="grid md:grid-cols-2 gap-4 text-sm">
			<div>
				<h4 class="font-medium text-gray-300 mb-2">User Variables:</h4>
				<ul class="text-gray-400 space-y-1">
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{name}}</code> - User's full name</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{email}}</code> - User's email address</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{site_name}}</code> - Site name (ShikaTicket)</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{login_url}}</code> - Login page URL</li>
				</ul>
			</div>
			<div>
				<h4 class="font-medium text-gray-300 mb-2">Event Variables:</h4>
				<ul class="text-gray-400 space-y-1">
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{event_title}}</code> - Event title</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{event_date}}</code> - Event date</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{venue}}</code> - Event venue</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{ticket_code}}</code> - Ticket code</li>
				</ul>
			</div>
			<div>
				<h4 class="font-medium text-gray-300 mb-2">Travel Variables:</h4>
				<ul class="text-gray-400 space-y-1">
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{destination_title}}</code> - Travel destination title</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{booking_reference}}</code> - Booking reference</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{agency_name}}</code> - Travel agency name</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{agency_phone}}</code> - Agency phone</li>
				</ul>
			</div>
			<div>
				<h4 class="font-medium text-gray-300 mb-2">Payment Variables:</h4>
				<ul class="text-gray-400 space-y-1">
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{amount}}</code> - Payment amount</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{currency}}</code> - Currency (KES)</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{order_id}}</code> - Order ID</li>
					<li><code class="bg-gray-700 px-2 py-1 rounded">{{status}}</code> - Status (approved/rejected/paid)</li>
				</ul>
			</div>
		</div>
	</div>
</div>
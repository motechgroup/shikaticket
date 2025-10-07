<?php
session_start();

// Basic autoloader
spl_autoload_register(function ($class) {
	$baseDir = __DIR__ . '/../app/';
	// Map namespaced classes to app/ directory
	if (strpos($class, 'App\\') === 0) {
		$class = substr($class, 4); // strip leading 'App\'
	}
	$class = str_replace('\\', '/', $class);
	$paths = [
		$baseDir . $class . '.php',
		$baseDir . $class,
	];
	foreach ($paths as $path) {
		if (file_exists($path)) {
			require_once $path;
			return;
		}
	}
});

require_once __DIR__ . '/../config/config.php';

use App\Core\Router;

// Define routes
$router = new Router();

// Home
$router->get('/', 'HomeController@index');
// Public pages
$router->get('/events', 'EventController@publicIndex');
$router->get('/events/show', 'EventController@show');
$router->get('/page', 'PagesController@show');
$router->get('/search', 'PagesController@search');
$router->get('/partners', 'PartnersController@index');
$router->post('/partners', 'PartnersController@store');
$router->get('/hotels', 'PagesController@hotelsComingSoon');
$router->post('/hotels/apply', 'PagesController@hotelApplication');
$router->get('/travel', 'PagesController@travelIndex');
$router->get('/travel/destination', 'PagesController@travelDestinationShow');
$router->get('/travel/agency', 'PagesController@travelAgencyShow');
$router->post('/travel/booking', 'TravelBookingController@create');
$router->get('/travel/checkout', 'TravelBookingController@checkout');
$router->post('/travel/payment', 'TravelBookingController@payment');
$router->get('/travel/payment/status', 'TravelBookingController@checkPaymentStatus');
$router->post('/travel/payment/reconcile', 'TravelBookingController@reconcilePayment');
$router->get('/tickets/view', 'TicketsController@view');
$router->get('/tickets/download', 'TicketsController@download');
$router->get('/travel-tickets/download', 'TicketsController@downloadTravel');
$router->get('/travel-tickets/qr', 'TicketsController@qrTravel');
$router->get('/travel-tickets/view', 'TravelBookingController@viewTicket');
$router->get('/organizers/show', 'OrganizersController@show');
$router->post('/organizers/follow', 'OrganizersController@follow');
$router->post('/organizers/unfollow', 'OrganizersController@unfollow');
$router->get('/help', 'DocsController@index');
$router->get('/help/show', 'DocsController@show');
$router->post('/travel/agency/follow', 'PagesController@travelAgencyFollow');
$router->post('/travel/agency/unfollow', 'PagesController@travelAgencyUnfollow');
$router->post('/travel/agency/rate', 'PagesController@travelAgencyRate');
$router->get('/sitemap.xml', 'SitemapController@index');

// User Auth (phone-based login)
$router->get('/login', 'AuthController@loginUserForm');
$router->post('/login', 'AuthController@loginUser');
$router->get('/register', 'AuthController@registerUserForm');
$router->post('/register', 'AuthController@registerUser');
$router->get('/login-otp', 'AuthController@loginUserOtpForm');
$router->post('/login-otp', 'AuthController@loginUserOtpRequest');
$router->get('/login-otp/verify', 'AuthController@loginUserOtpVerifyForm');
$router->post('/login-otp/verify', 'AuthController@loginUserOtpVerify');
$router->post('/login-otp/resend', 'AuthController@loginUserOtpResend');
$router->get('/password/forgot', 'AuthController@forgotPasswordForm');
$router->post('/password/forgot', 'AuthController@sendPasswordReset');
$router->get('/password/reset', 'AuthController@resetPasswordForm');
$router->post('/password/reset', 'AuthController@resetPassword');
$router->get('/email/verify', 'AuthController@verifyEmail');
$router->get('/logout', 'AuthController@logout');

// Universal Password Reset Routes (for all user types)
$router->get('/password-reset', 'PasswordResetController@showResetRequest');
$router->post('/password-reset/request', 'PasswordResetController@processResetRequest');
$router->get('/password-reset/verify', 'PasswordResetController@showResetVerify');
$router->post('/password-reset/verify', 'PasswordResetController@processResetVerify');
$router->get('/user/dashboard', 'UserController@dashboard');
$router->get('/user/orders', 'UserController@orders');
$router->get('/user/account', 'UserController@account');
$router->post('/user/account', 'UserController@accountUpdate');
$router->get('/user/orders/show', 'UserController@orderShow');
$router->get('/user/travel-bookings', 'UserController@travelBookings');
$router->get('/user/travel-bookings/show', 'UserController@travelBookingShow');
$router->get('/orders/status', 'UserController@orderStatus');
$router->get('/user/points', 'UserPointsController@index');

// Support (user)
$router->get('/support', 'SupportController@index');
$router->post('/support/start', 'SupportController@start');
$router->get('/support/messages', 'SupportController@messages');
$router->post('/support/send', 'SupportController@send');

// Organizer Auth (email-based login)
$router->get('/organizer/login', 'AuthController@loginOrganizerForm');
$router->post('/organizer/login', 'AuthController@loginOrganizer');
$router->get('/organizer/register', 'AuthController@registerOrganizerForm');
$router->post('/organizer/register', 'AuthController@registerOrganizer');
$router->get('/organizer/verify-otp', 'AuthController@organizerOtpForm');
$router->post('/organizer/verify-otp', 'AuthController@organizerOtpVerify');
$router->post('/organizer/verify-otp/resend', 'AuthController@organizerOtpResend');

// Organizer Dashboard and Events (requires approval)
$router->get('/organizer/dashboard', 'OrganizerController@dashboard');
$router->get('/organizer/events', 'EventController@index');
$router->get('/organizer/events/create', 'EventController@create');
$router->post('/organizer/events', 'EventController@store');
$router->get('/organizer/events/edit', 'EventController@edit');
$router->post('/organizer/events/update', 'EventController@update');
$router->post('/organizer/events/delete', 'EventController@destroy');
$router->get('/organizer/events/report', 'OrganizerController@eventReport');
$router->get('/organizer/reports', 'OrganizerController@reports');
$router->get('/organizer/withdrawals', 'OrganizerController@withdrawals');
$router->post('/organizer/withdrawals', 'OrganizerController@requestWithdrawal');
$router->get('/organizer/profile', 'OrganizerController@profile');
$router->post('/organizer/profile', 'OrganizerController@profileSave');
$router->post('/organizer/profile/verify-phone', 'OrganizerController@startPhoneVerify');
$router->post('/organizer/profile/verify-phone/confirm', 'OrganizerController@confirmPhoneVerify');
$router->post('/organizer/profile/payment-info', 'OrganizerController@savePaymentInfo');
$router->get('/organizer/scanner-devices', 'OrganizerController@scannerDevices');
$router->post('/organizer/scanner-devices', 'OrganizerController@createScannerDevice');
$router->post('/organizer/scanner-devices/update', 'OrganizerController@updateScannerDevice');
$router->post('/organizer/scanner-devices/delete', 'OrganizerController@deleteScannerDevice');
$router->get('/organizer/event-scanner-assignments', 'OrganizerController@eventScannerAssignments');
$router->post('/organizer/event-scanner-assignments/assign', 'OrganizerController@assignScannerToEvent');
$router->post('/organizer/event-scanner-assignments/unassign', 'OrganizerController@unassignScannerFromEvent');

// Admin
$router->get('/admin', 'AdminController@index');
$router->get('/admin/points', 'AdminPointsController@index');
$router->post('/admin/points/add', 'AdminPointsController@add');
$router->post('/admin/points/remove', 'AdminPointsController@remove');
$router->post('/admin/points/save-config', 'AdminPointsController@saveConfig');
$router->get('/admin/support', 'AdminSupportController@index');
$router->get('/admin/support/conversation', 'AdminSupportController@show');
$router->get('/admin/support/messages', 'AdminSupportController@messages');
$router->post('/admin/support/send', 'AdminSupportController@send');
$router->get('/admin/login', 'AdminController@loginForm');
$router->post('/admin/login', 'AdminController@login');
$router->get('/admin/organizers', 'AdminController@organizers');
$router->get('/admin/organizers/export', 'AdminController@exportOrganizers');
$router->post('/admin/organizers/approve', 'AdminController@approveOrganizer');
$router->post('/admin/organizers/commission', 'AdminController@setOrganizerCommission');
$router->post('/admin/organizers/delete', 'AdminController@deleteOrganizer');
$router->post('/admin/organizers/verify-payment', 'AdminController@verifyOrganizerPayment');
$router->post('/admin/organizers/toggle', 'AdminController@toggleOrganizer');
$router->get('/admin/organizers/create', 'AdminController@organizerEdit');
$router->get('/admin/organizers/edit', 'AdminController@organizerEdit');
$router->post('/admin/organizers/save', 'AdminController@organizerSave');
$router->get('/admin/organizers/show', 'AdminController@organizerShow');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/users/export', 'AdminController@exportUsers');
$router->post('/admin/users/toggle', 'AdminController@toggleUser');
$router->post('/admin/users/delete', 'AdminController@deleteUser');
$router->get('/admin/events', 'AdminController@events');
$router->get('/admin/events/create', 'AdminController@eventCreate');
$router->post('/admin/events/save', 'AdminController@eventStore');
$router->get('/admin/scanners', 'AdminController@adminScannerDevices');
$router->post('/admin/scanners/create', 'AdminController@adminCreateScannerDevice');
$router->post('/admin/scanners/update', 'AdminController@adminUpdateScannerDevice');
$router->post('/admin/scanners/delete', 'AdminController@adminDeleteScannerDevice');
$router->get('/admin/scanners/assignments', 'AdminController@adminScannerAssignments');
$router->post('/admin/scanners/assign', 'AdminController@adminAssignScannerToEvent');
$router->post('/admin/scanners/unassign', 'AdminController@adminUnassignScannerFromEvent');
// Admin Travel Scanners (system agency)
$router->get('/admin/travel/scanners', 'AdminController@adminTravelScanners');
$router->post('/admin/travel/scanners/create', 'AdminController@adminCreateTravelScanner');
$router->post('/admin/travel/scanners/update', 'AdminController@adminUpdateTravelScanner');
$router->post('/admin/travel/scanners/delete', 'AdminController@adminDeleteTravelScanner');
$router->get('/admin/travel/scanners/assignments', 'AdminController@adminTravelScannerAssignments');
$router->post('/admin/travel/scanners/assign', 'AdminController@adminAssignTravelScannerToDestination');
$router->post('/admin/travel/scanners/unassign', 'AdminController@adminUnassignTravelScannerFromDestination');
$router->get('/admin/categories', 'AdminCategoryController@index');
$router->get('/admin/categories/create', 'AdminCategoryController@form');
$router->get('/admin/categories/edit', 'AdminCategoryController@form');
$router->post('/admin/categories/save', 'AdminCategoryController@save');
$router->post('/admin/categories/delete', 'AdminCategoryController@delete');
$router->post('/admin/categories/toggle', 'AdminCategoryController@toggle');
$router->get('/admin/banners', 'AdminController@banners');
$router->get('/admin/banners/create', 'AdminController@bannerCreate');
$router->post('/admin/banners', 'AdminController@bannerStore');
$router->post('/admin/banners/delete', 'AdminController@bannerDelete');
$router->get('/admin/banners/edit', 'AdminController@bannerEdit');
$router->post('/admin/banners/update', 'AdminController@bannerUpdate');
$router->post('/admin/banners/toggle', 'AdminController@bannerToggle');
$router->get('/admin/travel-banners', 'AdminController@travelBanners');
$router->get('/admin/travel-banners/create', 'AdminController@travelBannerCreate');
$router->post('/admin/travel-banners', 'AdminController@travelBannerStore');
$router->post('/admin/travel-banners/delete', 'AdminController@travelBannerDelete');
$router->get('/admin/travel-banners/edit', 'AdminController@travelBannerEdit');
$router->post('/admin/travel-banners/update', 'AdminController@travelBannerUpdate');
$router->post('/admin/travel-banners/toggle', 'AdminController@travelBannerToggle');
$router->get('/admin/partner-logos', 'AdminController@partnerLogos');
$router->get('/admin/partner-logos/create', 'AdminController@partnerLogosCreate');
$router->post('/admin/partner-logos', 'AdminController@partnerLogosStore');
$router->post('/admin/partner-logos/delete', 'AdminController@partnerLogosDelete');
$router->get('/admin/partner-logos/edit', 'AdminController@partnerLogosEdit');
$router->post('/admin/partner-logos/update', 'AdminController@partnerLogosUpdate');
$router->post('/admin/partner-logos/toggle', 'AdminController@partnerLogosToggle');
$router->get('/admin/events/edit', 'AdminController@eventEdit');
$router->get('/admin/events/show', 'AdminController@eventShow');
$router->post('/admin/events/update', 'AdminController@eventUpdate');
$router->post('/admin/events/publish', 'AdminController@eventPublish');
$router->post('/admin/events/feature', 'AdminController@eventFeature');
$router->post('/admin/events/delete', 'AdminController@eventDelete');
// Admin — Travel module
$router->get('/admin/travel/agencies', 'AdminController@travelAgencies');
$router->get('/admin/travel/agencies/export', 'AdminController@exportTravelAgencies');
$router->get('/admin/travel/agencies/show', 'AdminController@travelAgencyShow');
$router->post('/admin/travel/agencies/approve', 'AdminController@approveTravelAgency');
$router->post('/admin/travel/agencies/verify-phone', 'AdminController@verifyTravelAgencyPhone');
$router->post('/admin/travel/agencies/commission', 'AdminController@setTravelAgencyCommission');
$router->post('/admin/travel/agencies/verify-payment', 'AdminController@verifyTravelAgencyPayment');
$router->post('/admin/travel/agencies/toggle', 'AdminController@toggleTravelAgency');
$router->post('/admin/travel/agencies/delete', 'AdminController@deleteTravelAgency');
$router->get('/admin/travel/destinations', 'AdminController@travelDestinations');
$router->get('/admin/travel/destinations/create', 'AdminController@travelDestinationCreate');
$router->post('/admin/travel/destinations/save', 'AdminController@travelDestinationStore');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings', 'AdminController@saveSettings');
$router->post('/admin/settings/test-email', 'AdminController@sendTestEmail');
$router->post('/admin/settings/restore-mpesa', 'AdminController@restoreMpesaFromEnv');
$router->get('/admin/profile', 'AdminController@profile');
$router->post('/admin/profile', 'AdminController@profileUpdate');
$router->get('/admin/accounts/create', 'AdminController@createAccounts');
$router->post('/admin/accounts/create-user', 'AdminController@createUser');
$router->post('/admin/accounts/create-organizer', 'AdminController@createOrganizer');
$router->post('/admin/accounts/create-travel', 'AdminController@createTravelAgency');
$router->get('/admin/email-templates', 'AdminController@emailTemplates');
$router->post('/admin/email-templates', 'AdminController@saveEmailTemplates');
$router->get('/admin/sms-templates', 'AdminController@smsTemplates');
$router->post('/admin/sms-templates', 'AdminController@saveSmsTemplates');
$router->post('/admin/sms-test', 'AdminController@testSms');
$router->get('/admin/communications', 'CommunicationController@index');
$router->get('/admin/communications/create', 'CommunicationController@create');
$router->post('/admin/communications/store', 'CommunicationController@store');
$router->get('/admin/communications/show', 'CommunicationController@show');
$router->post('/admin/communications/send', 'CommunicationController@send');
$router->get('/admin/communications/recipients', 'CommunicationController@getRecipients');
$router->get('/admin/communications/direct-sms', 'CommunicationController@directSms');
$router->post('/admin/communications/direct-sms', 'CommunicationController@sendDirectSms');
$router->get('/admin/hotels', 'AdminController@hotelsIndex');
$router->get('/admin/hotels/show', 'AdminController@hotelShow');
$router->post('/admin/hotels/update-status', 'AdminController@updateHotelStatus');
$router->get('/admin/pages', 'AdminController@pages');
$router->get('/admin/pages/edit', 'AdminController@pageEdit');
$router->post('/admin/pages/save', 'AdminController@pageSave');
$router->post('/admin/pages/delete', 'AdminController@pageDelete');
$router->get('/admin/partners', 'AdminController@partners');
$router->post('/admin/partners/status', 'AdminController@partnerStatus');
$router->get('/admin/scans', 'AdminController@scans');
// Admin scanner assignments removed - organizers manage their own devices
$router->get('/admin/withdrawals', 'AdminController@withdrawalsIndex');
$router->post('/admin/withdrawals/update', 'AdminController@withdrawalsUpdate');

// Featured Content Management
$router->get('/admin/featured-content', 'FeaturedContentController@index');
$router->post('/admin/featured-content/feature-event', 'FeaturedContentController@featureEvent');
$router->post('/admin/featured-content/unfeature-event', 'FeaturedContentController@unfeatureEvent');
$router->post('/admin/featured-content/feature-destination', 'FeaturedContentController@featureDestination');
$router->post('/admin/featured-content/unfeature-destination', 'FeaturedContentController@unfeatureDestination');
$router->post('/admin/featured-content/update-commission-settings', 'FeaturedContentController@updateCommissionSettings');

// Finance Module
$router->get('/admin/finance', 'FinanceController@adminDashboard');
$router->get('/travel/finance', 'FinanceController@travelDashboard');
$router->get('/travel/marketing', 'DisabledController@notFound');
$router->get('/travel/campaign-reports', 'DisabledController@notFound');
$router->post('/travel/campaign-request', 'DisabledController@notFound');
$router->get('/travel/destinations/api', 'TravelController@destinationsApi');
$router->post('/travel/marketing-order', 'TravelController@marketingOrder');
$router->get('/organizer/finance', 'FinanceController@organizerDashboard');
$router->get('/organizer/marketing', 'DisabledController@notFound');
$router->get('/organizer/campaign-reports', 'DisabledController@notFound');
$router->get('/organizer/campaign/details', 'DisabledController@notFound');
$router->post('/organizer/campaign-request', 'DisabledController@notFound');
$router->get('/organizer/events/api', 'OrganizerController@eventsApi');
$router->post('/organizer/marketing-order', 'OrganizerController@marketingOrder');
$router->get('/finance/export', 'FinanceController@exportReport');

        // Feature Requests
        $router->post('/organizer/events/request-feature', 'FeatureRequestController@requestFeatureEvent');
        $router->post('/travel/destinations/request-feature', 'FeatureRequestController@requestFeatureDestination');
        $router->post('/admin/featured-content/approve-request', 'FeatureRequestController@approveRequest');
        $router->post('/admin/featured-content/reject-request', 'FeatureRequestController@rejectRequest');
        
        // Notification Templates Management
        $router->get('/admin/notification-templates', 'NotificationTemplateController@index');
        $router->post('/admin/notification-templates/update', 'NotificationTemplateController@update');
        $router->get('/admin/notification-templates/preview', 'NotificationTemplateController@preview');

		// Marketing Management (disabled)
		$router->get('/admin/marketing', 'DisabledController@notFound');
		$router->get('/admin/marketing/orders/show', 'DisabledController@notFound');
		$router->get('/api/marketing-pricing', 'DisabledController@notFound');
		
		// Marketing API Routes (disabled)
		$router->get('/api/admin/marketing/campaigns', 'DisabledController@notFound');
		$router->get('/api/admin/marketing/campaigns/{id}', 'DisabledController@notFound');
		$router->post('/api/admin/marketing/campaigns/update-status', 'DisabledController@notFound');
		$router->get('/api/admin/marketing/stats', 'DisabledController@notFound');
		$router->get('/api/admin/marketing/packages', 'DisabledController@notFound');
		$router->post('/api/admin/marketing/packages', 'DisabledController@notFound');
		$router->get('/api/admin/marketing/packages/{id}', 'DisabledController@notFound');
		$router->put('/api/admin/marketing/packages/{id}', 'DisabledController@notFound');
		$router->put('/api/admin/marketing/packages', 'DisabledController@notFound');
		$router->delete('/api/admin/marketing/packages', 'DisabledController@notFound');
		$router->get('/api/marketing/packages', 'DisabledController@notFound');
		$router->get('/api/marketing/rates', 'DisabledController@notFound');
		$router->get('/api/admin/marketing/orders', 'DisabledController@notFound');
		$router->post('/api/admin/marketing/orders/update-status', 'DisabledController@notFound');

// Payments
$router->get('/pay/mpesa', 'PaymentController@mpesa');
$router->post('/pay/mpesa', 'PaymentController@mpesa');
$router->post('/pay/mpesa/callback', 'PaymentController@mpesaCallback');
$router->get('/pay/mpesa/reconcile', 'PaymentController@mpesaReconcile');
$router->get('/pay/paypal', 'PaymentController@paypal');
$router->post('/pay/paypal', 'PaymentController@paypal');
$router->get('/pay/flutterwave', 'PaymentController@flutterwave');
$router->post('/pay/flutterwave', 'PaymentController@flutterwave');

// Orders / Checkout
$router->post('/orders', 'CheckoutController@create');

// Scanner (subdomain can point to same app; routes prefixed by /scanner)
$router->get('/scanner/login', 'ScannerController@loginForm');
$router->post('/scanner/login', 'ScannerController@login');
$router->get('/scanner', 'ScannerController@index');
$router->post('/scanner/verify', 'ScannerController@verify');
$router->get('/scanner/debug-session', 'ScannerController@debugSession');
$router->get('/scanner/test-scan', 'ScannerController@testScan');
$router->get('/scanner/verify', 'ScannerController@verify');

// Travel Agency routes (subdomain can point to same app; routes prefixed by /travel)
$router->get('/travel/login', 'TravelAuthController@login');
$router->post('/travel/login', 'TravelAuthController@login');
$router->get('/travel/register', 'TravelAuthController@register');
$router->post('/travel/register', 'TravelAuthController@register');
$router->get('/travel/verify-otp', 'TravelAuthController@verifyOtp');
$router->post('/travel/verify-otp', 'TravelAuthController@verifyOtp');
$router->post('/travel/resend-otp', 'TravelAuthController@resendOtp');
$router->post('/travel/logout', 'TravelAuthController@logout');
$router->get('/travel/clear-session', 'TravelAuthController@clearSession');

// Travel scanner routes (simplified - use universal scanner)
$router->get('/travel/scanner', 'TravelController@scanner');
$router->get('/travel/scanner/create', 'TravelController@createScanner');
$router->post('/travel/scanner/create', 'TravelController@createScanner');
$router->post('/travel/scanner/delete', 'TravelController@deleteScanner');
$router->post('/travel/scanner/toggle', 'TravelController@toggleScanner');
$router->get('/travel/scanner/scan', 'TravelController@scannerScan');
$router->post('/travel/scanner/verify', 'TravelController@scannerVerify');
$router->get('/travel/scanner/edit', 'TravelController@editScanner');
$router->post('/travel/scanner/edit', 'TravelController@updateScanner');
$router->get('/travel/scanner/available', 'TravelController@getAvailableScanners');
$router->post('/travel/scanner/assign', 'TravelController@assignScanner');
$router->post('/travel/scanner/delete', 'TravelController@deleteScanner');

$router->get('/travel/dashboard', 'TravelController@dashboard');
$router->get('/travel/destinations', 'TravelController@destinations');
$router->get('/travel/destinations/create', 'TravelController@destinationCreate');
$router->post('/travel/destinations/create', 'TravelController@destinationCreate');
$router->get('/travel/destinations/edit', 'TravelController@destinationEdit');
$router->post('/travel/destinations/edit', 'TravelController@destinationEdit');
$router->post('/travel/destinations/request-feature', 'FeatureRequestController@requestFeatureDestination');
$router->get('/travel/withdrawals', 'TravelController@withdrawals');
$router->post('/travel/withdrawals/request', 'TravelController@requestWithdrawal');
$router->get('/travel/bookings', 'TravelController@bookings');
$router->get('/travel/profile', 'TravelController@profile');
$router->post('/travel/profile', 'TravelController@profile');
$router->post('/travel/profile/verify-phone', 'TravelController@startPhoneVerify');
$router->post('/travel/profile/verify-phone/confirm', 'TravelController@confirmPhoneVerify');
$router->post('/travel/profile/payment-info', 'TravelController@savePaymentInfo');

// Marketing Module Routes
$router->get('/marketing', 'DisabledController@notFound');
$router->get('/marketing/dashboard', 'DisabledController@notFound');
$router->get('/marketing/campaigns', 'DisabledController@notFound');
$router->get('/marketing/campaigns/create', 'DisabledController@notFound');
$router->post('/marketing/campaigns/create', 'DisabledController@notFound');
$router->get('/marketing/campaigns/analytics', 'DisabledController@notFound');
$router->get('/marketing/segmentation', 'DisabledController@notFound');
$router->get('/marketing/templates', 'DisabledController@notFound');
$router->get('/marketing/reports', 'DisabledController@notFound');
$router->get('/marketing/requests/organizers', 'DisabledController@notFound');
$router->get('/marketing/requests/agencies', 'DisabledController@notFound');

// Marketing AJAX Endpoints
$router->get('/marketing/api/audience-preview', 'DisabledController@notFound');
$router->post('/marketing/api/campaign/approve', 'DisabledController@notFound');
$router->post('/marketing/api/campaign/start', 'DisabledController@notFound');
// Campaign recipients API for admin UI selection
$router->get('/marketing/api/campaign/recipients', 'DisabledController@notFound');

// Dispatch
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
	$requestPath = substr($requestPath, strlen($basePath));
}
if ($requestPath === '' ) { $requestPath = '/'; }
$router->dispatch($_SERVER['REQUEST_METHOD'], $requestPath);



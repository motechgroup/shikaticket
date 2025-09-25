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
$router->get('/partners', 'PartnersController@index');
$router->post('/partners', 'PartnersController@store');
$router->get('/tickets/view', 'TicketsController@view');
$router->get('/organizers/show', 'OrganizersController@show');
$router->post('/organizers/follow', 'OrganizersController@follow');
$router->post('/organizers/unfollow', 'OrganizersController@unfollow');
$router->get('/help', 'DocsController@index');
$router->get('/help/show', 'DocsController@show');
$router->get('/sitemap.xml', 'SitemapController@index');

// User Auth (phone-based login)
$router->get('/login', 'AuthController@loginUserForm');
$router->post('/login', 'AuthController@loginUser');
$router->get('/register', 'AuthController@registerUserForm');
$router->post('/register', 'AuthController@registerUser');
$router->get('/password/forgot', 'AuthController@forgotPasswordForm');
$router->post('/password/forgot', 'AuthController@sendPasswordReset');
$router->get('/password/reset', 'AuthController@resetPasswordForm');
$router->post('/password/reset', 'AuthController@resetPassword');
$router->get('/email/verify', 'AuthController@verifyEmail');
$router->get('/logout', 'AuthController@logout');
$router->get('/user/dashboard', 'UserController@dashboard');
$router->get('/user/orders', 'UserController@orders');
$router->get('/user/account', 'UserController@account');
$router->post('/user/account', 'UserController@accountUpdate');
$router->get('/user/orders/show', 'UserController@orderShow');
$router->get('/orders/status', 'UserController@orderStatus');

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
$router->get('/organizer/scanner-devices', 'OrganizerController@scannerDevices');
$router->post('/organizer/scanner-devices', 'OrganizerController@createScannerDevice');
$router->post('/organizer/scanner-devices/update', 'OrganizerController@updateScannerDevice');
$router->post('/organizer/scanner-devices/delete', 'OrganizerController@deleteScannerDevice');
$router->get('/organizer/event-scanner-assignments', 'OrganizerController@eventScannerAssignments');
$router->post('/organizer/event-scanner-assignments/assign', 'OrganizerController@assignScannerToEvent');
$router->post('/organizer/event-scanner-assignments/unassign', 'OrganizerController@unassignScannerFromEvent');

// Admin
$router->get('/admin', 'AdminController@index');
$router->get('/admin/login', 'AdminController@loginForm');
$router->post('/admin/login', 'AdminController@login');
$router->get('/admin/organizers', 'AdminController@organizers');
$router->post('/admin/organizers/approve', 'AdminController@approveOrganizer');
$router->post('/admin/organizers/commission', 'AdminController@setOrganizerCommission');
$router->post('/admin/organizers/toggle', 'AdminController@toggleOrganizer');
$router->post('/admin/organizers/delete', 'AdminController@deleteOrganizer');
$router->get('/admin/organizers/create', 'AdminController@organizerEdit');
$router->get('/admin/organizers/edit', 'AdminController@organizerEdit');
$router->post('/admin/organizers/save', 'AdminController@organizerSave');
$router->get('/admin/organizers/show', 'AdminController@organizerShow');
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/users/toggle', 'AdminController@toggleUser');
$router->post('/admin/users/delete', 'AdminController@deleteUser');
$router->get('/admin/events', 'AdminController@events');
$router->get('/admin/banners', 'AdminController@banners');
$router->get('/admin/banners/create', 'AdminController@bannerCreate');
$router->post('/admin/banners', 'AdminController@bannerStore');
$router->post('/admin/banners/delete', 'AdminController@bannerDelete');
$router->get('/admin/banners/edit', 'AdminController@bannerEdit');
$router->post('/admin/banners/update', 'AdminController@bannerUpdate');
$router->post('/admin/banners/toggle', 'AdminController@bannerToggle');
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
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings', 'AdminController@saveSettings');
$router->post('/admin/settings/test-email', 'AdminController@sendTestEmail');
$router->post('/admin/settings/restore-mpesa', 'AdminController@restoreMpesaFromEnv');
$router->get('/admin/profile', 'AdminController@profile');
$router->post('/admin/profile', 'AdminController@profileSave');
$router->get('/admin/email-templates', 'AdminController@emailTemplates');
$router->post('/admin/email-templates', 'AdminController@saveEmailTemplates');
$router->get('/admin/sms-templates', 'AdminController@smsTemplates');
$router->post('/admin/sms-templates', 'AdminController@saveSmsTemplates');
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
$router->get('/scanner/verify', 'ScannerController@verify');

// Dispatch
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
if ($basePath !== '' && strpos($requestPath, $basePath) === 0) {
	$requestPath = substr($requestPath, strlen($basePath));
}
if ($requestPath === '' ) { $requestPath = '/'; }
$router->dispatch($_SERVER['REQUEST_METHOD'], $requestPath);



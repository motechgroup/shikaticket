<?php /** @var string $viewFile */ ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $siteTitle = \App\Models\Setting::get('site.name', 'ShikaTicket'); ?>
	<title><?php echo htmlspecialchars($siteTitle); ?></title>
	<script>
		// Tailwind CDN config: extend with brand colors
		tailwind = window.tailwind || {}; tailwind.config = {
			theme: { extend: { colors: { brand: { red: '#ef4444', red600: '#dc2626' }, dark: { bg: '#0b0b0b', card: '#111111', mute: '#9ca3af' } } } }
		};
	</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php 
    $siteLogo = \App\Models\Setting::get('site.logo', 'logo.png'); 
    $siteFavicon = \App\Models\Setting::get('site.favicon', $siteLogo);
    $siteTitle = \App\Models\Setting::get('site.name', 'ShikaTicket'); 
    // Build favicon URL with cache-busting when file exists
    $faviconRel = ltrim($siteFavicon, '/');
    $publicRoot = __DIR__ . '/../../public/';
    $absIcon = $publicRoot . $faviconRel;
    $faviconUrl = base_url($siteFavicon);
    if (@file_exists($absIcon)) {
        $faviconUrl = base_url($siteFavicon . '?v=' . @filemtime($absIcon));
    }
    ?>
    <link rel="icon" href="<?php echo $faviconUrl; ?>" type="image/png">
    <link rel="shortcut icon" href="<?php echo $faviconUrl; ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo $faviconUrl; ?>">
    <?php 
        $metaTitle = \App\Models\Setting::get('seo.meta_title', $siteTitle);
        $metaDesc = \App\Models\Setting::get('seo.meta_description', \App\Models\Setting::get('site.description',''));
        $metaKeywords = \App\Models\Setting::get('seo.meta_keywords', 'events,tickets,concerts');
        $metaRobots = \App\Models\Setting::get('seo.meta_robots', 'index,follow');
        $ogImage = \App\Models\Setting::get('seo.og_image', $siteLogo);
        $tw = \App\Models\Setting::get('seo.twitter', '');
        // Event-specific overrides
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (strpos($path, '/events/show') !== false && isset($_GET['id'])) {
            try {
                $stmt = db()->prepare('SELECT title, description, poster_path FROM events WHERE id = ? LIMIT 1');
                $stmt->execute([(int)$_GET['id']]);
                if ($row = $stmt->fetch()) {
                    $metaTitle = trim($row['title'] . ' | ' . $siteTitle);
                    $metaDesc = $row['description'] !== null && $row['description'] !== '' ? substr(strip_tags($row['description']), 0, 160) : $metaDesc;
                    if (!empty($row['poster_path'])) { $ogImage = $row['poster_path']; }
                }
            } catch (\Throwable $e) {}
        } elseif ($path === '/events') {
            // SEO defaults for events listing page
            $metaTitle = 'Buy Event Tickets in Kenya | Concerts, Comedy & Festivals - ' . $siteTitle;
            $metaDesc = 'Discover and book tickets for top events in Kenya. Concerts, comedy shows, theatre and more on ' . $siteTitle . '.';
        }
    ?>
    <meta name="title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($metaDesc); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($metaRobots); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDesc); ?>">
    <meta property="og:image" content="<?php echo base_url($ogImage); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteTitle); ?>">
    <meta property="og:url" content="<?php echo base_url(ltrim($_SERVER['REQUEST_URI'] ?? '/', '/')); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?php if ($tw): ?><meta name="twitter:site" content="<?php echo htmlspecialchars($tw); ?>"><?php endif; ?>
    <link rel="canonical" href="<?php echo base_url(ltrim($_SERVER['REQUEST_URI'] ?? '/', '/')); ?>">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": <?php echo json_encode($siteTitle); ?>,
      "url": <?php echo json_encode(base_url('/')); ?>,
      "logo": <?php echo json_encode(base_url($siteLogo)); ?>,
      "sameAs": [
        <?php 
          $same = [];
          $fb = \App\Models\Setting::get('site.facebook',''); if ($fb) $same[] = $fb;
          $twx = \App\Models\Setting::get('site.twitter',''); if ($twx) $same[] = $twx;
          $ig = \App\Models\Setting::get('site.instagram',''); if ($ig) $same[] = $ig;
          echo implode(',', array_map('json_encode', $same));
        ?>
      ]
    }
    </script>
	<style>
		:root{ --bg:#0b0b0b; --card:#111111; --text:#e5e7eb; --muted:#9ca3af; --accent:#ef4444; --accent-600:#dc2626; }
		body{ background-color:var(--bg); color:var(--text); }
		.header{ background-color:#0d0d0d; border-bottom:1px solid #1f2937; }
		.footer{ background-color:#0d0d0d; border-top:1px solid #1f2937; color:var(--muted); }
		.card{ background-color:var(--card); border:1px solid #1f2937; border-radius:0.5rem; transition:all .15s ease; }
		.card-hover:hover{ border-color:var(--accent); box-shadow:0 6px 20px rgba(0,0,0,.35); transform:translateY(-2px); }
		.input,.select,.textarea{ background:#0f0f10; border:1px solid #27272a; color:var(--text); border-radius:0.5rem; padding:0.5rem 0.75rem; width:100%; }
		.input:focus,.select:focus,.textarea:focus{ outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgb(239 68 68 / 15%); }
		.btn{ display:inline-flex; align-items:center; justify-content:center; gap:.5rem; border-radius:.5rem; padding:.5rem 1rem; font-weight:600; transition:all .15s ease; }
		.btn-primary{ background:var(--accent); color:white; }
		.btn-primary:hover{ background:var(--accent-600); }
		.btn-secondary{ background:#1f2937; color:#e5e7eb; }
		.btn-secondary:hover{ background:#374151; }
		.link{ color:#e5e7eb; }
		.link:hover{ color:var(--accent); }
		.badge{ display:inline-block; background:#1f2937; color:#e5e7eb; border:1px solid #374151; border-radius:.375rem; padding:.125rem .5rem; font-size:.75rem; }
		
		/* Modern Sidebar Styles */
		.sidebar-nav-item {
			transition: all 0.2s ease;
		}
		.sidebar-nav-item:hover {
			background-color: rgba(55, 65, 81, 0.5);
			transform: translateX(2px);
		}
		.sidebar-nav-item svg {
			transition: all 0.2s ease;
		}
		.sidebar-section-title {
			color: #6b7280;
			font-size: 0.75rem;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.05em;
		}
		.table th{ color:#e5e7eb; background:#0f0f10; }
		.table td, .table th{ border-top:1px solid #1f2937; }
		.alert-success{ border:1px solid #14532d; background:#052e16; color:#86efac; border-radius:.5rem; }
		.alert-error{ border:1px solid #7f1d1d; background:#450a0a; color:#fecaca; border-radius:.5rem; }
		.main-with-sidebar{ margin-left:0; }
		@media (min-width: 768px){ .main-with-sidebar{ margin-left:18rem; } }
		
		/* Mobile-First Responsive Design */
		@media (max-width: 767px) {
			.main-with-sidebar { margin-left: 0; }
			.mobile-sidebar-overlay {
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: rgba(0, 0, 0, 0.5);
				z-index: 39;
				display: none;
			}
			.mobile-sidebar-overlay.active {
				display: block;
			}
			.sidebar-mobile {
				position: fixed;
				top: 0;
				left: -100%;
				width: 280px;
				height: 100vh;
				background: #0d0d0d;
				border-right: 1px solid #374151;
				z-index: 40;
				transition: left 0.3s ease;
			}
			.sidebar-mobile.active {
				left: 0;
			}
			/* Mobile-friendly tables */
			.table-responsive {
				overflow-x: auto;
				-webkit-overflow-scrolling: touch;
			}
			.table-responsive table {
				min-width: 600px;
			}
			/* Mobile cards */
			.mobile-card {
				padding: 1rem;
				margin-bottom: 1rem;
			}
			/* Touch-friendly buttons */
			.touch-target {
				min-height: 44px;
				min-width: 44px;
				cursor: pointer;
				-webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
				touch-action: manipulation;
			}
			#adminMobileToggle {
				cursor: pointer !important;
				pointer-events: auto !important;
				z-index: 50 !important;
				position: relative;
			}
		}
	</style>
</head>
<body class="min-h-screen flex flex-col">
<?php 
// Debug: Check if we're in admin area
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
$currentRole = $_SESSION['role'] ?? 'none';
$isInAdminPath = strpos($currentUri, '/admin') !== false;
$hasAdminRole = in_array($currentRole, ['admin', 'manager', 'accountant']);
$isAdminSidebar = $isInAdminPath && $hasAdminRole;

// Debug output (remove after testing)
if ($isInAdminPath) {
    error_log("Admin Debug - URI: $currentUri, Role: $currentRole, Has Role: " . ($hasAdminRole ? 'yes' : 'no') . ", Sidebar: " . ($isAdminSidebar ? 'yes' : 'no'));
}
?>
<?php $isOrganizerSidebar = (strpos($_SERVER['REQUEST_URI'] ?? '', '/organizer') !== false) && isset($_SESSION['organizer_id']); ?>
<?php if ($isAdminSidebar): ?>
    <!-- Mobile Sidebar Overlay -->
    <div id="mobileSidebarOverlay" class="mobile-sidebar-overlay"></div>
    
    <!-- Mobile Sidebar -->
    <aside id="mobileSidebar" class="sidebar-mobile md:hidden">
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 flex items-center justify-between border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <img src="<?php echo base_url($siteLogo); ?>" class="h-9 w-auto" alt="logo">
                    <span class="font-semibold">Admin</span>
                </div>
                <button id="mobileSidebarClose" class="md:hidden inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-800 touch-target" aria-label="Close menu">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
                <!-- Dashboard -->
                <div class="space-y-1">
                    <a href="<?php echo base_url('/admin'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </div>

                <!-- Users & Organizers -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Users & Partners</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/users'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <span>Users</span>
                    </a>
                    <a href="<?php echo base_url('/admin/organizers'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Organizers</span>
                    </a>
                    <a href="<?php echo base_url('/admin/accounts/create'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Create Accounts</span>
                    </a>
                </div>

                <!-- Events & Content -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Events & Content</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/events'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Events</span>
                    </a>
                    
                    <a href="<?php echo base_url('/admin/categories'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <span>Categories</span>
                    </a>
                    <a href="<?php echo base_url('/admin/categories'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <span>Categories</span>
                    </a>

                    <a href="<?php echo base_url('/admin/banners'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Banners</span>
                    </a>
                    <a href="<?php echo base_url('/admin/pages'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Pages</span>
                    </a>
                    <a href="<?php echo base_url('/admin/featured-content'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span>Featured Content</span>
                        <?php
                        // Get pending feature requests count for notification badge
                        $featureRequestCount = 0;
                        if (isset($_SESSION['admin']) && $_SESSION['admin']) {
                            try {
                                $stmt = db()->query("SELECT COUNT(*) as count FROM feature_requests WHERE status = 'pending'");
                                $featureRequestCount = (int)($stmt->fetch()['count'] ?? 0);
                            } catch (\Throwable $e) {}
                        }
                        ?>
                        <?php if ($featureRequestCount > 0): ?>
                            <span class="bg-yellow-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                                <?php echo $featureRequestCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo base_url('/admin/notification-templates'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 00-15 0v5h5l-5 5-5-5h5v-5a10 10 0 0120 0v5z"></path>
                        </svg>
                        <span>Notification Templates</span>
                    </a>
                    <!-- Marketing Management link removed -->
                    <?php /*
                    <!-- Marketing items removed -->
                    */ ?>
                </div>

                <!-- Partners & Branding -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Partners & Branding</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/partners'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Partners</span>
                    </a>
                    <a href="<?php echo base_url('/admin/partner-logos'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Partner Logos</span>
                    </a>
                </div>
                <!-- Travel Management -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Travel</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/travel/agencies'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Travel Agencies</span>
                    </a>
                    <a href="<?php echo base_url('/admin/travel/destinations'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Destinations</span>
                    </a>
                    <a href="<?php echo base_url('/admin/travel-banners'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Travel Banners</span>
                    </a>
                </div>

                <!-- Hotels -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Hotels</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/hotels'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Hotel Applications</span>
                    </a>
                </div>

                <!-- Communications -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Communications</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/email-templates'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>Email Templates</span>
                    </a>
                    <a href="<?php echo base_url('/admin/sms-templates'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>SMS Templates</span>
                    </a>
                    <a href="<?php echo base_url('/admin/communications'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <span>Communications</span>
                    </a>
                </div>

                <!-- Operations -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Operations</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/points'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span>Loyalty Points</span>
                    </a>
                    <a href="<?php echo base_url('/admin/scans'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <span>Scans</span>
                    </a>
                    <a href="<?php echo base_url('/admin/withdrawals'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Withdrawals</span>
                    </a>
                    <a href="<?php echo base_url('/admin/finance'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Finance</span>
                    </a>
                </div>

                <!-- Settings & Profile -->
                <div class="space-y-1 pt-4 border-t border-gray-800">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Account</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/settings'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Settings</span>
                    </a>
                    <a href="<?php echo base_url('/admin/profile'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                    <a href="<?php echo base_url('/logout'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group text-red-400 hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>
    
    <!-- Desktop Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-72 hidden md:block z-40 header/side bg-[#0d0d0d] border-r border-gray-800">
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 flex items-center gap-3 border-b border-gray-800">
                <img src="<?php echo base_url($siteLogo); ?>" class="h-9 w-auto" alt="logo">
                <span class="font-semibold">Admin</span>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
                <!-- Dashboard -->
                <div class="space-y-1">
                    <a href="<?php echo base_url('/admin'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </div>

                <!-- Users & Organizers -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Users & Partners</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/users'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <span>Users</span>
                    </a>
                    <a href="<?php echo base_url('/admin/organizers'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Organizers</span>
                    </a>
                    <a href="<?php echo base_url('/admin/accounts/create'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Create Accounts</span>
                    </a>
                </div>

                <!-- Events & Content -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Events & Content</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/events'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Events</span>
                    </a>
                    <a href="<?php echo base_url('/admin/banners'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Banners</span>
                    </a>
                    <a href="<?php echo base_url('/admin/pages'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Pages</span>
                    </a>
                    <a href="<?php echo base_url('/admin/featured-content'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span>Featured Content</span>
                        <?php
                        // Get pending feature requests count for notification badge
                        $featureRequestCount = 0;
                        if (isset($_SESSION['admin']) && $_SESSION['admin']) {
                            try {
                                $stmt = db()->query("SELECT COUNT(*) as count FROM feature_requests WHERE status = 'pending'");
                                $featureRequestCount = (int)($stmt->fetch()['count'] ?? 0);
                            } catch (\Throwable $e) {}
                        }
                        ?>
                        <?php if ($featureRequestCount > 0): ?>
                            <span class="bg-yellow-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                                <?php echo $featureRequestCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo base_url('/admin/notification-templates'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 00-15 0v5h5l-5 5-5-5h5v-5a10 10 0 0120 0v5z"></path>
                        </svg>
                        <span>Notification Templates</span>
                    </a>
                </div>

                <!-- Partners & Branding -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Partners & Branding</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/partners'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Partners</span>
                    </a>
                    <a href="<?php echo base_url('/admin/partner-logos'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Partner Logos</span>
                    </a>
                </div>

                <!-- Travel Management -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Travel</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/travel/agencies'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Travel Agencies</span>
                    </a>
                    <a href="<?php echo base_url('/admin/travel/destinations'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Destinations</span>
                    </a>
                    <a href="<?php echo base_url('/admin/travel-banners'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Travel Banners</span>
                    </a>
                </div>

                <!-- Hotels -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Hotels</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/hotels'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Hotel Applications</span>
                    </a>
                </div>

                <!-- Communications -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Communications</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/email-templates'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>Email Templates</span>
                    </a>
                    <a href="<?php echo base_url('/admin/sms-templates'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>SMS Templates</span>
                    </a>
                    <a href="<?php echo base_url('/admin/communications'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <span>Communications</span>
                    </a>
                </div>

                <!-- Operations -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Operations</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/points'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span>Loyalty Points</span>
                    </a>
                    <a href="<?php echo base_url('/admin/scans'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <span>Scans</span>
                    </a>
                    <a href="<?php echo base_url('/admin/withdrawals'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Withdrawals</span>
                    </a>
                    <a href="<?php echo base_url('/admin/finance'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Finance</span>
                    </a>
                </div>

                <!-- Settings & Profile -->
                <div class="space-y-1 pt-4 border-t border-gray-800">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Account</h3>
                    </div>
                    <a href="<?php echo base_url('/admin/settings'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Settings</span>
                    </a>
                    <a href="<?php echo base_url('/admin/profile'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                    <a href="<?php echo base_url('/logout'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group text-red-400 hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>
<?php endif; ?>

<?php if ($isOrganizerSidebar): ?>
    <!-- Mobile Sidebar Overlay -->
    <div id="organizerMobileSidebarOverlay" class="mobile-sidebar-overlay"></div>
    
    <!-- Mobile Sidebar -->
    <aside id="organizerMobileSidebar" class="sidebar-mobile md:hidden">
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 flex items-center justify-between border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <img src="<?php echo base_url($siteLogo); ?>" class="h-9 w-auto" alt="logo">
                    <span class="font-semibold">Organizer</span>
                </div>
                <button id="organizerMobileSidebarClose" class="md:hidden inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-800 touch-target" aria-label="Close menu">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
                <!-- Dashboard -->
                <div class="space-y-1">
                    <a href="<?php echo base_url('/organizer/dashboard'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </div>

                <!-- Event Management -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Events</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/events'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>My Events</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/events/create'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Create Event</span>
                    </a>
                </div>

                <!-- Analytics & Reports -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Analytics</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/reports'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Reports</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/finance'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span>Finance</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/withdrawals'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span>Withdrawals</span>
                    </a>
                </div>

                <!-- Scanner Management -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Scanner</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/scanner-devices'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <span>Scanner Devices</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/event-scanner-assignments'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Scanner Assignments</span>
                    </a>
                </div>

                <!-- Account -->
                <div class="space-y-1 pt-4 border-t border-gray-800">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Account</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/profile'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                    <a href="<?php echo base_url('/logout'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group text-red-400 hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>

    <!-- Desktop Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-72 hidden md:block z-40 header/side bg-[#0d0d0d] border-r border-gray-800">
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 flex items-center gap-3 border-b border-gray-800">
                <img src="<?php echo base_url($siteLogo); ?>" class="h-9 w-auto" alt="logo">
                <span class="font-semibold">Organizer</span>
            </div>
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
                <!-- Dashboard -->
                <div class="space-y-1">
                    <a href="<?php echo base_url('/organizer/dashboard'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </div>

                <!-- Event Management -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Events</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/events'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>My Events</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/events/create'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Create Event</span>
                    </a>
                </div>

                <!-- Analytics & Reports -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Analytics</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/reports'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Reports</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/finance'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span>Finance</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/withdrawals'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span>Withdrawals</span>
                    </a>
                </div>

                <!-- Scanner Management -->
                <div class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Scanner</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/scanner-devices'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <span>Scanner Devices</span>
                    </a>
                    <a href="<?php echo base_url('/organizer/event-scanner-assignments'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Scanner Assignments</span>
                    </a>
                </div>

                <!-- Account -->
                <div class="space-y-1 pt-4 border-t border-gray-800">
                    <div class="px-3 py-1">
                        <h3 class="sidebar-section-title">Account</h3>
                    </div>
                    <a href="<?php echo base_url('/organizer/profile'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                    <a href="<?php echo base_url('/logout'); ?>" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group text-red-400 hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>
<?php endif; ?>

    <?php if ($isOrganizerSidebar): ?>
    <!-- Organizer Header with Hamburger Menu -->
    <header class="header">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Mobile Hamburger Menu -->
                <button id="organizerMobileToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-700 hover:border-red-600 bg-[#0f0f10]/60 backdrop-blur-sm touch-target cursor-pointer" aria-label="Toggle organizer menu" aria-expanded="false">
                    <svg class="w-6 h-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <!-- Logo and Title -->
                <a href="<?php echo base_url('/organizer/dashboard'); ?>" class="flex items-center gap-3">
                    <img src="<?php echo base_url($siteLogo); ?>" alt="logo" class="h-8 w-auto">
                    <div class="hidden sm:block">
                        <span class="font-semibold text-lg text-white">Organizer Portal</span>
                    </div>
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-4 text-sm">
                <a href="<?php echo base_url('/organizer/dashboard'); ?>" class="text-gray-300 hover:text-white transition-colors">Dashboard</a>
                <a href="<?php echo base_url('/organizer/events'); ?>" class="text-gray-300 hover:text-white transition-colors">Events</a>
                <a href="<?php echo base_url('/organizer/reports'); ?>" class="text-gray-300 hover:text-white transition-colors">Reports</a>
                <a href="<?php echo base_url('/organizer/profile'); ?>" class="text-gray-300 hover:text-white transition-colors">Profile</a>
                <a href="<?php echo base_url('/logout'); ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">Logout</a>
            </nav>
            
            <!-- Mobile User Menu -->
            <div class="md:hidden flex items-center gap-3">
                <div class="text-sm text-gray-300">
                    Welcome, Organizer
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <?php $isScanner = strpos($_SERVER['REQUEST_URI'] ?? '', '/scanner') !== false; ?>
    <?php if (!$isScanner && !$isAdminSidebar && !$isOrganizerSidebar): ?>
    <header class="header">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="<?php echo base_url('/'); ?>" class="flex items-center gap-3">
                <img src="<?php echo base_url($siteLogo); ?>" alt="logo" class="h-12 md:h-16 w-auto">
                <?php if (empty($siteLogo)): ?>
                <span class="font-semibold text-lg"><?php echo htmlspecialchars($siteTitle); ?></span>
                <?php endif; ?>
			</a>
            
            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-md mx-8">
                <form action="<?php echo base_url('/search'); ?>" method="GET" class="relative w-full">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Search events, travel destinations..." 
                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                               class="w-full pl-10 pr-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Mobile Search Button -->
            <button id="mobileSearchToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-700 hover:border-red-600 bg-[#0f0f10]/60 backdrop-blur-sm touch-target mr-2" aria-label="Toggle search" aria-expanded="false">
                <svg class="w-5 h-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            
            <?php if ($isAdminSidebar): ?>
            <!-- Mobile Admin Menu Toggle -->
            <button 
                id="adminMobileToggle" 
                type="button"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-red-500 hover:border-red-600 bg-red-900/60 backdrop-blur-sm touch-target cursor-pointer" 
                style="pointer-events: auto !important; z-index: 9999 !important; position: relative; touch-action: manipulation !important;"
                aria-label="Toggle admin menu" 
                aria-expanded="false"
                title="Admin Menu (Role: <?php echo $currentRole; ?>)">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="pointer-events: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <?php else: ?>
            <!-- DEBUG: Not showing admin menu. URI: <?php echo htmlspecialchars($currentUri); ?>, Role: <?php echo htmlspecialchars($currentRole); ?>, InPath: <?php echo $isInAdminPath ? 'Y' : 'N'; ?>, HasRole: <?php echo $hasAdminRole ? 'Y' : 'N'; ?> -->
            <!-- Regular Mobile Menu Toggle -->
            <button id="navToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-700 hover:border-red-600 bg-[#0f0f10]/60 backdrop-blur-sm touch-target" aria-label="Toggle menu" aria-expanded="false">
                <span id="bar1" class="block w-6 h-0.5 bg-gray-200 transition-transform duration-200 ease-out"></span>
                <span id="bar2" class="block w-6 h-0.5 bg-gray-200 mt-1.5 transition-opacity duration-200 ease-out"></span>
                <span id="bar3" class="block w-6 h-0.5 bg-gray-200 mt-1.5 transition-transform duration-200 ease-out"></span>
			</button>
            <?php endif; ?>
			<nav class="hidden md:flex items-center gap-5 text-sm" id="navDesktop">
				<a class="link" href="<?php echo base_url('/'); ?>">Home</a>
				<a class="link" href="<?php echo base_url('/events'); ?>">Events</a>
				<a class="link" href="<?php echo base_url('/travel'); ?>">Travel</a>
				<a class="link" href="<?php echo base_url('/hotels'); ?>">Hotels</a>
				<a class="btn btn-secondary" href="<?php echo base_url('/organizer/register'); ?>">Create event</a>
				<?php if (!isset($_SESSION['user_id'])): ?>
					<a class="btn btn-primary" href="<?php echo base_url('/login'); ?>">Login</a>
				<?php else: ?>
					<a class="link" href="<?php echo base_url('/user/dashboard'); ?>">Dashboard</a>
					<a class="btn btn-primary" href="<?php echo base_url('/logout'); ?>">Logout</a>
				<?php endif; ?>
			</nav>

		</div>
		
		<!-- Mobile Search Bar -->
		<div id="mobileSearchBar" class="md:hidden hidden border-t border-gray-800">
			<div class="max-w-6xl mx-auto px-4 py-3">
				<form action="<?php echo base_url('/search'); ?>" method="GET" class="relative">
					<div class="relative">
						<input type="text" name="q" placeholder="Search events, travel destinations..." 
							   value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
							   class="w-full pl-10 pr-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-base">
						<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
							</svg>
						</div>
						<button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
							<svg class="h-5 w-5 text-gray-400 hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
							</svg>
						</button>
					</div>
				</form>
			</div>
		</div>
		
		<div id="navMobile" class="md:hidden hidden border-t border-gray-800">
			<div class="max-w-6xl mx-auto px-4 py-3 grid gap-3 text-sm">
				<a class="link" href="<?php echo base_url('/'); ?>">Home</a>
				<a class="link" href="<?php echo base_url('/events'); ?>">Events</a>
				<a class="link" href="<?php echo base_url('/travel'); ?>">Travel</a>
				<a class="link" href="<?php echo base_url('/hotels'); ?>">Hotels</a>
				<a class="btn btn-secondary w-full" href="<?php echo base_url('/organizer/register'); ?>">Create event</a>
				<?php if (!isset($_SESSION['user_id'])): ?>
					<a class="btn btn-primary w-full" href="<?php echo base_url('/login'); ?>">Login</a>
				<?php else: ?>
					<a class="btn btn-secondary w-full" href="<?php echo base_url('/user/dashboard'); ?>">Dashboard</a>
					<a class="btn btn-primary w-full" href="<?php echo base_url('/logout'); ?>">Logout</a>
				<?php endif; ?>
			</div>
		</div>
    </header>
    <?php endif; ?>

	<main class="flex-1<?php echo ($isAdminSidebar || $isOrganizerSidebar) ? ' main-with-sidebar' : ''; ?>">
		<?php if ($msg = flash_get('success')): ?>
			<div class="max-w-6xl mx-auto px-4 pt-4">
				<div class="mb-4 alert-success px-4 py-3"><?php echo htmlspecialchars($msg); ?></div>
			</div>
		<?php endif; ?>
		<?php if ($msg = flash_get('error')): ?>
			<div class="max-w-6xl mx-auto px-4 pt-4">
				<div class="mb-4 alert-error px-4 py-3"><?php echo htmlspecialchars($msg); ?></div>
			</div>
		<?php endif; ?>
		<?php include $viewFile; ?>
	</main>
    <?php if ($isAdminSidebar): ?>
    <footer class="w-full px-4 py-3 text-xs text-gray-400 border-t border-gray-800 bg-[#0d0d0d]<?php echo ($isAdminSidebar || $isOrganizerSidebar) ? ' main-with-sidebar' : ''; ?>">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <span>Admin Panel</span>
            <span>Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.2'; ?></span>
        </div>
    </footer>
    <?php endif; ?>
<script>
// Ensure the script runs
console.log('Admin layout script loading...');

document.addEventListener('DOMContentLoaded', function(){
  console.log('DOM loaded, initializing admin mobile sidebar...');
  // Regular mobile menu toggle
  var t = document.getElementById('navToggle');
  if(t) {
  t.addEventListener('click', function(){
    var m=document.getElementById('navMobile');
    if(!m) return; m.classList.toggle('hidden');
    var ex=this.getAttribute('aria-expanded')==='true';
    this.setAttribute('aria-expanded', (!ex).toString());
    // animate burger -> close
    var b1=document.getElementById('bar1');
    var b2=document.getElementById('bar2');
    var b3=document.getElementById('bar3');
    if(!b1||!b2||!b3) return;
    if(ex){
      // to burger
      b1.style.transform='rotate(0) translate(0,0)';
      b2.style.opacity='1';
      b3.style.transform='rotate(0) translate(0,0)';
    } else {
      // to close
      b1.style.transform='rotate(45deg) translate(3px,3px)';
      b2.style.opacity='0';
      b3.style.transform='rotate(-45deg) translate(3px,-3px)';
    }
  });
  }

  // Admin mobile sidebar toggle
  var adminToggle = document.getElementById('adminMobileToggle');
  var mobileSidebar = document.getElementById('mobileSidebar');
  var overlay = document.getElementById('mobileSidebarOverlay');
  var closeButton = document.getElementById('mobileSidebarClose');
  
  console.log('Mobile sidebar elements:', {
    adminToggle: !!adminToggle,
    mobileSidebar: !!mobileSidebar,
    overlay: !!overlay,
    closeButton: !!closeButton
  });
  
  // Force button to be clickable
  if(adminToggle) {
    adminToggle.style.pointerEvents = 'auto';
    adminToggle.style.zIndex = '9999';
    adminToggle.style.position = 'relative';
    console.log('Admin toggle button found and styled');
  }
  
  if(adminToggle && mobileSidebar && overlay) {
    function openMobileSidebar() {
      console.log('Opening mobile sidebar');
      mobileSidebar.classList.add('active');
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    function closeMobileSidebar() {
      console.log('Closing mobile sidebar');
      mobileSidebar.classList.remove('active');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    
    // Add click event listener with debugging
    adminToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Hamburger menu clicked!');
      if(mobileSidebar.classList.contains('active')) {
        closeMobileSidebar();
      } else {
        openMobileSidebar();
      }
    }, { passive: false });
    
    // Also add touchstart for better mobile support
    adminToggle.addEventListener('touchstart', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Hamburger menu touched!');
      if(mobileSidebar.classList.contains('active')) {
        closeMobileSidebar();
      } else {
        openMobileSidebar();
      }
    }, { passive: false });
    
    // Add mousedown as backup
    adminToggle.addEventListener('mousedown', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Hamburger menu mousedown!');
      if(mobileSidebar.classList.contains('active')) {
        closeMobileSidebar();
      } else {
        openMobileSidebar();
      }
    });
    
    overlay.addEventListener('click', closeMobileSidebar);
    
    if(closeButton) {
      closeButton.addEventListener('click', closeMobileSidebar);
    }
    
    // Close sidebar when clicking on a link
    var sidebarLinks = mobileSidebar.querySelectorAll('a');
    sidebarLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        setTimeout(closeMobileSidebar, 150); // Small delay for smooth transition
      });
    });
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
      if(e.key === 'Escape' && mobileSidebar.classList.contains('active')) {
        closeMobileSidebar();
      }
    });
  }

  // Organizer mobile sidebar toggle
  var organizerToggle = document.getElementById('organizerMobileToggle');
  var organizerMobileSidebar = document.getElementById('organizerMobileSidebar');
  var organizerOverlay = document.getElementById('organizerMobileSidebarOverlay');
  var organizerCloseButton = document.getElementById('organizerMobileSidebarClose');
  
  console.log('Organizer mobile sidebar elements:', {
    organizerToggle: !!organizerToggle,
    organizerMobileSidebar: !!organizerMobileSidebar,
    organizerOverlay: !!organizerOverlay,
    organizerCloseButton: !!organizerCloseButton
  });
  
  if(organizerToggle && organizerMobileSidebar && organizerOverlay) {
    function openOrganizerMobileSidebar() {
      console.log('Opening organizer mobile sidebar');
      organizerMobileSidebar.classList.add('active');
      organizerOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    function closeOrganizerMobileSidebar() {
      console.log('Closing organizer mobile sidebar');
      organizerMobileSidebar.classList.remove('active');
      organizerOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    
    // Add click event listener with debugging
    organizerToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('Organizer hamburger menu clicked!');
      if(organizerMobileSidebar.classList.contains('active')) {
        closeOrganizerMobileSidebar();
      } else {
        openOrganizerMobileSidebar();
      }
    });
    
    // Also add touchstart for better mobile support
    organizerToggle.addEventListener('touchstart', function(e) {
      e.preventDefault();
      console.log('Organizer hamburger menu touched!');
      if(organizerMobileSidebar.classList.contains('active')) {
        closeOrganizerMobileSidebar();
      } else {
        openOrganizerMobileSidebar();
      }
    });
    
    organizerOverlay.addEventListener('click', closeOrganizerMobileSidebar);
    
    if(organizerCloseButton) {
      organizerCloseButton.addEventListener('click', closeOrganizerMobileSidebar);
    }
    
    // Close sidebar when clicking on a link
    var organizerSidebarLinks = organizerMobileSidebar.querySelectorAll('a');
    organizerSidebarLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        setTimeout(closeOrganizerMobileSidebar, 150); // Small delay for smooth transition
      });
    });
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
      if(e.key === 'Escape' && organizerMobileSidebar.classList.contains('active')) {
        closeOrganizerMobileSidebar();
      }
    });
  }
  
  // Mobile search toggle
  var mobileSearchToggle = document.getElementById('mobileSearchToggle');
  var mobileSearchBar = document.getElementById('mobileSearchBar');
  var navMobile = document.getElementById('navMobile');
  
  if (mobileSearchToggle && mobileSearchBar) {
    mobileSearchToggle.addEventListener('click', function() {
      if (mobileSearchBar.classList.contains('hidden')) {
        mobileSearchBar.classList.remove('hidden');
        if (navMobile && !navMobile.classList.contains('hidden')) {
          navMobile.classList.add('hidden');
        }
        // Focus on the search input
        setTimeout(function() {
          var searchInput = mobileSearchBar.querySelector('input[name="q"]');
          if (searchInput) {
            searchInput.focus();
          }
        }, 100);
      } else {
        mobileSearchBar.classList.add('hidden');
      }
    });
    
    // Close search when clicking outside
    document.addEventListener('click', function(e) {
      if (mobileSearchBar && !mobileSearchBar.classList.contains('hidden')) {
        if (!mobileSearchBar.contains(e.target) && !mobileSearchToggle.contains(e.target)) {
          mobileSearchBar.classList.add('hidden');
        }
      }
    });
  }
});
</script>

    <?php $isAppSection = $isScanner || $isAdminSidebar || $isOrganizerSidebar || (strpos($_SERVER['REQUEST_URI'] ?? '', '/user/') === 0); ?>
    <?php if (!$isAppSection): ?>
    <footer class="footer">
        <?php 
            $sitePhone = \App\Models\Setting::get('site.phone', '+254 700 000 000');
            $siteEmail = \App\Models\Setting::get('site.email', 'info@example.com');
            $siteAddress = \App\Models\Setting::get('site.address', 'Nairobi, Kenya');
            $siteDesc = \App\Models\Setting::get('site.description', 'Discover and book amazing events.');
            $fb = \App\Models\Setting::get('site.facebook', '');
            $tw = \App\Models\Setting::get('site.twitter', '');
            $ig = \App\Models\Setting::get('site.instagram', '');
        ?>
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <div class="card p-4 flex items-center gap-3"><div class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center">📞</div><div><div class="text-sm text-gray-400">Call us</div><div class="font-semibold"><?php echo htmlspecialchars($sitePhone); ?></div></div></div>
                <div class="card p-4 flex items-center gap-3"><div class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center">✉️</div><div><div class="text-sm text-gray-400">Write to us</div><div class="font-semibold"><?php echo htmlspecialchars($siteEmail); ?></div></div></div>
                <div class="card p-4 flex items-center gap-3"><div class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center">📍</div><div><div class="text-sm text-gray-400">Address</div><div class="font-semibold"><?php echo htmlspecialchars($siteAddress); ?></div></div></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <img src="<?php echo base_url($siteLogo); ?>" alt="logo" class="h-10 w-auto">
                        <?php if (empty($siteLogo)): ?>
                        <div class="text-xl font-semibold"><?php echo htmlspecialchars($siteTitle); ?></div>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed"><?php echo htmlspecialchars($siteDesc); ?></p>
                    <div class="flex items-center gap-3 mt-4">
                        <?php if ($fb): ?><a class="btn btn-secondary btn-sm" href="<?php echo htmlspecialchars($fb); ?>" target="_blank">Facebook</a><?php endif; ?>
                        <?php if ($tw): ?><a class="btn btn-secondary btn-sm" href="<?php echo htmlspecialchars($tw); ?>" target="_blank">Twitter</a><?php endif; ?>
                        <?php if ($ig): ?><a class="btn btn-secondary btn-sm" href="<?php echo htmlspecialchars($ig); ?>" target="_blank">Instagram</a><?php endif; ?>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-400 mb-3">Quick links</div>
                    <ul class="space-y-2 text-sm">
                        <li><a class="link" href="<?php echo base_url('/'); ?>">Home</a></li>
                        <li><a class="link" href="<?php echo base_url('/organizer/register'); ?>">Create event</a></li>
                        <li><a class="link" href="<?php echo base_url('/login'); ?>">User Login</a></li>
                        <li><a class="link" href="<?php echo base_url('/organizer/login'); ?>">Organizer Login</a></li>
                        <li><a class="link" href="<?php echo base_url('/travel/login'); ?>">Travel Portal Login</a></li>
                        <li><a class="link" href="<?php echo base_url('/help'); ?>">Help</a></li>

                    </ul>
                </div>

                <div>
                    <div class="text-sm text-gray-400 mb-3">Subscribe</div>
                    <form class="card p-3 flex items-center gap-2" onsubmit="event.preventDefault(); this.reset(); alert('Thanks for subscribing!');">
                        <input class="input" type="email" placeholder="Email Address" required>
                        <button class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-4 text-sm flex items-center justify-between flex-wrap gap-3">
                <span>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteTitle); ?>. All rights reserved. • Version <?php echo defined('APP_VERSION') ? APP_VERSION : '1.0.2'; ?></span>
                
                <!-- Social Media Links -->
                <?php if (!empty($fb) || !empty($tw) || !empty($ig)): ?>
                <nav class="flex items-center gap-4 text-gray-300">
                    <?php if (!empty($fb)): ?>
                        <a class="link hover:text-blue-400" href="<?php echo htmlspecialchars($fb); ?>" target="_blank" rel="noopener">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($tw)): ?>
                        <a class="link hover:text-blue-400" href="<?php echo htmlspecialchars($tw); ?>" target="_blank" rel="noopener">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($ig)): ?>
                        <a class="link hover:text-pink-400" href="<?php echo htmlspecialchars($ig); ?>" target="_blank" rel="noopener">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297zm7.83-9.281H9.721v6.562h6.558V7.707zm-3.279 5.721c-.718 0-1.297-.579-1.297-1.297s.579-1.297 1.297-1.297 1.297.579 1.297 1.297-.579 1.297-1.297 1.297z"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>
                
                <nav class="flex items-center gap-4 text-gray-300">
                    <a class="link" href="<?php echo base_url('/page?slug=blog'); ?>">Blog</a>
                    <a class="link" href="<?php echo base_url('/page?slug=terms-and-conditions'); ?>">Terms</a>
                    <a class="link" href="<?php echo base_url('/page?slug=privacy-policy'); ?>">Privacy</a>
                    <a class="link" href="<?php echo base_url('/page?slug=refund-policy'); ?>">Refund</a>
                    <a class="link" href="<?php echo base_url('/partners'); ?>">Partners</a>
                </nav>
                <span class="text-gray-500">Made with ❤️ · Developed by <a class="link" target="_blank" href="https://www.motechdigitalaagency.co.ke">Motech Digital Agency</a></span>
            </div>
        </div>
    </footer>
    <?php endif; ?>
</body>
</html>



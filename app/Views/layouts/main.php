<?php /** @var string $viewFile */ ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars($siteTitle ?? 'Ticko'); ?></title>
	<script>
		// Tailwind CDN config: extend with brand colors
		tailwind = window.tailwind || {}; tailwind.config = {
			theme: { extend: { colors: { brand: { red: '#ef4444', red600: '#dc2626' }, dark: { bg: '#0b0b0b', card: '#111111', mute: '#9ca3af' } } } }
		};
	</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php $siteLogo = \App\Models\Setting::get('site.logo', 'logo.png'); $siteFavicon = \App\Models\Setting::get('site.favicon', $siteLogo); $siteTitle = \App\Models\Setting::get('site.name', 'Ticko'); ?>
    <link rel="icon" href="<?php echo base_url($siteFavicon); ?>">
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
    <meta property="og:url" content="<?php echo base_url(ltrim($_SERVER['REQUEST_URI'] ?? '/', '/')); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?php if ($tw): ?><meta name="twitter:site" content="<?php echo htmlspecialchars($tw); ?>"><?php endif; ?>
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
		.table th{ color:#e5e7eb; background:#0f0f10; }
		.table td, .table th{ border-top:1px solid #1f2937; }
		.alert-success{ border:1px solid #14532d; background:#052e16; color:#86efac; border-radius:.5rem; }
		.alert-error{ border:1px solid #7f1d1d; background:#450a0a; color:#fecaca; border-radius:.5rem; }
		.main-with-sidebar{ margin-left:0; }
		@media (min-width: 768px){ .main-with-sidebar{ margin-left:18rem; } }
	</style>
</head>
<body class="min-h-screen flex flex-col">
<?php $isAdminSidebar = (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin') !== false) && (($_SESSION['role'] ?? null) === 'admin'); ?>
<?php $isOrganizerSidebar = (strpos($_SERVER['REQUEST_URI'] ?? '', '/organizer') !== false) && isset($_SESSION['organizer_id']); ?>
<?php if ($isAdminSidebar): ?>
    <aside class="fixed inset-y-0 left-0 w-72 hidden md:block z-40 header/side bg-[#0d0d0d] border-r border-gray-800">
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 flex items-center gap-3 border-b border-gray-800">
                <img src="<?php echo base_url($siteLogo); ?>" class="h-9 w-auto" alt="logo">
                <span class="font-semibold">Admin</span>
            </div>
            <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1 text-sm">
                <a href="<?php echo base_url('/admin'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Dashboard</a>
                <a href="<?php echo base_url('/admin/users'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Users</a>
                <a href="<?php echo base_url('/admin/organizers'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Organizers</a>
                <a href="<?php echo base_url('/admin/events'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Events</a>
                <a href="<?php echo base_url('/admin/banners'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Banners</a>
                <a href="<?php echo base_url('/admin/partners'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Partners</a>
                <a href="<?php echo base_url('/admin/partner-logos'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Partner Logos</a>
                <a href="<?php echo base_url('/admin/pages'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Pages</a>
                <a href="<?php echo base_url('/admin/settings'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Settings</a>
                <a href="<?php echo base_url('/admin/email-templates'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Email Templates</a>
                <a href="<?php echo base_url('/admin/sms-templates'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">SMS Templates</a>
                <a href="<?php echo base_url('/admin/withdrawals'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Withdrawals</a>
                <a href="<?php echo base_url('/admin/profile'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">My Profile</a>
                <a href="<?php echo base_url('/logout'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Logout</a>
            </nav>
        </div>
    </aside>
<?php endif; ?>

<?php if ($isOrganizerSidebar): ?>
    <aside class="fixed inset-y-0 left-0 w-72 hidden md:block z-40 header/side bg-[#0d0d0d] border-r border-gray-800">
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 flex items-center gap-3 border-b border-gray-800">
                <img src="<?php echo base_url($siteLogo); ?>" class="h-9 w-auto" alt="logo">
                <span class="font-semibold">Organizer</span>
            </div>
            <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1 text-sm">
                <a href="<?php echo base_url('/organizer/dashboard'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Dashboard</a>
                <a href="<?php echo base_url('/organizer/events'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">My Events</a>
                <a href="<?php echo base_url('/organizer/events/create'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Create Event</a>
                <a href="<?php echo base_url('/organizer/reports'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Revenue & Reports</a>
                <a href="<?php echo base_url('/organizer/withdrawals'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Withdrawals</a>
                <a href="<?php echo base_url('/organizer/profile'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">My Profile</a>
                <a href="<?php echo base_url('/logout'); ?>" class="block px-3 py-2 rounded hover:bg-gray-800">Logout</a>
            </nav>
        </div>
    </aside>
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
            <button id="navToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-700 hover:border-red-600 bg-[#0f0f10]/60 backdrop-blur-sm" aria-label="Toggle menu" aria-expanded="false">
                <span id="bar1" class="block w-6 h-0.5 bg-gray-200 transition-transform duration-200 ease-out"></span>
                <span id="bar2" class="block w-6 h-0.5 bg-gray-200 mt-1.5 transition-opacity duration-200 ease-out"></span>
                <span id="bar3" class="block w-6 h-0.5 bg-gray-200 mt-1.5 transition-transform duration-200 ease-out"></span>
			</button>
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
<script>
document.addEventListener('DOMContentLoaded', function(){
  var t = document.getElementById('navToggle');
  if(!t) return;
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
                <span>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteTitle); ?>. All rights reserved.</span>
                <nav class="flex items-center gap-4 text-gray-300">
                    <a class="link" href="<?php echo base_url('/page?slug=blog'); ?>">Blog</a>
                    <a class="link" href="<?php echo base_url('/page?slug=terms-and-conditions'); ?>">Terms</a>
                    <a class="link" href="<?php echo base_url('/page?slug=privacy-policy'); ?>">Privacy</a>
                    <a class="link" href="<?php echo base_url('/page?slug=refund-policy'); ?>">Refund</a>
                    <a class="link" href="<?php echo base_url('/partners'); ?>">Partners</a>
                </nav>
                <span class="text-gray-500">Made with ❤️</span>
            </div>
        </div>
    </footer>
    <?php endif; ?>
</body>
</html>



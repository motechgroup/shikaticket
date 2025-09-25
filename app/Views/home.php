<?php /** @var array $featuredEvents */ /** @var array $events */ ?>
<?php /** @var array $banners */ ?>
<?php if (!empty($banners)): ?>
<style>
.no-scrollbar::-webkit-scrollbar{display:none}
.no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
</style>
<div class="max-w-6xl mx-auto px-4 pt-6">
    <div class="relative overflow-hidden rounded border border-gray-800">
        <div id="bannerTrack" class="flex overflow-hidden no-scrollbar snap-x snap-mandatory" style="scroll-behavior:smooth">
            <?php foreach ($banners as $b): ?>
            <a class="min-w-full snap-start block banner-slide" href="<?php echo htmlspecialchars($b['link_url'] ?? '#'); ?>" target="_blank">
                <img src="<?php echo base_url($b['image_path']); ?>" class="w-full h-48 md:h-72 object-cover" alt="banner">
            </a>
            <?php endforeach; ?>
        </div>
    </div>

</div>
<script>
(function(){
  var track=document.getElementById('bannerTrack');
  if(!track) return;
  var index=0; var slides=track.querySelectorAll('.banner-slide');
  function go(i){ if(!slides.length) return; index=i%slides.length; var w=track.clientWidth; track.scrollTo({left:index*w, behavior:'smooth'});} 
  var timer=setInterval(function(){ go(index+1); }, 6000);
  window.addEventListener('resize', function(){ go(index); });
})();
</script>
<?php endif; ?>

<section class="bg-gradient-to-r from-black to-[#1a1a1a] text-white">
    <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-5xl font-bold leading-tight">Discover and book amazing events</h1>
            <p class="mt-3 text-white/90 text-sm md:text-base">Concerts, conferences, sports and more. Secure checkout with M-Pesa, PayPal and Flutterwave.</p>
            <div class="mt-6 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="<?php echo base_url('/organizer/register'); ?>" class="btn btn-primary w-full sm:w-auto">List your event</a>
                <a href="#events" class="btn btn-secondary w-full sm:w-auto">Browse events</a>
            </div>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-10">
    <h2 class="text-xl md:text-2xl font-semibold text-center mb-6">Featured Events</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6 grid-flow-row auto-rows-max">
		<?php if (empty($featuredEvents)): ?>
			<div class="col-span-full text-gray-400">No featured events yet.</div>
		<?php else: ?>
			<?php foreach ($featuredEvents as $event): ?>
                <a href="<?php echo base_url('/events/show?id='.(int)$event['id']); ?>" class="block rounded-lg border border-gray-800 hover:border-red-600 bg-[#0f0f10] focus:outline-none focus:ring-2 focus:ring-red-600 cursor-pointer overflow-hidden">
                    <?php if (!empty($event['poster_path'])): ?>
                        <div class="relative pt-[100%] bg-black">
                            <span class="absolute top-2 left-2 z-10 text-[11px] md:text-xs bg-red-600 text-white rounded px-2 py-1"><?php echo htmlspecialchars($event['event_date'] ?? ''); ?></span>
                            <?php if (!empty($event['category'])): ?><span class="absolute top-2 right-2 z-10 text-[11px] md:text-xs bg-gray-800 text-white rounded px-2 py-1"><?php echo htmlspecialchars(ucfirst($event['category'])); ?></span><?php endif; ?>
                            <img src="<?php echo base_url($event['poster_path']); ?>" alt="Poster" class="absolute inset-0 w-full h-full object-cover z-0">
                        </div>
                    <?php endif; ?>
                    <div class="p-2.5 md:p-3">
                        <h3 class="font-semibold text-sm md:text-base leading-snug line-clamp-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="text-[11px] md:text-xs text-gray-400 mt-1 line-clamp-1"><?php echo htmlspecialchars($event['venue']); ?> • <?php echo htmlspecialchars($event['event_date']); ?></p>
                        <?php $displayPrice = $event['early_bird_price'] ?? $event['regular_price'] ?? $event['price']; ?>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-red-400 font-semibold text-sm md:text-base"><?php echo htmlspecialchars($event['currency']); ?> <?php echo number_format((float)$displayPrice, 2); ?></p>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 8a3 3 0 11-6 0 3 3 0 016 0zM4 20a8 8 0 1116 0"/></svg>
                        </div>
                    </div>
					</a>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</section>

<section id="events" class="max-w-6xl mx-auto px-4 pb-16">
    <h2 class="text-xl md:text-2xl font-semibold text-center mb-6">Available Events</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
		<?php if (empty($events)): ?>
			<div class="col-span-full text-gray-400">No events available.</div>
		<?php else: ?>
			<?php foreach ($events as $event): ?>
                <a href="<?php echo base_url('/events/show?id='.(int)$event['id']); ?>" class="block card card-hover p-3 md:p-4 focus:outline-none focus:ring-2 focus:ring-red-600 cursor-pointer">
						<?php if (!empty($event['poster_path'])): ?>
                        <div class="relative rounded mb-3 h-64 bg-black overflow-hidden">
                            <span class="absolute top-2 left-2 z-10 text-[11px] md:text-xs bg-red-600 text-white rounded px-2 py-1"><?php echo htmlspecialchars($event['event_date'] ?? ''); ?></span>
                            <?php if (!empty($event['category'])): ?><span class="absolute top-2 right-2 z-10 text-[11px] md:text-xs bg-gray-800 text-white rounded px-2 py-1"><?php echo htmlspecialchars(ucfirst($event['category'])); ?></span><?php endif; ?>
                            <img src="<?php echo base_url($event['poster_path']); ?>" alt="Poster" class="absolute inset-0 w-full h-full object-cover z-0">
							</div>
						<?php endif; ?>
                    <h3 class="font-semibold text-base md:text-lg line-clamp-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p class="text-xs md:text-sm text-gray-400 mt-1 line-clamp-1"><?php echo htmlspecialchars($event['venue']); ?> • <?php echo htmlspecialchars($event['event_date']); ?></p>
					<?php $displayPrice = $event['early_bird_price'] ?? $event['regular_price'] ?? $event['price']; ?>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-red-400 font-semibold text-sm md:text-base"><?php echo htmlspecialchars($event['currency']); ?> <?php echo number_format((float)$displayPrice, 2); ?></p>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12v.01M12 12v.01M20 12v.01M4 12a8 8 0 0112.906-6.32M20 12a8 8 0 01-12.906 6.32M12 12a8 8 0 000 0"/></svg>
                    </div>
					</a>
			<?php endforeach; ?>
		<?php endif; ?>
    </div>
</section>

<!-- Travel Destinations Section -->
<?php if (!empty($featuredDestinations)): ?>
<section class="max-w-6xl mx-auto px-4 py-10">
    <h2 class="text-xl md:text-2xl font-semibold text-center mb-6">Travel Destinations</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6 grid-flow-row auto-rows-max">
        <?php foreach ($featuredDestinations as $destination): ?>
        <a href="<?php echo base_url('/travel/destination?id=' . $destination['id']); ?>" class="card card-hover p-3 md:p-4 block">
            <?php if (!empty($destination['image_path'])): ?>
                <div class="relative rounded mb-3 h-48 bg-black overflow-hidden">
                    <span class="absolute top-2 left-2 z-10 text-[11px] md:text-xs bg-blue-600 text-white rounded px-2 py-1"><?php echo htmlspecialchars($destination['departure_date']); ?></span>
                    <span class="absolute top-2 right-2 z-10 text-[11px] md:text-xs bg-gray-800 text-white rounded px-2 py-1"><?php echo $destination['duration_days']; ?> days</span>
                    <img src="<?php echo base_url($destination['image_path']); ?>" alt="Destination" class="absolute inset-0 w-full h-full object-cover z-0">
                </div>
            <?php endif; ?>
            <h3 class="font-semibold text-base md:text-lg line-clamp-2"><?php echo htmlspecialchars($destination['title']); ?></h3>
            <p class="text-xs md:text-sm text-gray-400 mt-1 line-clamp-1"><?php echo htmlspecialchars($destination['destination']); ?> • <?php echo htmlspecialchars($destination['departure_location']); ?></p>
            <div class="flex items-center justify-between mt-2">
                <p class="text-blue-400 font-semibold text-sm md:text-base"><?php echo htmlspecialchars($destination['currency']); ?> <?php echo number_format((float)$destination['price'], 2); ?></p>
                <span class="text-xs text-gray-400">by <?php echo htmlspecialchars($destination['company_name']); ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php $partners=[]; try { $partners = db()->query("SELECT * FROM partner_logos WHERE is_active=1 ORDER BY sort_order ASC, created_at DESC")->fetchAll(); } catch (\PDOException $e) {} ?>
<?php if (!empty($partners)): ?>
<section class="max-w-6xl mx-auto px-4 py-10">
    <h2 class="text-center text-sm uppercase tracking-wide text-gray-400 mb-4">Our Partners</h2>

    <style>
      /* Scroll-based slider (no duplication of logos) */
      .partners-scroll { overflow: hidden; position: relative; }
      .partners-row { display:flex; align-items:center; gap:2.5rem; padding:0.25rem 0; }
      .partners-row.center { justify-content:center; }
      .partners-item { flex:0 0 auto; display:flex; align-items:center; justify-content:center; }
      @media (min-width: 768px){ .partners-row{ gap:3rem; } }
    </style>

    <div class="partners-scroll opacity-90" id="partnersScroll" aria-label="Partner logos carousel" role="group">
      <div class="partners-row" id="partnersRow">
        <?php foreach ($partners as $p): ?>
          <a href="<?php echo htmlspecialchars($p['link_url'] ?? '#'); ?>" target="_blank" class="partners-item">
            <img src="<?php echo base_url($p['image_path']); ?>"
                 alt="<?php echo htmlspecialchars($p['title']); ?>" 
                 class="h-10 md:h-12 object-contain grayscale hover:grayscale-0 transition">
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <script>
    (function(){
      const wrap = document.getElementById('partnersScroll');
      const row  = document.getElementById('partnersRow');
      if(!wrap || !row) return;
      let raf, lastTs = 0; const speed = 40; // px/sec
      let paused = false;
      function step(ts){
        if(!lastTs) lastTs = ts;
        const dt = (ts - lastTs) / 1000;
        lastTs = ts;
        if(!paused){
          wrap.scrollLeft += speed * dt;
          const max = row.scrollWidth - wrap.clientWidth;
          if (wrap.scrollLeft >= max - 2) {
            wrap.scrollLeft = 0; // jump back to start (no duplicate logos shown)
          }
        }
        raf = requestAnimationFrame(step);
      }
      raf = requestAnimationFrame(step);
      // Pause on interaction
      ['mouseenter','focusin','touchstart'].forEach(ev=>wrap.addEventListener(ev, ()=>{ paused = true; }, {passive:true}));
      ['mouseleave','focusout','touchend'].forEach(ev=>wrap.addEventListener(ev, ()=>{ paused = false; }));
      // If content width smaller than container, don't animate
      function check(){
        const tooSmall = row.scrollWidth <= wrap.clientWidth;
        if (tooSmall) {
          paused = true; wrap.scrollLeft = 0; row.classList.add('center');
        } else {
          row.classList.remove('center');
        }
      }
      window.addEventListener('load', check);
      window.addEventListener('resize', check);
    })();
    </script>
</section>
<?php endif; ?>



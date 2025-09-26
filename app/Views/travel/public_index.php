<?php /** @var array $destinations */ /** @var array $featured */ /** @var array $latest */ /** @var array $banners */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
  <!-- Hero Banner -->
  <?php if (!empty($banners)): ?>
  <div class="relative mb-10 overflow-hidden rounded-lg">
    <?php $banner = $banners[0]; // Use first banner as hero ?>
    <div class="relative h-64 md:h-96">
      <img src="<?php echo base_url($banner['image_path']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" class="w-full h-full object-cover">
      <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center">
        <div class="max-w-2xl px-6 text-white">
          <?php if (!empty($banner['subtitle'])): ?>
          <div class="text-lg md:text-xl mb-2 text-gray-200"><?php echo htmlspecialchars($banner['subtitle']); ?></div>
          <?php endif; ?>
          <h1 class="text-3xl md:text-5xl font-bold mb-4"><?php echo htmlspecialchars($banner['title']); ?></h1>
          <?php if (!empty($banner['description'])): ?>
          <p class="text-lg mb-6 text-gray-200"><?php echo htmlspecialchars($banner['description']); ?></p>
          <?php endif; ?>
          <?php if (!empty($banner['button_text']) && !empty($banner['button_url'])): ?>
          <a href="<?php echo htmlspecialchars($banner['button_url']); ?>" class="btn btn-primary text-lg px-8 py-3"><?php echo htmlspecialchars($banner['button_text']); ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php else: ?>
  <h1 class="text-2xl font-semibold mb-6">Explore Travel Destinations</h1>
  <?php endif; ?>

  <form class="card p-4 mb-6" method="get" action="<?php echo base_url('/travel'); ?>">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <input class="input" type="text" name="q" placeholder="Search by destination, title, or agency" value="<?php echo htmlspecialchars($q ?? ''); ?>">
      <input class="input" type="text" name="country" placeholder="Country" value="<?php echo htmlspecialchars($country ?? ''); ?>">
      <input class="input" type="text" name="city" placeholder="City" value="<?php echo htmlspecialchars($city ?? ''); ?>">
      <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="featured" value="1" <?php echo !empty($_GET['featured'])?'checked':''; ?>> Featured only</label>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Search</button></div>
  </form>

  <?php if (!empty($featured)): ?>
  <h2 class="text-xl font-semibold mb-3">Featured</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
    <?php foreach ($featured as $d): ?>
    <a class="block card card-hover p-3 md:p-4" href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>">
      <?php if (!empty($d['image_path'])): ?>
      <div class="relative rounded mb-3 h-64 bg-black overflow-hidden">
        <span class="absolute top-2 left-2 z-10 text-[11px] md:text-xs bg-blue-600 text-white rounded px-2 py-1"><?php echo htmlspecialchars($d['departure_date']); ?></span>
        <span class="absolute top-2 right-2 z-10 text-[11px] md:text-xs bg-gray-800 text-white rounded px-2 py-1"><?php echo (int)($d['duration_days'] ?? 1); ?> days</span>
        <img src="<?php echo base_url($d['image_path']); ?>" alt="Destination" class="absolute inset-0 w-full h-full object-cover z-0">
      </div>
      <?php endif; ?>
      <h3 class="font-semibold text-base md:text-lg line-clamp-2"><?php echo htmlspecialchars($d['title']); ?></h3>
      <p class="text-xs md:text-sm text-gray-400 mt-1 line-clamp-1"><?php echo htmlspecialchars($d['destination']); ?></p>
      <div class="flex items-center justify-between mt-2">
        <p class="text-blue-400 font-semibold text-sm md:text-base"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></p>
        <span class="text-xs text-gray-500">by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-blue-400 hover:text-blue-300"><?php echo htmlspecialchars($d['company_name']); ?></a></span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <h2 class="text-xl font-semibold mb-3">Latest</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
    <?php foreach ($latest as $d): ?>
    <a class="block card card-hover p-3 md:p-4" href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>">
      <?php if (!empty($d['image_path'])): ?>
      <div class="relative rounded mb-3 h-64 bg-black overflow-hidden">
        <span class="absolute top-2 left-2 z-10 text-[11px] md:text-xs bg-blue-600 text-white rounded px-2 py-1"><?php echo htmlspecialchars($d['departure_date']); ?></span>
        <span class="absolute top-2 right-2 z-10 text-[11px] md:text-xs bg-gray-800 text-white rounded px-2 py-1"><?php echo (int)($d['duration_days'] ?? 1); ?> days</span>
        <img src="<?php echo base_url($d['image_path']); ?>" alt="Destination" class="absolute inset-0 w-full h-full object-cover z-0">
      </div>
      <?php endif; ?>
      <h3 class="font-semibold text-base md:text-lg line-clamp-2"><?php echo htmlspecialchars($d['title']); ?></h3>
      <p class="text-xs md:text-sm text-gray-400 mt-1 line-clamp-1"><?php echo htmlspecialchars($d['destination']); ?></p>
      <div class="flex items-center justify-between mt-2">
        <p class="text-blue-400 font-semibold text-sm md:text-base"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></p>
        <span class="text-xs text-gray-500">by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-blue-400 hover:text-blue-300"><?php echo htmlspecialchars($d['company_name']); ?></a></span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <h2 class="text-xl font-semibold mb-3">All results</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
    <?php if (empty($destinations)): ?>
      <div class="col-span-full text-gray-400">No destinations match your filters.</div>
    <?php else: foreach ($destinations as $d): ?>
      <a class="block card card-hover p-3 md:p-4" href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>">
        <?php if (!empty($d['image_path'])): ?>
        <div class="relative rounded mb-3 h-64 bg-black overflow-hidden">
          <span class="absolute top-2 left-2 z-10 text-[11px] md:text-xs bg-blue-600 text-white rounded px-2 py-1"><?php echo htmlspecialchars($d['departure_date']); ?></span>
          <span class="absolute top-2 right-2 z-10 text-[11px] md:text-xs bg-gray-800 text-white rounded px-2 py-1"><?php echo (int)($d['duration_days'] ?? 1); ?> days</span>
          <img src="<?php echo base_url($d['image_path']); ?>" alt="Destination" class="absolute inset-0 w-full h-full object-cover z-0">
        </div>
        <?php endif; ?>
        <div class="font-semibold text-base md:text-lg line-clamp-2"><?php echo htmlspecialchars($d['title']); ?></div>
        <div class="text-xs md:text-sm text-gray-400 mt-1 line-clamp-1"><?php echo htmlspecialchars($d['destination']); ?></div>
        <div class="flex items-center justify-between mt-2">
          <div class="text-blue-400 font-semibold text-sm md:text-base"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></div>
          <div class="text-xs text-gray-500">by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-blue-400 hover:text-blue-300"><?php echo htmlspecialchars($d['company_name']); ?></a></div>
        </div>
      </a>
    <?php endforeach; endif; ?>
  </div>
</div>



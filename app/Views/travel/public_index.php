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
      <input class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" type="text" name="q" placeholder="Search by destination, title, or agency" value="<?php echo htmlspecialchars($q ?? ''); ?>">
      <input class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" type="text" name="country" placeholder="Country" value="<?php echo htmlspecialchars($country ?? ''); ?>">
      <input class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" type="text" name="city" placeholder="City" value="<?php echo htmlspecialchars($city ?? ''); ?>">
      <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="featured" value="1" <?php echo !empty($_GET['featured'])?'checked':''; ?>> Featured only</label>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Search</button></div>
  </form>

  <?php if (!empty($featured)): ?>
  <h2 class="text-xl font-semibold mb-3">Featured</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php foreach ($featured as $d): ?>
    <div class="bg-dark-card rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
      <a href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>" class="block">
        <?php if (!empty($d['image_path'])): ?>
        <div class="relative h-56 overflow-hidden">
          <img src="<?php echo base_url($d['image_path']); ?>" alt="Destination" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
          <div class="absolute top-3 left-3 bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-medium">
            <?php echo htmlspecialchars($d['departure_date']); ?>
          </div>
          <div class="absolute top-3 right-3 bg-gray-800 text-white text-xs px-2 py-1 rounded-full font-medium">
            <?php echo (int)($d['duration_days'] ?? 1); ?> days
          </div>
          <?php if ($d['is_featured']): ?>
          <div class="absolute top-3 left-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium flex items-center gap-1">
            <span>⭐</span> Featured
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="p-4">
          <h3 class="font-bold text-lg text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($d['title']); ?></h3>
          <p class="text-sm text-gray-300 mb-3 line-clamp-1">
            <span class="inline-flex items-center">
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
              </svg>
              <?php echo htmlspecialchars($d['destination']); ?>
            </span>
            <span class="text-gray-300 mx-2">•</span>
            <span class="text-gray-300"><?php echo htmlspecialchars($d['departure_location'] ?? 'Nairobi'); ?></span>
          </p>
          <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-red-400">
              <?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?>
            </div>
            <div class="text-xs text-gray-300">
              by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-red-400 hover:text-red-300 font-medium"><?php echo htmlspecialchars($d['company_name']); ?></a>
            </div>
          </div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <h2 class="text-xl font-semibold mb-3">Latest</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php foreach ($latest as $d): ?>
    <div class="bg-dark-card rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
      <a href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>" class="block">
        <?php if (!empty($d['image_path'])): ?>
        <div class="relative h-56 overflow-hidden">
          <img src="<?php echo base_url($d['image_path']); ?>" alt="Destination" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
          <div class="absolute top-3 left-3 bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-medium">
            <?php echo htmlspecialchars($d['departure_date']); ?>
          </div>
          <div class="absolute top-3 right-3 bg-gray-800 text-white text-xs px-2 py-1 rounded-full font-medium">
            <?php echo (int)($d['duration_days'] ?? 1); ?> days
          </div>
          <?php if ($d['is_featured']): ?>
          <div class="absolute top-3 left-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium flex items-center gap-1">
            <span>⭐</span> Featured
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="p-4">
          <h3 class="font-bold text-lg text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($d['title']); ?></h3>
          <p class="text-sm text-gray-300 mb-3 line-clamp-1">
            <span class="inline-flex items-center">
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
              </svg>
              <?php echo htmlspecialchars($d['destination']); ?>
            </span>
            <span class="text-gray-300 mx-2">•</span>
            <span class="text-gray-300"><?php echo htmlspecialchars($d['departure_location'] ?? 'Nairobi'); ?></span>
          </p>
          <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-red-400">
              <?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?>
            </div>
            <div class="text-xs text-gray-300">
              by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-red-400 hover:text-red-300 font-medium"><?php echo htmlspecialchars($d['company_name']); ?></a>
            </div>
          </div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <h2 class="text-xl font-semibold mb-3">All results</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($destinations)): ?>
      <div class="col-span-full text-gray-300">No destinations match your filters.</div>
    <?php else: foreach ($destinations as $d): ?>
      <div class="bg-dark-card rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <a href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>" class="block">
          <?php if (!empty($d['image_path'])): ?>
          <div class="relative h-56 overflow-hidden">
            <img src="<?php echo base_url($d['image_path']); ?>" alt="Destination" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            <div class="absolute top-3 left-3 bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-medium">
              <?php echo htmlspecialchars($d['departure_date']); ?>
            </div>
            <div class="absolute top-3 right-3 bg-gray-800 text-white text-xs px-2 py-1 rounded-full font-medium">
              <?php echo (int)($d['duration_days'] ?? 1); ?> days
            </div>
          </div>
          <?php endif; ?>
          <div class="p-4">
            <h3 class="font-bold text-lg text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($d['title']); ?></h3>
            <p class="text-sm text-gray-300 mb-3 line-clamp-1">
              <span class="inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
                <?php echo htmlspecialchars($d['destination']); ?>
              </span>
              <span class="text-gray-300 mx-2">•</span>
              <span class="text-gray-300"><?php echo htmlspecialchars($d['departure_location'] ?? 'Nairobi'); ?></span>
            </p>
            <div class="flex items-center justify-between">
              <div class="text-2xl font-bold text-red-400">
                <?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?>
              </div>
              <div class="text-xs text-gray-300">
                by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-red-400 hover:text-red-300 font-medium"><?php echo htmlspecialchars($d['company_name']); ?></a>
              </div>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>



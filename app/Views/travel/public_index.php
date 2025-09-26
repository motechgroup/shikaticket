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
  <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6 mb-8">
    <?php foreach ($featured as $d): ?>
    <a class="card p-3 md:p-4 block" href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>">
      <?php if (!empty($d['image_path'])): ?><img class="w-full h-40 object-cover rounded mb-2" src="<?php echo base_url($d['image_path']); ?>" alt="img"><?php endif; ?>
      <div class="font-semibold"><?php echo htmlspecialchars($d['title']); ?></div>
      <div class="text-gray-400 text-sm"><?php echo htmlspecialchars($d['destination']); ?></div>
      <div class="text-blue-400 mt-1 text-sm"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></div>
      <div class="text-gray-500 text-xs mt-1">by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-blue-400 hover:text-blue-300"><?php echo htmlspecialchars($d['company_name']); ?></a></div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <h2 class="text-xl font-semibold mb-3">Latest</h2>
  <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6 mb-8">
    <?php foreach ($latest as $d): ?>
    <a class="card p-3 md:p-4 block" href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>">
      <?php if (!empty($d['image_path'])): ?><img class="w-full h-40 object-cover rounded mb-2" src="<?php echo base_url($d['image_path']); ?>" alt="img"><?php endif; ?>
      <div class="font-semibold"><?php echo htmlspecialchars($d['title']); ?></div>
      <div class="text-gray-400 text-sm"><?php echo htmlspecialchars($d['destination']); ?></div>
      <div class="text-blue-400 mt-1 text-sm"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></div>
      <div class="text-gray-500 text-xs mt-1">by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-blue-400 hover:text-blue-300"><?php echo htmlspecialchars($d['company_name']); ?></a></div>
    </a>
    <?php endforeach; ?>
  </div>

  <h2 class="text-xl font-semibold mb-3">All results</h2>
  <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6">
    <?php if (empty($destinations)): ?>
      <div class="col-span-full text-gray-400">No destinations match your filters.</div>
    <?php else: foreach ($destinations as $d): ?>
      <a class="card p-3 md:p-4 block" href="<?php echo base_url('/travel/destination?id=' . $d['id']); ?>">
        <?php if (!empty($d['image_path'])): ?><img class="w-full h-40 object-cover rounded mb-2" src="<?php echo base_url($d['image_path']); ?>" alt="img"><?php endif; ?>
        <div class="font-semibold"><?php echo htmlspecialchars($d['title']); ?></div>
        <div class="text-gray-400 text-sm"><?php echo htmlspecialchars($d['destination']); ?></div>
        <div class="text-blue-400 mt-1 text-sm"><?php echo htmlspecialchars($d['currency']); ?> <?php echo number_format((float)$d['price'], 2); ?></div>
        <div class="text-gray-500 text-xs mt-1">by <a href="<?php echo base_url('/travel/agency?id=' . $d['agency_id']); ?>" class="text-blue-400 hover:text-blue-300"><?php echo htmlspecialchars($d['company_name']); ?></a></div>
      </a>
    <?php endforeach; endif; ?>
  </div>
</div>



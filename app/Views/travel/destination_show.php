<?php /** @var array $destination */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
  <!-- Breadcrumb -->
  <nav class="mb-6">
    <a href="<?php echo base_url('/travel'); ?>" class="link">Travel</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-400"><?php echo htmlspecialchars($destination['title']); ?></span>
  </nav>

  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2">
      <!-- Hero Image -->
      <?php if (!empty($destination['image_path'])): ?>
      <div class="mb-6">
        <img src="<?php echo base_url($destination['image_path']); ?>" alt="<?php echo htmlspecialchars($destination['title']); ?>" class="w-full h-64 md:h-96 object-cover rounded-lg">
      </div>
      <?php endif; ?>

      <!-- Gallery -->
      <?php if (!empty($destination['gallery_paths']) && is_array($destination['gallery_paths'])): ?>
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Gallery</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <?php foreach ($destination['gallery_paths'] as $image): ?>
          <img src="<?php echo base_url($image); ?>" alt="Gallery image" class="w-full h-32 object-cover rounded-lg">
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Description -->
      <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">About this destination</h2>
        <div class="prose prose-invert max-w-none">
          <?php echo nl2br(htmlspecialchars($destination['description'])); ?>
        </div>
      </div>

      <!-- Includes -->
      <?php if (!empty($destination['includes']) && is_array($destination['includes'])): ?>
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">What's included</h3>
        <ul class="space-y-2">
          <?php foreach ($destination['includes'] as $item): ?>
          <li class="flex items-center gap-2">
            <span class="text-green-400">✓</span>
            <span><?php echo htmlspecialchars($item); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- Excludes -->
      <?php if (!empty($destination['excludes']) && is_array($destination['excludes'])): ?>
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">What's not included</h3>
        <ul class="space-y-2">
          <?php foreach ($destination['excludes'] as $item): ?>
          <li class="flex items-center gap-2">
            <span class="text-red-400">✗</span>
            <span><?php echo htmlspecialchars($item); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- Requirements -->
      <?php if (!empty($destination['requirements']) && is_array($destination['requirements'])): ?>
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Requirements</h3>
        <ul class="space-y-2">
          <?php foreach ($destination['requirements'] as $req): ?>
          <li class="flex items-center gap-2">
            <span class="text-blue-400">•</span>
            <span><?php echo htmlspecialchars($req); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- Itinerary -->
      <?php if (!empty($destination['itinerary']) && is_array($destination['itinerary'])): ?>
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Itinerary</h3>
        <div class="space-y-4">
          <?php foreach ($destination['itinerary'] as $day): ?>
          <div class="card p-4">
            <div class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($day['title'] ?? 'Day ' . ($day['day'] ?? '')); ?></div>
            <div class="text-gray-300"><?php echo nl2br(htmlspecialchars($day['description'] ?? '')); ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
      <!-- Booking Card -->
      <div class="card p-6 sticky top-4">
        <h3 class="text-xl font-semibold mb-4">Book this trip</h3>
        
        <div class="mb-4">
          <div class="text-3xl font-bold text-red-500"><?php echo htmlspecialchars($destination['currency']); ?> <?php echo number_format((float)$destination['price'], 2); ?></div>
          <div class="text-sm text-gray-400">per person</div>
        </div>

        <div class="space-y-3 mb-6 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-400">Duration:</span>
            <span><?php echo (int)$destination['duration_days']; ?> days</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Departure:</span>
            <span><?php echo date('M j, Y', strtotime($destination['departure_date'])); ?></span>
          </div>
          <?php if (!empty($destination['return_date'])): ?>
          <div class="flex justify-between">
            <span class="text-gray-400">Return:</span>
            <span><?php echo date('M j, Y', strtotime($destination['return_date'])); ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($destination['departure_location'])): ?>
          <div class="flex justify-between">
            <span class="text-gray-400">From:</span>
            <span><?php echo htmlspecialchars($destination['departure_location']); ?></span>
          </div>
          <?php endif; ?>
          <div class="flex justify-between">
            <span class="text-gray-400">Max participants:</span>
            <span><?php echo (int)$destination['max_participants']; ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Min participants:</span>
            <span><?php echo (int)$destination['min_participants']; ?></span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-400">Children allowed:</span>
            <span><?php echo (int)($destination['children_allowed'] ?? 1) ? 'Yes' : 'No'; ?></span>
          </div>
        </div>

        <?php if (!empty($destination['booking_deadline'])): ?>
        <div class="mb-4 p-3 bg-yellow-900/20 border border-yellow-600/30 rounded-lg">
          <div class="text-sm text-yellow-300">Booking deadline: <?php echo date('M j, Y H:i', strtotime($destination['booking_deadline'])); ?></div>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo base_url('/travel/booking'); ?>" class="space-y-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="destination_id" value="<?php echo $destination['id']; ?>">
          
          <div>
            <label class="block text-sm font-medium text-white mb-2">Number of Participants</label>
            <input type="number" name="participants_count" min="<?php echo (int)$destination['min_participants']; ?>" max="<?php echo (int)$destination['max_participants']; ?>" value="<?php echo (int)$destination['min_participants']; ?>" required class="w-full px-3 py-2 border border-gray-600 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" style="color: white !important; background-color: #1f2937 !important;">
            <div class="text-xs text-gray-400 mt-1">
              Min: <?php echo (int)$destination['min_participants']; ?> | Max: <?php echo (int)$destination['max_participants']; ?>
            </div>
          </div>
          
          <?php if (!isset($_SESSION['user_id'])): ?>
          <div class="p-3 bg-yellow-900/20 border border-yellow-600/30 rounded-lg">
            <div class="text-sm text-yellow-300 mb-2">You need to be logged in to book</div>
            <a href="<?php echo base_url('/login'); ?>" class="btn btn-secondary btn-sm">Login</a>
          </div>
          <?php else: ?>
          <button type="submit" class="btn btn-primary w-full">
            Book Now - <?php echo htmlspecialchars($destination['currency']); ?> <?php echo number_format((float)$destination['price'], 2); ?> per person
          </button>
          <?php endif; ?>
        </form>
      </div>

      <!-- Travel Agency Info -->
      <div class="card p-6 mt-6">
        <h3 class="text-lg font-semibold mb-4">Travel Agency</h3>
        <div class="flex items-center gap-3 mb-4">
          <?php if (!empty($destination['logo_path'])): ?>
          <img src="<?php echo base_url($destination['logo_path']); ?>" alt="<?php echo htmlspecialchars($destination['company_name']); ?>" class="w-12 h-12 rounded-lg object-cover">
          <?php endif; ?>
          <div>
            <div class="font-semibold"><a href="<?php echo base_url('/travel/agency?id=' . $destination['agency_id']); ?>" class="text-blue-400 hover:text-blue-300"><?php echo htmlspecialchars($destination['company_name']); ?></a></div>
            <div class="text-sm text-gray-400">Contact: <?php echo htmlspecialchars($destination['contact_person']); ?></div>
          </div>
        </div>
        
        <?php if (!empty($destination['agency_description'])): ?>
        <div class="text-sm text-gray-300 mb-4">
          <?php echo nl2br(htmlspecialchars($destination['agency_description'])); ?>
        </div>
        <?php endif; ?>

        <div class="space-y-2 text-sm">
          <?php if (!empty($destination['phone'])): ?>
          <div class="flex items-center gap-2">
            <span class="text-gray-400">📞</span>
            <span><?php echo htmlspecialchars($destination['phone']); ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($destination['email'])): ?>
          <div class="flex items-center gap-2">
            <span class="text-gray-400">✉️</span>
            <span><?php echo htmlspecialchars($destination['email']); ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($destination['website'])): ?>
          <div class="flex items-center gap-2">
            <span class="text-gray-400">🌐</span>
            <a href="<?php echo htmlspecialchars($destination['website']); ?>" target="_blank" class="link">Visit website</a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

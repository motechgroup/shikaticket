<?php /** @var string $query */ ?>
<?php /** @var array $events */ ?>
<?php /** @var array $destinations */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <a href="<?php echo base_url('/'); ?>" class="link">Home</a>
        <span class="text-gray-400 mx-2">/</span>
        <span class="text-gray-400">Search Results</span>
    </nav>

    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-4">Search Results</h1>
        <?php if (!empty($query)): ?>
            <p class="text-gray-400">Results for "<span class="text-red-400 font-medium"><?php echo htmlspecialchars($query); ?></span>"</p>
        <?php else: ?>
            <p class="text-gray-400">Please enter a search term to find events and travel destinations.</p>
        <?php endif; ?>
    </div>

    <?php if (empty($query)): ?>
        <!-- Empty Search -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">Enter a search term</h3>
            <p class="text-gray-400 mb-4">Search for events, travel destinations, or organizers</p>
            <a href="<?php echo base_url('/'); ?>" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>
        </div>
    <?php elseif (empty($events) && empty($destinations)): ?>
        <!-- No Results -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">No results found</h3>
            <p class="text-gray-400 mb-4">Try searching with different keywords or browse our events and travel destinations</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?php echo base_url('/events'); ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Browse Events
                </a>
                <a href="<?php echo base_url('/travel'); ?>" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Browse Travel
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Search Results -->
        <div class="space-y-12">
            <!-- Events Results -->
            <?php if (!empty($events)): ?>
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <h2 class="text-2xl font-bold text-white">Events</h2>
                    <span class="bg-blue-600 text-white text-sm px-2 py-1 rounded-full"><?php echo count($events); ?></span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($events as $event): ?>
                    <div class="card card-hover p-0 overflow-hidden">
                        <a href="<?php echo base_url('/events/show?id=' . $event['id']); ?>" class="block">
                            <?php if (!empty($event['poster_path'])): ?>
                            <img src="<?php echo base_url($event['poster_path']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="w-full h-48 object-cover">
                            <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <?php endif; ?>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <div class="text-sm text-gray-400 mb-3 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span><?php echo date('M j, Y', strtotime($event['event_date'])); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span><?php echo htmlspecialchars($event['venue']); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span><?php echo htmlspecialchars($event['organizer_name']); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <?php if (!empty($event['category'])): ?>
                                    <span class="badge bg-blue-600 text-white text-xs px-2 py-1 rounded-full"><?php echo htmlspecialchars($event['category']); ?></span>
                                    <?php endif; ?>
                                    <span class="text-red-400 font-medium">View Event</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Travel Destinations Results -->
            <?php if (!empty($destinations)): ?>
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <h2 class="text-2xl font-bold text-white">Travel Destinations</h2>
                    <span class="bg-green-600 text-white text-sm px-2 py-1 rounded-full"><?php echo count($destinations); ?></span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($destinations as $destination): ?>
                    <div class="card card-hover p-0 overflow-hidden">
                        <a href="<?php echo base_url('/travel/destination?id=' . $destination['id']); ?>" class="block">
                            <?php if (!empty($destination['image_path'])): ?>
                            <img src="<?php echo base_url($destination['image_path']); ?>" alt="<?php echo htmlspecialchars($destination['title']); ?>" class="w-full h-48 object-cover">
                            <?php else: ?>
                            <div class="w-full h-48 bg-gradient-to-br from-green-600 to-blue-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <?php endif; ?>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($destination['title']); ?></h3>
                                <div class="text-sm text-gray-400 mb-3 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0114 0z"></path>
                                        </svg>
                                        <span><?php echo (int)$destination['duration_days']; ?> <?php echo (int)$destination['duration_days'] === 1 ? 'Day' : 'Days'; ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span><?php echo htmlspecialchars($destination['destination']); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span><?php echo htmlspecialchars($destination['company_name']); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-green-400 font-medium"><?php echo htmlspecialchars($destination['currency']); ?> <?php echo number_format((float)$destination['price'], 0); ?></span>
                                    <span class="text-red-400 font-medium">View Destination</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

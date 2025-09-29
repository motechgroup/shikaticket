<?php /** @var array $agency */ ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center gap-4">
        <img src="<?php echo base_url($agency['logo_path'] ?? ''); ?>" alt="logo" class="w-16 h-16 rounded bg-gray-800 object-cover">
        <div>
            <h1 class="text-2xl font-semibold"><?php echo htmlspecialchars($agency['company_name']); ?></h1>
            <div class="text-sm text-gray-400 flex items-center gap-3">
                <span>Followers: <?php echo (int)($followersCount ?? 0); ?></span>
                <span>Rating: <?php echo number_format((float)($avgRating ?? 0),1); ?> ★ (<?php echo (int)($ratingsCount ?? 0); ?>)</span>
            </div>
        </div>
        <div class="ml-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="<?php echo base_url(($isFollowing??false)?'/travel/agency/unfollow':'/travel/agency/follow'); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="agency_id" value="<?php echo (int)$agency['id']; ?>">
                    <button class="btn <?php echo ($isFollowing??false)?'btn-secondary':'btn-primary'; ?>"><?php echo ($isFollowing??false)?'Unfollow':'Follow'; ?></button>
                </form>
            <?php else: ?>
                <a class="btn btn-primary" href="<?php echo base_url('/login'); ?>">Login to Follow</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($agency['description'])): ?>
        <div class="card p-4 mt-6 text-gray-300 leading-relaxed"><?php echo nl2br(htmlspecialchars($agency['description'])); ?></div>
    <?php endif; ?>

    <div class="mt-8 flex items-center justify-between">
        <h2 class="font-semibold">Past Destinations</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
        <form method="post" action="<?php echo base_url('/travel/agency/rate'); ?>" class="flex items-center gap-2">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="agency_id" value="<?php echo (int)$agency['id']; ?>">
            <select name="rating" class="w-28 px-3 py-2 border border-gray-600 bg-dark-card text-white rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
                <option value="">Rate ★</option>
                <?php for ($i=5;$i>=1;$i--): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                <?php endfor; ?>
            </select>
            <button class="btn btn-secondary">Submit</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 mt-4">
        <?php foreach (($pastDestinations ?? []) as $d): ?>
        <div class="card overflow-hidden opacity-75 cursor-not-allowed" title="Past Destination - <?php echo htmlspecialchars($d['title']); ?>">
            <?php if (!empty($d['image_path'])): ?>
                <img src="<?php echo base_url($d['image_path']); ?>" class="w-full h-24 object-cover" alt="poster">
            <?php else: ?>
                <div class="w-full h-24 bg-gray-700 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            <?php endif; ?>
            <div class="p-2">
                <div class="font-semibold text-xs truncate"><?php echo htmlspecialchars($d['title']); ?></div>
                <div class="text-xs text-gray-500">Departed: <?php echo date('M j, Y', strtotime($d['departure_date'])); ?></div>
                <div class="text-xs text-red-400 font-medium">Past Destination</div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($pastDestinations)): ?>
            <div class="col-span-full text-center py-8 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-white mb-2">No Past Destinations</h3>
                <p class="text-gray-400">This travel agency hasn't completed any trips yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>



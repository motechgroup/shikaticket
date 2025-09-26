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
            <select name="rating" class="select w-28">
                <option value="">Rate ★</option>
                <?php for ($i=5;$i>=1;$i--): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                <?php endfor; ?>
            </select>
            <button class="btn btn-secondary">Submit</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
        <?php foreach (($pastDestinations ?? []) as $d): ?>
        <a href="<?php echo base_url('/travel/destination?id='.(int)$d['id']); ?>" class="card card-hover overflow-hidden">
            <?php if (!empty($d['image_path'])): ?>
                <img src="<?php echo base_url($d['image_path']); ?>" class="w-full h-44 object-cover" alt="poster">
            <?php endif; ?>
            <div class="p-3">
                <div class="font-semibold truncate"><?php echo htmlspecialchars($d['title']); ?></div>
                <div class="text-xs text-gray-400">Departed: <?php echo htmlspecialchars($d['departure_date']); ?></div>
            </div>
        </a>
        <?php endforeach; ?>
        <?php if (empty($pastDestinations)): ?>
            <div class="text-gray-400">No past destinations yet.</div>
        <?php endif; ?>
    </div>
</div>



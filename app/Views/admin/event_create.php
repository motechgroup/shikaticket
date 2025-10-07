<?php ?>
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Create Event</h1>
    <form method="post" action="<?php echo base_url('/admin/events/save'); ?>" enctype="multipart/form-data" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <!-- Admin-owned event: no organizer selection -->
        <div>
            <label class="block text-sm mb-1">Title</label>
            <input class="input" name="title" required>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Venue</label>
                <input class="input" name="venue">
            </div>
            <div>
                <label class="block text-sm mb-1">Category</label>
                <select class="input" name="category">
                    <?php foreach (($categories ?? []) as $c): ?>
                    <option value="<?php echo htmlspecialchars($c['name']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Date</label>
                <input class="input" type="date" name="event_date">
            </div>
            <div>
                <label class="block text-sm mb-1">Time</label>
                <input class="input" type="time" name="event_time">
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1">Description</label>
            <textarea class="input" name="description" rows="4"></textarea>
        </div>
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Capacity</label>
                <input class="input" type="number" name="capacity" value="0">
            </div>
            <div>
                <label class="block text-sm mb-1">Regular Price</label>
                <input class="input" type="number" step="0.01" name="regular_price">
            </div>
            <div>
                <label class="block text-sm mb-1">Currency</label>
                <input class="input" name="currency" value="KES">
            </div>
        </div>
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Early Bird Price</label>
                <input class="input" type="number" step="0.01" name="early_bird_price">
            </div>
            <div>
                <label class="block text-sm mb-1">Early Bird Until</label>
                <input class="input" type="date" name="early_bird_until">
            </div>
            <div>
                <label class="block text-sm mb-1">VIP Price</label>
                <input class="input" type="number" step="0.01" name="vip_price">
            </div>
        </div>
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">VVIP Price</label>
                <input class="input" type="number" step="0.01" name="vvip_price">
            </div>
            <div>
                <label class="block text-sm mb-1">Group Price</label>
                <input class="input" type="number" step="0.01" name="group_price">
            </div>
            <div>
                <label class="block text-sm mb-1">Group Size</label>
                <input class="input" type="number" name="group_size">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Dress Code</label>
                <input class="input" name="dress_code" placeholder="e.g., All white, Black tie">
            </div>
            <div>
                <label class="block text-sm mb-1">Performers / Host</label>
                <input class="input" name="lineup" placeholder="e.g., DJ X, MC Y">
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1">Poster (1080x1080)</label>
            <input type="file" name="poster" accept="image/*">
        </div>
        <div class="flex gap-2">
            <button class="btn btn-primary">Save</button>
            <a class="btn btn-secondary" href="<?php echo base_url('/admin/events'); ?>">Cancel</a>
        </div>
    </form>
</div>



<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Create Travel Destination</h1>
    <form method="post" action="<?php echo base_url('/admin/travel/destinations/save'); ?>" enctype="multipart/form-data" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <!-- Admin-owned destination: not tied to any single agency -->
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Title</label>
                <input class="input" name="title" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Destination</label>
                <input class="input" name="destination" required>
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1">Description</label>
            <textarea class="input" name="description" rows="4"></textarea>
        </div>
        <div class="grid sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm mb-1">Duration (days)</label>
                <input class="input" type="number" name="duration_days" value="1" min="1">
            </div>
            <div>
                <label class="block text-sm mb-1">Price</label>
                <input class="input" type="number" step="0.01" name="price" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Currency</label>
                <input class="input" name="currency" value="KES">
            </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="children_allowed"> Children Allowed</label>
            </div>
        </div>
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Max Participants</label>
                <input class="input" type="number" name="max_participants" value="50">
            </div>
            <div>
                <label class="block text-sm mb-1">Min Participants</label>
                <input class="input" type="number" name="min_participants" value="1">
            </div>
            <div>
                <label class="block text-sm mb-1">Departure Location</label>
                <input class="input" name="departure_location">
            </div>
        </div>
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Departure Date</label>
                <input class="input" type="date" name="departure_date" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Return Date</label>
                <input class="input" type="date" name="return_date">
            </div>
            <div>
                <label class="block text-sm mb-1">Booking Deadline</label>
                <input class="input" type="date" name="booking_deadline">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Includes (one per line)</label>
                <textarea class="input" rows="4" name="includes"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Excludes (one per line)</label>
                <textarea class="input" rows="4" name="excludes"></textarea>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Requirements (one per line)</label>
                <textarea class="input" rows="4" name="requirements"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Itinerary (one per line)</label>
                <textarea class="input" rows="4" name="itinerary"></textarea>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Main Image</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <div>
                <label class="block text-sm mb-1">Gallery Images</label>
                <input type="file" name="gallery[]" accept="image/*" multiple>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_featured"> Featured</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_published"> Published</label>
        </div>
        <div class="flex gap-2">
            <button class="btn btn-primary">Save</button>
            <a class="btn btn-secondary" href="<?php echo base_url('/admin/travel/destinations'); ?>">Cancel</a>
        </div>
    </form>
</div>



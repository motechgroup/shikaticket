<?php /** @var array $destination */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Edit Destination</h1>
        <a class="btn btn-secondary" href="<?php echo base_url('/admin/travel/destinations'); ?>">Back</a>
    </div>
    <div class="card p-6">
        <form method="post" action="<?php echo base_url('/admin/travel/destinations/update'); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$destination['id']; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Title</label>
                    <input class="input" type="text" name="title" value="<?php echo htmlspecialchars($destination['title']); ?>" required>
                </div>
                <div>
                    <label class="label">Destination</label>
                    <input class="input" type="text" name="destination" value="<?php echo htmlspecialchars($destination['destination']); ?>" required>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Description</label>
                    <textarea class="textarea" name="description" rows="4"><?php echo htmlspecialchars($destination['description']); ?></textarea>
                </div>
                <div>
                    <label class="label">Duration (days)</label>
                    <input class="input" type="number" name="duration_days" value="<?php echo (int)$destination['duration_days']; ?>" min="1">
                </div>
                <div>
                    <label class="label">Price</label>
                    <input class="input" type="number" step="0.01" name="price" value="<?php echo (float)$destination['price']; ?>">
                </div>
                <div>
                    <label class="label">Currency</label>
                    <input class="input" type="text" name="currency" value="<?php echo htmlspecialchars($destination['currency']); ?>">
                </div>
                <div>
                    <label class="label">Max Participants</label>
                    <input class="input" type="number" name="max_participants" value="<?php echo (int)$destination['max_participants']; ?>">
                </div>
                <div>
                    <label class="label">Min Participants</label>
                    <input class="input" type="number" name="min_participants" value="<?php echo (int)$destination['min_participants']; ?>">
                </div>
                <div>
                    <label class="label">Departure Location</label>
                    <input class="input" type="text" name="departure_location" value="<?php echo htmlspecialchars($destination['departure_location']); ?>">
                </div>
                <div>
                    <label class="label">Departure Date</label>
                    <input class="input" type="date" name="departure_date" value="<?php echo htmlspecialchars($destination['departure_date']); ?>">
                </div>
                <div>
                    <label class="label">Return Date</label>
                    <input class="input" type="date" name="return_date" value="<?php echo htmlspecialchars($destination['return_date']); ?>">
                </div>
                <div>
                    <label class="label">Booking Deadline</label>
                    <input class="input" type="date" name="booking_deadline" value="<?php echo htmlspecialchars($destination['booking_deadline']); ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="label">Includes (one per line)</label>
                    <textarea class="textarea" name="includes" rows="3"><?php echo htmlspecialchars($destination['includes_text']); ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Excludes (one per line)</label>
                    <textarea class="textarea" name="excludes" rows="3"><?php echo htmlspecialchars($destination['excludes_text']); ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Requirements (one per line)</label>
                    <textarea class="textarea" name="requirements" rows="3"><?php echo htmlspecialchars($destination['requirements_text']); ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Itinerary (one per line)</label>
                    <textarea class="textarea" name="itinerary" rows="4"><?php echo htmlspecialchars($destination['itinerary_text']); ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Main Image</label>
                    <div class="flex items-center gap-4">
                        <?php if (!empty($destination['image_path'])): ?>
                            <img src="<?php echo base_url($destination['image_path']); ?>" class="h-16 w-16 object-cover rounded" alt="Current image">
                        <?php endif; ?>
                        <input class="input" type="file" name="image" accept="image/*">
                    </div>
                </div>
                <div class="flex items-center gap-6 md:col-span-2">
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_published" <?php echo (int)$destination['is_published'] ? 'checked' : ''; ?>> <span>Published</span></label>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_featured" <?php echo (int)$destination['is_featured'] ? 'checked' : ''; ?>> <span>Featured</span></label>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="children_allowed" <?php echo (int)$destination['children_allowed'] ? 'checked' : ''; ?>> <span>Children Allowed</span></label>
                </div>
            </div>
            <div class="mt-6">
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>



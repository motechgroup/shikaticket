<?php /** @var array|null $dest */ ?>
<?php $pageTitle = isset($dest) ? 'Edit Destination' : 'Create Destination'; ?>
<div class="max-w-4xl mx-auto px-4 md:px-6 py-6 md:py-10">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
    <h1 class="text-xl md:text-2xl font-semibold text-white"><?php echo htmlspecialchars($pageTitle); ?></h1>
    <a class="btn btn-secondary w-full sm:w-auto" href="<?php echo base_url('/travel/destinations'); ?>">Back</a>
  </div>

  <div class="card p-6">
    <form method="post" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php if (isset($dest['id'])): ?><input type="hidden" name="id" value="<?php echo (int)$dest['id']; ?>"><?php endif; ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm text-gray-400">Title *</label>
          <input type="text" name="title" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['title'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Destination *</label>
          <input type="text" name="destination" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['destination'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Duration (days)</label>
          <input type="number" name="duration_days" min="1" value="<?php echo htmlspecialchars($dest['duration_days'] ?? '1'); ?>" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Price *</label>
          <input type="number" step="0.01" name="price" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['price'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Currency</label>
          <input type="text" name="currency" value="<?php echo htmlspecialchars($dest['currency'] ?? 'KES'); ?>" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Departure Location *</label>
          <input type="text" name="departure_location" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['departure_location'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Departure Date *</label>
          <input type="date" name="departure_date" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" required value="<?php echo htmlspecialchars($dest['departure_date'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Return Date</label>
          <input type="date" name="return_date" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($dest['return_date'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Booking Deadline</label>
          <input type="date" name="booking_deadline" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" value="<?php echo htmlspecialchars($dest['booking_deadline'] ?? ''); ?>">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Max Participants</label>
          <input type="number" name="max_participants" value="50" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="block text-sm text-gray-400">Min Participants</label>
          <input type="number" name="min_participants" value="1" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-400">Image</label>
          <input type="file" name="image" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-400">Gallery (you can select multiple images)</label>
          <input type="file" name="gallery[]" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500" multiple>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-400">Description</label>
          <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo htmlspecialchars($dest['description'] ?? ''); ?></textarea>
        </div>
        <div>
          <label class="block text-sm text-gray-400">Includes (one per line)</label>
          <textarea name="includes" rows="3" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo isset($dest['includes']) ? htmlspecialchars(implode("\n", json_decode($dest['includes'] ?: '[]', true) ?: [])) : ''; ?></textarea>
        </div>
        <div>
          <label class="block text-sm text-gray-400">Excludes (one per line)</label>
          <textarea name="excludes" rows="3" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo isset($dest['excludes']) ? htmlspecialchars(implode("\n", json_decode($dest['excludes'] ?: '[]', true) ?: [])) : ''; ?></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-400">Requirements/Notes (one per line)</label>
          <textarea name="requirements" rows="3" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo isset($dest['requirements']) ? htmlspecialchars(implode("\n", json_decode($dest['requirements'] ?: '[]', true) ?: [])) : ''; ?></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-400">Itinerary (one per line)</label>
          <textarea name="itinerary" rows="4" class="w-full px-3 py-2 border border-gray-600 bg-dark-card text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"><?php echo isset($dest['itinerary']) ? htmlspecialchars(implode("\n", json_decode($dest['itinerary'] ?: '[]', true) ?: [])) : ''; ?></textarea>
        </div>
        <div class="md:col-span-2 flex items-center gap-6">
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" class="checkbox" <?php echo !empty($dest['is_featured'])?'checked':''; ?>> Featured</label>
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" class="checkbox" <?php echo isset($dest['is_published']) ? ((int)$dest['is_published']? 'checked':'') : 'checked'; ?>> Published</label>
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="children_allowed" class="checkbox" <?php echo !isset($dest) || !empty($dest['children_allowed']) ? 'checked' : ''; ?>> Children Allowed</label>
        </div>
      </div>
      <div class="mt-6">
        <button class="btn btn-primary">Create Destination</button>
      </div>
    </form>
  </div>
</div>



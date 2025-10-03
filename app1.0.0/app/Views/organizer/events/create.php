
<div class="max-w-3xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Create Event</h1>
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('/organizer/events'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<div class="grid sm:grid-cols-2 gap-4">
			<div>
				<label class="block text-sm mb-1">Title</label>
				<input name="title" required class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Category</label>
				<select name="category" class="select">
					<option value="music">Music</option>
					<option value="sports">Sports</option>
					<option value="movie">Movie</option>
					<option value="fashion">Fashion</option>
					<option value="awards">Awards</option>
					<option value="corporate">Corporate</option>
					<option value="virtual">Virtual</option>
					<option value="cultural">Cultural</option>
					<option value="other">Other</option>
				</select>
			</div>
		</div>
		<div>
			<label class="block text-sm mb-1">Description</label>
			<textarea name="description" rows="4" class="textarea"></textarea>
		</div>
		<div>
			<label class="block text-sm mb-1">Poster (1080x1080)</label>
			<input type="file" name="poster" accept="image/*" class="input">
		</div>
		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">Date</label>
				<input type="date" name="date" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Time</label>
				<input type="time" name="time" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Venue</label>
				<input name="venue" class="input">
			</div>
		</div>
		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">Capacity</label>
				<input type="number" name="capacity" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Regular Price</label>
				<input type="number" step="0.01" name="regular_price" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Currency</label>
				<input name="currency" value="KES" class="input">
			</div>
		</div>

		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">Early Bird Price (optional)</label>
				<input type="number" step="0.01" name="early_bird_price" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Early Bird Until</label>
				<input type="date" name="early_bird_until" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">VIP Price</label>
				<input type="number" step="0.01" name="vip_price" class="input">
			</div>
		</div>

		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">VVIP Price</label>
				<input type="number" step="0.01" name="vvip_price" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Group Price</label>
				<input type="number" step="0.01" name="group_price" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Group Size</label>
				<input type="number" name="group_size" class="input">
			</div>
		</div>

		<div class="grid sm:grid-cols-2 gap-4">
			<div>
				<label class="block text-sm mb-1">Dress Code (optional)</label>
				<input name="dress_code" class="input" placeholder="e.g., All white, Black tie">
			</div>
			<div>
				<label class="block text-sm mb-1">Performances / Host / Speaker (optional)</label>
				<input name="lineup" class="input" placeholder="e.g., DJ X, MC Y, Speaker Z">
			</div>
		</div>
		<button class="btn btn-primary">Save</button>
	</form>
</div>



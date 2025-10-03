<?php /** @var array $event */ ?>
<div class="max-w-3xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Edit Event</h1>
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('/organizer/events/update'); ?>" class="space-y-4 card p-6">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="id" value="<?php echo (int)$event['id']; ?>">
		<div class="grid sm:grid-cols-2 gap-4">
			<div>
				<label class="block text-sm mb-1">Title</label>
				<input name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Category</label>
				<select name="category" class="select">
					<?php $cats=['music','sports','movie','fashion','awards','corporate','virtual','cultural','other']; $cur=$event['category'] ?? ''; foreach($cats as $c): ?>
					<option value="<?php echo $c; ?>" <?php echo ($cur===$c)?'selected':''; ?>><?php echo ucfirst($c); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div>
			<label class="block text-sm mb-1">Description</label>
			<textarea name="description" rows="4" class="textarea"><?php echo htmlspecialchars($event['description']); ?></textarea>
		</div>
		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">Date</label>
				<input type="date" name="date" value="<?php echo htmlspecialchars($event['event_date']); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Time</label>
				<input type="time" name="time" value="<?php echo htmlspecialchars($event['event_time']); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Venue</label>
				<input name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" class="input">
			</div>
		</div>
		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">Capacity</label>
				<input type="number" name="capacity" value="<?php echo (int)$event['capacity']; ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Regular Price</label>
				<input type="number" step="0.01" name="regular_price" value="<?php echo htmlspecialchars($event['regular_price'] ?? ''); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Currency</label>
				<input name="currency" value="<?php echo htmlspecialchars($event['currency']); ?>" class="input">
			</div>
		</div>

		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">Early Bird Price</label>
				<input type="number" step="0.01" name="early_bird_price" value="<?php echo htmlspecialchars($event['early_bird_price'] ?? ''); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Early Bird Until</label>
				<input type="date" name="early_bird_until" value="<?php echo htmlspecialchars($event['early_bird_until'] ?? ''); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">VIP Price</label>
				<input type="number" step="0.01" name="vip_price" value="<?php echo htmlspecialchars($event['vip_price'] ?? ''); ?>" class="input">
			</div>
		</div>

		<div class="grid sm:grid-cols-3 gap-4">
			<div>
				<label class="block text-sm mb-1">VVIP Price</label>
				<input type="number" step="0.01" name="vvip_price" value="<?php echo htmlspecialchars($event['vvip_price'] ?? ''); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Group Price</label>
				<input type="number" step="0.01" name="group_price" value="<?php echo htmlspecialchars($event['group_price'] ?? ''); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Group Size</label>
				<input type="number" name="group_size" value="<?php echo htmlspecialchars($event['group_size'] ?? ''); ?>" class="input">
			</div>
		</div>

		<div class="grid sm:grid-cols-2 gap-4">
			<div>
				<label class="block text-sm mb-1">Dress Code (optional)</label>
				<input name="dress_code" value="<?php echo htmlspecialchars($event['dress_code'] ?? ''); ?>" class="input">
			</div>
			<div>
				<label class="block text-sm mb-1">Performances / Host / Speaker (optional)</label>
				<input name="lineup" value="<?php echo htmlspecialchars($event['lineup'] ?? ''); ?>" class="input">
			</div>
		</div>
		<button class="btn btn-primary">Update</button>
	</form>
</div>



<?php /** @var array $event */ /** @var array $settings */ /** @var string $userPhone */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="grid md:grid-cols-3 gap-8">
		<div class="md:col-span-1">
			<div class="card overflow-hidden">
				<?php if (!empty($event['poster_path'])): ?>
					<img src="<?php echo base_url($event['poster_path']); ?>" alt="Poster" class="w-full max-h-[28rem] object-contain bg-black">
				<?php else: ?>
					<div class="p-6 text-gray-400">No poster</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="md:col-span-2">
			<h1 class="text-3xl font-semibold"><?php echo htmlspecialchars($event['title']); ?></h1>
			<p class="text-gray-400 mt-1"><?php echo htmlspecialchars($event['venue']); ?> • <?php echo htmlspecialchars(($event['event_date'] ?? '') . ' ' . ($event['event_time'] ?? '')); ?></p>
			<div class="card p-4 mt-4">
				<h2 class="font-semibold mb-2">About</h2>
				<p class="text-gray-300 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($event['description'] ?? '')); ?></p>
				<?php if (!empty($event['dress_code'])): ?>
					<p class="mt-3"><span class="badge">Dress code</span> <?php echo htmlspecialchars($event['dress_code']); ?></p>
				<?php endif; ?>
				<?php if (!empty($event['lineup'])): ?>
					<p class="mt-2"><span class="badge">Lineup</span> <?php echo htmlspecialchars($event['lineup']); ?></p>
				<?php endif; ?>
			</div>
			<div class="card p-4 mt-4">
				<h2 class="font-semibold mb-2">Tickets</h2>
				<form method="post" action="<?php echo base_url('/orders'); ?>" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
					<?php
					$tiers = [
						['name' => 'Early Bird', 'price' => $event['early_bird_price'] ?? null, 'subtitle' => $event['early_bird_until'] ? ('Until ' . $event['early_bird_until']) : ''],
						['name' => 'Regular', 'price' => $event['regular_price'] ?? null, 'subtitle' => ''],
						['name' => 'VIP', 'price' => $event['vip_price'] ?? null, 'subtitle' => ''],
						['name' => 'VVIP', 'price' => $event['vvip_price'] ?? null, 'subtitle' => ''],
						['name' => 'Group', 'price' => $event['group_price'] ?? null, 'subtitle' => $event['group_size'] ? ($event['group_size'] . ' people') : ''],
					];
					foreach ($tiers as $key => $t) {
						if ($t['price'] === null || $t['price'] === '') continue;
						$val = strtolower(str_replace(' ', '_', $t['name']));
						echo '<label class="card p-4 cursor-pointer">';
						echo '<div class="flex items-center justify-between">';
						echo '<div><div class="font-semibold">' . htmlspecialchars($t['name']) . '</div>';
						echo '<div class="text-red-400 font-bold mt-1">' . htmlspecialchars($event['currency']) . ' ' . number_format((float)$t['price'], 2) . '</div>';
						if (!empty($t['subtitle'])) echo '<div class="text-xs text-gray-400 mt-1">' . htmlspecialchars($t['subtitle']) . '</div>';
						echo '</div>';
						echo '<input type="radio" name="tier" value="' . $val . '" class="ml-4">';
						echo '</div>';
						echo '</label>';
					}
					?>
						<div class="sm:col-span-2 lg:col-span-3 grid sm:grid-cols-4 gap-4 items-end mt-2">
						<div>
							<label class="block text-sm mb-1">Quantity</label>
							<input type="number" name="quantity" value="1" min="1" class="input">
						</div>
							<div>
								<label class="block text-sm mb-1">Phone (for M-Pesa)</label>
								<input type="tel" name="msisdn" value="<?php echo htmlspecialchars($userPhone); ?>" class="input" placeholder="2547XXXXXXXX">
							</div>
						<div>
							<label class="block text-sm mb-1">Pay With</label>
							<?php $available = [];
								if (!empty($settings['mpesa'])) { $available[] = 'mpesa'; }
								if (!empty($settings['paypal'])) { $available[] = 'paypal'; }
								if (!empty($settings['flutterwave'])) { $available[] = 'flutterwave'; }
								$hasGateways = count($available) > 0;
							?>
							<select name="gateway" class="select" <?php echo $hasGateways ? '' : 'disabled'; ?>>
								<?php if ($hasGateways): ?>
									<?php foreach ($available as $idx => $g): ?>
										<option value="<?php echo $g; ?>" <?php echo $idx === 0 ? 'selected' : ''; ?>><?php echo ucfirst($g); ?></option>
									<?php endforeach; ?>
								<?php else: ?>
									<option value="" disabled selected>No payment methods available</option>
								<?php endif; ?>
							</select>
						</div>
							<div>
								<button class="btn btn-primary w-full" <?php echo $hasGateways ? '' : 'disabled title="Enable gateways in Admin > Settings"'; ?>>Proceed to Pay</button>
							</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



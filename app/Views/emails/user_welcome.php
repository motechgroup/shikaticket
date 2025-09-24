<?php /** @var string $userPhone */ /** @var string $userEmail */ ?>
<h1 style="margin:0 0 8px 0;color:#e5e7eb;font-size:20px">Welcome to Ticko!</h1>
<p style="margin:0 0 12px 0;color:#d1d5db">Your account has been created successfully.</p>
<ul style="margin:0 0 12px 20px;color:#d1d5db">
	<li>Email: <?php echo htmlspecialchars($userEmail ?? ''); ?></li>
	<li>Phone: <?php echo htmlspecialchars($userPhone ?? ''); ?></li>
</ul>
<p style="margin:0;color:#d1d5db">You can now browse events and buy tickets.</p>



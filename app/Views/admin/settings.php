<?php /** @var array $settings */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Site Settings</h1>
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('/admin/settings'); ?>" class="space-y-6 card p-6">
		<?php echo csrf_field(); ?>

        <!-- Tabs -->
        <style>
            .tab-btn{ padding:.5rem .75rem; border-radius:.5rem; }
            .tab-btn.active{ background:#1f2937; color:#e5e7eb; }
        </style>
        <div class="border-b border-gray-800 pb-3 mb-3">
            <nav id="settingsTabs" class="flex flex-wrap gap-2 text-sm">
                <button type="button" class="tab-btn active" data-tab="general">General</button>
                <button type="button" class="tab-btn" data-tab="seo">SEO</button>
                <button type="button" class="tab-btn" data-tab="email">Email (SMTP)</button>
                <button type="button" class="tab-btn" data-tab="sms">SMS</button>
                <button type="button" class="tab-btn" data-tab="payments">Payments</button>
            </nav>
        </div>
        <div data-tab-panel="general">
            <label class="block text-sm mb-1">Site Name</label>
            <input name="site_name" value="<?php echo htmlspecialchars($settings['site.name'] ?? 'Ticko'); ?>" class="input">
        </div>
        <div data-tab-panel="general">
            <label class="block text-sm mb-1">Site Description</label>
            <textarea name="site_description" class="textarea" rows="2"><?php echo htmlspecialchars($settings['site.description'] ?? ''); ?></textarea>
        </div>
        <div class="grid sm:grid-cols-2 gap-4" data-tab-panel="general">
            <div>
                <label class="block text-sm mb-1">Logo</label>
                <input type="file" name="site_logo" accept="image/*" class="input">
                <div class="text-xs text-gray-400 mt-1">PNG/SVG preferred. Displays in header/footer.</div>
            </div>
            <div>
                <label class="block text-sm mb-1">Favicon</label>
                <input type="file" name="site_favicon" accept="image/*" class="input">
                <div class="text-xs text-gray-400 mt-1">Square image, e.g., 64x64.</div>
            </div>
        </div>

        <!-- SEO SECTION -->
        <div class="border-t pt-6" data-tab-panel="seo" style="display:none">
            <h2 class="font-semibold mb-3">SEO</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-1">Default Meta Title</label>
                    <input class="input" name="seo_meta_title" value="<?php echo htmlspecialchars($settings['seo.meta_title'] ?? ($settings['site.name'] ?? '')); ?>">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-1">Default Meta Description</label>
                    <textarea class="textarea" name="seo_meta_description" rows="2"><?php echo htmlspecialchars($settings['seo.meta_description'] ?? ($settings['site.description'] ?? '')); ?></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-1">Default Meta Keywords (comma-separated)</label>
                    <input class="input" name="seo_meta_keywords" value="<?php echo htmlspecialchars($settings['seo.meta_keywords'] ?? 'events,tickets,concerts'); ?>">
                </div>
                <div>
                    <label class="block text-sm mb-1">Robots</label>
                    <select class="select" name="seo_meta_robots">
                        <?php $robots = $settings['seo.meta_robots'] ?? 'index,follow'; ?>
                        <option value="index,follow" <?php echo $robots==='index,follow'?'selected':''; ?>>index,follow</option>
                        <option value="noindex,follow" <?php echo $robots==='noindex,follow'?'selected':''; ?>>noindex,follow</option>
                        <option value="index,nofollow" <?php echo $robots==='index,nofollow'?'selected':''; ?>>index,nofollow</option>
                        <option value="noindex,nofollow" <?php echo $robots==='noindex,nofollow'?'selected':''; ?>>noindex,nofollow</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Twitter Handle</label>
                    <input class="input" name="seo_twitter" placeholder="@brand" value="<?php echo htmlspecialchars($settings['seo.twitter'] ?? ''); ?>">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-1">Default OpenGraph Image</label>
                    <input type="file" name="seo_og_image" accept="image/*" class="input">
                    <div class="text-xs text-gray-400 mt-1">Recommended 1200x630</div>
                </div>
            </div>
        </div>

        <!-- SMTP EMAIL SECTION -->
        <div class="border-t pt-6" data-tab-panel="email" style="display:none">
            <h2 class="font-semibold mb-3">SMTP (Email)</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <input class="input" name="smtp_host" placeholder="SMTP Host" value="<?php echo htmlspecialchars($settings['smtp.host'] ?? ''); ?>">
                <input class="input" name="smtp_port" placeholder="Port" value="<?php echo htmlspecialchars($settings['smtp.port'] ?? '587'); ?>">
                <input class="input" name="smtp_username" placeholder="Username" value="<?php echo htmlspecialchars($settings['smtp.username'] ?? ''); ?>">
                <input class="input" type="password" name="smtp_password" placeholder="Password" value="<?php echo htmlspecialchars($settings['smtp.password'] ?? ''); ?>">
                <select class="select" name="smtp_encryption">
                    <option value="tls" <?php echo (($settings['smtp.encryption'] ?? 'tls') === 'tls') ? 'selected' : ''; ?>>TLS</option>
                    <option value="ssl" <?php echo (($settings['smtp.encryption'] ?? 'tls') === 'ssl') ? 'selected' : ''; ?>>SSL</option>
                    <option value="none" <?php echo (($settings['smtp.encryption'] ?? 'tls') === 'none') ? 'selected' : ''; ?>>None</option>
                </select>
                <input class="input" name="smtp_from_email" placeholder="From Email" value="<?php echo htmlspecialchars($settings['smtp.from_email'] ?? ''); ?>">
                <input class="input" name="smtp_from_name" placeholder="From Name" value="<?php echo htmlspecialchars($settings['smtp.from_name'] ?? 'Ticko'); ?>">
            </div>
            <div class="mt-6 flex items-center gap-3">
                <a class="btn btn-secondary" href="<?php echo base_url('/admin/email-templates'); ?>">Manage Email Templates</a>
                <a class="btn btn-primary" href="#test-email-box" onclick="document.getElementById('test-email-box').classList.toggle('hidden');return false;">Send Test Email</a>
            </div>
        </div>

        <!-- Twilio SMS SECTION -->
        <div class="border-t pt-6" data-tab-panel="sms" style="display:none">
            <h2 class="font-semibold mb-3">Twilio (SMS)</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <input class="input" name="twilio_sid" placeholder="Account SID" value="<?php echo htmlspecialchars($settings['twilio.sid'] ?? ''); ?>">
                <input class="input" name="twilio_token" placeholder="Auth Token" value="<?php echo htmlspecialchars($settings['twilio.token'] ?? ''); ?>">
                <input class="input sm:col-span-2" name="twilio_from" placeholder="From Number (e.g. +1234567890)" value="<?php echo htmlspecialchars($settings['twilio.from'] ?? ''); ?>">
            </div>
        </div>

        <!-- SMS Provider Selection -->
        <div class="border-t pt-6" data-tab-panel="sms" style="display:none">
            <h2 class="font-semibold mb-3">SMS Provider</h2>
            <select class="select" name="sms_provider">
                <?php $currentProvider = $settings['sms.provider'] ?? 'twilio'; ?>
                <option value="twilio" <?php echo $currentProvider==='twilio'?'selected':''; ?>>Twilio</option>
                <option value="textsms" <?php echo $currentProvider==='textsms'?'selected':''; ?>>TextSMS (Kenya)</option>
            </select>
        </div>

        <!-- TextSMS SECTION -->
        <div class="border-t pt-6" data-tab-panel="sms" style="display:none">
            <h2 class="font-semibold mb-3">TextSMS (Kenya)</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <input class="input" name="textsms_api_key" placeholder="API Key" value="<?php echo htmlspecialchars($settings['textsms.api_key'] ?? ''); ?>">
                <input class="input" name="textsms_partner_id" placeholder="Partner ID" value="<?php echo htmlspecialchars($settings['textsms.partner_id'] ?? ''); ?>">
                <input class="input" name="textsms_shortcode" placeholder="Sender ID / Shortcode" value="<?php echo htmlspecialchars($settings['textsms.shortcode'] ?? ''); ?>">
                <input class="input" name="textsms_domain" placeholder="Domain (sms.textsms.co.ke)" value="<?php echo htmlspecialchars($settings['textsms.domain'] ?? 'sms.textsms.co.ke'); ?>">
            </div>
            <div class="text-xs text-gray-400 mt-2">Docs: <a class="link" target="_blank" href="https://textsms.co.ke/bulk-sms-api/">TextSMS Bulk SMS API</a></div>
        </div>

        <!-- PAYMENTS SECTION -->
        <div class="border-t pt-6" data-tab-panel="payments" style="display:none">
			<h2 class="font-semibold mb-3">Payments</h2>
            <div class="space-y-4">
                <!-- MPESA -->
                <div class="p-4 rounded border border-gray-700 bg-[#0f0f10]">
                    <div class="flex items-center justify-between">
						<h3 class="font-semibold">M-Pesa (STK Push)</h3>
						<label class="inline-flex items-center gap-2 text-sm">
							<input type="checkbox" name="payments_mpesa_enabled" <?php echo (($settings['payments.mpesa.enabled'] ?? '0') === '1') ? 'checked' : ''; ?>> Enable
						</label>
					</div>
                    <div class="text-xs text-gray-400 mt-1">Tip: You can restore from environment variables if set (MPESA_CONSUMER_KEY, MPESA_CONSUMER_SECRET, MPESA_SHORTCODE, MPESA_PASSKEY, MPESA_ENV, MPESA_CALLBACK_URL).</div>
					<div class="grid sm:grid-cols-2 gap-4 mt-3">
						<input class="input" name="mpesa_consumer_key" placeholder="Consumer Key" value="<?php echo htmlspecialchars($settings['payments.mpesa.consumer_key'] ?? ''); ?>">
						<input class="input" name="mpesa_consumer_secret" placeholder="Consumer Secret" value="<?php echo htmlspecialchars($settings['payments.mpesa.consumer_secret'] ?? ''); ?>">
						<input class="input" name="mpesa_shortcode" placeholder="Shortcode" value="<?php echo htmlspecialchars($settings['payments.mpesa.shortcode'] ?? ''); ?>">
						<input class="input" name="mpesa_passkey" placeholder="Lipa Na M-Pesa Passkey" value="<?php echo htmlspecialchars($settings['payments.mpesa.passkey'] ?? ''); ?>">
						<select class="select" name="mpesa_env">
							<option value="sandbox" <?php echo (($settings['payments.mpesa.env'] ?? 'sandbox') === 'sandbox') ? 'selected' : ''; ?>>Sandbox</option>
							<option value="production" <?php echo (($settings['payments.mpesa.env'] ?? 'sandbox') === 'production') ? 'selected' : ''; ?>>Production</option>
						</select>
						<input class="input" name="mpesa_callback_url" placeholder="Callback URL (https)" value="<?php echo htmlspecialchars($settings['payments.mpesa.callback_url'] ?? ''); ?>">
					</div>
                    <div class="mt-3">
                        <button class="btn btn-secondary" type="submit" formaction="<?php echo base_url('/admin/settings/restore-mpesa'); ?>" formmethod="post">Restore from Environment</button>
                    </div>
				</div>

                <!-- PAYPAL -->
                <div class="p-4 rounded border border-gray-700 bg-[#0f0f10]">
					<div class="flex items-center justify-between">
						<h3 class="font-semibold">PayPal</h3>
						<label class="inline-flex items-center gap-2 text-sm">
							<input type="checkbox" name="payments_paypal_enabled" <?php echo (($settings['payments.paypal.enabled'] ?? '0') === '1') ? 'checked' : ''; ?>> Enable
						</label>
					</div>
					<div class="grid sm:grid-cols-2 gap-4 mt-3">
						<input class="input" name="paypal_client_id" placeholder="Client ID" value="<?php echo htmlspecialchars($settings['payments.paypal.client_id'] ?? ''); ?>">
						<input class="input" name="paypal_secret" placeholder="Secret" value="<?php echo htmlspecialchars($settings['payments.paypal.secret'] ?? ''); ?>">
						<select class="select" name="paypal_env">
							<option value="sandbox" <?php echo (($settings['payments.paypal.env'] ?? 'sandbox') === 'sandbox') ? 'selected' : ''; ?>>Sandbox</option>
							<option value="production" <?php echo (($settings['payments.paypal.env'] ?? 'sandbox') === 'production') ? 'selected' : ''; ?>>Production</option>
						</select>
					</div>
				</div>

                <!-- FLUTTERWAVE -->
                <div class="p-4 rounded border border-gray-700 bg-[#0f0f10]">
					<div class="flex items-center justify-between">
						<h3 class="font-semibold">Flutterwave</h3>
						<label class="inline-flex items-center gap-2 text-sm">
							<input type="checkbox" name="payments_flutterwave_enabled" <?php echo (($settings['payments.flutterwave.enabled'] ?? '0') === '1') ? 'checked' : ''; ?>> Enable
						</label>
					</div>
					<div class="grid sm:grid-cols-3 gap-4 mt-3">
						<input class="input" name="flutterwave_public_key" placeholder="Public Key" value="<?php echo htmlspecialchars($settings['payments.flutterwave.public_key'] ?? ''); ?>">
						<input class="input" name="flutterwave_secret_key" placeholder="Secret Key" value="<?php echo htmlspecialchars($settings['payments.flutterwave.secret_key'] ?? ''); ?>">
						<input class="input" name="flutterwave_encryption_key" placeholder="Encryption Key" value="<?php echo htmlspecialchars($settings['payments.flutterwave.encryption_key'] ?? ''); ?>">
					</div>
				</div>
            </div>
        </div>

        <div class="h-20"></div>
        <div class="fixed bottom-0 inset-x-0 z-40 border-t border-gray-800 bg-[#0f0f10]/95 backdrop-blur">
            <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-end gap-3">
                <a href="<?php echo base_url('/admin'); ?>" class="btn btn-secondary">Cancel</a>
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </div>
    </form>

    <!-- Separate, non-nested Test Email form -->
    <div id="test-email-box" class="card p-4 mt-4 hidden">
        <form method="post" action="<?php echo base_url('/admin/settings/test-email'); ?>" class="flex items-center gap-2" onsubmit="this.querySelector('button').textContent='Sending...';this.querySelector('button').disabled=true;">
            <?php echo csrf_field(); ?>
            <input class="input" type="email" name="test_email" placeholder="Send test to (email)" required>
            <button class="btn btn-primary" type="submit">Send</button>
        </form>
    </div>

    <script>
    (function(){
      const tabs = document.getElementById('settingsTabs');
      if(!tabs) return;
      const btns = Array.from(tabs.querySelectorAll('.tab-btn'));
      const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));
      function show(name){
        panels.forEach(p=>{ p.style.display = (p.getAttribute('data-tab-panel')===name)?'block':'none'; });
        btns.forEach(b=>{ b.classList.toggle('active', b.getAttribute('data-tab')===name); });
      }
      btns.forEach(b=>b.addEventListener('click', ()=>show(b.getAttribute('data-tab'))));
      // default
      show('general');
    })();
    </script>

</div>



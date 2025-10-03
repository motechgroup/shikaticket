<div class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-8 items-start">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Partner with <?php echo htmlspecialchars(\App\Models\Setting::get('site.name', 'Ticko')); ?></h1>
            <p class="text-gray-300 leading-relaxed mb-4">We collaborate with companies, organizations, celebrities and institutions on events, CSR, charities and strategic initiatives. Join us to create impact and deliver memorable experiences for audiences.</p>
            <ul class="list-disc pl-5 text-gray-300 space-y-2 mb-6">
                <li>Brand exposure across our platform and events</li>
                <li>Co-creation of experiences and activations</li>
                <li>CSR and charity partnerships with measurable impact</li>
                <li>Dedicated account support and reporting</li>
            </ul>
            <div class="card p-4 bg-gradient-to-r from-black to-[#131313]">
                <h2 class="font-semibold mb-2">How to partner</h2>
                <p class="text-gray-300 text-sm">Tell us about you, your goals and what you want to achieve. Our partnerships team will review and get back within 3 working days.</p>
            </div>
        </div>
        <div class="card p-6">
            <?php if ($msg = flash_get('success')): ?><div class="alert-success px-4 py-3 mb-3"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
            <form method="post" action="<?php echo base_url('/partners'); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm mb-1">Full Name</label>
                    <input class="input" name="name" required>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Email</label>
                        <input class="input" type="email" name="email" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Phone</label>
                        <input class="input" name="phone" placeholder="2547XXXXXXXX">
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1">Organization (optional)</label>
                    <input class="input" name="organization">
                </div>
                <div>
                    <label class="block text-sm mb-1">Category</label>
                    <select class="select" name="category">
                        <option>Corporate</option>
                        <option>Celebrity</option>
                        <option>NGO</option>
                        <option>Institution</option>
                        <option>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Message</label>
                    <textarea class="textarea" name="message" rows="5" placeholder="Tell us how you’d like to partner..."></textarea>
                </div>
                <button class="btn btn-primary w-full">Submit Partnership Request</button>
            </form>
        </div>
    </div>
</div>



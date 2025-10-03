<?php /** @var array $agency */ ?>
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Edit Travel Agency</h1>
        <a class="btn btn-secondary" href="<?php echo base_url('/admin/travel/agencies'); ?>">Back</a>
    </div>
    <div class="card p-6">
        <form method="post" action="<?php echo base_url('/admin/travel/agencies/update'); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$agency['id']; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Company Name</label>
                    <input class="input" type="text" name="company_name" value="<?php echo htmlspecialchars($agency['company_name']); ?>" required>
                </div>
                <div>
                    <label class="label">Contact Person</label>
                    <input class="input" type="text" name="contact_person" value="<?php echo htmlspecialchars($agency['contact_person']); ?>">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($agency['email']); ?>">
                </div>
                <div>
                    <label class="label">Phone</label>
                    <input class="input" type="text" name="phone" value="<?php echo htmlspecialchars($agency['phone']); ?>">
                </div>
                <div>
                    <label class="label">Website</label>
                    <input class="input" type="text" name="website" value="<?php echo htmlspecialchars($agency['website']); ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="label">Description</label>
                    <textarea class="textarea" name="description" rows="4"><?php echo htmlspecialchars($agency['description']); ?></textarea>
                </div>
                <div>
                    <label class="label">Commission Rate (%)</label>
                    <input class="input" type="number" step="0.01" min="0" max="100" name="commission_rate" value="<?php echo htmlspecialchars($agency['commission_rate'] ?? '10.0'); ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="label">Logo</label>
                    <div class="flex items-center gap-4">
                        <?php if (!empty($agency['logo_path'])): ?>
                            <img src="<?php echo base_url($agency['logo_path']); ?>" class="h-16 w-16 object-cover rounded" alt="Logo">
                        <?php endif; ?>
                        <input class="input" type="file" name="logo" accept="image/*">
                    </div>
                </div>
                <div class="flex items-center gap-6 md:col-span-2">
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" <?php echo (int)$agency['is_active'] ? 'checked' : ''; ?>> <span>Active</span></label>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_approved" <?php echo (int)$agency['is_approved'] ? 'checked' : ''; ?>> <span>Approved</span></label>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="phone_verified" <?php echo (int)$agency['phone_verified'] ? 'checked' : ''; ?>> <span>Phone Verified</span></label>
                </div>
            </div>
            <div class="mt-6">
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>



<?php /** @var array $org */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6"><?php echo $org['id']? 'Edit Organizer' : 'Create Organizer'; ?></h1>
    <form method="post" action="<?php echo base_url('/admin/organizers/save'); ?>" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$org['id']; ?>">
        <div>
            <label class="block text-sm mb-1">Full Name</label>
            <input class="input" name="full_name" value="<?php echo htmlspecialchars($org['full_name']); ?>" required>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($org['email']); ?>" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Phone</label>
                <input class="input" name="phone" value="<?php echo htmlspecialchars($org['phone']); ?>" required>
            </div>
        </div>
        <?php if (!$org['id']): ?>
        <div>
            <label class="block text-sm mb-1">Password (temporary)</label>
            <input class="input" type="text" name="password" value="changeme123">
        </div>
        <?php endif; ?>
        <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_approved" <?php echo (int)$org['is_approved'] ? 'checked' : ''; ?>> Approved</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" <?php echo (int)$org['is_active'] ? 'checked' : ''; ?>> Active</label>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>



<?php /** @var array $agencies */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Travel Agencies</h1>
        <a class="btn btn-secondary" href="<?php echo base_url('/admin'); ?>">Back</a>
    </div>
    <div class="card p-4">
        <?php if (empty($agencies)): ?>
            <div class="p-6 text-gray-400">No travel agencies yet.</div>
        <?php else: ?>
            <table class="min-w-full text-sm table">
                <thead>
                    <tr>
                        <th class="p-3 text-left">Company</th>
                        <th class="p-3 text-left">Contact</th>
                        <th class="p-3 text-left">Phone</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Commission %</th>
                        <th class="p-3 text-left">Approved</th>
                        <th class="p-3 text-left">Phone Verified</th>
                        <th class="p-3 text-left"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agencies as $a): ?>
                    <tr>
                        <td class="p-3 font-medium"><?php echo htmlspecialchars($a['company_name']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($a['contact_person']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($a['phone']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($a['email']); ?></td>
                        <td class="p-3">
                            <form method="post" action="<?php echo base_url('/admin/travel/agencies/commission'); ?>" class="flex items-center gap-2">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="<?php echo htmlspecialchars($a['commission_rate'] ?? '10.0'); ?>" class="w-24 input">
                                <button class="btn btn-secondary">Save</button>
                            </form>
                        </td>
                        <td class="p-3"><?php echo (int)$a['is_approved'] ? 'Yes' : 'No'; ?></td>
                        <td class="p-3"><?php echo (int)$a['phone_verified'] ? 'Yes' : 'No'; ?></td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <?php if (!(int)$a['is_approved']): ?>
                                <form method="post" action="<?php echo base_url('/admin/travel/agencies/approve'); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                    <button class="btn btn-primary">Approve</button>
                                </form>
                                <?php endif; ?>
                                <?php if (!(int)$a['phone_verified']): ?>
                                <form method="post" action="<?php echo base_url('/admin/travel/agencies/verify-phone'); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                    <button class="btn btn-secondary">Verify Phone</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>



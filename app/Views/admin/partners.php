<?php /** @var array $partners */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6">Partners</h1>
    <div class="card p-4">
        <?php if (empty($partners)): ?>
            <div class="p-6 text-gray-400">No partner requests yet.</div>
        <?php else: ?>
        <table class="min-w-full text-sm table">
            <thead><tr><th class="p-3 text-left">Name</th><th class="p-3 text-left">Organization</th><th class="p-3 text-left">Category</th><th class="p-3 text-left">Contact</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Actions</th></tr></thead>
            <tbody>
                <?php foreach ($partners as $p): ?>
                <tr>
                    <td class="p-3"><?php echo htmlspecialchars($p['name']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['organization']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['category']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['email']); ?><?php if($p['phone']): ?> • <?php echo htmlspecialchars($p['phone']); ?><?php endif; ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($p['status']); ?></td>
                    <td class="p-3">
                        <form method="post" action="<?php echo base_url('/admin/partners/status'); ?>" class="inline-flex items-center gap-2">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                            <select name="status" class="select">
                                <?php foreach (['new','in_review','approved','rejected'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo ($p['status']===$s?'selected':''); ?>><?php echo ucfirst(str_replace('_',' ', $s)); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>



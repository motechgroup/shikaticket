<?php /** @var array $users */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<h1 class="text-2xl font-semibold mb-6">Users</h1>
    <div class="card p-0">
		<?php if (empty($users)): ?>
			<div class="p-6 text-gray-400">No users yet.</div>
		<?php else: ?>
            <table class="min-w-full text-sm table">
				<thead>
					<tr>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Phone</th>
                        <th class="p-3 text-left">Active</th>
                        <th class="p-3 text-left">Joined</th>
                        <th class="p-3 text-left"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($users as $u): ?>
					<tr>
                        <td class="p-3"><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($u['phone'] ?? ''); ?></td>
                        <td class="p-3"><?php echo ((int)($u['is_active'] ?? 1) === 1) ? 'Yes' : 'No'; ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($u['created_at'] ?? ''); ?></td>
                        <td class="p-3 text-right flex gap-2 justify-end">
                            <form method="post" action="<?php echo base_url('/admin/users/toggle'); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                                <input type="hidden" name="is_active" value="<?php echo ((int)($u['is_active'] ?? 1) === 1) ? 0 : 1; ?>">
                                <button class="btn btn-secondary"><?php echo ((int)($u['is_active'] ?? 1) === 1) ? 'Deactivate' : 'Activate'; ?></button>
                            </form>
                            <form method="post" action="<?php echo base_url('/admin/users/delete'); ?>" onsubmit="return confirm('Delete user?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                                <button class="btn btn-primary">Delete</button>
                            </form>
                        </td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>



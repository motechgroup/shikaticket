<?php /** @var array $organizers */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
	<div class="flex items-center justify-between mb-6">
		<h1 class="text-2xl font-semibold">Organizers</h1>
		<a class="btn btn-primary" href="<?php echo base_url('/admin/organizers/create'); ?>">Create Organizer</a>
	</div>
	<div class="card p-0">
		<?php if (empty($organizers)): ?>
			<div class="p-6 text-gray-400">No organizers yet.</div>
		<?php else: ?>
			<table class="min-w-full text-sm table">
				<thead>
					<tr>
						<th class="p-3 text-left">Name</th>
						<th class="p-3 text-left">Email</th>
						<th class="p-3 text-left">Phone</th>
						<th class="p-3 text-left">Commission %</th>
						<th class="p-3 text-left">Approved</th>
						<th class="p-3 text-left"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($organizers as $org): ?>
					<tr>
					<td class="p-3"><a class="link" href="<?php echo base_url('/admin/organizers/edit?id='.(int)$org['id']); ?>"><?php echo htmlspecialchars($org['full_name']); ?></a></td>
						<td class="p-3"><?php echo htmlspecialchars($org['email']); ?></td>
						<td class="p-3"><?php echo htmlspecialchars($org['phone']); ?></td>
						<td class="p-3">
							<form method="post" action="<?php echo base_url('/admin/organizers/commission'); ?>" class="flex items-center gap-2">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="id" value="<?php echo (int)$org['id']; ?>">
								<input name="commission_percent" value="<?php echo htmlspecialchars($org['commission_percent'] ?? '0.00'); ?>" class="input" style="width:90px" />
								<button class="btn btn-secondary">Save</button>
							</form>
						</td>
						<td class="p-3"><?php echo (int)$org['is_approved'] === 1 ? 'Yes' : 'No'; ?></td>
                    <td class="p-3 text-right flex items-center gap-2 justify-end">
                        <a class="btn btn-secondary" href="<?php echo base_url('/admin/organizers/show?id='.(int)$org['id']); ?>">View</a>
                        <a class="btn btn-secondary" href="<?php echo base_url('/admin/organizers/edit?id='.(int)$org['id']); ?>">Edit</a>
							<?php if ((int)$org['is_approved'] !== 1): ?>
							<form method="post" action="<?php echo base_url('/admin/organizers/approve'); ?>">
									<?php echo csrf_field(); ?>
									<input type="hidden" name="id" value="<?php echo (int)$org['id']; ?>">
									<button class="btn btn-primary">Approve</button>
								</form>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>



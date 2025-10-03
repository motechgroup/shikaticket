<?php /** @var array $rows */ /** @var string $role */ /** @var string $q */ /** @var int $page */ /** @var int $pages */ /** @var int $total */ ?>
<div class="max-w-6xl mx-auto px-4 py-10">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Activity Logs</h1>
    <a class="btn btn-secondary" href="<?php echo base_url('/admin/logs/export'); ?>">Export CSV</a>
  </div>

  <form method="get" class="card p-4 mb-4 grid sm:grid-cols-4 gap-3">
    <div class="sm:col-span-1">
      <label class="block text-sm mb-1">Role</label>
      <select class="select" name="role">
        <option value="" <?php echo $role===''?'selected':''; ?>>All</option>
        <option value="admin" <?php echo $role==='admin'?'selected':''; ?>>Admin</option>
        <option value="manager" <?php echo $role==='manager'?'selected':''; ?>>Manager</option>
        <option value="accountant" <?php echo $role==='accountant'?'selected':''; ?>>Accountant</option>
      </select>
    </div>
    <div class="sm:col-span-2">
      <label class="block text-sm mb-1">Search</label>
      <input class="input" type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search category, message, URL, IP">
    </div>
    <div class="sm:col-span-1 flex items-end">
      <button class="btn btn-primary w-full" type="submit">Filter</button>
    </div>
  </form>

  <div class="card p-0">
    <div class="table-responsive">
      <table class="min-w-full text-sm table">
        <thead>
          <tr>
            <th class="p-3 text-left">Time</th>
            <th class="p-3 text-left">Role</th>
            <th class="p-3 text-left">Category</th>
            <th class="p-3 text-left">Level</th>
            <th class="p-3 text-left">IP</th>
            <th class="p-3 text-left">URL</th>
            <th class="p-3 text-left">Message</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
          <tr><td class="p-3 text-gray-400" colspan="7">No logs found.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td class="p-3 text-gray-400"><?php echo htmlspecialchars($r['occurred_at']); ?></td>
            <td class="p-3"><?php echo htmlspecialchars($r['role'] ?? ''); ?></td>
            <td class="p-3"><?php echo htmlspecialchars($r['category'] ?? ''); ?></td>
            <td class="p-3"><?php echo htmlspecialchars(strtoupper($r['level'] ?? '')); ?></td>
            <td class="p-3 text-gray-400"><?php echo htmlspecialchars($r['ip'] ?? ''); ?></td>
            <td class="p-3 text-gray-400 truncate max-w-[240px]"><?php echo htmlspecialchars($r['url'] ?? ''); ?></td>
            <td class="p-3"><?php echo htmlspecialchars($r['message'] ?? ''); ?></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4 flex items-center justify-between text-sm">
    <div>Total: <?php echo number_format($total); ?> logs</div>
    <div class="flex items-center gap-2">
      <?php if ($page > 1): ?>
        <a class="btn btn-secondary btn-xs" href="<?php echo base_url('/admin/logs?role='.urlencode($role).'&q='.urlencode($q).'&page='.($page-1)); ?>">Prev</a>
      <?php endif; ?>
      <span>Page <?php echo $page; ?> of <?php echo $pages; ?></span>
      <?php if ($page < $pages): ?>
        <a class="btn btn-secondary btn-xs" href="<?php echo base_url('/admin/logs?role='.urlencode($role).'&q='.urlencode($q).'&page='.($page+1)); ?>">Next</a>
      <?php endif; ?>
    </div>
  </div>
</div>

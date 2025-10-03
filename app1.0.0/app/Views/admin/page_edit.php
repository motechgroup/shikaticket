<?php /** @var array $page */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-6"><?php echo $page['id'] ? 'Edit Page' : 'New Page'; ?></h1>
    <form method="post" action="<?php echo base_url('/admin/pages/save'); ?>" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$page['id']; ?>">
        <div>
            <label class="block text-sm mb-1">Title</label>
            <input class="input" name="title" value="<?php echo htmlspecialchars($page['title']); ?>" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Slug</label>
            <input class="input" name="slug" value="<?php echo htmlspecialchars($page['slug']); ?>" placeholder="privacy-policy" required>
        </div>
        <div>
            <label class="block text-sm mb-1">Content</label>
            <textarea class="textarea" id="editor" name="content" rows="14"><?php echo htmlspecialchars($page['content']); ?></textarea>
            <div class="text-xs text-gray-400 mt-1">Supports rich text.</div>
        </div>
        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_published" <?php echo (int)$page['is_published'] ? 'checked' : ''; ?>> Published</label>
        <div>
            <button class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('editor');</script>



<?php /** @var array $page */ ?>
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-semibold mb-4"><?php echo htmlspecialchars($page['title']); ?></h1>
    <div class="prose prose-invert max-w-none">
        <?php echo $page['content']; ?>
    </div>
</div>



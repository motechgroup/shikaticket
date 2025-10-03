<?php
namespace App\Controllers;

class DocsController
{
    private array $pages = [
        'getting-started' => '01-getting-started.md',
        'user-guide' => '02-user-guide.md',
        'organizer-guide' => '03-organizer-guide.md',
        'partners-guide' => '05-partners-guide.md',
        'scanner-guide' => '06-scanner-guide.md',
        'email-sms-templates' => '07-email-sms-templates.md',
        'asset-branding' => '08-asset-branding-guidelines.md',
        'marketing-comms' => '09-marketing-and-comms.md',
    ];

    public function index(): void
    {
        $items = [
            ['slug'=>'getting-started','title'=>'Getting Started'],
            ['slug'=>'user-guide','title'=>'User Guide'],
            ['slug'=>'organizer-guide','title'=>'Organizer Guide'],
            ['slug'=>'partners-guide','title'=>'Partners Guide'],
            ['slug'=>'scanner-guide','title'=>'Scanner Guide'],
            ['slug'=>'email-sms-templates','title'=>'Email & SMS Templates'],
            ['slug'=>'asset-branding','title'=>'Asset & Branding Guidelines'],
            ['slug'=>'marketing-comms','title'=>'Marketing & Communications'],
        ];
        view('docs/index', compact('items'));
    }

    public function show(): void
    {
        $slug = preg_replace('/[^a-z0-9\-]/i','', $_GET['slug'] ?? '');
        if (!$slug || !isset($this->pages[$slug])) { redirect(base_url('/help')); }
        $mdPath = __DIR__ . '/../../docs/' . $this->pages[$slug];
        if (!is_file($mdPath)) { redirect(base_url('/help')); }
        $markdown = file_get_contents($mdPath) ?: '';
        $html = $this->markdownToHtml($markdown);
        view('docs/show', compact('slug','html'));
    }

    private function markdownToHtml(string $md): string
    {
        $md = str_replace(["\r\n","\r"], "\n", $md);
        // code blocks ```
        $md = preg_replace_callback('/```([\s\S]*?)```/m', function($m){
            $code = htmlspecialchars(trim($m[1]));
            return '<pre class="card p-3 overflow-auto"><code>'.$code.'</code></pre>';
        }, $md);
        // headings
        $md = preg_replace('/^######\s*(.+)$/m', '<h6>$1</h6>', $md);
        $md = preg_replace('/^#####\s*(.+)$/m', '<h5>$1</h5>', $md);
        $md = preg_replace('/^####\s*(.+)$/m', '<h4>$1</h4>', $md);
        $md = preg_replace('/^###\s*(.+)$/m', '<h3>$1</h3>', $md);
        $md = preg_replace('/^##\s*(.+)$/m', '<h2>$1</h2>', $md);
        $md = preg_replace('/^#\s*(.+)$/m', '<h1>$1</h1>', $md);
        // bold/italic
        $md = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $md);
        $md = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $md);
        // lists
        $md = preg_replace('/^\-\s+(.+)$/m', '<li>$1</li>', $md);
        $md = preg_replace('/(<li>.+<\/li>)/s', '<ul class="list-disc ml-6 space-y-1">$1</ul>', $md, 1);
        // paragraphs
        $parts = array_filter(array_map('trim', explode("\n\n", $md)), fn($p)=>$p!=='');
        $html = '';
        foreach ($parts as $p) {
            if (preg_match('/^<h[1-6]>/', $p) || str_starts_with($p, '<pre') || str_starts_with($p, '<ul')) {
                $html .= $p; continue;
            }
            $html .= '<p class="mb-4 text-gray-300">' . $p . '</p>';
        }
        return $html;
    }
}

<?php /** @var array|null $conversation */ ?>
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="card p-6">
        <h1 class="text-xl font-semibold mb-4">Support</h1>
        <?php if (!$conversation): ?>
            <form method="post" action="<?php echo base_url('/support/start'); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <input class="input" type="text" name="subject" placeholder="Subject (optional)">
                <textarea class="textarea" name="message" rows="3" placeholder="Describe your issue"></textarea>
                <button class="btn btn-primary">Start Chat</button>
            </form>
        <?php else: ?>
            <div id="chatBox" class="h-80 overflow-y-auto border border-gray-700 rounded p-3 bg-gray-900 mb-3"></div>
            <form method="post" action="<?php echo base_url('/support/send'); ?>" class="flex gap-2">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="conversation_id" value="<?php echo (int)$conversation['id']; ?>">
                <input class="input flex-1" type="text" name="message" placeholder="Type your message..." required>
                <button class="btn btn-primary">Send</button>
            </form>
            <script>
            const convoId = <?php echo (int)$conversation['id']; ?>;
            const chatBox = document.getElementById('chatBox');
            async function loadMessages(){
                try{
                    const r = await fetch('<?php echo base_url('/support/messages'); ?>?conversation_id=' + convoId + '&_=' + Date.now());
                    const j = await r.json();
                    chatBox.innerHTML = '';
                    (j.messages||[]).forEach(m => {
                        const div = document.createElement('div');
                        const side = m.sender_type === 'user' ? 'text-blue-300' : 'text-green-300';
                        div.className = 'mb-2';
                        div.innerHTML = `<div class="${side}"><strong>${m.sender_type}</strong> <span class="text-gray-500 text-xs">${m.created_at}</span></div><div>${m.message.replace(/</g,'&lt;')}</div>`;
                        chatBox.appendChild(div);
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                }catch(e){}
            }
            loadMessages(); setInterval(loadMessages, 3000);
            </script>
        <?php endif; ?>
    </div>
</div>



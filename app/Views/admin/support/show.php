<?php /** @var int $conversation_id */ ?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="card p-6">
        <h1 class="text-xl font-semibold mb-4">Conversation #<?php echo (int)$conversation_id; ?></h1>
        <div id="chatBox" class="h-96 overflow-y-auto border border-gray-700 rounded p-3 bg-gray-900 mb-3"></div>
        <form method="post" action="<?php echo base_url('/admin/support/send'); ?>" class="flex gap-2">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="conversation_id" value="<?php echo (int)$conversation_id; ?>">
            <input class="input flex-1" type="text" name="message" placeholder="Type your reply..." required>
            <button class="btn btn-primary">Send</button>
        </form>
    </div>
</div>
<script>
const convoId = <?php echo (int)$conversation_id; ?>;
const chatBox = document.getElementById('chatBox');
async function loadMessages(){
  try{
    const r = await fetch('<?php echo base_url('/admin/support/messages'); ?>?conversation_id=' + convoId + '&_=' + Date.now());
    const j = await r.json(); chatBox.innerHTML = '';
    (j.messages||[]).forEach(m => {
      const div = document.createElement('div');
      const side = m.sender_type === 'admin' ? 'text-green-300' : 'text-blue-300';
      div.className = 'mb-2';
      div.innerHTML = `<div class="${side}"><strong>${m.sender_type}</strong> <span class="text-gray-500 text-xs">${m.created_at}</span></div><div>${m.message.replace(/</g,'&lt;')}</div>`;
      chatBox.appendChild(div);
    });
    chatBox.scrollTop = chatBox.scrollHeight;
  }catch(e){}
}
loadMessages(); setInterval(loadMessages, 2500);
</script>



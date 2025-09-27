<?php /** @var array $templates */ ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="flex items-center gap-4 mb-6">
        <a href="<?php echo base_url('/admin/communications'); ?>" class="text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold">Create Communication</h1>
    </div>

    <form method="post" action="<?php echo base_url('/admin/communications/store'); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>
        
        <!-- Basic Information -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm mb-1">Title *</label>
                    <input type="text" name="title" class="input" placeholder="Communication title" required>
                    <div class="text-xs text-gray-400 mt-1">A descriptive title for this communication</div>
                </div>
                
                <div>
                    <label class="block text-sm mb-1">Recipient Type *</label>
                    <select name="recipient_type" id="recipient_type" class="input" required>
                        <option value="">Select recipient type</option>
                        <option value="users">All Users</option>
                        <option value="organizers">All Organizers</option>
                        <option value="travel_agencies">All Travel Agencies</option>
                        <option value="custom">Custom Selection</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Message Template -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Message Template</h2>
            
            <div class="mb-4">
                <label class="block text-sm mb-1">Use Template</label>
                <select id="template_select" class="input">
                    <option value="">Select a template (optional)</option>
                    <?php foreach ($templates as $template): ?>
                        <option value="<?php echo $template['id']; ?>" data-message="<?php echo htmlspecialchars($template['message']); ?>">
                            <?php echo htmlspecialchars($template['name']); ?> - <?php echo htmlspecialchars($template['description']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm mb-1">Message *</label>
                <textarea name="message" id="message" class="textarea" rows="6" placeholder="Enter your message here..." required></textarea>
                <div class="text-xs text-gray-400 mt-1">
                    Available placeholders: {{name}}, {{phone}}, {{recipient_type}}
                </div>
            </div>
        </div>

        <!-- Recipient Selection (for custom) -->
        <div class="card p-6" id="custom_recipients" style="display: none;">
            <h2 class="text-xl font-semibold mb-4">Select Recipients</h2>
            
            <div class="mb-4">
                <label class="block text-sm mb-1">Search Recipients</label>
                <input type="text" id="recipient_search" class="input" placeholder="Search by name or phone number">
                <div class="text-xs text-gray-400 mt-1">Start typing to search for specific recipients</div>
            </div>
            
            <div class="mb-4">
                <button type="button" id="load_recipients" class="btn btn-secondary">
                    Load Recipients
                </button>
                <span id="recipient_count" class="ml-4 text-sm text-gray-400">0 recipients selected</span>
            </div>
            
            <div id="recipients_list" class="max-h-96 overflow-y-auto border border-gray-700 rounded-lg p-4">
                <div class="text-center text-gray-400 py-8">
                    Select recipient type and click "Load Recipients" to see available recipients
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Preview</h2>
            <div id="message_preview" class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                <div class="text-gray-400">Message preview will appear here</div>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Create Communication
            </button>
            <a href="<?php echo base_url('/admin/communications'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientTypeSelect = document.getElementById('recipient_type');
    const customRecipientsDiv = document.getElementById('custom_recipients');
    const templateSelect = document.getElementById('template_select');
    const messageTextarea = document.getElementById('message');
    const messagePreview = document.getElementById('message_preview');
    const loadRecipientsBtn = document.getElementById('load_recipients');
    const recipientsList = document.getElementById('recipients_list');
    const recipientCount = document.getElementById('recipient_count');
    const recipientSearch = document.getElementById('recipient_search');
    
    let selectedRecipients = [];
    
    // Show/hide custom recipients section
    recipientTypeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customRecipientsDiv.style.display = 'block';
        } else {
            customRecipientsDiv.style.display = 'none';
            selectedRecipients = [];
            updateRecipientCount();
        }
    });
    
    // Template selection
    templateSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const message = selectedOption.getAttribute('data-message');
            messageTextarea.value = message;
            updatePreview();
        }
    });
    
    // Message preview
    messageTextarea.addEventListener('input', updatePreview);
    
    function updatePreview() {
        let preview = messageTextarea.value;
        preview = preview.replace(/\{\{name\}\}/g, 'John Doe');
        preview = preview.replace(/\{\{phone\}\}/g, '254700000000');
        preview = preview.replace(/\{\{recipient_type\}\}/g, 'User');
        
        messagePreview.innerHTML = preview || '<div class="text-gray-400">Message preview will appear here</div>';
    }
    
    // Load recipients
    loadRecipientsBtn.addEventListener('click', function() {
        const recipientType = recipientTypeSelect.value;
        if (!recipientType || recipientType === 'custom') {
            alert('Please select a recipient type first');
            return;
        }
        
        const search = recipientSearch.value;
        loadRecipients(recipientType, search);
    });
    
    // Search recipients
    recipientSearch.addEventListener('input', function() {
        const recipientType = recipientTypeSelect.value;
        if (recipientType && recipientType !== 'custom') {
            loadRecipients(recipientType, this.value);
        }
    });
    
    function loadRecipients(type, search = '') {
        const url = `<?php echo base_url('/admin/communications/recipients'); ?>?type=${type}&search=${encodeURIComponent(search)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                displayRecipients(data);
            })
            .catch(error => {
                console.error('Error loading recipients:', error);
                recipientsList.innerHTML = '<div class="text-red-400">Error loading recipients</div>';
            });
    }
    
    function displayRecipients(recipients) {
        if (recipients.length === 0) {
            recipientsList.innerHTML = '<div class="text-gray-400 text-center py-4">No recipients found</div>';
            return;
        }
        
        recipientsList.innerHTML = recipients.map(recipient => `
            <div class="flex items-center justify-between p-3 border border-gray-700 rounded-lg mb-2 hover:bg-gray-800/50">
                <div>
                    <div class="font-medium">${recipient.name}</div>
                    <div class="text-sm text-gray-400">${recipient.phone}</div>
                </div>
                <label class="flex items-center">
                    <input type="checkbox" value="${recipient.id}" class="recipient-checkbox mr-2">
                    <span class="text-sm">Select</span>
                </label>
            </div>
        `).join('');
        
        // Add event listeners to checkboxes
        recipientsList.querySelectorAll('.recipient-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    selectedRecipients.push(this.value);
                } else {
                    selectedRecipients = selectedRecipients.filter(id => id !== this.value);
                }
                updateRecipientCount();
            });
        });
    }
    
    function updateRecipientCount() {
        recipientCount.textContent = `${selectedRecipients.length} recipients selected`;
        
        // Add hidden inputs for selected recipients
        const existingInputs = document.querySelectorAll('input[name="recipient_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        selectedRecipients.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'recipient_ids[]';
            input.value = id;
            document.querySelector('form').appendChild(input);
        });
    }
    
    // Initial preview update
    updatePreview();
});
</script>

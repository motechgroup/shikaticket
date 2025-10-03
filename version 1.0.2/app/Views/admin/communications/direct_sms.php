<?php ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="flex items-center gap-4 mb-6">
        <a href="<?php echo base_url('/admin/communications'); ?>" class="text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold">Direct SMS</h1>
        <span class="text-sm text-gray-400">Quick response to inquiries</span>
    </div>

    <form method="post" action="" class="space-y-6" onsubmit="return validateForm()">
        <?php echo csrf_field(); ?>
        
        <!-- Hidden fields for recipient data -->
        <input type="hidden" name="recipient_id" id="hidden_recipient_id" value="">
        <input type="hidden" name="recipient_name" id="hidden_recipient_name" value="">
        
        <!-- Quick Recipient Selection -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Recipient Selection</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm mb-1">Recipient Type</label>
                    <select name="recipient_type" id="recipient_type" class="input" onchange="loadRecipients()">
                        <option value="">Select recipient type</option>
                        <option value="users">All Users</option>
                        <option value="organizers">All Organizers</option>
                        <option value="travel_agencies">All Travel Agencies</option>
                        <option value="direct">Direct Number</option>
                    </select>
                    <div class="text-xs text-gray-400 mt-1">Select a type to see all recipients</div>
                </div>
                
                <div>
                    <label class="block text-sm mb-1">Search Within Type</label>
                    <div class="flex gap-2">
                        <input type="text" id="recipient_search" class="input flex-1" placeholder="Search by name or phone..." onkeyup="filterRecipients()">
                        <button type="button" id="clear_search" class="btn btn-secondary">Clear</button>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Search within the selected recipient type</div>
                </div>
            </div>
            
            <!-- Recipients List -->
            <div id="recipients_list" class="mt-4 hidden">
                <div class="border border-gray-700 rounded-lg max-h-64 overflow-y-auto">
                    <div class="p-3 border-b border-gray-700 bg-blue-900/20">
                        <div class="text-sm text-blue-300" id="recipients_header">Select a recipient type to see recipients</div>
                        <div class="text-xs text-blue-400 mt-1" id="recipients_count">0 recipients</div>
                    </div>
                    <div id="recipients_content" class="p-4 text-center text-gray-400">
                        Select a recipient type above to see all recipients
                    </div>
                </div>
            </div>
            
            <!-- Fallback Search Form (Hidden) -->
            <form id="fallback_search_form" method="get" action="" class="hidden">
                <input type="hidden" name="search_type" id="fallback_search_type">
                <input type="hidden" name="search_term" id="fallback_search_term">
            </form>
        </div>

        <!-- Phone Number Input -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Phone Number</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm mb-1">Phone Number *</label>
                    <input type="text" name="phone_number" id="phone_number" class="input" placeholder="254700000000" required>
                    <div class="text-xs text-gray-400 mt-1">Enter phone number in international format</div>
                </div>
                
                <div>
                    <label class="block text-sm mb-1">Recipient Name (for tracking)</label>
                    <input type="text" id="recipient_name" class="input" placeholder="Recipient name (optional)" readonly>
                    <div class="text-xs text-gray-400 mt-1">Will be auto-filled when you select a recipient</div>
                </div>
            </div>
        </div>

        <!-- Message -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Message</h2>
            
            <div>
                <label class="block text-sm mb-1">SMS Message *</label>
                <textarea name="message" class="textarea" rows="6" placeholder="Enter your message here..." required></textarea>
                <div class="flex justify-between items-center mt-2">
                    <div class="text-xs text-gray-400">
                        Available placeholders: {{name}}, {{phone}}
                    </div>
                    <div class="text-xs text-gray-400">
                        <span id="char_count">0</span> characters
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Templates -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Quick Templates</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button type="button" class="template-btn btn btn-secondary" data-template="Hi {{name}}, thank you for contacting us. We'll get back to you shortly.">
                    Inquiry Response
                </button>
                <button type="button" class="template-btn btn btn-secondary" data-template="Hi {{name}}, your issue has been resolved. Please let us know if you need further assistance.">
                    Issue Resolved
                </button>
                <button type="button" class="template-btn btn btn-secondary" data-template="Hi {{name}}, we have an update regarding your request. Please check your dashboard.">
                    Update Notification
                </button>
                <button type="button" class="template-btn btn btn-secondary" data-template="Hi {{name}}, please call us at +254 XXX XXX XXX for immediate assistance.">
                    Call Back Request
                </button>
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Send SMS
            </button>
            <a href="<?php echo base_url('/admin/communications'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Sample data - in a real implementation, this would come from the server
const sampleRecipients = {
    users: [
        { id: 1, name: 'obed', phone: '254792758752' },
        { id: 2, name: 'ongeri', phone: '0790089922' },
        { id: 3, name: 'test', phone: '0715857882' },
        { id: 4, name: 'hillarynyanchiri66', phone: '0704274731' }
    ],
    organizers: [
        { id: 3, name: 'James', phone: '0703207984' },
        { id: 4, name: 'John Doe', phone: '0712345678' }
    ],
    travel_agencies: [
        { id: 1, name: 'test (obed)', phone: '254792758752' }
    ]
};

let allRecipients = [];
let filteredRecipients = [];

document.addEventListener('DOMContentLoaded', function() {
    const recipientTypeSelect = document.getElementById('recipient_type');
    const recipientSearch = document.getElementById('recipient_search');
    const clearSearchBtn = document.getElementById('clear_search');
    const recipientsList = document.getElementById('recipients_list');
    const recipientsHeader = document.getElementById('recipients_header');
    const recipientsCount = document.getElementById('recipients_count');
    const recipientsContent = document.getElementById('recipients_content');
    const phoneNumberInput = document.getElementById('phone_number');
    const recipientNameInput = document.getElementById('recipient_name');
    const messageTextarea = document.querySelector('textarea[name="message"]');
    const charCount = document.getElementById('char_count');
    const templateButtons = document.querySelectorAll('.template-btn');
    
    let selectedRecipient = null;
    
    // Character count
    messageTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
    
    // Template buttons
    templateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const template = this.dataset.template;
            messageTextarea.value = template;
            charCount.textContent = template.length;
        });
    });
    
    // Clear search button
    clearSearchBtn.addEventListener('click', function() {
        recipientSearch.value = '';
        filterRecipients();
    });
    
    // Auto-format phone number
    phoneNumberInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.startsWith('0')) {
            value = '254' + value.substring(1);
        } else if (!value.startsWith('254')) {
            value = '254' + value;
        }
        this.value = value;
    });
    
    // Debug: Add click handler to Send SMS button
    const sendBtn = document.querySelector('button[type="submit"]');
    if (sendBtn) {
        sendBtn.addEventListener('click', function(e) {
            console.log('Send SMS button clicked!');
            console.log('Event:', e);
            console.log('Button text:', this.textContent);
        });
    }
    
    // Load recipients when type changes
    window.loadRecipients = function() {
        const selectedType = recipientTypeSelect.value;
        
        if (!selectedType || selectedType === 'direct') {
            recipientsList.classList.add('hidden');
            return;
        }
        
        // Get recipients for the selected type
        allRecipients = sampleRecipients[selectedType] || [];
        filteredRecipients = [...allRecipients];
        
        // Update header
        const typeName = selectedType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        recipientsHeader.textContent = `${typeName} (${allRecipients.length} total)`;
        recipientsCount.textContent = `${allRecipients.length} recipients`;
        
        // Display recipients
        displayRecipients();
        recipientsList.classList.remove('hidden');
    };
    
    // Filter recipients based on search
    window.filterRecipients = function() {
        const searchTerm = recipientSearch.value.toLowerCase().trim();
        
        if (!searchTerm) {
            filteredRecipients = [...allRecipients];
        } else {
            filteredRecipients = allRecipients.filter(recipient => 
                recipient.name.toLowerCase().includes(searchTerm) || 
                recipient.phone.includes(searchTerm)
            );
        }
        
        displayRecipients();
    };
    
    function displayRecipients() {
        if (filteredRecipients.length === 0) {
            recipientsContent.innerHTML = `
                <div class="text-center text-gray-400">
                    ${allRecipients.length === 0 ? 'No recipients found for this type' : 'No recipients match your search'}
                </div>
            `;
        } else {
            recipientsContent.innerHTML = filteredRecipients.map(recipient => `
                <div class="p-3 border-b border-gray-700 hover:bg-gray-800/50 cursor-pointer recipient-option" 
                     data-id="${recipient.id}" 
                     data-name="${recipient.name}" 
                     data-phone="${recipient.phone}"
                     onclick="selectRecipient(${JSON.stringify(recipient).replace(/"/g, '&quot;')})">
                    <div class="font-medium">${recipient.name}</div>
                    <div class="text-sm text-gray-400">${recipient.phone}</div>
                </div>
            `).join('');
        }
        
        // Update count
        recipientsCount.textContent = `${filteredRecipients.length} of ${allRecipients.length} recipients`;
    }
    
    window.selectRecipient = function(recipient) {
        selectedRecipient = recipient;
        phoneNumberInput.value = recipient.phone;
        recipientNameInput.value = recipient.name;
        
        // Update hidden fields for form submission
        document.getElementById('hidden_recipient_id').value = recipient.id;
        document.getElementById('hidden_recipient_name').value = recipient.name;
        
        // Update message with recipient name if placeholder exists
        if (messageTextarea.value.includes('{{name}}')) {
            messageTextarea.value = messageTextarea.value.replace(/\{\{name\}\}/g, recipient.name);
        }
        
        // Update character count
        charCount.textContent = messageTextarea.value.length;
        
        // Hide recipients list
        recipientsList.classList.add('hidden');
        
        // Clear search
        recipientSearch.value = '';
    };
    
    // Form validation function
    window.validateForm = function() {
        const phoneNumber = phoneNumberInput.value.trim();
        const message = messageTextarea.value.trim();
        const recipientId = document.getElementById('hidden_recipient_id').value;
        const recipientName = document.getElementById('hidden_recipient_name').value;
        
        console.log('Form validation - Phone:', phoneNumber, 'Message:', message, 'Recipient ID:', recipientId, 'Recipient Name:', recipientName);
        
        if (!phoneNumber) {
            alert('Phone number is required');
            phoneNumberInput.focus();
            return false;
        }
        
        if (!message) {
            alert('SMS message is required');
            messageTextarea.focus();
            return false;
        }
        
        // Auto-format phone number before submission
        let formattedPhone = phoneNumber.replace(/\D/g, '');
        if (formattedPhone.startsWith('0')) {
            formattedPhone = '254' + formattedPhone.substring(1);
        } else if (!formattedPhone.startsWith('254')) {
            formattedPhone = '254' + formattedPhone;
        }
        
        phoneNumberInput.value = formattedPhone;
        
        console.log('Form is valid, submitting...');
        console.log('Final form data - Phone:', formattedPhone, 'Message:', message);
        
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        }
        
        // Add a small delay to ensure the loading state is visible
        setTimeout(() => {
            console.log('Form submission proceeding...');
        }, 100);
        
        // Test if form is actually submitting
        console.log('Form validation passed, allowing submission...');
        
        return true;
    };
});
</script>

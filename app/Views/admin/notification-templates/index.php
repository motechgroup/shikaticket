<?php /** @var array $templates */ ?>
<div class="max-w-6xl mx-auto px-4 py-6 md:py-10">
	<?php $pageTitle = 'Notification Templates'; include __DIR__ . '/../../components/mobile_nav_simple.php'; ?>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Notification Templates</h1>
            <p class="text-gray-400">Manage SMS and email templates for feature approvals and password resets</p>
        </div>
        <a href="<?php echo base_url('/admin/featured-content'); ?>" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Featured Content
        </a>
    </div>

    <form method="POST" action="<?php echo base_url('/admin/notification-templates/update'); ?>" class="space-y-8">
        <?php echo csrf_field(); ?>
        
        <!-- SMS Templates -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white">SMS Templates</h2>
                <div class="text-sm text-gray-400">
                    Available placeholders: {event_title}, {organizer_name}, {destination_title}, {agency_name}
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Feature Approval Event SMS -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-300">
                        Feature Approval - Event
                    </label>
                    <textarea 
                        name="templates[sms][feature_approval_event]" 
                        rows="4" 
                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                        placeholder="SMS message for event feature approval..."
                    ><?php echo htmlspecialchars($templates['sms']['feature_approval_event']); ?></textarea>
                    <button type="button" onclick="previewTemplate('sms', 'feature_approval_event')" class="text-xs text-blue-400 hover:text-blue-300">
                        Preview Template
                    </button>
                </div>

                <!-- Feature Approval Destination SMS -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-300">
                        Feature Approval - Travel Destination
                    </label>
                    <textarea 
                        name="templates[sms][feature_approval_destination]" 
                        rows="4" 
                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                        placeholder="SMS message for destination feature approval..."
                    ><?php echo htmlspecialchars($templates['sms']['feature_approval_destination']); ?></textarea>
                    <button type="button" onclick="previewTemplate('sms', 'feature_approval_destination')" class="text-xs text-blue-400 hover:text-blue-300">
                        Preview Template
                    </button>
                </div>

                <!-- Password Reset SMS Templates -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-300">
                        Password Reset - User
                    </label>
                    <textarea 
                        name="templates[sms][password_reset_user]" 
                        rows="4" 
                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                        placeholder="SMS message for user password reset..."
                    ><?php echo htmlspecialchars($templates['sms']['password_reset_user']); ?></textarea>
                    <button type="button" onclick="previewTemplate('sms', 'password_reset_user')" class="text-xs text-blue-400 hover:text-blue-300">
                        Preview Template
                    </button>
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-300">
                        Password Reset - Organizer
                    </label>
                    <textarea 
                        name="templates[sms][password_reset_organizer]" 
                        rows="4" 
                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                        placeholder="SMS message for organizer password reset..."
                    ><?php echo htmlspecialchars($templates['sms']['password_reset_organizer']); ?></textarea>
                    <button type="button" onclick="previewTemplate('sms', 'password_reset_organizer')" class="text-xs text-blue-400 hover:text-blue-300">
                        Preview Template
                    </button>
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-300">
                        Password Reset - Travel Agency
                    </label>
                    <textarea 
                        name="templates[sms][password_reset_travel]" 
                        rows="4" 
                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                        placeholder="SMS message for travel agency password reset..."
                    ><?php echo htmlspecialchars($templates['sms']['password_reset_travel']); ?></textarea>
                    <button type="button" onclick="previewTemplate('sms', 'password_reset_travel')" class="text-xs text-blue-400 hover:text-blue-300">
                        Preview Template
                    </button>
                </div>
            </div>
        </div>

        <!-- Email Templates -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white">Email Templates</h2>
                <div class="text-sm text-gray-400">
                    Available placeholders: {event_title}, {organizer_name}, {destination_title}, {agency_name}, {event_url}, {destination_url}
                </div>
            </div>
            
            <div class="space-y-8">
                <!-- Feature Approval Event Email -->
                <div class="border border-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-white mb-4">Feature Approval - Event</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-300">Email Subject</label>
                            <input 
                                type="text" 
                                name="templates[email][feature_approval_event_subject]" 
                                value="<?php echo htmlspecialchars($templates['email']['feature_approval_event_subject']); ?>"
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                                placeholder="Email subject for event feature approval..."
                            >
                        </div>
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-300">Email Body</label>
                            <textarea 
                                name="templates[email][feature_approval_event_body]" 
                                rows="8" 
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                                placeholder="HTML email body for event feature approval..."
                            ><?php echo htmlspecialchars($templates['email']['feature_approval_event_body']); ?></textarea>
                        </div>
                    </div>
                    <button type="button" onclick="previewTemplate('email', 'feature_approval_event_body')" class="mt-2 text-sm text-blue-400 hover:text-blue-300">
                        Preview Email Body
                    </button>
                </div>

                <!-- Feature Approval Destination Email -->
                <div class="border border-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-white mb-4">Feature Approval - Travel Destination</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-300">Email Subject</label>
                            <input 
                                type="text" 
                                name="templates[email][feature_approval_destination_subject]" 
                                value="<?php echo htmlspecialchars($templates['email']['feature_approval_destination_subject']); ?>"
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                                placeholder="Email subject for destination feature approval..."
                            >
                        </div>
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-300">Email Body</label>
                            <textarea 
                                name="templates[email][feature_approval_destination_body]" 
                                rows="8" 
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500"
                                placeholder="HTML email body for destination feature approval..."
                            ><?php echo htmlspecialchars($templates['email']['feature_approval_destination_body']); ?></textarea>
                        </div>
                    </div>
                    <button type="button" onclick="previewTemplate('email', 'feature_approval_destination_body')" class="mt-2 text-sm text-blue-400 hover:text-blue-300">
                        Preview Email Body
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save All Templates
            </button>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-900 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Template Preview</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div id="previewContent" class="prose prose-invert max-w-none">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewTemplate(type, key) {
    fetch(`<?php echo base_url('/admin/notification-templates/preview'); ?>?type=${type}&key=${key}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            document.getElementById('previewContent').innerHTML = data.preview;
            document.getElementById('previewModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load preview');
        });
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreviewModal();
    }
});
</script>

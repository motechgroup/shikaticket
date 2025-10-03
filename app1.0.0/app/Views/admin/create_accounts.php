<?php 
/** @var array $settings */
$pageTitle = 'Create Accounts';
?>
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Create New Accounts</h1>
        <a class="btn btn-secondary" href="<?php echo base_url('/admin'); ?>">Back to Dashboard</a>
    </div>

    <!-- Account Type Selection -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <button onclick="showCreateForm('user')" class="card p-6 text-center hover:bg-gray-50 transition-colors">
            <div class="text-4xl mb-3">👤</div>
            <h3 class="text-lg font-semibold mb-2">Create User</h3>
            <p class="text-sm text-gray-600">Create a new customer account</p>
        </button>
        
        <button onclick="showCreateForm('organizer')" class="card p-6 text-center hover:bg-gray-50 transition-colors">
            <div class="text-4xl mb-3">🎪</div>
            <h3 class="text-lg font-semibold mb-2">Create Organizer</h3>
            <p class="text-sm text-gray-600">Create a new event organizer account</p>
        </button>
        
        <button onclick="showCreateForm('travel')" class="card p-6 text-center hover:bg-gray-50 transition-colors">
            <div class="text-4xl mb-3">✈️</div>
            <h3 class="text-lg font-semibold mb-2">Create Travel Agency</h3>
            <p class="text-sm text-gray-600">Create a new travel agency account</p>
        </button>
    </div>

    <!-- Create User Form -->
    <div id="user-form" class="create-form hidden">
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <span class="text-2xl">👤</span>
                Create New User Account
            </h2>
            
            <form method="post" action="<?php echo base_url('/admin/accounts/create-user'); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Full Name *</label>
                        <input type="text" name="full_name" class="input" required placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email Address *</label>
                        <input type="email" name="email" class="input" required placeholder="john@example.com">
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Phone Number</label>
                        <input type="text" name="phone" class="input" placeholder="+254 700 000 000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Password *</label>
                        <input type="password" name="password" class="input" required placeholder="Auto-generated if empty">
                        <div class="text-xs text-gray-500 mt-1">Leave empty to auto-generate a secure password</div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Send Credentials Via</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="send_email" value="1" checked class="mr-2">
                                <span class="text-sm">Email</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="send_sms" value="1" class="mr-2">
                                <span class="text-sm">SMS (if phone provided)</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Account Status</label>
                        <select name="is_active" class="input">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="btn btn-primary">Create User Account</button>
                    <button type="button" onclick="hideAllForms()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Organizer Form -->
    <div id="organizer-form" class="create-form hidden">
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <span class="text-2xl">🎪</span>
                Create New Organizer Account
            </h2>
            
            <form method="post" action="<?php echo base_url('/admin/accounts/create-organizer'); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Full Name *</label>
                        <input type="text" name="full_name" class="input" required placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email Address *</label>
                        <input type="email" name="email" class="input" required placeholder="john@example.com">
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Phone Number *</label>
                        <input type="text" name="phone" class="input" required placeholder="+254 700 000 000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Password *</label>
                        <input type="password" name="password" class="input" required placeholder="Auto-generated if empty">
                        <div class="text-xs text-gray-500 mt-1">Leave empty to auto-generate a secure password</div>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Commission Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="commission_percent" class="input" value="10.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Send Credentials Via</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="send_email" value="1" checked class="mr-2">
                                <span class="text-sm">Email</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="send_sms" value="1" checked class="mr-2">
                                <span class="text-sm">SMS</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Account Status</label>
                        <select name="is_approved" class="input">
                            <option value="1">Approved & Active</option>
                            <option value="0">Pending Approval</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="btn btn-primary">Create Organizer Account</button>
                    <button type="button" onclick="hideAllForms()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Travel Agency Form -->
    <div id="travel-form" class="create-form hidden">
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <span class="text-2xl">✈️</span>
                Create New Travel Agency Account
            </h2>
            
            <form method="post" action="<?php echo base_url('/admin/accounts/create-travel'); ?>" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Company Name *</label>
                        <input type="text" name="company_name" class="input" required placeholder="ABC Travel Agency">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Contact Person *</label>
                        <input type="text" name="contact_person" class="input" required placeholder="John Doe">
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Email Address *</label>
                        <input type="email" name="email" class="input" required placeholder="contact@abctravel.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Phone Number *</label>
                        <input type="text" name="phone" class="input" required placeholder="+254 700 000 000">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Password *</label>
                        <input type="password" name="password" class="input" required placeholder="Auto-generated if empty">
                        <div class="text-xs text-gray-500 mt-1">Leave empty to auto-generate a secure password</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Website</label>
                        <input type="url" name="website" class="input" placeholder="https://abctravel.com">
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">City</label>
                        <input type="text" name="city" class="input" placeholder="Nairobi">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Country</label>
                        <input type="text" name="country" class="input" placeholder="Kenya">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Commission Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="commission_rate" class="input" value="10.00">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" class="textarea" rows="3" placeholder="Brief description of the travel agency..."></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Logo</label>
                        <input type="file" name="logo" accept="image/*" class="input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Send Credentials Via</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="send_email" value="1" checked class="mr-2">
                                <span class="text-sm">Email</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="send_sms" value="1" checked class="mr-2">
                                <span class="text-sm">SMS</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Account Status</label>
                    <select name="is_approved" class="input">
                        <option value="1">Approved & Active</option>
                        <option value="0">Pending Approval</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <button type="submit" class="btn btn-primary">Create Travel Agency Account</button>
                    <button type="button" onclick="hideAllForms()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCreateForm(type) {
    // Hide all forms
    hideAllForms();
    
    // Show selected form
    const form = document.getElementById(type + '-form');
    if (form) {
        form.classList.remove('hidden');
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

function hideAllForms() {
    const forms = document.querySelectorAll('.create-form');
    forms.forEach(form => form.classList.add('hidden'));
}
</script>

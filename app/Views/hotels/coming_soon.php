<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels - Coming Soon | <?php echo htmlspecialchars($siteTitle); ?></title>
    <meta name="description" content="Join Kenya's premier hotel booking platform. Get early access to our hotel directory and start attracting more guests today.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981',
                        secondary: '#3b82f6',
                        accent: '#f59e0b'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        }
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .feature-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.1));
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Navigation -->
    <nav class="relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="<?php echo base_url('/'); ?>" class="flex items-center">
                        <img src="<?php echo base_url($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteTitle); ?>" class="h-10 w-auto">
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo base_url('/events'); ?>" class="text-gray-300 hover:text-white transition-colors">Events</a>
                    <a href="<?php echo base_url('/travel'); ?>" class="text-gray-300 hover:text-white transition-colors">Travel</a>
                    <a href="<?php echo base_url('/hotels'); ?>" class="text-green-400 font-semibold">Hotels</a>
                    <a href="<?php echo base_url('/login'); ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Coming Soon Badge -->
                <div class="inline-flex items-center px-6 py-3 rounded-full glass-effect mb-8">
                    <div class="w-3 h-3 bg-green-400 rounded-full pulse-animation mr-3"></div>
                    <span class="text-green-400 font-semibold">Coming Soon</span>
                </div>
                
                <!-- Main Heading -->
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6">
                    Hotels Are
                    <span class="bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                        Coming Soon
                    </span>
                </h1>
                
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Join Kenya's premier hotel booking platform and start attracting more guests with our proven track record of success.
                </p>
                
                <!-- Stats removed -->
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Why Hotel Owners Trust Us
                </h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    We've already proven our platform works with events and travel. Now we're bringing the same success to hotels.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Proven Track Record -->
                <div class="glass-effect rounded-2xl p-8 hover-lift">
                    <div class="feature-icon text-4xl mb-4">📊</div>
                    <h3 class="text-xl font-bold text-white mb-3">Proven Track Record</h3>
                    <p class="text-gray-300">
                        We've successfully facilitated travel bookings and events,
                        generating meaningful revenue for our partners — bringing that experience to hotels.
                    </p>
                </div>

                <!-- Secure Payment System -->
                <div class="glass-effect rounded-2xl p-8 hover-lift">
                    <div class="feature-icon text-4xl mb-4">🔒</div>
                    <h3 class="text-xl font-bold text-white mb-3">Secure Payment System</h3>
                    <p class="text-gray-300">
                        Our M-Pesa integration and secure payment gateway ensure your guests can book with confidence. 
                        All transactions are encrypted and PCI compliant.
                    </p>
                </div>

                <!-- Mobile-First Platform -->
                <div class="glass-effect rounded-2xl p-8 hover-lift">
                    <div class="feature-icon text-4xl mb-4">📱</div>
                    <h3 class="text-xl font-bold text-white mb-3">Mobile-First Platform</h3>
                    <p class="text-gray-300">
                        Built for the Kenyan market with mobile-first design. Your guests can browse and book 
                        directly from their phones with our optimized booking flow.
                    </p>
                </div>

                <!-- Real-Time Management -->
                <div class="glass-effect rounded-2xl p-8 hover-lift">
                    <div class="feature-icon text-4xl mb-4">⚡</div>
                    <h3 class="text-xl font-bold text-white mb-3">Real-Time Management</h3>
                    <p class="text-gray-300">
                        Manage your room inventory, pricing, and bookings in real-time. Get instant notifications 
                        for new bookings and guest communications.
                    </p>
                </div>

                <!-- Marketing Support -->
                <div class="glass-effect rounded-2xl p-8 hover-lift">
                    <div class="feature-icon text-4xl mb-4">📢</div>
                    <h3 class="text-xl font-bold text-white mb-3">Marketing Support</h3>
                    <p class="text-gray-300">
                        We'll help promote your hotel through our existing user base and marketing channels. 
                        Get featured in our newsletters and social media campaigns.
                    </p>
                </div>

                <!-- 24/7 Customer Support -->
                <div class="glass-effect rounded-2xl p-8 hover-lift">
                    <div class="feature-icon text-4xl mb-4">🎧</div>
                    <h3 class="text-xl font-bold text-white mb-3">24/7 Customer Support</h3>
                    <p class="text-gray-300">
                        Our dedicated support team helps both you and your guests. From booking issues to 
                        payment problems, we're here to help around the clock.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Features -->
    <section class="py-20 bg-black/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Powerful Platform Features
                </h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Everything you need to manage your hotel bookings efficiently and grow your business.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Easy Room Management</h3>
                                <p class="text-gray-300">Add rooms, set availability, manage pricing, and handle seasonal rates with our intuitive dashboard.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Automated Booking Flow</h3>
                                <p class="text-gray-300">Guests can browse, select rooms, make payments, and receive instant confirmation - all automated.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Guest Communication</h3>
                                <p class="text-gray-300">Send booking confirmations, check-in instructions, and promotional offers via SMS and email.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Analytics & Reports</h3>
                                <p class="text-gray-300">Track your performance with detailed analytics on bookings, revenue, occupancy rates, and guest feedback.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="glass-effect rounded-2xl p-8">
                        <div class="text-center">
                            <div class="text-6xl mb-4">🏨</div>
                            <h3 class="text-2xl font-bold text-white mb-4">Early Access Benefits</h3>
                            <ul class="text-left space-y-3 text-gray-300">
                                <li class="flex items-center space-x-3">
                                    <span class="text-green-400">✓</span>
                                    <span>Free setup and onboarding</span>
                                </li>
                                <li class="flex items-center space-x-3">
                                    <span class="text-green-400">✓</span>
                                    <span>Reduced commission for first 6 months</span>
                                </li>
                                <li class="flex items-center space-x-3">
                                    <span class="text-green-400">✓</span>
                                    <span>Priority customer support</span>
                                </li>
                                <li class="flex items-center space-x-3">
                                    <span class="text-green-400">✓</span>
                                    <span>Featured listing placement</span>
                                </li>
                                <li class="flex items-center space-x-3">
                                    <span class="text-green-400">✓</span>
                                    <span>Marketing campaign inclusion</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Form Section -->
    <section class="py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Apply to Join Our Platform
                </h2>
                <p class="text-xl text-gray-300">
                    Be among the first hotels to join Kenya's premier booking platform. Limited early access spots available.
                </p>
            </div>

            <div class="glass-effect rounded-2xl p-8 md:p-12">
                <?php if (flash_get('success')): ?>
                    <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg">
                        <p class="text-green-300"><?php echo htmlspecialchars(flash_get('success')); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (flash_get('error')): ?>
                    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                        <p class="text-red-300"><?php echo htmlspecialchars(flash_get('error')); ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo base_url('/hotels/apply'); ?>" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="hotel_name" class="block text-sm font-medium text-white mb-2">Hotel Name *</label>
                            <input type="text" id="hotel_name" name="hotel_name" required 
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   placeholder="Enter your hotel name">
                        </div>
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-white mb-2">Contact Person *</label>
                            <input type="text" id="contact_person" name="contact_person" required 
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   placeholder="Your full name">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-white mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   placeholder="your@email.com">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-white mb-2">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required 
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   placeholder="254700000000">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="location" class="block text-sm font-medium text-white mb-2">Location *</label>
                            <input type="text" id="location" name="location" required 
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   placeholder="e.g., Nairobi, Mombasa, Kisumu">
                        </div>
                        <div>
                            <label for="rooms" class="block text-sm font-medium text-white mb-2">Number of Rooms *</label>
                            <input type="number" id="rooms" name="rooms" required min="1" 
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   placeholder="e.g., 25">
                        </div>
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-white mb-2">Website (Optional)</label>
                        <input type="url" id="website" name="website" 
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                               placeholder="yourhotel.com (https:// will be added automatically)">
                    </div>

                    <div>
                        <label for="experience" class="block text-sm font-medium text-white mb-2">Hotel Experience</label>
                        <textarea id="experience" name="experience" rows="4" 
                                  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                  placeholder="Tell us about your hotel - facilities, amenities, unique features, etc."></textarea>
                    </div>

                    <div>
                        <label for="why_interested" class="block text-sm font-medium text-white mb-2">Why are you interested in joining our platform?</label>
                        <textarea id="why_interested" name="why_interested" rows="4" 
                                  class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                  placeholder="What benefits do you hope to gain from joining our platform?"></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" 
                                class="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-semibold px-12 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            Apply for Early Access
                        </button>
                        <p class="text-sm text-gray-400 mt-4">
                            We'll review your application and get back to you within 2-3 business days.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Success Popup Modal -->
    <div id="successPopup" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Modal Content -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md mx-auto">
                <!-- Modal Card -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden transform transition-all duration-500 scale-95 opacity-0" id="modalCard">
                    <!-- Header with Animation -->
                    <div class="relative p-8 text-center">
                        <!-- Success Icon with Animation -->
                        <div class="relative mb-6">
                            <div class="w-20 h-20 mx-auto bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-10 h-10 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <!-- Pulse Ring -->
                            <div class="absolute inset-0 w-20 h-20 mx-auto bg-green-500/20 rounded-full animate-ping"></div>
                        </div>
                        
                        <!-- Success Title -->
                        <h3 class="text-2xl font-bold text-white mb-2">Application Submitted!</h3>
                        <p class="text-gray-300 text-sm">Thank you for your interest in joining our platform</p>
                    </div>
                    
                    <!-- Content -->
                    <div class="px-8 pb-8">
                        <!-- Status Card -->
                        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-xl p-6 mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold">What's Next?</h4>
                                    <p class="text-gray-400 text-sm">We'll review your application</p>
                                </div>
                            </div>
                            
                            <!-- Timeline -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-300 text-sm">Application received successfully</span>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-300 text-sm">Review in progress (2-3 business days)</span>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-400 text-sm">Response via email</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Info -->
                        <div class="bg-gradient-to-r from-green-500/10 to-blue-500/10 border border-green-500/20 rounded-xl p-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white text-sm font-medium">Questions?</p>
                                    <p class="text-gray-400 text-xs">Contact us if you need assistance</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button onclick="closeSuccessPopup()" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                                Got It!
                            </button>
                            <button onclick="window.location.href='<?php echo base_url('/'); ?>'" class="px-6 py-3 border border-gray-600 hover:border-gray-500 text-gray-300 hover:text-white rounded-lg transition-all duration-300">
                                Home
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black/40 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <img src="<?php echo base_url($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteTitle); ?>" class="h-8 w-auto">
                </div>
                <p class="text-gray-400 mb-6">
                    Kenya's premier platform for events, travel, and soon - hotels.
                </p>
                <div class="flex justify-center space-x-8">
                    <a href="<?php echo base_url('/events'); ?>" class="text-gray-400 hover:text-white transition-colors">Events</a>
                    <a href="<?php echo base_url('/travel'); ?>" class="text-gray-400 hover:text-white transition-colors">Travel</a>
                    <a href="<?php echo base_url('/hotels'); ?>" class="text-green-400 font-semibold">Hotels</a>
                    <a href="<?php echo base_url('/help'); ?>" class="text-gray-400 hover:text-white transition-colors">Help</a>
                </div>
                <p class="text-gray-500 text-sm mt-6">
                    © <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteTitle); ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for form enhancements -->
    <script>
        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = '254' + value.substring(1);
            } else if (!value.startsWith('254')) {
                value = '254' + value;
            }
            e.target.value = value;
        });

        // Website auto-fill with https://
        document.getElementById('website').addEventListener('input', function(e) {
            let value = e.target.value.trim();
            
            // Only auto-fill if the field is not empty and doesn't already have a protocol
            if (value && !value.match(/^https?:\/\//i)) {
                // Remove any existing protocol if user typed one
                value = value.replace(/^https?:\/\//i, '');
                // Add https:// prefix
                e.target.value = 'https://' + value;
            }
        });

        // Website field focus handler to show placeholder behavior
        document.getElementById('website').addEventListener('focus', function(e) {
            let value = e.target.value.trim();
            if (!value) {
                e.target.value = 'https://';
                // Position cursor after https://
                setTimeout(() => {
                    e.target.setSelectionRange(8, 8);
                }, 0);
            }
        });

        // Website field blur handler to clean up
        document.getElementById('website').addEventListener('blur', function(e) {
            let value = e.target.value.trim();
            // If only https:// is left, clear the field
            if (value === 'https://') {
                e.target.value = '';
            }
        });

        // Success popup functions
        function showSuccessPopup() {
            const popup = document.getElementById('successPopup');
            const modalCard = document.getElementById('modalCard');
            
            popup.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Trigger animation
            setTimeout(() => {
                modalCard.classList.remove('scale-95', 'opacity-0');
                modalCard.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeSuccessPopup() {
            const popup = document.getElementById('successPopup');
            const modalCard = document.getElementById('modalCard');
            
            modalCard.classList.remove('scale-100', 'opacity-100');
            modalCard.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                popup.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        // Close popup when clicking backdrop
        document.getElementById('successPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuccessPopup();
            }
        });

        // Close popup with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuccessPopup();
            }
        });

        // Form validation and submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = ['hotel_name', 'contact_person', 'email', 'phone', 'location', 'rooms'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state on submit button
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
            `;
            submitBtn.disabled = true;

            // Submit form via fetch
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            fetch(e.target.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.log('Raw JSON response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            console.log('Failed to parse JSON. Raw text:', text);
                            throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                        }
                    });
                } else {
                    // If not JSON, read as text to see what we got
                    return response.text().then(text => {
                        console.log('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response: ' + text.substring(0, 100));
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    // Show success popup
                    showSuccessPopup();
                    // Reset form
                    e.target.reset();
                } else {
                    alert(data.message || 'There was an error submitting your application. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your application. Please refresh the page and try again.');
            })
            .finally(() => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>

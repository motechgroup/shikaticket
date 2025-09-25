<?php
$pageTitle = 'Travel Agency Registration';
ob_start();
?>
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-red-100">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Register Your Travel Agency
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Join our platform and start listing your travel destinations
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                    <input id="company_name" name="company_name" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                           placeholder="Your travel company name">
                </div>
                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person *</label>
                    <input id="contact_person" name="contact_person" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                           placeholder="Full name of contact person">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                           placeholder="your@company.com">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                    <input id="phone" name="phone" type="tel" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                           placeholder="700 000 000">
                </div>
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                    <input id="website" name="website" type="url" 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                           placeholder="yourcompany.com">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                    <select id="country" name="country" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"></select>
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <select id="city" name="city" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"></select>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                           placeholder="Minimum 6 characters">
                </div>
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea id="address" name="address" rows="2" 
                          class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                          placeholder="Physical address of your company"></textarea>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Company Description</label>
                <textarea id="description" name="description" rows="3" 
                          class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm" 
                          placeholder="Tell us about your travel agency, specialties, and experience..."></textarea>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Register Travel Agency
                </button>
            </div>

            <div class="text-center">
                <a href="<?php echo base_url('/travel/login'); ?>" class="text-sm text-red-600 hover:text-red-500">
                    Already have an account? Sign in
                </a>
            </div>

            <div class="text-center">
                <a href="<?php echo base_url('/'); ?>" class="text-sm text-gray-600 hover:text-gray-500">
                    ← Back to main site
                </a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
?>
<script>
// Minimal country/city data and dial codes
const COUNTRY_DATA = {
  "Kenya": { code: "+254", cities: ["Nairobi","Mombasa","Kisumu","Nakuru","Eldoret"] },
  "Tanzania": { code: "+255", cities: ["Dar es Salaam","Dodoma","Arusha","Mwanza"] },
  "Uganda": { code: "+256", cities: ["Kampala","Entebbe","Jinja","Gulu"] },
  "Rwanda": { code: "+250", cities: ["Kigali","Huye","Musanze"] },
  "South Africa": { code: "+27", cities: ["Johannesburg","Cape Town","Durban","Pretoria"] },
  "Zambia": { code: "+260", cities: ["Lusaka","Ndola","Kitwe"] },
  "Malawi": { code: "+265", cities: ["Lilongwe","Blantyre","Mzuzu"] }
};

document.addEventListener('DOMContentLoaded', function(){
  const country = document.getElementById('country');
  const city = document.getElementById('city');
  const phone = document.getElementById('phone');
  const website = document.getElementById('website');

  // Populate countries
  if(country){
    country.innerHTML = '<option value="" disabled selected>Select country</option>' +
      Object.keys(COUNTRY_DATA).map(c => `<option value="${c}">${c}</option>`).join('');
  }

  // When country changes, set dial code and cities
  function onCountryChange(){
    const c = COUNTRY_DATA[country.value];
    if(!c) return;
    // Ensure phone starts with dial code
    if(phone){
      const digits = phone.value.replace(/\D+/g,'');
      phone.value = c.code + ' ' + digits.replace(/^0+/, '');
    }
    // Populate cities
    if(city){
      city.innerHTML = c.cities.map(ct => `<option value="${ct}">${ct}</option>`).join('');
    }
  }
  country?.addEventListener('change', onCountryChange);

  // Normalize website to https
  function normalizeWebsite(){
    if(!website || !website.value) return;
    const v = website.value.trim();
    if(!/^https?:\/\//i.test(v)) {
      website.value = 'https://' + v;
    }
  }
  website?.addEventListener('blur', normalizeWebsite);

  // Normalize phone on blur (keep dial code if present)
  function normalizePhone(){
    if(!phone || !phone.value) return;
    const c = COUNTRY_DATA[country?.value];
    const digits = phone.value.replace(/\D+/g,'');
    if(c){
      const trimmed = digits.replace(/^0+/, '');
      phone.value = c.code + ' ' + trimmed;
    }
  }
  phone?.addEventListener('blur', normalizePhone);
});
</script>
<?php
include __DIR__ . '/../../layouts/travel.php';

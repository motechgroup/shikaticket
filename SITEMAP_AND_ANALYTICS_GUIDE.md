# Sitemap and Analytics Integration Guide

## Overview
This guide covers the newly implemented sitemap and analytics tracking features for ShikaTicket.

---

## 🗺️ Sitemap Feature

### What is it?
An XML sitemap that helps search engines discover and index all pages on your website, improving SEO and visibility.

### Access the Sitemap
**URL:** `https://shikaticket.com/sitemap.xml`

### What's Included?
The sitemap automatically includes:

1. **Static Pages:**
   - Homepage (/)
   - Events Listing (/events)
   - Travel Destinations (/travel)
   - Partners (/partners)
   - Hotels (/hotels)
   - Login/Register Pages

2. **Dynamic Content:**
   - All published events with their individual URLs
   - All published travel destinations
   - CMS pages (if you add them)

3. **SEO Properties:**
   - `<loc>` - URL location
   - `<lastmod>` - Last modification date
   - `<changefreq>` - Update frequency
   - `<priority>` - Page importance (0.0 - 1.0)

### Submit to Search Engines

**Google Search Console:**
1. Go to https://search.google.com/search-console
2. Add your property (shikaticket.com)
3. Click "Sitemaps" in the sidebar
4. Enter: `sitemap.xml`
5. Click "Submit"

**Bing Webmaster Tools:**
1. Go to https://www.bing.com/webmasters
2. Add your site
3. Navigate to "Sitemaps"
4. Submit: `https://shikaticket.com/sitemap.xml`

### Automatic Updates
The sitemap updates automatically when you:
- ✅ Publish new events
- ✅ Update existing events
- ✅ Publish new travel destinations
- ✅ Create CMS pages

---

## 📊 Analytics & Tracking

### Admin Settings
Navigate to: **Admin Panel → Settings → Analytics Tab**

### Supported Platforms

#### 1. **Google Analytics (GA4)**
- **Field:** Google Analytics Measurement ID
- **Format:** `G-XXXXXXXXXX` (GA4) or `UA-XXXXXXXXX-X` (Universal)
- **Get your ID:**
  1. Go to https://analytics.google.com
  2. Create a property for shikaticket.com
  3. Copy the Measurement ID from Admin → Data Streams

#### 2. **Google Tag Manager (GTM)**
- **Field:** Google Tag Manager ID
- **Format:** `GTM-XXXXXXX`
- **Get your ID:**
  1. Go to https://tagmanager.google.com
  2. Create a container
  3. Copy the Container ID

**Why use GTM?** Manage all tracking codes (GA, ads, pixels) from one dashboard without editing code.

#### 3. **Facebook Pixel**
- **Field:** Facebook Pixel ID
- **Format:** `1234567890123456` (numeric)
- **Get your ID:**
  1. Go to https://business.facebook.com
  2. Navigate to Events Manager
  3. Create a pixel
  4. Copy the Pixel ID

**Tracks:** Page views, purchases, event bookings for Facebook Ads optimization.

#### 4. **Custom Head Code**
- **What:** Any tracking script that goes in the `<head>` section
- **Use for:**
  - Microsoft Clarity
  - Hotjar
  - Crazy Egg
  - Custom analytics
  - Verification codes (Google, Bing)

**Example:**
```html
<!-- Microsoft Clarity -->
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "YOUR_PROJECT_ID");
</script>
```

#### 5. **Custom Body Code**
- **What:** Scripts that need to load right after `<body>` opens
- **Use for:**
  - Chat widgets (Intercom, Drift, Tawk.to)
  - TikTok Pixel
  - LinkedIn Insight Tag
  - Custom tracking

**Example:**
```html
<!-- Tawk.to Live Chat -->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/YOUR_PROPERTY_ID/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
```

### How It Works

1. **Admin enters tracking codes** in Settings → Analytics
2. **Codes are saved** to the database
3. **Main layout automatically loads** the codes on every page
4. **All pages track** user behavior automatically

### Where Tracking Codes Appear

**In `<head>` section:**
- Google Tag Manager
- Google Analytics
- Facebook Pixel
- Custom Head Code

**After `<body>` tag:**
- Google Tag Manager (noscript)
- Custom Body Code

### Privacy Considerations

**GDPR Compliance:**
- Add a cookie consent banner if targeting EU users
- Update your Privacy Policy
- Consider using cookie-less analytics

**Recommended Cookie Consent Tools:**
- Cookiebot
- OneTrust
- Termly

---

## 🎯 Recommended Setup

### For Basic Analytics:
1. **Google Analytics** - Track all user behavior
2. **Google Search Console** - Submit sitemap, monitor SEO

### For Marketing:
1. **Google Tag Manager** - Centralized tag management
2. **Facebook Pixel** - Track conversions for Facebook Ads
3. **Microsoft Clarity** - Free heatmaps and session recordings

### For Customer Support:
1. **Tawk.to** (Custom Body Code) - Free live chat
2. **Hotjar** (Custom Head Code) - User feedback and surveys

---

## 📈 Testing Your Setup

### Test Google Analytics:
1. Enter your GA4 ID in settings
2. Save settings
3. Visit your website
4. Open GA4 in another tab
5. Check "Realtime" → "Overview"
6. You should see yourself as an active user

### Test Facebook Pixel:
1. Install "Facebook Pixel Helper" Chrome extension
2. Visit your website
3. Click the extension icon
4. It should show your pixel is firing

### Test Sitemap:
1. Visit: `https://shikaticket.com/sitemap.xml`
2. You should see XML with all your URLs
3. Check for errors (should be properly formatted XML)

---

## 🔧 Troubleshooting

### Sitemap not showing pages:
- **Check:** Are events/destinations marked as `is_published=1` in database?
- **Solution:** Publish pages from admin panel

### Analytics not tracking:
- **Check:** Did you save settings?
- **Check:** Is the ID format correct?
- **Check:** Browser ad blockers can block tracking
- **Solution:** Test in incognito mode or another browser

### Custom code not working:
- **Check:** Is the code properly formatted HTML/JavaScript?
- **Check:** Are there any console errors? (F12 → Console)
- **Solution:** Validate your code before pasting

---

## 📝 Important Notes

1. **Analytics IDs are sensitive** - Don't share them publicly
2. **Test in incognito mode** - Ad blockers can interfere
3. **Sitemap updates automatically** - No manual regeneration needed
4. **Custom code runs on ALL pages** - Test thoroughly
5. **GTM vs GA:** Use GTM if you plan to add multiple tracking tools

---

## 🚀 Next Steps

### After Setup:
1. ✅ Submit sitemap to Google Search Console
2. ✅ Set up Google Analytics goals (ticket purchases, bookings)
3. ✅ Configure Facebook Pixel events (Purchase, ViewContent)
4. ✅ Add conversion tracking for ads
5. ✅ Set up weekly analytics reports

### Monitor:
- **Google Analytics:** User behavior, traffic sources
- **Search Console:** Search rankings, click-through rates
- **Facebook Events Manager:** Conversion tracking

---

## 🆘 Support

If you encounter issues:
1. Check browser console for errors (F12)
2. Verify IDs are correct format
3. Test with pixel helpers/debuggers
4. Clear browser cache and test again

---

**Last Updated:** January 2025  
**Version:** 1.1.3


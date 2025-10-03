-- Update richer legal pages content (idempotent)

-- Terms & Conditions
UPDATE pages SET 
  title = 'Terms & Conditions',
  content = '<h2>Terms &amp; Conditions</h2><p>Welcome to ShikaTicket. By using our platform, you agree to these terms.</p><h3>1. Using ShikaTicket</h3><ul><li>You must provide accurate information.</li><li>Do not attempt to abuse, bypass, or interfere with our systems.</li><li>Event listings and travel destinations must comply with local laws.</li></ul><h3>2. Tickets &amp; Bookings</h3><ul><li>All orders are subject to availability and payment confirmation.</li><li>Tickets are unique to the purchaser and may require ID at entry.</li><li>Misuse or resale at markup may result in cancellation.</li></ul><h3>3. Organizer &amp; Agency Responsibilities</h3><ul><li>Ensure event/destination details are accurate (dates, venue, pricing).</li><li>Maintain safety and comply with regulations.</li></ul><h3>4. Liability</h3><p>To the extent permitted by law, ShikaTicket is not liable for indirect or consequential losses. Our total liability is limited to the amount paid to us for the affected transaction.</p><h3>5. Changes</h3><p>We may update these terms. Material changes will be communicated via the site.</p>',
  is_published = 1
WHERE slug = 'terms-and-conditions';

-- Privacy Policy
UPDATE pages SET 
  title = 'Privacy Policy',
  content = '<h2>Privacy Policy</h2><p>Your privacy matters. This policy explains what we collect and why.</p><h3>Information We Collect</h3><ul><li>Account data (email, phone), and profile data.</li><li>Order and booking details, device and usage data for security.</li></ul><h3>How We Use Data</h3><ul><li>Process orders, send confirmations, prevent fraud, improve services.</li><li>Communicate important updates; you can manage preferences.</li></ul><h3>Sharing</h3><p>We share with payment providers, SMS/email gateways, and organizers/agencies to fulfill your bookings. We never sell personal data.</p><h3>Security</h3><p>We use encryption and best practices; no method is 100% secure.</p><h3>Your Rights</h3><p>Request access, correction, or deletion via Support. We retain data to comply with legal obligations.</p>',
  is_published = 1
WHERE slug = 'privacy-policy';

-- Refund Policy
UPDATE pages SET 
  title = 'Refund Policy',
  content = '<h2>Refund Policy</h2><p>We aim to make refunds fair and transparent.</p><h3>Eligibility</h3><ul><li>Event cancellation or significant change by organizer.</li><li>Travel destination cancellation or unavailability.</li></ul><h3>Exclusions</h3><ul><li>Change of mind after order confirmation.</li><li>No-shows or late arrivals beyond the check-in window.</li></ul><h3>Process</h3><ol><li>Submit a request within 48 hours of the scheduled time.</li><li>Provide order reference and reason; we verify with the organizer/agency.</li><li>Approved refunds are processed to the original payment method within 7–14 business days.</li></ol><h3>Partial Refunds</h3><p>For multi-item orders, applicable lines may be partially refunded.</p><h3>Contact</h3><p>For assistance, contact Support via the Help page.</p>',
  is_published = 1
WHERE slug = 'refund-policy';



INSERT INTO pages (slug, title, content, is_published)
SELECT 'advertise-with-us', 'Advertise with Us',
  '<h2>Advertise with Us</h2>
   <p>Reach thousands of event‑goers, travelers and vendors across Kenya every month through ShikaTicket placements.</p>
   <h3>Why ShikaTicket</h3>
   <ul>
     <li>Highly targeted audience interested in events, travel and experiences</li>
     <li>Premium placements on homepage, event pages and travel listings</li>
     <li>Flexible formats: banner, sponsored card, newsletter, push notifications</li>
   </ul>
   <h3>Formats & Rates</h3>
   <table>
     <tr><th>Placement</th><th>Format</th><th>Billing</th></tr>
     <tr><td>Homepage hero banner</td><td>1920x600 / WebP</td><td>Weekly</td></tr>
     <tr><td>Homepage sponsored card</td><td>Poster 800x800</td><td>CPC/Weekly</td></tr>
     <tr><td>Travel landing banner</td><td>1920x500</td><td>Weekly</td></tr>
     <tr><td>Newsletter feature</td><td>Hero + CTA</td><td>Per send</td></tr>
   </table>
   <h3>Booking & Specs</h3>
   <p>Email <strong>ads@shikaticket.com</strong> with your brand, objective, preferred dates and budget. We provide a proposal within 2 business days.</p>
   <p>Creative specs: WebP/PNG, max 500KB, include destination URL and UTM parameters.</p>
   <h3>Policies</h3>
   <ul>
     <li>No adult, misleading or illegal content</li>
     <li>All claims must be substantiated</li>
     <li>Prepayment required to reserve inventory</li>
   </ul>',
  1
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE slug='advertise-with-us');

-- Ensure existing installs can update title (keep content if admin customized)
UPDATE pages SET title='Advertise with Us' WHERE slug='advertise-with-us';



INSERT INTO pages (slug, title, content, is_published)
SELECT 'blog', 'Blog', '<p>Welcome to our blog.</p>', 1
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE slug='blog');

INSERT INTO pages (slug, title, content, is_published)
SELECT 'terms-and-conditions', 'Terms &amp; Conditions', '<h3>Terms &amp; Conditions</h3><p>Update this content in Admin &gt; Pages.</p>', 1
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE slug='terms-and-conditions');

INSERT INTO pages (slug, title, content, is_published)
SELECT 'privacy-policy', 'Privacy Policy', '<h3>Privacy Policy</h3><p>Update this content in Admin &gt; Pages.</p>', 1
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE slug='privacy-policy');

INSERT INTO pages (slug, title, content, is_published)
SELECT 'refund-policy', 'Refund Policy', '<h3>Refund Policy</h3><p>Update this content in Admin &gt; Pages.</p>', 1
WHERE NOT EXISTS (SELECT 1 FROM pages WHERE slug='refund-policy');



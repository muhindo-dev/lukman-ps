USE lukman_php;

-- Ensure a default admin account exists for FK relations in seed records.
INSERT INTO admin_users (username, password, full_name, email, status)
SELECT
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'System Administrator',
    'admin@lukmanps.ac.ug',
    'active'
WHERE NOT EXISTS (
    SELECT 1 FROM admin_users WHERE username = 'admin' OR email = 'admin@lukmanps.ac.ug'
);

-- Team members (real school leadership context + high-quality defaults).
INSERT INTO team_members (name, position, department, bio, qualification, photo, email, phone, display_order, status)
SELECT
    'Mr. Lubega Ibrahim',
    'Head Teacher',
    'management',
    'Head Teacher of Lukman Primary School, championing academic excellence and holistic child development grounded in Islamic values.',
    'B.Ed, Dip. Education Management',
    'lubega-ibrahim.jpeg',
    'headteacher@lukmanps.ac.ug',
    '',
    1,
    'active'
WHERE NOT EXISTS (SELECT 1 FROM team_members WHERE name = 'Mr. Lubega Ibrahim');

INSERT INTO team_members (name, position, department, bio, qualification, photo, email, phone, display_order, status)
SELECT
    'Hajj Ahamad Bisegerwa',
    'Chairperson, School Management Committee',
    'management',
    'Provides strategic direction, governance oversight, and community partnerships to strengthen school outcomes.',
    'School Governance & Community Leadership',
    'team/mr-ahmad-bisegerwa.jpg',
    'chairperson@lukmanps.ac.ug',
    '+256772100221',
    2,
    'active'
WHERE NOT EXISTS (SELECT 1 FROM team_members WHERE name = 'Hajj Ahamad Bisegerwa');

INSERT INTO team_members (name, position, department, bio, qualification, photo, email, phone, display_order, status)
SELECT
    'Hajat Joweria Bagonza',
    'Director',
    'management',
    'Supports school growth initiatives, quality assurance, and sustainable institutional development.',
    'Educational Leadership & Policy',
    'team/hajat-joweria-bagonza.jpg',
    'director@lukmanps.ac.ug',
    '+256701220332',
    3,
    'active'
WHERE NOT EXISTS (SELECT 1 FROM team_members WHERE name = 'Hajat Joweria Bagonza');

INSERT INTO team_members (name, position, department, bio, qualification, photo, email, phone, display_order, status)
SELECT
    'Lukwago',
    'Senior Teacher',
    'teaching',
    'Long-serving teacher supporting classroom excellence, discipline and learner mentorship.',
    'Dip. Primary Education',
    'team/lukwago.jpeg',
    'academics@lukmanps.ac.ug',
    '+256782445110',
    4,
    'active'
WHERE NOT EXISTS (SELECT 1 FROM team_members WHERE name = 'Lukwago');

-- Ensure legacy placeholder record is aligned with old-system team profile.
UPDATE team_members
SET
    name = 'Lukwago',
    position = 'Senior Teacher',
    department = 'teaching',
    bio = 'Long-serving teacher supporting classroom excellence, discipline and learner mentorship.',
    qualification = 'Dip. Primary Education',
    photo = 'team/lukwago.jpeg',
    email = 'academics@lukmanps.ac.ug',
    phone = '+256782445110',
    display_order = 4,
    status = 'active'
WHERE name = 'Ustadh Muhammad Kato';

-- Keep team photos mapped correctly for homepage and admin modules.
UPDATE team_members SET photo = 'lubega-ibrahim.jpeg' WHERE name = 'Mr. Lubega Ibrahim';
UPDATE team_members SET photo = 'team/mr-ahmad-bisegerwa.jpg' WHERE name = 'Hajj Ahamad Bisegerwa';
UPDATE team_members SET photo = 'team/hajat-joweria-bagonza.jpg' WHERE name = 'Hajat Joweria Bagonza';
UPDATE team_members SET photo = 'team/lukwago.jpeg' WHERE name = 'Lukwago';

-- Remove any accidental duplicate records for Lukwago, keeping the oldest row.
DELETE FROM team_members
WHERE name = 'Lukwago'
    AND id NOT IN (
        SELECT id FROM (
            SELECT MIN(id) AS id FROM team_members WHERE name = 'Lukwago'
        ) AS keep_row
    );

-- Testimonials.
INSERT INTO testimonials (name, role, content, photo, rating, display_order, status)
SELECT
    'Amina Nansubuga',
    'Parent',
    'Lukman Primary School has nurtured both the academic strength and discipline of my child. The teachers are caring, structured and very supportive.',
    NULL,
    5,
    1,
    'active'
WHERE NOT EXISTS (SELECT 1 FROM testimonials WHERE name = 'Amina Nansubuga' AND role = 'Parent');

INSERT INTO testimonials (name, role, content, photo, rating, display_order, status)
SELECT
    'Hassan Sserwanga',
    'Old Student',
    'The dual curriculum shaped my confidence and values. I am grateful for the mentorship and strong foundation I received here.',
    NULL,
    5,
    2,
    'active'
WHERE NOT EXISTS (SELECT 1 FROM testimonials WHERE name = 'Hassan Sserwanga' AND role = 'Old Student');

INSERT INTO testimonials (name, role, content, photo, rating, display_order, status)
SELECT
    'Najjemba Mariam',
    'Parent Representative',
    'The school communication is excellent and the learning environment is safe and focused. We have seen steady progress in our children.',
    NULL,
    5,
    3,
    'active'
WHERE NOT EXISTS (SELECT 1 FROM testimonials WHERE name = 'Najjemba Mariam' AND role = 'Parent Representative');

-- Latest news cards.
INSERT INTO news_posts (title, slug, excerpt, content, featured_image, author_id, category, tags, status, views, published_at)
SELECT
    'Lukman Primary School Opens New Learning Resource Room',
    'new-learning-resource-room-2026',
    'The school has launched a new resource room to strengthen reading culture and guided research.',
    'Lukman Primary School has officially opened a new learning resource room designed to improve literacy and guided research for learners across all levels. The room includes curriculum books, revision materials and supervised study support sessions.',
    NULL,
    (SELECT id FROM admin_users ORDER BY id ASC LIMIT 1),
    'Academics',
    'academics,library,school-development',
    'published',
    0,
    '2026-03-12 10:00:00'
WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug = 'new-learning-resource-room-2026');

INSERT INTO news_posts (title, slug, excerpt, content, featured_image, author_id, category, tags, status, views, published_at)
SELECT
    'Outstanding PLE Performance Celebrated',
    'outstanding-ple-performance-2026',
    'Learners and teachers were recognized for excellent PLE performance and discipline.',
    'The school community held a thanksgiving and recognition assembly for learners and teachers who contributed to outstanding PLE performance. Parents commended the consistent academic guidance and mentorship offered by the school.',
    NULL,
    (SELECT id FROM admin_users ORDER BY id ASC LIMIT 1),
    'Results',
    'ple,results,achievement',
    'published',
    0,
    '2026-02-04 09:30:00'
WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug = 'outstanding-ple-performance-2026');

INSERT INTO news_posts (title, slug, excerpt, content, featured_image, author_id, category, tags, status, views, published_at)
SELECT
    'Inter-House Sports Day Brings School Community Together',
    'inter-house-sports-day-2026',
    'A vibrant sports day highlighted teamwork, discipline and healthy competition.',
    'The annual inter-house sports day featured athletics, team games and awards that encouraged teamwork and physical development. Parents, teachers and learners celebrated talent and sportsmanship throughout the day.',
    NULL,
    (SELECT id FROM admin_users ORDER BY id ASC LIMIT 1),
    'Sports',
    'sports,co-curricular,events',
    'published',
    0,
    '2026-01-20 14:00:00'
WHERE NOT EXISTS (SELECT 1 FROM news_posts WHERE slug = 'inter-house-sports-day-2026');

-- Upcoming events cards.
INSERT INTO events (title, slug, description, content, featured_image, event_date, end_date, location, organizer, event_type, status, created_by)
SELECT
    'Term II Opening Orientation',
    'term-ii-opening-orientation-2026',
    'Orientation for learners and parents for the start of Term II.',
    'The orientation session will cover academics, school routines, co-curricular activities and parent communication channels for Term II.',
    NULL,
    '2026-05-05 09:00:00',
    '2026-05-05 12:00:00',
    'Lukman Primary School Main Hall',
    'School Administration',
    'academic',
    'upcoming',
    (SELECT id FROM admin_users ORDER BY id ASC LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM events WHERE slug = 'term-ii-opening-orientation-2026');

INSERT INTO events (title, slug, description, content, featured_image, event_date, end_date, location, organizer, event_type, status, created_by)
SELECT
    'Parents and Teachers Consultation Day',
    'parents-teachers-consultation-day-2026',
    'A scheduled consultation to review learner progress and support plans.',
    'Parents will meet class teachers to discuss learner performance, behavior, attendance and improvement strategies for the term.',
    NULL,
    '2026-06-13 08:30:00',
    '2026-06-13 14:00:00',
    'Class Blocks, Lukman Primary School',
    'Academic Office',
    'administrative',
    'upcoming',
    (SELECT id FROM admin_users ORDER BY id ASC LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM events WHERE slug = 'parents-teachers-consultation-day-2026');

INSERT INTO events (title, slug, description, content, featured_image, event_date, end_date, location, organizer, event_type, status, created_by)
SELECT
    'Quran and Culture Celebration Day',
    'quran-culture-celebration-day-2026',
    'A school celebration of Quran recitation, culture, and student talent.',
    'Learners will participate in Quran recitation, nasheed, poetry and cultural presentations that reflect discipline and confidence.',
    NULL,
    '2026-07-04 10:00:00',
    '2026-07-04 16:00:00',
    'School Grounds',
    'Islamic Studies Department',
    'religious',
    'upcoming',
    (SELECT id FROM admin_users ORDER BY id ASC LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM events WHERE slug = 'quran-culture-celebration-day-2026');

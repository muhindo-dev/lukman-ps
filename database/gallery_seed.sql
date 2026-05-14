-- Gallery Albums & Images Seed Data
-- Imported from old WordPress system (NextGEN Gallery + WP Media Library)
-- Run: mysql -h 127.0.0.1 -P 8889 -u root -proot lukman_php < database/gallery_seed.sql

-- Clean existing data (safe re-run)
DELETE FROM gallery_images;
DELETE FROM gallery_albums;

-- Reset auto-increment
ALTER TABLE gallery_albums AUTO_INCREMENT = 1;
ALTER TABLE gallery_images AUTO_INCREMENT = 1;

-- =============================================
-- GALLERY ALBUMS
-- =============================================

INSERT INTO gallery_albums (id, title, slug, description, cover_image, category, display_order, status, created_by, created_at) VALUES
(1, 'Games & Sports Day', 'games-and-sports', 'Students participating in various sports and athletic activities including football, netball, athletics, and team competitions at Lukman Primary School.', 'gallery/games-and-sports/Games-and-sports-1.jpg', 'Sports', 1, 'active', 1, '2020-11-01 10:00:00'),
(2, 'Alumni Marathon Day', 'alumni-marathon', 'Annual alumni marathon event bringing together former and current students of Lukman Primary School in a spirit of unity, fitness, and community.', 'gallery/alumni-marathon/Alumni-marathon-day-1.jpg', 'Events', 2, 'active', 1, '2020-11-15 10:00:00'),
(3, 'School Events & Speeches', 'speeches-events', 'Assembly gatherings, speech days, prize-giving ceremonies, and important school events at Lukman Primary School.', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-10.jpg', 'Events', 3, 'active', 1, '2020-11-20 10:00:00'),
(4, 'Our Proud Alumni', 'our-alumni', 'Celebrating the achievements and community of Lukman Primary School alumni who continue to make us proud.', 'gallery/our-alumni/Lukman-Alumni-1.jpg', 'Alumni', 4, 'active', 1, '2020-11-25 10:00:00'),
(5, 'Campus Life', 'campus-life', 'A glimpse into everyday life at Lukman Primary School — classrooms, playgrounds, learning activities, and our beautiful campus.', 'gallery/campus-life/Lukman-primary-school-hero-kids.jpg', 'School Life', 5, 'active', 1, '2020-12-01 10:00:00'),
(6, 'Our Teaching Staff', 'teaching-staff', 'Meet the dedicated teachers and staff members who shape the future of our students at Lukman Primary School.', 'gallery/teaching-staff/teaching-staff-1.jpg', 'Staff', 6, 'active', 1, '2020-10-29 10:00:00');

-- =============================================
-- GALLERY IMAGES — Games & Sports (Album 1)
-- =============================================
INSERT INTO gallery_images (album_id, title, description, image_path, thumbnail_path, alt_text, display_order, status, uploaded_by) VALUES
(1, 'Sports Day Activities', 'Students competing in sports activities', 'gallery/games-and-sports/Games-and-sports-1.jpg', NULL, 'Games and Sports at Lukman PS', 1, 'active', 1),
(1, 'Team Sports', 'Students playing team sports on the field', 'gallery/games-and-sports/Games-and-sports-3.jpg', NULL, 'Team sports at Lukman PS', 2, 'active', 1),
(1, 'Athletics Competition', 'Track and field events', 'gallery/games-and-sports/Games-and-sports-5.jpg', NULL, 'Athletics at Lukman PS', 3, 'active', 1),
(1, 'Football Match', 'Students during a football match', 'gallery/games-and-sports/Games-and-sports-7.jpg', NULL, 'Football at Lukman PS', 4, 'active', 1),
(1, 'Sports Training', 'Physical education and training session', 'gallery/games-and-sports/Games-and-sports-10.jpg', NULL, 'Sports training at Lukman PS', 5, 'active', 1),
(1, 'Inter-house Competition', 'Inter-house sports competition', 'gallery/games-and-sports/Games-and-sports-14.jpg', NULL, 'Inter-house sports', 6, 'active', 1),
(1, 'Netball Competition', 'Students playing netball', 'gallery/games-and-sports/Games-and-sports-18.jpg', NULL, 'Netball at Lukman PS', 7, 'active', 1),
(1, 'Award Ceremony', 'Sports day award ceremony', 'gallery/games-and-sports/Games-and-sports-22.jpg', NULL, 'Sports awards', 8, 'active', 1),
(1, 'Team Spirit', 'Students showing team spirit', 'gallery/games-and-sports/Games-and-sports-26.jpg', NULL, 'Team spirit at Lukman PS', 9, 'active', 1),
(1, 'Victory Lap', 'Celebrations after competitions', 'gallery/games-and-sports/Games-and-sports-30.jpg', NULL, 'Victory celebrations', 10, 'active', 1);

-- =============================================
-- GALLERY IMAGES — Alumni Marathon (Album 2)
-- =============================================
INSERT INTO gallery_images (album_id, title, description, image_path, thumbnail_path, alt_text, display_order, status, uploaded_by) VALUES
(2, 'Marathon Start Line', 'Participants gathering at the start line', 'gallery/alumni-marathon/Alumni-marathon-day-1.jpg', NULL, 'Marathon start line', 1, 'active', 1),
(2, 'Runners in Action', 'Alumni and students running together', 'gallery/alumni-marathon/Alumni-marathon-day-5.jpg', NULL, 'Marathon runners', 2, 'active', 1),
(2, 'Community Spirit', 'The community comes together for the marathon', 'gallery/alumni-marathon/Alumni-marathon-day-10.jpg', NULL, 'Community marathon', 3, 'active', 1),
(2, 'Young Runners', 'Current students participating in the run', 'gallery/alumni-marathon/Alumni-marathon-day-20.jpg', NULL, 'Young marathon runners', 4, 'active', 1),
(2, 'Cheering Crowd', 'Supporters cheering on the participants', 'gallery/alumni-marathon/Alumni-marathon-day-30.jpg', NULL, 'Marathon spectators', 5, 'active', 1),
(2, 'Half-way Point', 'Runners at the half-way mark', 'gallery/alumni-marathon/Alumni-marathon-day-50.jpg', NULL, 'Marathon halfway', 6, 'active', 1),
(2, 'Determination', 'Participants showing determination on the course', 'gallery/alumni-marathon/Alumni-marathon-day-70.jpg', NULL, 'Marathon determination', 7, 'active', 1),
(2, 'Final Stretch', 'Runners approaching the finish line', 'gallery/alumni-marathon/Alumni-marathon-day-90.jpg', NULL, 'Marathon finish approach', 8, 'active', 1),
(2, 'Finish Line Celebrations', 'Celebrations at the finish line', 'gallery/alumni-marathon/Alumni-marathon-day-110.jpg', NULL, 'Marathon finish', 9, 'active', 1),
(2, 'Group Photo', 'All participants pose for a group photo', 'gallery/alumni-marathon/Alumni-marathon-day-130.jpg', NULL, 'Marathon group photo', 10, 'active', 1);

-- =============================================
-- GALLERY IMAGES — Speeches & Events (Album 3)
-- =============================================
INSERT INTO gallery_images (album_id, title, description, image_path, thumbnail_path, alt_text, display_order, status, uploaded_by) VALUES
(3, 'Assembly Gathering', 'Students and staff at a school assembly', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-5.jpg', NULL, 'School assembly', 1, 'active', 1),
(3, 'Headteacher Address', 'Headteacher addressing the school community', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-10.jpg', NULL, 'Headteacher speech', 2, 'active', 1),
(3, 'Guest Speaker', 'Distinguished guest speaker at the event', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-15.jpg', NULL, 'Guest speaker', 3, 'active', 1),
(3, 'Prize Giving', 'Students receiving prizes and awards', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-20.jpg', NULL, 'Prize giving ceremonies', 4, 'active', 1),
(3, 'Student Performances', 'Students performing during the event', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-30.jpg', NULL, 'Student performances', 5, 'active', 1),
(3, 'Parents Day', 'Parents attending the school event', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-40.jpg', NULL, 'Parents day', 6, 'active', 1),
(3, 'Cultural Display', 'Cultural activities and displays', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-50.jpg', NULL, 'Cultural display', 7, 'active', 1),
(3, 'Award Winners', 'Outstanding students receiving their awards', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-55.jpg', NULL, 'Award winners', 8, 'active', 1),
(3, 'School Choir', 'School choir performing at the ceremony', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-60.jpg', NULL, 'School choir', 9, 'active', 1),
(3, 'Closing Ceremony', 'The closing of the event', 'gallery/speeches-events/LUKMAN-PHOTOS-OF-SPEECHES-65.jpg', NULL, 'Closing ceremony', 10, 'active', 1);

-- =============================================
-- GALLERY IMAGES — Our Alumni (Album 4)
-- =============================================
INSERT INTO gallery_images (album_id, title, description, image_path, thumbnail_path, alt_text, display_order, status, uploaded_by) VALUES
(4, 'Alumni Gathering', 'Lukman PS alumni coming together', 'gallery/our-alumni/Lukman-Alumni-1.jpg', NULL, 'Lukman alumni', 1, 'active', 1),
(4, 'Class Reunion', 'Former classmates meeting again', 'gallery/our-alumni/Lukman-Alumni-3.jpg', NULL, 'Alumni reunion', 2, 'active', 1),
(4, 'Alumni Networking', 'Alumni networking and sharing experiences', 'gallery/our-alumni/Lukman-Alumni-5.jpg', NULL, 'Alumni networking', 3, 'active', 1),
(4, 'Success Stories', 'Alumni sharing their success stories', 'gallery/our-alumni/Lukman-Alumni-7.jpg', NULL, 'Alumni success stories', 4, 'active', 1),
(4, 'Mentorship', 'Alumni mentoring current students', 'gallery/our-alumni/Lukman-Alumni-9.jpg', NULL, 'Alumni mentorship', 5, 'active', 1),
(4, 'Alumni Award', 'Alumni receiving recognition awards', 'gallery/our-alumni/Lukman-Alumni-12.jpg', NULL, 'Alumni awards', 6, 'active', 1),
(4, 'Community Service', 'Alumni participating in community service', 'gallery/our-alumni/Lukman-Alumni-15.jpg', NULL, 'Alumni community service', 7, 'active', 1),
(4, 'Alumni Leadership', 'Alumni in leadership roles', 'gallery/our-alumni/Lukman-Alumni-18.jpg', NULL, 'Alumni leadership', 8, 'active', 1),
(4, 'Homecoming', 'Alumni visiting the school', 'gallery/our-alumni/Lukman-Alumni-21.jpg', NULL, 'Alumni homecoming', 9, 'active', 1),
(4, 'Alumni Legacy', 'The lasting legacy of Lukman PS alumni', 'gallery/our-alumni/Lukman-Alumni-25.jpg', NULL, 'Alumni legacy', 10, 'active', 1);

-- =============================================
-- GALLERY IMAGES — Campus Life (Album 5)
-- =============================================
INSERT INTO gallery_images (album_id, title, description, image_path, thumbnail_path, alt_text, display_order, status, uploaded_by) VALUES
(5, 'Our Happy Students', 'Students enjoying school life at Lukman PS', 'gallery/campus-life/Lukman-primary-school-hero-kids.jpg', NULL, 'Lukman PS students', 1, 'active', 1),
(5, 'Classroom Learning', 'Students engaged in classroom activities', 'gallery/campus-life/DSC_1519.jpg', NULL, 'Classroom learning', 2, 'active', 1),
(5, 'School Grounds', 'The beautiful school grounds and campus', 'gallery/campus-life/DSC_1545.jpg', NULL, 'School grounds', 3, 'active', 1),
(5, 'School Community', 'The vibrant school community', 'gallery/campus-life/67292859_593046817892745_4202687737507086336_o.jpg', NULL, 'School community', 4, 'active', 1),
(5, 'Learning Together', 'Students learning collaboratively', 'gallery/campus-life/68244353_605555619975198_4411485004211683328_o.jpg', NULL, 'Collaborative learning', 5, 'active', 1),
(5, 'Outdoor Activities', 'Students during outdoor activities', 'gallery/campus-life/69310128_605592973304796_2199200921169166336_o.jpg', NULL, 'Outdoor activities', 6, 'active', 1),
(5, 'School Life Moments', 'Everyday moments at Lukman PS', 'gallery/campus-life/68486880_605591989971561_7423112807991738368_o.jpg', NULL, 'School life moments', 7, 'active', 1);

-- =============================================
-- GALLERY IMAGES — Teaching Staff (Album 6)
-- =============================================
INSERT INTO gallery_images (album_id, title, description, image_path, thumbnail_path, alt_text, display_order, status, uploaded_by) VALUES
(6, 'Our Teaching Team', 'The dedicated teaching staff of Lukman PS', 'gallery/teaching-staff/teaching-staff-1.jpg', NULL, 'Teaching staff', 1, 'active', 1),
(6, 'Teachers in Action', 'Teachers conducting lessons', 'gallery/teaching-staff/teaching-staff-2.jpg', NULL, 'Teachers in action', 2, 'active', 1),
(6, 'Staff Development', 'Staff during professional development', 'gallery/teaching-staff/teaching-staff-3.jpg', NULL, 'Staff development', 3, 'active', 1),
(6, 'Teacher-Student Interaction', 'Teachers engaging with students', 'gallery/teaching-staff/68480675_605583896639037_8667707722357014528_o.jpg', NULL, 'Teacher-student interaction', 4, 'active', 1),
(6, 'Dedicated Educators', 'Our dedicated educators at work', 'gallery/teaching-staff/68615535_605556126641814_7956349559184031744_o.jpg', NULL, 'Dedicated educators', 5, 'active', 1),
(6, 'Teaching Excellence', 'Demonstrating teaching excellence', 'gallery/teaching-staff/69320983_605593043304789_5386649327244410880_o.jpg', NULL, 'Teaching excellence', 6, 'active', 1),
(6, 'Collaborative Teaching', 'Staff working together', 'gallery/teaching-staff/68498510_605556133308480_2836277874065932288_o.jpg', NULL, 'Collaborative teaching', 7, 'active', 1);

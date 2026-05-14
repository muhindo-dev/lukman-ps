-- ========================================
-- Lukman Primary School Database Schema
-- Created: 14 April 2026
-- Database: lukman_php
-- ========================================

USE lukman_php;

-- ========================================
-- Admin Users Table
-- ========================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Admin Activity Log
-- ========================================
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    record_id INT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_admin_id (admin_id),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- News/Blog Posts Table
-- ========================================
CREATE TABLE IF NOT EXISTS news_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    excerpt TEXT NULL,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    author_id INT NOT NULL,
    category VARCHAR(50) NULL,
    tags TEXT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    published_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES admin_users(id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_published_at (published_at DESC),
    INDEX idx_category (category),
    FULLTEXT idx_search (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Events Table
-- ========================================
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    description TEXT NULL,
    content LONGTEXT NULL,
    featured_image VARCHAR(255) NULL,
    event_date DATETIME NOT NULL,
    end_date DATETIME NULL,
    location VARCHAR(200) NULL,
    organizer VARCHAR(100) NULL,
    event_type ENUM('academic', 'sports', 'cultural', 'religious', 'administrative', 'other') DEFAULT 'other',
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id),
    INDEX idx_slug (slug),
    INDEX idx_event_date (event_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Gallery Albums Table
-- ========================================
CREATE TABLE IF NOT EXISTS gallery_albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    category VARCHAR(50) NULL,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Gallery Images Table
-- ========================================
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT NOT NULL,
    title VARCHAR(200) NULL,
    description TEXT NULL,
    image_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255) NULL,
    alt_text VARCHAR(200) NULL,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    uploaded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id),
    INDEX idx_album_id (album_id),
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Team Members Table
-- ========================================
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    department ENUM('management', 'teaching', 'support', 'student_leaders') DEFAULT 'teaching',
    bio TEXT NULL,
    qualification VARCHAR(200) NULL,
    photo VARCHAR(255) NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_department (department),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Contact Inquiries Table
-- ========================================
CREATE TABLE IF NOT EXISTS contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    inquiry_type ENUM('general', 'admission', 'academic', 'fees', 'other') DEFAULT 'general',
    subject VARCHAR(100) NOT NULL,
    message TEXT,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    INDEX idx_created_at (created_at DESC),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_inquiry_type (inquiry_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Site Settings Table
-- ========================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_type VARCHAR(20) DEFAULT 'text',
    description TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SCHOOL-SPECIFIC TABLES
-- ========================================

-- ========================================
-- Testimonials Table
-- ========================================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NULL,
    content TEXT NOT NULL,
    photo VARCHAR(255) NULL,
    rating TINYINT DEFAULT 5,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Downloads / Documents Table
-- ========================================
CREATE TABLE IF NOT EXISTS downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(20) DEFAULT 'pdf',
    file_size INT NULL,
    category ENUM('fees', 'circulars', 'academic', 'forms', 'routine', 'general') DEFAULT 'general',
    download_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Academic Results Table (PLE)
-- ========================================
CREATE TABLE IF NOT EXISTS academic_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year YEAR NOT NULL,
    exam_type ENUM('secular', 'theology') NOT NULL,
    centre_number VARCHAR(20) NULL,
    total_candidates INT DEFAULT 0,
    division_1 INT DEFAULT 0,
    division_2 INT DEFAULT 0,
    division_3 INT DEFAULT 0,
    division_4 INT DEFAULT 0,
    ungraded INT DEFAULT 0,
    pass_rate DECIMAL(5,2) DEFAULT 0.00,
    results_detail LONGTEXT NULL,
    pdf_file VARCHAR(255) NULL,
    summary TEXT NULL,
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_year_type (year, exam_type),
    INDEX idx_status (status),
    INDEX idx_year (year DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- School Calendar Table
-- ========================================
CREATE TABLE IF NOT EXISTS school_calendar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    term TINYINT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    description TEXT NULL,
    event_type ENUM('term', 'holiday', 'exam', 'event', 'meeting', 'other') DEFAULT 'event',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_start_date (start_date),
    INDEX idx_term (term),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- FAQ Items Table
-- ========================================
CREATE TABLE IF NOT EXISTS faq_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Page Content Blocks Table (CMS)
-- ========================================
CREATE TABLE IF NOT EXISTS page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_slug VARCHAR(100) NOT NULL,
    section_key VARCHAR(100) NOT NULL,
    title VARCHAR(200) NULL,
    content LONGTEXT NULL,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_page_section (page_slug, section_key),
    INDEX idx_page_slug (page_slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Admission Inquiries Table
-- ========================================
CREATE TABLE IF NOT EXISTS admission_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_name VARCHAR(255) NOT NULL,
    parent_email VARCHAR(255) NOT NULL,
    parent_phone VARCHAR(50) NOT NULL,
    child_name VARCHAR(255) NOT NULL,
    child_dob DATE NOT NULL,
    child_gender ENUM('male', 'female') NOT NULL,
    current_school VARCHAR(255) NULL,
    class_applying ENUM('P1', 'P2', 'P3', 'P4', 'P5', 'P6', 'P7') NOT NULL,
    boarding_type ENUM('boarding', 'day') NOT NULL,
    how_heard VARCHAR(100) NULL,
    message TEXT NULL,
    status ENUM('new', 'contacted', 'visited', 'enrolled', 'declined') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NULL,
    INDEX idx_created_at (created_at DESC),
    INDEX idx_status (status),
    INDEX idx_class (class_applying)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Newsletter Subscriptions Table
-- ========================================
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(100) NULL,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- DEFAULT ADMIN USER
-- Password: admin123 (change immediately after first login)
-- ========================================
INSERT INTO admin_users (username, password, full_name, email, status) 
VALUES ('admin', '$2y$12$yJCY7/0JLPZOahjSFwNXDeC7OZlI.UQJK.CjXr1fxqSZ1/EIMgbE.', 'Lukman PS Administrator', 'lukmanps2004@gmail.com', 'active')
ON DUPLICATE KEY UPDATE full_name='Lukman PS Administrator', email='lukmanps2004@gmail.com';

-- ========================================
-- SITE SETTINGS - Lukman Primary School
-- ========================================
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
-- School Identity
('site_name', 'Lukman Primary School', 'text', 'School name'),
('site_short_name', 'LPS', 'text', 'Short name / abbreviation'),
('site_tagline', 'Seek Knowledge and Attain Wisdom', 'text', 'School motto / tagline'),
('site_description', 'Lukman Primary School is a mixed boarding primary school established in 2004 as a project of UMUWA, offering a dual secular and Islamic theology curriculum in Entebbe, Uganda.', 'textarea', 'School description for SEO'),
('founding_year', '2004', 'text', 'Year school was founded'),
('mission_statement', 'TO PRODUCE AN INTEGRATED, EFFECTIVE AND A BALANCED CHILD WHO IS ACADEMICALLY AND RELIGIOUSLY SOUND.', 'textarea', 'School mission'),
('vision_statement', 'A PLACE WHERE ANY CHILD CAN BE TRANSFORMED INTO A PRODUCTIVE CITIZEN ANYWHERE IN THE WORLD.', 'textarea', 'School vision'),
('motto', 'SEEK KNOWLEDGE AND ATTAIN WISDOM', 'text', 'School motto'),

-- Contact Information
('contact_email', 'lukmanps2004@gmail.com', 'text', 'Primary contact email'),
('contact_email_2', 'kyamex@gmail.com', 'text', 'Secondary contact email'),
('contact_phone', '+256 700 000 000', 'text', 'Primary phone number'),
('contact_address', 'Entebbe, Wakiso District, Uganda', 'text', 'Physical address'),
('contact_city', 'Entebbe', 'text', 'City'),
('contact_country', 'Uganda', 'text', 'Country'),
('whatsapp_number', '+256 700 000 000', 'text', 'WhatsApp contact number'),

-- Social Media
('facebook_url', '', 'text', 'Facebook page URL'),
('twitter_url', '', 'text', 'Twitter/X profile URL'),
('instagram_url', '', 'text', 'Instagram profile URL'),
('youtube_url', '', 'text', 'YouTube channel URL'),

-- Branding
('primary_color', '#00723F', 'text', 'Primary brand color (Islamic green)'),
('secondary_color', '#DAA520', 'text', 'Secondary brand color (Gold)'),
('logo_icon_class', 'fas fa-school', 'text', 'Font Awesome icon for logo fallback'),

-- SEO & Meta
('meta_keywords', 'Lukman Primary School, Entebbe, Uganda, Islamic school, boarding school, primary school, UMUWA, PLE results, dual curriculum', 'textarea', 'SEO meta keywords'),
('og_title', 'Lukman Primary School - Seek Knowledge and Attain Wisdom', 'text', 'Open Graph title'),
('og_description', 'A mixed boarding primary school in Entebbe, Uganda offering dual secular and Islamic theology curriculum since 2004.', 'textarea', 'Open Graph description'),

-- Footer
('footer_about', 'Lukman Primary School was formed and registered on 19th April 2004. It is a mixed Boarding school and a project of Uganda Muslim Welfare Association (UMUWA), established on a purely Islamic foundation.', 'textarea', 'Footer about text'),
('developer_name', 'TusomeTech', 'text', 'Developer credit'),
('developer_url', '', 'text', 'Developer website URL'),

-- Academic
('current_term', '1', 'text', 'Current school term (1, 2, or 3)'),
('current_year', '2026', 'text', 'Current academic year'),
('secular_centre_no', '530253', 'text', 'UNEB secular examination centre number'),
('theology_centre_no', '97', 'text', 'Theology examination centre number'),

-- Google Analytics
('google_analytics_id', '', 'text', 'Google Analytics 4 measurement ID')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- ========================================
-- SEED FAQ DATA
-- ========================================
INSERT INTO faq_items (question, answer, category, display_order, status) VALUES
('What curriculum does Lukman Primary School follow?', 'We offer a unique dual curriculum combining the national secular curriculum (UNEB) with Islamic theology education. Students sit for both PLE and Islamic studies examinations.', 'academics', 1, 'active'),
('What are the school fees?', 'Fees vary by class and whether the student is a boarder or day scholar. Please visit our Admissions page or contact us directly for the current fee structure.', 'fees', 2, 'active'),
('Is Lukman PS a boarding school?', 'Yes, we are a mixed boarding school. We offer both boarding and day scholar options for all classes from P1 to P7.', 'admissions', 3, 'active'),
('What are the admission requirements?', 'Parents should fill out an admission inquiry form on our website or visit the school. Requirements include previous school reports (for transfer students) and birth certificate.', 'admissions', 4, 'active'),
('When was the school founded?', 'Lukman Primary School was founded on 19th April 2004 as a project of the Uganda Muslim Welfare Association (UMUWA).', 'general', 5, 'active'),
('What are the school terms?', 'We follow the Uganda school calendar with three terms: Term 1 (February-April), Term 2 (May-August), and Term 3 (September-December).', 'academics', 6, 'active'),
('Does the school provide transport?', 'Please contact the school administration for information about available transport arrangements for day scholars.', 'general', 7, 'active'),
('What co-curricular activities are available?', 'We offer sports (football, netball, volleyball), clubs (debate, science, English), Scouts & Guides, Quran recitation competitions, and community service activities.', 'academics', 8, 'active')
ON DUPLICATE KEY UPDATE question=question;

-- ========================================
-- End of Schema
-- ========================================

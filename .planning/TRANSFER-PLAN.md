# Lukman Primary School - WordPress to PHP Transfer Plan

## Project Overview

**Objective:** Transfer the Lukman Primary School website from WordPress to a modern, robust, responsive plain PHP website.

**Source (WordPress):** `/Applications/MAMP/htdocs/lukmanps` (Database: `lukman_ps`)
**Benchmark Project:** `/Applications/MAMP/htdocs/dtem-web` (Database: `dtehm_insurance_api`)
**Destination (New PHP):** `/Applications/MAMP/htdocs/lukman-ps` (Database: `lukman_php`)

---

## Architecture Overview (from dtem-web Benchmark)

The dtem-web project uses a clean, modern PHP architecture:

```
lukman-ps/
├── config.php              # Database & environment config
├── functions.php           # Shared functions (DB, sanitization, settings)
├── index.php               # Homepage
├── about.php               # About page
├── contact.php             # Contact page
├── news.php                # News/blog listing
├── news-detail.php         # Single news post
├── events.php              # Events listing
├── event-detail.php        # Single event
├── gallery.php             # Gallery listing
├── gallery-album.php       # Gallery album detail
├── faq.php                 # FAQ page
├── 404.php                 # 404 error page
├── includes/
│   ├── header.php          # Shared HTML header & navigation
│   └── footer.php          # Shared HTML footer
├── assets/
│   ├── css/
│   │   ├── style.css       # Main stylesheet
│   │   └── style.min.css   # Minified CSS
│   └── js/
│       ├── main.js         # Main JavaScript
│       └── main.min.js     # Minified JS
├── admin/                  # Admin panel (CRUD operations)
│   ├── login.php
│   ├── index.php           # Admin dashboard
│   ├── news-add.php
│   ├── news-edit.php
│   ├── events-add.php
│   ├── gallery.php
│   ├── settings.php        # Site settings manager
│   ├── team.php            # Team CRUD
│   ├── includes/
│   └── assets/
├── uploads/                # Media storage
├── database.sql            # Base schema
├── admin-schema.sql        # Full admin schema
└── .htaccess               # URL rewriting
```

### Key Technical Features
- **PDO** for database connections (prepared statements for security)
- **Bootstrap 5** for responsive layout
- **Font Awesome** for icons
- **AOS** (Animate On Scroll) for animations
- **Site Settings System** (database-driven, admin-editable)
- **Admin Panel** with authentication, CRUD for all content types
- **SEO**: Open Graph tags, Schema.org structured data, meta descriptions
- **Security**: CSRF protection, input sanitization, session security

---

## Transfer Steps

### Phase 1: Foundation Setup

#### Step 1.1: Copy Benchmark Project
- Copy the entire `dtem-web` project to `lukman-ps`
- Exclude: `.git/`, test files, backup files, dtem-specific content

#### Step 1.2: Create Database
- Create new database `lukman_php`
- Run the adapted schema (based on dtem-web's `admin-schema.sql`)
- Tables needed:
  - `admin_users` - Admin accounts
  - `admin_activity_log` - Admin audit trail
  - `news_posts` - News/blog posts
  - `events` - School events
  - `causes` - School causes/projects
  - `gallery_images` - Gallery photos
  - `gallery_albums` - Gallery album grouping
  - `team_members` - Staff/team members
  - `testimonials` - Parent/student testimonials
  - `contact_inquiries` - Contact form submissions
  - `site_settings` - Dynamic site settings
  - `downloads` - PDF documents (fees, circulars, etc.)
  - `academic_results` - PLE results data (NEW - school specific)

#### Step 1.3: Configure Database Connection
- Update `config.php` to point to `lukman_php` database
- Set proper DB credentials (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS)
- Configure environment (development/production)

---

### Phase 2: Content Migration

#### Step 2.1: Migrate Site Settings
Insert school-specific settings into `site_settings`:
- `site_name` → "Lukman Primary School"
- `site_short_name` → "LPS"
- `site_tagline` → "Seek Knowledge and Attain Wisdom"
- `site_description` → School description from WordPress
- `mission_statement` → Mission text
- `vision_statement` → Vision text
- `motto` → "SEEK KNOWLEDGE AND ATTAIN WISDOM"
- `founding_year` → "2004"
- `contact_email` → School email
- `contact_phone` → School phone
- `contact_address` → "Entebbe, Wakiso District, Uganda"
- Social media URLs
- Logo & favicon paths

#### Step 2.2: Migrate Media Assets
- Copy original (non-thumbnail) images from WP uploads
- Copy PDF documents to `uploads/documents/`
- Copy team photos from NextGen gallery
- Organize into: `uploads/gallery/`, `uploads/team/`, `uploads/news/`, `uploads/documents/`

#### Step 2.3: Migrate News/Blog Content
- Extract 39 published posts from `DzE_posts`
- Strip WordPress shortcodes, clean HTML
- Insert into `news_posts` table
- Categorize: Exam Results, School Gallery, Student Life, Circulars, Events, Academics, News

#### Step 2.4: Migrate Gallery Content
- Extract 16 Modula galleries as albums
- Insert albums into `gallery_albums` table
- Link images to albums in `gallery_images` table

#### Step 2.5: Migrate Team Members
- 6 team members from `our_team` post type
- Insert into `team_members` table with positions and photos

#### Step 2.6: Migrate Testimonials
- 6 testimonials to `testimonials` table

#### Step 2.7: Migrate Events
- Transfer 10 events (adapt dummy events to school context)

---

### Phase 3: Page Development

#### Step 3.1: Homepage (`index.php`)
Replace dtem-web health content with school content:
- **Hero Section**: School banner with motto, key stats (Years since 2004, Students, Staff, Programs)
- **About Preview**: Brief school history with link to About
- **Dual Curriculum**: Secular & Islamic highlights
- **Latest News**: Recent news/blog posts
- **Upcoming Events**: School events
- **Gallery Preview**: Featured photos
- **Testimonials Carousel**: Parent/student testimonials
- **Call-to-Action**: Admission, Contact, Visit

#### Step 3.2: About Section (Multiple Pages)
- `about.php` - Main about page (school overview, history, mission/vision, core values)
- `about.php#history` - School history section
- `about.php#mission` - Mission & Vision section
- `about.php#why-lukman` - Why the name Lukman section
- `about.php#anthem` - School Anthem & Dua
- `about.php#structure` - Organisational structure
- `team.php` - Staff/team listing page
- `leadership.php` - SMC, Executive Committee, Student Leaders

#### Step 3.3: Academics Section
- `academics.php` - Main academics page (dual curriculum overview)
- `results.php` - PLE Results (historical data)
- `resources.php` - Academic resources & past papers
- `timetable.php` - School timetable/routine (with PDF downloads)

#### Step 3.4: Student Life Section
- `student-life.php` - Co-curricular activities, sports, clubs
- `gallery.php` - Photo gallery with album view
- `gallery-album.php` - Individual album photos

#### Step 3.5: Admissions Section
- `admissions.php` - Admission info, fees structure, requirements
  - Fees structure (PDF download)
  - Daily routine (PDF download)
  - School rules and regulations
  - Admission form/requirements
  - School menu (PDF download)

#### Step 3.6: News & Events
- `news.php` - Blog/news listing with pagination
- `news-detail.php` - Single post view
- `events.php` - Events listing
- `event-detail.php` - Single event view

#### Step 3.7: Contact
- `contact.php` - Contact form, school address, map, phone/email

#### Step 3.8: Publications/Downloads
- `downloads.php` - Downloadable documents (circulars, school magazine, brochures)

#### Step 3.9: Additional Pages
- `faq.php` - Frequently Asked Questions
- `404.php` - Custom 404 page

---

### Phase 4: Design & Branding

#### Step 4.1: Color Scheme
Replace dtem-web health/green theme with school-appropriate colors:
- **Primary**: Islamic/school green (#00723F or similar)
- **Secondary**: Gold/amber (#DAA520) - representing wisdom
- **Accent**: White, dark text
- Maintain the modern, clean design from dtem-web

#### Step 4.2: Typography & Icons
- Keep Bootstrap 5 responsive grid
- Update Font Awesome icons to education-related
- School-specific iconography (📚 books, 🎓 graduation, 🕌 mosque, ⚽ sports)

#### Step 4.3: Logo & Branding
- Replace dtem-web logo with Lukman PS logo
- Update favicon
- School banner images

---

### Phase 5: Admin Panel Adaptation

#### Step 5.1: Adapt Admin Dashboard
- Update admin panel branding to Lukman PS
- Dashboard stats: Total News, Events, Gallery Albums, Inquiries

#### Step 5.2: Content Management
Keep all existing admin CRUD operations:
- ✅ News/Posts management
- ✅ Events management
- ✅ Gallery management (albums + images)
- ✅ Team members management
- ✅ Contact inquiries
- ✅ Site settings
- ✅ Admin users

#### Step 5.3: Add School-Specific Admin Features
- Downloads/Documents management (PDF uploads)
- Academic Results management
- Testimonials management (if not already present)

---

### Phase 6: Testing & Optimization

#### Step 6.1: Functional Testing
- All pages load correctly
- Navigation works (all links)
- Contact form submits successfully
- Gallery displays properly
- PDF downloads work
- Admin panel CRUD operations
- Mobile responsiveness

#### Step 6.2: Content Verification
- All WordPress content successfully migrated
- Images display correctly
- PDF downloads functional
- No broken links
- Spelling/content accuracy

#### Step 6.3: Performance
- Image optimization (compress large uploads)
- CSS/JS minification
- Browser caching (.htaccess)
- Database query optimization

#### Step 6.4: Security Audit
- SQL injection prevention (PDO prepared statements)
- XSS prevention (htmlspecialchars)
- CSRF tokens on forms
- Admin authentication secure
- File upload validation
- Error logging (not display)

---

## Database Schema for Lukman PS (`lukman_php`)

### Core Tables (from dtem-web)
```sql
-- admin_users: Admin accounts
-- admin_activity_log: Admin audit trail
-- news_posts: Blog/news articles
-- events: School events
-- causes: School projects/fundraising
-- gallery_albums: Photo album grouping (NEW)
-- gallery_images: Individual photos
-- team_members: Staff profiles
-- contact_inquiries: Form submissions
-- site_settings: Dynamic configuration
```

### New Tables (school-specific)
```sql
-- testimonials: Parent/student testimonials
-- downloads: PDF documents (fees, circulars, etc.)
-- academic_results: PLE results data
```

---

## Content Mapping: WordPress → PHP Pages

| WordPress Page | New PHP Page | Notes |
|----------------|-------------|-------|
| Homepage (home-books) | `index.php` | Complete redesign |
| About Us | `about.php` | Merged about content |
| Head teacher's message | `about.php#headteacher` | Section |
| Chairman SMC | `about.php#chairman` | Section |
| Our mission and vision | `about.php#mission` | Section |
| Why the name Lukman | `about.php#why-lukman` | Section |
| School history | `about.php#history` | Section |
| Org structure | `about.php#structure` | Section |
| School Anthem & Dua | `about.php#anthem` | Section |
| Our team | `team.php` | Dedicated page |
| Teaching Staff | `team.php#teaching` | Section |
| Support Staff | `team.php#support` | Section |
| SMC / Executive | `team.php#leadership` | Section |
| Student Leaders | `team.php#students` | Section |
| Dual curriculum | `academics.php` | Main academics |
| Examination Results | `results.php` | Results archive |
| Academic Resources | `resources.php` | Resources |
| Past Papers | `resources.php#papers` | Section |
| Timetable | `admissions.php#routine` | Section |
| Assessments | `academics.php#assessments` | Section |
| Grading | `academics.php#grading` | Section |
| Co-curricular | `student-life.php` | Activities |
| Events calendar | `events.php` | Events listing |
| Gallery | `gallery.php` | Gallery albums |
| Fees structure | `admissions.php#fees` | With PDF download |
| Daily Routine | `admissions.php#routine` | With PDF download |
| School rules | `admissions.php#rules` | Section |
| Admission Form | `admissions.php#apply` | Section |
| School Menu | `admissions.php#menu` | With PDF download |
| School circulars | `downloads.php` | Documents |
| School magazine | `downloads.php` | Documents |
| Contact Us | `contact.php` | Contact form + info |
| Blog | `news.php` | News listing |
| E-Learning | `academics.php#elearning` | Link section |
| FAQ | `faq.php` | FAQ page |
| Privacy Policy | `privacy.php` | Legal |

---

## Potential Challenges

1. **Large Media Library**: 13,000+ images (mostly thumbnails). Need to identify and copy only originals.
2. **Shortcode Content**: WordPress pages use vc_row, woodmart, Elementor shortcodes. Content must be stripped and converted to clean HTML.
3. **PLE Results Data**: Multiple years of exam results in blog posts with HTML tables. Need careful extraction.
4. **PDF Links**: Some pages just link to PDF downloads. Links need to be updated to new paths.
5. **Gallery Reorganization**: 16 Modula galleries with images spread across WP upload dirs. Need consolidation.
6. **WooCommerce Content**: Shop functionality may not be needed but should be verified with stakeholder.
7. **E-Learning**: LearnPress courses may need external linking strategy.

---

## File Exclusions (from dtem-web copy)

Files/folders to NOT copy or to remove after copy:
- `dtem-web/.git/` - Git history
- `*-backup.php` - Backup files
- `*-old*.php` - Old versions
- `test-*.php`, `test.php` - Test files
- `setup*.php` - Setup scripts (after initial setup)
- `import_*.sql`, `import_*.py` - Import scripts
- Content specific to DTEHM (will be replaced)

---

## Implementation Priority

1. **High Priority** (Core pages):
   - Homepage, About, Contact, Gallery, News, 404
   - Admin panel with settings management
   - Database schema and initial data

2. **Medium Priority** (Content pages):
   - Academics, Results, Team, Student Life
   - Admissions, Downloads
   - Events

3. **Lower Priority** (Enhancements):
   - FAQ, Privacy Policy
   - Newsletter integration
   - SEO optimization
   - Performance tuning

---

## Tech Stack Summary

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8.x (plain, no framework) |
| Database | MySQL 8.0 (via PDO) |
| Frontend | HTML5, CSS3, Bootstrap 5 |
| JavaScript | Vanilla JS + minimal libraries |
| Icons | Font Awesome 6 |
| Animations | AOS (Animate On Scroll) |
| Image Gallery | Lightbox / custom JS |
| Admin Panel | Custom PHP (from dtem-web) |
| Forms | PHP with CSRF + validation |
| SEO | Schema.org, Open Graph, Sitemap |

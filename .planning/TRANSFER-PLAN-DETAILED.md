# Lukman Primary School - Comprehensive Transfer Plan

## Project Summary

| Item | Detail |
|------|--------|
| **Project** | WordPress → Plain PHP Website Transfer |
| **School** | Lukman Primary School, Entebbe, Wakiso, Uganda |
| **WordPress Source** | `/Applications/MAMP/htdocs/lukmanps` (DB: `lukman_ps`, prefix `DzE_`) |
| **Benchmark Source** | `/Applications/MAMP/htdocs/dtem-web` (DB: `dtehm_insurance_api`) |
| **Destination** | `/Applications/MAMP/htdocs/lukman-ps` (DB: `lukman_php`) |
| **MySQL Binary** | `/Applications/MAMP/Library/bin/mysql80/bin/mysql` |
| **DB Connection** | `-u root -proot --port=8889 --socket=/Applications/MAMP/tmp/mysql/mysql.sock` |

---

## Table of Contents

1. [Phase 1: Project Foundation](#phase-1-project-foundation)
2. [Phase 2: Database Setup & Schema](#phase-2-database-setup--schema)
3. [Phase 3: Content Migration from WordPress](#phase-3-content-migration-from-wordpress)
4. [Phase 4: Core Page Development](#phase-4-core-page-development)
5. [Phase 5: School-Specific Features](#phase-5-school-specific-features)
6. [Phase 6: Design & Branding](#phase-6-design--branding)
7. [Phase 7: Admin Panel Adaptation](#phase-7-admin-panel-adaptation)
8. [Phase 8: Testing & QA](#phase-8-testing--qa)
9. [Phase 9: Deployment Preparation](#phase-9-deployment-preparation)
10. [dtem-web Removals vs. Additions](#dtem-web-removals-vs-additions)
11. [Risk Register](#risk-register)

---

## Phase 1: Project Foundation

### Step 1.1: Copy Benchmark Project to Destination
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
1. Copy all files from `/Applications/MAMP/htdocs/dtem-web/` to `/Applications/MAMP/htdocs/lukman-ps/`
2. Exclude these files/folders from copy:
   - `.git/` — Git history (not needed)
   - `*-backup.php` — Old backup files (about-backup.php, contact-backup.php, footer-backup.php, header-backup.php, index-backup.php)
   - `*-old*.php` — Old versions (index-old-academy.php, index-old-spa.php, index-spa-backup.php, news-old.php.backup)
   - `index.html.backup`, `index.php.backup-*` — Backup files
   - `test-*.php`, `test.php`, `test-form.html` — Test files
   - `import_tables.py`, `import_custom_tables.sql` — Import scripts
   - `ngrok-setup.sh`, `cloudflare-tunnel-setup.sh` — Tunnel scripts
   - `new changes.md` — Dev notes
   - `settings.php.backup` (in admin/)
   - `admin/setup-sample-data*.php`, `admin/setup-*.php` — Setup scripts (copy but remove after initial setup)
   - `admin/fix-*.php`, `admin/check-tables.php`, `admin/create-missing-images.php`, `admin/download-real-images.php` — Maintenance scripts
3. Keep documentation md files as reference but will replace with Lukman PS versions

**Potential Challenges:**
- File permissions may differ between MAMP directories
- Hidden files (`.htaccess`, `.DS_Store`) need handling

**Mitigation:**
- Use `rsync` or `cp -R` with permission preservation
- Verify `.htaccess` is copied (critical for URL rewriting)

---

### Step 1.2: Configure Environment
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
1. Update `config.php`:
   - `DB_NAME` → `'lukman_php'`
   - `DB_HOST` → `'127.0.0.1'`
   - `DB_PORT` → `'8889'`
   - `DB_USER` → `'root'`
   - `DB_PASS` → `'root'`
   - `DB_SOCKET` → `'/Applications/MAMP/tmp/mysql/mysql.sock'`
   - `ENVIRONMENT` → `'development'`
   - Remove `API_STORAGE_URL` (not needed for school site)
2. Update `config-production.php` template for future deployment to lukmanps.ac.ug

**Potential Challenges:**
- MAMP MySQL socket path may vary
- Production server may use different PHP version

**Mitigation:**
- Socket fallback to TCP connection already built into dtem-web functions.php
- Test with both socket and TCP connections

---

### Step 1.3: Set Up .htaccess
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
1. Copy `.htaccess` from dtem-web
2. Update `RewriteBase` if needed (from `/dtem-web/` to `/lukman-ps/`)
3. Ensure clean URLs work
4. Set proper caching headers for images/CSS/JS

**Potential Challenges:**
- MAMP may have mod_rewrite disabled
- Subdirectory path differences

**Mitigation:**
- Verify mod_rewrite is enabled in MAMP Apache config
- Test URL routing before proceeding

---

## Phase 2: Database Setup & Schema

### Step 2.1: Create Database
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
```sql
CREATE DATABASE IF NOT EXISTS lukman_php 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

**Potential Challenges:**
- MySQL user may lack CREATE DATABASE privilege on production
- Character set conflicts

**Mitigation:**
- Test with root user locally
- Document exact SQL for production DBA

---

### Step 2.2: Create Core Tables (from dtem-web schema)
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**Tables to create (adapted from dtem-web `admin-schema.sql`):**

| Table | Purpose | From dtem-web? | Changes Needed |
|-------|---------|----------------|----------------|
| `admin_users` | Admin accounts | YES | Update default admin email to school admin |
| `admin_activity_log` | Audit trail | YES | No changes |
| `news_posts` | Blog/news articles | YES | Add `category` enum for school-specific categories |
| `events` | School events | YES | Add `event_type` for school-specific types |
| `causes` | School projects/fundraising | YES | Optional — rename to `projects` or keep |
| `gallery_albums` | Photo album groups | YES | No changes |
| `gallery_images` | Individual photos | YES | No changes |
| `team_members` | Staff profiles | YES | Add `department` field, `qualification` field |
| `contact_inquiries` | Form submissions | YES | Add inquiry type (admission, general, etc.) |
| `site_settings` | Dynamic config | YES | Replace all values with school data |

**Potential Challenges:**
- Foreign key constraints may fail if tables created in wrong order
- Auto-increment IDs need considered for data migration

**Mitigation:**
- Create tables in dependency order (admin_users first, then dependent tables)
- Use explicit INSERT with IDs during migration

---

### Step 2.3: Create School-Specific Tables (NEW)
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**New tables not in dtem-web:**

| Table | Purpose | Fields |
|-------|---------|--------|
| `testimonials` | Parent/student testimonials | id, name, role, content, photo, rating, status, display_order, created_at |
| `downloads` | PDF documents (fees, circulars, menu) | id, title, description, file_path, file_type, category, download_count, status, created_at |
| `academic_results` | PLE results by year | id, year, exam_type (secular/theology), centre_no, results_data (JSON/TEXT), summary, status, created_at |
| `school_calendar` | Academic calendar events | id, title, term, start_date, end_date, description, type, status, created_at |
| `faq_items` | FAQ entries | id, question, answer, category, display_order, status, created_at |
| `page_content` | Static page content blocks | id, page_slug, section_key, title, content, display_order, status, updated_at |

**Potential Challenges:**
- Schema design for PLE results (complex table data per year)
- Page content system needs to be flexible enough for varied page layouts

**Mitigation:**
- Use JSON/LONGTEXT for results_data to store complex table structures
- Use section_key + page_slug for flexible content blocks

---

### Step 2.4: Populate Site Settings
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
Create `settings-setup.sql` with Lukman PS specific settings:

| Setting Key | Value |
|-------------|-------|
| `site_name` | Lukman Primary School |
| `site_short_name` | LPS |
| `site_tagline` | Seek Knowledge and Attain Wisdom |
| `site_description` | Lukman Primary School is a mixed boarding school established in 2004 as a project of UMUWA, offering dual secular and Islamic theology curriculum in Entebbe, Uganda. |
| `founding_year` | 2004 |
| `mission_statement` | TO PRODUCE AN INTEGRATED, EFFECTIVE AND A BALANCED CHILD WHO IS ACADEMICALLY AND RELIGIOUSLY SOUND. |
| `vision_statement` | A PLACE WHERE ANY CHILD CAN BE TRANSFORMED INTO A PRODUCTIVE CITIZEN ANYWHERE IN THE WORLD |
| `motto` | SEEK KNOWLEDGE AND ATTAIN WISDOM |
| `contact_email` | lukmanps2004@gmail.com |
| `contact_phone` | (from WordPress data) |
| `contact_address` | Entebbe, Wakiso District |
| `contact_city` | Entebbe |
| `contact_country` | Uganda |
| `logo_icon_class` | fas fa-school |
| `primary_color` | #00723F (Islamic green) |
| `secondary_color` | #DAA520 (Gold/wisdom) |
| `footer_about` | Lukman Primary School was formed and registered on 19th April 2004. It is a mixed Boarding school and a project of Uganda Muslim Welfare Association (UMUWA), established on a purely Islamic foundation. |
| `meta_keywords` | Lukman Primary School, Entebbe, Uganda, Islamic school, boarding school, primary school, UMUWA, PLE results |
| `og_title` | Lukman Primary School - Seek Knowledge and Attain Wisdom |
| `developer_name` | TusomeTech |

**Potential Challenges:**
- Special characters in mission/vision text (quotes, ampersands)
- Missing contact phone/social URLs from WordPress

**Mitigation:**
- Use prepared statements for setting insertion
- Leave empty social URLs, admin can fill later

---

### Step 2.5: Create Default Admin User
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
- Create admin user with school credentials
- Username: `admin` / Email: `lukmanps2004@gmail.com`
- Set a secure temporary password (to be changed on first login)

**Potential Challenges:**
- Password hash compatibility between PHP versions

**Mitigation:**
- Use `PASSWORD_DEFAULT` algorithm which adapts

---

## Phase 3: Content Migration from WordPress

### Step 3.1: Extract & Clean WordPress Page Content
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
1. Query all published pages from `DzE_posts` (50 pages)
2. Strip WordPress shortcodes: `[vc_row]`, `[vc_column]`, `[woodmart_*]`, `[/vc_*]`, `[elementor_*]`
3. Clean HTML: remove inline styles from WP editors, normalize paragraph tags
4. Insert cleaned content into `page_content` table keyed by page_slug + section_key
5. Key pages to migrate:

| WordPress Page (ID) | → page_slug | → section_key | Priority |
|---------------------|-------------|---------------|----------|
| School history (11746) | about | history | HIGH |
| Mission & Vision (11736) | about | mission-vision | HIGH |
| Head teacher's message (12246) | about | headteacher-message | HIGH |
| Chairman SMC (12254) | about | chairman-message | HIGH |
| Why the name Lukman (11727) | about | why-lukman | HIGH |
| Org structure (11731) | about | org-structure | MEDIUM |
| School Anthem & Dua (11741) | about | anthem-dua | MEDIUM |
| School rules (11577) | admissions | rules | HIGH |
| Fees structure (11659) | admissions | fees | HIGH |
| Daily Routine (11709) | admissions | routine | MEDIUM |
| School Menu (11828) | admissions | menu | MEDIUM |
| Dual curriculum (12781) | academics | curriculum | HIGH |
| Grading (12788) | academics | grading | MEDIUM |
| Co-curricular (12791) | student-life | activities | MEDIUM |
| Contact Us (12299) | contact | main | HIGH |

**Potential Challenges:**
- WordPress shortcodes are deeply nested and complex (WPBakery, WoodMart, Elementor)
- Some content is purely visual builder markup with no actual text
- HTML content has inconsistent formatting

**Mitigation:**
- Write a PHP cleanup function that regex-strips all `[shortcode]...[/shortcode]` patterns
- Manually review top-priority pages after automated cleanup
- For pages with only shortcode content (no text), extract manually from WP admin preview

---

### Step 3.2: Migrate Blog Posts/News
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
1. Query 39 published posts from `DzE_posts WHERE post_type='post'`
2. For each post, extract: title, slug, content, excerpt, featured_image, post_date, category
3. Map WordPress categories to new categories:
   - `examinations-results` → `exam-results`
   - `school-gallery` → `gallery`
   - `lukmanps-student-life` → `student-life`
   - `yearly-circular` → `circulars`
   - `lukmanps-events` → `events`
   - `academics` → `academics`
   - `lukmanps-facilities` → `facilities`
   - `news` → `news`
4. Get featured image path from `DzE_postmeta` (meta_key `_thumbnail_id`) → `DzE_posts` (attachment) → guid/path
5. Strip shortcodes from post content
6. Insert into `news_posts` table

**Potential Challenges:**
- Featured images are stored as attachment post IDs, requiring lookup chain
- Image paths use WordPress directory structure (year/month)
- Some posts contain WP gallery shortcodes within content

**Mitigation:**
- Write migration script to resolve attachment IDs to file paths
- Copy referenced images to `uploads/news/` with new naming
- Log any posts where image resolution fails

---

### Step 3.3: Migrate Gallery Albums & Images
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
1. Extract 16 Modula galleries from `DzE_posts WHERE post_type='modula-gallery'`
2. For each gallery, get images from post_content (Modula stores image IDs in post meta)
3. Create gallery albums in `gallery_albums`:
   - Alumni marathon day
   - Assembly time
   - Buildings
   - Pupils
   - Class time
   - Co-curricular activities
   - Community cleaning
   - Games and sports
   - Lukman Alumni
   - Speeches
   - Management committee
   - Prefectorial swearing in 2017-2018
   - Scouts & Guides
   - Science department
   - Home gallery (featured)
4. For each image, copy original file (not WP thumbnails) to `uploads/gallery/[album-slug]/`
5. Insert image records into `gallery_images` with album_id reference
6. Also handle NextGen Gallery images from `/wp-content/gallery/simple-gallery/` (team photos)

**Image Selection Strategy:**
- From ~13,125 images (including WP-generated thumbnails), only copy originals
- Identify originals: files WITHOUT dimension suffixes like `-100x100`, `-300x300`, `-768x432`
- Estimated originals: ~800-1200 images

**Potential Challenges:**
- Modula gallery stores image data in serialized PHP arrays within post meta
- Some images may be very large (scaled versions from phone cameras)
- Gallery images in `/wp-content/uploads/my_uploads/` subfolder

**Mitigation:**
- Parse serialized arrays to extract image IDs
- Define max image dimension (1920px) and resize on migration
- Handle both `/uploads/YYYY/MM/` and `/uploads/my_uploads/` paths

---

### Step 3.4: Migrate Team Members
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
1. Extract 6 team members from `DzE_posts WHERE post_type='our_team'`
2. Get member details from post_content and post_meta (position, email, social links)
3. Copy team photos from `/wp-content/gallery/simple-gallery/`
4. Insert into `team_members` table:
   - Ms. Halima Kayuge
   - Mr. Sam Ahmad Ssentongo
   - Mr. Ahmad Bisegerwa
   - Ms. Hadijah Kayuge
   - Mr. Ibrahim Bagonza
   - Hajat Aisha Nambooze

**Potential Challenges:**
- Team position/role stored in WordPress custom fields (need to identify meta_keys)
- Team photos may be in NextGen gallery, not standard WP uploads

**Mitigation:**
- Query `DzE_postmeta` for team member IDs to find all custom fields
- Check both gallery and uploads paths for photos

---

### Step 3.5: Migrate Testimonials
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
1. Extract 6 testimonials from `DzE_posts WHERE post_type='testimonials'`
2. Get content and any associated metadata
3. Insert into `testimonials` table:
   - Mr. Musa
   - Ssemanda Asuman
   - Mrs. Hamis
   - Mariam
   - Sumayya
   - Biirah Halima

---

### Step 3.6: Migrate Events
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
1. Extract 10 events from `DzE_posts WHERE post_type='tp_event'`
2. Get event meta (date, location, etc.) from `DzE_postmeta`
3. Map to `events` table (title, description, event_date, location, status)
4. Convert old events (2015-2020) to status 'completed'

---

### Step 3.7: Migrate PDF Documents
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
1. Copy 19 PDF files to `uploads/documents/`
2. Insert into `downloads` table with categorization:

| File | Category |
|------|----------|
| SCHOOL-REQUIREMENTS-FEES-STRUCTURE-2022.pdf | fees |
| lukman-ps-fees.pdf | fees |
| lukmna-primary-school-fees-structure.pdf | fees |
| PLE-ANALYSIS_100141.pdf | results |
| LUKMAN-PRIMARY-SCHOOL-DAILY-ROUTINE.pdf | routine |
| SCHOOL-MENU.pdf | routine |
| SCHOOL-ANTHEM-DUA.pdf | about |
| FEES-STRUCTURE-ADMISSION-FORM.pdf | admissions |
| END-OF-TERM-3-CIRCULAR-2018.pdf | circulars |
| CIRCULAR-TERM-1-2018.pdf | circulars |
| end-of-term-3-circular-2017.pdf | circulars |
| end-of-term-1-circular-2019.pdf | circulars |
| end-of-term-ii-circular-2019.pdf | circulars |
| Porject-Proposal-1.doc-Hareer-Aden.pdf | general |

**Potential Challenges:**
- Some PDFs are duplicates (e.g., multiple circular versions)
- File names have inconsistent naming conventions

**Mitigation:**
- Deduplicate by file hash
- Rename files to consistent slug format on copy

---

### Step 3.8: Migrate Academic Results Data
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
1. Extract PLE results from 16+ blog posts in 'examinations-results' category
2. Parse HTML tables within posts to extract structured data
3. Results span years 2007-2023, both Secular (Centre 530253) and Theology (Centre 97)
4. Insert into `academic_results` table with year, exam_type, and results_data

**Potential Challenges:**
- Results are embedded as HTML tables within blog post content  
- Varying table formats across years
- Some results may be in images rather than text

**Mitigation:**
- Start with clean text extraction; enhance with HTML table parsing later
- For image-based results, link to the original image in the results entry
- Verify each year's data manually after extraction

---

## Phase 4: Core Page Development

### Step 4.1: Homepage (`index.php`)
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
Replace dtem-web health ministry homepage with school homepage:

| Section | Content |
|---------|---------|
| Hero Banner | School name, motto, hero image of campus, CTA buttons (Apply Now, Virtual Tour) |
| Stats | Years since 2004 (22+), number of staff, students, dual curriculum |
| About Preview | Brief school history excerpt + Read More |
| Dual Curriculum | Secular & Islamic education highlights |
| Head Teacher's Welcome | Brief message + photo |
| Latest News | 3 most recent posts |
| Upcoming Events | Next 3 school events |
| Gallery Preview | 6-8 featured photos grid |
| Testimonials | Rotating parent/student testimonials |
| Downloads Quick Access | Fees Structure, Daily Routine, School Menu PDFs |
| Call to Action | Apply Now, Contact Us, Visit Us |

**What to remove from dtem-web index.php:**
- Health products section
- "Download App" button and Google Play links
- Insurance/Investment sections
- Network marketing content
- Product showcase grid
- "Curing Lives with Ayukalash" messaging
- Members/Products/Memberships/Branches stats

---

### Step 4.2: About Page (`about.php`)
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**Sections (tabbed or accordion layout):**
1. School History — Founded 2004 by UMUWA, growth story
2. Mission & Vision — Mission, Vision, Motto, Commitment
3. Why the Name Lukman — Story of Lukman the Wise (Quran Ch.31)
4. Head Teacher's Message — Full welcome message
5. Chairman SMC's Message — SMC chairman's statement
6. Core Values — School values and principles
7. Organisational Structure — Org chart image
8. School Anthem & Dua — Text + PDF download

**What to remove from dtem-web about.php:**
- DTEHM Health Ministries description
- Dr. Thembo Enostus references
- Healthcare/herbal medicine content
- "Ayukalash" and naturopathy references
- Branch offices section
- Products/Members stats

---

### Step 4.3: Academics Page (`academics.php`) — NEW
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**Sections:**
1. Dual Curriculum Overview — Secular + Islamic Theology
2. Secular Subjects — English, Mathematics, Social Studies, Science
3. Theology Subjects — Quran, Arabic, Islamic History, Belief, Hadeeth, Islamic Laws
4. Grading System — How students are assessed
5. E-Learning Portal — Link/integration with external e-learning
6. Academic Resources — Links or embedded materials

---

### Step 4.4: Results Page (`results.php`) — NEW
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
- Display PLE results by year (2007-2023)
- Show both Secular (Centre 530253) and Theology (Centre 97) results
- Include summary statistics and pass rates
- Option to download PDF results
- Year filter/selector

---

### Step 4.5: Team/Staff Page (`team.php`)
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**Sections:**
1. School Management Committee (SMC)
2. Executive Committee
3. Teaching Staff
4. Support Staff
5. Student Leaders (Prefects)

**What to modify from dtem-web:**
- Replace "branch offices" section with staff departments
- Add sections for different staff categories

---

### Step 4.6: Admissions Page (`admissions.php`) — NEW
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**Sections:**
1. Admission Requirements
2. Fees Structure — Display + PDF download
3. Daily Routine — Display + PDF download
4. School Menu — Display + PDF download
5. School Rules & Regulations — Full rules list (from WP page 11577)
6. Application/Inquiry Form — Online admission inquiry

**What this replaces from dtem-web:**
- `enroll.php` → Adapted as admission inquiry form
- `donate*.php` → Not needed, removed

---

### Step 4.7: Student Life Page (`student-life.php`) — NEW
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**Sections:**
1. Co-Curricular Activities — Sports, clubs, scouts
2. Clubs — English club, Debating, Science, LUPDA
3. Games & Sports — Football, netball, volleyball, cycling
4. Community Service — Community cleaning programs
5. Photo Gallery Preview — Links to gallery

---

### Step 4.8: Gallery Page (`gallery.php`)
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
- Keep dtem-web gallery album → images architecture
- Display 16 school gallery albums as grid with cover images
- Each album opens to `gallery-album.php` with masonry/grid of photos
- Lightbox for full-size image viewing
- Category filtering

---

### Step 4.9: News Page (`news.php`)
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
- Keep dtem-web news listing architecture
- Add category filter (exam results, student life, circulars, events, etc.)
- Pagination (10 posts per page)
- `news-detail.php` for single post view

---

### Step 4.10: Events Page (`events.php`)
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
- Keep dtem-web events architecture
- Add school-specific event types: Academic, Sports, Cultural, Religious, Administrative
- Calendar view option (optional enhancement)

---

### Step 4.11: Downloads Page (`downloads.php`) — NEW
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
- List all downloadable documents categorized
- Categories: Fees, Circulars, Academic, School Info, Forms
- Each document shows: title, category, file size, download count, upload date
- Download counter tracking

---

### Step 4.12: Contact Page (`contact.php`)
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
- Keep dtem-web contact form architecture (name, email, phone, subject, message)
- Add inquiry type selector: General, Admission, Academic, Fees, Other
- School address with Google Maps embed (Entebbe lakeshores location)
- Phone numbers, email, office hours
- WhatsApp quick contact button

**What to remove from dtem-web:**
- Donation/cause references
- Branch office listings

---

### Step 4.13: FAQ Page (`faq.php`)
| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |

**What to do:**
- Keep dtem-web FAQ accordion architecture
- Replace content with school-specific FAQs:
  - Admissions process
  - Fees and payment
  - Boarding vs. day schooling
  - Dual curriculum questions
  - Transport
  - School term dates
  - Uniform requirements
  - Visiting hours

---

### Step 4.14: 404 Page & Sitemap
| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |

**What to do:**
- Customize 404.php with school branding
- Update sitemap.php with all new page URLs

---

## Phase 5: School-Specific Features

> See [SCHOOL-FEATURES.md](SCHOOL-FEATURES.md) for detailed feature specifications.

### Step 5.1: Academic Calendar System
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

Display school term dates, holidays, exam periods, events calendar.

### Step 5.2: PLE Results Archive & Dashboard
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

Historical PLE results with charts/graphs, pass rate trends, year-over-year comparison.

### Step 5.3: Online Admission Inquiry Form
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

Parents can submit admission interest forms online, stored in DB for admin review.

### Step 5.4: Document Download Center
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

Categorized document downloads with tracking.

### Step 5.5: School Newsletter Subscription
| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |

Email subscription form for school updates (integrate with existing Mailchimp or simple DB storage).

### Step 5.6: Virtual Campus Tour
| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |

Photo gallery with building labels, or embedded Google Maps Street View of campus.

---

## Phase 6: Design & Branding

### Step 6.1: Color Scheme & CSS Variables
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
Replace dtem-web CSS color variables:

| Variable | dtem-web Value | Lukman PS Value | Rationale |
|----------|---------------|-----------------|-----------|
| `--primary` | Blue/Green (#04a028) | Islamic Green (#00723F) | School Islamic identity |
| `--primary-dark` | Dark green | Darker green (#005C32) | Depth |
| `--secondary` | Gold (#FFC107) | Gold (#DAA520) | Wisdom/knowledge |
| `--accent` | — | Cream (#FDF5E6) | Warmth |
| `--text-dark` | Dark gray | #1A1A1A | Readability |
| `--gray-light` | Light gray | #F8F9FA | Background |

**Typography:**
- Primary font: Keep Bootstrap default or use "Inter" / "Poppins"
- Heading accent: Consider Arabic-style font for Quran/Islamic content headers

---

### Step 6.2: Logo & Favicon
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**What to do:**
- Replace dtem-web logo (`ulfa-logo.png`) with Lukman PS logo
- Check if logo exists in WP uploads, extract it
- Create favicon from logo
- Update `getSiteLogo()` and `getSiteFavicon()` defaults in functions.php

**Potential Challenges:**
- School may not have a high-res digital logo
- May need to use text-based logo initially

**Mitigation:**
- Use Font Awesome school icon + text as fallback logo
- Admin can upload logo later through settings panel

---

### Step 6.3: Hero Images & Banners
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
- Select best campus/school photos from WP uploads for hero banners
- Optimize for web (compress, resize to 1920px max)
- Create page-specific header backgrounds
- Replace all dtem-web health imagery

---

### Step 6.4: Icon Strategy
| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |

**Replace dtem-web icons:**
| dtem-web Icon | Lukman PS Icon | Context |
|--------------|---------------|---------|
| `fa-heartbeat` | `fa-school` | Logo/brand |
| `fa-leaf` | `fa-book-quran` | Islamic education |
| `fa-pills` | `fa-graduation-cap` | Academic |
| `fa-stethoscope` | `fa-chalkboard-teacher` | Teaching |
| `fa-hospital` | `fa-mosque` | Islamic identity |
| `fa-hand-holding-heart` | `fa-child` | Student welfare |

---

## Phase 7: Admin Panel Adaptation

### Step 7.1: Admin Dashboard Customization
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
- Change dashboard title/branding to "Lukman PS Admin"
- Update dashboard stats cards:
  - Total News Posts
  - Total Events
  - Total Gallery Albums / Photos
  - Total Contact Inquiries
  - Total Document Downloads
  - Total Admission Inquiries

**What to remove from dtem-web admin dashboard:**
- Donation stats/charts
- Product/shop stats
- Insurance stats
- Member stats

---

### Step 7.2: Add Downloads/Documents Admin
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
Create admin CRUD pages for document management:
- `admin/downloads.php` — List documents
- `admin/downloads-add.php` — Upload new document
- `admin/downloads-edit.php` — Edit document info
- `admin/downloads-delete.php` — Delete document

---

### Step 7.3: Add Testimonials Admin
| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |

Create admin CRUD pages for testimonials:
- `admin/testimonials.php` — List testimonials
- `admin/testimonials-add.php` — Add testimonial
- `admin/testimonials-edit.php` — Edit testimonial
- `admin/testimonials-delete.php` — Delete testimonial

---

### Step 7.4: Add Academic Results Admin
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

Create admin pages for PLE results management:
- `admin/results.php` — List results by year
- `admin/results-add.php` — Add new year's results
- `admin/results-edit.php` — Edit results

---

### Step 7.5: Remove dtem-web Specific Admin Pages
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

**Remove these admin files:**
- `admin/donations.php`, `admin/donations-view.php`, `admin/donations-export.php` — No donations
- All donation-related schemas

**Remove these admin nav items from `admin/includes/header.php`:**
- Donations link
- Any health/product references

---

### Step 7.6: Update Admin Settings Categories
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

**What to do:**
Update `admin/settings.php` to show school-specific setting groups:
1. School Information (name, tagline, mission, vision, motto, founding year)
2. Contact Information (email, phone, address, maps)
3. Social Media Links
4. Branding (logo, favicon, colors)
5. SEO & Meta Tags
6. Academic Settings (current term, year, exam dates)
7. Footer Settings

**Remove setting groups:**
- Pesapal/Payment settings
- API/Product settings
- Insurance/Investment settings
- Network marketing settings

---

## Phase 8: Testing & QA

> See [TESTING-PLAN.md](TESTING-PLAN.md) for detailed testing procedures.

### Step 8.1: Functional Testing
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

Test every page, form, and feature.

### Step 8.2: Content Verification
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

Verify all migrated content accuracy.

### Step 8.3: Responsive & Cross-Browser Testing
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

Test on mobile, tablet, desktop + Safari, Chrome, Firefox.

### Step 8.4: Security Testing
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

SQL injection, XSS, CSRF, authentication tests.

### Step 8.5: Performance Testing
| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |

Page load times, image optimization, caching.

---

## Phase 9: Deployment Preparation

### Step 9.1: Production Config
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

- Create `config-production.php` for lukmanps.ac.ug
- Set ENVIRONMENT to 'production'
- Configure production DB credentials
- Set error display OFF, error logging ON

### Step 9.2: DNS & Hosting Setup
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

- Configure lukmanps.ac.ug to point to new PHP site
- Set up SSL certificate (Let's Encrypt)
- Configure Apache/Nginx virtual host

### Step 9.3: Data Export Package
| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |

- Export `lukman_php` database as SQL dump
- Package all files for deployment
- Create deployment instructions document

---

## dtem-web: Removals vs. Additions

### Files to REMOVE (dtem-web specific, not needed for school)

| File | Reason |
|------|--------|
| `donate.php` | No donation system needed |
| `donate-confirm.php` | Donation flow |
| `donate-payment.php` | Donation flow |
| `donate-process.php` | Donation flow |
| `donate-verify.php` | Donation flow |
| `donation-ipn.php` | Pesapal IPN |
| `donation-step1.php` | Donation wizard |
| `donation-step2.php` | Donation wizard |
| `donation-step3.php` | Donation wizard |
| `insurance.php` | Insurance products |
| `investment.php` | Investment platform |
| `shop.php` | Product shop |
| `product-detail.php` | Product detail |
| `network.php` | Network marketing |
| `enroll.php` | Enrollment (replace with admissions) |
| `enroll-production.php` | Enrollment production |
| `get-involved.php` | Volunteering (replace or adapt) |
| `stories.php` | Stories (replace with testimonials) |
| `programs.php` | Health programs |
| `causes.php` | Charity causes (optional keep) |
| `cause-detail.php` | Cause detail (optional keep) |
| `includes/pesapal-config.php` | Payment gateway config |
| `includes/PesapalHelper.php` | Payment helper |
| `admin/donations.php` | Donation admin |
| `admin/donations-view.php` | Donation admin |
| `admin/donations-export.php` | Donation admin |
| `database-donations.sql` | Donation schema |
| `donations-schema.sql` | Donation schema |
| `DONATION-SYSTEM.md` | Donation docs |
| `DTEHM-WEBSITE-PROJECT-PLAN.md` | DTEHM docs |
| `PROJECT-BENCHMARK-DOCUMENTATION.md` | DTEHM docs |
| `SETTINGS-INTEGRATION.md` | DTEHM docs |
| `SECURITY-CHECKLIST.md` | Keep or adapt |
| All `*-backup.php`, `*-old*.php` | Backups |
| All `test-*.php`, `test.php` | Tests |
| `cloudflare-tunnel-setup.sh`, `ngrok-setup.sh` | Tunnel scripts |
| `import_tables.py`, `import_custom_tables.sql` | DTEHM import |
| `new changes.md` | Dev notes |

### Files to KEEP (reusable infrastructure)

| File | Purpose | Changes Needed |
|------|---------|----------------|
| `config.php` | DB configuration | Update DB name, credentials |
| `config-production.php` | Production config | Update for lukmanps.ac.ug |
| `functions.php` | Shared utilities | Remove DTEHM-specific functions, add school functions |
| `index.php` | Homepage | Complete content replacement |
| `about.php` | About page | Complete content replacement |
| `contact.php` | Contact page | Adapt form, update info |
| `news.php` | News listing | Update branding |
| `news-detail.php` | News detail | Update branding |
| `events.php` | Events listing | Update branding |
| `event-detail.php` | Event detail | Update branding |
| `gallery.php` | Gallery listing | Update branding |
| `gallery-album.php` | Album detail | Update branding |
| `faq.php` | FAQ page | Replace Q&As |
| `testimonials.php` | Testimonials | Adapt for school |
| `404.php` | Error page | Update branding |
| `sitemap.php` | Sitemap | Update URLs |
| `includes/header.php` | Page header | Update nav, branding, meta |
| `includes/footer.php` | Page footer | Update links, info |
| `assets/css/style.css` | Main CSS | Update colors, school styles |
| `assets/js/main.js` | Main JS | Minor updates |
| `.htaccess` | URL rules | Update base path |
| `admin/*` (core) | Admin panel | Adapt branding, add/remove modules |
| `admin/config/auth.php` | Authentication | No changes |
| `admin/config/crud-helper.php` | CRUD utilities | No changes |
| `admin/includes/header.php` | Admin nav | Remove DTEHM sections, add school sections |
| `admin/includes/footer.php` | Admin footer | Update branding |
| `robots.txt` | SEO | Update sitemap URL |
| `database.sql` | Base schema | Adapt for school |
| `admin-schema.sql` | Full schema | Adapt + add new tables |
| `settings-setup.sql` | Settings data | Complete replacement with school data |

### Files to ADD (new for school)

| File | Purpose |
|------|---------|
| `academics.php` | Academics/curriculum page |
| `results.php` | PLE results archive |
| `admissions.php` | Admissions info, fees, rules |
| `student-life.php` | Co-curricular activities |
| `downloads.php` | Document download center |
| `team.php` | Complete staff/leadership page |
| `privacy.php` | Privacy policy |
| `admin/downloads.php` | Document admin |
| `admin/downloads-add.php` | Add document |
| `admin/downloads-edit.php` | Edit document |
| `admin/downloads-delete.php` | Delete document |
| `admin/testimonials.php` | Testimonial admin |
| `admin/testimonials-add.php` | Add testimonial |
| `admin/testimonials-edit.php` | Edit testimonial |
| `admin/testimonials-delete.php` | Delete testimonial |
| `admin/results.php` | Results admin |
| `admin/results-add.php` | Add results |
| `admin/results-edit.php` | Edit results |
| `admin/results-delete.php` | Delete results |
| `admin/calendar.php` | Academic calendar admin |
| `admin/faq.php` | FAQ admin |
| `admin/faq-add.php` | Add FAQ |
| `admin/faq-edit.php` | Edit FAQ |
| `admin/faq-delete.php` | Delete FAQ |
| `admin/page-content.php` | Static page content editor |
| `lukman-ps-schema.sql` | School-specific tables |
| `lukman-ps-settings.sql` | School settings data |
| `lukman-ps-seed-data.sql` | Initial content migration data |
| `WORDPRESS-ANALYSIS.md` | WordPress analysis (already created) |
| `TRANSFER-PLAN.md` | This document |
| `SCHOOL-FEATURES.md` | Feature specifications |
| `TESTING-PLAN.md` | Testing procedure |
| `FUTURE-ROADMAP.md` | Post-launch enhancements |

---

## Risk Register

| # | Risk | Likelihood | Impact | Mitigation Strategy |
|---|------|-----------|--------|---------------------|
| 1 | **WordPress shortcode content unreadable** — Some pages have only WPBakery/WoodMart shortcodes with no extractable text | HIGH | HIGH | Backup approach: View WP pages in browser, copy rendered text manually for critical pages |
| 2 | **Image migration volume** — 13,000+ files could slow migration | HIGH | MEDIUM | Filter to originals only (~800-1200), use batch copy script, exclude WP-generated thumbnails |
| 3 | **PLE results data extraction** — Complex HTML tables in blog posts may not parse cleanly | MEDIUM | MEDIUM | Manual verification of each year's data; keep original posts as reference |
| 4 | **Missing school logo/branding** — No high-res digital logo available | MEDIUM | LOW | Use text + icon fallback; admin can upload later |
| 5 | **Production deployment complexity** — Domain DNS, SSL, hosting config | MEDIUM | HIGH | Document every step; create deployment checklist; test on staging first |
| 6 | **Gallery image quality** — Some WP images may be low-res phone uploads | LOW | LOW | Accept as-is; admin can upload better images later |
| 7 | **MAMP vs Production PHP version** — Different PHP behavior | MEDIUM | MEDIUM | Use PHP 8.x compatible code; avoid deprecated functions; test on production PHP version |
| 8 | **Database encoding** — WordPress uses utf8 vs utf8mb4 in some columns | LOW | MEDIUM | Use utf8mb4 throughout; convert on import |
| 9 | **Lost content** — Some WP pages have no useful text (only shortcodes/images) | MEDIUM | LOW | Document gaps; flag for manual content creation |
| 10 | **Admin user training** — School staff unfamiliar with new admin panel | LOW | MEDIUM | Create admin user guide; admin panel is simpler than WP dashboard |

---

## Progress Tracker Summary

| Phase | Steps | Status | Priority |
|-------|-------|--------|----------|
| Phase 1: Foundation | 3 steps | NOT STARTED → Batch A | HIGH |
| Phase 2: Database | 5 steps | NOT STARTED → Batch A | HIGH |
| Phase 3: Content Migration | 8 steps | NOT STARTED → Batches C-E | HIGH |
| Phase 4: Core Pages | 14 steps | NOT STARTED → Batches C-E | HIGH-MEDIUM |
| Phase 5: School Features | 6 steps | NOT STARTED → Batches D-E | MEDIUM-LOW |
| Phase 6: Design & Branding | 4 steps | NOT STARTED → Batch B | HIGH-MEDIUM |
| Phase 7: Admin Panel | 6 steps | NOT STARTED → Batch F | HIGH-MEDIUM |
| Phase 8: Testing | 5 steps | NOT STARTED → Batch G | HIGH |
| Phase 9: Deployment | 3 steps | NOT STARTED → Batch G | HIGH |
| **TOTAL** | **54 steps** | — | — |

> **Planning complete as of 13 April 2026.** See [NEXT-IMPLEMENTATION-BATCH.md](NEXT-IMPLEMENTATION-BATCH.md) for the execution plan organized into 7 focused batches (A-G) with dependencies, strategic enhancements, and priority matrix.

# Lukman Primary School — Website Roadmap

> Last reviewed: April 2026 · No timelines — implement in order of priority

---

## Project Status Overview

| Layer | Status | Notes |
|---|---|---|
| Public pages (21 pages) | ✅ Complete | index, about, academics, admissions, results, downloads, student-life, contact, faq, testimonials, news, news-detail, events, event-detail, gallery, gallery-album, sitemap, robots.txt, 404 |
| Admin — core CRUD | ✅ Complete | news, events, gallery, team, admins, inquiries, login, settings, dashboard |
| Admin — school-specific | ✅ Complete | testimonials, downloads, PLE results, FAQ, admission inquiries, calendar, newsletter (all built in Batch E) |
| Email notifications | ✅ Complete | contact.php + admissions.php send admin email on every submission |
| WhatsApp float button | ✅ Complete | Reads DB settings; auto-hides if disabled |
| Admin badges | ✅ Complete | Testimonials pending + Admission Inquiries new count badges in sidebar |
| Print stylesheets | ✅ Complete | @media print rules in style.css; tested on results + fees pages |
| Cookie consent | ✅ Complete | 1-year cookie, no external library, green LPS branding |
| Production config | ✅ Fixed | config-production.php updated to lukman_php DB and lukmanps.ac.ug domain |
| CSRF protection | ✅ Complete | generateCsrfToken() + validateCsrfToken() in functions.php; contact form wired |
| Public interactivity | ✅ Complete | search.php (G1), notice board strip (G2), social sharing + copy-link on news & events (G4) |
| Islamic features | ✅ Complete | prayer-times.php (H1), quran.php Hifdh Wall (H2), Hijri date in footer (H3), past papers on academics (H4) |
| Parent communication | ❌ Not built | No SMS, no parent portal |

---

## Batch F — Operational Must-Haves ✅ COMPLETE

**All items in this batch are implemented.**

### F1. Email Notifications on Form Submissions
- Contact form (`contact.php`) → email `lukmanps2004@gmail.com`: name, email, subject, message body
- Admissions form (`admissions.php`) → email full parent + child detail + class + boarding preference
- PHP `mail()` with proper `From:` / `Reply-To:` headers
- Upgrade to PHPMailer + Gmail SMTP if `mail()` is blocked on the VPS (likely)
- **Why now:** the school has no idea when parents have submitted contact or admission forms

### F2. WhatsApp Float Button
- Floating button bottom-right on all public pages
- Reads `whatsapp_number` + `whatsapp_default_message` + `enable_whatsapp_chat` from `site_settings`
- URL: `https://wa.me/{number}?text={encoded_message}`
- Animated pulse ring on load; hides if the setting is disabled
- **Why now:** DB columns already exist; this is ~15 lines of HTML/CSS added to `includes/footer.php`

### F3. Admin Notification Badges
- Sidebar badge on **Testimonials** showing pending-approval count
- Sidebar badge on **Admission Inquiries** showing `status = 'new'` count
- Same pattern as the existing contact-inquiry badge in `admin/includes/header.php`
- **Why now:** admin is currently blind to new submissions on these two sections

### F4. Fix config-production.php
- DB name: `dtehm_insurance_api` → `lukman_php`
- Update domain, SMTP credentials placeholder, and all deployment values
- **Why now:** deploy-day confusion risk; this is the first file used on the live server

### F5. Print Stylesheets
- `@media print` rules in `style.css`: hide nav, footer, hero sections, buttons, animations
- Key pages to verify: `results.php` (division table), `admissions.php` (fees table), `event-detail.php`
- **Why now:** parents and staff regularly print fee structures and term results; current printouts are broken

### F6. Cookie Consent Banner
- Dismissible banner: "This site may use cookies. Click Accept to continue." + Accept button
- Sets a 365-day cookie on accept to suppress on return visits
- No external library — plain JS + CSS, ~30 lines total
- Required for Google Analytics consent mode before GA tracking is added

---

## Batch G — Public Site Enhancements ✅ COMPLETE

**Priority: HIGH — Direct impact on parents' and visitors' daily experience**

### G1. Site-Wide Search (`search.php`) ✅
- Created `search.php`: searches `news_posts`, `events`, `faq_items`
- Results grouped by type (News / Event / FAQ) with badges and keyword highlighting
- Context-aware excerpt snippet centred on matched keyword

### G2. Notice Board / Announcements ✅
- Created `notices` table (title, content, type, start_date, end_date, is_pinned, status)
- Built `admin/notices.php`, `admin/notices-add.php`, `admin/notices-edit.php` (full CRUD)
- Notices sidebar link with live-count badge added to admin header
- Public pinned strip injected below navbar in `includes/header.php` — auto-hides expired notices
- Colour-coded by type: info (green), warning (amber), urgent (red), event (dark green)

### G3. Testimonial Submission Form (Public) ✅
- Added POST handler and form to `testimonials.php`
- Fields: name, role, star rating (1–5), content (≥20 chars)
- Submissions INSERT with `status='pending'`; admin approves via Testimonials panel
- Thank-you confirmation displayed after successful submit

### G4. Social Sharing on News & Events ✅
- `news-detail.php`: already had Facebook/Twitter/LinkedIn/WhatsApp — added copy-link button
- `event-detail.php`: already had Facebook/Twitter/WhatsApp — added copy-link button
- Copy-link uses `navigator.clipboard` with animated "Copied!" feedback

### G5. Related Content Below Articles ✅ (was already built)
- `news-detail.php` already had related articles; `event-detail.php` has related events too

### G6. Google Maps Embed on Contact Page ✅
- Added `<iframe>` Google Maps embed (search query: Lukman Primary School Entebbe Uganda)
- "Get Directions on Google Maps" link opens in new tab
- Styled with border-radius + box-shadow matching the page design

### G7. Breadcrumb Navigation ✅
- Created `includes/breadcrumb.php` with `breadcrumb()` helper function
- Outputs Schema.org `BreadcrumbList` JSON-LD + Bootstrap `.breadcrumb` nav
- Added to: news.php, news-detail.php, events.php, event-detail.php, about.php, contact.php, faq.php, testimonials.php, gallery.php, gallery-album.php, search.php
- CSS added to `assets/css/style.css` (`.lps-breadcrumb`)

### G8. Gallery Swipe on Mobile ✅
- Added `touchstart` / `touchend` detection to lightbox JS in `gallery-album.php`
- Horizontal swipe > 50px triggers `nextImage()` / `previousImage()`
- Uses `{ passive: true }` listeners for smooth scrolling performance

---

## Batch H — Islamic School–Specific Features ✅ COMPLETE

**Priority: HIGH — These features directly reflect LPS's identity and resonate most strongly with the school community**

### ✅ H1. Prayer Timetable Page (`prayer-times.php`) — BUILT
- Full static monthly prayer-times table for Entebbe, Uganda (lat 0.05°N, lon 32.47°E UTC+3)
- Live JS clock (`setInterval`) highlights current prayer with a NOW badge + countdown to next Salah
- Hero banner with decorative Islamic SVG geometric pattern and crescent icon
- Annual timetable grid with current month highlighted; school prayer-routine 4-card section
- Linked from new "Islamic" dropdown in public navigation

### ✅ H2. Qur'an Memorisation (Hifdh) Wall of Fame — BUILT
- Dedicated `quran.php` public recognition page with tier badges:
  - Bronze (Juz 1, Juz 5) · Silver (Juz 10, Juz 15) · Gold (Juz 20, Half Hifdh) · Diamond (Full Hifdh)
- Diamond tier: gold shimmer banner + circular photo + Qur'anic blessing du'ā in Arabic
- Milestone ladder graphic; Hifdh programme info with progress bars
- Arabic hadith hero: `خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ`
- Full admin CRUD: `admin/hifdh.php` (list + toggle + delete), `admin/hifdh-add.php`, `admin/hifdh-edit.php`
- Photo upload to `uploads/hifdh/`; `hifdh_achievements` DB table with 4 sample rows seeded
- "Ḥifẓ Wall" entry added to admin sidebar with live badge count

### ✅ H3. Islamic Calendar & Hijri Date — BUILT
- `gregorianToHijri()`, `getHijriMonthName()`, `getHijriDateToday()` added to `functions.php`
- Hijri date displayed in site footer alongside Gregorian date with gold moon icon
- e.g. "🌙 16 Shawwal 1447 AH · 14 April 2026"

### ✅ H4. Resource Library — Past Papers & Study Materials — BUILT
- Dedicated section on `academics.php` before the CTA, grouped by category
- DB-driven from existing `downloads` table (`category IN ('academic','past_papers','revision','islamic_studies')`)
- Empty-state shows 4 descriptive preview cards with "Browse" links per subject area
- "View All Downloads" CTA button linking to `downloads.php`

---

## Batch I — Parent Communication & Engagement

**Priority: MEDIUM — Parents are the school's primary decision-makers and most important audience**

### I1. SMS Notifications via Africa's Talking API
- PHP SMS wrapper class around the Africa's Talking REST API
- Admin compose screen: message text + recipient group (all parents / boarders / specific class)
- Triggered automatically for: term start announcements, PLE results release, urgent school news
- Parent phone numbers from `admission_inquiries` or a dedicated `parent_contacts` table
- **Why:** Uganda parents respond to SMS faster than any other channel; cost is ~UGX 65 per message

### I2. Newsletter / Email Broadcasting
- `newsletter_subscribers` table already populates from the footer sign-up form
- Admin compose screen: subject + rich-text body + send to all active subscribers
- PHPMailer + Gmail SMTP or SendGrid free tier
- One-click token-based unsubscribe link in every email (no login required)
- **Why:** subscribers are already accumulating; the broadcast mechanism is the only missing piece

### I3. Fee Payment Records (Admin-Side First)
- Admin records fee payment: date, amount, term, payment method (cash / MoMo / bank), parent name
- Generates automatic email receipt to the parent
- Phase 2: parent views payment history via the portal in I4
- No payment gateway required at this stage — record-keeping only
- **Why:** eliminates cash ambiguity; gives parents a digital confirmation they trust

### I4. Parent Information Portal
- Phase 1 (read-only): parents log in with email + password
- Can view: admission inquiry status, downloaded circulars, upcoming term dates
- `parent_users` table: email, bcrypt password, linked child record, status
- Phase 2: add fee payment history and downloadable term report PDFs
- **Why:** even a minimal login builds loyalty and regular return visits from parents

---

## Batch J — Performance, Security & Technical Health

**Priority: MEDIUM — Required before scaling or going fully live**

### J1. CSRF Tokens on All POST Forms
- Generate per-session CSRF token stored in `$_SESSION['csrf_token']`
- Embed as a hidden `<input>` in every POST form: contact, admissions, newsletter, all admin forms
- Verify on every POST; reject with HTTP 403 if mismatch
- **Why:** all public POST forms are currently CSRF-vulnerable — an OWASP Top 10 issue

### J2. Rate Limiting on Public Forms
- Maximum 3 submissions per IP per hour on contact and admissions forms
- Track in `form_rate_limits` table: ip, form_name, attempt_count, window_start
- Return HTTP 429 with a friendly "please try again later" message
- **Why:** unprotected contact forms attract automated spam submissions

### J3. Image Optimisation on Upload
- Auto-resize via PHP GD on upload: gallery images ≤ 1200 px wide, thumbnails at 400 px
- Convert to WebP with JPEG fallback using `<picture>` element
- Strip EXIF / metadata from all uploaded photos
- **Why:** unoptimised gallery uploads are the main cause of slow page loads

### J4. Page Output Caching
- Cache rendered HTML for read-only public pages (homepage, about, academics) as flat files, 10-minute TTL
- Auto-bust cache when admin saves any content or changes a site setting
- Plain PHP file cache — no Redis or Memcached needed at this traffic level
- **Why:** shared VPS hosting degrades quickly under concurrent visitors; file cache gives 5–10× throughput improvement

### J5. Content Security Policy Header
- Add CSP via `.htaccess`: allowlist self + Bootstrap CDN + Font Awesome CDN + AOS CDN + Google Fonts
- Run in Report-Only mode for one week; enforce after reviewing violation logs
- **Why:** a structural XSS defence layer on top of `htmlspecialchars()` throughout the codebase

### J6. Admin Activity Log Display
- `admin_activity_log` table already exists in the schema
- Write a log entry on every: login, create, update, delete, settings change
- Display the last 50 entries on the admin dashboard
- Columns: admin username, action type, table affected, record ID, timestamp, IP address
- **Why:** accountability and audit trail; easy to diagnose accidental deletions

### J7. 301 Redirects from WordPress URLs
- Map old WordPress permalink slugs (e.g. `/about-us/`) to new PHP pages (`/about.php`)
- Added as `RewriteRule` entries in `.htaccess` before going live
- **Why:** preserves any Google ranking credit from the previous WordPress site

---

## Batch K — Long-Term Vision

**Priority: LOW — Strategic investments for year 2 and beyond**

### K1. Progressive Web App (PWA)
- `manifest.json` + service worker with offline caching
- Offline availability for: about, contact, results, school calendar
- Add-to-homescreen prompt on Android
- Push notifications for school announcements (opt-in)
- **Why:** majority of visitors are on Android; a PWA delivers a near-native experience without an App Store listing

### K2. Online Fee Payment (MTN MoMo / Airtel Money)
- Pesapal integration (credentials schema already in `site_settings`)
- Admin generates a fee invoice per child; parent pays via mobile money
- Webhook confirmation → automatic email + SMS receipt
- **Why:** eliminates cash handling at the school gate; creates auditable digital payment records

### K3. Student Report Card Portal
- Admin uploads a term report PDF per student per term
- Parents log in (I4 portal) to download their child's report
- Organised by: academic year → term
- **Why:** significant reduction in paper printing and distribution workload for staff

### K4. Interactive Class Timetable
- Visual timetable per class (P1–P7): days × periods grid
- Admin builds timetable per class in a structured UI
- Public display on `academics.php` with browser-print support
- **Why:** the class timetable is among the most frequently requested documents by parents

### K5. Alumni Network
- Former pupil registration: graduation year, current school or career, optional photo — opt-in only
- Public alumni showcase page
- Connected to PLE results history for continuity storytelling
- **Why:** alumni are the school's most credible enrolment marketing asset; their stories convert prospective parents

### K6. Multi-Language Support (Arabic & Luganda)
- Language toggle on key pages: About, FAQ, Contact, Admissions
- `page_content` table extended with a `lang` column
- Arabic pages use `dir="rtl"` on the `<html>` element
- **Why:** a significant portion of the parent community reads Arabic primarily; Luganda for Baganda parents

### K7. Virtual Open Day
- Dedicated page with an embedded video tour of school facilities
- Online visit booking form: parent selects a date and preferred time
- Automatic confirmation email to parent and notification to admin
- **Why:** post-2020 parents expect schools to have a visible digital presence before committing to an enrolment visit

---

## Admin Panel — Page Status Tracker

| Sidebar Link | Admin Page | Status | Batch |
|---|---|---|---|
| Dashboard | index.php | ✅ Done | — |
| News & Blog | news.php + add/edit/delete | ✅ Done | — |
| Events | events.php + add/edit/delete | ✅ Done | — |
| Gallery | gallery.php + albums + images | ✅ Done | — |
| Our Team | team.php + add/edit/delete | ✅ Done | — |
| Testimonials | testimonials.php + add/edit | ✅ Done | E1 |
| Downloads | downloads.php + add/edit | ✅ Done | E2 |
| PLE Results | results.php + add/edit | ✅ Done | E3 |
| Admission Inquiries | admission-inquiries.php | ✅ Done | E4 |
| FAQ | faq.php + add/edit | ✅ Done | E5 |
| School Calendar | calendar.php + add/edit | ✅ Done | E6 |
| Newsletter | newsletter.php | ✅ Done | E7 |
| Inquiries (contact) | inquiries.php + view | ✅ Done | — |
| Site Settings | settings.php | ✅ Done | — |
| Admin Users | admins.php + add/edit | ✅ Done | — |
| Notice Board | notices.php + add/edit | ✅ Done | G2 |
| Prayer Times | prayer-times.php (public, static table) | ✅ Done | H1 |
| Ḥifẓ Wall | hifdh.php + add/edit | ✅ Done | H2 |
| SMS Broadcast | sms.php | ❌ Not built | I1 |
| Email Broadcast | broadcast.php | ❌ Not built | I2 |
| Fee Records | fees.php | ❌ Not built | I3 |
| Activity Log | (embedded in dashboard) | ❌ Not built | J6 |

---

## Maintenance Schedule

### Every Week
- Log in and check: contact inquiries + admission inquiries + pending testimonials
- Respond to new submissions within 48 hours
- Check PHP error log: `error_log` in project root

### Every Term (3× per year)
- Update school calendar: new term dates, exams, events
- Upload new circulars or fee notices to downloads
- Upload PLE results if a P7 cohort graduated
- Add new term photos to gallery
- Review and approve or reject pending testimonials
- Audit all nav links for 404s in the server access log

### Annually
- SSL certificate renewal (before expiry)
- Full site + database backup archived off-server
- Review and update all `site_settings` values: phone, email, fees, WhatsApp number
- Update team members: add new staff, mark departed as inactive
- Reprioritise this roadmap based on the school's evolving needs

---

## Technology Stack

| Layer | Current | Future Option |
|---|---|---|
| Language | PHP 8.x | Stay — right scale for this project |
| Framework | Plain PHP + custom helpers | Slim/Lumen if a REST API is ever needed |
| Frontend CSS | Bootstrap 5.3 + CSS variables | Tailwind if a full redesign is undertaken |
| Frontend JS | jQuery 3.7 + vanilla JS | Reduce jQuery; add Alpine.js for selective reactivity |
| Database | MySQL 8 via PDO | Stay |
| Email | PHP mail() | PHPMailer + Gmail SMTP (Batch F1) |
| SMS | Not yet | Africa's Talking API (Batch I1) |
| Media storage | Local /uploads/ | Cloudinary or S3 if image volume grows significantly |
| Hosting | MAMP (dev) → VPS (prod) | Nginx + PHP-FPM on DigitalOcean or Contabo |
| Deployment | Manual rsync / FTP | Git push + webhook deploy script |
| PWA | Not yet | service-worker.js (Batch K1) |

---

## Success Metrics

| Metric | Target |
|---|---|
| Page load time | Under 3 seconds on 3G mobile |
| Mobile usability | 90+ score on Google PageSpeed Insights |
| Monthly unique visitors | 500+ after launch |
| Admission inquiries via site | 20+ per term |
| Contact form submissions | 10+ per month |
| Admin content updates | At least weekly |
| Site uptime | 99.5%+ |
| Google ranking | Page 1 for "Lukman Primary School Entebbe" |
| WhatsApp click-throughs | 5+ per week |
| Newsletter subscribers | 100+ within the first school year |

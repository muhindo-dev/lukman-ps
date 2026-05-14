# Lukman Primary School - Testing Plan

## Overview

This document defines the testing strategy for the new Lukman PS PHP website. Testing should be performed after each phase of development and comprehensively before deployment.

---

## 1. Functional Testing

### 1.1 Navigation & Page Loading

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 1.1.1 | Load homepage (`index.php`) | Page loads, all sections render, no PHP errors | PENDING | |
| 1.1.2 | Load about page (`about.php`) | All sections display: history, mission/vision, headteacher msg, chairman msg, why lukman, anthem | PENDING | |
| 1.1.3 | Load academics page (`academics.php`) | Curriculum, grading, e-learning sections render | PENDING | |
| 1.1.4 | Load results page (`results.php`) | Results by year display, year filter works | PENDING | |
| 1.1.5 | Load admissions page (`admissions.php`) | Fees, routine, rules, menu sections render with PDF downloads | PENDING | |
| 1.1.6 | Load student life page (`student-life.php`) | Activities, clubs, sports sections render | PENDING | |
| 1.1.7 | Load team page (`team.php`) | All staff categories display with photos | PENDING | |
| 1.1.8 | Load gallery page (`gallery.php`) | Album grid displays with cover images | PENDING | |
| 1.1.9 | Load gallery album (`gallery-album.php?id=X`) | Individual album photos display in grid | PENDING | |
| 1.1.10 | Load news page (`news.php`) | News posts list with pagination | PENDING | |
| 1.1.11 | Load news detail (`news-detail.php?slug=X`) | Single post displays with full content | PENDING | |
| 1.1.12 | Load events page (`events.php`) | Events list displays | PENDING | |
| 1.1.13 | Load event detail (`event-detail.php?slug=X`) | Single event displays | PENDING | |
| 1.1.14 | Load contact page (`contact.php`) | Form renders, map displays, contact info shows | PENDING | |
| 1.1.15 | Load downloads page (`downloads.php`) | Documents listed by category | PENDING | |
| 1.1.16 | Load FAQ page (`faq.php`) | FAQ accordion renders and toggles | PENDING | |
| 1.1.17 | Load 404 page (invalid URL) | Custom 404 page displays | PENDING | |
| 1.1.18 | Test all header navigation links | Every link goes to correct page | PENDING | |
| 1.1.19 | Test all footer links | Every link goes to correct page or URL | PENDING | |
| 1.1.20 | Test breadcrumb navigation | Breadcrumbs show correct path | PENDING | |

### 1.2 Contact Form

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 1.2.1 | Submit with all valid fields | Success message, record saved in DB | PENDING | |
| 1.2.2 | Submit with empty required fields | Validation error, form not submitted | PENDING | |
| 1.2.3 | Submit with invalid email | Email validation error | PENDING | |
| 1.2.4 | Submit with invalid phone | Phone validation error | PENDING | |
| 1.2.5 | Submit duplicate within 1 hour | Duplicate detection message | PENDING | |
| 1.2.6 | Verify inquiry saved in `contact_inquiries` table | Record present with correct data | PENDING | |
| 1.2.7 | Test inquiry type selector | Different types saved correctly | PENDING | |

### 1.3 Admission Inquiry Form

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 1.3.1 | Submit with all valid fields | Success message, record saved | PENDING | |
| 1.3.2 | Submit with missing required fields | Validation errors | PENDING | |
| 1.3.3 | Submit with future DOB | Validation error (child not born) | PENDING | |
| 1.3.4 | Test class selector (P1-P7) | All options available | PENDING | |
| 1.3.5 | Test boarding/day selector | Both options work | PENDING | |
| 1.3.6 | Verify record in `admission_inquiries` table | Data saved correctly | PENDING | |

### 1.4 Downloads

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 1.4.1 | Click PDF download link | File downloads successfully | PENDING | |
| 1.4.2 | Download counter increments | `download_count` increased by 1 | PENDING | |
| 1.4.3 | Filter by category (fees) | Only fees documents shown | PENDING | |
| 1.4.4 | Filter by category (circulars) | Only circulars shown | PENDING | |
| 1.4.5 | All 19 migrated PDFs accessible | No broken download links | PENDING | |

### 1.5 Gallery

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 1.5.1 | Gallery albums page loads | 16 albums displayed as grid | PENDING | |
| 1.5.2 | Click album opens album page | Photos display correctly | PENDING | |
| 1.5.3 | Click photo opens lightbox | Full-size image shown | PENDING | |
| 1.5.4 | Lightbox navigation (next/prev) | Navigate between album photos | PENDING | |
| 1.5.5 | Category filter works | Filters albums by category | PENDING | |
| 1.5.6 | Pagination works (if many albums) | Page navigation functional | PENDING | |

### 1.6 News / Blog

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 1.6.1 | News listing shows 10 per page | Correct count, first page | PENDING | |
| 1.6.2 | Pagination to page 2+ works | Next page shows different posts | PENDING | |
| 1.6.3 | Category filter (exam-results) | Only exam result posts shown | PENDING | |
| 1.6.4 | News detail page shows full content | Full post content rendered | PENDING | |
| 1.6.5 | All 39 migrated posts accessible | No missing posts | PENDING | |
| 1.6.6 | Featured images display | Post thumbnails visible | PENDING | |

### 1.7 Academic Results

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 1.7.1 | Results page loads | Default year shown | PENDING | |
| 1.7.2 | Year selector works | Switching years loads different data | PENDING | |
| 1.7.3 | Secular/Theology toggle works | Correct results per type | PENDING | |
| 1.7.4 | Summary stats display | Division counts, pass rate | PENDING | |
| 1.7.5 | Trend chart renders | Chart.js graph visible | PENDING | |

---

## 2. Admin Panel Testing

### 2.1 Authentication

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.1.1 | Login with correct credentials | Redirect to dashboard | PENDING | |
| 2.1.2 | Login with wrong password | Error message, stay on login | PENDING | |
| 2.1.3 | Access admin page without login | Redirect to login | PENDING | |
| 2.1.4 | Logout | Session cleared, redirect to login | PENDING | |
| 2.1.5 | Session timeout (30 min idle) | Auto-logout on next action | PENDING | |

### 2.2 Admin Dashboard

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.2.1 | Dashboard loads | Stats cards show correct counts | PENDING | |
| 2.2.2 | News count correct | Matches `news_posts` table count | PENDING | |
| 2.2.3 | Gallery count correct | Matches album count | PENDING | |
| 2.2.4 | Inquiry count correct | Matches inquiry table count | PENDING | |
| 2.2.5 | Recent activity shows | Latest admin actions listed | PENDING | |

### 2.3 News CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.3.1 | Create new post | Post saved, appears in list | PENDING | |
| 2.3.2 | Edit existing post | Changes saved correctly | PENDING | |
| 2.3.3 | Delete post | Post removed from list | PENDING | |
| 2.3.4 | Upload featured image | Image saved to uploads/ | PENDING | |
| 2.3.5 | Publish/Draft toggle | Status updated correctly | PENDING | |
| 2.3.6 | Slug auto-generation | Slug created from title | PENDING | |

### 2.4 Events CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.4.1 | Create new event | Event saved | PENDING | |
| 2.4.2 | Edit existing event | Changes saved | PENDING | |
| 2.4.3 | Delete event | Event removed | PENDING | |
| 2.4.4 | Event date/time picker works | Dates save correctly | PENDING | |

### 2.5 Gallery CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.5.1 | Create new album | Album saved | PENDING | |
| 2.5.2 | Upload images to album | Images saved and displayed | PENDING | |
| 2.5.3 | Edit album details | Changes saved | PENDING | |
| 2.5.4 | Delete image from album | Image removed | PENDING | |
| 2.5.5 | Delete entire album | Album and images removed | PENDING | |
| 2.5.6 | Reorder images | Display order updated | PENDING | |

### 2.6 Team CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.6.1 | Add team member | Member saved with photo | PENDING | |
| 2.6.2 | Edit team member | Changes saved | PENDING | |
| 2.6.3 | Delete team member | Member removed | PENDING | |
| 2.6.4 | Reorder team members | Display order updated | PENDING | |

### 2.7 Downloads CRUD

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.7.1 | Upload new document | File saved, record created | PENDING | |
| 2.7.2 | Edit document info | Title/category updated | PENDING | |
| 2.7.3 | Delete document | File and record removed | PENDING | |
| 2.7.4 | File type validation | Only PDF/DOC accepted | PENDING | |

### 2.8 Settings Management

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 2.8.1 | Update site name | Change reflected on frontend | PENDING | |
| 2.8.2 | Update contact info | Footer/contact page updated | PENDING | |
| 2.8.3 | Upload logo | Logo displayed on site | PENDING | |
| 2.8.4 | Upload favicon | Favicon updated in browser | PENDING | |
| 2.8.5 | Update social media URLs | Footer links updated | PENDING | |
| 2.8.6 | Update mission/vision | About page updated | PENDING | |

---

## 3. Content Verification Testing

### 3.1 Migrated Content Accuracy

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 3.1.1 | School history text matches WordPress | Identical content (minus shortcodes) | PENDING | |
| 3.1.2 | Mission/Vision text correct | Exact match with WP page 11736 | PENDING | |
| 3.1.3 | Head teacher's message complete | Full message from WP page 12246 | PENDING | |
| 3.1.4 | Chairman's message complete | Full message from WP page 12254 | PENDING | |
| 3.1.5 | Why Lukman text complete | Full story from WP page 11727 | PENDING | |
| 3.1.6 | School rules complete | All rules from WP page 11577 | PENDING | |
| 3.1.7 | All 6 team members present | Names, photos, positions correct | PENDING | |
| 3.1.8 | All 6 testimonials present | Names, content correct | PENDING | |
| 3.1.9 | All 39 blog posts migrated | Title, content, dates match | PENDING | |
| 3.1.10 | All 16 gallery albums present | Correct names & image counts | PENDING | |
| 3.1.11 | All 19 PDFs downloadable | Files open correctly | PENDING | |
| 3.1.12 | PLE results data (2007-2023) | Results match WordPress posts | PENDING | |
| 3.1.13 | No WordPress shortcode artifacts | No `[vc_row]`, `[woodmart_*]` in content | PENDING | |
| 3.1.14 | No DTEHM/ULFA references remain | All replaced with Lukman PS | PENDING | |
| 3.1.15 | Images display without broken links | All images render properly | PENDING | |

---

## 4. Responsive & Cross-Browser Testing

### 4.1 Mobile Responsiveness

| # | Test Case | Device/Width | Status | Notes |
|---|-----------|-------------|--------|-------|
| 4.1.1 | Homepage renders | iPhone SE (375px) | PENDING | |
| 4.1.2 | Homepage renders | iPhone 12/13 (390px) | PENDING | |
| 4.1.3 | Homepage renders | Samsung Galaxy (360px) | PENDING | |
| 4.1.4 | Navigation hamburger menu works | All mobile widths | PENDING | |
| 4.1.5 | Gallery grid adjusts | Mobile (1 col) | PENDING | |
| 4.1.6 | Tables scroll horizontally | Results/data tables on mobile | PENDING | |
| 4.1.7 | Forms are usable on mobile | All form inputs accessible | PENDING | |
| 4.1.8 | PDF download buttons visible | Mobile footer not covered | PENDING | |
| 4.1.9 | Font sizes readable | Minimum 16px body text | PENDING | |
| 4.1.10 | Touch targets adequate | Min 44px tap targets | PENDING | |

### 4.2 Tablet

| # | Test Case | Device/Width | Status | Notes |
|---|-----------|-------------|--------|-------|
| 4.2.1 | All pages render | iPad (768px) | PENDING | |
| 4.2.2 | All pages render | iPad Pro (1024px) | PENDING | |
| 4.2.3 | Gallery grid adapts | 2-3 columns | PENDING | |

### 4.3 Desktop

| # | Test Case | Device/Width | Status | Notes |
|---|-----------|-------------|--------|-------|
| 4.3.1 | All pages render | 1280px | PENDING | |
| 4.3.2 | All pages render | 1920px | PENDING | |
| 4.3.3 | Max-width container respected | No ultra-wide stretching | PENDING | |

### 4.4 Browser Compatibility

| # | Browser | Version | Status | Notes |
|---|---------|---------|--------|-------|
| 4.4.1 | Chrome | Latest | PENDING | |
| 4.4.2 | Safari | Latest (macOS) | PENDING | |
| 4.4.3 | Safari Mobile | iOS 16+ | PENDING | |
| 4.4.4 | Firefox | Latest | PENDING | |
| 4.4.5 | Edge | Latest | PENDING | |
| 4.4.6 | Samsung Internet | Latest | PENDING | |

---

## 5. Security Testing

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 5.1 | SQL injection on contact form | Input sanitized, no SQL execution | PENDING | Test with `'; DROP TABLE--` |
| 5.2 | SQL injection on news search | Input sanitized via prepared statements | PENDING | |
| 5.3 | XSS on contact form name field | HTML escaped, no script execution | PENDING | Test with `<script>alert(1)</script>` |
| 5.4 | XSS on news detail page | Post content sanitized | PENDING | |
| 5.5 | CSRF on contact form | Token validation, reject without token | PENDING | |
| 5.6 | CSRF on admin forms | Token validation on all admin POSTs | PENDING | |
| 5.7 | Direct admin URL access without login | Redirect to login page | PENDING | |
| 5.8 | Admin session hijacking | Session regenerated on login | PENDING | |
| 5.9 | File upload validation (admin) | Only allowed file types accepted | PENDING | |
| 5.10 | Directory listing disabled | No directory browsing in uploads/ | PENDING | |
| 5.11 | Error messages don't leak info | No DB credentials in error pages | PENDING | |
| 5.12 | Password hashing uses bcrypt | `password_hash()` with PASSWORD_DEFAULT | PENDING | |
| 5.13 | HTTP-only session cookies | Cannot access session via JS | PENDING | |
| 5.14 | Rate limiting on login attempts | Brute force prevented | PENDING | |

---

## 6. Performance Testing

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 6.1 | Homepage load time | < 3 seconds on 3G | PENDING | |
| 6.2 | Gallery page load time | < 4 seconds (image heavy) | PENDING | |
| 6.3 | Image optimization | All images < 300KB | PENDING | |
| 6.4 | CSS minification | style.min.css served | PENDING | |
| 6.5 | JS minification | main.min.js served | PENDING | |
| 6.6 | Browser caching headers | Images/CSS/JS cached 30+ days | PENDING | |
| 6.7 | Gzip compression enabled | Text assets compressed | PENDING | |
| 6.8 | Database query count per page | < 20 queries per page load | PENDING | |
| 6.9 | No N+1 query problems | Efficient joins/batch queries | PENDING | |

---

## 7. SEO Testing

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 7.1 | Every page has unique `<title>` | Page-specific title + site name | PENDING | |
| 7.2 | Every page has `<meta description>` | Page-specific description | PENDING | |
| 7.3 | Open Graph tags present | og:title, og:description, og:image | PENDING | |
| 7.4 | Schema.org structed data | Organization, WebSite schemas | PENDING | |
| 7.5 | Canonical URLs set | Correct canonical for each page | PENDING | |
| 7.6 | robots.txt correct | Allows indexing, points to sitemap | PENDING | |
| 7.7 | sitemap.php lists all pages | All public pages listed | PENDING | |
| 7.8 | No duplicate content | Each URL has unique content | PENDING | |
| 7.9 | Images have alt attributes | Descriptive alt text on all images | PENDING | |
| 7.10 | Heading hierarchy correct | Single H1, logical H2-H6 order | PENDING | |

---

## 8. Accessibility Testing

| # | Test Case | Expected Result | Status | Notes |
|---|-----------|-----------------|--------|-------|
| 8.1 | Color contrast ratio | Minimum 4.5:1 for body text | PENDING | |
| 8.2 | Keyboard navigation | All interactive elements focusable | PENDING | |
| 8.3 | Form labels associated | All inputs have labels | PENDING | |
| 8.4 | Skip navigation link | "Skip to content" link present | PENDING | |
| 8.5 | ARIA landmarks | Main, nav, footer landmarks | PENDING | |
| 8.6 | Image alt text | All images have meaningful alt | PENDING | |

---

## Test Execution Log

| Date | Phase Tested | Tester | Pass/Fail | Issues Found | Notes |
|------|-------------|--------|-----------|-------------|-------|
| — | — | — | — | — | Testing not yet started |

---

## Issue Tracking Template

| # | Issue Description | Severity | Page/Feature | Status | Resolution |
|---|-------------------|----------|-------------|--------|------------|
| 1 | — | — | — | OPEN | — |

**Severity Levels:**
- **CRITICAL**: Site broken, data loss, security vulnerability
- **HIGH**: Feature not working, significant UI issue
- **MEDIUM**: Feature partially working, minor UI issue
- **LOW**: Cosmetic, enhancement suggestion

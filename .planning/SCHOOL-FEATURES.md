# Lukman Primary School - School-Specific Features

## Overview

This document details the school-specific features to be added to the Lukman PS website beyond what the dtem-web benchmark provides. These features are designed to serve three primary audiences: **parents**, **students**, and **staff/administration**.

---

## Feature 1: Academic Results Dashboard

| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |
| **Audience** | Parents, Students, Prospective Families |
| **New Pages** | `results.php` |
| **New Admin** | `admin/results.php`, `admin/results-add.php`, `admin/results-edit.php` |
| **New Tables** | `academic_results` |

### Description
A dedicated page showcasing the school's PLE (Primary Leaving Examinations) performance across years.

### Functionality
- **Year Selector**: Dropdown/tabs to switch between years (2007-2023+)
- **Dual Results View**: Toggle between Secular (Centre No. 530253) and Theology (Centre No. 97)
- **Summary Stats**: Total candidates, Division 1/2/3/4/U counts, pass rate percentage
- **Trend Charts**: Line chart showing pass rate trends year-over-year (using Chart.js)
- **Results Table**: Full results data per year with sorting
- **PDF Download**: Link to download original results PDF when available
- **Highlight Banner**: Latest year's results prominently displayed on homepage

### Database Schema
```sql
CREATE TABLE academic_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year YEAR NOT NULL,
    exam_type ENUM('secular', 'theology') NOT NULL,
    centre_number VARCHAR(20),
    total_candidates INT DEFAULT 0,
    division_1 INT DEFAULT 0,
    division_2 INT DEFAULT 0,
    division_3 INT DEFAULT 0,
    division_4 INT DEFAULT 0,
    ungraded INT DEFAULT 0,
    pass_rate DECIMAL(5,2) DEFAULT 0.00,
    results_detail LONGTEXT,  -- JSON or HTML table for detailed individual results
    pdf_file VARCHAR(255),     -- Path to downloadable PDF
    summary TEXT,
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_year_type (year, exam_type),
    INDEX idx_status (status),
    INDEX idx_year (year DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Admin Features
- Add/edit results per year per type
- Upload results PDF
- Enter summary statistics
- Paste or type detailed results data

---

## Feature 2: Online Admission Inquiry Form

| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |
| **Audience** | Prospective Parents |
| **New Pages** | Section in `admissions.php` |
| **New Admin** | `admin/inquiries.php` (extend existing) |
| **New Tables** | Extend `contact_inquiries` or create `admission_inquiries` |

### Description
Allow prospective parents to submit an admission interest/inquiry form directly from the website.

### Form Fields
| Field | Type | Required |
|-------|------|----------|
| Parent/Guardian Full Name | text | Yes |
| Email Address | email | Yes |
| Phone Number | tel | Yes |
| Child's Full Name | text | Yes |
| Child's Date of Birth | date | Yes |
| Gender | select (Male/Female) | Yes |
| Current School (if any) | text | No |
| Class Applying For | select (P1-P7) | Yes |
| Boarding/Day | select | Yes |
| How did you hear about us? | select | No |
| Additional Message | textarea | No |

### Database Schema
```sql
CREATE TABLE admission_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_name VARCHAR(255) NOT NULL,
    parent_email VARCHAR(255) NOT NULL,
    parent_phone VARCHAR(50) NOT NULL,
    child_name VARCHAR(255) NOT NULL,
    child_dob DATE NOT NULL,
    child_gender ENUM('Male', 'Female') NOT NULL,
    current_school VARCHAR(255),
    class_applying VARCHAR(10) NOT NULL,
    boarding_type ENUM('Boarding', 'Day') NOT NULL,
    referral_source VARCHAR(100),
    message TEXT,
    status ENUM('new', 'contacted', 'enrolled', 'declined') DEFAULT 'new',
    admin_notes TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at DESC),
    INDEX idx_class (class_applying)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Admin Features
- List all inquiries with status filter
- View inquiry details
- Update status (new → contacted → enrolled/declined)
- Add admin notes
- Export to CSV
- Email notification to admin on new inquiry (optional)

---

## Feature 3: Document Download Center

| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |
| **Audience** | Parents, Students, Staff |
| **New Pages** | `downloads.php` |
| **New Admin** | `admin/downloads.php`, `admin/downloads-add.php`, etc. |
| **New Tables** | `downloads` |

### Description
A centralized page for all downloadable school documents — fees structures, circulars, forms, routine schedules, academic materials.

### Categories
| Category | Examples |
|----------|----------|
| Fees & Finance | Fees structure PDFs, payment guidelines |
| Circulars | Term circulars, holiday notices |
| Academic | Past papers, revision materials, timetables |
| Admissions | Admission form, requirements checklist |
| School Info | School routine, school menu, anthem & dua |
| Forms | Transfer forms, absence request forms |

### Database Schema
```sql
CREATE TABLE downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size INT DEFAULT 0,
    file_type VARCHAR(50) DEFAULT 'pdf',
    category ENUM('fees', 'circulars', 'academic', 'admissions', 'school-info', 'forms', 'other') NOT NULL,
    academic_year VARCHAR(20),
    term VARCHAR(10),
    download_count INT DEFAULT 0,
    status ENUM('active', 'archived') DEFAULT 'active',
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_academic_year (academic_year DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Functionality
- Categorized listing with filter buttons
- Search by title/description
- Download counter increment on each download
- File size display
- Upload date display
- Pagination for large document collections

---

## Feature 4: Academic Calendar / School Calendar

| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |
| **Audience** | Parents, Students, Staff |
| **New Pages** | Section in `events.php` or standalone `calendar.php` |
| **New Tables** | `school_calendar` |

### Description
Display the school's academic calendar with term dates, examination periods, holidays, and special events.

### Database Schema
```sql
CREATE TABLE school_calendar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE,
    academic_year VARCHAR(20) NOT NULL,
    term ENUM('Term 1', 'Term 2', 'Term 3') NOT NULL,
    event_type ENUM('term-start', 'term-end', 'holiday', 'exam', 'event', 'sports', 'meeting', 'other') NOT NULL,
    is_highlighted BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_academic_year (academic_year),
    INDEX idx_term (term),
    INDEX idx_start_date (start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Functionality
- Display calendar by term and academic year
- Color-coded event types
- Current term highlight
- Countdown to next key event (exams, holidays)
- Print-friendly view

---

## Feature 5: FAQ System (Database-Driven)

| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |
| **Audience** | Prospective Families |
| **New Pages** | `faq.php` (adapt existing) |
| **New Admin** | `admin/faq.php`, `admin/faq-add.php`, etc. |
| **New Tables** | `faq_items` |

### Description
Database-driven FAQ page with categorized questions and answers, manageable from admin panel.

### Database Schema
```sql
CREATE TABLE faq_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer LONGTEXT NOT NULL,
    category ENUM('admissions', 'fees', 'academics', 'boarding', 'transport', 'general', 'other') DEFAULT 'general',
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Pre-populated FAQ Topics
1. **Admissions**: How to apply, age requirements, required documents, admission timeline
2. **Fees**: Fee amounts, payment methods, installment options, what fees cover
3. **Academics**: Curriculum details, subjects offered, exam schedule, grading system
4. **Boarding**: Boarding facilities, what to bring, visiting days, meals
5. **Transport**: School bus availability, routes, pickup/drop-off times
6. **General**: School uniform, term dates, communication channels, parent-teacher meetings

---

## Feature 6: Page Content Management System

| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |
| **Audience** | Admin/Staff |
| **New Admin** | `admin/page-content.php` |
| **New Tables** | `page_content` |

### Description
Allow admin to edit static page content (About sections, Academics, rules, etc.) directly from the admin panel without modifying PHP files.

### Database Schema
```sql
CREATE TABLE page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_slug VARCHAR(100) NOT NULL,
    section_key VARCHAR(100) NOT NULL,
    title VARCHAR(255),
    content LONGTEXT NOT NULL,
    content_type ENUM('html', 'text', 'markdown') DEFAULT 'html',
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_page_section (page_slug, section_key),
    INDEX idx_page_slug (page_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Functionality
- Admin edits page sections with a simple text/HTML editor
- Changes reflected immediately on the frontend
- Version history tracking (optional)
- Preview before publish

---

## Feature 7: Testimonials Section

| Detail | Value |
|--------|-------|
| **Priority** | MEDIUM |
| **Status** | PENDING |
| **Audience** | Prospective Families |
| **New Pages** | Section on homepage + `testimonials.php` |
| **New Admin** | `admin/testimonials.php`, etc. |
| **New Tables** | `testimonials` |

### Description
Display parent and alumni testimonials about the school experience.

### Database Schema
```sql
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL,  -- 'Parent', 'Alumni', 'Student', 'Community Member'
    content TEXT NOT NULL,
    photo VARCHAR(255),
    rating INT DEFAULT 5,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Feature 8: Newsletter Subscription

| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |
| **Audience** | Parents, Community |
| **New Pages** | Footer section + optional `subscribe.php` |
| **New Tables** | `newsletter_subscribers` |

### Description
Simple email subscription form for school updates, circulars, and event notifications.

### Database Schema
```sql
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255),
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Feature 9: School Rules & Daily Routine Display

| Detail | Value |
|--------|-------|
| **Priority** | HIGH |
| **Status** | PENDING |
| **Audience** | Parents, Students |
| **New Pages** | Sections in `admissions.php` |

### Description
Display school rules and daily routine in a well-formatted, printable way.

### School Rules (from WordPress)
Already extracted from WP page 11577. Display as numbered list:
1. All pupils must be punctual
2. Due respect to teachers
3. No money/eating without permission
4. English and Arabic are official languages
5. ... (full list from WordPress)

### Daily Routine
Display time-activity table + PDF download option

---

## Feature 10: Visitor Counter / Analytics Dashboard (Admin)

| Detail | Value |
|--------|-------|
| **Priority** | LOW |
| **Status** | PENDING |
| **Audience** | Admin/Staff |
| **New Tables** | `page_visits` |

### Description
Simple server-side visitor tracking for admin dashboard analytics.

### Database Schema
```sql
CREATE TABLE page_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(255) NOT NULL,
    visitor_ip VARCHAR(45),
    user_agent VARCHAR(255),
    referrer VARCHAR(255),
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_page (page_url),
    INDEX idx_date (visited_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Feature Priority Summary

| Feature | Priority | Complexity | Audience Impact |
|---------|----------|------------|-----------------|
| Academic Results Dashboard | HIGH | Medium | High — differentiator |
| Online Admission Inquiry | HIGH | Medium | High — lead generation |
| Document Download Center | MEDIUM | Low | High — utility |
| School Rules & Routine Display | HIGH | Low | Medium — utility |
| Page Content Management | MEDIUM | Medium | Medium — admin efficiency |
| Academic Calendar | MEDIUM | Medium | Medium — planning tool |
| Testimonials Section | MEDIUM | Low | Medium — social proof |
| FAQ System | LOW | Low | Medium — self-service |
| Newsletter Subscription | LOW | Low | Low — engagement |
| Visitor Analytics | LOW | Low | Low — insights |

---

## Implementation Order (Recommended)

1. **First batch** (with core page development):
   - School Rules & Routine Display (pure content)
   - Document Download Center (simple CRUD)
   - Testimonials Section (simple CRUD)
   - Page Content Management (foundation for editable pages)

2. **Second batch** (high-value features):
   - Academic Results Dashboard
   - Online Admission Inquiry Form
   - FAQ System

3. **Third batch** (enhancements):
   - Academic Calendar
   - Newsletter Subscription
   - Visitor Analytics

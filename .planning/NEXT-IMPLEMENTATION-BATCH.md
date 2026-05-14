# Lukman Primary School Website - Next Implementation Batch

## Operating Rule
This backlog is intentionally timeline-free.
Every step must always carry two fields and be updated continuously:
- Status: BACKLOG | NOT STARTED | IN PROGRESS | DONE | BLOCKED | DEFERRED
- Priority: CRITICAL | HIGH | MEDIUM | LOW

## Current State Snapshot

| Workstream | Status | Priority | Notes |
|---|---|---|---|
| Foundation setup (project copy, DB, schema, seed) | DONE | CRITICAL | Base system is running |
| Global branding shell (public header/footer/colors) | IN PROGRESS | CRITICAL | Core shell updated, legacy content remains on several pages |
| Homepage conversion | IN PROGRESS | HIGH | Main page changed to school direction; needs content polish and data checks |
| Admin branding and navigation | NOT STARTED | HIGH | Admin still contains legacy labels and old modules |
| Legacy reference cleanup (DTEHM links/text) | NOT STARTED | CRITICAL | Found across many public and admin pages |
| Missing school pages from new nav | NOT STARTED | CRITICAL | Needed: academics, admissions, results, downloads, student-life |
| QA and regression testing | NOT STARTED | CRITICAL | Must run after each batch |

## Next Batch to Implement (Execution Now)

### Batch C - Content Parity and Legacy Cleanup
Goal: make the public site coherent, school-specific, and free of broken links.

| ID | Step | Priority | Status | Definition of Done |
|---|---|---|---|---|
| C1 | Replace legacy text and defaults in public pages (about, contact, faq, news, events, testimonials, event-detail, news-detail, gallery, gallery-album, 404, sitemap, robots) | CRITICAL | NOT STARTED | No legacy brand text remains in public pages |
| C2 | Remove links to deleted modules (shop, insurance, investment, network, donate, causes, get-involved) from all public pages | CRITICAL | NOT STARTED | No dead links to removed modules |
| C3 | Build missing pages required by current navigation: academics.php, admissions.php, results.php, downloads.php, student-life.php | CRITICAL | NOT STARTED | Every main nav link resolves to a functional page |
| C4 | Rework FAQ content to school context (admissions, academics, fees, boarding, rules, contacts) | HIGH | NOT STARTED | FAQ fully school-specific |
| C5 | Rework Contact page and inquiry flows for parents/guardians | HIGH | NOT STARTED | Inquiry form + contact details aligned to school |
| C6 | Update sitemap and robots to school domain and valid routes only | HIGH | NOT STARTED | Crawlable sitemap with only live URLs |
| C7 | Verify responsive behavior across key templates after content replacement | HIGH | NOT STARTED | No major breakpoints/layout regressions |

### Batch D - Admin Alignment
Goal: make admin useful for school operations only.

| ID | Step | Priority | Status | Definition of Done |
|---|---|---|---|---|
| D1 | Rebrand admin shell (login, header, footer, titles, email defaults) | HIGH | NOT STARTED | Admin has school identity only |
| D2 | Remove old admin menu items and dashboard cards tied to causes/donations legacy model | CRITICAL | NOT STARTED | Sidebar/dashboard only show active modules |
| D3 | Add/enable school modules in admin nav (downloads, results, testimonials, admission inquiries) | HIGH | NOT STARTED | Admin navigation matches school site needs |
| D4 | Validate admin dashboard queries against current schema (no queries to missing tables) | CRITICAL | NOT STARTED | Dashboard loads with no SQL errors |
| D5 | Tighten auth defaults and seeded admin metadata for school use | HIGH | NOT STARTED | Secure defaults and school email/domain |

### Batch E - Quality Gate
Goal: verify behavior before deeper feature expansion.

| ID | Step | Priority | Status | Definition of Done |
|---|---|---|---|---|
| E1 | Run link integrity pass on all public and admin routes | CRITICAL | NOT STARTED | No broken internal links |
| E2 | Run content consistency pass (names, logo usage, phone/email, metadata) | HIGH | NOT STARTED | Branding consistency confirmed |
| E3 | Run form validation + submission checks (contact, admission, admin CRUD) | CRITICAL | NOT STARTED | Forms submit and persist correctly |
| E4 | Run smoke tests for home, about, academics, admissions, results, gallery, news, events, downloads, faq, contact, admin login | CRITICAL | NOT STARTED | Core journey pass completed |

## Strategic Evolution Backlog (Future-Ready Enhancements)

### 1) Parent Experience Layer

| ID | Feature | Priority | Status | Strategic Value |
|---|---|---|---|---|
| F1 | Admission eligibility assistant (interactive checklist by class/age/docs) | HIGH | BACKLOG | Reduces inquiry friction and improves conversion |
| F2 | Fees estimator (class + boarding/day + extras) | HIGH | BACKLOG | Improves transparency and trust |
| F3 | WhatsApp quick-intent launcher (Admissions, Fees, Visit, Results) | HIGH | BACKLOG | Faster parent communication |
| F4 | School visit booking flow with admin confirmation | MEDIUM | BACKLOG | Structured lead capture for tours |

### 2) Academic Trust and Transparency

| ID | Feature | Priority | Status | Strategic Value |
|---|---|---|---|---|
| F5 | Results analytics dashboard (year trends, division distribution, subject strength) | HIGH | BACKLOG | Demonstrates academic outcomes visually |
| F6 | Curriculum explorer (secular + Islamic track mapping by class) | HIGH | BACKLOG | Clarifies dual-curriculum advantage |
| F7 | Download center with version history and "updated on" traceability | MEDIUM | BACKLOG | Improves document reliability |

### 3) Community and Reputation Engine

| ID | Feature | Priority | Status | Strategic Value |
|---|---|---|---|---|
| F8 | Alumni spotlight stories with filters by cohort/achievement | MEDIUM | BACKLOG | Long-term brand credibility |
| F9 | Parent testimonials with moderated publishing workflow | MEDIUM | BACKLOG | Social proof with quality control |
| F10 | Event media storytelling templates (before/during/after event format) | MEDIUM | BACKLOG | Better content consistency and engagement |

### 4) Operations and Admin Intelligence

| ID | Feature | Priority | Status | Strategic Value |
|---|---|---|---|---|
| F11 | Role-based admin permissions (content editor vs academic officer vs super admin) | HIGH | BACKLOG | Safer, scalable team workflows |
| F12 | Content health dashboard (stale pages, missing metadata, broken media) | MEDIUM | BACKLOG | Prevents quality decay |
| F13 | Audit trail viewer for sensitive admin actions | HIGH | BACKLOG | Governance and accountability |

### 5) Design and UX Maturity

| ID | Feature | Priority | Status | Strategic Value |
|---|---|---|---|---|
| F14 | Visual design system library (tokens + reusable section components) | MEDIUM | BACKLOG | Consistent and faster page building |
| F15 | Enhanced motion language (purposeful page reveals and section transitions) | LOW | BACKLOG | Perceived polish and modern feel |
| F16 | Accessibility pass (contrast, keyboard nav, aria labels, focus states) | HIGH | BACKLOG | Inclusive UX and better compliance |

## Priority Policy
Use this rule during execution:
- CRITICAL: blocks core site function, trust, or safety
- HIGH: strongly improves parent utility and school operations
- MEDIUM: quality and scalability improvements
- LOW: polish and enhancements after core reliability

## Status Update Protocol
After each implementation session:
1. Mark each touched step with current Status.
2. Re-evaluate Priority if school needs have changed.
3. Add brief Notes for blockers or decisions.
4. Keep this backlog as the single source of truth.

## Recommended Immediate Focus
Start with Batch C in this order:
1. C1
2. C2
3. C3
4. C6
5. C7

This order removes reputational risk first (legacy branding and dead links), then restores full navigation coverage, then closes technical SEO and UX gaps.

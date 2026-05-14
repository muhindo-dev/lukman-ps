# Lukman PS — Rebuild Implementation Status
**Started:** May 2026 | **Based on:** WEBSITE-AUDIT-2026.md

Legend: ✅ Done | 🔄 In Progress | ⏳ Pending | ❌ Blocked

---

## 🔴 Immediate — Critical Fixes

| # | Task | Status | Notes |
|---|---|---|---|
| 1 | Founding year unified to `site_settings` | ✅ | Dynamic in header, footer, index, about |
| 2 | CSRF token on admissions form | ✅ | `csrfField()` + `validateCsrfToken()` |
| 3 | Create `privacy.php` | ✅ | Full DPPA 2019-compliant policy |
| 4 | Remove admin link from footer | ✅ | Replaced with Privacy Policy link |
| 5 | Fix `@mail()` error suppression | ✅ | `$mailSent = mail(...)` with `error_log` |
| 6 | Fix open redirect via `HTTP_REFERER` | ✅ | Hardcoded `index.php` redirect |
| 7 | Add `<main>` landmark to `index.php` | ✅ | `<main id="main-content">` wrapping all content |
| 8 | Add skip-to-content link in `header.php` | ✅ | `.skip-link` with focus CSS |
| 9 | Standardize contact email to `info@lukmanps.ac.ug` | ✅ | All files updated |
| 10 | Fix Google Maps embed to use GPS coordinates | ✅ | lat 0.0670, lng 32.4600 |

## 🟠 Short Term — Sprint 1

| # | Task | Status | Notes |
|---|---|---|---|
| 11 | Fix stats inconsistency homepage vs about page | ✅ | Dynamic via `site_settings` |
| 12 | Add form submission loading states | ✅ | Spinner + disable on `.contact-form, #inquiry-form` |
| 13 | Remove deprecated `<meta name="keywords">` | ✅ | Tag removed from header |
| 14 | Fix canonical URL (strip query strings, define base URL) | ✅ | `SITE_BASE_URL` constant + `strtok()` |
| 15 | Fix OG image fallback | ✅ | Always falls back to school logo PNG |
| 16 | Delete backup CSS files | ✅ | `style-backup.css` and pre-redesign files removed |
| 17 | Move planning markdown files out of web root | ✅ | Moved to `.planning/` with `Deny from all` |
| 18 | Add `.htaccess` deny to `/logs/` | ✅ | `Deny from all` added |

## 🟠 Sprint 2 — UI/UX & Code Quality

| # | Task | Status | Notes |
|---|---|---|---|
| 19 | Replace inline event card styles with CSS classes | ✅ | Full CSS class system in `style.css` |
| 20 | Fix gallery filter + lightbox sync | ✅ | JS toggles `data-lightbox` attr on filter |
| 21 | Fix WhatsApp / cookie banner mobile overlap | ✅ | `bottom:90px` + `cookie-banner-active` class |
| 22 | Add SRI hashes to CDN scripts | ✅ | Bootstrap, jQuery, FontAwesome, Lightbox2 |
| 23 | Fix dual DB connection (singleton) | ✅ | `getDBConnection()` rewritten as singleton |
| 24 | Add Baby Class / Pre-Primary to admissions form | ✅ | `<optgroup>` for Pre-Primary and Primary |
| 25 | Add data consent checkbox + honeypot to admissions form | ✅ | Checkbox + hidden field + `goto skip_save` |
| 26 | Add ARIA `aria-expanded` to notice strip toggles | ✅ | `aria-expanded` + `aria-controls` + `id` on detail span |
| 27 | Add `lang="ar"` to Arabic text spans | ✅ | `quran.php` Arabic labels + `prayer-times.php` prayer names |
| 28 | Fix Schema.org `foundingDate` to be dynamic | ✅ | Uses `getSetting('founding_year', '1997')` |
| 29 | Fix Twitter handle meta tag to use settings | ✅ | `getSetting('twitter_handle', '@LukmanPS')` |
| 30 | Add rate limiting / honeypot to admissions form | ✅ | Honeypot field + silent reject via `goto` |
| 31 | Add back-to-top button on long inner pages | ✅ | `#back-to-top` button in footer, JS in main.js |
| 32 | Reduce homepage stats repetition (3 → 2) | ⏳ | |
| 33 | Configure 404 error document in `.htaccess` | ✅ | `ErrorDocument 404 /lukman-ps/404.php` |

---

*Last updated: May 2026*

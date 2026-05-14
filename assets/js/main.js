// Hero Height Calculator - Set hero to full remaining viewport height
function setHeroHeight() {
    const hero = document.querySelector('#hero');
    if (hero) {
        if (window.innerWidth > 768) {
            hero.style.minHeight = `${window.innerHeight}px`;
            hero.style.height = `${window.innerHeight}px`;
        } else {
            hero.style.minHeight = '';
            hero.style.height = '';
        }
    }
}

// Homepage gallery filter + count
function initHomeGalleryFilter() {
    if (typeof jQuery === 'undefined') return;

    const $ = jQuery;
    const $section = $('#gallery-home');
    if (!$section.length) return;

    const $filters = $section.find('.gal-filter');
    const $cells = $section.find('.gal-cell');
    const $count = $section.find('#gal-visible-count');

    function applyFilter(filter) {
        const normalized = String(filter || 'all').toLowerCase();
        let visibleCount = 0;

        $filters.removeClass('active').attr('aria-pressed', 'false');
        $filters.filter('[data-filter="' + normalized + '"]').addClass('active').attr('aria-pressed', 'true');

        $cells.each(function () {
            const $cell = $(this);
            const category = String($cell.data('category') || '').toLowerCase();
            const shouldShow = normalized === 'all' || category === normalized;
            const $link = $cell.find('a[data-lightbox]');

            $cell.stop(true, true);
            if (shouldShow) {
                visibleCount += 1;
                $cell.removeClass('is-hidden').fadeIn(180);
                // Re-include in lightbox group so only visible photos cycle together
                $link.attr('data-lightbox', 'gallery');
            } else {
                $cell.addClass('is-hidden').fadeOut(150);
                // Exclude from lightbox group by using a unique per-cell key
                $link.attr('data-lightbox', 'gallery-hidden-' + category);
            }
        });

        if ($count.length) {
            $count.text(visibleCount);
        }
    }

    $filters.on('click', function (e) {
        e.preventDefault();
        applyFilter($(this).data('filter'));
    });

    applyFilter($filters.filter('.active').data('filter') || 'all');
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    setHeroHeight();
    initHomeGalleryFilter();
    
    // Initialize AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
            disable: 'mobile'
        });
    }
    
    // Animated number counter
    function animateCounters() {
        const counters = document.querySelectorAll('[data-count]');
        counters.forEach(counter => {
            if (counter.dataset.counted) return;
            
            const rect = counter.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                counter.dataset.counted = 'true';
                const target = parseInt(counter.dataset.count);
                const suffix = counter.textContent.includes('+') ? '+' : '';
                const duration = 2000;
                const start = 0;
                const startTime = performance.now();
                
                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    // Ease out cubic
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    const current = Math.round(start + (target - start) * easeOut);
                    counter.textContent = current.toLocaleString() + suffix;
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                }
                requestAnimationFrame(updateCounter);
            }
        });
    }
    
    window.addEventListener('scroll', animateCounters, { passive: true });
    animateCounters(); // Initial check
});

// Run on window resize
window.addEventListener('resize', setHeroHeight);

// Smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Close mobile menu
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        }
    });
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Mobile dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                const dropdown = this.closest('.dropdown');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                    if (m !== menu) m.classList.remove('show');
                });
                
                // Toggle current dropdown
                menu.classList.toggle('show');
            }
        });
    });
    
    // Close dropdown on menu item click
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                    m.classList.remove('show');
                });
            }
        });
    });
});

// Form submission (if contact form exists)
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const alertDiv = document.getElementById('contactAlert');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        alertDiv.style.display = 'none';
        
        const formData = new FormData();
        formData.append('action', 'contact');
        formData.append('name', this.name.value);
        formData.append('email', this.email.value);
        formData.append('phone', this.phone.value);
        formData.append('subject', this.subject.value);
        formData.append('message', this.message.value);
        
        fetch('enroll.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alertDiv.textContent = data.message;
            alertDiv.className = 'alert-custom ' + (data.success ? 'alert-success' : 'alert-error');
            alertDiv.style.display = 'block';
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            if (data.success) {
                contactForm.reset();
            }
            
            setTimeout(() => { alertDiv.style.display = 'none'; }, 10000);
        })
        .catch(error => {
            alertDiv.textContent = 'Connection error. Please try again.';
            alertDiv.className = 'alert-custom alert-error';
            alertDiv.style.display = 'block';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        });
    });
}

// Active nav link on scroll
window.addEventListener('scroll', () => {
    let current = '';
    document.querySelectorAll('section').forEach(section => {
        const sectionTop = section.offsetTop;
        if (scrollY >= sectionTop - 100) {
            current = section.getAttribute('id');
        }
    });

    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
        }
    });
});

// ─── Form submission loading state ───────────────────────────────────────────
// Prevents double-submit on all server-rendered POST forms.
// Disables the submit button and shows a spinner after first click.
document.addEventListener('DOMContentLoaded', function () {
    const managedForms = document.querySelectorAll(
        '.contact-form, #inquiry-form'
    );

    managedForms.forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('[type="submit"]');
            if (!btn || btn.disabled) return;
            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending&hellip;';
            // Re-enable after 15 s as a safety net (server might time out)
            setTimeout(function () {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }, 15000);
        });
    });
});

// ─── Back-to-top button ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;
    window.addEventListener('scroll', function () {
        btn.classList.toggle('btt-visible', window.scrollY > 400);
    }, { passive: true });
    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

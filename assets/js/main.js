/**
 * HMI IT Telkom - Main JavaScript
 * Counter animation, navbar scroll, mobile toggle, scroll reveal
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // NAVBAR SCROLL EFFECT
    // ============================================================
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    // ============================================================
    // MOBILE MENU TOGGLE — with auto-close on link click
    // ============================================================
    const navToggle = document.querySelector('.navbar-toggle');
    const navMenu = document.querySelector('.navbar-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('show');
            this.textContent = navMenu.classList.contains('show') ? '✕' : '☰';
            // Prevent body scroll when menu is open
            document.body.style.overflow = navMenu.classList.contains('show') ? 'hidden' : '';
        });

        // Close menu when a nav link is clicked
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function () {
                navMenu.classList.remove('show');
                navToggle.textContent = '☰';
                document.body.style.overflow = '';
            });
        });

        // Close menu on resize (e.g. rotate phone to landscape)
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768 && navMenu.classList.contains('show')) {
                navMenu.classList.remove('show');
                navToggle.textContent = '☰';
                document.body.style.overflow = '';
            }
        });
    }

    // ============================================================
    // ANIMATED COUNTERS (Intersection Observer)
    // ============================================================
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length > 0) {
        const animateCounter = (el) => {
            const target = parseInt(el.getAttribute('data-count'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = Math.floor(current).toLocaleString('id-ID');
            }, 16);
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    animateCounter(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    }

    // ============================================================
    // SCROLL REVEAL ANIMATIONS
    // ============================================================
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target); // Only animate once
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

        reveals.forEach(el => revealObserver.observe(el));
    }

    // ============================================================
    // ADMIN SIDEBAR TOGGLE (Mobile) — with backdrop
    // ============================================================
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');

    if (sidebarToggle && sidebar) {
        // Create backdrop
        const backdrop = document.createElement('div');
        backdrop.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:1099;display:none;transition:opacity 0.3s;';
        document.body.appendChild(backdrop);

        function openSidebar() {
            sidebar.classList.add('show');
            backdrop.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.remove('show');
            backdrop.style.display = 'none';
            document.body.style.overflow = '';
        }

        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
        });
        backdrop.addEventListener('click', closeSidebar);
    }

    // ============================================================
    // FLASH MESSAGE AUTO-DISMISS
    // ============================================================
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // ============================================================
    // FORM VALIDATION HELPERS
    // ============================================================
    window.HMI = window.HMI || {};

    HMI.validateForm = function (formId) {
        const form = document.getElementById(formId);
        if (!form) return true;

        let isValid = true;
        const required = form.querySelectorAll('[required]');

        required.forEach(field => {
            const errorEl = field.parentElement.querySelector('.form-error');
            if (!field.value.trim()) {
                field.style.borderColor = '#C62828';
                if (errorEl) errorEl.textContent = 'Field ini wajib diisi';
                isValid = false;
            } else {
                field.style.borderColor = '';
                if (errorEl) errorEl.textContent = '';
            }
        });

        // Password match check
        const pass = form.querySelector('[name="password"]');
        const conf = form.querySelector('[name="confirm_password"]');
        if (pass && conf && pass.value !== conf.value) {
            conf.style.borderColor = '#C62828';
            const err = conf.parentElement.querySelector('.form-error');
            if (err) err.textContent = 'Password tidak cocok';
            isValid = false;
        }

        return isValid;
    };

    // ============================================================
    // CONFIRM DELETE
    // ============================================================
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.getAttribute('data-confirm') || 'Yakin ingin menghapus?')) {
                e.preventDefault();
            }
        });
    });
});

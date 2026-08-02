import './bootstrap';
import * as bootstrap from 'bootstrap';
import 'flatpickr';
import 'flatpickr/dist/l10n/ar.js';
import 'flatpickr/dist/l10n/fr.js';
import 'flatpickr/dist/l10n/es.js';
import Swal from 'sweetalert2';

window.Swal = Swal;
window.bootstrap = bootstrap;

// ============================================
// Unified toast notifications (Livewire 'toast' event + session flashes)
// ============================================
window.showToast = function (type, message) {
    const styles = {
        success: { icon: 'success', timer: 4000, borderColor: 'rgba(22,163,74,0.3)', iconColor: '#16a34a' },
        error: { icon: 'error', timer: 5000, borderColor: 'rgba(239,68,68,0.3)', iconColor: '#ef4444' },
        info: { icon: 'info', timer: 4000, borderColor: 'rgba(59,130,246,0.3)', iconColor: '#f5a622' },
        warning: { icon: 'warning', timer: 5000, borderColor: 'rgba(245,166,34,0.3)', iconColor: '#f5a622' },
    };
    const s = styles[type] || styles.info;
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: s.icon,
        title: message,
        showConfirmButton: false,
        timer: s.timer,
        timerProgressBar: true,
        background: '#1a1f35',
        color: '#fff',
        borderColor: s.borderColor,
        iconColor: s.iconColor,
    });
};

document.addEventListener('livewire:init', () => {
    Livewire.on('toast', ({ type, message }) => showToast(type, message));
});

// Backward compat for the legacy swal:* browser events dispatched from Livewire
window.addEventListener('swal:success', (event) => showToast('success', event.detail?.message));
window.addEventListener('swal:error', (event) => showToast('error', event.detail?.message));
window.addEventListener('swal:info', (event) => showToast('info', event.detail?.message));

// ApexCharts is loaded on demand (charts render on the admin dashboard only)
let apexPromise = null;
window.loadApexCharts = function () {
    if (!apexPromise) {
        apexPromise = import('apexcharts').then((module) => {
            window.ApexCharts = module.default;
            return module.default;
        });
    }
    return apexPromise;
};

// ============================================
// Theme Toggle (Dark / Light)
// ============================================
(function initTheme() {
    const saved = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = saved || (prefersDark ? 'dark' : 'light');
    document.documentElement.setAttribute('data-bs-theme', theme);
    updateThemeIcon(theme);
})();

window.toggleTheme = function () {
    const current = document.documentElement.getAttribute('data-bs-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
};

function updateThemeIcon(theme) {
    document.querySelectorAll('.theme-icon').forEach(icon => {
        if (icon.classList) {
            icon.classList.toggle('bi-moon-stars', theme === 'light');
            icon.classList.toggle('bi-sun-fill', theme === 'dark');
        }
    });
}

// Initialize all flatpickr inputs with class "flatpickr-input"
document.addEventListener('livewire:initialized', () => {
    initFlatpickr();
});
document.addEventListener('livewire:navigated', () => {
    setTimeout(initFlatpickr, 100);
});
document.addEventListener('DOMContentLoaded', () => {
    initFlatpickr();
});

function initFlatpickr() {
    const locale = document.documentElement.lang || 'ar';
    const flatpickrLocale = locale.split('-')[0];

    document.querySelectorAll('input.flatpickr-input:not(.flatpickr-input-initialized)').forEach(el => {
        el.classList.add('flatpickr-input-initialized');
        flatpickr(el, {
            locale: flatpickrLocale,
            enableTime: el.dataset.enableTime === 'true',
            dateFormat: el.dataset.dateFormat || 'Y-m-d',
            altInput: true,
            altFormat: el.dataset.altFormat || 'd/m/Y',
            time_24hr: true,
            onChange: function(selectedDates, dateStr, instance) {
                // Dispatch input event so Livewire wire:model picks up the value
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
}

// Entrance animations play once on first load; suppress them on subsequent Livewire soft navigations
document.addEventListener('livewire:navigate', () => {
    document.documentElement.classList.add('livewire-entered');
});

// Reset Bootstrap's scrollbar compensation when a soft navigation replaced the
// DOM while an offcanvas/modal was open. Bootstrap's ScrollBarHelper.reset() only
// runs when the overlay's hide() completes, which is skipped if the DOM is torn
// down mid-navigation, leaving body{padding-right;overflow:hidden} behind.
// Root cause of the RTL phantom gap: Bootstrap 5.3's ScrollBarHelper hardcodes
// 'padding-right'/'margin-right' (bootstrap.esm.js), but in RTL the scrollbar is
// on the left, so the compensation lands on the wrong edge.
document.addEventListener('livewire:navigated', () => {
    if (document.querySelector('.modal.show, .offcanvas.show')) {
        return;
    }
    const body = document.body;
    body.classList.remove('modal-open');
    if (body.style.overflow === 'hidden') {
        body.style.overflow = '';
    }
    if (body.style.paddingRight) {
        body.style.paddingRight = '';
    }
    document.querySelectorAll('.fixed-top, .fixed-bottom, .is-fixed, .sticky-top').forEach(el => {
        const saved = el.getAttribute('data-bs-margin-right');
        if (saved !== null) {
            el.style.marginRight = saved;
            el.removeAttribute('data-bs-margin-right');
        } else if (el.style.marginRight) {
            el.style.marginRight = '';
        }
        if (el.style.paddingRight) {
            el.style.paddingRight = '';
        }
    });
});

// Alpine.js match timer component (shared between match-control and matches listing)
document.addEventListener('alpine:init', () => {
    window.Alpine.data('matchTimer', (config) => ({
        phase: config.phase || '',
        fhs: config.fhs || null,
        shs: config.shs || null,
        et1s: config.et1s || null,
        et2s: config.et2s || null,
        at1: config.at1 || 0,
        at2: config.at2 || 0,
        ate1: config.ate1 || 0,
        ate2: config.ate2 || 0,
        mode: config.mode || 'full',
        period: '',
        display: '00:00',
        _id: null,
        init() {
            this.tick();
            this._id = setInterval(() => this.tick(), 1000);
        },
        tick() {
            const now = Date.now();
            const isCompact = this.mode === 'compact';

            if (this.phase === 'full_time') {
                this.period = isCompact ? 'FT' : 'FT';
                this.display = isCompact ? 'FT' : 'Full Time';
                return;
            }
            if (this.phase === 'scheduled') {
                this.period = '--';
                this.display = isCompact ? '—' : 'Not Started';
                return;
            }
            if (this.phase === 'first_half' && this.fhs) {
                const s = Math.max(0, Math.floor((now - this.fhs) / 1000));
                const m = Math.floor(s / 60); const sec = s % 60;
                this.period = isCompact ? '1st' : '1st Half';
                this.display = String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                if (m >= 45) this.display += (isCompact ? '+' : ' +') + (m - 45 + (!isCompact ? this.at1 : 0));
                return;
            }
            if (this.phase === 'half_time') {
                this.period = isCompact ? 'HT' : 'Half Time';
                this.display = isCompact ? 'HT' : 'HT ' + Math.max(0, Math.floor((this.fhs ? Math.max(0, Math.floor((now - this.fhs) / 1000)) : 0) - 45*60) / 60) + ':00';
                return;
            }
            if (this.phase === 'second_half' && this.shs) {
                const s = Math.max(0, Math.floor((now - this.shs) / 1000));
                const m = Math.floor(s / 60); const sec = s % 60;
                this.period = isCompact ? '2nd' : '2nd Half';
                const t = 45 + m;
                this.display = String(t).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                if (m >= 45) this.display += (isCompact ? '+' : ' +') + (m - 45 + (!isCompact ? this.at2 : 0));
                return;
            }
            if (this.phase === 'et_break') {
                this.period = isCompact ? '—' : 'ET Break';
                this.display = '—';
                return;
            }
            if (this.phase === 'et_first_half' && this.et1s) {
                const s = Math.max(0, Math.floor((now - this.et1s) / 1000));
                const m = Math.floor(s / 60); const sec = s % 60;
                this.period = isCompact ? 'ET1' : 'ET 1st Half';
                const t = 90 + m;
                this.display = String(t).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                if (m >= 15) this.display += (isCompact ? '+' : ' +') + (m - 15 + (!isCompact ? this.ate1 : 0));
                return;
            }
            if (this.phase === 'et_half_time') {
                this.period = isCompact ? '—' : 'ET HT';
                this.display = '—';
                return;
            }
            if (this.phase === 'et_second_half' && this.et2s) {
                const s = Math.max(0, Math.floor((now - this.et2s) / 1000));
                const m = Math.floor(s / 60); const sec = s % 60;
                this.period = isCompact ? 'ET2' : 'ET 2nd Half';
                const t = 105 + m;
                this.display = String(t).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                if (m >= 15) this.display += (isCompact ? '+' : ' +') + (m - 15 + (!isCompact ? this.ate2 : 0));
                return;
            }
            this.period = '';
            this.display = '—';
        },
        destroy() { if (this._id) clearInterval(this._id); }
    }));
});

// Global SweetAlert delete confirmation (used by delete-confirm-button component)
window.confirmSweetAlert = function(url, title, message, confirmText, cancelText) {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            window.Livewire.navigate(url);
        }
    });
};

// Styled SweetAlert2 confirmation modal (replaces native wire:confirm).
// Resolves true when confirmed; false on cancel/dismiss. Options: title, text,
// icon ('warning'|'info'), confirmButtonText, cancelButtonText.
// warning = destructive (red confirm button), info = positive (green confirm button).
window.confirmAction = function (options = {}) {
    const destructive = (options.icon || 'warning') === 'warning';
    return Swal.fire({
        title: options.title || '',
        text: options.text || '',
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: options.confirmButtonColor || (destructive ? '#ef4444' : '#16a34a'),
        cancelButtonColor: options.cancelButtonColor || '#6b7280',
        confirmButtonText: options.confirmButtonText,
        cancelButtonText: options.cancelButtonText,
        reverseButtons: true,
        background: '#1a1f35',
        color: '#fff',
        iconColor: destructive ? '#ef4444' : '#16a34a',
    }).then((result) => Boolean(result.isConfirmed));
};

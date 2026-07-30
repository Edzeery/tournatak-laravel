import './bootstrap';
import * as bootstrap from 'bootstrap';
import 'flatpickr';
import 'flatpickr/dist/l10n/ar.js';
import 'flatpickr/dist/l10n/fr.js';
import 'flatpickr/dist/l10n/es.js';
import Swal from 'sweetalert2';


import ApexCharts from 'apexcharts';

window.Swal = Swal;
window.ApexCharts = ApexCharts;
window.bootstrap = bootstrap;

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

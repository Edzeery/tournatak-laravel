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

// Global delete confirmation helper
window.confirmDelete = function(url, message = 'هل أنت متأكد من الحذف؟', title = 'تأكيد الحذف') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.navigate(url);
        }
    });
};

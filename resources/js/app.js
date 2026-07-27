import './bootstrap';
import 'flatpickr';
import 'flatpickr/dist/l10n/ar.js';
import 'flatpickr/dist/l10n/fr.js';
import 'flatpickr/dist/l10n/es.js';
import Swal from 'sweetalert2';

// Make SweetAlert2 globally available for Livewire
window.Swal = Swal;

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

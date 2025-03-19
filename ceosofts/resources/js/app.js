import './bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();

// Make jQuery available globally
window.$ = window.jQuery = $;

// Import custom modules
import './modules/forms';
import './modules/tables';
import './modules/notifications';

// เพิ่มการจัดการ Modal สำหรับทั้งแอปพลิเคชัน
document.addEventListener('DOMContentLoaded', function() {
    // จัดการ Modal Bootstrap
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            // Clear any existing backdrops that might be causing problems
            document.querySelectorAll('.modal-backdrop.fade.show').forEach(backdrop => {
                backdrop.remove();
            });
        });
    });
    
    // Fix for modals closing issue
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                backdrop.remove();
            });
        });
    });
});

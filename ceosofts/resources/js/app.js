import './bootstrap';

// Initialize Bootstrap components
document.addEventListener('DOMContentLoaded', () => {
    // This ensures the navbar toggle works correctly
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', () => {
            const target = document.querySelector(navbarToggler.dataset.bsTarget);
            if (target) {
                target.classList.toggle('show');
            }
        });
    }
});

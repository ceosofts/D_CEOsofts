// Basic JavaScript functionality for CEOsofts

document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Enable Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // Handle Bootstrap modals
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
    
    // Table sorting functionality
    document.querySelectorAll('table.sortable th[data-sort]').forEach((th, index) => {
        th.addEventListener('click', () => {
            const table = th.closest('table');
            const currentSort = th.getAttribute('data-sort');
            const ascending = currentSort !== 'asc';
            
            // Reset all headers
            table.querySelectorAll('th[data-sort]').forEach(header => {
                header.setAttribute('data-sort', '');
            });
            
            // Set current header sort direction
            th.setAttribute('data-sort', ascending ? 'asc' : 'desc');
            
            // Sort the table
            sortTable(table, index, ascending);
        });
    });
    
    // Form validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (this.classList.contains('needs-validation')) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                this.classList.add('was-validated');
            }
        });
    });
});

// Table sorting function
function sortTable(table, columnIndex, ascending = true) {
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const direction = ascending ? 1 : -1;
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Check if values are numeric
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return direction * (aNum - bNum);
        }
        
        return direction * aValue.localeCompare(bValue);
    });
    
    // Remove existing rows
    rows.forEach(row => row.parentNode.removeChild(row));
    
    // Append sorted rows
    const tbody = table.querySelector('tbody');
    rows.forEach(row => tbody.appendChild(row));
}

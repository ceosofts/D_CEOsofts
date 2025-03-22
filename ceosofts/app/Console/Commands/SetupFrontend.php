<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SetupFrontend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-frontend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up frontend dependencies and environment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up frontend development environment...');

        // 1. Check if package.json exists
        if (!File::exists(base_path('package.json'))) {
            $this->error('package.json not found. Creating a default one...');
            $this->createDefaultPackageJson();
        }

        // 2. Check if node_modules exists
        if (!File::exists(base_path('node_modules'))) {
            $this->info('Installing Node.js dependencies...');
            
            // Fix: Use Symfony Process directly instead of the Laravel facade
            $process = new Process(['npm', 'install'], base_path());
            $process->setTimeout(null); // No timeout for npm install
            
            $this->info('Running npm install...');
            
            try {
                $process->setTty(true); // Show real-time output (if supported)
            } catch (\Exception $e) {
                $this->warn('TTY mode not available, hiding detailed output');
                // Continue without TTY mode for environments that don't support it
            }
            
            try {
                $process->run(function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        $this->error($buffer);
                    } else {
                        $this->info($buffer);
                    }
                });
                
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
                
                $this->info('Node.js dependencies installed successfully!');
                
            } catch (ProcessFailedException $exception) {
                $this->error('Failed to install dependencies.');
                $this->error($exception->getMessage());
                $this->info("\nYou can manually install dependencies by running:");
                $this->info('  cd ' . base_path());
                $this->info('  npm install');
                return Command::FAILURE;
            }
        } else {
            $this->info('Node modules already installed.');
        }

        // 3. Check if vite.config.js exists
        if (!File::exists(base_path('vite.config.js'))) {
            $this->info('Creating Vite configuration file...');
            $this->createViteConfig();
        }

        // 4. Create necessary JS module files
        $this->createJsModules();

        $this->info('Frontend environment setup completed!');
        $this->info("\nYou can now run:");
        $this->info('  npm run dev     - For development with hot-reload');
        $this->info('  npm run build   - For production build');
        
        return Command::SUCCESS;
    }

    /**
     * Create a default package.json file
     */
    protected function createDefaultPackageJson()
    {
        $packageJson = [
            'private' => true,
            'type' => 'module',
            'scripts' => [
                'dev' => 'vite',
                'build' => 'vite build'
            ],
            'devDependencies' => [
                'axios' => '^1.6.1',
                'laravel-vite-plugin' => '^0.8.0',
                'vite' => '^4.0.0',
                '@vitejs/plugin-vue' => '^4.2.3',
                'sass' => '^1.62.1'
            ],
            'dependencies' => [
                'bootstrap' => '^5.3.0',
                '@popperjs/core' => '^2.11.7',
                'alpinejs' => '^3.12.0',
                'jquery' => '^3.7.0'
            ]
        ];

        File::put(
            base_path('package.json'),
            json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        
        $this->info('Created default package.json');
    }

    /**
     * Create a default vite.config.js file
     */
    protected function createViteConfig()
    {
        $viteConfig = <<<'EOD'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
EOD;

        File::put(base_path('vite.config.js'), $viteConfig);
        $this->info('Created vite.config.js');

        // Create basic CSS and JS files if they don't exist
        $this->ensureResourcesExist();
    }

    /**
     * Create JavaScript module files
     */
    protected function createJsModules()
    {
        $modulesPath = resource_path('js/modules');
        File::ensureDirectoryExists($modulesPath);
        
        // Create forms.js
        $formsJs = <<<'EOD'
/**
 * Forms module
 * Contains functions for form handling, validation, and submission
 */

// Form validation
const validateForm = (form) => {
    // Basic validation logic
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
};

// Form submission with AJAX
const submitFormAjax = (form, callback) => {
    if (!validateForm(form)) {
        return false;
    }
    
    const formData = new FormData(form);
    const url = form.getAttribute('action');
    const method = form.getAttribute('method') || 'POST';
    
    axios({
        method: method,
        url: url,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (callback && typeof callback === 'function') {
            callback(response.data, null);
        }
    })
    .catch(error => {
        if (callback && typeof callback === 'function') {
            callback(null, error);
        }
    });
    
    return true;
};

// Initialize form handlers
const initForms = () => {
    document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitFormAjax(this, (data, error) => {
                if (error) {
                    console.error('Form submission error:', error);
                    // Handle error display
                } else {
                    console.log('Form submitted successfully:', data);
                    // Handle success
                }
            });
        });
    });
};

// Export functions
export { validateForm, submitFormAjax, initForms };

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initForms);
EOD;
        File::put($modulesPath . '/forms.js', $formsJs);
        $this->info('Created resources/js/modules/forms.js');
        
        // Create tables.js
        $tablesJs = <<<'EOD'
/**
 * Tables module
 * Contains functions for table manipulation, sorting, and filtering
 */

// Sort table by column
const sortTable = (table, columnIndex, ascending = true) => {
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
};

// Filter table rows based on search input
const filterTable = (table, searchText) => {
    const rows = table.querySelectorAll('tbody tr');
    const lowerSearchText = searchText.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(lowerSearchText) ? '' : 'none';
    });
};

// Initialize sortable and filterable tables
const initTables = () => {
    // Setup sortable tables
    document.querySelectorAll('table.sortable').forEach(table => {
        const headers = table.querySelectorAll('thead th[data-sort]');
        
        headers.forEach((th, index) => {
            th.addEventListener('click', () => {
                const currentSort = th.getAttribute('data-sort');
                const ascending = currentSort !== 'asc';
                
                // Reset all headers
                headers.forEach(header => header.setAttribute('data-sort', ''));
                
                // Set current header sort direction
                th.setAttribute('data-sort', ascending ? 'asc' : 'desc');
                
                // Sort the table
                sortTable(table, index, ascending);
            });
        });
    });
    
    // Setup table filters
    document.querySelectorAll('.table-filter-input').forEach(input => {
        const targetTable = document.querySelector(input.getAttribute('data-table'));
        if (targetTable) {
            input.addEventListener('input', () => {
                filterTable(targetTable, input.value);
            });
        }
    });
};

// Export functions
export { sortTable, filterTable, initTables };

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initTables);
EOD;
        File::put($modulesPath . '/tables.js', $tablesJs);
        $this->info('Created resources/js/modules/tables.js');
        
        // Create notifications.js
        $notificationsJs = <<<'EOD'
/**
 * Notifications module
 * Contains functions for displaying alerts, toasts, and notifications
 */

// Toast notification types
const TOAST_TYPES = {
    SUCCESS: 'success',
    ERROR: 'danger',
    WARNING: 'warning',
    INFO: 'info'
};

// Show a toast notification
const showToast = (message, type = TOAST_TYPES.INFO, title = '', duration = 5000) => {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('id', toastId);
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Toast content
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${title ? `<strong>${title}</strong><br>` : ''}
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    // Add toast to container
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: duration });
    bsToast.show();
    
    // Remove toast from DOM after hiding
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
    
    return toastId;
};

// Show success toast
const showSuccess = (message, title = 'Success') => {
    return showToast(message, TOAST_TYPES.SUCCESS, title);
};

// Show error toast
const showError = (message, title = 'Error') => {
    return showToast(message, TOAST_TYPES.ERROR, title);
};

// Show warning toast
const showWarning = (message, title = 'Warning') => {
    return showToast(message, TOAST_TYPES.WARNING, title);
};

// Show info toast
const showInfo = (message, title = 'Information') => {
    return showToast(message, TOAST_TYPES.INFO, title);
};

// Export functions and constants
export { 
    TOAST_TYPES,
    showToast,
    showSuccess,
    showError,
    showWarning,
    showInfo
};

// Initialize notifications from flash messages
document.addEventListener('DOMContentLoaded', () => {
    // Check for flash messages in the DOM (example implementation)
    const flashMessages = document.querySelectorAll('[data-flash-message]');
    flashMessages.forEach(el => {
        const type = el.getAttribute('data-flash-type') || TOAST_TYPES.INFO;
        const message = el.getAttribute('data-flash-message');
        const title = el.getAttribute('data-flash-title') || '';
        
        if (message) {
            showToast(message, type, title);
        }
        
        // Remove the element after processing
        el.remove();
    });
});
EOD;
        File::put($modulesPath . '/notifications.js', $notificationsJs);
        $this->info('Created resources/js/modules/notifications.js');
    }

    /**
     * Ensure basic CSS and JS resources exist
     */
    protected function ensureResourcesExist()
    {
        // Create resources/css/app.css if it doesn't exist
        $cssPath = resource_path('css/app.css');
        if (!File::exists($cssPath)) {
            File::ensureDirectoryExists(resource_path('css'));
            File::put($cssPath, "/* Base CSS styles */\n@import 'bootstrap/dist/css/bootstrap.min.css';\n");
            $this->info('Created resources/css/app.css');
        }

        // Create resources/js/app.js if it doesn't exist
        $jsPath = resource_path('js/app.js');
        if (!File::exists($jsPath)) {
            File::ensureDirectoryExists(resource_path('js'));
            $js = <<<'EOD'
import './bootstrap';
import '../css/app.css';

// Import Bootstrap JS
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

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

// You can add your custom JavaScript code here
EOD;
            File::put($jsPath, $js);
            $this->info('Created resources/js/app.js');
        }

        // Create resources/js/bootstrap.js if it doesn't exist
        $bootstrapJsPath = resource_path('js/bootstrap.js');
        if (!File::exists($bootstrapJsPath)) {
            $bootstrapJs = <<<'EOD'
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
EOD;
            File::put($bootstrapJsPath, $bootstrapJs);
            $this->info('Created resources/js/bootstrap.js');
        }
    }
}

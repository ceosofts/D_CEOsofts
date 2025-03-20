<div 
    class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-800 bg-opacity-50 z-50" 
    style="display: none"
    id="{{ $id ?? 'loading-indicator' }}"
>
    <div class="bg-white p-5 rounded-lg shadow-lg flex flex-col items-center">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="sr-only">กำลังโหลด...</span>
        </div>
        <p class="text-gray-700">{{ $message ?? 'กำลังโหลด...' }}</p>
    </div>
</div>

<script>
    // Function to show/hide the loading indicator
    function toggleLoading(show, id = 'loading-indicator') {
        const loadingElement = document.getElementById(id);
        if (loadingElement) {
            loadingElement.style.display = show ? 'flex' : 'none';
        }
    }
    
    // Add this to window object so it can be called from anywhere
    window.toggleLoading = toggleLoading;
    
    // Example usage:
    // Toggle show: window.toggleLoading(true)
    // Toggle hide: window.toggleLoading(false)
</script>

{{-- resources/views/components/ui/loader.blade.php --}}
<div id="global-loader" class="fixed inset-0 z-[9999] hidden flex-col items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300">
    <div class="flex flex-col items-center gap-4 rounded-2xl bg-white p-8 shadow-2xl transform scale-95 transition-transform duration-300 loader-content">
        <div class="relative flex h-12 w-12 items-center justify-center">
            {{-- Spinner --}}
            <svg class="h-12 w-12 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <div class="text-center">
            <h3 class="text-base font-semibold text-slate-800">Memproses...</h3>
            <p class="text-sm text-slate-500 mt-1">Mohon tunggu sebentar.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('global-loader');
        const loaderContent = loader.querySelector('.loader-content');
        
        // Function to show loader
        window.showGlobalLoader = function() {
            loader.classList.remove('hidden');
            // Small delay to allow display:block to apply before animating opacity/transform
            setTimeout(() => {
                loader.classList.remove('opacity-0');
                loaderContent.classList.remove('scale-95');
                loaderContent.classList.add('scale-100');
            }, 10);
        };

        // Function to hide loader
        window.hideGlobalLoader = function() {
            loader.classList.add('opacity-0');
            loaderContent.classList.remove('scale-100');
            loaderContent.classList.add('scale-95');
            setTimeout(() => {
                loader.classList.add('hidden');
            }, 300); // match duration-300
        };

        // Initialize hidden state
        loader.classList.add('opacity-0');

        // Show loader on form submits unless they have data-no-loader
        document.querySelectorAll('form:not([data-no-loader])').forEach(form => {
            form.addEventListener('submit', function() {
                // If form is valid (for required fields etc)
                if (!this.checkValidity || this.checkValidity()) {
                    showGlobalLoader();
                }
            });
        });

        // Intercept fetch to show loader
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            // Check if we should ignore this fetch (e.g. background polling)
            const options = args[1] || {};
            if (options.headers && options.headers['X-No-Loader']) {
                return originalFetch.apply(this, args);
            }

            showGlobalLoader();
            try {
                const response = await originalFetch.apply(this, args);
                return response;
            } finally {
                hideGlobalLoader();
            }
        };

        // Example for Axios interceptors if Axios is used globally
        if (window.axios) {
            window.axios.interceptors.request.use(function (config) {
                if (!config.headers['X-No-Loader']) {
                    showGlobalLoader();
                }
                return config;
            }, function (error) {
                hideGlobalLoader();
                return Promise.reject(error);
            });

            window.axios.interceptors.response.use(function (response) {
                hideGlobalLoader();
                return response;
            }, function (error) {
                hideGlobalLoader();
                return Promise.reject(error);
            });
        }
    });
</script>

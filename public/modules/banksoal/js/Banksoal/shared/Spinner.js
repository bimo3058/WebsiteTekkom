class SpinnerManager {
    constructor() {
        this.injectStyles();
    }

    injectStyles() {
        // Prevent duplicate injection
        if (document.getElementById('banksoal-spinner-styles')) return;

        const style = document.createElement('style');
        style.id = 'banksoal-spinner-styles';
        style.innerHTML = `
            /* Global Spinner */
            .pm-loader {
                position: fixed;
                inset: 0;
                background: rgba(255, 255, 255, 0.7);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 999999;
            }
            .pm-loader.show {
                display: flex !important;
            }
            .pm-spinner {
                width: 36px;
                height: 36px;
                border: 3px solid #e2e8f0;
                border-top-color: rgb(11, 38, 110);
                border-radius: 50%;
                animation: pm-spin 0.7s linear infinite;
            }
            @keyframes pm-spin {
                to { transform: rotate(360deg); }
            }

            /* Table/Small Spinner */
            .tbl-loading {
                display: none;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 40px 20px;
                color: #475569;
                font-size: 13px;
            }
            .tbl-loading.show {
                display: flex;
            }
            .tbl-spinner {
                width: 22px;
                height: 22px;
                border: 3px solid #e2e8f0;
                border-top-color: rgb(11, 38, 110);
                border-radius: 50%;
                animation: tbl-spin 0.7s linear infinite;
                flex-shrink: 0;
            }
            @keyframes tbl-spin {
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }

    showGlobal() {
        let loader = document.getElementById('global-spinner');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'global-spinner';
            loader.className = 'pm-loader';
            loader.innerHTML = '<div class="pm-spinner"></div>';
            document.body.appendChild(loader);
        }
        loader.classList.add('show');
    }

    hideGlobal() {
        const loader = document.getElementById('global-spinner');
        if (loader) {
            loader.classList.remove('show');
        }
    }

    showTable(containerId, message = 'Memuat data...') {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.classList.add('tbl-loading');
        container.innerHTML = `<div class="tbl-spinner"></div> ${message}`;
        container.classList.add('show');
    }

    hideTable(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.classList.remove('show');
        }
    }
}

// Initialize globally
window.Spinner = new SpinnerManager();

// Backwards compatibility for existing inline calls
window.showLoader = () => window.Spinner.showGlobal();
window.hideLoader = () => window.Spinner.hideGlobal();

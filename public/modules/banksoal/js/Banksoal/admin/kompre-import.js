window.kompreImport = function (config) {
    return {
        importOpen: false,
        importFiles: [],
        importPreview: null,
        importToken: null,
        importLoading: false,
        importExecuting: false,
        importResult: null,
        importError: null,

        openImport() {
            this.importOpen = true;
            this.resetImport();
        },

        closeImport() {
            if (this.importExecuting) return;
            this.importOpen = false;
        },

        resetImport() {
            this.importFiles = [];
            this.importPreview = null;
            this.importToken = null;
            this.importResult = null;
            this.importError = null;
        },

        onImportFiles(event) {
            this.importFiles = Array.from(event.target.files || []);
            this.importPreview = null;
            this.importToken = null;
            this.importResult = null;
            this.importError = null;
        },

        async previewImport() {
            if (!this.importFiles.length) {
                this.importError = 'Pilih minimal satu file terlebih dahulu.';
                return;
            }

            this.importLoading = true;
            this.importError = null;

            const form = new FormData();
            this.importFiles.forEach((file) => form.append('files[]', file));
            form.append('_token', config.csrf);

            try {
                const response = await fetch(config.previewUrl, {
                    method: 'POST',
                    body: form,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memproses file.');
                }

                this.importPreview = data;
                this.importToken = data.token;
            } catch (error) {
                this.importError = error.message;
            } finally {
                this.importLoading = false;
            }
        },

        async executeImport() {
            if (!this.importToken || this.importExecuting) return;

            this.importExecuting = true;
            this.importError = null;

            const form = new FormData();
            form.append('_token', config.csrf);

            try {
                const response = await fetch(
                    config.executeUrl.replace('__TOKEN__', this.importToken),
                    {
                        method: 'POST',
                        body: form,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    }
                );
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menjalankan import.');
                }

                this.importResult = data;
                this.importPreview = null;
                this.importToken = null;
            } catch (error) {
                this.importError = error.message;
            } finally {
                this.importExecuting = false;
            }
        },
    };
};

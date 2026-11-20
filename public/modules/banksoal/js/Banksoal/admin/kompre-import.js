window.kompreImport = function (config) {
    return {
        importOpen: false,
        importFile: null,
        importFileError: null,
        importPreview: null,
        importToken: null,
        importLoading: false,
        importExecuting: false,
        importResult: null,
        importError: null,
        maxFileSize: 10 * 1024 * 1024,

        openImport() {
            this.importOpen = true;
            this.resetImport();
        },

        closeImport() {
            if (this.importExecuting) return;
            this.importOpen = false;
        },

        resetImport() {
            this.importFile = null;
            this.importFileError = null;
            this.importPreview = null;
            this.importToken = null;
            this.importResult = null;
            this.importError = null;

            if (this.$refs.importFileInput) {
                this.$refs.importFileInput.value = '';
            }
        },

        formatFileSize(bytes) {
            const mb = bytes / (1024 * 1024);

            if (mb >= 1) {
                return mb.toFixed(1).replace('.', ',') + ' MB';
            }

            return (bytes / 1024).toFixed(1).replace('.', ',') + ' KB';
        },

        validateImportFile(file) {
            const extension = file.name.split('.').pop().toLowerCase();

            if (!['xlsx', 'xls'].includes(extension)) {
                return 'Format file harus .xlsx atau .xls.';
            }

            if (file.size > this.maxFileSize) {
                return 'File ' + this.formatFileSize(file.size) + ' melebihi batas 10 MB. Pilih file yang lebih kecil.';
            }

            return null;
        },

        onImportFiles(event) {
            const file = event.target.files?.[0] || null;

            this.importFile = file;
            this.importFileError = file ? this.validateImportFile(file) : null;
            this.importPreview = null;
            this.importToken = null;
            this.importResult = null;
            this.importError = null;
        },

        removeImportFile() {
            this.importFile = null;
            this.importFileError = null;
            this.importPreview = null;
            this.importToken = null;
            this.importResult = null;
            this.importError = null;

            if (this.$refs.importFileInput) {
                this.$refs.importFileInput.value = '';
            }
        },

        async previewImport() {
            if (!this.importFile) {
                this.importError = 'Pilih file terlebih dahulu.';
                return;
            }

            if (this.importFileError) {
                return;
            }

            this.importLoading = true;
            this.importError = null;

            const form = new FormData();
            form.append('files[]', this.importFile);
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

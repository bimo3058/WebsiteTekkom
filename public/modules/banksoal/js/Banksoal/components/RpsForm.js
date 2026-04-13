/**
 * ════════════════════════════════════════════════════════════════════
 * RpsForm.js - RPS Form Component
 * ════════════════════════════════════════════════════════════════════
 * Handles cascading dropdowns and form validation for RPS submission.
 *
 * Features:
 * - Cascading: Mata Kuliah → Dosen + CPL → CPMK
 * - File drag & drop upload
 * - Form validation before submit
 * - Error handling with Snackbar notifications
 *
 * Usage:
 *   RpsForm.init({
 *       mkSelectId: 'mkSelect',           // Mata Kuliah select
 *       dosenMsId: 'dosenMs',             // Dosen multi-select
 *       cplMsId: 'cplMs',                 // CPL multi-select
 *       cpmkMsId: 'cpmkMs',               // CPMK multi-select
 *       fileInputId: 'fileInput',         // File input
 *       uploadZoneId: 'uploadZone',       // Drag & drop zone
 *       formSelector: 'form'              // Form element
 *   });
 */

class RpsFormComponent {
    constructor(config) {
        this.config = {
            mkSelectId: "mkSelect",
            dosenMsId: "dosenMs",
            cplMsId: "cplMs",
            cpmkMsId: "cpmkMs",
            fileInputId: "fileInput",
            uploadZoneId: "uploadZone",
            formSelector: "form",
            ...config,
        };

        this.mkSelect = document.getElementById(this.config.mkSelectId);
        this.fileInput = document.getElementById(this.config.fileInputId);
        this.uploadZone = document.getElementById(this.config.uploadZoneId);
        this.form = document.querySelector(this.config.formSelector);

        // MultiSelect instances (will be set by init)
        this.dosenMs = null;
        this.cplMs = null;
        this.cpmkMs = null;
    }

    /**
     * Initialize form component with MultiSelect instances
     * @param {Object} multiSelectInstances - {dosenMs, cplMs, cpmkMs}
     */
    init(multiSelectInstances) {
        this.dosenMs = multiSelectInstances.dosenMs;
        this.cplMs = multiSelectInstances.cplMs;
        this.cpmkMs = multiSelectInstances.cpmkMs;

        this._setupCascading();
        this._setupFileUpload();
        this._setupFormValidation();
    }

    /**
     * Setup cascading dropdowns
     * @private
     */
    _setupCascading() {
        // ── CASCADE: Mata Kuliah → Dosen + CPL ──
        this.mkSelect.addEventListener("change", () =>
            this._onMataKuliahChange(),
        );

        // ── CASCADE: CPL → CPMK ──
        document
            .getElementById(this.config.cplMsId)
            ?.addEventListener("ms:change", (e) => {
                this._onCplChange(e.detail);
            });
    }

    /**
     * Handle Mata Kuliah selection change
     * @private
     */
    _onMataKuliahChange() {
        const mkId = this.mkSelect.value;

        if (!mkId) {
            this.dosenMs.setDisabled("Pilih mata kuliah terlebih dahulu");
            this.cplMs.setDisabled("Pilih mata kuliah terlebih dahulu");
            this.cpmkMs.setDisabled("Pilih CPL terlebih dahulu");
            return;
        }

        this.dosenMs.setLoading("Memuat dosen…");
        this.cplMs.setLoading("Memuat CPL…");
        this.cpmkMs.setDisabled("Pilih CPL terlebih dahulu");

        // Fetch Dosen
        this._fetchDosen();

        // Fetch CPL
        this._fetchCpl(mkId);
    }

    /**
     * Fetch dosen list from API
     * @private
     */
    _fetchDosen() {
        const url =
            document.querySelector("[data-route-dosen]")?.dataset.routeDosen ||
            "/bank-soal/rps/dosen/dosen";

        fetch(url)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                if (data?.length) {
                    this.dosenMs.setItems(
                        data.map((d) => ({ id: d.id, label: d.name })),
                        "Pilih dosen",
                    );
                } else {
                    this.dosenMs.setDisabled("Tidak ada dosen terdaftar");
                }
            })
            .catch((err) => {
                console.error("Error fetching dosen:", err);
                this.dosenMs.setDisabled("Error loading dosen");
                Snackbar?.show?.("Gagal memuat dosen", "error");
            });
    }

    /**
     * Fetch CPL list from API
     * @private
     */
    _fetchCpl(mkId) {
        const baseUrl =
            document.querySelector("[data-route-cpl]")?.dataset.routeCpl ||
            "/bank-soal/rps/dosen/cpl";
        const url = `${baseUrl}/${mkId}`;

        fetch(url)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                if (data?.length) {
                    this.cplMs.setItems(
                        data.map((c) => ({ id: c.id, label: c.kode })),
                        "Pilih CPL",
                    );
                } else {
                    this.cplMs.setDisabled(
                        "Tidak ada CPL untuk mata kuliah ini",
                    );
                }
            })
            .catch((err) => {
                console.error("Error fetching CPL:", err);
                this.cplMs.setDisabled("Error loading CPL");
                Snackbar?.show?.("Gagal memuat CPL", "error");
            });
    }

    /**
     * Handle CPL selection change (fetch CPMK)
     * @private
     */
    _onCplChange(cplIds) {
        if (!cplIds.length) {
            this.cpmkMs.setDisabled("Pilih CPL terlebih dahulu");
            return;
        }

        this.cpmkMs.setLoading("Memuat CPMK…");

        const baseUrl =
            document.querySelector("[data-route-cpmk]")?.dataset.routeCpmk ||
            "/bank-soal/rps/dosen/cpmk";
        const queryString = cplIds.map((id) => `cpl_ids[]=${id}`).join("&");
        const url = `${baseUrl}?${queryString}`;

        fetch(url)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                if (data?.length) {
                    this.cpmkMs.setItems(
                        data.map((c) => ({ id: c.id, label: c.kode })),
                        "Pilih CPMK",
                    );
                } else {
                    this.cpmkMs.setDisabled(
                        "Tidak ada CPMK untuk CPL yang dipilih",
                    );
                }
            })
            .catch((err) => {
                console.error("Error fetching CPMK:", err);
                this.cpmkMs.setDisabled("Error loading CPMK");
                Snackbar?.show?.("Gagal memuat CPMK", "error");
            });
    }

    /**
     * Setup file upload drag & drop
     * @private
     */
    _setupFileUpload() {
        if (!this.uploadZone || !this.fileInput) return;

        this.uploadZone.addEventListener("dragover", (e) => {
            e.preventDefault();
            this.uploadZone.style.borderColor = "#3b82f6";
            this.uploadZone.style.backgroundColor = "rgba(59, 130, 246, 0.05)";
        });

        this.uploadZone.addEventListener("dragleave", () => {
            this.uploadZone.style.borderColor = "#d1d5db";
            this.uploadZone.style.backgroundColor = "#fff";
        });

        this.uploadZone.addEventListener("drop", (e) => {
            e.preventDefault();
            this.uploadZone.style.borderColor = "#d1d5db";
            this.uploadZone.style.backgroundColor = "#fff";
            this.fileInput.files = e.dataTransfer.files;
            this._updateFileName();
        });

        this.fileInput.addEventListener("change", () => this._updateFileName());
    }

    /**
     * Update file name display
     * @private
     */
    _updateFileName() {
        if (this.fileInput.files?.length) {
            const uploadText = document.getElementById("uploadText");
            if (uploadText)
                uploadText.textContent = this.fileInput.files[0].name;
        }
    }

    /**
     * Setup form validation
     * @private
     */
    _setupFormValidation() {
        this.form?.addEventListener("submit", (e) => {
            const errors = this._validateForm();
            if (errors.length) {
                e.preventDefault();
                const errorMsg = errors
                    .map((e, i) => `${i + 1}. ${e}`)
                    .join("\n");
                Snackbar?.show?.(`Validasi Error:\n${errorMsg}`, "error");
            }
        });
    }

    /**
     * Validate form fields
     * @private
     * @returns {Array<string>} Array of error messages
     */
    _validateForm() {
        const errors = [];

        if (!this.mkSelect.value) {
            errors.push("Mata Kuliah harus dipilih");
        }
        if (!this.cplMs.getSelected().length) {
            errors.push("Minimal satu CPL harus dipilih");
        }
        if (!this.cpmkMs.getSelected().length) {
            errors.push("Minimal satu CPMK harus dipilih");
        }
        if (!this.fileInput?.files?.length) {
            errors.push("File RPS harus diunggah");
        }

        return errors;
    }
}

// Export singleton
const RpsForm = new RpsFormComponent();

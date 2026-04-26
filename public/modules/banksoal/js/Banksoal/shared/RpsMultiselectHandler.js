/**
 * RpsMultiselectHandler
 * Handles multiselect dropdowns for RPS form using TomSelect
 * Manages: Dosen Pengampu Lain, CPL, CPMK
 */
class RpsMultiselectHandler {
    constructor(config = {}) {
        this.config = {
            dosenSelectId: "dosenSelect",
            cplSelectId: "cplSelect",
            cpmkSelectId: "cpmkSelect",
            mkSelectId: "mkSelect",
            rootElement: document,
            routeSourceElement: null,
            isEditForm: false,
            rpsId: null,
            ...config,
        };

        this.rootElement = this.config.rootElement || document;
        this.routeSourceElement =
            this.config.routeSourceElement || this.rootElement;
        this.formElement =
            this.rootElement instanceof HTMLFormElement
                ? this.rootElement
                : this.rootElement.querySelector?.("form") ||
                  this.rootElement.closest?.("form") ||
                  document.querySelector("form");

        this.dosenSelect = this._findFieldById(this.config.dosenSelectId);
        this.cplSelect = this._findFieldById(this.config.cplSelectId);
        this.cpmkSelect = this._findFieldById(this.config.cpmkSelectId);
        this.mkSelect = this._findFieldById(this.config.mkSelectId);

        // TomSelect instances
        this.dosenTs = null;
        this.cplTs = null;
        this.cpmkTs = null;
    }

    _findFieldById(id) {
        if (!id) return null;

        if (
            this.rootElement &&
            typeof this.rootElement.querySelector === "function"
        ) {
            const scoped = this.rootElement.querySelector(`#${id}`);
            if (scoped) return scoped;
        }

        return document.getElementById(id);
    }

    _resolveRoute(dataKey, fallback) {
        if (
            this.routeSourceElement &&
            this.routeSourceElement.dataset &&
            this.routeSourceElement.dataset[dataKey]
        ) {
            return this.routeSourceElement.dataset[dataKey];
        }

        return (
            document.querySelector(
                `[data-${dataKey.replace(/([A-Z])/g, "-$1").toLowerCase()}]`,
            )?.dataset?.[dataKey] || fallback
        );
    }

    _normalizeSelectedIds(values) {
        if (!values) return [];
        return Array.isArray(values)
            ? values.map((id) => id.toString())
            : [values.toString()];
    }

    /**
     * Initialize all multiselect dropdowns
     */
    init() {
        try {
            // Debug: Check if elements exist
            console.log("=== RpsMultiselectHandler Init ===");
            console.log(
                "MK Select element:",
                this.mkSelect,
                "ID:",
                this.config.mkSelectId,
            );
            console.log(
                "Dosen Select element:",
                this.dosenSelect,
                "ID:",
                this.config.dosenSelectId,
            );
            console.log(
                "CPL Select element:",
                this.cplSelect,
                "ID:",
                this.config.cplSelectId,
            );
            console.log(
                "CPMK Select element:",
                this.cpmkSelect,
                "ID:",
                this.config.cpmkSelectId,
            );

            // Initialize TomSelect instances
            this._initDosenMultiselect();
            this._initCplMultiselect();
            this._initCpmkMultiselect();

            // Setup event listeners
            this._setupEventListeners();

            // Setup form validation
            this._setupFormValidation();

            // Initialize all dropdowns as DISABLED until MK is selected
            this._initializeAllDisabled();

            console.log("RpsMultiselectHandler initialized successfully");
        } catch (error) {
            console.error("Error initializing RpsMultiselectHandler:", error);
        }
    }

    /**
     * Initialize all dropdowns as disabled
     * Dosen dan CPL akan unlock saat MK dipilih
     * CPMK akan unlock saat CPL dipilih
     */
    _initializeAllDisabled() {
        if (this.dosenTs) {
            this.dosenTs.disable();
            this.dosenTs.clearOptions();
            this._setPlaceholder(
                this.dosenTs,
                "Pilih Mata Kuliah Terlebih Dahulu",
            );
        }
        if (this.cplTs) {
            this.cplTs.disable();
            this.cplTs.clearOptions();
            this._setPlaceholder(
                this.cplTs,
                "Pilih Mata Kuliah Terlebih Dahulu",
            );
        }
        if (this.cpmkTs) {
            this.cpmkTs.disable();
            this.cpmkTs.clearOptions();
            this._setPlaceholder(this.cpmkTs, "Pilih CPL Terlebih Dahulu");
        }
    }

    /**
     * Helper method to set placeholder on TomSelect
     */
    _setPlaceholder(tomSelectInstance, text) {
        if (!tomSelectInstance) return;
        if (tomSelectInstance.input) {
            tomSelectInstance.input.placeholder = text;
        }
    }

    /**
     * Initialize Dosen Pengampu Lain multiselect
     */
    _initDosenMultiselect() {
        if (!this.dosenSelect) return;

        this.dosenTs = new TomSelect(this.dosenSelect, {
            placeholder: "Pilih Mata Kuliah Terlebih Dahulu",
            searchField: "name",
            labelField: "name",
            valueField: "id",
            maxOptions: 100, // Allow up to 100 dosen to be selected
            plugins: {
                remove_button: {
                    title: "Hapus dosen ini",
                },
            },
            onOptionRemove: (value) => {
                // Trigger change event for validation
                this.dosenSelect.dispatchEvent(new Event("change"));
            },
            onOptionAdd: (value) => {
                this.dosenSelect.dispatchEvent(new Event("change"));
            },
        });
    }

    /**
     * Initialize CPL multiselect
     */
    _initCplMultiselect() {
        if (!this.cplSelect) return;

        this.cplTs = new TomSelect(this.cplSelect, {
            placeholder: "Pilih Mata Kuliah Terlebih Dahulu",
            searchField: "kode",
            labelField: "kode",
            valueField: "id",
            plugins: {
                remove_button: {
                    title: "Hapus CPL ini",
                },
            },
            onOptionRemove: (value) => {
                this.cplSelect.dispatchEvent(new Event("change"));
                this._onCplChange(); // Trigger CPMK update
            },
            onOptionAdd: (value) => {
                this.cplSelect.dispatchEvent(new Event("change"));
                this._onCplChange(); // Trigger CPMK update
            },
        });
    }

    /**
     * Initialize CPMK multiselect
     */
    _initCpmkMultiselect() {
        if (!this.cpmkSelect) return;

        this.cpmkTs = new TomSelect(this.cpmkSelect, {
            placeholder: "Pilih CPL Terlebih Dahulu",
            searchField: "kode",
            labelField: "kode",
            valueField: "id",
            plugins: {
                remove_button: {
                    title: "Hapus CPMK ini",
                },
            },
            onOptionRemove: (value) => {
                this.cpmkSelect.dispatchEvent(new Event("change"));
            },
            onOptionAdd: (value) => {
                this.cpmkSelect.dispatchEvent(new Event("change"));
            },
        });
    }

    /**
     * Load initial data for CREATE form (show all options without waiting for MK selection)
     */
    _loadInitialData() {
        // Load all Dosen (tidak tergantung MK) - untuk CREATE form, selalu tampilkan semua
        this._fetchAndPopulateDosen(null);

        // Load all CPL (tidak tergantung MK) - untuk CREATE form, selalu tampilkan semua
        this._fetchAndPopulateCpl(null);

        // Load all CPMK (tidak tergantung CPL) - initial load semua
        this._fetchAndPopulateCpmk();
    }

    /**
     * Setup event listeners for cascading loads
     */
    _setupEventListeners() {
        if (this.mkSelect) {
            console.log("Setting up MK change listener");
            this.mkSelect.addEventListener("change", () => {
                console.log("MK changed:", this.mkSelect.value);
                this._onMataKuliahChange();
            });

            const mkTomSelect = this.mkSelect.tomselect;
            if (mkTomSelect && typeof mkTomSelect.on === "function") {
                mkTomSelect.on("change", () => {
                    console.log(
                        "MK changed via TomSelect:",
                        this.mkSelect.value,
                    );
                    this._onMataKuliahChange();
                });
            }
        } else {
            console.warn("MK Select element not found!");
        }

        if (this.cplSelect) {
            console.log("Setting up CPL change listener");
            this.cplSelect.addEventListener("change", () => {
                console.log("CPL changed:", this.cplTs?.getValue());
                this._onCplChange();
            });
        } else {
            console.warn("CPL Select element not found!");
        }
    }

    /**
     * Handle Mata Kuliah change event
     * Enable Dosen dan CPL dropdowns ketika MK dipilih
     * Disable dan clear ketika MK tidak dipilih
     */
    _onMataKuliahChange() {
        const mkId = this.mkSelect?.value;

        if (!mkId) {
            // MK tidak dipilih: DISABLE dan clear Dosen, CPL, CPMK
            console.log("MK kosong - disable semua dropdown");

            if (this.dosenTs) {
                this.dosenTs.disable();
                this.dosenTs.clearOptions();
                this._setPlaceholder(
                    this.dosenTs,
                    "Pilih Mata Kuliah Terlebih Dahulu",
                );
            }
            if (this.cplTs) {
                this.cplTs.disable();
                this.cplTs.clearOptions();
                this._setPlaceholder(
                    this.cplTs,
                    "Pilih Mata Kuliah Terlebih Dahulu",
                );
            }
            if (this.cpmkTs) {
                this.cpmkTs.disable();
                this.cpmkTs.clearOptions();
                this._setPlaceholder(this.cpmkTs, "Pilih CPL Terlebih Dahulu");
            }
            return;
        }

        // MK dipilih: ENABLE Dosen dan CPL, load datanya
        console.log("MK dipilih (id:", mkId, ") - enable Dosen dan CPL");

        // Update placeholder untuk Dosen dan CPL
        if (this.dosenTs) {
            this._setPlaceholder(this.dosenTs, "Pilih dosen pengampu lain");
        }
        if (this.cplTs) {
            this._setPlaceholder(this.cplTs, "Pilih CPL");
        }

        // Fetch dan populate Dosen
        this._fetchAndPopulateDosen(mkId);

        // Fetch dan populate CPL
        this._fetchAndPopulateCpl(mkId);

        // Clear CPMK sampai CPL dipilih
        if (this.cpmkTs) {
            this.cpmkTs.disable();
            this.cpmkTs.clearOptions();
            this._setPlaceholder(this.cpmkTs, "Pilih CPL Terlebih Dahulu");
        }
    }

    /**
     * Handle CPL change event
     * Enable CPMK ketika CPL dipilih
     * Disable dan clear CPMK ketika CPL kosong
     */
    _onCplChange() {
        const selectedCplIds = this.cplTs?.getValue();

        if (!selectedCplIds || selectedCplIds.length === 0) {
            // CPL kosong: DISABLE dan clear CPMK
            console.log("CPL kosong - disable CPMK");
            if (this.cpmkTs) {
                this.cpmkTs.disable();
                this.cpmkTs.clearOptions();
                this._setPlaceholder(this.cpmkTs, "Pilih CPL Terlebih Dahulu");
            }
            return;
        }

        // CPL dipilih: ENABLE CPMK dan fetch datanya
        console.log(
            "CPL dipilih:",
            selectedCplIds,
            "- enable dan populate CPMK",
        );

        // Update placeholder untuk CPMK
        if (this.cpmkTs) {
            this.cpmkTs.enable();
            this._setPlaceholder(this.cpmkTs, "Pilih CPMK");
        }

        // Fetch CPMK berdasarkan CPL yang dipilih
        this._fetchAndPopulateCpmkByCpl(selectedCplIds);
    }

    /**
     * Fetch and populate CPL dropdown based on MK
     * If mkId is null/undefined, fetch all CPL
     */
    _fetchAndPopulateCpl(mkId) {
        const routeCpl = this._resolveRoute(
            "routeCpl",
            "/bank-soal/rps/dosen/cpl",
        );

        // Build the URL based on whether mkId is provided
        let url = routeCpl;
        if (mkId) {
            url = `${routeCpl}/${mkId}`;
        }

        console.log("Fetching CPL from URL:", url, "MK ID:", mkId);

        // Show loading state
        if (this.cplTs) {
            this._setPlaceholder(this.cplTs, "⏳ Memperbarui CPL...");
        }

        fetch(url)
            .then((r) => {
                console.log("CPL Response status:", r.status);
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                console.log("CPL data received:", data);
                if (this.cplTs) {
                    this.cplTs.clearOptions();
                    if (data && data.length > 0) {
                        this.cplTs.addOptions(data);
                        this.cplTs.enable();
                        this._setPlaceholder(this.cplTs, "");

                        if (
                            this.config.isEditForm &&
                            this.config.selectedCplIds?.length
                        ) {
                            this.cplTs.setValue(
                                this._normalizeSelectedIds(
                                    this.config.selectedCplIds,
                                ),
                            );
                            this._onCplChange();
                        }
                    } else {
                        this.cplTs.disable();
                        this._setPlaceholder(
                            this.cplTs,
                            "Tidak ada CPL untuk MK ini",
                        );
                        if (mkId) {
                            Snackbar?.show?.(
                                "Tidak ada CPL untuk mata kuliah ini",
                                "info",
                            );
                        }
                    }
                }
            })
            .catch((err) => {
                console.error("Error fetching CPL:", err);
                if (this.cplTs) {
                    this.cplTs.clearOptions();
                    this.cplTs.disable();
                    this._setPlaceholder(this.cplTs, "Gagal memuat CPL");
                }
                Snackbar?.show?.("Gagal memuat CPL", "error");
            });
    }

    /**
     * Fetch and populate Dosen dropdown - fetch all dosen users with role "dosen"
     * Always fetch all dosen regardless of MK selection
     */
    _fetchAndPopulateDosen(mkId) {
        const routeDosen = this._resolveRoute(
            "routeDosen",
            "/bank-soal/rps/dosen/dosen",
        );

        // Always fetch without parameters - get all dosen users
        const url = routeDosen;

        console.log("Fetching Dosen from URL:", url);

        // Show loading state
        if (this.dosenTs) {
            this._setPlaceholder(this.dosenTs, "Mengambil data dosen...");
        }

        fetch(url)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                console.log("Dosen data received:", data);
                if (this.dosenTs) {
                    this.dosenTs.clearOptions();
                    if (data && data.length > 0) {
                        this.dosenTs.addOptions(data);
                        this.dosenTs.enable();
                        this._setPlaceholder(this.dosenTs, "");

                        if (
                            this.config.isEditForm &&
                            this.config.selectedDosenIds?.length
                        ) {
                            this.dosenTs.setValue(
                                this._normalizeSelectedIds(
                                    this.config.selectedDosenIds,
                                ),
                            );
                        }
                    } else {
                        // Even if no data, keep enabled (don't disable)
                        this.dosenTs.enable();
                        this._setPlaceholder(
                            this.dosenTs,
                            "Tidak ada dosen tersedia",
                        );
                        console.warn("No dosen data received");
                    }
                }
            })
            .catch((err) => {
                console.error("Error fetching Dosen:", err);
                if (this.dosenTs) {
                    this.dosenTs.clearOptions();
                    this.dosenTs.enable();
                    this._setPlaceholder(
                        this.dosenTs,
                        "Gagal memuat data dosen",
                    );
                }
                Snackbar?.show?.("Gagal memuat dosen", "error");
            });
    }

    /**
     * Fetch and populate CPMK dropdown - for CREATE form (show all CPMK)
     */
    _fetchAndPopulateCpmk() {
        const routeCpmk = this._resolveRoute(
            "routeCpmk",
            "/bank-soal/rps/dosen/cpmk",
        );

        fetch(routeCpmk)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                if (this.cpmkTs) {
                    this.cpmkTs.clearOptions();
                    if (data && data.length > 0) {
                        this.cpmkTs.addOptions(data);
                        this.cpmkTs.enable();
                    } else {
                        this.cpmkTs.disable();
                    }
                }
            })
            .catch((err) => {
                console.error("Error fetching CPMK:", err);
                if (this.cpmkTs) {
                    this.cpmkTs.clearOptions();
                    this.cpmkTs.disable();
                }
                Snackbar?.show?.("Gagal memuat CPMK", "error");
            });
    }

    /**
     * Fetch and populate CPMK based on selected CPL(s)
     * If multiple CPL selected (e.g., 1, 2, 3), fetch CPMK for all (1.1, 1.2, 2.1, 2.2, 3.1, etc.)
     */
    _fetchAndPopulateCpmkByCpl(cplIds) {
        if (!cplIds || cplIds.length === 0) {
            this._fetchAndPopulateCpmk();
            return;
        }

        // Ensure cplIds is an array
        const ids = Array.isArray(cplIds) ? cplIds : [cplIds];

        // Build query string with array notation cpl_id[]=1&cpl_id[]=2
        const queryParams = ids
            .map((id) => `cpl_id[]=${encodeURIComponent(id)}`)
            .join("&");
        const routeCpmk = this._resolveRoute(
            "routeCpmk",
            "/bank-soal/rps/dosen/cpmk",
        );

        const url = `${routeCpmk}?${queryParams}`;

        console.log("Fetching CPMK from URL:", url, "CPL IDs:", ids);

        // Show loading state
        if (this.cpmkTs) {
            this._setPlaceholder(this.cpmkTs, "Memperbarui CPMK...");
        }

        fetch(url)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                console.log("CPMK data received:", data);
                if (this.cpmkTs) {
                    this.cpmkTs.clearOptions();
                    if (data && data.length > 0) {
                        this.cpmkTs.addOptions(data);
                        this.cpmkTs.enable();
                        this._setPlaceholder(this.cpmkTs, "");

                        if (
                            this.config.isEditForm &&
                            this.config.selectedCpmkIds?.length
                        ) {
                            this.cpmkTs.setValue(
                                this._normalizeSelectedIds(
                                    this.config.selectedCpmkIds,
                                ),
                            );
                        }
                    } else {
                        this.cpmkTs.disable();
                        this._setPlaceholder(
                            this.cpmkTs,
                            "Tidak ada CPMK untuk CPL ini",
                        );
                        Snackbar?.show?.(
                            "Tidak ada CPMK untuk CPL yang dipilih",
                            "info",
                        );
                    }
                }
            })
            .catch((err) => {
                console.error("Error fetching CPMK by CPL:", err);
                if (this.cpmkTs) {
                    this.cpmkTs.clearOptions();
                    this.cpmkTs.disable();
                    this._setPlaceholder(this.cpmkTs, "Gagal memuat CPMK");
                }
                Snackbar?.show?.("Gagal memuat CPMK", "error");
            });
    }

    /**
     * Populate CPMK for edit form (pre-load from RPS ID)
     */
    populateCpmkForEdit(rpsId) {
        if (!rpsId || !this.cpmkTs) return;

        const routeCpmkByRps = this._resolveRoute(
            "routeCpmkByRps",
            `/bank-soal/rps/dosen/cpmk-by-rps/${rpsId}`,
        );

        fetch(routeCpmkByRps)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                if (data && data.length > 0) {
                    this.cpmkTs.clearOptions();
                    this.cpmkTs.addOptions(data);
                    this.cpmkTs.enable();

                    // Set selected values
                    const selectedIds = data.map((c) => c.id.toString());
                    this.cpmkTs.setValue(selectedIds);
                }
            })
            .catch((err) => {
                console.error("Error fetching CPMK for edit:", err);
                Snackbar?.show?.("Gagal memuat CPMK yang dipilih", "error");
            });
    }

    /**
     * Populate pre-selected values for edit form
     */
    populateEditFormData(data) {
        // Populate Dosen
        if (data.selectedDosenIds && this.dosenTs) {
            // First add options
            if (data.dosenOptions) {
                this.dosenTs.clearOptions();
                this.dosenTs.addOptions(data.dosenOptions);
            }
            // Then set values
            this.dosenTs.setValue(
                data.selectedDosenIds.map((id) => id.toString()),
            );
        }

        // Populate CPL
        if (data.selectedCplIds && this.cplTs) {
            if (data.cplOptions) {
                this.cplTs.clearOptions();
                this.cplTs.addOptions(data.cplOptions);
            }
            this.cplTs.setValue(data.selectedCplIds.map((id) => id.toString()));
        }

        // Populate CPMK
        if (data.selectedCpmkIds && this.cpmkTs) {
            if (data.cpmkOptions) {
                this.cpmkTs.clearOptions();
                this.cpmkTs.addOptions(data.cpmkOptions);
            }
            this.cpmkTs.setValue(
                data.selectedCpmkIds.map((id) => id.toString()),
            );
        }
    }

    /**
     * Reset all multiselect dropdowns
     */
    _resetAllMultiselects() {
        if (this.dosenTs) {
            this.dosenTs.clearOptions();
            this.dosenTs.disable();
        }
        if (this.cplTs) {
            this.cplTs.clearOptions();
            this.cplTs.disable();
        }
        if (this.cpmkTs) {
            this.cpmkTs.clearOptions();
            this.cpmkTs.disable();
        }
    }

    /**
     * Get selected values for form submission
     */
    getFormData() {
        return {
            dosen_lain: this.dosenTs ? this.dosenTs.getValue() : [],
            cpl_id: this.cplTs ? this.cplTs.getValue() : [],
            cpmk_id: this.cpmkTs ? this.cpmkTs.getValue() : [],
        };
    }

    /**
     * Validate that required fields are selected
     */
    validate() {
        const errors = [];

        if (!this.cplTs || this.cplTs.getValue().length === 0) {
            errors.push("Minimal satu CPL harus dipilih");
        }

        if (!this.cpmkTs || this.cpmkTs.getValue().length === 0) {
            errors.push("Minimal satu CPMK harus dipilih");
        }

        return errors;
    }

    /**
     * Setup form validation on submit
     */
    _setupFormValidation() {
        const form = this.formElement || document.querySelector("form");
        if (!form) return;

        form.addEventListener("submit", (e) => {
            const errors = this.validate();
            if (errors.length > 0) {
                e.preventDefault();
                const errorMsg = errors
                    .map((error, index) => `${index + 1}. ${error}`)
                    .join("\n");
                Snackbar?.show?.(`Validasi Error:\n${errorMsg}`, "error");
            }
        });
    }
}

// Instance tunggal
const RpsMultiselect = new RpsMultiselectHandler();

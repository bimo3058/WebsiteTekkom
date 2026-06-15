class RpsCpmkRows {
    constructor(form) {
        this.form = form;
        this.rowContainer = form?.querySelector("[data-cpmk-rows]");
        this.template = form?.querySelector("#cpmkRowTemplate");
        this.addButton = form?.querySelector("#addCpmkRowBtn");
        this.mkSelect = form?.querySelector("#mkSelect");
        this.dosenSelect = form?.querySelector("#dosenSelect");
        this.dosenTs = null;
        this.routeCpl = form?.dataset?.routeCpl || "";
        this.routeDosen = form?.dataset?.routeDosen || "";
        this.cplOptions = [];
        this.dosenOptions = [];
        
        let maxIndex = -1;
        const existingRows = this.rowContainer ? this.rowContainer.querySelectorAll("[data-cpmk-row]") : [];
        existingRows.forEach(row => {
            const idx = parseInt(row.dataset.rowIndex, 10);
            if (!isNaN(idx) && idx > maxIndex) {
                maxIndex = idx;
            }
        });
        this.rowCounter = maxIndex + 1;
    }

    init() {
        if (!this.form || !this.rowContainer || !this.template) {
            return;
        }

        this.bindEvents();
        this.ensureAtLeastOneRow();
        this.loadLinkedData();
        this.refreshAllPreviews();
        this.updateCpmkFormState();

        window.BanksoalRpsUploadForm = this;
    }

    bindEvents() {
        if (this.addButton) {
            this.addButton.addEventListener("click", () => this.addRow());
        }

        if (this.mkSelect) {
            this.mkSelect.addEventListener("change", () => {
                this.loadLinkedData();
            });
        }

        this.rowContainer.addEventListener("click", (event) => {
            const removeButton = event.target.closest("[data-remove-cpmk-row]");
            if (!removeButton) {
                return;
            }

            const row = removeButton.closest("[data-cpmk-row]");
            this.removeRow(row);
        });

        this.rowContainer.addEventListener("input", (event) => {
            const row = event.target.closest("[data-cpmk-row]");
            if (row) {
                this.updatePreview(row);
                this.updateCpmkFormState();
            }
        });

        this.rowContainer.addEventListener("change", (event) => {
            const row = event.target.closest("[data-cpmk-row]");
            if (row) {
                this.updatePreview(row);
                this.updateCpmkFormState();
            }
        });
    }

    ensureAtLeastOneRow() {
        if (
            this.rowContainer.querySelectorAll("[data-cpmk-row]").length === 0
        ) {
            this.addRow();
        }
    }

    reset() {
        this.rowContainer.innerHTML = "";
        this.rowCounter = 0;
        this.ensureAtLeastOneRow();
        this.loadLinkedData();
    }

    async loadLinkedData() {
        await Promise.all([this.loadDosenOptions(), this.loadCplOptions()]);
    }

    async loadDosenOptions() {
        if (!this.dosenSelect || !this.routeDosen) {
            return;
        }

        const mkId = this.mkSelect?.value || "";
        const url = mkId
            ? `${this.routeDosen}?mk_id=${encodeURIComponent(String(mkId))}`
            : this.routeDosen;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            this.dosenOptions = Array.isArray(data) ? data : [];
            this.renderDosenOptions();
        } catch (error) {
            console.error("Error loading dosen options:", error);
        }
    }

    renderDosenOptions() {
        if (!this.dosenSelect) {
            return;
        }

        let selectedValues = Array.from(this.dosenSelect.selectedOptions).map(
            (option) => option.value,
        );
        
        if (this.dosenSelect.dataset.selectedDosenIds) {
            try {
                const preselected = JSON.parse(this.dosenSelect.dataset.selectedDosenIds);
                if (Array.isArray(preselected)) {
                    selectedValues = [...new Set([...selectedValues, ...preselected.map(String)])];
                }
            } catch (e) {
                console.warn('Failed to parse selectedDosenIds:', e);
            }
        }

        this.dosenSelect.innerHTML =
            '<option value="">Pilih dosen pengampu lain</option>';

        this.dosenOptions.forEach((item) => {
            const option = document.createElement("option");
            option.value = String(item.id);
            option.textContent = item.name;
            if (selectedValues.includes(option.value)) {
                option.selected = true;
            }
            this.dosenSelect.appendChild(option);
        });

        // Initialize TomSelect on dosen select for better UX (search + remove button)
        try {
            if (window.TomSelect) {
                if (this.dosenTs) {
                    try {
                        this.dosenTs.destroy();
                    } catch (e) {
                        /* ignore */
                    }
                    this.dosenTs = null;
                }

                this.dosenTs = new TomSelect(this.dosenSelect, {
                    plugins: { remove_button: { title: "Hapus dosen ini" } },
                    maxOptions: 100,
                    searchField: ["text"],
                    valueField: "value",
                    labelField: "text",
                    persist: false,
                    hideSelected: false,
                });
            }
        } catch (err) {
            console.warn("TomSelect init failed for dosenSelect:", err);
        }
    }

    async loadCplOptions() {
        if (!this.routeCpl) {
            return;
        }

        const mkId = this.mkSelect?.value || "";
        const url = mkId
            ? `${this.routeCpl}?mk_id=${encodeURIComponent(String(mkId))}`
            : this.routeCpl;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            this.cplOptions = Array.isArray(data) ? data : [];
            this.renderAllCplSelects();
            this.updateCpmkFormState();
        } catch (error) {
            console.error("Error loading CPL options:", error);
        }
    }

    renderAllCplSelects() {
        const rows = this.rowContainer.querySelectorAll("[data-cpmk-row]");
        rows.forEach((row) => this.renderCplSelect(row));
        this.updateCpmkFormState();
    }

    renderCplSelect(row) {
        const select = row.querySelector("[data-cpmk-cpl-select]");
        if (!select) {
            return;
        }

        const selectedValue =
            select.dataset.selectedValue || select.value || "";
        const previousSelected = selectedValue ? String(selectedValue) : "";

        select.innerHTML = '<option value="">Pilih CPL</option>';

        this.cplOptions.forEach((item) => {
            const option = document.createElement("option");
            option.value = String(item.id);
            // Show only the CPL code to keep dropdown compact
            option.textContent = item.kode;
            if (String(option.value) === previousSelected) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        const hasOptions = this.cplOptions.length > 0;
        select.disabled = !hasOptions;
        if (previousSelected) {
            select.value = previousSelected;
            if (select.value === previousSelected) {
                select.dataset.selectedValue = previousSelected;
            } else {
                delete select.dataset.selectedValue;
            }
        } else {
            delete select.dataset.selectedValue;
        }
    }

    addRow(initialValues = {}) {
        const index = this.rowCounter++;
        const markup = this.template.innerHTML.replaceAll(
            "__INDEX__",
            String(index),
        );
        const wrapper = document.createElement("div");
        wrapper.innerHTML = markup.trim();
        const row = wrapper.firstElementChild;

        if (!row) {
            return;
        }

        this.rowContainer.appendChild(row);
        this.applyInitialValues(row, initialValues);
        this.renderCplSelect(row);
        this.updatePreview(row);
        this.updateCpmkFormState();

        return row;
    }

    applyInitialValues(row, values = {}) {
        const cplSelect = row.querySelector("[data-cpmk-cpl-select]");
        const kkoSelect = row.querySelector('select[name*="[kko]"]');
        const kodeInput = row.querySelector('input[name*="[kode]"]');
        const objekInput = row.querySelector('input[name*="[objek]"]');
        const konteksInput = row.querySelector('input[name*="[konteks]"]');

        if (cplSelect && values.cpl_id) {
            cplSelect.dataset.selectedValue = String(values.cpl_id);
        }

        if (kkoSelect && values.kko) {
            kkoSelect.value = values.kko;
        }

        if (kodeInput && values.kode) {
            kodeInput.value = values.kode;
        }

        if (objekInput && values.objek) {
            objekInput.value = values.objek;
        }

        if (konteksInput && values.konteks) {
            konteksInput.value = values.konteks;
        }
    }

    removeRow(row) {
        if (!row) {
            return;
        }

        const rows = this.rowContainer.querySelectorAll("[data-cpmk-row]");
        if (rows.length === 1) {
            row.querySelectorAll("input, select").forEach((element) => {
                if (element.tagName === "SELECT") {
                    element.selectedIndex = 0;
                    if (element.dataset.selectedValue) {
                        delete element.dataset.selectedValue;
                    }
                } else {
                    element.value = "";
                }
            });
            this.updatePreview(row);
            this.updateCpmkFormState();
            return;
        }

        row.remove();
        this.refreshAllPreviews();
        this.updateCpmkFormState();
    }

    refreshAllPreviews() {
        const rows = this.rowContainer.querySelectorAll("[data-cpmk-row]");
        rows.forEach((row) => this.updatePreview(row));
    }

    updatePreview(row) {
        const preview = row.querySelector("[data-cpmk-preview]");
        if (!preview) {
            return;
        }

        const kode = this.cleanValue(
            row.querySelector('input[name*="[kode]"]')?.value,
        );
        const kko = this.cleanValue(
            row.querySelector('select[name*="[kko]"]')?.value,
        );
        const objek = this.cleanValue(
            row.querySelector('input[name*="[objek]"]')?.value,
        );
        const konteks = this.cleanValue(
            row.querySelector('input[name*="[konteks]"]')?.value,
        );
        const kkoLabel = this.getKkoLabel(kko);

        if (!kode && !kko && !objek && !konteks) {
            preview.textContent =
                "Pratinjau CPMK akan muncul setelah field diisi.";
            return;
        }

        const parts = [
            `CPMK ${kode || "..."}`,
            "-",
            "Mahasiswa mampu",
            kkoLabel ? `(KKO ${kkoLabel})` : "(KKO ...)",
            objek || "...",
        ];

        if (konteks) {
            parts.push(konteks);
        }

        preview.textContent = parts.join(" ");
    }

    getKkoLabel(kkoValue) {
        const mapping = {
            C1: "Mengingat",
            C2: "Memahami",
            C3: "Menerapkan",
            C4: "Menganalisis",
            C5: "Mengevaluasi",
            C6: "Mencipta",
            P1: "Meniru",
            P2: "Menyesuaikan",
            P3: "Membiasakan",
            P4: "Menguasai",
            P5: "Mahir",
            A1: "Menerima",
            A2: "Merespon",
            A3: "Menilai",
            A4: "Mengorganisasi",
            A5: "Menghayati",
            P: "Praktik",
            A: "Afektif",
        };

        return mapping[kkoValue] || "";
    }

    cleanValue(value) {
        return String(value || "").trim();
    }

    updateCpmkFormState() {
        const hasMk = !!(this.mkSelect?.value);
        
        let allRequiredFilled = true;
        const rows = this.rowContainer ? this.rowContainer.querySelectorAll("[data-cpmk-row]") : [];
        
        rows.forEach(row => {
            const cplSelect = row.querySelector("[data-cpmk-cpl-select]");
            const kodeInput = row.querySelector('input[name*="[kode]"]');
            const kkoSelect = row.querySelector('select[name*="[kko]"]');
            const objekInput = row.querySelector('input[name*="[objek]"]');
            const konteksInput = row.querySelector('input[name*="[konteks]"]');
            const removeBtn = row.querySelector("[data-remove-cpmk-row]");
            
            if (!hasMk) {
                if (cplSelect) {
                    cplSelect.disabled = true;
                    cplSelect.title = "Pilih Mata Kuliah terlebih dahulu";
                }
                if (kodeInput) {
                    kodeInput.disabled = true;
                    kodeInput.title = "Pilih Mata Kuliah terlebih dahulu";
                }
                if (kkoSelect) {
                    kkoSelect.disabled = true;
                    kkoSelect.title = "Pilih Mata Kuliah terlebih dahulu";
                }
                if (objekInput) {
                    objekInput.disabled = true;
                    objekInput.title = "Pilih Mata Kuliah terlebih dahulu";
                }
                if (konteksInput) {
                    konteksInput.disabled = true;
                    konteksInput.title = "Pilih Mata Kuliah terlebih dahulu";
                }
                if (removeBtn) {
                    removeBtn.disabled = true;
                    removeBtn.title = "Pilih Mata Kuliah terlebih dahulu";
                    removeBtn.style.opacity = "0.5";
                    removeBtn.style.cursor = "not-allowed";
                }
                allRequiredFilled = false;
            } else {
                if (cplSelect) {
                    cplSelect.disabled = !this.cplOptions.length;
                    cplSelect.title = cplSelect.disabled 
                        ? "Tidak ada CPL yang terpetakan dengan Mata Kuliah ini" 
                        : "Pilih CPL";
                }
                if (kodeInput) {
                    kodeInput.disabled = false;
                    kodeInput.title = "";
                }
                if (kkoSelect) {
                    kkoSelect.disabled = false;
                    kkoSelect.title = "";
                }
                if (objekInput) {
                    objekInput.disabled = false;
                    objekInput.title = "";
                }
                if (konteksInput) {
                    konteksInput.disabled = false;
                    konteksInput.title = "";
                }
                if (removeBtn) {
                    removeBtn.disabled = false;
                    removeBtn.title = "Hapus baris CPMK ini";
                    removeBtn.style.opacity = "";
                    removeBtn.style.cursor = "";
                }

                // Check required values
                const isCplFilled = cplSelect ? !!cplSelect.value : false;
                const isKodeFilled = kodeInput ? !!kodeInput.value.trim() : false;
                const isKkoFilled = kkoSelect ? !!kkoSelect.value : false;
                const isObjekFilled = objekInput ? !!objekInput.value.trim() : false;

                if (!isCplFilled || !isKodeFilled || !isKkoFilled || !isObjekFilled) {
                    allRequiredFilled = false;
                }
            }
        });

        if (this.addButton) {
            if (!hasMk) {
                this.addButton.title = "Pilih Mata Kuliah terlebih dahulu";
            } else if (!allRequiredFilled) {
                this.addButton.title = "Lengkapi seluruh kolom CPMK yang ada terlebih dahulu";
            } else {
                this.addButton.title = "Tambah baris CPMK baru";
            }
            this.addButton.disabled = !(hasMk && allRequiredFilled);
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector('form[data-cpmk-row-builder="1"]');
    if (!form) {
        return;
    }

    const builder = new RpsCpmkRows(form);
    builder.init();
});

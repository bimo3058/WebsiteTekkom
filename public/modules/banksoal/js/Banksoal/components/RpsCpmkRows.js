class RpsCpmkRows {
    constructor(form) {
        console.log("RpsCpmkRows constructor initialized. Form element:", form);
        this.form = form;
        // Collect ALL [data-cpmk-rows] containers inside the form (supports both upload + generator panels)
        const allContainers = form ? Array.from(form.querySelectorAll("[data-cpmk-rows]")) : Array.from(document.querySelectorAll("[data-cpmk-rows]"));
        this.rowContainers = allContainers; // all containers
        this.rowContainer = allContainers[0] || null; // primary (for addRow / template fallback)
        this.template = form?.querySelector("#cpmkRowTemplate") || document.querySelector("#cpmkRowTemplate");
        this.addButton = form?.querySelector("#addCpmkRowBtn") || document.querySelector("#addCpmkRowBtn");
        this.addButtonGenerator = form?.querySelector("#addCpmkRowBtnGenerator") || document.querySelector("#addCpmkRowBtnGenerator");
        this.mkSelect = form?.querySelector("#mkSelect") || document.querySelector("#mkSelect");
        this.dosenSelect = form?.querySelector("#dosenSelect") || document.querySelector("#dosenSelect");
        this.dosenTs = null;
        this.routeCpl = form?.dataset?.routeCpl || "";
        this.routeDosen = form?.dataset?.routeDosen || "";
        this.cplOptions = [];
        this.dosenOptions = [];

        console.log("RpsCpmkRows rowContainers:", this.rowContainers, "template:", this.template);
        console.log("RpsCpmkRows config - routeCpl:", this.routeCpl, "routeDosen:", this.routeDosen);

        let maxIndex = -1;
        this.rowContainers.forEach(container => {
            container.querySelectorAll("[data-cpmk-row]").forEach(row => {
                const idx = parseInt(row.dataset.rowIndex, 10);
                if (!isNaN(idx) && idx > maxIndex) {
                    maxIndex = idx;
                }
            });
        });
        this.rowCounter = maxIndex + 1;
    }

    init() {
        console.log("RpsCpmkRows init() executing.");
        if (!this.form) {
            console.warn("RpsCpmkRows missing required form element, aborting init.");
            return;
        }

        this.bindEvents();

        if (this.rowContainers.length > 0 && this.template) {
            this.ensureAtLeastOneRow();
            this.refreshAllPreviews();
            this.updateCpmkFormState();
        }

        this.loadLinkedData();

        window.BanksoalRpsUploadForm = this;
        console.log("RpsCpmkRows init() complete.");
    }

    bindEvents() {
        if (this.addButton) {
            this.addButton.addEventListener("click", () => this.addRow(this.rowContainers[0]));
        }

        if (this.addButtonGenerator) {
            this.addButtonGenerator.addEventListener("click", () => {
                const genContainer = this.form?.querySelector("#cpmkRowsGenerator") || document.querySelector("#cpmkRowsGenerator");
                this.addRow(genContainer || this.rowContainers[0]);
            });
        }

        if (this.mkSelect) {
            this.mkSelect.addEventListener("change", () => {
                this.loadLinkedData();
            });
        }

        this.rowContainers.forEach(container => {
            container.addEventListener("click", (event) => {
                const removeButton = event.target.closest("[data-remove-cpmk-row]");
                if (!removeButton) {
                    return;
                }

                const row = removeButton.closest("[data-cpmk-row]");
                this.removeRow(row);
            });

            container.addEventListener("input", (event) => {
                const row = event.target.closest("[data-cpmk-row]");
                if (row) {
                    this.updatePreview(row);
                    this.updateCpmkFormState();
                }
            });

            container.addEventListener("change", (event) => {
                const row = event.target.closest("[data-cpmk-row]");
                if (row) {
                    this.updatePreview(row);
                    this.updateCpmkFormState();
                }
            });
        });
    }

    ensureAtLeastOneRow() {
        // Only add a default row if the active (primary) container has no rows
        if (
            this.rowContainer && this.rowContainer.querySelectorAll("[data-cpmk-row]").length === 0
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
        if (window.showLoader) window.showLoader();
        try {
            await Promise.all([this.loadDosenOptions(), this.loadCplOptions()]);
        } finally {
            if (window.hideLoader) window.hideLoader();
        }
    }

    async loadDosenOptions() {
        console.log("loadDosenOptions starting. routeDosen:", this.routeDosen, "dosenSelect:", this.dosenSelect);
        if (!this.dosenSelect || !this.routeDosen) {
            console.warn("loadDosenOptions early return: missing select or route");
            return;
        }

        const mkId = this.mkSelect?.value || "";
        const url = mkId
            ? `${this.routeDosen}?mk_id=${encodeURIComponent(String(mkId))}`
            : this.routeDosen;
        console.log("loadDosenOptions fetching from URL:", url);

        try {
            const response = await fetch(url);
            console.log("loadDosenOptions response status:", response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            console.log("loadDosenOptions data fetched length:", data?.length);
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

        const selectedDosenData = this.dosenSelect.dataset.selectedDosenIds || this.form?.dataset?.selectedDosenIds;
        if (selectedDosenData) {
            try {
                const preselected = JSON.parse(selectedDosenData);
                if (Array.isArray(preselected)) {
                    selectedValues = [...new Set([...selectedValues, ...preselected.map(String)])];
                }
            } catch (e) {
                console.warn('Failed to parse selectedDosenIds:', e);
            }
        }

        const isEditPage = !!selectedDosenData || this.dosenSelect.hasAttribute('data-selected-dosen-ids') || (this.form && (this.form.hasAttribute('data-edit-mode') || this.form.dataset.editMode === "1"));

        console.log("renderDosenOptions starting. Options count:", this.dosenOptions.length);

        // Destroy existing TomSelect instance first to prevent HTML reset overwriting new options
        try {
            if (window.TomSelect) {
                const existingTs = this.dosenSelect.tomselect || this.dosenTs;
                if (existingTs) {
                    try {
                        existingTs.destroy();
                    } catch (e) {
                        console.warn("Failed to destroy TomSelect instance:", e);
                    }
                    this.dosenTs = null;
                }
            }
        } catch (err) {
            console.warn("TomSelect destroy failed:", err);
        }

        this.dosenSelect.innerHTML =
            '<option value="">Pilih dosen pengampu lain</option>';

        this.dosenOptions.forEach((item) => {
            const option = document.createElement("option");
            option.value = String(item.id);
            option.textContent = item.name;
            if (selectedValues.includes(option.value) || (!isEditPage && item.is_pengampu)) {
                option.selected = true;
            }
            this.dosenSelect.appendChild(option);
        });

        // Initialize TomSelect with "X Terpilih" summary and auto-sorting of checked items to top
        try {
            if (window.TomSelect) {
                this.dosenTs = new TomSelect(this.dosenSelect, {
                    plugins: { remove_button: { title: "Hapus dosen ini" } },
                    maxOptions: 100,
                    searchField: ["text"],
                    persist: false,
                    hideSelected: false,
                    render: {
                        item: function(data, escape) {
                            return `<div class="item summary-item" data-value="${escape(data.value)}">${escape(data.text)}</div>`;
                        },
                        option: function(data, escape) {
                            return `<div class="option py-2 px-3">${escape(data.text)}</div>`;
                        }
                    }
                });

                if (typeof this.dosenTs.hook === 'function') {
                    const orig_dosen_onOptionSelect = this.dosenTs.onOptionSelect;
                    this.dosenTs.hook('instead', 'onOptionSelect', (evt, option) => {
                        if (!option && evt && evt.target) {
                            option = evt.target.closest('.option');
                        }
                        if (!option) return;
                        const val = option.dataset?.value || option.getAttribute('data-value');
                        if (option.classList.contains('selected') || (val && this.dosenTs.items.includes(String(val)))) {
                            option.classList.remove('selected');
                            if (val) this.dosenTs.removeItem(val);
                            this.dosenTs.refreshOptions(false);
                            if (evt && evt.preventDefault) evt.preventDefault();
                            return;
                        }
                        orig_dosen_onOptionSelect.call(this.dosenTs, evt, option);
                    });
                }

                const updateDisplayAndSort = () => {
                    if (!this.dosenTs) return;
                    const count = this.dosenTs.items.length;

                    // 1. Re-sort options in dropdown so selected items move to the top
                    const allOptions = Object.values(this.dosenTs.options);
                    allOptions.forEach(opt => {
                        opt.$is_selected = this.dosenTs.items.includes(String(opt.value));
                    });
                    allOptions.sort((a, b) => {
                        if (a.$is_selected && !b.$is_selected) return -1;
                        if (!a.$is_selected && b.$is_selected) return 1;
                        return (a.$order || 0) - (b.$order || 0);
                    });

                    // 2. Format items inside control box to show "X Terpilih"
                    const control = this.dosenTs.control;
                    if (control) {
                        const itemEls = control.querySelectorAll('.item');
                        itemEls.forEach((el, index) => {
                            // Find the remove button injected by TomSelect remove_button plugin
                            const removeBtn = el.querySelector('.remove');
                            if (index === 0) {
                                // Update only the text node, preserve the remove button element
                                const textNode = Array.from(el.childNodes).find(n => n.nodeType === Node.TEXT_NODE);
                                if (textNode) {
                                    textNode.textContent = `${count} Terpilih`;
                                } else {
                                    // No text node yet — prepend one
                                    el.insertBefore(document.createTextNode(`${count} Terpilih`), el.firstChild);
                                }
                                el.style.display = 'inline-flex';
                                el.style.alignItems = 'center';
                                el.style.backgroundColor = '#f1f5f9';
                                el.style.color = '#0b266e';
                                el.style.fontWeight = '600';
                                el.style.border = '1px solid #cbd5e1';
                                el.style.borderRadius = '6px';
                                el.style.padding = '2px 8px';
                                el.style.fontSize = '12px';
                                if (removeBtn) removeBtn.style.display = '';
                            } else {
                                el.style.display = 'none';
                                if (removeBtn) removeBtn.style.display = 'none';
                            }
                        });
                    }
                };

                this.dosenTs.on('change', () => {
                    updateDisplayAndSort();
                    if (this.dosenTs) {
                        this.dosenTs.refreshOptions(false);
                    }
                });

                this.dosenTs.on('item_add', updateDisplayAndSort);
                this.dosenTs.on('item_remove', updateDisplayAndSort);

                updateDisplayAndSort();
            }
        } catch (err) {
            console.warn("TomSelect init failed for dosenSelect:", err);
        }
    }

    async loadCplOptions() {
        if (!this.routeCpl) {
            return;
        }

        // Set loading placeholder in ALL containers
        this.rowContainers.forEach(container => {
            container.querySelectorAll("[data-cpmk-cpl-select]").forEach(select => {
                select.innerHTML = '<option value="">Memuat CPL</option>';
                select.disabled = true;
            });
        });

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
        this.renderCplCheckboxes();
        if (!this.rowContainers || this.rowContainers.length === 0) return;
        this.rowContainers.forEach(container => {
            container.querySelectorAll("[data-cpmk-row]").forEach(row => this.renderCplSelect(row));
        });
        this.updateCpmkFormState();
    }

    renderCplCheckboxes() {
        const cplContainer = document.getElementById('cpl_checkbox_container');
        if (!cplContainer) return;
        cplContainer.innerHTML = '';
        if (!this.cplOptions || this.cplOptions.length === 0) {
            cplContainer.innerHTML = '<p class="field-hint text-rose-500">Mata kuliah ini belum dipetakan ke CPL manapun.</p>';
            return;
        }
        this.cplOptions.forEach(cpl => {
            const item = document.createElement('label');
            item.style.display = 'flex';
            item.style.alignItems = 'flex-start';
            item.style.gap = '10px';
            item.style.fontSize = '13px';
            item.style.cursor = 'pointer';
            item.innerHTML = `
                <input type="checkbox" name="cpl_ids[]" class="cpl-checkbox-item" value="${cpl.id}" style="margin-top: 3px; accent-color: var(--primary-blue);" data-code="${cpl.kode}">
                <div><strong>${cpl.kode}</strong>: ${cpl.deskripsi}</div>
            `;
            cplContainer.appendChild(item);
        });
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

    addRow(targetContainer = null, initialValues = {}) {
        const container = targetContainer || this.rowContainer;
        if (!container || !this.template) {
            return;
        }
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

        container.appendChild(row);
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

        // Find which container this row belongs to
        const container = row.closest("[data-cpmk-rows]");
        if (!container) return;

        const rows = container.querySelectorAll("[data-cpmk-row]");
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
        this.rowContainers.forEach(container => {
            container.querySelectorAll("[data-cpmk-row]").forEach(row => this.updatePreview(row));
        });
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
        const creationMethodEl = document.getElementById('creation_method_input');
        const creationMethod = creationMethodEl ? creationMethodEl.value : 'upload';

        const isUploadActive = (creationMethod === 'upload');
        const isEditMode = !!(this.form && (this.form.hasAttribute('data-edit-mode') || this.form.dataset.editMode === "1"));
        const hasMk = !!(this.mkSelect?.value);

        let allRequiredFilled = true;
        // Collect rows from ALL containers
        const rows = [];
        this.rowContainers.forEach(container => {
            container.querySelectorAll("[data-cpmk-row]").forEach(r => rows.push(r));
        });

        rows.forEach(row => {
            const cplSelect = row.querySelector("[data-cpmk-cpl-select]");
            const kodeInput = row.querySelector('input[name*="[kode]"]');
            const kkoSelect = row.querySelector('select[name*="[kko]"]');
            const objekInput = row.querySelector('input[name*="[objek]"]');
            const konteksInput = row.querySelector('input[name*="[konteks]"]');
            const removeBtn = row.querySelector("[data-remove-cpmk-row]");

            if ((!isUploadActive && !isEditMode) || !hasMk) {
                if (cplSelect) {
                    cplSelect.disabled = true;
                    cplSelect.title = (!isUploadActive && !isEditMode) ? "" : "Pilih Mata Kuliah terlebih dahulu";
                }
                if (kodeInput) {
                    kodeInput.disabled = true;
                    kodeInput.title = (!isUploadActive && !isEditMode) ? "" : "Pilih Mata Kuliah terlebih dahulu";
                }
                if (kkoSelect) {
                    kkoSelect.disabled = true;
                    kkoSelect.title = (!isUploadActive && !isEditMode) ? "" : "Pilih Mata Kuliah terlebih dahulu";
                }
                if (objekInput) {
                    objekInput.disabled = true;
                    objekInput.title = (!isUploadActive && !isEditMode) ? "" : "Pilih Mata Kuliah terlebih dahulu";
                }
                if (konteksInput) {
                    konteksInput.disabled = true;
                    konteksInput.title = (!isUploadActive && !isEditMode) ? "" : "Pilih Mata Kuliah terlebih dahulu";
                }
                if (removeBtn) {
                    removeBtn.disabled = true;
                    removeBtn.title = (!isUploadActive && !isEditMode) ? "" : "Pilih Mata Kuliah terlebih dahulu";
                    removeBtn.style.opacity = "0.5";
                    removeBtn.style.cursor = "not-allowed";
                }
                allRequiredFilled = false;
            } else {
                const isCplSelected = cplSelect ? !!cplSelect.value : false;

                if (cplSelect) {
                    cplSelect.disabled = !this.cplOptions.length;
                    cplSelect.title = cplSelect.disabled
                        ? "Tidak ada CPL yang terpetakan dengan Mata Kuliah ini"
                        : "Pilih CPL";
                }
                if (kodeInput) {
                    kodeInput.disabled = !isCplSelected;
                    kodeInput.title = !isCplSelected ? "Pilih CPL terlebih dahulu" : "";
                }
                if (kkoSelect) {
                    kkoSelect.disabled = !isCplSelected;
                    kkoSelect.title = !isCplSelected ? "Pilih CPL terlebih dahulu" : "";
                }
                if (objekInput) {
                    objekInput.disabled = !isCplSelected;
                    objekInput.title = !isCplSelected ? "Pilih CPL terlebih dahulu" : "";
                }
                if (konteksInput) {
                    konteksInput.disabled = !isCplSelected;
                    konteksInput.title = !isCplSelected ? "Pilih CPL terlebih dahulu" : "";
                }
                if (removeBtn) {
                    if (rows.length < 2) {
                        removeBtn.style.display = "none";
                    } else {
                        removeBtn.style.display = ""; // default display
                        removeBtn.disabled = false;
                        removeBtn.title = "Hapus baris CPMK ini";
                        removeBtn.style.opacity = "";
                        removeBtn.style.cursor = "";
                    }
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

        const buttonsToUpdate = [this.addButton, this.addButtonGenerator].filter(Boolean);
        buttonsToUpdate.forEach(btn => {
            if (!isUploadActive && !isEditMode) {
                btn.disabled = true;
                btn.title = "";
            } else if (!hasMk) {
                btn.title = "Pilih Mata Kuliah terlebih dahulu";
                btn.disabled = true;
            } else if (!allRequiredFilled) {
                btn.title = "Lengkapi seluruh kolom CPMK yang ada terlebih dahulu";
                btn.disabled = true;
            } else {
                btn.title = "Tambah baris CPMK baru";
                btn.disabled = false;
            }
        });
    }
}

function initRpsCpmk() {
    const form = document.querySelector('[data-cpmk-row-builder="1"]');
    if (!form) {
        return;
    }

    const builder = new RpsCpmkRows(form);
    builder.init();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initRpsCpmk);
} else {
    initRpsCpmk();
}

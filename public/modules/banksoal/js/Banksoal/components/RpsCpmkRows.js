class RpsCpmkRows {
    constructor(form) {
        this.form = form;
        const allContainers = Array.from(document.querySelectorAll("[data-cpmk-rows], #cpmkRows, #cpmkRowsGenerator"));
        this.rowContainers = allContainers;
        this.rowContainer = allContainers[0] || null;
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
        if (!this.form) {
            return;
        }

        this.bindEvents();

        if (this.rowContainers.length > 0 && this.template) {
            this.ensureAtLeastOneRow();
            this.refreshAllPreviews();
            this.updateCpmkFormState();
            this.validateDuplicates();
        }

        window.BanksoalRpsUploadForm = this;
    }

    bindEvents() {
        if (this._eventsBound) return;
        this._eventsBound = true;

        if (this.addButton) {
            this.addButton.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                const uploadContainer = document.getElementById("cpmkRows") || this.rowContainers[0];
                this.addRow(uploadContainer);
            };
        }

        if (this.addButtonGenerator) {
            this.addButtonGenerator.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                const genContainer = document.getElementById("cpmkRowsGenerator") || this.rowContainers[1] || this.rowContainers[0];
                this.addRow(genContainer);
            };
        }

        this.rowContainers.forEach(container => {
            container.onclick = (event) => {
                const removeButton = event.target.closest("[data-remove-cpmk-row]");
                if (!removeButton) {
                    return;
                }
                event.preventDefault();
                event.stopPropagation();
                const row = removeButton.closest("[data-cpmk-row]");
                if (row) {
                    this.removeRow(row);
                }
            };

            container.oninput = (event) => {
                const row = event.target.closest("[data-cpmk-row]");
                if (row) {
                    this.updatePreview(row);
                    this.updateCpmkFormState();
                    this.validateDuplicates(container);
                }
            };

            container.onchange = (event) => {
                const row = event.target.closest("[data-cpmk-row]");
                if (row) {
                    const target = event.target;
                    if (target.matches('[data-cpmk-cpl-select], .cpl-select-input')) {
                        const kodeIn = row.querySelector('input[name*="[kode]"]');
                        if (kodeIn && !kodeIn.value.trim()) {
                            const nextVal = this.getNextKodeForCpl(container, target.value);
                            if (nextVal) {
                                kodeIn.value = nextVal;
                            }
                        }
                    }
                    this.updatePreview(row);
                    this.updateCpmkFormState();
                    this.validateDuplicates(container);
                }
            };
        });
    }

    ensureAtLeastOneRow() {
        if (
            this.rowContainer && this.rowContainer.querySelectorAll("[data-cpmk-row]").length === 0
        ) {
            this.addRow();
        }
    }

    reset() {
        if (this.rowContainer) {
            this.rowContainer.innerHTML = "";
        }
        this.rowCounter = 0;
        this.ensureAtLeastOneRow();
    }

    getCplList() {
        if (this.cplOptions && this.cplOptions.length > 0) {
            return this.cplOptions;
        }
        if (window.currentCplsList && window.currentCplsList.length > 0) {
            return window.currentCplsList;
        }
        const checkboxes = document.querySelectorAll('#cpl_checkbox_container input.cpl-checkbox-item, input[name="cpl_ids[]"]');
        if (checkboxes.length > 0) {
            const list = [];
            checkboxes.forEach(cb => {
                const code = cb.dataset.code || cb.closest('label')?.querySelector('strong')?.textContent?.trim() || cb.value;
                list.push({ id: cb.value, kode: code });
            });
            if (list.length > 0) return list;
        }
        return [];
    }

    renderAllCplSelects() {
        if (!this.rowContainers || this.rowContainers.length === 0) return;
        this.rowContainers.forEach(container => {
            container.querySelectorAll("[data-cpmk-row]").forEach(row => this.renderCplSelect(row));
        });
        this.updateCpmkFormState();
        this.validateDuplicates();
    }

    renderCplSelect(row) {
        const select = row.querySelector("[data-cpmk-cpl-select], .cpl-select-input");
        if (!select) {
            return;
        }

        const selectedValue =
            select.dataset.selectedValue || select.value || "";
        const previousSelected = selectedValue ? String(selectedValue) : "";

        select.innerHTML = '<option value="">Pilih CPL</option>';

        const cpls = this.getCplList();
        cpls.forEach((item) => {
            const option = document.createElement("option");
            option.value = String(item.id);
            option.textContent = item.kode;
            if (String(option.value) === previousSelected) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        const hasOptions = cpls.length > 0;
        select.disabled = !hasOptions;
        if (previousSelected) {
            select.value = previousSelected;
            if (select.value === previousSelected) {
                select.dataset.selectedValue = previousSelected;
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
        this.validateDuplicates(container);

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

        const container = row.closest("[data-cpmk-rows], #cpmkRows, #cpmkRowsGenerator");
        if (!container) return;

        const rows = container.querySelectorAll("[data-cpmk-row]");
        if (rows.length <= 1) {
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
            this.validateDuplicates(container);
            return;
        }

        row.remove();
        this.refreshAllPreviews();
        this.updateCpmkFormState();
        this.validateDuplicates(container);
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

        // Identitas CPMK mengikuti nomor CPL: CPMK {nomor CPL}.{kode baris}
        const cplSelect = row.querySelector("[data-cpmk-cpl-select], .cpl-select-input");
        const cplText =
            cplSelect && cplSelect.selectedIndex >= 0 && cplSelect.options[cplSelect.selectedIndex]
                ? cplSelect.options[cplSelect.selectedIndex].textContent.trim()
                : "";
        const cplMatch = cplText.match(/\d+/);
        const cplNum = cplMatch ? String(parseInt(cplMatch[0], 10)) : "";
        const kodeLabel = cplNum && kode ? `${cplNum}.${kode}` : kode || "...";

        const parts = [
            `CPMK ${kodeLabel}`,
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
        const hasMk = !!(this.mkSelect?.value || document.getElementById('mkSelect')?.value);

        let allRequiredFilled = true;
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
                const isCplSelected = cplSelect
                    ? !!(cplSelect.value || cplSelect.dataset.selectedValue)
                    : false;

                if (cplSelect) {
                    const cpls = this.getCplList();
                    const cplHasStoredValue = !!(cplSelect.value || cplSelect.dataset.selectedValue);
                    cplSelect.disabled = !cpls.length && !cplHasStoredValue;
                    cplSelect.title = cplSelect.disabled
                        ? "Tidak ada CPL yang terpetakan dengan Mata Kuliah ini"
                        : "Pilih CPL";
                }
                // Baris CPMK pada form upload dokumen dibiarkan terbuka:
                // field kode/KKO/objek/konteks bisa diisi tanpa memilih CPL dulu.
                const isUploadRow = !row.closest("#cpmkRowsGenerator");
                const gateByCpl = !isUploadRow;
                const lockByCpl = gateByCpl && !isCplSelected;
                const cplHint = lockByCpl ? "Pilih CPL terlebih dahulu" : "";

                if (kodeInput) {
                    kodeInput.disabled = lockByCpl;
                    kodeInput.title = cplHint;
                }
                if (kkoSelect) {
                    kkoSelect.disabled = lockByCpl;
                    kkoSelect.title = cplHint;
                }
                if (objekInput) {
                    objekInput.disabled = lockByCpl;
                    objekInput.title = cplHint;
                }
                if (konteksInput) {
                    konteksInput.disabled = lockByCpl;
                    konteksInput.title = cplHint;
                }
                if (removeBtn) {
                    const parentContainer = row.closest('[data-cpmk-rows], #cpmkRows, #cpmkRowsGenerator');
                    const siblingRows = parentContainer ? parentContainer.querySelectorAll("[data-cpmk-row]") : rows;
                    if (siblingRows.length < 2) {
                        removeBtn.style.display = "none";
                    } else {
                        removeBtn.style.display = "";
                        removeBtn.disabled = false;
                        removeBtn.title = "Hapus baris CPMK ini";
                        removeBtn.style.opacity = "";
                        removeBtn.style.cursor = "";
                    }
                }

                const isCplFilled = cplSelect
                    ? !!(cplSelect.value || cplSelect.dataset.selectedValue)
                    : false;
                const isKodeFilled = kodeInput ? !!kodeInput.value.trim() : false;
                const isKkoFilled = kkoSelect ? !!kkoSelect.value : false;
                const isObjekFilled = objekInput ? !!objekInput.value.trim() : false;

                if (!isCplFilled || !isKodeFilled || !isKkoFilled || !isObjekFilled) {
                    allRequiredFilled = false;
                }
            }
        });

        const buttonsToUpdate = [
            this.addButton,
            this.addButtonGenerator,
            document.getElementById('addCpmkRowBtn'),
            document.getElementById('addCpmkRowBtnGenerator')
        ].filter(Boolean);
        const uniqueButtons = [...new Set(buttonsToUpdate)];

        uniqueButtons.forEach(btn => {
            btn.title = "Tambah baris CPMK baru";
            btn.disabled = false;
        });
    }

    getNextKodeForCpl(container, cplVal) {
        if (!container || !cplVal) return "";
        const rows = container.querySelectorAll("[data-cpmk-row]");
        let maxNum = 0;
        rows.forEach(r => {
            const sel = r.querySelector("[data-cpmk-cpl-select], .cpl-select-input");
            const cVal = sel ? (sel.value || sel.dataset.selectedValue || "") : "";
            if (String(cVal) === String(cplVal)) {
                const kodeIn = r.querySelector('input[name*="[kode]"]');
                const val = parseInt(kodeIn?.value?.trim(), 10);
                if (!isNaN(val) && val > maxNum) {
                    maxNum = val;
                }
            }
        });
        return maxNum > 0 ? String(maxNum + 1) : "1";
    }

    validateDuplicates(targetContainer = null) {
        const containers = targetContainer ? [targetContainer] : this.rowContainers;
        let isOverallValid = true;

        containers.forEach(container => {
            if (!container) return;
            const rows = Array.from(container.querySelectorAll("[data-cpmk-row]"));

            // Clear previous duplicate errors
            rows.forEach(row => {
                const cplSelect = row.querySelector("[data-cpmk-cpl-select], .cpl-select-input");
                const kodeInput = row.querySelector('input[name*="[kode]"]');
                const warns = row.querySelectorAll('.cpmk-duplicate-warning');
                warns.forEach(w => w.remove());

                if (cplSelect) {
                    cplSelect.style.borderColor = "";
                    cplSelect.style.backgroundColor = "";
                    cplSelect.classList.remove("is-invalid");
                }
                if (kodeInput) {
                    kodeInput.style.borderColor = "";
                    kodeInput.style.backgroundColor = "";
                    kodeInput.classList.remove("is-invalid");
                    kodeInput.setCustomValidity("");
                }
            });

            // Group by CPL and Kode CPMK
            const groups = new Map();
            rows.forEach(row => {
                const cplSelect = row.querySelector("[data-cpmk-cpl-select], .cpl-select-input");
                const kodeInput = row.querySelector('input[name*="[kode]"]');
                const cplVal = (cplSelect?.value || cplSelect?.dataset?.selectedValue || "").trim();
                let cplText = "";
                if (cplSelect && cplSelect.selectedIndex >= 0 && cplSelect.options[cplSelect.selectedIndex]) {
                    cplText = cplSelect.options[cplSelect.selectedIndex].textContent.trim();
                }
                if (!cplText) cplText = cplVal ? `CPL ${cplVal}` : "CPL";
                const kodeVal = (kodeInput?.value || "").trim();

                if (cplVal && kodeVal) {
                    const key = `${cplVal}___${kodeVal.toLowerCase()}`;
                    if (!groups.has(key)) {
                        groups.set(key, []);
                    }
                    groups.get(key).push({ row, cplSelect, kodeInput, cplText, kodeVal });
                }
            });

            // Mark duplicated pairs
            groups.forEach((items) => {
                if (items.length > 1) {
                    isOverallValid = false;
                    items.forEach(({ row, cplSelect, kodeInput, cplText, kodeVal }) => {
                        if (cplSelect) {
                            cplSelect.style.borderColor = "#ef4444";
                            cplSelect.style.backgroundColor = "#fef2f2";
                            cplSelect.classList.add("is-invalid");
                        }
                        if (kodeInput) {
                            kodeInput.style.borderColor = "#ef4444";
                            kodeInput.style.backgroundColor = "#fef2f2";
                            kodeInput.classList.add("is-invalid");
                            kodeInput.setCustomValidity(`Kombinasi ${cplText} dan Kode CPMK ${kodeVal} sudah digunakan pada baris lain.`);

                            const colDiv = kodeInput.parentElement ? kodeInput.parentElement.parentElement : row;
                            if (colDiv && !colDiv.querySelector('.cpmk-duplicate-warning')) {
                                const warn = document.createElement("div");
                                warn.className = "cpmk-duplicate-warning";
                                warn.style.cssText = "color:#ef4444;font-size:11px;font-weight:600;margin-top:4px;display:flex;align-items:center;gap:4px;line-height:1.2;";
                                warn.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Duplikat (${cplText} & CPMK ${kodeVal})`;
                                colDiv.appendChild(warn);
                            }
                        }
                    });
                }
            });
        });

        return isOverallValid;
    }
}

window.validateCpmkDuplicates = function(container) {
    if (window._rpsCpmkInstance) {
        return window._rpsCpmkInstance.validateDuplicates(container);
    }
    return true;
};

function initRpsCpmk() {
    if (window._rpsCpmkInstance) return;
    const form = document.querySelector('form[data-cpmk-row-builder="1"], #generatorForm, #uploadForm, #rpsSubmitForm, form');
    if (!form) return;
    window._rpsCpmkInstance = new RpsCpmkRows(form);
    window._rpsCpmkInstance.init();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initRpsCpmk);
} else {
    initRpsCpmk();
}

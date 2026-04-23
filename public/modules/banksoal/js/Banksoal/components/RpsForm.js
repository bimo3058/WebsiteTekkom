class RpsFormComponent {
    constructor(config) {
        // Konfigurasi default untuk elemen form RPS.
        this.config = {
            mkSelectId: "mkSelect",
            semesterSelectId: "semester",
            tahunAjaranSelectId: "tahun_ajaran",
            dosenSelectId: "dosenSelect",
            cplSelectId: "cplSelect",
            cpmkSelectId: "cpmkSelect",
            fileInputId: "fileInput",
            uploadZoneId: "uploadZone",
            formSelector: "form",
            maxFileSizeBytes: 1024 * 1024,
            ...config,
        };

        this.mkSelect = document.getElementById(this.config.mkSelectId);
        this.semesterSelect = document.getElementById(
            this.config.semesterSelectId,
        );
        this.tahunAjaranSelect = document.getElementById(
            this.config.tahunAjaranSelectId,
        );
        this.dosenSelect = document.getElementById(this.config.dosenSelectId);
        this.cplSelect = document.getElementById(this.config.cplSelectId);
        this.cpmkSelect = document.getElementById(this.config.cpmkSelectId);
        this.fileInput = document.getElementById(this.config.fileInputId);
        this.uploadZone = document.getElementById(this.config.uploadZoneId);
        this.form = document.querySelector(this.config.formSelector);
        this.uploadText = document.getElementById("uploadText");
        this.defaultUploadText =
            this.uploadText?.textContent?.trim() ||
            "Klik untuk unggah atau seret file ke sini";
    }

    // Menyiapkan semua perilaku form.
    init() {
        this._setupCascading();
        this._setupFileUpload();
        this._setupFormValidation();
    }

    // Mengatur alur perubahan berantai: mata kuliah, CPL, dan CPMK.
    _setupCascading() {
        this.mkSelect.addEventListener("change", () => {
            this._onMataKuliahChange();
        });

        this.cplSelect?.addEventListener("change", (e) => {
            this._onCplChange();
        });
    }

    // Menangani perubahan mata kuliah dan memuat data dosen serta CPL terkait.
    _onMataKuliahChange() {
        const mkId = this.mkSelect.value;

        if (!mkId) {
            this.dosenSelect.disabled = true;
            this.dosenSelect.innerHTML = '<option value="">Pilih mata kuliah terlebih dahulu</option>';
            this.cplSelect.disabled = true;
            this.cplSelect.innerHTML = '<option value="">Pilih mata kuliah terlebih dahulu</option>';
            this.cpmkSelect.disabled = true;
            this.cpmkSelect.innerHTML = '<option value="">Pilih CPL terlebih dahulu</option>';
            return;
        }

        // Load dosen
        this._fetchDosenByMk(mkId);

        // Load CPL
        this._fetchCpl(mkId);

        // Disable CPMK
        this.cpmkSelect.disabled = true;
        this.cpmkSelect.innerHTML = '<option value="">Pilih CPL terlebih dahulu</option>';
    }

    // Mengambil daftar dosen berdasarkan assignment di mata kuliah terpilih.
    _fetchDosenByMk(mkId) {
        const url =
            document.querySelector("[data-route-dosen]")?.dataset.routeDosen ||
            "/bank-soal/rps/dosen/dosen";

        const fetchUrl = `${url}?mk_id=${encodeURIComponent(String(mkId))}`;

        fetch(fetchUrl)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                this.dosenSelect.innerHTML = '<option value="">Pilih dosen pengampu tambahan</option>';
                if (data?.length) {
                    data.forEach(d => {
                        const option = document.createElement('option');
                        option.value = d.id;
                        option.textContent = d.name;
                        this.dosenSelect.appendChild(option);
                    });
                    this.dosenSelect.disabled = false;
                } else {
                    this.dosenSelect.innerHTML = '<option value="">Tidak ada dosen pengampu lain ter-assign</option>';
                    this.dosenSelect.disabled = true;
                }
            })
            .catch((err) => {
                console.error("Error fetching dosen:", err);
                this.dosenSelect.disabled = true;
                this.dosenSelect.innerHTML = '<option value="">Error loading dosen</option>';
                Snackbar?.show?.("Gagal memuat dosen", "error");
            });
    }

    // Mengambil daftar CPL berdasarkan mata kuliah.
    _fetchCpl(mkId) {
        const baseUrl =
            document.querySelector("[data-route-cpl]")?.dataset.routeCpl ||
            "/bank-soal/rps/dosen/cpl";
        const url = baseUrl;

        fetch(url)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                this.cplSelect.innerHTML = '<option value="">Pilih CPL</option>';
                if (data?.length) {
                    data.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.id;
                        option.textContent = c.kode;
                        this.cplSelect.appendChild(option);
                    });
                    this.cplSelect.disabled = false;
                } else {
                    this.cplSelect.innerHTML = '<option value="">Tidak ada CPL tersedia</option>';
                    this.cplSelect.disabled = true;
                }
            })
            .catch((err) => {
                console.error("Error fetching CPL:", err);
                this.cplSelect.disabled = true;
                this.cplSelect.innerHTML = '<option value="">Error loading CPL</option>';
                Snackbar?.show?.("Gagal memuat CPL", "error");
            });
    }

    // Mengambil daftar CPMK berdasarkan CPL yang dipilih.
    _onCplChange() {
        const cplId = this.cplSelect.value;

        if (!cplId) {
            this.cpmkSelect.disabled = true;
            this.cpmkSelect.innerHTML = '<option value="">Pilih CPL terlebih dahulu</option>';
            return;
        }

        const baseUrl =
            document.querySelector("[data-route-cpmk]")?.dataset.routeCpmk ||
            "/bank-soal/rps/dosen/cpmk";
        const url = `${baseUrl}?cpl_id=${cplId}`;

        fetch(url)
            .then((r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then((data) => {
                this.cpmkSelect.innerHTML = '<option value="">Pilih CPMK</option>';
                if (data?.length) {
                    data.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.id;
                        option.textContent = c.kode;
                        this.cpmkSelect.appendChild(option);
                    });
                    this.cpmkSelect.disabled = false;
                } else {
                    this.cpmkSelect.innerHTML = '<option value="">Tidak ada CPMK tersedia</option>';
                    this.cpmkSelect.disabled = true;
                }
            })
            .catch((err) => {
                console.error("Error fetching CPMK:", err);
                this.cpmkSelect.disabled = true;
                this.cpmkSelect.innerHTML = '<option value="">Error loading CPMK</option>';
                Snackbar?.show?.("Gagal memuat CPMK", "error");
            });
    }
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

    // Menyiapkan area unggah file agar mendukung drag and drop.
    _setupFileUpload() {
        if (!this.uploadZone || !this.fileInput) return;

        this.uploadZone.addEventListener("dragover", (e) => {
            e.preventDefault();
            this.uploadZone.style.borderColor = "#6B4FF4";
            this.uploadZone.style.backgroundColor = "rgba(107, 79, 244, 0.05)";
        });

        this.uploadZone.addEventListener("dragleave", () => {
            this.uploadZone.style.borderColor = "#cbd5e1";
            this.uploadZone.style.backgroundColor = "#f9fafb";
        });

        this.uploadZone.addEventListener("drop", (e) => {
            e.preventDefault();
            this.uploadZone.style.borderColor = "#cbd5e1";
            this.uploadZone.style.backgroundColor = "#f9fafb";
            this.fileInput.files = e.dataTransfer.files;
            this._handleSelectedFile();
        });

        this.fileInput.addEventListener("change", () =>
            this._handleSelectedFile(),
        );
    }

    // Memvalidasi file yang dipilih lalu memperbarui label unggahan.
    _handleSelectedFile() {
        if (!this.fileInput?.files?.length) {
            this._setUploadText(this.defaultUploadText);
            return;
        }

        const file = this.fileInput.files[0];
        const fileError = this._validateSingleFile(file);

        if (fileError) {
            this._resetSelectedFile();
            Snackbar?.show?.(fileError, "error");
            return;
        }

        this._setUploadText(file.name);
    }

    // Menampilkan nama file yang dipilih di area unggah.
    _updateFileName() {
        if (this.fileInput.files?.length) {
            this._setUploadText(this.fileInput.files[0].name);
        }
    }

    // Memperbarui teks pada label upload.
    _setUploadText(text) {
        if (this.uploadText) {
            this.uploadText.textContent = text;
        }
    }

    // Mengosongkan file input lalu mengembalikan teks default upload.
    _resetSelectedFile() {
        if (this.fileInput) {
            this.fileInput.value = "";
        }
        this._setUploadText(this.defaultUploadText);
    }

    // Memvalidasi tipe dan ukuran satu file.
    _validateSingleFile(file) {
        if (!file) {
            return "File RPS harus diunggah";
        }

        const fileName = (file.name || "").toLowerCase();
        const isPdfByName = fileName.endsWith(".pdf");
        const isPdfByType = file.type === "application/pdf";

        if (!isPdfByName && !isPdfByType) {
            return "Hanya menerima file berformat PDF";
        }

        if (file.size > this.config.maxFileSizeBytes) {
            return "Ukuran file maksimal 1MB";
        }

        return null;
    }

    // Menjalankan validasi sebelum form dikirim.
    _setupFormValidation() {
        this.form?.addEventListener("submit", (e) => {
            const errors = this._validateForm();
            if (errors.length) {
                e.preventDefault();
                const errorMsg = errors
                    .map((error, index) => `${index + 1}. ${error}`)
                    .join("\n");
                Snackbar?.show?.(`Validasi Error:\n${errorMsg}`, "error");
            }
        });
    }

    // Memeriksa kelengkapan isian form RPS.
    _validateForm() {
        const errors = [];

        if (!this.mkSelect.value) {
            errors.push("Mata Kuliah harus dipilih");
        }
        if (!this.cplSelect.value) {
            errors.push("Minimal satu CPL harus dipilih");
        }
        if (!this.cpmkSelect.value) {
            errors.push("Minimal satu CPMK harus dipilih");
        }
        if (!this.fileInput?.files?.length) {
            errors.push("File RPS harus diunggah");
        } else {
            const fileError = this._validateSingleFile(this.fileInput.files[0]);
            if (fileError) {
                errors.push(fileError);
            }
        }

        return errors;
    }
}

// Instance tunggal agar bisa dipakai langsung dari komponen lain.
const RpsForm = new RpsFormComponent();

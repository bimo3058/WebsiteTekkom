class SnackbarManager {
    constructor() {
        this.snackbars = new Map();
        this.defaultTimeout = 5000; // Durasi default 5 detik.
        this.animationDuration = 300; // Durasi animasi dalam milidetik.
    }

    // Menyiapkan semua snackbar yang sudah ada di halaman.
    init() {
        const snackbars = document.querySelectorAll(".snackbar");
        snackbars.forEach((snackbar) => {
            this._setupSnackbar(snackbar);
            // Sembunyikan otomatis setelah waktu default.
            this._autoHide(snackbar, this.defaultTimeout);
        });
    }

    // Menyiapkan satu elemen snackbar.
    _setupSnackbar(element) {
        if (this.snackbars.has(element)) return; // Sudah pernah disiapkan.

        const closeBtn = element.querySelector(".snackbar-close");
        if (closeBtn) {
            closeBtn.addEventListener("click", () => this.hide(element));
        }

        this.snackbars.set(element, {
            timeoutId: null,
            isShown: true,
        });
    }

    // Menjalankan sembunyi otomatis untuk snackbar.
    _autoHide(element, timeout) {
        if (!this.snackbars.has(element)) {
            this._setupSnackbar(element);
        }

        const data = this.snackbars.get(element);
        if (data.timeoutId) clearTimeout(data.timeoutId);

        data.timeoutId = setTimeout(() => {
            this.hide(element);
        }, timeout);
    }

    // Menampilkan snackbar baru dengan tipe pesan tertentu.
    show(message, type = "info", timeout = this.defaultTimeout) {
        // Membuat elemen snackbar baru.
        const snackbar = document.createElement("div");
        snackbar.className = `snackbar snackbar-${type}`;
        snackbar.setAttribute("role", "alert");

        // Pemetaan judul, ikon, kelas warna berdasarkan tipe pesan.
        const typeConfig = {
            success: {
                title: "BERHASIL",
                icon: "fas fa-check",
                iconBg: "bg-emerald-50",
                iconColor: "text-emerald-600",
                titleColor: "text-emerald-600"
            },
            error: {
                title: "GAGAL",
                icon: "fas fa-times",
                iconBg: "bg-rose-50",
                iconColor: "text-rose-600",
                titleColor: "text-rose-600"
            },
            warning: {
                title: "PERINGATAN",
                icon: "fas fa-exclamation-triangle",
                iconBg: "bg-amber-50",
                iconColor: "text-amber-600",
                titleColor: "text-amber-600"
            },
            info: {
                title: "INFORMASI",
                icon: "fas fa-info",
                iconBg: "bg-blue-50",
                iconColor: "text-blue-600",
                titleColor: "text-blue-600"
            }
        };

        const config = typeConfig[type] || typeConfig.info;

        // Menyusun konten HTML snackbar.
        snackbar.innerHTML = `
            <div class="flex items-start gap-3 w-full">
                <!-- Icon Container -->
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full ${config.iconBg} ${config.iconColor}">
                    <i class="${config.icon} text-sm"></i>
                </div>
                <!-- Content -->
                <div class="flex-1 min-w-0 pr-2">
                    <p class="text-[11px] font-bold ${config.titleColor} tracking-wider uppercase">${config.title}</p>
                    <div class="text-xs text-slate-600 font-semibold mt-1 leading-relaxed">${message}</div>
                </div>
                <!-- Close Button -->
                <button type="button" class="snackbar-close text-slate-400 hover:text-slate-600 shrink-0 transition-colors" title="Tutup">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        `;

        // Menghitung offset top jika sudah ada snackbar lain di layar.
        const existing = document.querySelectorAll(".snackbar");
        let topOffset = 24;
        existing.forEach((el) => {
            topOffset += el.offsetHeight + 12; // 12px gap
        });
        snackbar.style.top = `${topOffset}px`;

        // Menambahkan elemen snackbar ke DOM.
        document.body.appendChild(snackbar);

        // Menyiapkan lalu menampilkan snackbar.
        this._setupSnackbar(snackbar);
        this._autoHide(snackbar, timeout);

        return snackbar;
    }

    // Menutup snackbar.
    hide(element) {
        if (!element || !element.classList.contains("snackbar")) return;

        const data = this.snackbars.get(element);
        if (data?.timeoutId) clearTimeout(data.timeoutId);

        // Memicu animasi keluar.
        element.style.animation =
            "slideOutRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards";

        // Hapus elemen setelah animasi selesai.
        setTimeout(() => {
            element.remove();
            this.snackbars.delete(element);
        }, this.animationDuration);
    }

    // Menutup semua snackbar yang aktif.
    closeAll() {
        const snackbars = Array.from(this.snackbars.keys());
        snackbars.forEach((snackbar) => this.hide(snackbar));
    }
}

// Instance tunggal agar bisa dipanggil dari mana saja.
const Snackbar = new SnackbarManager();

// Inisialisasi otomatis saat DOM siap.
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        Snackbar.init();
    });
} else {
    Snackbar.init();
}

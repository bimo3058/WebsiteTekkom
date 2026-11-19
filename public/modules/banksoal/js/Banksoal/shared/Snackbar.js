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
        // Mencegah penumpukan snackbar dengan pesan dan tipe yang persis sama
        const currentSnackbars = document.querySelectorAll(".snackbar");
        for (const el of currentSnackbars) {
            const elType = el.getAttribute("data-type");
            const elMsg = el.getAttribute("data-message");
            if (elType === type && elMsg === message) {
                return null; // Abaikan jika sudah ada yang identik
            }
        }

        // Membuat elemen snackbar baru.
        const snackbar = document.createElement("div");
        snackbar.className = `snackbar snackbar-${type}`;
        snackbar.setAttribute("role", "alert");
        snackbar.setAttribute("data-type", type);
        snackbar.setAttribute("data-message", message);

        // Pemetaan ikon gaya SICATA (lingkaran muted dengan glyph warna).
        const typeConfig = {
            success: {
                icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#E8F7EC"/><path d="M8.5 12.4l2.4 2.4 4.6-5" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                iconClass: "snackbar-icon"
            },
            error: {
                icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#FDECEC"/><path d="M9.3 9.3l5.4 5.4m0-5.4l-5.4 5.4" stroke="#DC2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                iconClass: "snackbar-icon"
            },
            warning: {
                icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#FEF6E0"/><path d="M12 8v5m0 2.6h.01" stroke="#D97706" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                iconClass: "snackbar-icon"
            },
            info: {
                icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#EAF2FE"/><path d="M12 11h.01M11.5 14.4H12a.6.6 0 0 0 .6-.6V10.6" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                iconClass: "snackbar-icon"
            }
        };

        const config = typeConfig[type] || typeConfig.info;

        // Menyusun konten HTML snackbar.
        snackbar.innerHTML = `
            <span class="${config.iconClass}" aria-hidden="true">${config.icon}</span>
            <div class="snackbar-content">${message}</div>
            <button type="button" class="snackbar-close" title="Tutup" aria-label="Tutup notifikasi">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        `;

        // Menghitung offset bottom jika sudah ada snackbar lain di layar.
        snackbar.style.top = "auto";
        const existing = document.querySelectorAll(".snackbar");
        let bottomOffset = 24;
        existing.forEach((el) => {
            bottomOffset += el.offsetHeight + 12; // 12px gap
        });
        snackbar.style.bottom = `${bottomOffset}px`;

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
            "slideOutBottom 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards";

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

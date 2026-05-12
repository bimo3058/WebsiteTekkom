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

        // Pemetaan ikon berdasarkan tipe pesan.
        const icons = {
            success: "fas fa-check-circle",
            error: "fas fa-exclamation-circle",
            warning: "fas fa-exclamation-triangle",
            info: "fas fa-info-circle",
        };

        const icon = icons[type] || icons.info;

        // Menyusun konten HTML snackbar.
        snackbar.innerHTML = `
      <i class="${icon}"></i>
      <span>${message}</span>
      <button type="button" class="snackbar-close" title="Tutup">
        <i class="fas fa-times"></i>
      </button>
    `;

        // Menambahkan elemen snackbar ke DOM.
        document.body.appendChild(snackbar);

        // Menyiapkan lalu menampilkan snackbar.
        this._setupSnackbar(snackbar);
        this._autoHide(snackbar, timeout);

        return snackbar;
    }

    // Menambahkan snackbar ke DOM lalu menampilkannya.
    hide(element) {
        if (!element || !element.classList.contains("snackbar")) return;

        const data = this.snackbars.get(element);
        if (data?.timeoutId) clearTimeout(data.timeoutId);

        // Memicu animasi keluar.
        element.style.animation =
            "slideOutDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards";

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

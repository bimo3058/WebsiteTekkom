class FileUploadHandlerComponent {
    static init() {
        // Tutup modal saat area luar diklik.
        document.addEventListener("click", (e) => {
            const modal = document.getElementById("dokumenModal");
            if (e.target === modal) {
                this.closeDokumenModal();
            }
        });

        // Tutup modal saat tombol Escape ditekan.
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                this.closeDokumenModal();
            }
        });
    }

    // Menampilkan pratinjau dokumen RPS.
    static previewDokumen(rpsId, mkNama, dokumenPath) {
        const modal = document.getElementById("dokumenModal");
        const iframe = document.getElementById("dokumenFrame");
        const embed = document.getElementById("dokumenEmbed");
        const title = document.getElementById("modalTitle");

        if (!modal || !iframe || !embed) {
            console.error("Modal elements not found");
            return;
        }

        // Menyusun URL pratinjau untuk PDF dan format lain.
        const baseUrl =
            document.querySelector("[data-preview-route]")?.dataset
                .previewRoute || `/bank-soal/rps/dosen/preview/${rpsId}`;
        const previewUrl = baseUrl.includes("?")
            ? baseUrl
            : baseUrl.replace(":rpsId", rpsId);

        // Mengambil ekstensi file.
        const fileExt = dokumenPath.split(".").pop().toLowerCase();

        // Menyusun URL publik untuk Office viewer.
        const appUrl =
            document.querySelector("[data-app-url]")?.dataset.appUrl ||
            window.location.origin;
        const publicFileUrl = appUrl + "/storage/bank-soal/" + dokumenPath;

        // Mengisi judul modal.
        title.textContent = `Preview: ${mkNama}`;

        // Jika file Office, gunakan viewer Microsoft.
        if (["docx", "xlsx", "pptx"].includes(fileExt)) {
            // Gunakan Microsoft Office viewer.
            const encodedUrl = encodeURIComponent(publicFileUrl);
            iframe.style.display = "none";
            embed.style.display = "block";
            embed.innerHTML = `
                <iframe 
                    src="https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}"
                    style="width: 100%; height: 100%; border: none;" 
                    allowfullscreen>
                </iframe>
            `;
        } else {
            // Gunakan iframe bawaan untuk PDF, gambar, dan format lain.
            iframe.style.display = "block";
            embed.style.display = "none";
            embed.innerHTML = "";
            iframe.src = previewUrl;
        }

        // Tampilkan modal.
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
    }

    // Menutup modal pratinjau dokumen.
    static closeDokumenModal() {
        const modal = document.getElementById("dokumenModal");
        const iframe = document.getElementById("dokumenFrame");
        const embed = document.getElementById("dokumenEmbed");

        if (!modal) return;

        modal.style.display = "none";
        document.body.style.overflow = "auto";

        // Mengosongkan isi iframe dan embed.
        if (iframe) {
            iframe.src = "";
            iframe.style.display = "none";
        }
        if (embed) {
            embed.innerHTML = "";
            embed.style.display = "none";
        }
    }
}

// Inisialisasi otomatis saat DOM siap.
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        FileUploadHandlerComponent.init();
    });
} else {
    FileUploadHandlerComponent.init();
}

// Instance global agar bisa diakses dari komponen lain.
const FileHandler = FileUploadHandlerComponent;

// Fungsi global untuk pemanggilan inline pada template.
window.previewDokumen = function (rpsId, mkNama, dokumenPath) {
    FileHandler.previewDokumen(rpsId, mkNama, dokumenPath);
};

// Fungsi global untuk menutup modal dari event inline.
window.closeDokumenModal = function () {
    FileHandler.closeDokumenModal();
};

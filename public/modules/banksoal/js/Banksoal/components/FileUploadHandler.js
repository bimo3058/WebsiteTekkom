/**
 * ════════════════════════════════════════════════════════════════════
 * FileUploadHandler.js - File Preview and Upload Handler
 * ════════════════════════════════════════════════════════════════════
 * Handles document preview in modal (supports Office files via Office API).
 *
 * Usage:
 *   FileHandler.previewDokumen(rpsId, mkNama, dokumenPath);
 *   FileHandler.closeDokumenModal();
 */

class FileUploadHandlerComponent {
    /**
     * Initialize file handler (setup modal event listeners)
     */
    static init() {
        // Close modal on backdrop click
        document.addEventListener("click", (e) => {
            const modal = document.getElementById("dokumenModal");
            if (e.target === modal) {
                this.closeDokumenModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                this.closeDokumenModal();
            }
        });
    }

    /**
     * Preview document in modal
     * @param {number|string} rpsId - RPS ID
     * @param {string} mkNama - Mata Kuliah name
     * @param {string} dokumenPath - Document path/filename
     */
    static previewDokumen(rpsId, mkNama, dokumenPath) {
        const modal = document.getElementById("dokumenModal");
        const iframe = document.getElementById("dokumenFrame");
        const embed = document.getElementById("dokumenEmbed");
        const title = document.getElementById("modalTitle");

        if (!modal || !iframe || !embed) {
            console.error("Modal elements not found");
            return;
        }

        // Build preview URL (for PDF and other formats)
        const baseUrl =
            document.querySelector("[data-preview-route]")?.dataset
                .previewRoute || `/bank-soal/rps/dosen/preview/${rpsId}`;
        const previewUrl = baseUrl.includes("?")
            ? baseUrl
            : baseUrl.replace(":rpsId", rpsId);

        // Get file extension
        const fileExt = dokumenPath.split(".").pop().toLowerCase();

        // Get full URL for Office viewer
        const appUrl =
            document.querySelector("[data-app-url]")?.dataset.appUrl ||
            window.location.origin;
        const publicFileUrl = appUrl + "/storage/bank-soal/" + dokumenPath;

        // Set modal title
        title.textContent = `Preview: ${mkNama}`;

        // Check if Office format (DOCX, XLSX, PPTX)
        if (["docx", "xlsx", "pptx"].includes(fileExt)) {
            // Use Microsoft Office viewer
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
            // Use native iframe for PDF, images, etc.
            iframe.style.display = "block";
            embed.style.display = "none";
            embed.innerHTML = "";
            iframe.src = previewUrl;
        }

        // Show modal
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
    }

    /**
     * Close document preview modal
     */
    static closeDokumenModal() {
        const modal = document.getElementById("dokumenModal");
        const iframe = document.getElementById("dokumenFrame");
        const embed = document.getElementById("dokumenEmbed");

        if (!modal) return;

        modal.style.display = "none";
        document.body.style.overflow = "auto";

        // Clear iframe and embed content
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

// Auto-init on DOM ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        FileUploadHandlerComponent.init();
    });
} else {
    FileUploadHandlerComponent.init();
}

// Export as FileHandler for global access
const FileHandler = FileUploadHandlerComponent;

// Global wrapper function for inline onclick handlers
window.previewDokumen = function (rpsId, mkNama, dokumenPath) {
    FileHandler.previewDokumen(rpsId, mkNama, dokumenPath);
};

// Global wrapper function for closing modal from inline events
window.closeDokumenModal = function () {
    FileHandler.closeDokumenModal();
};

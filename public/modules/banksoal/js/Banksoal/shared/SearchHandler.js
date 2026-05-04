class SearchHandlerComponent {
    // Menghubungkan input pencarian dengan tabel yang ingin difilter.
    static bindSearch(searchInputId, tableId, skipColspanRows = false) {
        const searchInput = document.getElementById(searchInputId);
        const table = document.getElementById(tableId);

        if (!searchInput || !table) return;

        searchInput.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            const rows = table.querySelectorAll("tbody tr");

            rows.forEach((row) => {
                // Lewati baris colspan jika dipakai untuk spasi atau placeholder.
                if (skipColspanRows && row.querySelector("td[colspan]")) return;

                // Tampilkan atau sembunyikan baris sesuai hasil pencarian.
                const matches = row.textContent.toLowerCase().includes(query);
                row.style.display = matches ? "" : "none";
            });
        });
    }

    // Menghubungkan beberapa pasangan input dan tabel sekaligus.
    static bindSearches(bindings) {
        bindings.forEach((binding) => {
            this.bindSearch(
                binding.search,
                binding.table,
                binding.skipColspan || false,
            );
        });
    }
}

// Mengikat pencarian otomatis saat DOM siap jika elemen umum tersedia.
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        // Coba sambungkan kombinasi pencarian dan tabel yang umum dipakai.
        if (
            document.getElementById("searchSoal") &&
            document.getElementById("tableSoal")
        ) {
            SearchHandler.bindSearch("searchSoal", "tableSoal");
        }
        if (
            document.getElementById("searchPackages") &&
            document.getElementById("tablePackages")
        ) {
            SearchHandler.bindSearch("searchPackages", "tablePackages");
        }
        if (
            document.getElementById("searchArsip") &&
            document.getElementById("tableArsip")
        ) {
            SearchHandler.bindSearch("searchArsip", "tableArsip", true);
        }
    });
}

// Instance global agar bisa dipakai dari script lain.
const SearchHandler = SearchHandlerComponent;

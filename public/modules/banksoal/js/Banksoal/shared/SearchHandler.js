/**
 * ════════════════════════════════════════════════════════════════════
 * SearchHandler.js - Table Search Component
 * ════════════════════════════════════════════════════════════════════
 * Lightweight search functionality for filtering table rows by text content.
 *
 * Usage in HTML:
 *   <input id="searchSoal" class="search-input" ...>
 *   <table id="tableSoal" ...>
 *
 * Usage in JS:
 *   SearchHandler.bindSearch('searchSoal', 'tableSoal');
 *   SearchHandler.bindSearch('searchArsip', 'tableArsip', true); // skip colspan rows
 */

class SearchHandlerComponent {
    /**
     * Bind search input to table
     * @param {string} searchInputId - ID of search input element
     * @param {string} tableId - ID of table element to search
     * @param {boolean} skipColspanRows - Skip rows with colspan (for arsip)
     */
    static bindSearch(searchInputId, tableId, skipColspanRows = false) {
        const searchInput = document.getElementById(searchInputId);
        const table = document.getElementById(tableId);

        if (!searchInput || !table) return;

        searchInput.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            const rows = table.querySelectorAll("tbody tr");

            rows.forEach((row) => {
                // Skip rows with colspan (used in some tables for spacing)
                if (skipColspanRows && row.querySelector("td[colspan]")) return;

                // Show/hide based on search match
                const matches = row.textContent.toLowerCase().includes(query);
                row.style.display = matches ? "" : "none";
            });
        });
    }

    /**
     * Bind multiple search inputs at once
     * @param {Array<Object>} bindings - Array of {search, table, skipColspan}
     */
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

// Auto-bind on DOM ready if common IDs exist
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        // Try to auto-bind common search/table combinations
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

// Export as SearchHandler for global access
const SearchHandler = SearchHandlerComponent;

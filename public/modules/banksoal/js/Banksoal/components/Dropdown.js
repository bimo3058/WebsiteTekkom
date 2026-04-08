/**
 * ════════════════════════════════════════════════════════════════════
 * TomSelectDropdown.js - TomSelect Dropdown Component
 * ════════════════════════════════════════════════════════════════════
 * Initializes TomSelect instances for form dropdowns.
 *
 * Features:
 * - Consistent styling across all dropdowns
 * - Single item selection only
 * - Customizable placeholder and options
 * - Prevents clipping with dropdown parent
 *
 * Usage:
 *   const dropdowns = new TomSelectDropdown();
 *   dropdowns.init('#mkSelect', 'Pilih Mata Kuliah');
 *   dropdowns.init('#semester', 'Pilih Semester');
 *   dropdowns.init('#tahun_ajaran', 'Pilih Tahun Ajaran');
 */

class Dropdown {
    constructor() {
        this.instances = {};
    }

    /**
     * Initialize a TomSelect instance
     * @param {string} selector - CSS selector for the select element
     * @param {string} placeholder - Placeholder text
     * @param {Object} options - Additional TomSelect options
     * @returns {TomSelect|null} - TomSelect instance or null if element not found
     */
    init(selector, placeholder = "Pilih Opsi", options = {}) {
        const element = document.querySelector(selector);
        if (!element) {
            console.warn(`Element with selector "${selector}" not found`);
            return null;
        }

        const defaultOptions = {
            maxItems: 1,
            placeholder: placeholder,
            allowEmptyOption: false,
            dropdownParent: "body",
            create: false,
            render: {
                option: (data, escape) => {
                    return `<div class="ts-option">${escape(data.text)}</div>`;
                },
                item: (data, escape) => {
                    return `<div class="ts-item">${escape(data.text)}</div>`;
                },
            },
            ...options,
        };

        const instance = new TomSelect(selector, defaultOptions);
        this.instances[selector] = instance;
        return instance;
    }

    /**
     * Get a TomSelect instance by selector
     * @param {string} selector - CSS selector
     * @returns {TomSelect|undefined}
     */
    getInstance(selector) {
        return this.instances[selector];
    }

    /**
     * Initialize all dropdowns at once
     * @param {Object} dropdowns - Object with selector as key and placeholder as value
     * Example: { '#mkSelect': 'Pilih Mata Kuliah', '#semester': 'Pilih Semester' }
     */
    initAll(dropdowns) {
        Object.entries(dropdowns).forEach(([selector, placeholder]) => {
            this.init(selector, placeholder);
        });
    }

    /**
     * Destroy a TomSelect instance
     * @param {string} selector - CSS selector
     */
    destroy(selector) {
        if (this.instances[selector]) {
            this.instances[selector].destroy();
            delete this.instances[selector];
        }
    }

    /**
     * Destroy all instances
     */
    destroyAll() {
        Object.keys(this.instances).forEach((selector) => {
            this.destroy(selector);
        });
    }
}

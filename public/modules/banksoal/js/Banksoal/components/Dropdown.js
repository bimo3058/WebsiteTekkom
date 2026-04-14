class Dropdown {
    constructor() {
        this.instances = {};
    }
    // Menyiapkan dropdown Tom Select.
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

    // Mengambil instance berdasarkan selector
    getInstance(selector) {
        return this.instances[selector];
    }

    initAll(dropdowns) {
        Object.entries(dropdowns).forEach(([selector, placeholder]) => {
            this.init(selector, placeholder);
        });
    }

    // Menghapus instance dropdown yang sudah tidak dipakai.
    destroy(selector) {
        if (this.instances[selector]) {
            this.instances[selector].destroy();
            delete this.instances[selector];
        }
    }

    destroyAll() {
        Object.keys(this.instances).forEach((selector) => {
            this.destroy(selector);
        });
    }
}

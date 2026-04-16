class Dropdown {
    constructor() {
        this.instances = {};
        this._bindViewportEvents();
    }

    // Menutup semua dropdown yang sedang terbuka.
    _closeAllOpenInstances() {
        Object.values(this.instances).forEach((instance) => {
            if (!instance) return;
            if (instance.isOpen && typeof instance.close === "function") {
                instance.close();
            }
        });
    }

    // Mengecek apakah event scroll berasal dari panel dropdown Tom Select.
    _isInternalDropdownScroll(event) {
        const target = event?.target;
        if (!target || !(target instanceof Element)) return false;

        if (
            target.closest(".ts-dropdown") ||
            target.closest(".ts-dropdown-content")
        ) {
            return true;
        }

        return Object.values(this.instances).some((instance) => {
            if (!instance || !instance.isOpen) return false;
            const dropdown = instance.dropdown;
            const dropdownContent = instance.dropdown_content;
            return (
                (dropdown instanceof Element && dropdown.contains(target)) ||
                (dropdownContent instanceof Element &&
                    dropdownContent.contains(target))
            );
        });
    }

    // Menutup dropdown saat viewport berubah agar panel tidak ikut terbawa saat scroll.
    _bindViewportEvents() {
        const handleViewportChange = (event) => {
            if (
                event?.type === "scroll" &&
                this._isInternalDropdownScroll(event)
            ) {
                return;
            }
            this._closeAllOpenInstances();
        };

        window.addEventListener("scroll", handleViewportChange, true);
        window.addEventListener("resize", handleViewportChange);
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

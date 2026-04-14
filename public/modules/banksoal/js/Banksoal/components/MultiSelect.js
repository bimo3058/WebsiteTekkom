class MultiSelect {
    constructor(wrapper, options = {}) {
        // Menyimpan elemen pembungkus dan opsi dasar komponen.
        this.wrapper = wrapper;
        this.name = wrapper.dataset.name;
        this.placeholder = wrapper.dataset.placeholder || "Pilih…";
        this.disabled = wrapper.dataset.disabled === "true";
        this.items = [];
        this.open = false;
        this.searchVal = "";
        this.maxWidth = options.maxWidth || null;
        this.hasTooltip = options.hasTooltip !== false;
        this.keepOpen = options.keepOpen === true;

        this._build();
        this._bindEvents();
    }

    // Membangun struktur HTML awal untuk komponen multi-select.
    _build() {
        this.wrapper.innerHTML = `
            <div class="ms-trigger${this.disabled ? " disabled" : ""}">
                <span class="ms-placeholder">${this.placeholder}</span>
                <i class="fas fa-chevron-down ms-chevron"></i>
            </div>
            <div class="ms-dropdown">
                <div class="ms-option-list"></div>
            </div>
        `;
        this.trigger = this.wrapper.querySelector(".ms-trigger");
        this.dropdown = this.wrapper.querySelector(".ms-dropdown");
        this.list = this.wrapper.querySelector(".ms-option-list");
    }

    // Mengikat event klik pada trigger dan area luar komponen.
    _bindEvents() {
        this.trigger.addEventListener("click", (e) => {
            if (this.disabled) return;
            if (e.target.classList.contains("ms-badge-clear")) return;
            this._toggle();
        });
        document.addEventListener("click", (e) => {
            if (!this.wrapper.contains(e.target)) this._close();
        });
    }

    // Membuka atau menutup dropdown.
    _toggle() {
        this.open ? this._close() : this._openDropdown();
    }

    _openDropdown() {
        this.open = true;
        this.trigger.classList.add("open");
        this.dropdown.classList.add("open");

        const ph = this.trigger.querySelector(
            ".ms-placeholder, .ms-selected-labels",
        );
        if (ph) ph.remove();

        if (!this.trigger.querySelector(".ms-search-input")) {
            const inp = document.createElement("input");
            inp.className = "ms-search-input";
            inp.placeholder = "Cari…";
            inp.autocomplete = "off";
            this.trigger.insertBefore(
                inp,
                this.trigger.querySelector(".ms-chevron"),
            );
            inp.addEventListener("input", () => {
                this.searchVal = inp.value;
                this._renderList();
            });
            inp.focus();
        }
        this._renderList();
    }

    _forceClose() {
        this.open = false;
        this.trigger.classList.remove("open");
        this.dropdown.classList.remove("open");
        const inp = this.trigger.querySelector(".ms-search-input");
        if (inp) inp.remove();
        this.searchVal = "";
    }

    _close() {
        if (!this.open) return;
        this._forceClose();
        this._renderTrigger();
    }

    _measureText(text) {
        if (!this._canvas) {
            this._canvas = document.createElement("canvas");
            this._ctx = this._canvas.getContext("2d");
        }
        const style = window.getComputedStyle(this.trigger);
        this._ctx.font = `${style.fontWeight} ${style.fontSize} ${style.fontFamily}`;
        return this._ctx.measureText(text).width;
    }

    _truncateToWidth(text, maxPx) {
        if (this._measureText(text) <= maxPx) return text;
        let truncated = "";
        for (let i = 0; i < text.length; i++) {
            const test = text.slice(0, i + 1) + "…";
            if (this._measureText(test) > maxPx) break;
            truncated = text.slice(0, i + 1);
        }
        return truncated + "…";
    }

    _renderTrigger() {
        if (!this.trigger.querySelector(".ms-chevron")) {
            const ch = document.createElement("i");
            ch.className = "fas fa-chevron-down ms-chevron";
            this.trigger.appendChild(ch);
        }
        const chevron = this.trigger.querySelector(".ms-chevron");

        ["ms-badge", "ms-placeholder", "ms-selected-labels"].forEach((cls) => {
            this.trigger
                .querySelectorAll("." + cls)
                .forEach((el) => el.remove());
        });

        if (this.open) return;

        const selected = this.items.filter((i) => i.selected);

        if (!selected.length) {
            const ph = document.createElement("span");
            ph.className = "ms-placeholder";
            ph.textContent = this.placeholder;
            this.trigger.insertBefore(ph, chevron);
        } else {
            const labelsEl = document.createElement("span");
            labelsEl.className = "ms-selected-labels";
            labelsEl.style.minWidth = "0";

            const allLabels = selected.map((i) => i.label).join(", ");

            if (this.maxWidth) {
                const reserved = 52 + 24 + 16;
                const available = this.maxWidth - reserved;
                labelsEl.textContent = this._truncateToWidth(
                    allLabels,
                    available,
                );
            } else {
                labelsEl.textContent = allLabels;
            }

            this.trigger.insertBefore(labelsEl, chevron);

            const badgeEl = document.createElement("span");
            badgeEl.className = "ms-badge";
            if (this.hasTooltip) {
                badgeEl.setAttribute("data-tooltip", allLabels);
            }
            badgeEl.innerHTML = `${selected.length} <button type="button" class="ms-badge-clear" title="Hapus semua">×</button>`;
            this.trigger.insertBefore(badgeEl, chevron);
            badgeEl
                .querySelector(".ms-badge-clear")
                .addEventListener("click", (e) => {
                    e.stopPropagation();
                    this.items.forEach((i) => (i.selected = false));
                    this._renderTrigger();
                    this._syncHidden();
                    this._emitChange();
                });
        }
    }

    _renderList() {
        const query = this.searchVal.toLowerCase();
        const filtered = this.items.filter(
            (i) => !query || i.label.toLowerCase().includes(query),
        );

        const _state = () => {
            const sel = this.items.filter((i) => i.selected);
            const allSel =
                sel.length === this.items.length && this.items.length > 0;
            const someSel = sel.length > 0 && !allSel;
            return { sel, allSel, someSel };
        };

        const { allSel, someSel } = _state();

        const sorted = query
            ? filtered
            : [
                  ...filtered.filter((i) => i.selected),
                  ...filtered.filter((i) => !i.selected),
              ];

        const saClass = allSel ? "selected" : someSel ? "indeterminate" : "";
        let html = `<div class="ms-option select-all ${saClass}" data-action="select-all">
                        <span class="ms-cb"></span><span>Pilih Semua</span>
                    </div>`;

        if (!sorted.length) {
            html += `<div class="ms-no-results">Tidak ada hasil</div>`;
        } else {
            sorted.forEach((item) => {
                html += `<div class="ms-option ${item.selected ? "selected" : ""}" data-id="${item.id}">
                            <span class="ms-cb"></span><span>${item.label}</span>
                         </div>`;
            });
        }

        this.list.innerHTML = html;

        this.list.querySelectorAll(".ms-option").forEach((el) => {
            el.addEventListener("click", (e) => {
                e.stopPropagation();

                if (el.dataset.action === "select-all") {
                    const currentState = _state();
                    this.items.forEach(
                        (i) => (i.selected = !currentState.allSel),
                    );
                } else {
                    const id = parseInt(el.dataset.id);
                    const item = this.items.find((i) => i.id === id);
                    if (item) item.selected = !item.selected;
                }

                this._renderList();
                this._syncHidden();
                this._emitChange();
                this._syncTriggerBadge();

                if (!this.keepOpen) {
                    this._close();
                }
            });
        });
    }

    _syncTriggerBadge() {
        const selNow = this.items.filter((i) => i.selected);
        const chevron = this.trigger.querySelector(".ms-chevron");
        const inp = this.trigger.querySelector(".ms-search-input");
        let badge = this.trigger.querySelector(".ms-badge");

        if (selNow.length) {
            if (badge) {
                badge.childNodes[0].textContent = selNow.length + " ";
            } else {
                badge = document.createElement("span");
                badge.className = "ms-badge";
                badge.innerHTML = `${selNow.length} <button type="button" class="ms-badge-clear" title="Hapus semua">×</button>`;
                this.trigger.insertBefore(badge, inp || chevron);
                badge
                    .querySelector(".ms-badge-clear")
                    .addEventListener("click", (e) => {
                        e.stopPropagation();
                        this.items.forEach((i) => (i.selected = false));
                        this._renderTrigger();
                        this._syncHidden();
                        this._emitChange();
                        this._syncTriggerBadge();
                    });
            }
        } else {
            if (badge) badge.remove();
        }
    }

    _syncHidden() {
        this.wrapper
            .querySelectorAll('input[type="hidden"]')
            .forEach((el) => el.remove());
        this.items
            .filter((i) => i.selected)
            .forEach((i) => {
                const inp = document.createElement("input");
                inp.type = "hidden";
                inp.name = this.name;
                inp.value = i.id;
                this.wrapper.appendChild(inp);
            });
    }

    _emitChange() {
        this.wrapper.dispatchEvent(
            new CustomEvent("ms:change", {
                detail: this.getSelected(),
                bubbles: true,
            }),
        );
    }

    setItems(items, readyPlaceholder) {
        this.items = items.map((i) => ({ ...i, selected: false }));
        this.placeholder = readyPlaceholder || this.placeholder;
        this.disabled = false;
        this._forceClose();
        this.trigger.classList.remove("disabled");
        this._renderTrigger();
    }

    setLoading(msg = "Memuat…") {
        this.items = [];
        this.placeholder = msg;
        this.disabled = true;
        this._forceClose();
        this.trigger.classList.add("disabled");
        this._renderTrigger();
    }

    setDisabled(placeholder) {
        this.items = [];
        this.placeholder = placeholder;
        this.disabled = true;
        this._forceClose();
        this.trigger.classList.add("disabled");
        this._renderTrigger();
    }

    getSelected() {
        return this.items.filter((i) => i.selected).map((i) => i.id);
    }
}

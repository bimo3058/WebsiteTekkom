window.bankSoalSelect = function () {
    let select;
    let observer;
    let sync;
    let onInvalid;

    return {
        ready: false, open: false, invalid: false, disabled: false, required: false,
        value: '', selectedLabel: '', options: [], active: -1,
        init() {
            select = this.$el.querySelector('select');
            sync = () => {
                this.options = Array.from(select.options, option => ({
                    value: option.value, label: option.textContent.trim(), disabled: option.disabled,
                }));
                this.value = select.value;
                this.selectedLabel = select.selectedOptions[0]?.textContent.trim() || 'Pilih...';
                this.disabled = select.disabled;
                this.required = select.required;
                this.active = select.selectedIndex;
                if (select.validity.valid) this.invalid = false;
            };
            sync();
            // Existing CPL/CPMK loaders replace native options after a fetch.
            observer = new MutationObserver(sync);
            observer.observe(select, { childList: true, subtree: true, attributes: true,
                attributeFilter: ['selected', 'disabled', 'value', 'label', 'required'] });
            select.addEventListener('change', sync);
            onInvalid = event => {
                event.preventDefault();
                this.invalid = true;
                // Focus the first invalid field, as native form validation would.
                if (select.form?.querySelector(':invalid') === select) {
                    this.$refs.trigger.focus();
                    this.open = true;
                }
            };
            select.addEventListener('invalid', onInvalid);
            select.tabIndex = -1;
            select.setAttribute('aria-hidden', 'true');
            this.ready = true;
        },
        toggle() {
            if (this.disabled) return;
            this.open = !this.open;
            this.active = select.selectedIndex;
            if (this.open) this.scrollToActive();
        },
        choose(index) {
            const option = this.options[index];
            if (!option || option.disabled || this.disabled) return;
            select.value = option.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            sync();
            this.open = false;
            this.$refs.trigger.focus();
        },
        move(direction) {
            if (this.disabled) return;
            if (!this.open) { this.toggle(); return; }
            let next = this.active + direction;
            while (next >= 0 && next < this.options.length) {
                if (!this.options[next].disabled) { this.active = next; break; }
                next += direction;
            }
            this.scrollToActive();
        },
        edge(last) {
            if (this.disabled) return;
            this.open = true;
            this.active = last ? this.options.length : -1;
            this.move(last ? -1 : 1);
        },
        scrollToActive() {
            this.$nextTick(() => {
                this.$el.querySelectorAll('[role="option"]')[this.active]?.scrollIntoView({ block: 'nearest' });
            });
        },
        destroy() {
            observer?.disconnect();
            select?.removeEventListener('change', sync);
            select?.removeEventListener('invalid', onInvalid);
        },
    };
};

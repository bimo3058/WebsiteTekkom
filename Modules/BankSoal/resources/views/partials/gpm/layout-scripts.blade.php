<script src="{{ asset('modules/banksoal/js/Banksoal/shared/Snackbar.js') }}"></script>

<script>
(function () {
    function setActiveTab(button, buttons, panels) {
        const target = button.getAttribute('data-tab-target');
        buttons.forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-blue-600', 'font-semibold');
            btn.classList.add('text-slate-500', 'border-transparent');
        });
        panels.forEach(panel => {
            if (panel.getAttribute('data-tab-panel') === target) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        });
        button.classList.add('text-blue-600', 'border-blue-600', 'font-semibold');
        button.classList.remove('text-slate-500', 'border-transparent');
    }

    function initTabs() {
        const tabGroups = document.querySelectorAll('[data-tabs]');
        tabGroups.forEach(group => {
            const buttons = Array.from(group.querySelectorAll('[data-tab-target]'));
            const panels = Array.from(group.querySelectorAll('[data-tab-panel]'));
            if (!buttons.length || !panels.length) return;

            buttons.forEach(button => {
                button.addEventListener('click', () => setActiveTab(button, buttons, panels));
            });

            const activeButton = buttons.find(btn => btn.hasAttribute('data-tab-active')) || buttons[0];
            if (activeButton) setActiveTab(activeButton, buttons, panels);
        });
    }

    function toggleModal(modalId, shouldOpen) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.toggle('hidden', !shouldOpen);
        modal.setAttribute('aria-hidden', String(!shouldOpen));
        if (shouldOpen) {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function initModals() {
        document.querySelectorAll('[data-modal-open]').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const modalId = trigger.getAttribute('data-modal-open');
                if (modalId) toggleModal(modalId, true);
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const modalId = trigger.getAttribute('data-modal-close');
                if (modalId) toggleModal(modalId, false);
            });
        });

        document.querySelectorAll('[data-modal-overlay]').forEach(overlay => {
            overlay.addEventListener('click', event => {
                if (event.target !== overlay) return;
                const modalId = overlay.getAttribute('data-modal-overlay');
                if (modalId) toggleModal(modalId, false);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initTabs();
            initModals();
        });
    } else {
        initTabs();
        initModals();
    }
})();
</script>

(() => {
    let activeModal = null;
    let previousFocus = null;
    let previousOverflow = '';
    const focusable = modal => [...modal.querySelectorAll('button, a[href], input, textarea, select, [tabindex="0"]')]
        .filter(element => !element.disabled && element.getClientRects().length);

    window.openModal = id => {
        const modal = document.getElementById(id);
        if (!modal) return;
        if (activeModal) window.closeModal(activeModal.id);
        previousFocus = document.activeElement;
        previousOverflow = document.body.style.overflow;
        activeModal = modal;
        modal.style.display = 'flex';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        const title = modal.querySelector('h2, h3');
        if (title) {
            title.id ||= `${id}Title`;
            modal.setAttribute('aria-labelledby', title.id);
        }
        document.body.style.overflow = 'hidden';
        modal.tabIndex = -1;
        (focusable(modal)[0] || modal).focus();
    };

    window.closeModal = id => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.style.display = 'none';
        if (activeModal === modal) {
            activeModal = null;
            document.body.style.overflow = previousOverflow;
            previousFocus?.focus();
        }
    };

    document.addEventListener('keydown', event => {
        if (!activeModal) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            window.closeModal(activeModal.id);
        } else if (event.key === 'Tab') {
            const elements = focusable(activeModal);
            const first = elements[0];
            const last = elements[elements.length - 1];
            if (!first) { event.preventDefault(); activeModal.focus(); return; }
            if (event.shiftKey && (document.activeElement === first || document.activeElement === activeModal)) {
                event.preventDefault(); last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault(); first.focus();
            }
        }
    });
    document.getElementById('modalSuspend')?.addEventListener('click', event => {
        if (event.target === event.currentTarget) window.closeModal('modalSuspend');
    });
})();

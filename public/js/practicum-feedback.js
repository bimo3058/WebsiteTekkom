(() => {
    document.addEventListener('input', event => {
        const input = event.target;
        if (!input.matches?.('input.mp-grade-score') || input.disabled) return;
        const value = input.value.replace(',', '.');
        const pattern = input.dataset.gradeInteger === '1' ? /^[0-9]{1,3}$/ : /^[0-9]{1,3}([.][0-9]{0,2})?$/;
        if (value !== '' && (!pattern.test(value) || Number(value) > 100)) input.value = '';
    }, true);
    // Adapt simple inline confirmations without evaluating source code or replacing window.confirm.
    function messageFrom(handler) {
        const match = handler?.match(/^\s*return\s+confirm\(\s*(['"])((?:\\.|(?!\1)[\s\S])*)\1\s*\)\s*;?\s*$/);
        if (!match) return null;
        return match[2].replace(/\\([nrt'"\\])/g, (_, c) => ({n:'\n',r:'\r',t:'\t'}[c] ?? c));
    }
    function ask(message) {
        return new Promise(resolve => window.dispatchEvent(new CustomEvent('mp-confirm', {detail:{message,resolve}})));
    }
    document.addEventListener('submit', async event => {
        const form = event.target;
        const message = messageFrom(form.getAttribute('onsubmit'));
        if (message === null) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (form.dataset.mpConfirmPending) return;
        form.dataset.mpConfirmPending = '1';
        const submitter = event.submitter;
        const approved = await ask(message);
        delete form.dataset.mpConfirmPending;
        if (!approved || !form.isConnected) return;
        const handler = form.getAttribute('onsubmit');
        form.removeAttribute('onsubmit');
        try { form.requestSubmit(submitter?.isConnected ? submitter : undefined); }
        finally { form.setAttribute('onsubmit', handler); }
    }, true);
    document.addEventListener('click', async event => {
        const button = event.target.closest('button,a,input[type=submit]');
        if (!button) return;
        const message = messageFrom(button.getAttribute('onclick'));
        if (message === null) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (button.dataset.mpConfirmPending) return;
        button.dataset.mpConfirmPending = '1';
        const approved = await ask(message);
        delete button.dataset.mpConfirmPending;
        if (!approved || !button.isConnected) return;
        const handler = button.getAttribute('onclick');
        button.removeAttribute('onclick');
        try { button.click(); } finally { button.setAttribute('onclick', handler); }
    }, true);
    const alerts = document.querySelectorAll('.mp-feedback-stack .mp-floating-feedback,.mp-box-body .mp-flash-success,.mp-box-body .mp-flash-error,.mp-box-body .mp-alert.success,.mp-box-body .mp-alert.error');
    let stack;
    const messages = new Set();
    alerts.forEach(alert => {
        if (alert.closest('form,[role=dialog]')) return;
        if (messages.size) { alert.remove(); return; }
        if (alert.classList.contains('mp-alert')) alert.classList.add('mp-flash', alert.classList.contains('error') ? 'mp-flash-error' : 'mp-flash-success');
        const text = alert.textContent.replace(/\s+/g, ' ').trim();
        if (messages.has(text)) { alert.remove(); return; }
        messages.add(text);
        if (!stack) {
            stack = document.querySelector('.mp-feedback-stack') || document.createElement('div');
            stack.className = 'mp-feedback-stack';
            stack.setAttribute('aria-label', 'Hasil tindakan');
            document.body.append(stack);
            const content = document.querySelector('.mp-box');
            const align = () => {
                if (!content) return;
                const box = content.getBoundingClientRect();
                stack.style.left = `${Math.max(12, box.left + box.width / 2)}px`;
                stack.style.width = `${Math.min(box.width, window.innerWidth - 24)}px`;
                stack.style.maxWidth = 'none';
            };
            align(); window.addEventListener('resize', align);
            stack.removeAttribute('data-feedback-pending');
            if (typeof ResizeObserver !== 'undefined' && content) new ResizeObserver(align).observe(content);
        }
        stack.append(alert);
        alert.classList.add('mp-floating-feedback');
        let timer;
        const dismiss = () => {
            clearTimeout(timer);
            alert.classList.add('is-leaving');
            setTimeout(() => alert.remove(), 300);
        };
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'mp-feedback-dismiss';
        button.setAttribute('aria-label', 'Tutup pemberitahuan');
        button.textContent = '×';
        button.addEventListener('click', dismiss);
        alert.setAttribute('role', alert.classList.contains('mp-flash-error') ? 'alert' : 'status');
        alert.append(button);
        const schedule = () => { clearTimeout(timer); timer = setTimeout(dismiss, alert.classList.contains('mp-flash-error') ? 6000 : 4000); };
        alert.addEventListener('mouseenter', () => clearTimeout(timer));
        alert.addEventListener('mouseleave', schedule);
        alert.addEventListener('focusin', () => clearTimeout(timer));
        alert.addEventListener('focusout', schedule);
        schedule();
    });
})();

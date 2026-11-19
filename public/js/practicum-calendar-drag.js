(() => {
    const enhance = calendar => {
        if (calendar.querySelector('.mp-calendar-drag')) return;
        const handle = document.createElement('div');
        handle.className = 'mp-calendar-drag';
        handle.textContent = 'Geser kalender';
        handle.style.cssText = 'cursor:move;touch-action:none;text-align:center;font-size:11px;color:#666D80;padding:6px;border-bottom:1px solid #DFE1E7;user-select:none;';
        handle.addEventListener('pointerdown', event => {
            if (event.button !== 0) return;
            event.preventDefault();
            const rect = calendar.getBoundingClientRect();
            const dx = event.clientX - rect.left, dy = event.clientY - rect.top;
            calendar.style.setProperty('position', 'fixed', 'important');
            calendar.style.setProperty('transform', 'none', 'important');
            calendar.style.setProperty('right', 'auto', 'important');
            const move = e => {
                calendar.style.setProperty('left', `${Math.max(4, Math.min(e.clientX-dx, window.innerWidth-calendar.offsetWidth-4))}px`, 'important');
                calendar.style.setProperty('top', `${Math.max(4, Math.min(e.clientY-dy, window.innerHeight-calendar.offsetHeight-4))}px`, 'important');
            };
            move(event);
            handle.setPointerCapture(event.pointerId);
            const stop = () => {
                handle.removeEventListener('pointermove', move);
                handle.removeEventListener('pointerup', stop);
                handle.removeEventListener('pointercancel', stop);
                handle.removeEventListener('lostpointercapture', stop);
            };
            handle.addEventListener('pointermove', move);
            handle.addEventListener('pointerup', stop);
            handle.addEventListener('pointercancel', stop);
            handle.addEventListener('lostpointercapture', stop);
        });
        calendar.prepend(handle);
    };
    document.querySelectorAll('.flatpickr-calendar').forEach(enhance);
    new MutationObserver(records => records.forEach(record => record.addedNodes.forEach(node => {
        if (!(node instanceof Element)) return;
        if (node.matches('.flatpickr-calendar')) enhance(node);
        node.querySelectorAll('.flatpickr-calendar').forEach(enhance);
    }))).observe(document.body, {childList:true,subtree:true});
})();

(() => {
    if (window.__mobileNavigationLoaded) return;
    window.__mobileNavigationLoaded = true;
    function boot() {
        const nav = document.getElementById('mobile-navigation');
        if (!nav || nav.dataset.ready) return;
        nav.dataset.ready = 'true';
        const mobile = matchMedia('(max-width:767px)');
        const button = nav.querySelector('[data-mobile-menu]');
        const backdrop = document.querySelector('.mobile-navigation-backdrop');
        const sidebar = document.querySelector('[data-mobile-sidebar]');
        const menu = sidebar || document.getElementById('mobile-account-menu');
        const account = !sidebar;
        const body = document.body;
        body.dataset.mobileShell = '';
        if (!menu.id) menu.id = 'mobile-module-menu';
        button.setAttribute('aria-controls', menu.id);
        let opened = false, returnFocus = button, snapshots = [];
        const originalInert = menu.inert;
        const originalRole = menu.getAttribute('role');
        const originalTabindex = menu.getAttribute('tabindex');
        // Capstone hides the explicit close button; backdrop tap, Escape, or navigation still closes the menu.
        const hideCloseButton = !!document.querySelector('.sitkom-shell-capstone');
        let closeButton = null;
        if (!hideCloseButton) {
            closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'mobile-navigation-close';
            closeButton.dataset.mobileClose = '';
            closeButton.innerHTML = '<span>Tutup menu</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>';
            menu.prepend(closeButton);
        }
        // Focus target when the menu opens: the close button, or the menu itself when hidden (Capstone).
        const closeFocusTarget = closeButton || menu;

        // Use the original rendered sidebar: permission gates and disabled links are never copied or replaced.
        function states() {
            if (!sidebar || !window.Alpine) return [];
            const result = [];
            for (let el = sidebar; el; el = el.parentElement) {
                for (const state of el._x_dataStack || []) {
                    if (result.some(item => item.state === state)) continue;
                    const values = {};
                    for (const key of ['sidebarOpen', 'mobileSidebar', 'collapsed', 'sidebarCollapsed']) {
                        if (Object.prototype.hasOwnProperty.call(state, key) && typeof state[key] === 'boolean') values[key] = state[key];
                    }
                    if (el.classList.contains('sitkom-shell') && typeof state.open === 'boolean') values.open = state.open;
                    if (Object.keys(values).length) result.push({state, values});
                }
            }
            return result;
        }
        function expand(value) {
            for (const {state, values} of states()) {
                for (const key of Object.keys(values)) state[key] = key === 'collapsed' || key === 'sidebarCollapsed' ? false : value;
            }
        }
        function focusable() {
            return [...menu.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex="0"]')]
                .filter(el => !el.closest('[inert]') && el.getAttribute('aria-disabled') !== 'true' && el.getClientRects().length && getComputedStyle(el).visibility !== 'hidden');
        }
        function openMenu(trigger = button) {
            if (!mobile.matches) return;
            returnFocus = trigger;
            opened = true;
            expand(true);
            if (account) menu.hidden = false;
            menu.inert = false;
            menu.classList.add('mobile-navigation-open');
            menu.setAttribute('role', 'dialog');
            menu.setAttribute('aria-modal', 'true');
            if (!menu.hasAttribute('aria-label')) menu.setAttribute('aria-label', 'Menu aplikasi');
            menu.setAttribute('tabindex', '-1');
            button.setAttribute('aria-expanded', 'true');
            backdrop.hidden = false;
            body.classList.add('mobile-navigation-is-open');
            closeFocusTarget.focus({preventScroll:true});
            const focusMenu = () => requestAnimationFrame(() => { if (opened) closeFocusTarget.focus(); });
            if (window.Alpine) window.Alpine.nextTick(focusMenu); else focusMenu();
        }
        function closeMenu(restoreFocus = true) {
            opened = false;
            if (mobile.matches) expand(false);
            menu.classList.remove('mobile-navigation-open');
            if (account) menu.hidden = true;
            menu.inert = mobile.matches && !account ? true : originalInert;
            if (originalRole === null) menu.removeAttribute('role'); else menu.setAttribute('role', originalRole);
            menu.removeAttribute('aria-modal');
            if (originalTabindex === null) menu.removeAttribute('tabindex'); else menu.setAttribute('tabindex', originalTabindex);
            button.setAttribute('aria-expanded', 'false');
            backdrop.hidden = true;
            body.classList.remove('mobile-navigation-is-open');
            if (restoreFocus && mobile.matches) (returnFocus?.isConnected ? returnFocus : button).focus();
        }

        // Reserve height in viewport-bound shells; normal pages keep natural document scrolling.
        document.querySelectorAll('.h-screen,.mp-shell,.eo-dashboard-shell,.sitkom-shell').forEach(el => {
            if (el.closest('[data-mobile-sidebar]') || el.parentElement?.closest('[data-mobile-frame]')) return;
            el.dataset.mobileFrame = '';
        });
        if (!document.querySelector('[data-mobile-frame]')) body.dataset.mobileNatural = '';
        document.querySelectorAll('[x-show]').forEach(el => {
            if (/^(sidebarOpen|mobileSidebar)$/.test(el.getAttribute('x-show')?.trim()) && el.classList.contains('fixed') && !el.hasAttribute('data-mobile-sidebar')) el.dataset.mobileLegacyBackdrop = '';
        });
        document.querySelectorAll('.mp-sidebar-backdrop,.eo-sidebar-backdrop').forEach(el => el.dataset.mobileLegacyBackdrop = '');

        function prepareContent(root = document) {
            root.querySelectorAll('table').forEach(table => {
                if (table.closest('[data-mobile-sidebar],.mobile-table-scroll,[role="dialog"],.modal') || table.closest('table') !== table) return;
                // Reuse only a dedicated table wrapper. A scrolling page/card must not
                // make its headings and forms scroll horizontally with a wide table.
                const parent = table.parentElement;
                if (parent.children.length === 1 && ['auto','scroll'].includes(getComputedStyle(parent).overflowX)
                    && !parent.matches('main,[data-mobile-frame],.mp-box-body,.simenma-main,.sitkom-main')) return;
                const wrap = document.createElement('div');
                wrap.className = 'mobile-table-scroll';
                wrap.tabIndex = 0;
                wrap.setAttribute('role','region');
                wrap.setAttribute('aria-label','Tabel, geser untuk melihat kolom lainnya');
                table.before(wrap);
                wrap.append(table);
            });
            root.querySelectorAll('main form,.simenma-main form,.mp-box-body form,.mp-page-header,.eo-page-header').forEach(el => {
                if (!el.closest('[data-mobile-sidebar]') && getComputedStyle(el).display === 'flex') el.dataset.mobileToolbar = '';
            });
            root.querySelectorAll('main .grid,.simenma-main .grid,.mp-box-body .grid').forEach(el => {
                const classes = el.className || '';
                // Only unqualified multi-column grids without responsive variants; data tables retain horizontal scrolling.
                if (/\bgrid-cols-[2-9]\b/.test(classes) && !/(?:sm|md|lg|xl):grid-cols-/.test(classes) && !el.closest('table,[role="grid"],.calendar,.fc') && !el.style.gridTemplateColumns) el.dataset.mobileGrid = '';
            });
        }
        prepareContent();
        let enteredMobile = false;
        function viewportChanged() {
            if (mobile.matches && !enteredMobile) {
                snapshots = states();
                enteredMobile = true;
                closeMenu(false);
            } else if (!mobile.matches && enteredMobile) {
                closeMenu(false);
                enteredMobile = false;
                for (const {state, values} of snapshots) Object.assign(state, values);
                menu.inert = originalInert;
            }
        }
        // Alpine may be deferred independently by a module.
        viewportChanged();
        document.addEventListener('alpine:initialized', () => {
            if (mobile.matches) { snapshots = states(); expand(false); }
        }, {once:true});
        mobile.addEventListener('change', viewportChanged);
        button.addEventListener('click', () => opened ? closeMenu() : openMenu());
        backdrop.addEventListener('click', () => closeMenu());
        if (closeButton) closeButton.addEventListener('click', () => closeMenu());
        menu.addEventListener('transitionend', () => {
            if (opened && !menu.contains(document.activeElement)) closeFocusTarget.focus({preventScroll:true});
        });
        document.addEventListener('keydown', event => {
            if (!opened) return;
            if (event.key === 'Escape') { event.preventDefault(); event.stopImmediatePropagation(); closeMenu(); }
            if (event.key === 'Tab') {
                const targets = focusable(), first = targets[0] || menu, last = targets.at(-1) || menu;
                if (event.shiftKey && (document.activeElement === first || !menu.contains(document.activeElement))) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && (document.activeElement === last || !menu.contains(document.activeElement))) { event.preventDefault(); first.focus(); }
            }
        }, true);
        document.addEventListener('focusin', event => {
            if (opened && !menu.contains(event.target)) closeFocusTarget.focus();
        });
        document.addEventListener('click', event => {
            if (!mobile.matches || !sidebar) return;
            const trigger = event.target.closest('button');
            if (!trigger || trigger.closest('#mobile-navigation') || trigger.hasAttribute('data-mobile-close')) return;
            const action = trigger.getAttribute('@click') || trigger.getAttribute('x-on:click') || '';
            if (/^(?:sidebarOpen\s*=\s*(?:!sidebarOpen|true|false)|mobileSidebar\s*=\s*!mobileSidebar|sidebarCollapsed\s*=\s*!sidebarCollapsed|toggleSidebar|open\s*=\s*!open)\s*;?$/.test(action.trim())
                || trigger.matches('.mp-mobile-menu,.mp-sidebar-toggle,.eo-mobile-menu,.eo-sidebar-toggle,.sidebar-toggle')) {
                // Portal account dropdowns also use "open"; only intercept it inside the actual sidebar.
                if (/^open\s*=/.test(action) && !sidebar.contains(trigger)) return;
                event.preventDefault(); event.stopImmediatePropagation();
                opened ? closeMenu() : openMenu(trigger);
            }
        }, true);
        document.addEventListener('livewire:navigated', () => { closeMenu(false); prepareContent(); });
        window.addEventListener('pageshow', event => { if (event.persisted && opened) closeMenu(false); });
        window.MobileNavigation = {open:openMenu, close:closeMenu, prepareContent};
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, {once:true}); else boot();
})();

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
        const menu = document.getElementById('mobile-account-menu');
        const moduleLinks = menu.querySelector('[data-mobile-module-links]');
        const body = document.body;
        body.dataset.mobileShell = '';
        // Keep the dialog outside Alpine shells with transforms or overflow clipping.
        body.append(backdrop, menu);
        let opened = false, returnFocus = button;
        const closeButton = menu.querySelector('[data-mobile-close]');
        const backgroundStates = new Map();

        function buildModuleMenu() {
            moduleLinks.replaceChildren();
            if (!sidebar) return;
            const seen = new Set();
            const accountUrls = new Set([...menu.querySelectorAll('a[href]')].map(link => link.href));
            // The server-rendered navigation is the role authority. Never infer access
            // from a role name or manufacture module URLs in the browser.
            sidebar.querySelectorAll('a').forEach(source => {
                if (source.querySelector('img') && !source.closest('nav')) return;
                const href = source.getAttribute('href');
                const disabled = source.matches('[aria-disabled="true"],.is-disabled,.pointer-events-none')
                    || !!source.closest('[aria-disabled="true"],.is-disabled,.pointer-events-none')
                    || getComputedStyle(source).pointerEvents === 'none';
                if (!disabled && (!href || href === '#' || !['http:', 'https:'].includes(source.protocol))) return;
                if (source.closest('form') || (!disabled && accountUrls.has(source.href))) return;
                const labelNode = source.querySelector('.sb-item-label,.nav-text,.nav-label')
                    || [...source.querySelectorAll('span:not([aria-hidden]):not([x-text])')].find(el => el.textContent.trim());
                const label = (labelNode?.textContent || source.textContent || source.getAttribute('aria-label') || source.title).trim().replace(/\s+/g, ' ');
                const pathRole = href ? new URL(source.href).pathname.match(/\/capstone\/(admin|dosen|mahasiswa)\//)?.[1] : null;
                const roleSection = source.closest('section[aria-label]')?.getAttribute('aria-label')
                    || (pathRole && menu.querySelectorAll('.mobile-menu-roles > span').length > 1 ? pathRole[0].toUpperCase() + pathRole.slice(1) : '');
                const key = `${href}|${label}|${roleSection}`;
                if (!label || seen.has(key)) return;
                seen.add(key);
                const link = document.createElement('a');
                if (disabled) {
                    link.setAttribute('aria-disabled', 'true');
                    link.setAttribute('role', 'link');
                    if (source.title) link.title = source.title;
                } else {
                    link.href = source.href;
                    if (source.target) link.target = source.target;
                    if (source.rel) link.rel = source.rel;
                    if (source.hasAttribute('download')) link.setAttribute('download', source.getAttribute('download'));
                    if (source.getAttribute('aria-current') === 'page' || source.matches('.is-active,.active,.bg-sidebar-accent') || source.href === location.href) link.setAttribute('aria-current', 'page');
                }
                const icon = source.querySelector('svg');
                if (icon) {
                    const copy = icon.cloneNode(true);
                    // Icons must not carry Alpine bindings, IDs, or desktop-only styling.
                    [copy, ...copy.querySelectorAll('*')].forEach(el => {
                        [...el.attributes].forEach(attr => {
                            if (/^(x-|:|@|on)/.test(attr.name) || ['id', 'class', 'style'].includes(attr.name)) el.removeAttribute(attr.name);
                        });
                    });
                    copy.setAttribute('aria-hidden', 'true');
                    link.append(copy);
                }
                const text = document.createElement('span');
                text.textContent = label;
                if (roleSection) {
                    const role = document.createElement('small');
                    role.textContent = roleSection.replace(/^Menu\s+/, '');
                    text.append(role);
                }
                link.append(text);
                moduleLinks.append(link);
            });
            menu.querySelector('[data-mobile-module-section]').hidden = !moduleLinks.children.length;
        }
        function focusable() {
            return [...menu.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex="0"]')]
                .filter(el => !el.closest('[inert]') && el.getAttribute('aria-disabled') !== 'true' && el.getClientRects().length && getComputedStyle(el).visibility !== 'hidden');
        }
        function openMenu(trigger = button) {
            if (!mobile.matches) return;
            if (opened) return;
            buildModuleMenu();
            returnFocus = trigger;
            opened = true;
            menu.hidden = false;
            menu.inert = false;
            menu.querySelector('.mobile-menu-scroll').scrollTop = 0;
            // Make the background unavailable to keyboard and assistive technology.
            [...body.children].filter(el => el !== menu && el !== backdrop).forEach(el => {
                backgroundStates.set(el, el.inert);
                el.inert = true;
            });
            button.setAttribute('aria-expanded', 'true');
            backdrop.hidden = false;
            body.classList.add('mobile-navigation-is-open');
            closeButton.focus({preventScroll:true});
            menu.getBoundingClientRect(); // Commit the starting position before sliding upward.
            requestAnimationFrame(() => { if (opened) menu.classList.add('mobile-navigation-open'); });
        }
        function closeMenu(restoreFocus = true) {
            opened = false;
            menu.classList.remove('mobile-navigation-open');
            menu.hidden = true;
            for (const [el, inert] of backgroundStates) el.inert = inert;
            backgroundStates.clear();
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
        document.querySelectorAll('button').forEach(trigger => {
            if (trigger.closest('#mobile-navigation,#mobile-account-menu')) return;
            const action = trigger.getAttribute('@click') || trigger.getAttribute('x-on:click') || '';
            if (/^(?:sidebarOpen\s*=|mobileSidebar\s*=|sidebarCollapsed\s*=|toggleSidebar\b)/.test(action.trim())
                || trigger.matches('.mp-mobile-menu,.mp-sidebar-toggle,.eo-mobile-menu,.eo-sidebar-toggle,.sidebar-toggle')) trigger.dataset.mobileSidebarTrigger = '';
        });

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
        function viewportChanged() {
            if (!mobile.matches && opened) closeMenu(false);
        }
        viewportChanged();
        mobile.addEventListener('change', viewportChanged);
        button.addEventListener('click', () => opened ? closeMenu() : openMenu());
        backdrop.addEventListener('click', () => closeMenu());
        closeButton.addEventListener('click', () => closeMenu());
        menu.addEventListener('click', event => {
            if (event.target.closest('a[href]:not([aria-disabled="true"])')) closeMenu(false);
        });
        menu.addEventListener('transitionend', () => {
            if (opened && !menu.contains(document.activeElement)) closeButton.focus({preventScroll:true});
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
            if (opened && !menu.contains(event.target)) closeButton.focus();
        });
        document.addEventListener('click', event => {
            if (!mobile.matches || !sidebar) return;
            const trigger = event.target.closest('button');
            if (!trigger || trigger.closest('#mobile-navigation,#mobile-account-menu') || trigger.hasAttribute('data-mobile-close')) return;
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

(function () {
    window.userPermissionEditor = function (roles, permissions, availablePermissions) {
        return {
            open: false,
            selected: [...roles],
            permissions: [...permissions],
            availablePermissions: [...availablePermissions],
            restrictedModules() {
                if (this.selected.some(role => ['superadmin', 'admin'].includes(role))) return null;
                const admins = this.selected.filter(role => role.startsWith('admin_'));
                return admins.length
                    ? [...new Set(admins.flatMap(role => window.superadminRoleModules[role] || []))]
                    : null;
            },
            moduleAllowed(module) {
                const modules = this.restrictedModules();
                return modules === null || modules.includes(module);
            },
            refreshPermissions(fillDefaults = false) {
                this.permissions = this.permissions.filter(name =>
                    this.availablePermissions.includes(name) && this.moduleAllowed(name.split('.')[0])
                );
                if (fillDefaults && this.restrictedModules() === null &&
                    this.selected.some(role => ['dosen', 'mahasiswa', 'superadmin', 'admin'].includes(role))) {
                    this.permissions = [...this.availablePermissions];
                }
            },
            init() {
                this.refreshPermissions(this.permissions.length === 0);
            },
            addRole(role) {
                if (!this.selected.includes(role)) this.selected.push(role);
                this.refreshPermissions(true);
                this.open = false;
            },
            removeRole(role) {
                this.selected = this.selected.filter(value => value !== role);
                this.refreshPermissions(true);
            },
            modulePermissions(module) {
                return this.availablePermissions.filter(name => name.startsWith(module + '.'));
            },
            moduleSelected(module) {
                const names = this.modulePermissions(module);
                return names.length > 0 && names.every(name => this.permissions.includes(name));
            },
            modulePartiallySelected(module) {
                return !this.moduleSelected(module) && this.modulePermissions(module).some(name => this.permissions.includes(name));
            },
            toggleModule(module, checked) {
                if (!this.moduleAllowed(module)) return;
                const names = this.modulePermissions(module);
                this.permissions = this.permissions.filter(name => !names.includes(name));
                if (checked) this.permissions.push(...names);
            }
        };
    };
})();

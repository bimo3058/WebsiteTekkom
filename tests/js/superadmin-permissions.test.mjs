import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';

const window = {
    superadminRoleModules: {
        admin_capstone: ['capstone'], admin_banksoal: ['banksoal'],
        admin_eoffice: ['eoffice'], admin_kemahasiswaan: ['kemahasiswaan']
    }
};
vm.runInNewContext(fs.readFileSync(new URL('../../public/js/superadmin-permissions.js', import.meta.url), 'utf8'), { window });
const names = ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'].flatMap(module => ['view', 'edit', 'delete'].map(action => `${module}.${action}`));
const editor = (roles, selected = []) => window.userPermissionEditor(roles, selected, names);

test('switching from academic to module admin immediately clears and locks other modules', () => {
    const state = editor(['mahasiswa'], names);
    state.addRole('admin_capstone');
    assert.deepEqual(Array.from(state.permissions), names.filter(name => name.startsWith('capstone.')));
    assert.equal(state.moduleAllowed('banksoal'), false);
    assert.equal(state.moduleAllowed('capstone'), true);
    state.toggleModule('banksoal', true);
    assert.equal(state.permissions.length, 3);
});

test('existing stale administrator permissions are pruned on initialization', () => {
    const state = editor(['admin_eoffice'], names);
    state.init();
    assert.deepEqual(Array.from(state.permissions), names.filter(name => name.startsWith('eoffice.')));
});

test('main lecturer and student roles automatically fill all modules', () => {
    for (const role of ['dosen', 'mahasiswa']) {
        const state = editor([]);
        state.addRole(role);
        assert.deepEqual(Array.from(state.permissions), names);
    }
});

test('select all reflects individual selections and toggles only its own module', () => {
    const state = editor(['dosen'], ['capstone.view']);
    assert.equal(state.moduleSelected('capstone'), false);
    assert.equal(state.modulePartiallySelected('capstone'), true);
    state.toggleModule('capstone', true);
    assert.equal(state.moduleSelected('capstone'), true);
    state.toggleModule('capstone', false);
    assert.equal(state.permissions.length, 0);
});

test('multiple administrator roles unlock their combined modules and removal clears old scope', () => {
    const state = editor(['admin_capstone', 'admin_eoffice'], names);
    state.init();
    assert.equal(state.permissions.length, 6);
    state.removeRole('admin_capstone');
    assert.equal(state.permissions.length, 3);
    assert.equal(state.moduleAllowed('capstone'), false);
});

test('removing module admin restores academic defaults and global superadmin bypasses scope', () => {
    const state = editor(['mahasiswa', 'admin_capstone'], names);
    state.init();
    state.removeRole('admin_capstone');
    assert.equal(state.permissions.length, 12);
    state.addRole('admin_capstone');
    state.addRole('superadmin');
    assert.equal(state.permissions.length, 12);
});

test('module status does not prevent allowed permissions from being configured', () => {
    const state = editor(['admin_capstone']);
    state.init();
    state.toggleModule('capstone', true);
    assert.equal(state.permissions.length, 3);
    state.toggleModule('capstone', false);
    assert.equal(state.permissions.length, 0);
});

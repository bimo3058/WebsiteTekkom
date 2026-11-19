import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
import test from 'node:test';

const source = readFileSync(new URL('../../resources/views/manajemen-praktikum/koordinator/daftar-praktikan.blade.php', import.meta.url), 'utf8');
function picker() {
    let factory;
    const participants = Array.from({length: 300}, (_, i) => ({id: String(i), nama: `Mahasiswa ${i}`, nim: `NIM${i}`, kel: i < 250 ? '' : '2', shift: '1'}));
    const script = source.match(/<script>\s*([\s\S]*?)<\/script>/)[1];
    vm.runInNewContext(script, {
        document: {addEventListener: (_, fn) => fn(), getElementById: () => ({textContent: JSON.stringify({praktikans: participants})})},
        Alpine: {data: (_, fn) => {factory = fn;}},
    });
    return factory();
}

test('all students remain searchable and selection does not remove a suggestion', () => {
    const p = picker();
    p.openEditModal('1', '1');
    assert.equal(p.availableSections.length, 1);
    assert.equal(p.availableSections[0].items.length, 300);
    p.searchQuery = 'NIM299';
    assert.equal(p.availableSections[0].items[0].id, '299');
    p.selectedMembers.push('299');
    assert.equal(p.availableSections[0].items[0].id, '299');
    p.searchQuery = 'unrelated';
    assert.equal(p.memberPraktikans[0].id, '299');
});

test('removed members are searchable again', () => {
    const p = picker();
    p.openEditModal('2', '1');
    assert.equal(p.selectedMembers.length, 50);
    p.selectedMembers = p.selectedMembers.filter(id => id !== '299');
    p.searchQuery = 'NIM299';
    assert.equal(p.availableSections[0].items[0].id, '299');
    assert.ok(source.includes('max-height:216px'));
    assert.ok(!source.includes('BELUM MEMILIKI KELOMPOK'));
});

test('recommendation uses all participants and configured draft groups, not selected members', () => {
    const p = picker();
    p.groupCount = 6;
    p.allPraktikans = p.allPraktikans.slice(0, 32);
    assert.equal(p.memberRecommendation, 'Rekomendasi: 5–6 anggota');
    p.selectedMembers = ['1', '2'];
    assert.equal(p.memberRecommendation, 'Rekomendasi: 5–6 anggota');
    p.allPraktikans = p.allPraktikans.slice(0, 30);
    assert.equal(p.memberRecommendation, 'Rekomendasi: 5 anggota');
    p.groupCount = 0;
    assert.equal(p.memberRecommendation, 'Rekomendasi: — anggota');
    p.groupCount = 6;
    p.allPraktikans = [];
    assert.equal(p.memberRecommendation, 'Rekomendasi: — anggota');
});

<x-capstone::dialog id="schedule-detail" title="Detail Jadwal" class="sm:max-w-[520px]">
    <template x-if="selected"><div class="space-y-4 py-4">
        <div class="flex items-center gap-2"><span class="rounded-md px-2.5 py-1 text-xs font-medium" :class="color(selected.type)" x-text="label(selected.type)"></span><span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusColor(selected.status)" x-text="selected.status"></span></div>
        <h3 class="text-lg font-semibold" x-text="selected.group?.title?.title || 'Tanpa Judul'"></h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div><p class="text-sm text-muted-foreground">Tanggal &amp; Waktu</p><p class="font-medium" x-text="date(selected.date)+' '+time(selected)"></p></div>
            <div><p class="text-sm text-muted-foreground">Lokasi</p><p class="font-medium break-all" x-text="selected.room || selected.mode || '—'"></p></div>
            <div><p class="text-sm text-muted-foreground">Periode</p><p x-text="selected.period_name || '—'"></p></div>
            <div x-show="selected.mode"><p class="text-sm text-muted-foreground">Mode</p><p class="capitalize" x-text="selected.mode"></p></div>
            <div x-show="students(selected)"><p class="text-sm text-muted-foreground">Mahasiswa</p><p x-text="students(selected)"></p></div>
            <div x-show="selected.group?.members?.length"><p class="text-sm text-muted-foreground">Anggota Kelompok</p><p x-text="selected.group?.members?.map(m=>m.student?.name).join(', ')"></p></div>
        </div>
        <div x-show="examiners(selected)"><p class="text-sm text-muted-foreground">Penguji</p><p x-text="examiners(selected)"></p></div>
        <p x-show="selected.notes" class="whitespace-pre-wrap text-sm text-muted-foreground" x-text="selected.notes"></p>
        <p x-show="selected.rejection_reason" class="rounded-md bg-red-50 p-3 text-sm text-red-700" x-text="selected.rejection_reason"></p>
        <a x-show="selected.online_link || (selected.mode==='online' && safeLink(selected.room)!=='#')" :href="safeLink(selected.online_link || selected.room)" target="_blank" rel="noopener noreferrer" class="text-primary underline">Gabung Pertemuan</a>
        <div class="flex flex-wrap justify-end gap-2">
            @if($activeRole==='dosen' && ($allowManage ?? true))
            <x-capstone::button variant="outline" x-show="canEdit(selected)" @click="document.getElementById('schedule-detail').close();edit(selected)"><x-capstone::icon name="Edit" />Ubah</x-capstone::button>
            <x-capstone::button variant="destructive" x-show="canEdit(selected)" @click="document.getElementById('schedule-detail').close();confirmDelete(selected)"><x-capstone::icon name="Trash2" />Hapus</x-capstone::button>
            @endif
            <x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Tutup</x-capstone::button>
        </div>
    </div></template>
</x-capstone::dialog>

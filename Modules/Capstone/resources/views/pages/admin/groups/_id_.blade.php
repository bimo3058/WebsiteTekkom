@extends('capstone::layouts.app')
@section('title','Detail Kelompok')
@section('content')
<div x-data="adminGroups(true)" class="space-y-6">
    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error && group" x-cloak class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-capstone::button href="/admin/groups" variant="outline" class="text-slate-500"><x-capstone::icon name="ChevronLeft" size="16" /> Kembali</x-capstone::button>
            <x-capstone::button variant="destructive" @click="openDelete()"><x-capstone::icon name="Trash2" size="16" /> Hapus Grup</x-capstone::button>
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900" x-text="group.code || ('Group '+group.id)"></h1>

        <div class="grid gap-x-10 gap-y-4 md:grid-cols-2">
            <div class="space-y-4">
                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Periode</p>
                    <p class="text-sm font-medium text-slate-800" x-text="group.period?.name || '—'"></p>
                </div>
                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Group Status</p>
                    <p><span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="groupStatusClass(group.status)" x-text="groupStatusLabel(group)"></span></p>
                </div>
                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Progress</p>
                    <p class="flex items-center gap-2 text-sm font-medium text-slate-800"><span x-text="progressPct+'%'"></span><span class="h-2 w-32 overflow-hidden rounded-full bg-slate-200"><span class="block h-full rounded-full bg-[#2f3d8a]" :style="'width:'+progressPct+'%'"></span></span></p>
                </div>
                <div class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Ketua Kelompok</p>
                    <template x-if="ketua(group)">
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-1.5">
                            <input type="checkbox" :checked="selectedMembers.includes(ketua(group).id)" @change="toggleMember(ketua(group).id)" class="ml-1 h-3.5 w-3.5 rounded border-slate-300" aria-label="Pilih ketua">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(memberName(ketua(group)))"></span>
                            <span class="text-[13px] font-medium text-slate-800" x-text="memberName(ketua(group))"></span>
                            <button type="button" @click="openFlag(ketua(group))" class="rounded-full p-1 text-slate-300 hover:bg-red-50 hover:text-red-500" :aria-label="'Flag '+memberName(ketua(group))" title="Flag mahasiswa"><x-capstone::icon name="Flag" size="13" /></button>
                        </span>
                    </template>
                </div>
                <div class="grid grid-cols-[130px_1fr] items-start gap-3">
                    <p class="pt-1 text-sm text-slate-500">Team Members</p>
                    <span class="flex flex-wrap gap-2">
                        <template x-for="m in (group.members||[]).filter(m=>!m.is_leader)" :key="m.id">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-1 pr-1.5">
                                <input type="checkbox" :checked="selectedMembers.includes(m.id)" @change="toggleMember(m.id)" class="h-3.5 w-3.5 rounded border-slate-300" :aria-label="'Pilih '+memberName(m)">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(memberName(m))"></span>
                                <span class="text-[13px] font-medium text-slate-800" x-text="memberName(m)"></span>
                                <button type="button" @click="openFlag(m)" class="rounded-full p-1 text-slate-300 hover:bg-red-50 hover:text-red-500" :aria-label="'Flag '+memberName(m)" title="Flag mahasiswa"><x-capstone::icon name="Flag" size="13" /></button>
                            </span>
                        </template>
                        <span x-show="!(group.members||[]).filter(m=>!m.is_leader).length" class="text-sm text-slate-300">—</span>
                    </span>
                </div>
                <div x-show="selectedMembers.length" class="grid grid-cols-[130px_1fr] items-center gap-3">
                    <span></span>
                    <span><x-capstone::button size="sm" @click="openMessage()"><x-capstone::icon name="Send" size="14" /> Pesan (<span x-text="selectedMembers.length"></span>)</x-capstone::button></span>
                </div>
                <div x-show="(group.flagged_members||[]).length" class="grid grid-cols-[130px_1fr] items-start gap-3">
                    <p class="pt-1 text-sm text-red-500">Flagged</p>
                    <span class="flex flex-wrap gap-2">
                        <template x-for="m in group.flagged_members" :key="m.id">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 py-0.5 pl-1 pr-1.5">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-200 text-[10px] font-bold text-red-600" x-text="initials(memberName(m))"></span>
                                <span class="text-[13px] font-medium text-red-700" x-text="memberName(m)"></span>
                                <button type="button" @click="openUnflag(m)" class="rounded-full p-1 text-red-400 hover:bg-red-100 hover:text-red-600" :aria-label="'Unflag '+memberName(m)" title="Kembalikan mahasiswa"><x-capstone::icon name="Undo2" size="13" /></button>
                            </span>
                        </template>
                    </span>
                </div>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-[150px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Dosen Pembimbing 1</p>
                    <span><span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(person(group.supervisor1))"></span>
                        <span class="text-[13px] font-medium text-slate-800" x-text="person(group.supervisor1)"></span>
                    </span></span>
                </div>
                <div class="grid grid-cols-[150px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Dosen Pembimbing 2</p>
                    <span><span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(person(group.supervisor2))"></span>
                        <span class="text-[13px] font-medium text-slate-800" x-text="person(group.supervisor2)"></span>
                    </span></span>
                </div>
                <div class="grid grid-cols-[150px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Created at</p>
                    <p class="text-sm font-medium text-slate-800" x-text="longDate(group.created_at)"></p>
                </div>
                <div class="grid grid-cols-[150px_1fr] items-center gap-3">
                    <p class="text-sm text-slate-500">Judul Capstone</p>
                    <p class="text-sm font-medium text-slate-800" x-text="group.title?.title || 'Belum ada judul'"></p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 p-3">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                <template x-for="ph in phases" :key="ph.phase">
                    <div class="rounded-xl border bg-white" :class="group.workflow?.current_phase===ph.phase ? 'border-[#2f3d8a] shadow-sm' : 'border-slate-200'">
                        <div class="flex items-center justify-between border-b border-slate-100 px-3.5 py-2.5">
                            <p class="text-sm font-bold text-slate-900" x-text="phaseLabel(ph.phase)"></p>
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-medium" :class="phasePill(ph.status)" x-text="phasePillLabel(ph.status)"></span>
                        </div>
                        <div class="space-y-1.5 px-3.5 py-3 text-[13px]">
                            <template x-for="doc in ph.documents" :key="doc.type">
                                <p class="flex items-center gap-1.5" :class="docRowClass(doc.status)">
                                    <x-capstone::icon name="CircleCheck" size="15" x-show="doc.status==='APPROVED'" class="text-[#2f3d8a]" />
                                    <x-capstone::icon name="CircleAlert" size="15" x-show="doc.status!=='APPROVED' && doc.status!=='missing'" class="text-amber-500" />
                                    <x-capstone::icon name="CircleCheck" size="15" x-show="doc.status==='missing'" class="text-slate-300" />
                                    <span class="font-medium" x-text="doc.type"></span>
                                </p>
                            </template>
                            <p x-show="!ph.documents.length" class="text-slate-300">—</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <h3 class="border-b border-slate-100 px-4 py-2.5 text-sm font-bold text-slate-900">Dokumen</h3>
                <div class="divide-y divide-slate-100">
                    <template x-for="doc in group.documents || []" :key="doc.id">
                        <div class="flex items-center justify-between gap-3 px-4 py-2.5 text-[13px]"><span class="text-slate-600" x-text="doc.phase+' / '+doc.document_type"></span><span class="rounded-full px-2 py-0.5 text-[11px] font-medium" :class="doc.status==='APPROVED' ? 'bg-emerald-50 text-emerald-600' : doc.status==='REJECTED' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600'" x-text="doc.status"></span></div>
                    </template>
                    <p x-show="!(group.documents||[]).length" class="px-4 py-6 text-center text-sm text-slate-400">Belum ada dokumen.</p>
                </div>
                <a :href="url('/admin/document-uploads')+'?group_id='+group.id" class="block px-4 py-2.5 text-[13px] font-medium text-[#2f3d8a] hover:underline">Lihat unggahan</a>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <h3 class="border-b border-slate-100 px-4 py-2.5 text-sm font-bold text-slate-900">Jadwal</h3>
                <div class="divide-y divide-slate-100">
                    <template x-for="schedule in group.schedules || []" :key="schedule.id">
                        <p class="px-4 py-2.5 text-[13px] text-slate-600" x-text="(schedule.date || '').slice(0,10)+' '+(schedule.start_time || '').slice(0,5)+' — '+(schedule.type || '')+' '+(schedule.room || '')"></p>
                    </template>
                    <p x-show="!(group.schedules||[]).length" class="px-4 py-6 text-center text-sm text-slate-400">Belum ada jadwal.</p>
                </div>
            </div>
        </div>
    </div>

    <x-capstone::dialog id="group-message" title="Kirim Pesan" description="Pesan terkirim sebagai notifikasi dalam aplikasi.">
        <div class="mt-4 space-y-4">
            <p class="text-sm text-slate-500">Kepada <span class="font-semibold text-slate-800" x-text="selectedMembers.length+' anggota'"></span></p>
            <textarea x-model="messageText" rows="4" maxlength="1000" class="w-full rounded-lg border border-slate-200 p-3 text-sm" placeholder="Tulis pesan..." aria-label="Isi pesan"></textarea>
            <div class="flex justify-end gap-2">
                <x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Batal</x-capstone::button>
                <x-capstone::button @click="sendMessage()" ::disabled="saving || !messageText.trim()">Kirim</x-capstone::button>
            </div>
        </div>
    </x-capstone::dialog>

    <x-capstone::dialog id="group-flag" title="Flag Mahasiswa">
        <div class="mt-4 space-y-4">
            <p class="text-sm text-slate-600">Tandai <span class="font-semibold text-slate-900" x-text="flagTarget ? memberName(flagTarget) : ''"></span> dari periode ini?</p>
            <p class="rounded-lg border border-red-200 bg-red-50 p-3 text-[13px] text-red-700">Mahasiswa akan dikeluarkan dari kelompok dan periode. Nilai yang sudah ada tetap tersimpan. Tindakan ini dapat dibatalkan lewat Unflag.</p>
            <textarea x-model="flagReason" rows="3" maxlength="1000" class="w-full rounded-lg border border-slate-200 p-3 text-sm" placeholder="Alasan flag (wajib)..." aria-label="Alasan flag"></textarea>
            <div class="flex justify-end gap-2">
                <x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Batal</x-capstone::button>
                <x-capstone::button variant="destructive" @click="sendFlag()" ::disabled="saving || !flagReason.trim()">Flag</x-capstone::button>
            </div>
        </div>
    </x-capstone::dialog>

    <x-capstone::dialog id="group-unflag" title="Kembalikan Mahasiswa">
        <div class="mt-4 space-y-4">
            <p class="text-sm text-slate-600">Kembalikan <span class="font-semibold text-slate-900" x-text="unflagTarget ? memberName(unflagTarget) : ''"></span> ke status aktif? Mahasiswa perlu bergabung kembali ke kelompok.</p>
            <div class="flex justify-end gap-2">
                <x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Batal</x-capstone::button>
                <x-capstone::button @click="sendUnflag()" ::disabled="saving">Kembalikan</x-capstone::button>
            </div>
        </div>
    </x-capstone::dialog>

    @include('capstone::partials.group-delete-dialogs')
</div>
@endsection

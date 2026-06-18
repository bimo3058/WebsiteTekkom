<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Pengalaman Kerja & Organisasi</h3>

    <div class="p-4 mb-6 rounded-xl border" style="background: var(--c-primary-subtle); border-color: rgba(11,38,110,0.12);">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-[20px] mt-0.5 flex-shrink-0" style="color: var(--c-primary);">info</span>
            <p class="text-sm" style="color: var(--c-fg-sec);">
                Data <strong style="color: var(--c-primary);">Pengalaman</strong> dan <strong style="color: var(--c-primary);">Kegiatan Mahasiswa</strong> diambil secara otomatis dari modul Manajemen Mahasiswa.
            </p>
        </div>
    </div>

    <div class="flex flex-col gap-6 mb-8">
        @if(auth()->user()->hasRole('alumni') || auth()->user()->hasRole('superadmin'))
        <div class="w-full">
            <h4 class="text-sm font-bold text-slate-600 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">work</span>
                Pengalaman dari Sistem (Alumni)
            </h4>
            <div class="space-y-3">
                <template x-for="(exp, index) in data.pengalaman_sync" :key="index">
                    <div class="rounded-xl p-4 flex justify-between items-start border" style="background: var(--c-primary-subtle); border-color: rgba(11,38,110,0.15);">
                        <div>
                            <h5 class="font-bold text-slate-800 text-sm" x-text="exp.posisi"></h5>
                            <p class="text-sm font-medium" style="color: var(--c-primary);" x-text="exp.perusahaan"></p>
                            <p class="text-xs text-slate-500 mt-1" x-text="`${exp.tahun_mulai} - ${exp.tahun_selesai || 'Sekarang'}`"></p>
                        </div>
                        <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md shrink-0" style="background: var(--c-primary-subtle); color: var(--c-primary); border: 1px solid rgba(11,38,110,0.15);">Auto-Sync</span>
                    </div>
                </template>
                <div x-show="data.pengalaman_sync.length === 0" class="text-sm text-slate-500 italic p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                    Tidak ada data karir alumni di sistem.
                </div>
            </div>
        </div>
        @endif

        <div class="w-full">
            <h4 class="text-sm font-bold text-slate-600 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">event</span>
                Kegiatan Mahasiswa (Auto)
            </h4>
            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2">
                <template x-for="(keg, index) in data.kegiatan_sync" :key="index">
                    <div class="rounded-xl p-4 flex justify-between items-start border" style="background: var(--c-primary-subtle); border-color: rgba(11,38,110,0.15);">
                        <div>
                            <h5 class="font-bold text-slate-800 text-sm" x-text="keg.nama"></h5>
                            <p class="text-sm text-slate-600">Peran: <span class="font-medium" x-text="keg.peran"></span></p>
                        </div>
                    </div>
                </template>
                <div x-show="data.kegiatan_sync.length === 0" class="text-sm text-slate-500 italic p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                    Belum ada riwayat kegiatan.
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Tambah Pengalaman Kerja / Magang (Opsional)</h4>
        
        <div class="space-y-3 mb-4">
            <template x-for="(exp, index) in data.cv.pengalaman_kerja" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="exp.posisi"></h5>
                        <p class="text-sm text-slate-600" x-text="exp.perusahaan"></p>
                        <p class="text-xs text-slate-500 my-1" x-text="`${exp.tahun_mulai} - ${exp.tahun_selesai || 'Sekarang'}`"></p>
                        <p class="text-sm text-slate-600 mt-2" x-text="exp.deskripsi"></p>
                    </div>
                    <button @click="removeExp(index)" class="text-red-400 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </template>
        </div>

        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label" style="font-size:11px">Posisi / Jabatan</label>
                    <input type="text" x-model="newExp.posisi" class="form-control">
                </div>
                <div>
                    <label class="form-label" style="font-size:11px">Perusahaan / Organisasi</label>
                    <input type="text" x-model="newExp.perusahaan" class="form-control">
                </div>
                <div>
                    <label class="form-label" style="font-size:11px">Tahun Mulai</label>
                    <input type="text" x-model="newExp.tahun_mulai" class="form-control">
                </div>
                <div>
                    <label class="form-label" style="font-size:11px">Tahun Selesai (Kosongkan jika masih)</label>
                    <input type="text" x-model="newExp.tahun_selesai" class="form-control">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label" style="font-size:11px">Deskripsi Pekerjaan (Opsional)</label>
                    <textarea x-model="newExp.deskripsi" rows="3" class="form-control"></textarea>
                </div>
            </div>
            <button @click="addExp()" class="btn-secondary text-xs">+ Tambah Pengalaman</button>
        </div>
    </div>
</div>

<div>
    <h3 class="cvb-title">Pengalaman Kerja & Organisasi</h3>

    <div class="cvb-note">
        <span><x-icon name="information-circle" size="18" /></span>
        <p>
            Data <strong>Pengalaman</strong> dan <strong>Kegiatan Mahasiswa</strong> diambil secara otomatis dari modul Manajemen Mahasiswa.
        </p>
    </div>

    <div class="flex flex-col gap-6 mb-8">
        @if(auth()->user()->hasRole('alumni') || auth()->user()->hasRole('superadmin'))
        <div class="w-full">
            <h4 class="cvb-heading is-sync">
                <x-icon name="briefcase-01" size="18" />
                Pengalaman dari Sistem (Alumni)
            </h4>
            <div class="space-y-3">
                <template x-for="(exp, index) in data.pengalaman_sync" :key="index">
                    <div class="cvb-item is-sync">
                        <div>
                            <h5 class="cvb-item-title" x-text="exp.posisi"></h5>
                            <p class="cvb-item-sub" x-text="exp.perusahaan"></p>
                            <p class="cvb-item-meta" x-text="`${exp.tahun_mulai} - ${exp.tahun_selesai || 'Sekarang'}`"></p>
                        </div>
                        <span class="cvb-chip">Auto-Sync</span>
                    </div>
                </template>
                <div x-show="data.pengalaman_sync.length === 0" class="cvb-empty">
                    Tidak ada data karir alumni di sistem.
                </div>
            </div>
        </div>
        @endif

        <div class="w-full">
            <h4 class="cvb-heading is-sync">
                <x-icon name="calendar" size="18" />
                Kegiatan Mahasiswa (Auto)
            </h4>
            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2">
                <template x-for="(keg, index) in data.kegiatan_sync" :key="index">
                    <div class="cvb-item is-sync">
                        <div>
                            <h5 class="cvb-item-title" x-text="keg.nama"></h5>
                            <p class="cvb-item-desc" style="margin-top: 2px;">Peran: <span class="font-medium" x-text="keg.peran"></span></p>
                        </div>
                    </div>
                </template>
                <div x-show="data.kegiatan_sync.length === 0" class="cvb-empty">
                    Belum ada riwayat kegiatan.
                </div>
            </div>
        </div>
    </div>

    <div class="cvb-section">
        <h4 class="cvb-heading">Tambah Pengalaman Kerja / Magang (Opsional)</h4>

        <div class="space-y-3 mb-4">
            <template x-for="(exp, index) in data.cv.pengalaman_kerja" :key="index">
                <div class="cvb-item">
                    <div>
                        <h5 class="cvb-item-title" x-text="exp.posisi"></h5>
                        <p class="cvb-item-sub" x-text="exp.perusahaan"></p>
                        <p class="cvb-item-meta" x-text="`${exp.tahun_mulai} - ${exp.tahun_selesai || 'Sekarang'}`"></p>
                        <p class="cvb-item-desc" x-text="exp.deskripsi"></p>
                    </div>
                    <button type="button" @click="removeExp(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus pengalaman">
                        <x-icon name="trash" size="16" />
                    </button>
                </div>
            </template>
        </div>

        <div class="cvb-add">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label-custom">Posisi / Jabatan</label>
                    <input type="text" x-model="newExp.posisi" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Perusahaan / Organisasi</label>
                    <input type="text" x-model="newExp.perusahaan" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Tahun Mulai</label>
                    <input type="text" x-model="newExp.tahun_mulai" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Tahun Selesai (Kosongkan jika masih)</label>
                    <input type="text" x-model="newExp.tahun_selesai" class="form-control-custom">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label-custom">Deskripsi Pekerjaan (Opsional)</label>
                    <textarea x-model="newExp.deskripsi" rows="3" class="form-control-custom"></textarea>
                </div>
            </div>
            <button @click="addExp()" class="btn-secondary text-xs">+ Tambah Pengalaman</button>
        </div>
    </div>
</div>

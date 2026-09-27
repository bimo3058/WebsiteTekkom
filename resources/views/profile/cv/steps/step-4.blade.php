<div>
    <h3 class="cvb-title">Proyek & Sertifikasi & Prestasi</h3>

    <div class="cvb-note">
        <span><x-icon name="information-circle" size="18" /></span>
        <p>
            Data <strong>Prestasi</strong> diambil secara otomatis dari rekam jejak akademik Anda. Jika ada kesalahan atau prestasi yang belum tercatat, silakan tambahkan melalui modul Manajemen Mahasiswa.
        </p>
    </div>

    <!-- Auto-Sync Prestasi -->
    <div class="mb-8">
        <h4 class="cvb-heading is-sync">
            <x-icon name="trophy" size="18" />
            Prestasi dari Sistem Kemahasiswaan
        </h4>
        <div class="space-y-3">
            <template x-for="(p, index) in data.prestasi_sync" :key="index">
                <div class="cvb-item is-sync">
                    <div>
                        <h5 class="cvb-item-title" x-text="p.nama"></h5>
                        <p class="cvb-item-desc" style="margin-top: 4px;">
                            Tingkat: <span class="font-medium capitalize" style="color: var(--c-primary);" x-text="p.tingkat"></span>
                            <span class="mx-2" style="color: var(--c-border-strong);" aria-hidden="true">|</span>
                            Tahun <span x-text="p.tahun"></span>
                        </p>
                    </div>
                    <span class="cvb-chip">Auto-Sync</span>
                </div>
            </template>
            <div x-show="data.prestasi_sync.length === 0" class="cvb-empty">
                Belum ada data prestasi.
            </div>
        </div>
    </div>

    <div class="cvb-section mt-6">
        <h4 class="cvb-heading">Proyek Akademis / Pribadi</h4>

        <div class="space-y-3 mb-4">
            <template x-for="(proj, index) in data.cv.proyek" :key="index">
                <div class="cvb-item">
                    <div>
                        <h5 class="cvb-item-title" x-text="proj.nama"></h5>
                        <p class="cvb-item-sub" x-text="proj.peran"></p>
                        <p class="cvb-item-meta" x-text="proj.tahun"></p>
                        <p class="cvb-item-desc" x-text="proj.deskripsi"></p>
                        <a x-show="proj.tautan" :href="proj.tautan" target="_blank" class="cvb-item-link">Lihat Proyek</a>
                    </div>
                    <button type="button" @click="removeProj(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus proyek">
                        <x-icon name="trash" size="16" />
                    </button>
                </div>
            </template>
        </div>

        <div class="cvb-add">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label-custom">Nama Proyek</label>
                    <input type="text" x-model="newProj.nama" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Peran / Teknologi</label>
                    <input type="text" x-model="newProj.peran" class="form-control-custom" placeholder="Contoh: Backend Developer (Laravel, MySQL)">
                </div>
                <div>
                    <label class="form-label-custom">Tahun Pembuatan</label>
                    <input type="text" x-model="newProj.tahun" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Tautan URL (Opsional)</label>
                    <input type="url" x-model="newProj.tautan" class="form-control-custom" placeholder="https://github.com/...">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label-custom">Deskripsi Singkat</label>
                    <textarea x-model="newProj.deskripsi" rows="2" class="form-control-custom"></textarea>
                </div>
            </div>
            <button @click="addProj()" class="btn-secondary text-xs">+ Tambah Proyek</button>
        </div>
    </div>

    <div class="cvb-section mt-6">
        <h4 class="cvb-heading">Sertifikasi</h4>

        <div class="space-y-3 mb-4">
            <template x-for="(cert, index) in data.cv.sertifikasi" :key="index">
                <div class="cvb-item">
                    <div>
                        <h5 class="cvb-item-title" x-text="cert.nama"></h5>
                        <p class="cvb-item-sub" x-text="cert.penerbit"></p>
                        <p class="cvb-item-meta" x-text="cert.tahun"></p>
                    </div>
                    <button type="button" @click="removeCert(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus sertifikasi">
                        <x-icon name="trash" size="16" />
                    </button>
                </div>
            </template>
        </div>

        <div class="cvb-add">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="form-label-custom">Nama Sertifikasi / Pelatihan</label>
                    <input type="text" x-model="newCert.nama" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Lembaga Penerbit</label>
                    <input type="text" x-model="newCert.penerbit" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Tahun</label>
                    <input type="text" x-model="newCert.tahun" class="form-control-custom">
                </div>
            </div>
            <button @click="addCert()" class="btn-secondary text-xs">+ Tambah Sertifikasi</button>
        </div>
    </div>
</div>

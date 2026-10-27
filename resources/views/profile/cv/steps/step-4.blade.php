<div>
    @include('profile.cv.partials._judul', [
        'judul' => 'Proyek & Sertifikasi & Prestasi',
        'info' => auth()->user()->hasRole('alumni')
            ? 'Data <strong>Prestasi</strong> diambil secara otomatis dari prestasi Anda yang sudah disetujui. Daftar lengkapnya ada di {prestasi}.'
            : 'Data <strong>Prestasi</strong> diambil secara otomatis dari prestasi Anda yang sudah disetujui. Jika ada kesalahan atau prestasi yang belum tercatat, ajukan di {prestasi}.',
    ])

    <!-- Auto-Sync Prestasi -->
    <div class="mb-8">
        @include('profile.cv.partials._sync-list', [
            'judul' => 'Prestasi dari Sistem Kemahasiswaan',
            'items' => 'syncPrestasi',
            'kosong' => 'Belum ada data prestasi.',
        ])
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
            <div x-show="data.cv.proyek.length === 0" class="cvb-empty">
                Belum ada proyek.
            </div>
        </div>

        <div class="cvb-add" @keydown.enter="enterTambah($event, 'addProj')">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @include('profile.cv.partials._field', ['form' => 'newProj', 'name' => 'nama', 'label' => 'Nama Proyek', 'required' => true, 'placeholder' => 'Contoh: Sistem Informasi Perpustakaan'])
                @include('profile.cv.partials._field', ['form' => 'newProj', 'name' => 'peran', 'label' => 'Peran / Teknologi', 'placeholder' => 'Contoh: Backend Developer (Laravel, MySQL)'])
                @include('profile.cv.partials._field', ['form' => 'newProj', 'name' => 'tahun', 'label' => 'Tahun Pembuatan', 'year' => true, 'placeholder' => '2024'])
                @include('profile.cv.partials._field', ['form' => 'newProj', 'name' => 'tautan', 'label' => 'Tautan Proyek', 'type' => 'url', 'placeholder' => 'https://github.com/...'])
                <div class="md:col-span-2">
                    @include('profile.cv.partials._field', ['form' => 'newProj', 'name' => 'deskripsi', 'label' => 'Deskripsi Singkat', 'required' => true, 'rows' => 3, 'placeholder' => 'Apa yang dikerjakan dan hasilnya'])
                </div>
            </div>
            <div class="cvb-add-foot">
                <button type="button" @click="addProj()" class="cvb-add-btn">
                    <x-icon name="plus" size="14" />
                    Tambah Proyek
                </button>
            </div>
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
            <div x-show="data.cv.sertifikasi.length === 0" class="cvb-empty">
                Belum ada sertifikasi.
            </div>
        </div>

        <div class="cvb-add" @keydown.enter="enterTambah($event, 'addCert')">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @include('profile.cv.partials._field', ['form' => 'newCert', 'name' => 'nama', 'label' => 'Nama Sertifikasi / Pelatihan', 'required' => true, 'placeholder' => 'Contoh: Dasar Pemrograman Web'])
                @include('profile.cv.partials._pilih-lainnya', [
                    'form' => 'newCert', 'name' => 'penerbit', 'label' => 'Lembaga Penerbit', 'required' => true,
                    'placeholder' => 'Pilih lembaga', 'lainnyaHint' => 'Tulis nama lembaga',
                    'options' => ['Dicoding Indonesia', 'Cisco Networking Academy', 'Coursera', 'Udemy', 'Google', 'Microsoft', 'Amazon Web Services (AWS)', 'Oracle', 'MikroTik', 'BNSP', 'Digital Talent Scholarship (Komdigi)', 'Kampus Merdeka'],
                ])
                @include('profile.cv.partials._field', ['form' => 'newCert', 'name' => 'tahun', 'label' => 'Tahun', 'year' => true, 'placeholder' => '2024'])
            </div>
            <div class="cvb-add-foot">
                <button type="button" @click="addCert()" class="cvb-add-btn">
                    <x-icon name="plus" size="14" />
                    Tambah Sertifikasi
                </button>
            </div>
        </div>
    </div>
</div>

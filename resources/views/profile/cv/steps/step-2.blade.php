<div>
    @include('profile.cv.partials._judul', [
        'judul' => 'Pendidikan & Bahasa',
        'info' => 'Data dengan label <strong>Auto-Sync</strong> diambil secara otomatis dari rekam jejak akademik Anda. Jika ada yang keliru, periksa profil Anda di {direktori}.',
    ])

    <!-- Auto-Sync Edu -->
    <div class="mb-8">
        @include('profile.cv.partials._sync-list', [
            'judul' => 'Pendidikan dari Sistem',
            'items' => 'syncPendidikan',
            'kosong' => 'Belum ada data pendidikan di sistem.',
        ])
    </div>

    <div class="cvb-section">
        <h4 class="cvb-heading">Pendidikan Lain</h4>

        <!-- Manual Edu List -->
        <div class="space-y-3 mb-4">
            <template x-for="(edu, index) in data.cv.pendidikan" :key="index">
                <div class="cvb-item">
                    <div>
                        <h5 class="cvb-item-title" x-text="edu.institusi"></h5>
                        <p class="cvb-item-sub" x-text="edu.jurusan"></p>
                        <p class="cvb-item-meta" x-text="`${edu.tahun_masuk} - ${edu.tahun_lulus || 'Sekarang'}`"></p>
                    </div>
                    <button type="button" @click="removeEdu(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus pendidikan">
                        <x-icon name="trash" size="16" />
                    </button>
                </div>
            </template>
            <div x-show="data.cv.pendidikan.length === 0" class="cvb-empty">
                Belum ada pendidikan tambahan.
            </div>
        </div>

        <!-- Add Form — tampil setelah "Tambah Pendidikan" ditekan -->
        @include('profile.cv.partials._tambah-buka', ['form' => 'newEdu', 'label' => 'Tambah Pendidikan'])
        <div class="cvb-add" data-cvb-form="newEdu" x-show="terbuka.newEdu" style="display: none;" @keydown.enter="enterTambah($event, 'addEdu')">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @include('profile.cv.partials._field', ['form' => 'newEdu', 'name' => 'institusi', 'label' => 'Nama Institusi', 'required' => true, 'placeholder' => 'Contoh: SMA Negeri 1 Semarang'])
                @include('profile.cv.partials._field', ['form' => 'newEdu', 'name' => 'jurusan', 'label' => 'Jurusan / Bidang', 'required' => true, 'placeholder' => 'Contoh: IPA'])
                @include('profile.cv.partials._field', ['form' => 'newEdu', 'name' => 'tahun_masuk', 'label' => 'Tahun Masuk', 'year' => true, 'placeholder' => '2019'])
                @include('profile.cv.partials._field', ['form' => 'newEdu', 'name' => 'tahun_lulus', 'label' => 'Tahun Lulus', 'year' => true, 'placeholder' => '2022', 'hint' => 'Kosongkan jika belum lulus.'])
            </div>
            @include('profile.cv.partials._tambah-foot', ['form' => 'newEdu', 'metode' => 'addEdu'])
        </div>
    </div>

    <!-- Language Section -->
    <div class="cvb-section mt-6">
        <h4 class="cvb-heading">Kemampuan Bahasa</h4>

        <!-- Manual Lang List -->
        <div class="space-y-3 mb-4">
            <template x-for="(lang, index) in data.cv.bahasa" :key="index">
                <div class="cvb-item">
                    <div>
                        <h5 class="cvb-item-title" x-text="lang.nama"></h5>
                        <p class="cvb-item-level" x-text="lang.level"></p>
                        <p class="cvb-item-meta" x-show="lang.skor" x-text="`Skor / Nilai: ${lang.skor}`"></p>
                    </div>
                    <button type="button" @click="removeLang(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus bahasa">
                        <x-icon name="trash" size="16" />
                    </button>
                </div>
            </template>
            <div x-show="data.cv.bahasa.length === 0" class="cvb-empty">
                Belum ada kemampuan bahasa.
            </div>
        </div>

        <!-- Add Form — tampil setelah "Tambah Bahasa" ditekan -->
        @include('profile.cv.partials._tambah-buka', ['form' => 'newLang', 'label' => 'Tambah Bahasa'])
        <div class="cvb-add" data-cvb-form="newLang" x-show="terbuka.newLang" style="display: none;" @keydown.enter="enterTambah($event, 'addLang')">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @include('profile.cv.partials._pilih-lainnya', [
                    'form' => 'newLang', 'name' => 'nama', 'label' => 'Bahasa', 'required' => true,
                    'placeholder' => 'Pilih bahasa', 'lainnyaHint' => 'Tulis nama bahasa',
                    'options' => ['Indonesia', 'Inggris', 'Jawa', 'Mandarin', 'Jepang', 'Korea', 'Arab', 'Jerman', 'Prancis', 'Belanda', 'Spanyol', 'Rusia'],
                ])
                <div>
                    <label class="form-label-custom">Tingkat Kemahiran</label>
                    @include('profile.cv.partials._pilihan', [
                        'model' => 'newLang.level',
                        'options' => ['Dasar (Basic)', 'Menengah (Intermediate)', 'Fasih (Fluent)', 'Penutur Asli (Native)'],
                    ])
                </div>
                @include('profile.cv.partials._field', ['form' => 'newLang', 'name' => 'skor', 'label' => 'Skor / Nilai', 'placeholder' => 'Contoh: TOEFL 550'])
            </div>
            @include('profile.cv.partials._tambah-foot', ['form' => 'newLang', 'metode' => 'addLang'])
        </div>
    </div>
</div>

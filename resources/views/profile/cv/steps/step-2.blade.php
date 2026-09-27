<div>
    <h3 class="cvb-title">Pendidikan & Bahasa</h3>

    <div class="cvb-note">
        <span><x-icon name="information-circle" size="18" /></span>
        <p>
            Data dengan label <strong>Auto-Sync</strong> diambil secara otomatis dari rekam jejak akademik Anda. Jika ada kesalahan, silakan perbarui data Anda di menu Profil / Akademik terkait.
        </p>
    </div>

    <!-- Auto-Sync Edu -->
    <div class="mb-8">
        <h4 class="cvb-heading is-sync">
            <x-icon name="sync" size="18" />
            Pendidikan dari Sistem
        </h4>
        <div class="space-y-3">
            <template x-for="edu in data.pendidikan_sync" :key="edu.tahun_masuk">
                <div class="cvb-item is-sync">
                    <div>
                        <h5 class="cvb-item-title" x-text="edu.institusi"></h5>
                        <p class="cvb-item-sub" x-text="edu.jurusan"></p>
                        <p class="cvb-item-meta" x-text="`${edu.tahun_masuk} - ${edu.tahun_lulus || 'Sekarang'}`"></p>
                    </div>
                    <span class="cvb-chip">Auto-Sync</span>
                </div>
            </template>
            <div x-show="data.pendidikan_sync.length === 0" class="cvb-empty">
                Belum ada data pendidikan di sistem.
            </div>
        </div>
    </div>

    <div class="cvb-section">
        <h4 class="cvb-heading">Tambah Pendidikan Lain (Opsional)</h4>

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
        </div>

        <!-- Add Form -->
        <div class="cvb-add">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label-custom">Nama Institusi (SMA/Kursus)</label>
                    <input type="text" x-model="newEdu.institusi" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Jurusan / Bidang</label>
                    <input type="text" x-model="newEdu.jurusan" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Tahun Masuk</label>
                    <input type="text" x-model="newEdu.tahun_masuk" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Tahun Lulus (Kosongkan jika belum)</label>
                    <input type="text" x-model="newEdu.tahun_lulus" class="form-control-custom">
                </div>
            </div>
            <button @click="addEdu()" class="btn-secondary text-xs">
                + Tambah Pendidikan
            </button>
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
                        <p class="cvb-item-sub" style="color: var(--c-primary);" x-text="lang.level"></p>
                        <p class="cvb-item-meta" x-show="lang.skor" x-text="`Skor / Nilai: ${lang.skor}`"></p>
                    </div>
                    <button type="button" @click="removeLang(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus bahasa">
                        <x-icon name="trash" size="16" />
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="cvb-add">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="form-label-custom">Bahasa</label>
                    <input type="text" x-model="newLang.nama" placeholder="Contoh: Inggris" class="form-control-custom">
                </div>
                <div>
                    <label class="form-label-custom">Tingkat Kemahiran</label>
                    @include('profile.cv.partials._pilihan', [
                        'model' => 'newLang.level',
                        'options' => ['Dasar (Basic)', 'Menengah (Intermediate)', 'Fasih (Fluent)', 'Penutur Asli (Native)'],
                    ])
                </div>
                <div>
                    <label class="form-label-custom">Skor / Nilai (Opsional)</label>
                    <input type="text" x-model="newLang.skor" placeholder="Contoh: TOEFL 550" class="form-control-custom">
                </div>
            </div>
            <button @click="addLang()" class="btn-secondary text-xs">
                + Tambah Bahasa
            </button>
        </div>
    </div>
</div>

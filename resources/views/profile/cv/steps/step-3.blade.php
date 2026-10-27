<div>
    @php
        // Nama tautan ({kegiatan}, {arsip}, {direktori}) mengikuti sidebar SIMENMA — lihat
        // CvBuilderController::tautanSubmenuMm(). Alumni hanya melihat, tidak mengajukan.
        $infoLangkah3 = auth()->user()->hasRole('alumni')
            ? 'Data <strong>Pengalaman</strong> diambil dari profil Anda di {direktori}. Data <strong>Kegiatan Mahasiswa</strong> diambil dari {kegiatan} (yang sudah disetujui) dan {arsip} (kegiatan selesai tempat Anda menjadi ketua pelaksana atau panitia).'
            : 'Data <strong>Kegiatan Mahasiswa</strong> diambil secara otomatis dari {kegiatan} (yang sudah disetujui) dan {arsip} (kegiatan selesai tempat Anda menjadi ketua pelaksana atau panitia). Kegiatan yang belum tercatat bisa diajukan di {kegiatan}.';
    @endphp
    @include('profile.cv.partials._judul', [
        'judul' => 'Pengalaman Kerja & Organisasi',
        'info' => $infoLangkah3,
    ])

    <div class="flex flex-col gap-6 mb-8">
        @if(auth()->user()->hasRole('alumni') || auth()->user()->hasRole('superadmin'))
            @include('profile.cv.partials._sync-list', [
                'judul' => 'Pengalaman dari Sistem (Alumni)',
                'items' => 'syncPengalaman',
                'kosong' => 'Tidak ada data karir alumni di sistem.',
            ])
        @endif

        @include('profile.cv.partials._sync-list', [
            'judul' => 'Kegiatan Mahasiswa dari Sistem',
            'items' => 'syncKegiatan',
            'kosong' => 'Belum ada riwayat kegiatan.',
        ])
    </div>

    <div class="cvb-section">
        <h4 class="cvb-heading">Tambah Pengalaman Kerja / Magang</h4>

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
            <div x-show="data.cv.pengalaman_kerja.length === 0" class="cvb-empty">
                Belum ada pengalaman kerja atau magang.
            </div>
        </div>

        <div class="cvb-add" @keydown.enter="enterTambah($event, 'addExp')">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @include('profile.cv.partials._field', ['form' => 'newExp', 'name' => 'posisi', 'label' => 'Posisi / Jabatan', 'required' => true, 'placeholder' => 'Contoh: Asisten Laboratorium'])
                @include('profile.cv.partials._field', ['form' => 'newExp', 'name' => 'perusahaan', 'label' => 'Perusahaan / Organisasi', 'required' => true, 'placeholder' => 'Contoh: PT Teknologi Nusantara'])
                @include('profile.cv.partials._field', ['form' => 'newExp', 'name' => 'tahun_mulai', 'label' => 'Tahun Mulai', 'year' => true, 'placeholder' => '2024'])
                @include('profile.cv.partials._field', ['form' => 'newExp', 'name' => 'tahun_selesai', 'label' => 'Tahun Selesai', 'year' => true, 'placeholder' => '2025', 'hint' => 'Kosongkan jika masih berjalan.'])
                <div class="md:col-span-2">
                    @include('profile.cv.partials._field', ['form' => 'newExp', 'name' => 'deskripsi', 'label' => 'Deskripsi Pekerjaan', 'rows' => 3, 'placeholder' => 'Tanggung jawab dan hasil yang Anda capai'])
                </div>
            </div>
            <div class="cvb-add-foot">
                <button type="button" @click="addExp()" class="cvb-add-btn">
                    <x-icon name="plus" size="14" />
                    Tambah Pengalaman
                </button>
            </div>
        </div>
    </div>
</div>

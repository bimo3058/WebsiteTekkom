<div>
    @include('profile.cv.partials._judul', [
        'judul' => 'Keahlian (Skills)',
        'info' => 'Tambahkan keahlian teknis (hard skills) dan non-teknis (soft skills) yang relevan. Keahlian ini sangat penting untuk membantu sistem <strong>Applicant Tracking System (ATS)</strong> memfilter profil Anda.',
    ])

    <div>
        <h4 class="cvb-heading">Daftar Keahlian</h4>

        <!-- Manual Skill List — baris yang sama dengan Kemampuan Bahasa di langkah 2 -->
        <div class="space-y-3 mb-4">
            <template x-for="(skill, index) in data.cv.keahlian" :key="index">
                <div class="cvb-item">
                    <div>
                        <h5 class="cvb-item-title" x-text="skill.nama"></h5>
                        <p class="cvb-item-level" x-text="skill.level"></p>
                    </div>
                    <button type="button" @click="removeSkill(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus keahlian">
                        <x-icon name="trash" size="16" />
                    </button>
                </div>
            </template>
            <div x-show="data.cv.keahlian.length === 0" class="cvb-empty">
                Belum ada keahlian.
            </div>
        </div>

        <!-- Add Form -->
        <div class="cvb-add" @keydown.enter="enterTambah($event, 'addSkill')">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @include('profile.cv.partials._pilih-lainnya', [
                    'form' => 'newSkill', 'name' => 'nama', 'label' => 'Nama Keahlian', 'required' => true,
                    'placeholder' => 'Pilih keahlian', 'lainnyaHint' => 'Tulis nama keahlian',
                    'groups' => [
                        'Teknis (hard skills)' => ['Python', 'Java', 'JavaScript', 'PHP', 'Laravel', 'HTML & CSS', 'React', 'SQL / MySQL', 'C / C++', 'Git & GitHub', 'Linux', 'Jaringan Komputer', 'Arduino / Mikrokontroler', 'Internet of Things (IoT)', 'Machine Learning', 'Analisis Data', 'UI/UX Design', 'Microsoft Office'],
                        'Non-teknis (soft skills)' => ['Public Speaking', 'Kepemimpinan', 'Kerja Sama Tim', 'Komunikasi', 'Manajemen Waktu', 'Pemecahan Masalah', 'Berpikir Kritis'],
                    ],
                ])
                <div>
                    <label class="form-label-custom">Level</label>
                    @include('profile.cv.partials._pilihan', [
                        'model' => 'newSkill.level',
                        'options' => ['Beginner', 'Intermediate', 'Advanced', 'Expert'],
                    ])
                </div>
            </div>
            <div class="cvb-add-foot">
                <button type="button" @click="addSkill()" class="cvb-add-btn">
                    <x-icon name="plus" size="14" />
                    Tambah Keahlian
                </button>
            </div>
        </div>
    </div>
</div>

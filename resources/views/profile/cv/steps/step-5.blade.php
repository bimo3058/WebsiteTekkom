<div>
    <h3 class="cvb-title">Keahlian (Skills)</h3>

    <div class="cvb-note">
        <span><x-icon name="information-circle" size="18" /></span>
        <p>
            Tambahkan keahlian teknis (hard skills) dan non-teknis (soft skills) yang relevan. Keahlian ini sangat penting untuk membantu sistem <strong>Applicant Tracking System (ATS)</strong> memfilter profil Anda.
        </p>
    </div>

    <div class="cvb-section">
        <h4 class="cvb-heading">Daftar Keahlian</h4>

        <!-- Manual Skill List -->
        <div class="flex flex-wrap gap-3 mb-6">
            <template x-for="(skill, index) in data.cv.keahlian" :key="index">
                <div class="cvb-item" style="align-items: center; padding: 8px 8px 8px 16px;">
                    <div class="flex items-center gap-2">
                        <span class="cvb-item-title" x-text="skill.nama"></span>
                        <span class="cvb-chip is-neutral" x-text="skill.level"></span>
                    </div>
                    <button type="button" @click="removeSkill(index)" class="cvb-icon-btn" title="Hapus" aria-label="Hapus keahlian">
                        <x-icon name="close" size="14" />
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="cvb-add">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="form-label-custom">Nama Keahlian</label>
                    <input type="text" x-model="newSkill.nama" placeholder="Contoh: Laravel, Python, Public Speaking" class="form-control-custom">
                </div>
                <div class="w-full md:w-40">
                    <label class="form-label-custom">Level</label>
                    @include('profile.cv.partials._pilihan', [
                        'model' => 'newSkill.level',
                        'options' => ['Beginner', 'Intermediate', 'Advanced', 'Expert'],
                    ])
                </div>
                <button @click="addSkill()" class="btn-primary text-xs w-full md:w-auto justify-center" style="height: 38px;">
                    Tambah
                </button>
            </div>
        </div>
    </div>
</div>

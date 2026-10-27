<div>
    @include('profile.cv.partials._judul', ['judul' => 'Data Pribadi'])

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="form-label-custom">Nama Lengkap</label>
            <div class="form-control-custom cvb-readonly">
                <span x-text="data.user.name"></span>
                <span class="cvb-chip">SSO</span>
            </div>
        </div>

        <div>
            <label class="form-label-custom">Email Publik / Kontak</label>
            {{-- Nilai ini Email Pribadi dari Settings; baru jatuh ke email SSO bila Email
                 Pribadi kosong — jadi tanda sumbernya mengikuti nilai yang tampil. --}}
            <div class="form-control-custom cvb-readonly">
                <span x-text="data.user.personal_email || data.user.email"></span>
                <span class="cvb-chip" x-text="data.user.personal_email ? 'Dari Settings' : 'SSO'"></span>
            </div>
            <p class="form-hint">Ubah Email Pribadi di <a href="{{ route('profile.edit') }}" target="_blank" rel="noopener" class="cvb-link">Settings → Umum</a>.</p>
        </div>

        <div>
            <label class="form-label-custom">Kontak / WhatsApp</label>
            <div class="form-control-custom cvb-readonly">
                <span x-text="data.user.whatsapp || 'Belum diatur di Settings'"></span>
                <span class="cvb-chip" x-show="data.user.whatsapp">Dari Settings</span>
            </div>
            <p class="form-hint">Ubah Nomor Telepon di <a href="{{ route('profile.edit') }}" target="_blank" rel="noopener" class="cvb-link">Settings → Umum</a>.</p>
        </div>

        <div>
            <label class="form-label-custom">Domisili (Kota/Provinsi)</label>
            <input type="text" x-model="data.cv.cv_domisili" placeholder="Contoh: Jakarta Selatan, DKI Jakarta"
                   class="form-control-custom">
        </div>

        <div class="md:col-span-2">
            <label class="form-label-custom">Tautan Profesional (Portofolio / LinkedIn / GitHub)</label>
            <input type="url" x-model="data.cv.cv_portfolio" placeholder="Contoh: linkedin.com/in/username atau github.com/username"
                   class="form-control-custom">
        </div>
    </div>

    <div class="cvb-section">
        <label class="form-label-custom">Tentang Diri</label>
        <p class="form-hint mb-3">Tuliskan deskripsi singkat mengenai diri Anda, fokus karir, dan objektif profesional.</p>
        <textarea x-model="data.cv.tentang_diri"
                  rows="5"
                  class="form-control-custom"
                  placeholder="Saya adalah seorang mahasiswa tingkat akhir..."></textarea>
    </div>
</div>

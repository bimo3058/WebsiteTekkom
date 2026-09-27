<div>
    <h3 class="cvb-title">Data Pribadi</h3>

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
            <div class="form-control-custom cvb-readonly">
                <span x-text="data.user.personal_email || data.user.email"></span>
                <span class="cvb-chip">SSO</span>
            </div>
            <p class="form-hint">Pembaruan email dapat dilakukan melalui menu edit Profil Utama.</p>
        </div>

        <div>
            <label class="form-label-custom">Kontak / WhatsApp</label>
            <div class="form-control-custom cvb-readonly">
                <span x-text="data.user.whatsapp || 'Belum diatur di Profil Utama'"></span>
                <span class="cvb-chip">SSO</span>
            </div>
            <p class="form-hint">Pembaruan nomor dapat dilakukan melalui menu edit Profil Utama.</p>
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
        <label class="form-label-custom">Tentang Diri (Opsional)</label>
        <p class="form-hint mb-3">Tuliskan deskripsi singkat mengenai diri Anda, fokus karir, dan objektif profesional.</p>
        <textarea x-model="data.cv.tentang_diri"
                  rows="5"
                  class="form-control-custom"
                  placeholder="Saya adalah seorang mahasiswa tingkat akhir..."></textarea>
    </div>
</div>

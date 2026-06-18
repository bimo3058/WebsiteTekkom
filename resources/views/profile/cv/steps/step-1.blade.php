<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Data Pribadi</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="form-label">Nama Lengkap</label>
            <div class="flex items-center bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 cursor-not-allowed">
                <span class="text-sm font-semibold text-slate-500" x-text="data.user.name"></span>
                <span class="ml-auto flex-shrink-0 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md" style="background: var(--c-primary-subtle); color: var(--c-primary);">SSO</span>
            </div>
        </div>

        <div>
            <label class="form-label">Email Publik / Kontak</label>
            <div class="flex items-center bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 cursor-not-allowed">
                <span class="text-sm font-semibold text-slate-500 truncate" x-text="data.user.personal_email || data.user.email"></span>
                <span class="ml-auto flex-shrink-0 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md" style="background: var(--c-primary-subtle); color: var(--c-primary);">SSO</span>
            </div>
            <p class="form-hint">Pembaruan email dapat dilakukan melalui menu edit Profil Utama.</p>
        </div>

        <div>
            <label class="form-label">Kontak / WhatsApp</label>
            <div class="flex items-center bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 cursor-not-allowed">
                <span class="text-sm font-semibold text-slate-500 truncate" x-text="data.user.whatsapp || 'Belum diatur di Profil Utama'"></span>
                <span class="ml-auto flex-shrink-0 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md" style="background: var(--c-primary-subtle); color: var(--c-primary);">SSO</span>
            </div>
            <p class="form-hint">Pembaruan nomor dapat dilakukan melalui menu edit Profil Utama.</p>
        </div>

        <div>
            <label class="form-label">Domisili (Kota/Provinsi)</label>
            <div class="relative">
                <input type="text" x-model="data.cv.cv_domisili" placeholder="Contoh: Jakarta Selatan, DKI Jakarta" 
                       class="form-control">
            </div>
        </div>

        <div class="md:col-span-2">
            <label class="form-label">Tautan Profesional (Portofolio / LinkedIn / GitHub)</label>
            <div class="relative">
                <input type="url" x-model="data.cv.cv_portfolio" placeholder="Contoh: linkedin.com/in/username atau github.com/username" 
                       class="form-control">
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <label class="block text-sm font-bold text-slate-800 mb-2">Tentang Diri (Opsional)</label>
        <p class="form-hint mb-3">Tuliskan deskripsi singkat mengenai diri Anda, fokus karir, dan objektif profesional.</p>
        <textarea x-model="data.cv.tentang_diri"
                  rows="5"
                  class="form-control"
                  placeholder="Saya adalah seorang mahasiswa tingkat akhir..."></textarea>
    </div>
</div>
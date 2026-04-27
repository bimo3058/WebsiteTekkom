<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Data Pribadi</h3>



    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
            <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                <span class="text-sm font-semibold text-slate-700" x-text="data.user.name"></span>
                <span class="ml-auto text-[9px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md">SSO</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Publik / Kontak</label>
            <div class="relative">
                <input type="email" x-model="data.user.personal_email" :placeholder="data.user.email" 
                       class="w-full text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] transition-all">
                <span class="absolute right-3 top-3.5 text-[9px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md" 
                      x-text="data.user.personal_email ? 'Pribadi' : 'SSO Default'"></span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kontak / WhatsApp</label>
            <div class="relative">
                <input type="text" x-model="data.user.whatsapp" placeholder="Contoh: 08123456789" 
                       class="w-full text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] transition-all">
                <span class="absolute right-3 top-3.5 text-[9px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md">Bisa Diubah</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Domisili (Kota/Provinsi)</label>
            <div class="relative">
                <input type="text" x-model="data.cv.cv_domisili" placeholder="Contoh: Jakarta Selatan, DKI Jakarta" 
                       class="w-full text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] transition-all">
            </div>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tautan Profesional (Portofolio / LinkedIn / GitHub)</label>
            <div class="relative">
                <input type="url" x-model="data.cv.cv_portfolio" placeholder="Contoh: linkedin.com/in/username atau github.com/username" 
                       class="w-full text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-3 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] transition-all">
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <label class="block text-sm font-bold text-slate-800 mb-2">Tentang Diri (Opsional)</label>
        <p class="text-xs text-slate-500 mb-3">Tuliskan deskripsi singkat mengenai diri Anda, fokus karir, dan objektif profesional.</p>
        <textarea x-model="data.cv.tentang_diri"
                  rows="5"
                  class="w-full bg-white border border-slate-200 rounded-xl p-4 text-sm focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] transition-all outline-none"
                  placeholder="Saya adalah seorang mahasiswa tingkat akhir..."></textarea>
    </div>
</div>

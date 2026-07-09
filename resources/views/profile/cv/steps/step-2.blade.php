<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Pendidikan & Bahasa</h3>

    <div class="p-4 mb-6 rounded-xl border" style="background: var(--c-primary-subtle); border-color: rgba(11,38,110,0.12);">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-[20px] mt-0.5 flex-shrink-0" style="color: var(--c-primary);">info</span>
            <p class="text-sm" style="color: var(--c-fg-sec);">
                Data dengan label <strong style="color: var(--c-primary);">Auto-Sync</strong> diambil secara otomatis dari rekam jejak akademik Anda. Jika ada kesalahan, silakan perbarui data Anda di menu Profil / Akademik terkait.
            </p>
        </div>
    </div>

    <!-- Auto-Sync Edu -->
    <div class="mb-8">
        <h4 class="text-sm font-bold text-slate-600 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">sync</span>
            Pendidikan dari Sistem
        </h4>
        <div class="space-y-3">
            <template x-for="edu in data.pendidikan_sync" :key="edu.tahun_masuk">
                <div class="rounded-xl p-4 flex justify-between items-start border" style="background: var(--c-primary-subtle); border-color: rgba(11,38,110,0.15);">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="edu.institusi"></h5>
                        <p class="text-sm font-medium" style="color: var(--c-primary);" x-text="edu.jurusan"></p>
                        <p class="text-xs text-slate-500 mt-1" x-text="`${edu.tahun_masuk} - ${edu.tahun_lulus || 'Sekarang'}`"></p>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md" style="background: var(--c-primary-subtle); color: var(--c-primary); border: 1px solid rgba(11,38,110,0.15);">Auto-Sync</span>
                </div>
            </template>
            <div x-show="data.pendidikan_sync.length === 0" class="text-sm text-slate-500 italic p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                Belum ada data pendidikan di sistem.
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Tambah Pendidikan Lain (Opsional)</h4>
        
        <!-- Manual Edu List -->
        <div class="space-y-3 mb-4">
            <template x-for="(edu, index) in data.cv.pendidikan" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="edu.institusi"></h5>
                        <p class="text-sm text-slate-600" x-text="edu.jurusan"></p>
                        <p class="text-xs text-slate-500 mt-1" x-text="`${edu.tahun_masuk} - ${edu.tahun_lulus || 'Sekarang'}`"></p>
                    </div>
                    <button @click="removeEdu(index)" class="text-red-400 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label" style="font-size: 11px;">Nama Institusi (SMA/Kursus)</label>
                    <input type="text" x-model="newEdu.institusi" class="form-control">
                </div>
                <div>
                    <label class="form-label" style="font-size: 11px;">Jurusan / Bidang</label>
                    <input type="text" x-model="newEdu.jurusan" class="form-control">
                </div>
                <div>
                    <label class="form-label" style="font-size: 11px;">Tahun Masuk</label>
                    <input type="text" x-model="newEdu.tahun_masuk" class="form-control">
                </div>
                <div>
                    <label class="form-label" style="font-size: 11px;">Tahun Lulus (Kosongkan jika belum)</label>
                    <input type="text" x-model="newEdu.tahun_lulus" class="form-control">
                </div>
            </div>
            <button @click="addEdu()" class="btn-secondary text-xs">
                + Tambah Pendidikan
            </button>
        </div>
    </div>

    <!-- Language Section -->
    <div class="border-t border-slate-100 pt-6 mt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Kemampuan Bahasa</h4>
        
        <!-- Manual Lang List -->
        <div class="space-y-3 mb-4">
            <template x-for="(lang, index) in data.cv.bahasa" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="lang.nama"></h5>
                        <p class="text-sm" style="color: var(--c-primary);" x-text="lang.level"></p>
                        <p class="text-xs text-slate-500 mt-1" x-show="lang.skor" x-text="`Skor / Nilai: ${lang.skor}`"></p>
                    </div>
                    <button @click="removeLang(index)" class="text-red-400 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="form-label" style="font-size: 11px;">Bahasa</label>
                    <input type="text" x-model="newLang.nama" placeholder="Contoh: Inggris" class="form-control">
                </div>
                <div>
                    <label class="form-label" style="font-size: 11px;">Tingkat Kemahiran</label>
                    <select x-model="newLang.level" class="form-control">
                        <option>Dasar (Basic)</option>
                        <option>Menengah (Intermediate)</option>
                        <option>Fasih (Fluent)</option>
                        <option>Penutur Asli (Native)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size: 11px;">Skor / Nilai (Opsional)</label>
                    <input type="text" x-model="newLang.skor" placeholder="Contoh: TOEFL 550" class="form-control">
                </div>
            </div>
            <button @click="addLang()" class="btn-secondary text-xs">
                + Tambah Bahasa
            </button>
        </div>
    </div>
</div>

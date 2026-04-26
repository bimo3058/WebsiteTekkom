<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Proyek & Sertifikasi & Prestasi</h3>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-xl">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-500 text-[20px]">info</span>
            <p class="text-sm text-blue-700">
                Data <strong>Prestasi</strong> diambil secara otomatis dari rekam jejak akademik Anda. Jika ada kesalahan atau prestasi yang belum tercatat, silakan tambahkan melalui modul Manajemen Mahasiswa.
            </p>
        </div>
    </div>

    <!-- Auto-Sync Prestasi -->
    <div class="mb-8">
        <h4 class="text-sm font-bold text-slate-600 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">emoji_events</span>
            Prestasi dari Sistem Kemahasiswaan
        </h4>
        <div class="space-y-3">
            <template x-for="p in data.prestasi_sync" :key="p.nama">
                <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="p.nama"></h5>
                        <p class="text-sm text-slate-600 mt-1">
                            Tingkat: <span class="font-medium text-indigo-600 capitalize" x-text="p.tingkat"></span>
                            <span class="mx-2 text-slate-300">|</span>
                            Tahun <span x-text="p.tahun"></span>
                        </p>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md shrink-0">Auto-Sync</span>
                </div>
            </template>
            <div x-show="data.prestasi_sync.length === 0" class="text-sm text-slate-500 italic p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                Belum ada data prestasi.
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6 mt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Proyek Akademis / Pribadi</h4>
        
        <!-- Manual Project List -->
        <div class="space-y-3 mb-4">
            <template x-for="(proj, index) in data.cv.proyek" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="proj.nama"></h5>
                        <p class="text-sm text-slate-600" x-text="proj.peran"></p>
                        <p class="text-xs text-slate-500 my-1" x-text="proj.tahun"></p>
                        <p class="text-sm text-slate-600 mt-2" x-text="proj.deskripsi"></p>
                        <a x-show="proj.tautan" :href="proj.tautan" target="_blank" class="text-xs text-blue-500 hover:underline mt-1 inline-block">Lihat Proyek</a>
                    </div>
                    <button @click="removeProj(index)" class="text-red-400 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nama Proyek</label>
                    <input type="text" x-model="newProj.nama" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Peran / Teknologi yang Digunakan</label>
                    <input type="text" x-model="newProj.peran" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]" placeholder="Contoh: Backend Developer (Laravel, MySQL)">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Tahun Pembuatan</label>
                    <input type="text" x-model="newProj.tahun" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Tautan URL (Opsional)</label>
                    <input type="url" x-model="newProj.tautan" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]" placeholder="https://github.com/...">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi Singkat</label>
                    <textarea x-model="newProj.deskripsi" rows="2" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]"></textarea>
                </div>
            </div>
            <button @click="addProj()" class="text-xs font-bold bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                + Tambah Proyek
            </button>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6 mt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Sertifikasi</h4>
        
        <!-- Manual Cert List -->
        <div class="space-y-3 mb-4">
            <template x-for="(cert, index) in data.cv.sertifikasi" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="cert.nama"></h5>
                        <p class="text-sm text-slate-600" x-text="cert.penerbit"></p>
                        <p class="text-xs text-slate-500 my-1" x-text="cert.tahun"></p>
                    </div>
                    <button @click="removeCert(index)" class="text-red-400 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nama Sertifikasi / Pelatihan</label>
                    <input type="text" x-model="newCert.nama" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Lembaga Penerbit</label>
                    <input type="text" x-model="newCert.penerbit" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Tahun</label>
                    <input type="text" x-model="newCert.tahun" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
            </div>
            <button @click="addCert()" class="text-xs font-bold bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                + Tambah Sertifikasi
            </button>
        </div>
    </div>
</div>

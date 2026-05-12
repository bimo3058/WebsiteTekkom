<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Pengalaman Kerja & Organisasi</h3>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-xl">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-500 text-[20px]">info</span>
            <p class="text-sm text-blue-700">
                Data <strong>Pengalaman</strong> dan <strong>Kegiatan Mahasiswa</strong> diambil secara otomatis dari modul Manajemen Mahasiswa. Untuk memperbarui data ini secara permanen, silakan gunakan fitur di dalam modul tersebut.
            </p>
        </div>
    </div>

    <!-- Auto-Sync Exp & Kegiatan -->
    <div class="flex flex-col gap-6 mb-8">
        @if(auth()->user()->hasRole('alumni') || auth()->user()->hasRole('superadmin'))
        <div class="w-full">
            <h4 class="text-sm font-bold text-slate-600 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">work</span>
                Pengalaman dari Sistem (Alumni)
            </h4>
            <div class="space-y-3">
                <template x-for="(exp, index) in data.pengalaman_sync" :key="index">
                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 flex justify-between items-start">
                        <div>
                            <h5 class="font-bold text-slate-800 text-sm" x-text="exp.posisi"></h5>
                            <p class="text-sm text-indigo-600 font-medium" x-text="exp.perusahaan"></p>
                            <p class="text-xs text-slate-500 mt-1" x-text="`${exp.tahun_mulai} - ${exp.tahun_selesai || 'Sekarang'}`"></p>
                        </div>
                        <span class="text-[9px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md shrink-0">Auto-Sync</span>
                    </div>
                </template>
                <div x-show="data.pengalaman_sync.length === 0" class="text-sm text-slate-500 italic p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                    Tidak ada data karir alumni di sistem.
                </div>
            </div>
        </div>
        @endif

        <div class="w-full">
            <h4 class="text-sm font-bold text-slate-600 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">event</span>
                Kegiatan Mahasiswa (Auto)
            </h4>
            <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                <template x-for="(keg, index) in data.kegiatan_sync" :key="index">
                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 flex justify-between items-start">
                        <div>
                            <h5 class="font-bold text-slate-800 text-sm" x-text="keg.nama"></h5>
                            <p class="text-sm text-slate-600">Peran: <span class="font-medium" x-text="keg.peran"></span></p>
                        </div>
                    </div>
                </template>
                <div x-show="data.kegiatan_sync.length === 0" class="text-sm text-slate-500 italic p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                    Belum ada riwayat kegiatan.
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Tambah Pengalaman Kerja / Magang (Opsional)</h4>
        
        <!-- Manual Exp List -->
        <div class="space-y-3 mb-4">
            <template x-for="(exp, index) in data.cv.pengalaman_kerja" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="exp.posisi"></h5>
                        <p class="text-sm text-slate-600" x-text="exp.perusahaan"></p>
                        <p class="text-xs text-slate-500 my-1" x-text="`${exp.tahun_mulai} - ${exp.tahun_selesai || 'Sekarang'}`"></p>
                        <p class="text-sm text-slate-600 mt-2" x-text="exp.deskripsi"></p>
                    </div>
                    <button @click="removeExp(index)" class="text-red-400 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Posisi / Jabatan</label>
                    <input type="text" x-model="newExp.posisi" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Perusahaan / Organisasi</label>
                    <input type="text" x-model="newExp.perusahaan" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Tahun Mulai</label>
                    <input type="text" x-model="newExp.tahun_mulai" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Tahun Selesai (Kosongkan jika masih)</label>
                    <input type="text" x-model="newExp.tahun_selesai" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi Pekerjaan (Opsional)</label>
                    <textarea x-model="newExp.deskripsi" rows="3" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]"></textarea>
                </div>
            </div>
            <button @click="addExp()" class="text-xs font-bold bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                + Tambah Pengalaman
            </button>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6 mt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Tambah Pengalaman Organisasi / Kepanitiaan Lain (Opsional)</h4>
        
        <!-- Manual Org List -->
        <div class="space-y-3 mb-4">
            <template x-for="(org, index) in data.cv.kegiatan_organisasi" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex justify-between items-start">
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm" x-text="org.peran"></h5>
                        <p class="text-sm text-slate-600" x-text="org.organisasi"></p>
                        <p class="text-xs text-slate-500 my-1" x-text="`${org.tahun_mulai} - ${org.tahun_selesai || 'Sekarang'}`"></p>
                        <p class="text-sm text-slate-600 mt-2" x-text="org.deskripsi"></p>
                    </div>
                    <button @click="removeOrg(index)" class="text-red-400 hover:text-red-600 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Peran / Jabatan</label>
                    <input type="text" x-model="newOrg.peran" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]" placeholder="Contoh: Ketua Divisi Acara">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nama Organisasi / Acara</label>
                    <input type="text" x-model="newOrg.organisasi" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Tahun Mulai</label>
                    <input type="text" x-model="newOrg.tahun_mulai" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Tahun Selesai</label>
                    <input type="text" x-model="newOrg.tahun_selesai" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi Kegiatan (Opsional)</label>
                    <textarea x-model="newOrg.deskripsi" rows="3" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]"></textarea>
                </div>
            </div>
            <button @click="addOrg()" class="text-xs font-bold bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                + Tambah Organisasi
            </button>
        </div>
    </div>
</div>

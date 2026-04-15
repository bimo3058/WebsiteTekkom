<!-- Modal Popup: Tambah Peserta Manual -->
<div id="modal-tambah-manual" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6">
    
    <!-- Dimmed Backdrop -->
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="document.getElementById('modal-tambah-manual').classList.add('hidden')"></div>

    <!-- Modal Content Wrapper -->
    <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-full z-10">
        
        <!-- Modal Header -->
        <div class="px-6 py-5 flex items-center justify-between border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 tracking-tight">Tambah Peserta Manual</h3>
            <button type="button" onclick="document.getElementById('modal-tambah-manual').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-6 overflow-y-auto">
            <!-- Alert -->
            <div class="mb-6 bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-[13px] leading-relaxed text-blue-800 font-medium">
                    Form Pendaftaran Manual. Pastikan seluruh data registrasi mahasiswa telah dilengkapi dengan benar.
                </div>
            </div>

            <!-- Form -->
            <form
                method="POST"
                action="{{ route('banksoal.pendaftaran.store') }}"
                id="form-tambah-manual"
                class="space-y-5"
            >
                @csrf

                {{-- Hidden: periode_ujian_id dari URL --}}
                <input type="hidden" name="periode_ujian_id" value="{{ request('periode_id') }}">

                <!-- NIM -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">NIM (Nomor Induk Mahasiswa) <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="nim"
                        required
                        placeholder="Contoh: 210101xxx"
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-shadow"
                    >
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="nama_lengkap"
                        required
                        placeholder="Masukkan nama sesuai KTM"
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-shadow"
                    >
                </div>

                <!-- Semester Aktif -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Semester Aktif <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        name="semester_aktif"
                        required
                        min="7"
                        placeholder="Contoh: 7"
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-shadow"
                    >
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">Catatan: minimal semester 7</p>
                </div>

                <!-- Target Wisuda (Sidang Skripsi) -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Target Wisuda (Sidang Skripsi) <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="target_wisuda"
                        required
                        placeholder="Contoh: Periode 183 (Apr-Jun '26)"
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-shadow"
                    >
                </div>

                <!-- Dosen Pembimbing 1 -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Dosen Pembimbing 1</label>
                    <div class="relative">
                        <select
                            name="dosen_pembimbing_1_id"
                            class="w-full appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-shadow cursor-pointer"
                        >
                            <option value="">— Pilih Dosen Pembimbing 1 —</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Dosen Pembimbing 2 -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Dosen Pembimbing 2</label>
                    <div class="relative">
                        <select
                            name="dosen_pembimbing_2_id"
                            class="w-full appearance-none pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-shadow cursor-pointer"
                        >
                            <option value="">— Pilih Dosen Pembimbing 2 —</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-end gap-3 rounded-b-2xl bg-white border-t border-slate-100">
            <button
                type="button"
                onclick="document.getElementById('modal-tambah-manual').classList.add('hidden')"
                class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 focus:outline-none transition-colors"
            >
                Batal
            </button>
            <button
                type="submit"
                form="form-tambah-manual"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm rounded-xl focus:outline-none transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Peserta
            </button>
        </div>
    </div>
</div>

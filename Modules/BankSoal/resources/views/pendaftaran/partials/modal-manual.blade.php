<!-- Modal Popup: Tambah Peserta Manual -->
<div id="modal-tambah-manual" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 sm:p-6">

    <!-- Dimmed Backdrop -->
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
        onclick="document.getElementById('modal-tambah-manual').classList.add('hidden')"></div>

    <!-- Modal Content Wrapper -->
    <div class="relative w-full max-w-xl bg-white rounded-lg shadow-lg flex flex-col max-h-full z-10">

        <!-- Modal Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Peserta Manual</h3>
            <button type="button" onclick="document.getElementById('modal-tambah-manual').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4 overflow-y-auto">
            <!-- Form -->
            <form method="POST" action="{{ route('banksoal.pendaftaran.store') }}" id="form-tambah-manual"
                class="space-y-5">
                @csrf

                {{-- Hidden: periode_ujian_id dari URL --}}
                <input type="hidden" name="periode_ujian_id" value="{{ request('periode_id') }}">

                <!-- NIM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIM (Nomor Induk Mahasiswa) <span
                            class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="nim" id="input-nim" value="{{ old('nim') }}" required
                            placeholder="210101xxx"
                            class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900 @error('nim') border-red-500 @enderror">
                        <button type="button" onclick="lookupNIM()"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md text-sm font-medium text-gray-700">
                            Cari
                        </button>
                    </div>
                    @error('nim')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                    <p id="nim-status" class="text-xs mt-1.5 font-medium hidden"></p>
                </div>

                <!-- Nama Lengkap (auto-fill dari NIM lookup) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_lengkap" id="input-nama" value="{{ old('nama_lengkap') }}" required
                        readonly placeholder="Nama Mahasiswa"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-gray-900">
                </div>

                <!-- Semester Aktif -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester<span
                            class="text-red-500">*</span></label>
                    <input type="number" name="semester_aktif" value="{{ old('semester_aktif') }}" required min="7"
                        readonly placeholder="Semester"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-gray-900">
                    <p class="text-xs text-gray-500 mt-1">Catatan: minimal semester 7</p>
                </div>

                <!-- Target Wisuda (Sidang Skripsi) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Wisuda (Sidang Skripsi) <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="target_wisuda" value="{{ old('target_wisuda') }}" required
                        placeholder="Contoh: Periode 183 (Apr-Jun '26)"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                </div>

                <!-- Dosen Pembimbing 1 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosen Pembimbing 1</label>
                    <select name="dosen_pembimbing_1_id"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">— Pilih Dosen Pembimbing 1 —</option>
                        @foreach($dosenList as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dosen Pembimbing 2 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosen Pembimbing 2</label>
                    <select name="dosen_pembimbing_2_id"
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">— Pilih Dosen Pembimbing 2 —</option>
                        @foreach($dosenList as $dosen)
                            <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                        @endforeach
                    </select>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div
            class="px-6 py-4 flex flex-col sm:flex-row items-center justify-end gap-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <button type="button" onclick="document.getElementById('modal-tambah-manual').classList.add('hidden')"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </button>
            <button type="submit" form="form-tambah-manual"
                class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Simpan Peserta
            </button>
        </div>
    </div>
</div>

<script>
    function lookupNIM() {
        const nim = document.getElementById('input-nim').value.trim();
        const statusEl = document.getElementById('nim-status');
        const namaEl = document.getElementById('input-nama');
        const semesterEl = document.querySelector('input[name="semester_aktif"]');

        if (!nim) {
            statusEl.textContent = 'Masukkan NIM terlebih dahulu.';
            statusEl.className = 'text-xs mt-1.5 font-medium text-amber-600';
            statusEl.classList.remove('hidden');
            return;
        }

        statusEl.textContent = 'Mencari...';
        statusEl.className = 'text-xs mt-1.5 font-medium text-slate-500';
        statusEl.classList.remove('hidden');

        fetch(`{{ route('banksoal.pendaftaran.lookupNIM') }}?nim=${encodeURIComponent(nim)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
            .then(r => r.json())
            .then(data => {
                if (data.found) {
                    namaEl.value = data.nama;
                    if (data.semester && semesterEl) {
                        semesterEl.value = data.semester;
                    }
                    statusEl.textContent = '✓ Mahasiswa ditemukan: ' + data.nama;
                    statusEl.className = 'text-xs mt-1.5 font-medium text-emerald-600';
                } else {
                    namaEl.value = '';
                    if (semesterEl && !semesterEl.value) {
                        semesterEl.value = '';
                    }
                    statusEl.textContent = '✗ ' + (data.message || 'Mahasiswa tidak ditemukan.');
                    statusEl.className = 'text-xs mt-1.5 font-medium text-red-600';
                }
            })
            .catch(() => {
                statusEl.textContent = 'Gagal menghubungi server.';
                statusEl.className = 'text-xs mt-1.5 font-medium text-red-600';
            });
    }

    // Juga trigger lookup saat Enter di field NIM
    document.getElementById('input-nim')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            lookupNIM();
        }
    });
</script>
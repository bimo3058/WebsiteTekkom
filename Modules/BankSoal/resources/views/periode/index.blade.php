@section('hide_global_errors', true)

@section('breadcrumbs')
    <a href="#" class="hover:text-primary transition-colors text-slate-500">Sistem Ujian</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Setup Periode</span>
@endsection

<x-banksoal::layouts.admin>
    <div x-data="periodeManagerApp()" class="w-full">
        {{-- PHP data passed safely via JSON script tag --}}
        <script id="periode-init-data" type="application/json">
        {
            "openModal": {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
            "editModal": {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
            "editData": {
                "id":                    @json(old('id')),
                "nama_periode":          @json(old('nama_periode')),
                "tanggal_mulai":         @json(old('tanggal_mulai')),
                "tanggal_selesai":       @json(old('tanggal_selesai')),
                "tanggal_mulai_ujian":   @json(old('tanggal_mulai_ujian')),
                "tanggal_selesai_ujian": @json(old('tanggal_selesai_ujian')),
                "kuota_peserta":         @json(old('kuota_peserta'))
            },
            "createOptions": @json(old('target_wisuda_options') ? array_values(array_filter(old('target_wisuda_options'))) : [''])
        }
        </script>


        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Manajemen Periode Ujian</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Atur periode pelaksanaan ujian komprehensif mahasiswa.</p>
            </div>

            <button @click="openModal = true"
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 transition-colors rounded-lg px-4 py-2.5 text-white font-medium text-[13px] shadow-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Periode Ujian
            </button>
        </div>

        {{-- Table Container --}}
        <div class="mb-8">
            @include('banksoal::periode._table')
        </div>
<!-- Modal Popup: Setup Periode Baru -->
        <div x-show="openModal" tabindex="-1"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">

            <!-- Dimmed Backdrop -->
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="openModal = false">
            </div>

            <!-- Modal Content Wrapper -->
            <div x-show="openModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">

                <!-- Modal Header -->
                <div class="px-6 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-[16px] font-semibold text-gray-900">Setup Periode Baru</h3>
                    <button @click="openModal = false"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 pb-6 pt-3 overflow-y-auto">

                    <!-- Setup Form Grid -->
                    <form action="{{ route('banksoal.periode.store') }}" method="POST" id="formPeriodeBaru"
                        class="space-y-5">
                        @csrf
                        <!-- Box 1: Nama Periode -->
                        <div class="space-y-1.5">
                            <x-ui.label required class="text-[13px] font-medium text-gray-700">Nama
                                Periode</x-ui.label>
                            <x-ui.input type="text" name="nama_periode" value="{{ old('nama_periode') }}"
                                placeholder="Misal: Ujian Komprehensif bulan Februari 2026" required
                                class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 placeholder:text-gray-400 outline-none" />
                            @error('nama_periode') <p class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}
                            </p> @enderror
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Buka
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @error('tanggal_mulai') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Tutup
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                    required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @error('tanggal_selesai') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Mulai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian"
                                    value="{{ old('tanggal_mulai_ujian') }}" required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @error('tanggal_mulai_ujian') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Selesai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian"
                                    value="{{ old('tanggal_selesai_ujian') }}" required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @error('tanggal_selesai_ujian') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <!-- Box 5: Kuota Peserta -->
                        <div class="space-y-1.5">
                            <x-ui.label required class="text-[13px] font-medium text-gray-700">Kuota Peserta
                                Ujian</x-ui.label>
                            <x-ui.input type="number" name="kuota_peserta" value="{{ old('kuota_peserta') }}" min="1"
                                max="9999" placeholder="Masukkan  kuota maksimal peserta (misal: 40)" required
                                class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 placeholder:text-gray-400 outline-none" />
                            @error('kuota_peserta') <p class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}
                            </p> @enderror
                        </div>

                        <!-- Box 6: Pilihan Target Wisuda (Create) -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Pilihan Target
                                    Wisuda</x-ui.label>
                            </div>

                            <!-- Dynamic Option List -->
                            <div class="space-y-2">
                                <template x-for="(opt, idx) in createOptions" :key="idx">
                                    <div class="flex items-center gap-2 group">
                                        <!-- Nomor -->
                                        <span
                                            class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 text-gray-600 text-[11px] font-bold flex items-center justify-center"
                                            x-text="idx + 1"></span>
                                        <!-- Input -->
                                        <input type="text" :name="'target_wisuda_options[]'"
                                            x-model="createOptions[idx]" placeholder="Contoh: Periode 183 (Apr–Jun '26)"
                                            required
                                            class="flex-1 h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 placeholder:text-gray-400 outline-none" />
                                        <!-- Hapus -->
                                        <button type="button" @click="createOptions.splice(idx, 1)"
                                            x-show="createOptions.length > 1"
                                            class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <!-- Spacer kalau tidak bisa dihapus -->
                                        <span x-show="createOptions.length <= 1" class="flex-shrink-0 w-7"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Tambah Opsi -->
                            <button type="button" @click="createOptions.push('')"
                                class="mt-1 inline-flex items-center gap-1.5 text-[13px] font-medium text-primary hover:text-[#1E2A5E] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Pilihan
                            </button>

                            @if($errors->has('target_wisuda_options') || $errors->has('target_wisuda_options.*')) 
                                <p class="text-[13px] text-red-500 mt-1 font-medium">Pilihan target wisuda wajib diisi.</p> 
                            @endif
                        </div>

                        {{-- Status dikelola otomatis oleh sistem, tidak ada dropdown di sini --}}
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3 bg-gray-50 rounded-b-2xl">
                    <button @click="openModal = false" type="button"
                        class="px-5 py-2 border border-gray-300 text-gray-700 font-medium text-[13px] bg-white rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formPeriodeBaru').submit()"
                        class="px-5 py-2 bg-primary hover:bg-primary/90 text-white font-medium text-[13px] rounded-lg transition-colors">
                        Simpan Periode
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Popup: Edit Periode -->
        <div x-show="editModal" tabindex="-1"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-cloak>

            <!-- Dimmed Backdrop -->
            <div x-show="editModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="editModal = false">
            </div>

            <!-- Modal Content Wrapper -->
            <div x-show="editModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">

                <!-- Modal Header -->
                <div class="px-6 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-[16px] font-semibold text-gray-900">Edit Periode Ujian</h3>
                    <button @click="editModal = false"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 pb-6 pt-3 overflow-y-auto">
                    <!-- Setup Form Grid -->
                    <form
                        :action="`{{ route('banksoal.periode.update', 'REPLACE_ID') }}`.replace('REPLACE_ID', editData.id)"
                        method="POST" id="formEditPeriode" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" x-model="editData.id">

                        <!-- Box 1: Nama Periode -->
                        <div class="space-y-1.5">
                            <x-ui.label required class="text-[13px] font-medium text-gray-700">Nama
                                Periode</x-ui.label>
                            <x-ui.input type="text" name="nama_periode" x-model="editData.nama_periode" required
                                class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 placeholder:text-gray-400 outline-none" />
                            @if(old('_method') === 'PUT') @error('nama_periode') <p
                            class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror @endif
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Buka
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_mulai') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Tutup
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai"
                                    required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_selesai') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Mulai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian"
                                    x-model="editData.tanggal_mulai_ujian" required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_mulai_ujian') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Tanggal Selesai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian"
                                    x-model="editData.tanggal_selesai_ujian" required
                                    class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_selesai_ujian') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        </div>

                        <!-- Box 5: Kuota Peserta (Edit) -->
                        <div class="space-y-1.5">
                            <x-ui.label required class="text-[13px] font-medium text-gray-700">Kuota Peserta
                                Ujian</x-ui.label>
                            <x-ui.input type="number" name="kuota_peserta" x-model="editData.kuota_peserta" min="1"
                                max="9999" placeholder="Masukkan  kuota maksimal peserta (misal: 40)" required
                                class="h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 placeholder:text-gray-400 outline-none" />
                            @if(old('_method') === 'PUT') @error('kuota_peserta') <p
                            class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror @endif
                        </div>

                        <!-- Box 6: Pilihan Target Wisuda (Edit) -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <x-ui.label required class="text-[13px] font-medium text-gray-700">Pilihan Target
                                    Wisuda</x-ui.label>

                            </div>

                            <!-- Dynamic Option List -->
                            <div class="space-y-2">
                                <template x-for="(opt, idx) in editOptions" :key="idx">
                                    <div class="flex items-center gap-2 group">
                                        <!-- Nomor -->
                                        <span
                                            class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 text-gray-600 text-[11px] font-bold flex items-center justify-center"
                                            x-text="idx + 1"></span>
                                        <!-- Input -->
                                        <input type="text" :name="'target_wisuda_options[]'" x-model="editOptions[idx]"
                                            placeholder="Contoh: Periode 183 (Apr–Jun '26)" required
                                            class="flex-1 h-10 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-900 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all px-3 placeholder:text-gray-400 outline-none" />
                                        <!-- Hapus -->
                                        <button type="button" @click="editOptions.splice(idx, 1)"
                                            x-show="editOptions.length > 1"
                                            class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <span x-show="editOptions.length <= 1" class="flex-shrink-0 w-7"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Tambah Opsi -->
                            <button type="button" @click="editOptions.push('')"
                                class="mt-1 inline-flex items-center gap-1.5 text-[13px] font-medium text-primary hover:text-[#1E2A5E] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Pilihan
                            </button>

                            @if(old('_method') === 'PUT')
                                @if($errors->has('target_wisuda_options') || $errors->has('target_wisuda_options.*'))
                                    <p class="text-[13px] text-red-500 mt-1 font-medium">Pilihan target wisuda wajib diisi.</p>
                                @endif
                            @endif
                        </div>


                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3 bg-gray-50 rounded-b-2xl">
                    <button @click="editModal = false" type="button"
                        class="px-5 py-2 border border-gray-300 text-gray-700 font-medium text-[13px] bg-white rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formEditPeriode').submit()"
                        class="px-5 py-2 bg-primary hover:bg-primary/90 text-white font-medium text-[13px] rounded-lg transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Popup: Konfirmasi -->
        <div x-show="confirmModal" tabindex="-1" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;" x-cloak>
            <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="closeConfirm()"></div>

            <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">
                
                <div class="px-6 pt-6 pb-4 text-center">
                    <div :class="'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 ' + confirmIconBg">
                        <svg :class="'w-7 h-7 ' + confirmIconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-[16px] font-semibold text-gray-900 tracking-tight mb-2" x-text="confirmTitle"></h3>
                    <p class="text-[13px] text-gray-500 leading-relaxed" x-text="confirmText"></p>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex items-center gap-3">
                    <button type="button" @click="closeConfirm()" class="flex-1 px-4 py-2 text-[13px] font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">Batal</button>
                    <form :action="confirmAction" method="POST" class="flex-1 m-0">
                        @csrf
                        <input type="hidden" name="_method" :value="confirmMethod">
                        <button type="submit" :class="'w-full px-4 py-2 text-[13px] font-medium text-white rounded-lg transition-all ' + confirmBtnColor">
                            Ya, Lanjutkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Register Alpine component BEFORE Alpine processes x-data --}}
    <script>
        (function () {
            function registerPeriodeManagerApp() {
                if (!window.Alpine || !window.Alpine.data) return false;

                window.Alpine.data('periodeManagerApp', function () {
                    var initEl = document.getElementById('periode-init-data');
                    var init = {};
                    try { if (initEl) init = JSON.parse(initEl.textContent); } catch (e) { }

                    return {
                        openModal: init.openModal !== undefined ? init.openModal : false,
                        editModal: init.editModal !== undefined ? init.editModal : false,
                        editData: init.editData || { id: null, nama_periode: '', tanggal_mulai: '', tanggal_selesai: '', tanggal_mulai_ujian: '', tanggal_selesai_ujian: '', kuota_peserta: '' },
                        createOptions: init.createOptions || [''],
                        editOptions: [''],

                        confirmModal: false,
                        confirmAction: '',
                        confirmMethod: 'POST',
                        confirmTitle: '',
                        confirmText: '',
                        confirmBtnColor: '',
                        confirmIconColor: '',
                        confirmIconBg: '',

                        openConfirm: function(action, method, title, text, btnColor, iconColor, iconBg) {
                            this.confirmAction = action;
                            this.confirmMethod = method;
                            this.confirmTitle = title;
                            this.confirmText = text;
                            this.confirmBtnColor = btnColor;
                            this.confirmIconColor = iconColor;
                            this.confirmIconBg = iconBg;
                            this.confirmModal = true;
                        },
                        closeConfirm: function() {
                            this.confirmModal = false;
                        },

                        openEdit: function (periodeData) {
                            this.editData = {
                                id: periodeData.id,
                                nama_periode: periodeData.nama_periode,
                                tanggal_mulai: periodeData.tanggal_mulai,
                                tanggal_selesai: periodeData.tanggal_selesai,
                                tanggal_mulai_ujian: periodeData.tanggal_mulai_ujian,
                                tanggal_selesai_ujian: periodeData.tanggal_selesai_ujian,
                                kuota_peserta: periodeData.kuota_peserta,
                            };
                            this.editOptions = (periodeData.target_wisuda_options && periodeData.target_wisuda_options.length > 0)
                                ? periodeData.target_wisuda_options.slice()
                                : [''];
                            this.editModal = true;
                        }
                    };
                });

                return true;
            }

            function initPeriodeManagerApp() {
                if (!registerPeriodeManagerApp()) return;

                var root = document.querySelector('[x-data="periodeManagerApp()"]');
                if (root && window.Alpine.initTree) {
                    window.Alpine.initTree(root);
                }
            }

            if (window.Alpine && window.Alpine.data) {
                initPeriodeManagerApp();
            }

            document.addEventListener('livewire:init', initPeriodeManagerApp, { once: true });
            document.addEventListener('alpine:init', initPeriodeManagerApp, { once: true });
        })();
    </script>
</x-banksoal::layouts.admin>

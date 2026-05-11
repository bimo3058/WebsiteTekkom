@section('hide_global_errors', true)
<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Ujian Komprehensif</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Setup Periode</span>
    @endsection

    <div x-data="{ 
        openModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }}, 
        editModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }}, 
        editData: {
            id: '{{ old('id') }}',
            nama_periode: '{{ old('nama_periode') }}',
            tanggal_mulai: '{{ old('tanggal_mulai') }}',
            tanggal_selesai: '{{ old('tanggal_selesai') }}',
            tanggal_mulai_ujian: '{{ old('tanggal_mulai_ujian') }}',
            tanggal_selesai_ujian: '{{ old('tanggal_selesai_ujian') }}'
        } 
    }" class="w-full">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-grey-900 tracking-tight">Manajemen Periode Ujian</h1>
                <p class="text-base text-grey-500 mt-2 font-medium">Atur periode pelaksanaan ujian komprehensif
                    mahasiswa.</p>
            </div>

            <button @click="openModal = true"
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 transition-colors rounded-xl px-5 py-2.5 text-white font-medium text-sm shadow-sm shadow-primary/20">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Periode Ujian
            </button>
        </div>

        {{-- Tidak ada conflict banner — sistem date-driven mencegah konflik secara otomatis --}}



        @livewire(\Modules\BankSoal\Livewire\PeriodeTable::class)

        <!-- Modal Popup: Setup Periode Baru -->
        <div x-show="openModal" tabindex="-1" aria-hidden="true"
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
                <div class="px-6 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Setup Periode Baru</h3>
                    <button @click="openModal = false"
                        class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
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
                            <x-ui.label required class="text-[14px] font-semibold text-grey-700">Nama
                                Periode</x-ui.label>
                            <x-ui.input type="text" name="nama_periode" value="{{ old('nama_periode') }}"
                                placeholder="Misal: Ujian Komprehensif bulan Februari 2026" required
                                class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none placeholder:text-grey-400" />
                            @error('nama_periode') <p class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}
                            </p> @enderror
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Buka
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @error('tanggal_mulai') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                {{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Tutup
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                    required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @error('tanggal_selesai') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                {{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label class="text-[14px] font-semibold text-grey-700">Tanggal Mulai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian"
                                    value="{{ old('tanggal_mulai_ujian') }}"
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @error('tanggal_mulai_ujian') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                {{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label class="text-[14px] font-semibold text-grey-700">Tanggal Selesai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian"
                                    value="{{ old('tanggal_selesai_ujian') }}"
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @error('tanggal_selesai_ujian') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                {{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Status dikelola otomatis oleh sistem, tidak ada dropdown di sini --}}
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-grey-100/80 flex items-center justify-end gap-3 bg-grey-50/50">
                    <button @click="openModal = false" type="button"
                        class="px-6 py-3 border border-grey-200 text-grey-700 font-bold bg-white rounded-xl hover:bg-grey-50 hover:text-grey-900 transition-colors shadow-sm">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formPeriodeBaru').submit()"
                        class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-sm transition-all shadow-primary/25">
                        Simpan Periode
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Popup: Edit Periode -->
        <div x-show="editModal" tabindex="-1" aria-hidden="true"
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
                <div class="px-6 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Edit Periode Ujian</h3>
                    <button @click="editModal = false"
                        class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
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
                            <x-ui.label required class="text-[14px] font-semibold text-grey-700">Nama
                                Periode</x-ui.label>
                            <x-ui.input type="text" name="nama_periode" x-model="editData.nama_periode" required
                                class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none placeholder:text-grey-400" />
                            @if(old('_method') === 'PUT') @error('nama_periode') <p
                            class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror @endif
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Buka
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_mulai') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Tutup
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai"
                                    required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_selesai') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label class="text-[14px] font-semibold text-grey-700">Tanggal Mulai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian"
                                    x-model="editData.tanggal_mulai_ujian"
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_mulai_ujian') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label class="text-[14px] font-semibold text-grey-700">Tanggal Selesai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian"
                                    x-model="editData.tanggal_selesai_ujian"
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_selesai_ujian') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        </div>

                        {{-- Status dikelola otomatis berdasarkan tanggal, tidak ada dropdown di Edit --}}
                        <div class="pt-4 border-t border-grey-100">
                            <p class="text-[13px] text-slate-500 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Status periode diperbarui <strong>otomatis</strong> berdasarkan tanggal yang diubah.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-grey-100/80 flex items-center justify-end gap-3 bg-grey-50/50">
                    <button @click="editModal = false" type="button"
                        class="px-6 py-3 border border-grey-200 text-grey-700 font-bold bg-white rounded-xl hover:bg-grey-50 hover:text-grey-900 transition-colors shadow-sm">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formEditPeriode').submit()"
                        class="px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-sm transition-all shadow-primary/25">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-banksoal::layouts.admin>
@section('hide_global_errors', true)
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
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-grey-900 tracking-tight">Manajemen Periode Ujian</h1>
                <p class="text-base text-grey-500 mt-2 font-medium">Atur periode pelaksanaan ujian komprehensif
                    mahasiswa.</p>
            </div>

            <button @click="openModal = true"
                class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 transition-colors rounded-xl px-5 py-2.5 text-white font-medium text-sm shadow-sm shadow-blue-500/20">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Periode Ujian
            </button>
        </div>




        {{-- Search & Per-Page Filter --}}
        <form method="GET" action="{{ route('banksoal.periode.setup') }}"
            class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm text-slate-500 font-medium">
            <div class="flex items-center gap-2">
                <span>Tampilkan</span>
                <select name="perPage" onchange="this.form.submit()"
                    class="pl-3 pr-8 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-slate-600 cursor-pointer shadow-sm">
                    @foreach([5, 10, 25, 50] as $opt)
                        <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                <span>data</span>
            </div>
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 shadow-sm">
            </div>
        </form>

        {{-- Table --}}
        <div
            class="bg-white border border-slate-200 rounded-[10px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.08)] mb-8">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-[14px] text-slate-700 border-collapse">
                    <thead
                        class="bg-slate-100 border-b-2 border-slate-200 text-[12px] font-bold text-slate-700 uppercase tracking-[0.5px]">
                        <tr>
                            <th class="px-4 py-[14px] whitespace-nowrap">Nama Periode</th>
                            <th class="px-4 py-[14px] whitespace-nowrap">Timeline Pendaftaran</th>
                            <th class="px-4 py-[14px] whitespace-nowrap">Rentang Ujian</th>
                            <th class="px-4 py-[14px] whitespace-nowrap text-center">Status</th>
                            <th class="px-4 py-[14px] whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $periode)
                            @php
                                $now = now();
                                $tglMulai = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
                                $tglSelesai = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();
                                $hasPendaftar = \Modules\BankSoal\Models\Komprehensif\PendaftarUjian::where('periode_ujian_id', $periode->id)->exists();
                                $periodeData = [
                                    'id' => $periode->id,
                                    'nama_periode' => $periode->nama_periode,
                                    'tanggal_mulai' => $periode->tanggal_mulai ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d') : null,
                                    'tanggal_selesai' => $periode->tanggal_selesai ? \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d') : null,
                                    'tanggal_mulai_ujian' => $periode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->format('Y-m-d') : null,
                                    'tanggal_selesai_ujian' => $periode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->format('Y-m-d') : null,
                                    'status' => $periode->status,
                                    'deskripsi' => $periode->deskripsi,
                                    'kuota_peserta' => $periode->kuota_peserta,
                                    'target_wisuda_options' => $periode->target_wisuda_options ?? [],
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors border-b border-slate-200 last:border-b-0">
                                <td class="px-4 py-[14px] whitespace-nowrap">
                                    <span class="font-bold text-slate-800">{{ $periode->nama_periode }}</span>
                                </td>
                                <td class="px-4 py-[14px] whitespace-nowrap text-slate-700">
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->translatedFormat('d M Y') }}
                                    – {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-[14px] whitespace-nowrap text-slate-700">
                                    @if($periode->tanggal_mulai_ujian && $periode->tanggal_selesai_ujian)
                                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->translatedFormat('d M Y') }}
                                        –
                                        {{ \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                    @else
                                        <span class="text-slate-400 italic">Belum diatur</span>
                                    @endif
                                </td>
                                <td class="px-4 py-[14px] whitespace-nowrap text-center">
                                    @if($periode->status === 'selesai')
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-bold bg-slate-100 text-slate-700 tracking-wide">SELESAI</span>
                                    @elseif($now->lt($tglMulai))
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-bold bg-slate-100 text-slate-500 tracking-wide border border-slate-200">
                                            DRAFT &middot; Buka {{ $periode->tanggal_mulai->translatedFormat('d M') }}
                                        </span>
                                    @elseif($now->gt($tglSelesai))
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-bold bg-slate-100 text-slate-600 tracking-wide border border-slate-300">DAFTAR
                                            TUTUP</span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-bold bg-blue-500 text-white tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            AKTIF &middot; DAFTAR BUKA
                                        </span>
                                    @endif
                                    @if($periode->pendaftaran_ditutup_paksa && $periode->status === 'aktif')
                                        <div class="mt-1">
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white tracking-wide">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                DAFTAR DITUTUP
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-[14px] whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Edit (Alpine modal) --}}
                                        <button type="button"
                                            @click="openEdit({{ \Illuminate\Support\Js::from($periodeData) }})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#3B82F6] hover:bg-[#2563EB] text-white transition-all"
                                            title="Edit">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Tutup Pendaftaran --}}
                                        @if($periode->pendaftaran_terbuka)
                                            <button type="button" 
                                                @click="openConfirm('{{ route('banksoal.periode.close-pendaftaran', $periode->id) }}', 'PATCH', 'Tutup Pendaftaran?', 'Mahasiswa tidak dapat mendaftar lagi meskipun tanggal belum berakhir.', 'bg-amber-500 hover:bg-amber-600', 'text-amber-500', 'bg-amber-50')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#F59E0B] hover:bg-[#D97706] text-white transition-all"
                                                title="Tutup Pendaftaran">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Buka Kembali Pendaftaran --}}
                                        @if($periode->pendaftaran_ditutup_paksa && $now->lte(\Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay()))
                                            <button type="button" 
                                                @click="openConfirm('{{ route('banksoal.periode.open-pendaftaran', $periode->id) }}', 'PATCH', 'Buka Pendaftaran?', 'Mahasiswa yang memenuhi syarat akan bisa mendaftar lagi.', 'bg-emerald-500 hover:bg-emerald-600', 'text-emerald-500', 'bg-emerald-50')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#10B981] hover:bg-[#059669] text-white transition-all"
                                                title="Buka Kembali Pendaftaran">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Hapus --}}
                                        @if(!$hasPendaftar)
                                            <button type="button" 
                                                @click="openConfirm('{{ route('banksoal.periode.destroy', $periode->id) }}', 'DELETE', 'Hapus Periode?', 'Apakah Anda yakin ingin menghapus periode ini? Aksi ini tidak dapat dibatalkan.', 'bg-red-500 hover:bg-red-600', 'text-red-500', 'bg-red-50')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[#EF4444] hover:bg-[#DC2626] text-white transition-all"
                                                title="Hapus">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @else
                                            <span class="text-slate-400 text-[11px] font-medium italic"
                                                title="Periode memiliki data pendaftar dan tidak dapat dihapus">Terkunci</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-12 h-12 bg-slate-50 flex items-center justify-center rounded-full mb-3">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800 mb-1">Tidak ada data periode</h3>
                                        <p class="text-[13px] text-slate-500">Buat periode ujian komprehensif baru untuk
                                            mulai membuka pendaftaran bagi mahasiswa.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($periodes->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                    {{ $periodes->links() }}
                </div>
            @endif
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
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Tutup
                                    Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                    required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @error('tanggal_selesai') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Mulai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian"
                                    value="{{ old('tanggal_mulai_ujian') }}" required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @error('tanggal_mulai_ujian') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Selesai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian"
                                    value="{{ old('tanggal_selesai_ujian') }}" required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @error('tanggal_selesai_ujian') <p class="text-[13px] text-red-500 mt-1 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>

                        <!-- Box 5: Kuota Peserta -->
                        <div class="space-y-1.5">
                            <x-ui.label required class="text-[14px] font-semibold text-grey-700">Kuota Peserta
                                Ujian</x-ui.label>
                            <x-ui.input type="number" name="kuota_peserta" value="{{ old('kuota_peserta') }}" min="1"
                                max="9999" placeholder="Masukkan  kuota maksimal peserta (misal: 40)" required
                                class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none placeholder:text-grey-400" />
                            @error('kuota_peserta') <p class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}
                            </p> @enderror
                        </div>

                        <!-- Box 6: Pilihan Target Wisuda (Create) -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Pilihan Target
                                    Wisuda</x-ui.label>
                            </div>

                            <!-- Dynamic Option List -->
                            <div class="space-y-2">
                                <template x-for="(opt, idx) in createOptions" :key="idx">
                                    <div class="flex items-center gap-2 group">
                                        <!-- Nomor -->
                                        <span
                                            class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-[11px] font-bold flex items-center justify-center"
                                            x-text="idx + 1"></span>
                                        <!-- Input -->
                                        <input type="text" :name="'target_wisuda_options[]'"
                                            x-model="createOptions[idx]" placeholder="Contoh: Periode 183 (Apr–Jun '26)"
                                            required
                                            class="flex-1 h-10 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[13.5px] font-medium rounded-xl px-3.5 transition-all outline-none placeholder:text-grey-400" />
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
                                class="mt-1 inline-flex items-center gap-1.5 text-[13px] font-semibold text-blue-600 hover:text-blue-700 transition-colors">
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
                <div class="px-6 py-4 border-t border-grey-100/80 flex items-center justify-end gap-3 bg-grey-50/50">
                    <button @click="openModal = false" type="button"
                        class="px-6 py-3 border border-grey-200 text-grey-700 font-bold bg-white rounded-xl hover:bg-grey-50 hover:text-grey-900 transition-colors shadow-sm">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formPeriodeBaru').submit()"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-all shadow-blue-500/25">
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
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Mulai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian"
                                    x-model="editData.tanggal_mulai_ujian" required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_mulai_ujian') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Tanggal Selesai
                                    Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian"
                                    x-model="editData.tanggal_selesai_ujian" required
                                    class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none" />
                                @if(old('_method') === 'PUT') @error('tanggal_selesai_ujian') <p
                                    class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                                @endif
                            </div>
                        </div>

                        <!-- Box 5: Kuota Peserta (Edit) -->
                        <div class="space-y-1.5">
                            <x-ui.label required class="text-[14px] font-semibold text-grey-700">Kuota Peserta
                                Ujian</x-ui.label>
                            <x-ui.input type="number" name="kuota_peserta" x-model="editData.kuota_peserta" min="1"
                                max="9999" placeholder="Masukkan  kuota maksimal peserta (misal: 40)" required
                                class="h-11 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[15px] font-medium rounded-xl px-4 transition-all outline-none placeholder:text-grey-400" />
                            @if(old('_method') === 'PUT') @error('kuota_peserta') <p
                            class="text-[13px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror @endif
                        </div>

                        <!-- Box 6: Pilihan Target Wisuda (Edit) -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <x-ui.label required class="text-[14px] font-semibold text-grey-700">Pilihan Target
                                    Wisuda</x-ui.label>

                            </div>

                            <!-- Dynamic Option List -->
                            <div class="space-y-2">
                                <template x-for="(opt, idx) in editOptions" :key="idx">
                                    <div class="flex items-center gap-2 group">
                                        <!-- Nomor -->
                                        <span
                                            class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-[11px] font-bold flex items-center justify-center"
                                            x-text="idx + 1"></span>
                                        <!-- Input -->
                                        <input type="text" :name="'target_wisuda_options[]'" x-model="editOptions[idx]"
                                            placeholder="Contoh: Periode 183 (Apr–Jun '26)" required
                                            class="flex-1 h-10 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-[13.5px] font-medium rounded-xl px-3.5 transition-all outline-none placeholder:text-grey-400" />
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
                                class="mt-1 inline-flex items-center gap-1.5 text-[13px] font-semibold text-blue-600 hover:text-blue-700 transition-colors">
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
                <div class="px-6 py-4 border-t border-grey-100/80 flex items-center justify-end gap-3 bg-grey-50/50">
                    <button @click="editModal = false" type="button"
                        class="px-6 py-3 border border-grey-200 text-grey-700 font-bold bg-white rounded-xl hover:bg-grey-50 hover:text-grey-900 transition-colors shadow-sm">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formEditPeriode').submit()"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-all shadow-blue-500/25">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Popup: Konfirmasi -->
        <div x-show="confirmModal" tabindex="-1" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;" x-cloak>
            <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeConfirm()"></div>

            <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden max-h-full">
                
                <div class="px-6 pt-6 pb-4 text-center">
                    <div :class="'w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ' + confirmIconBg">
                        <svg :class="'w-8 h-8 ' + confirmIconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-[17px] font-extrabold text-slate-800 tracking-tight mb-2" x-text="confirmTitle"></h3>
                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed" x-text="confirmText"></p>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center gap-3">
                    <button type="button" @click="closeConfirm()" class="flex-1 px-4 py-2.5 text-[13px] font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm rounded-xl focus:outline-none transition-colors">Batal</button>
                    <form :action="confirmAction" method="POST" class="flex-1 m-0">
                        @csrf
                        <input type="hidden" name="_method" :value="confirmMethod">
                        <button type="submit" :class="'w-full px-4 py-2.5 text-[13px] font-bold text-white shadow-sm rounded-xl focus:outline-none transition-all ' + confirmBtnColor">
                            Ya, Lanjutkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Register Alpine component after Livewire+Alpine initializes --}}
    <script>
        document.addEventListener('livewire:init', function () {
            if (!window.Alpine || !window.Alpine.data) return;

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

            // Re-initialize Alpine on the container so the new component is recognized
            window.Alpine.initTree(document.querySelector('[x-data="periodeManagerApp()"]'));
        });
    </script>
</x-banksoal::layouts.admin>
<x-banksoal::layouts.admin>
    <div x-data="{ openModal: false, editModal: false, editData: {} }" class="w-full">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-grey-900 tracking-tight">Manajemen Periode Ujian</h1>
                <p class="text-base text-grey-500 mt-2 font-medium">Atur periode pelaksanaan ujian komprehensif mahasiswa.</p>
            </div>
            
            <button @click="openModal = true" class="inline-flex items-center justify-center gap-2 bg-primary-500 hover:bg-primary-400 transition-colors rounded-xl px-5 py-2.5 text-white font-medium text-sm shadow-sm shadow-primary-500/20">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Periode Ujian
            </button>
        </div>

        @php
            $activeCount = collect($periodes)->where('status', 'aktif')->count();
        @endphp

        @if ($activeCount > 1)
        <div class="mb-4 bg-[#FEF2F2] border border-[#FEE2E2] rounded-xl flex items-center justify-between p-5 border-l-4 border-l-[#DC2626] shadow-sm animate-pulse">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#FEE2E2] text-[#DC2626] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h4 class="text-[15px] font-bold text-[#991B1B] mb-0.5">Peringatan Konflik Sistem</h4>
                    <p class="text-[13px] text-[#DC2626]">
                        Terdeteksi ada <strong>{{ $activeCount }} periode ujian komprehensif</strong> yang sedang berstatus AKTIF secara bersamaan. Harap segera set hanya menjadi 1 periode aktif agar mahasiswa tidak bingung.
                    </p>
                </div>
            </div>
        </div>
        @endif



        <!-- Search & Filter Area -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" placeholder="Cari periode ujian..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-shadow text-slate-700 placeholder:text-slate-400">
            </div>
            
            <div class="relative min-w-[200px]">
                <select class="w-full appearance-none pl-4 pr-10 py-2 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-shadow cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Aktif</option>
                    <option value="completed">Selesai</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-grey-100/80 overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-[14px] text-grey-600">
                    <thead class="bg-grey-50/70 border-b border-grey-100/80 text-[12px] font-bold text-grey-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-12 text-center">
                                <input type="checkbox" class="w-4 h-4 rounded border-grey-300 text-primary-600 focus:ring-primary-500/20 cursor-pointer">
                            </th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Nama Periode</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Timeline Pendaftaran</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Rentang Ujian</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100/50">
                        @forelse($periodes as $periode)
                        <tr class="hover:bg-grey-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="w-4 h-4 rounded border-grey-300 text-primary-600 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-grey-900 border-b border-grey-300 border-dashed pb-0.5">{{ $periode->nama_periode }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-grey-600 font-medium">
                                {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-grey-600 font-medium">
                                @if($periode->tanggal_mulai_ujian && $periode->tanggal_selesai_ujian)
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->format('d M') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->format('d M Y') }}
                                @else
                                    <span class="text-grey-400 italic">Belum diatur</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($periode->status === 'aktif')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-bold bg-success-50 text-success-700 tracking-wide border border-success-200/50">AKTIF</span>
                                @elseif($periode->status === 'selesai')
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-bold bg-grey-100 text-grey-700 tracking-wide border border-grey-300">SELESAI</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[12px] font-bold bg-warning-50 text-warning-700 tracking-wide border border-warning-200/50">DRAFT</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    @php
                                        $periodeData = [
                                            'id' => $periode->id,
                                            'nama_periode' => $periode->nama_periode,
                                            'tanggal_mulai' => $periode->tanggal_mulai ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d') : null,
                                            'tanggal_selesai' => $periode->tanggal_selesai ? \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d') : null,
                                            'tanggal_mulai_ujian' => $periode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->format('Y-m-d') : null,
                                            'tanggal_selesai_ujian' => $periode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->format('Y-m-d') : null,
                                            'status' => $periode->status,
                                            'deskripsi' => $periode->deskripsi,
                                        ];
                                    @endphp
                                    <button @click="editData = {{ json_encode($periodeData) }}; editModal = true" class="text-primary-600 hover:text-primary-800 bg-primary-50 px-3 py-1.5 font-semibold rounded-lg transition-colors border border-primary-100">Edit</button>
                                    
                                    <form action="{{ route('banksoal.periode.destroy', $periode->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-error-600 hover:text-error-800 bg-error-50 px-3 py-1.5 font-semibold rounded-lg transition-colors border border-error-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <!-- Empty State Row -->
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center border-b border-transparent">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 bg-slate-50 flex items-center justify-center rounded-full mb-3">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-[13px] font-medium text-slate-700">Manajemen Periode Ujian Kosong</h3>
                                    <p class="text-xs text-slate-500 mt-1">Silakan klik "Buat Periode Baru" di atas untuk menambahkan data.</p>
                                </div>
                            </td>
                        </tr>
                        @endempty
                    </tbody>
                </table>
            </div>

            <!-- Pagination (Placeholder) -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-[13px] text-slate-500">Menampilkan 1 hingga 2 dari 2 data</span>
                <div class="flex items-center gap-1.5">
                    <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-400 bg-white cursor-not-allowed">Periouse</button>
                    <button class="px-3 py-1.5 border border-slate-200 bg-white rounded-lg text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">1</button>
                    <button class="px-3 py-1.5 border border-slate-200 bg-white rounded-lg text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">Next</button>
                </div>
            </div>
        </div>

        <!-- Modal Popup: Setup Periode Baru -->
        <div x-show="openModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
            
            <!-- Dimmed Backdrop -->
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
                 @click="openModal = false">
            </div>

            <!-- Modal Content Wrapper -->
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Setup Periode Baru</h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 sm:p-8 overflow-y-auto">
                    <!-- Alert Info -->
                    <div class="mb-8 bg-primary-50 border border-primary-100/50 rounded-[16px] p-5 flex gap-4">
                        <svg class="w-6 h-6 text-primary-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-[14px] leading-relaxed text-primary-800 font-medium">
                            Pastikan seluruh data periode terisi dengan benar. Mahasiswa hanya dapat mendaftar jika periode dalam status aktif dan masih dalam rentang masa pendaftaran.
                        </div>
                    </div>

                    <!-- Setup Form Grid -->
                    <form action="{{ route('banksoal.periode.store') }}" method="POST" id="formPeriodeBaru" class="space-y-6">
                        @csrf
                        <!-- Box 1: Nama Periode -->
                        <div class="space-y-2">
                            <x-ui.label required class="text-[15px] font-semibold text-grey-700">Nama Periode</x-ui.label>
                            <x-ui.input type="text" name="nama_periode" placeholder="Misal: Periode Ujian Komprehensif bulan Februari 2026" required class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none placeholder:text-grey-400" />
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <x-ui.label required class="text-[15px] font-semibold text-grey-700">Tanggal Buka Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai" required class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label required class="text-[15px] font-semibold text-grey-700">Tanggal Tutup Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai" required class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <x-ui.label class="text-[15px] font-semibold text-grey-700">Tanggal Mulai Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian" class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label class="text-[15px] font-semibold text-grey-700">Tanggal Selesai Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian" class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                        </div>

                        <!-- Box 5: Toggle -->
                        <div x-data="{ isActive: false }" class="flex items-center justify-between pt-4 border-t border-grey-100">
                            <div>
                                <h4 class="text-[16px] font-bold text-grey-900">Aktifkan Pendaftaran</h4>
                                <p class="text-sm text-grey-500 mt-0.5">Memungkinkan mahasiswa melihat dan mendaftar di periode ini.</p>
                            </div>
                            <button type="button" 
                                    @click="isActive = !isActive"
                                    :class="isActive ? 'bg-success-500' : 'bg-grey-200'"
                                    class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-primary-500/20">
                                <span aria-hidden="true" 
                                      :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                                      class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                            <input type="hidden" name="status" :value="isActive ? 'aktif' : 'draft'">
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 sm:px-8 py-5 border-t border-grey-100/80 flex items-center justify-end gap-4 bg-grey-50/50">
                    <button @click="openModal = false" type="button" class="px-6 py-3 border border-grey-200 text-grey-700 font-bold bg-white rounded-xl hover:bg-grey-50 hover:text-grey-900 transition-colors shadow-sm">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formPeriodeBaru').submit()" class="px-6 py-3 bg-primary-500 hover:bg-primary-400 text-white font-bold rounded-xl shadow-sm transition-all shadow-primary-500/25">
                        Simpan Periode
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Popup: Edit Periode -->
        <div x-show="editModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-cloak>
            
            <!-- Dimmed Backdrop -->
            <div x-show="editModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
                 @click="editModal = false">
            </div>

            <!-- Modal Content Wrapper -->
            <div x-show="editModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Edit Periode Ujian</h3>
                    <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 sm:p-8 overflow-y-auto">
                    <!-- Setup Form Grid -->
                    <form :action="`{{ url('bank-soal/admin/periode/setup') }}/${editData.id}`" method="POST" id="formEditPeriode" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Box 1: Nama Periode -->
                        <div class="space-y-2">
                            <x-ui.label required class="text-[15px] font-semibold text-grey-700">Nama Periode</x-ui.label>
                            <x-ui.input type="text" name="nama_periode" x-model="editData.nama_periode" required class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none placeholder:text-grey-400" />
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <x-ui.label required class="text-[15px] font-semibold text-grey-700">Tanggal Buka Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label required class="text-[15px] font-semibold text-grey-700">Tanggal Tutup Pendaftaran</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai" required class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <x-ui.label class="text-[15px] font-semibold text-grey-700">Tanggal Mulai Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_mulai_ujian" x-model="editData.tanggal_mulai_ujian" class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                            <div class="space-y-2">
                                <x-ui.label class="text-[15px] font-semibold text-grey-700">Tanggal Selesai Ujian</x-ui.label>
                                <x-ui.input type="date" name="tanggal_selesai_ujian" x-model="editData.tanggal_selesai_ujian" class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none" />
                            </div>
                        </div>

                        <!-- Status & Keterangan -->
                        <div class="space-y-2">
                            <x-ui.label required class="text-[15px] font-semibold text-grey-700">Status Periode</x-ui.label>
                            <select name="status" x-model="editData.status" required class="w-full h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none cursor-pointer">
                                <option value="draft">Draft (Tidak aktif)</option>
                                <option value="aktif">Aktif (Pendaftaran Buka)</option>
                                <option value="selesai">Selesai (Ditutup)</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 sm:px-8 py-5 border-t border-grey-100/80 flex items-center justify-end gap-4 bg-grey-50/50">
                    <button @click="editModal = false" type="button" class="px-6 py-3 border border-grey-200 text-grey-700 font-bold bg-white rounded-xl hover:bg-grey-50 hover:text-grey-900 transition-colors shadow-sm">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formEditPeriode').submit()" class="px-6 py-3 bg-primary-500 hover:bg-primary-400 text-white font-bold rounded-xl shadow-sm transition-all shadow-primary-500/25">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-banksoal::layouts.admin>

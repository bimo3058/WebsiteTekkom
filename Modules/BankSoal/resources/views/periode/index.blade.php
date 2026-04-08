<x-banksoal::layouts.admin>
    <div x-data="{ openModal: false, editModal: false, editData: {} }" class="w-full">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Periode Ujian</h1>
                <p class="text-sm text-slate-500 mt-1">Atur periode pelaksanaan ujian komprehensif mahasiswa.</p>
            </div>
            
            <button @click="openModal = true" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 transition-colors rounded-xl px-5 py-2.5 text-white font-medium text-sm shadow-sm shadow-blue-600/20">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Periode Baru
            </button>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
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
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-12 text-center">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer">
                            </th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Nama Periode</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Timeline Pendaftaran</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap">Rentang Ujian</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                            <th scope="col" class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($periodes as $periode)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 cursor-pointer">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-slate-800">{{ $periode->nama_periode }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                @if($periode->tanggal_mulai_ujian && $periode->tanggal_selesai_ujian)
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai_ujian)->format('d M') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai_ujian)->format('d M Y') }}
                                @else
                                    <span class="text-slate-400 italic">Belum diatur</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($periode->status === 'aktif')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                @elseif($periode->status === 'selesai')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Selesai</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Draft</span>
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
                                    <button @click="editData = {{ json_encode($periodeData) }}; editModal = true" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-1.5 rounded-lg transition-colors">Edit</button>
                                    
                                    <form action="{{ route('banksoal.periode.destroy', $periode->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 p-1.5 rounded-lg transition-colors">Hapus</button>
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
                <div class="p-6 overflow-y-auto">
                    <!-- Alert Info -->
                    <div class="mb-6 bg-blue-50 border border-blue-100/50 rounded-xl p-4 flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-[13px] leading-relaxed text-blue-800">
                            Pastikan seluruh data periode terisi dengan benar. Mahasiswa hanya dapat mendaftar jika periode dalam status aktif dan masih dalam rentang masa pendaftaran.
                        </div>
                    </div>

                    <!-- Setup Form Grid -->
                    <form action="{{ route('banksoal.periode.store') }}" method="POST" id="formPeriodeBaru" class="space-y-5">
                        @csrf
                        <!-- Box 1: Nama Periode -->
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Nama Periode</label>
                            <input type="text" name="nama_periode" placeholder="Ujian Nov 2025" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-shadow">
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Buka Pendaftaran</label>
                                <div class="relative">
                                    <input type="date" name="tanggal_mulai" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Tutup Pendaftaran</label>
                                <div class="relative">
                                    <input type="date" name="tanggal_selesai" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                                </div>
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Mulai Ujian</label>
                                <div class="relative">
                                    <input type="date" name="tanggal_mulai_ujian" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Selesai Ujian</label>
                                <div class="relative">
                                    <input type="date" name="tanggal_selesai_ujian" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Deskripsi / Keterangan (Opsional)</label>
                            <textarea name="deskripsi" placeholder="Informasi tambahan..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-shadow"></textarea>
                        </div>

                        <!-- Box 5: Toggle -->
                        <div x-data="{ isActive: false }" class="flex items-center justify-between pt-2">
                            <div>
                                <h4 class="text-[13px] font-semibold text-slate-800">Aktifkan Pendaftaran</h4>
                            </div>
                            <button type="button" 
                                    @click="isActive = !isActive"
                                    :class="isActive ? 'bg-blue-600' : 'bg-slate-300'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                                <span aria-hidden="true" 
                                      :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                            </button>
                            <input type="hidden" name="status" :value="isActive ? 'aktif' : 'draft'">
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl bg-white">
                    <button @click="openModal = false" type="button" class="px-5 py-2 text-[13px] font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 focus:outline-none transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formPeriodeBaru').submit()" class="px-5 py-2 text-[13px] font-medium text-slate-100 bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none transition-colors shadow-sm">
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
                <div class="p-6 overflow-y-auto">
                    <!-- Setup Form Grid -->
                    <form :action="`{{ url('bank-soal/admin/periode/setup') }}/${editData.id}`" method="POST" id="formEditPeriode" class="space-y-5">
                        @csrf
                        @method('PUT')
                        
                        <!-- Box 1: Nama Periode -->
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Nama Periode</label>
                            <input type="text" name="nama_periode" x-model="editData.nama_periode" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                        </div>

                        <!-- Box 2 & 3: Tanggal Pendaftaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Buka Pendaftaran</label>
                                <input type="date" name="tanggal_mulai" x-model="editData.tanggal_mulai" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Tutup Pendaftaran</label>
                                <input type="date" name="tanggal_selesai" x-model="editData.tanggal_selesai" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                            </div>
                        </div>

                        <!-- Box 4: Rentang Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Mulai Ujian</label>
                                <input type="date" name="tanggal_mulai_ujian" x-model="editData.tanggal_mulai_ujian" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Tanggal Selesai Ujian</label>
                                <input type="date" name="tanggal_selesai_ujian" x-model="editData.tanggal_selesai_ujian" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                            </div>
                        </div>

                        <!-- Status & Keterangan -->
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Status Periode</label>
                            <select name="status" x-model="editData.status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow">
                                <option value="draft">Draft (Tidak aktif)</option>
                                <option value="aktif">Aktif (Pendaftaran Buka)</option>
                                <option value="selesai">Selesai (Ditutup)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[13px] font-semibold text-slate-800 mb-1.5">Deskripsi / Keterangan</label>
                            <textarea name="deskripsi" x-model="editData.deskripsi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 transition-shadow"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl bg-white">
                    <button @click="editModal = false" type="button" class="px-5 py-2 text-[13px] font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 focus:outline-none transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formEditPeriode').submit()" class="px-5 py-2 text-[13px] font-medium text-slate-100 bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none transition-colors shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-banksoal::layouts.admin>

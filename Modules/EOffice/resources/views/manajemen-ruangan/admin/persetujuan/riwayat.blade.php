<x-eoffice::manajemen-ruangan.layout pageTitle="Arsip & Rekap">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Arsip & Rekap</h1>
            <p class="mp-page-sub">Gudang arsip seluruh data histori pengajuan ruangan yang telah berstatus Selesai, Ditolak, atau Dibatalkan.</p>
        </div>
    </div>

    @if(session('success'))
        <div
            class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-[12px] mt-6" style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);"
        x-data="arsipManager()">
        
        <!-- Header Actions -->
        <div
            class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Arsip Peminjaman</h2>

            <div class="flex flex-wrap items-center gap-2.5">
                <form action="{{ route('eoffice.peminjaman.admin.riwayat.index') }}" method="GET"
                    class="flex flex-wrap items-center gap-2.5">
                    {{-- Search --}}
                    <div class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full sm:w-56 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:bg-slate-50 focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] outline-none transition-all placeholder-gray-400 cursor-text"
                            placeholder="Search" x-on:input.debounce.700ms="$el.form.submit()">
                    </div>

                    {{-- Filter Engine Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="h-[38px] px-3.5 bg-white border border-gray-200 rounded-lg flex items-center gap-2 text-[13px] font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            Filter
                            @if(request()->hasAny(['status', 'ruangan_id', 'start_date', 'end_date']) && array_filter(request()->only(['status', 'ruangan_id', 'start_date', 'end_date'])))
                                <span class="w-2 h-2 rounded-full bg-[#0B266E] absolute -top-0.5 -right-0.5"></span>
                            @endif
                        </button>

                        <div x-show="open" @click.outside="open = false" style="display: none;"
                            class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-gray-100 z-50 transform origin-top-right">
                            <div class="p-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Rentang Waktu</label>
                                    <div class="flex gap-2">
                                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                                            class="w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] cursor-text">
                                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                                            class="w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] cursor-text">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Ruangan</label>
                                    <div x-data="{ 
                                            open: false, 
                                            selectedId: '{{ request('ruangan_id') }}', 
                                            selectedName: '{{ request('ruangan_id') ? addslashes($ruangans->firstWhere('id', request('ruangan_id'))->nama ?? 'Semua Ruangan') : 'Semua Ruangan' }}',
                                            selectItem(id, name) { 
                                                this.selectedId = id; 
                                                this.selectedName = name; 
                                                this.open = false; 
                                            } 
                                        }" class="relative w-full" @click.away="open = false">
                                        
                                        <input type="hidden" name="ruangan_id" :value="selectedId">

                                        <button type="button" @click="open = !open" 
                                            class="w-full flex items-center justify-between text-xs border border-gray-200 rounded p-2 bg-white hover:border-[#0B266E] focus:outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-colors cursor-pointer">
                                            <span x-text="selectedName" class="truncate text-gray-700"></span>
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                            class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-48 overflow-y-auto" style="display: none;">
                                            <div class="p-1">
                                                <button type="button" @click="selectItem('', 'Semua Ruangan')" class="w-full text-left px-3 py-2 text-xs font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == '', 'text-gray-700 hover:bg-gray-50': selectedId != ''}">Semua Ruangan</button>
                                                
                                                @foreach($ruangans as $ruangan)
                                                    <button type="button" @click="selectItem('{{ $ruangan->id }}', '{{ addslashes($ruangan->nama) }}')" class="w-full text-left px-3 py-2 mt-0.5 text-xs font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == '{{ $ruangan->id }}', 'text-gray-700 hover:bg-gray-50': selectedId != '{{ $ruangan->id }}'}">
                                                        {{ $ruangan->nama }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Status</label>
                                    <div x-data="{ 
                                            open: false, 
                                            selectedVal: '{{ request('status') }}', 
                                            get selectedName() {
                                                const map = {
                                                    'disetujui': 'Disetujui',
                                                    'ditolak': 'Ditolak',
                                                    'dibatalkan': 'Dibatalkan',
                                                    'selesai': 'Selesai'
                                                };
                                                return map[this.selectedVal] || 'Semua Status';
                                            },
                                            selectItem(val) { 
                                                this.selectedVal = val; 
                                                this.open = false; 
                                            } 
                                        }" class="relative w-full" @click.away="open = false">
                                        
                                        <input type="hidden" name="status" :value="selectedVal">

                                        <button type="button" @click="open = !open" 
                                            class="w-full flex items-center justify-between text-xs border border-gray-200 rounded p-2 bg-white hover:border-[#0B266E] focus:outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-colors cursor-pointer">
                                            <span x-text="selectedName" class="truncate text-gray-700"></span>
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                            class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] overflow-hidden" style="display: none;">
                                            <div class="p-1">
                                                <button type="button" @click="selectItem('')" class="w-full text-left px-3 py-2 text-xs font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == '', 'text-gray-700 hover:bg-gray-50': selectedVal != ''}">Semua Status</button>
                                                <button type="button" @click="selectItem('disetujui')" class="w-full text-left px-3 py-2 mt-0.5 text-xs font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'disetujui', 'text-gray-700 hover:bg-gray-50': selectedVal != 'disetujui'}">Disetujui</button>
                                                <button type="button" @click="selectItem('ditolak')" class="w-full text-left px-3 py-2 mt-0.5 text-xs font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'ditolak', 'text-gray-700 hover:bg-gray-50': selectedVal != 'ditolak'}">Ditolak</button>
                                                <button type="button" @click="selectItem('dibatalkan')" class="w-full text-left px-3 py-2 mt-0.5 text-xs font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'dibatalkan', 'text-gray-700 hover:bg-gray-50': selectedVal != 'dibatalkan'}">Dibatalkan</button>
                                                <button type="button" @click="selectItem('selesai')" class="w-full text-left px-3 py-2 mt-0.5 text-xs font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'selesai', 'text-gray-700 hover:bg-gray-50': selectedVal != 'selesai'}">Selesai</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                                    <a href="{{ route('eoffice.peminjaman.admin.riwayat.index') }}"
                                        class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 border border-transparent cursor-pointer">Reset</a>
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-[#060E2A] rounded-md hover:bg-[#030715] transition-colors cursor-pointer">Terapkan
                                        Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Quick Export Dropdown --}}
                <div class="relative" x-data="{ openExport: false }">
                    <button type="button" @click="openExport = !openExport"
                        class="h-[38px] px-3.5 bg-[#0B266E] border border-[#060E2A] rounded-lg flex items-center gap-2 text-[13px] font-medium text-white hover:bg-[#060E2A] transition-colors cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Ekspor Laporan
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openExport" @click.outside="openExport = false" style="display: none;"
                        class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden transform origin-top-right">
                        <div class="p-2 space-y-1">
                            <a href="{{ route('eoffice.peminjaman.admin.riwayat.export-pdf', request()->query()) }}" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-gray-50 flex items-start gap-3 transition-colors cursor-pointer">
                                <div class="bg-red-50 p-2 rounded-md text-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg></div>
                                <div>
                                    <div class="text-[13px] font-bold text-gray-800">Cetak PDF (Sesuai Filter)</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 leading-snug">Unduh laporan resmi format PDF sesuai data tabel saat ini</div>
                                </div>
                            </a>
                            <a href="{{ route('eoffice.peminjaman.admin.riwayat.export-excel', request()->query()) }}" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-gray-50 flex items-start gap-3 transition-colors cursor-pointer">
                                <div class="bg-green-50 p-2 rounded-md text-green-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                                <div>
                                    <div class="text-[13px] font-bold text-gray-800">Unduh Excel (Sesuai Filter)</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 leading-snug">Unduh data mentah Excel untuk diolah kembali</div>
                                </div>
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            @php
                                $startBulanIni = now()->startOfMonth()->format('Y-m-d');
                                $endBulanIni = now()->endOfMonth()->format('Y-m-d');
                            @endphp
                            <a href="{{ route('eoffice.peminjaman.admin.riwayat.export-excel', ['start_date' => $startBulanIni, 'end_date' => $endBulanIni]) }}" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-gray-50 flex items-start gap-3 transition-colors cursor-pointer">
                                <div class="bg-blue-50 p-2 rounded-md text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
                                <div>
                                    <div class="text-[13px] font-bold text-gray-800">Rekap Cepat: Bulan Ini</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 leading-snug">Langsung unduh seluruh peminjaman di bulan berjalan tanpa atur filter</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="mp-table-wrap mt-0 border-t-0">
            <table class="mp-table" style="table-layout: auto; width: 100%;">
                <thead>
                    <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Peminjam</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Ruangan</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Waktu Pemakaian</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Kegiatan</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $pinjam)
                        @php
                            $fullName = $pinjam->user->name ?? 'User Tidak Diketahui';
                            $isDosen = $pinjam->user && $pinjam->user->hasRole('dosen');
                            $identityNumber = $pinjam->user ? ($pinjam->user->student->student_number ?? $pinjam->user->lecturer->employee_number ?? $pinjam->user->external_id) : '-';
                            $roleName = $isDosen ? 'Dosen' : 'Mahasiswa';
                            
                            $displayName = $fullName;
                            if (!$isDosen) {
                                $nameParts = explode(' ', $fullName);
                                if (count($nameParts) > 2) {
                                    $displayName = $nameParts[0] . ' ' . $nameParts[1];
                                    for ($i = 2; $i < count($nameParts); $i++) {
                                        $displayName .= ' ' . strtoupper(substr($nameParts[$i], 0, 1)) . '.';
                                    }
                                }
                            }
                        @endphp
                        <tr class="mp-tr">
                            <td>
                                @if($isDosen)
                                    <div class="text-[13px] font-semibold text-[#111827] truncate max-w-[160px]" title="{{ $fullName }}">
                                        {{ $fullName }}
                                    </div>
                                @else
                                    <div class="text-[13px] font-semibold text-[#111827]" title="{{ $fullName }}">
                                        {{ $displayName }}
                                    </div>
                                @endif
                                <div class="text-[11px] font-medium text-gray-500 mt-0.5 inline-block px-1.5 py-0.5 rounded {{ $isDosen ? 'bg-[#0B266E]/10 text-[#0B266E]' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $roleName }}
                                </div>
                            </td>
                            <td class="max-w-[140px]">
                                <div class="text-[13px] font-medium text-[#111827] truncate" title="{{ $pinjam->ruangan->nama ?? 'Dihapus' }}">
                                    {{ $pinjam->ruangan->nama ?? 'Dihapus' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-[13px] font-medium text-[#111827]">
                                    {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }} WIB
                                </div>
                            </td>
                            <td class="max-w-[140px]">
                                <div class="text-[12px] text-gray-600 truncate" title="{{ $pinjam->tujuan }}">
                                    {{ $pinjam->tujuan }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                    if (strtolower($pinjam->status) === 'disetujui' || strtolower($pinjam->status) === 'selesai')
                                        $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                    elseif (strtolower($pinjam->status) === 'ditolak' || strtolower($pinjam->status) === 'dibatalkan')
                                        $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                    elseif (strtolower($pinjam->status) === 'menunggu')
                                        $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                @endphp
                                <span style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 10px; border-radius:9999px; white-space:nowrap; text-transform:uppercase; display:inline-block;">
                                    {{ $pinjam->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" @click="openDetail({{ json_encode([
                                    'nama' => $fullName,
                                    'nim_nip' => $identityNumber,
                                    'telepon' => $pinjam->nomor_telepon ?: '-',
                                    'role' => $roleName,
                                    'ruangan' => $pinjam->ruangan->nama ?? '-',
                                    'tanggal' => \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('l, d F Y'),
                                    'jam' => \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') . ' WIB',
                                    'durasi' => \Carbon\Carbon::parse($pinjam->jam_mulai)->diffInHours(\Carbon\Carbon::parse($pinjam->jam_selesai)) . ' Jam',
                                    'kegiatan' => $pinjam->tujuan,
                                    'berkas' => $pinjam->berkas_pendukung ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($pinjam->berkas_pendukung) : null,
                                    'waktu_pengajuan' => \Carbon\Carbon::parse($pinjam->created_at)->translatedFormat('d M Y, H:i'),
                                    'waktu_diproses' => $pinjam->waktu_approval ? \Carbon\Carbon::parse($pinjam->waktu_approval)->translatedFormat('d M Y, H:i') : '-',
                                    'diproses_oleh' => '-', // To be implemented later (need relation to admin)
                                    'alasan_penolakan' => $pinjam->alasan_penolakan,
                                    'status' => $pinjam->status
                                ]) }})"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[#0B266E] hover:bg-[#0B266E]/10 hover:text-[#060E2A] transition-colors tooltip-btn cursor-pointer" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                
                                @if(auth()->user()->hasRole('superadmin'))
                                    <!-- Soft Delete action for superadmin (Placeholder for now) -->
                                    <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors tooltip-btn cursor-pointer" title="Hapus (Soft Delete)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="mp-tr">
                            <td colspan="6" class="py-12 text-center text-gray-500 text-[13px]">Belum ada arsip peminjaman yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
            <div class="flex items-center gap-4">
                <div class="flex items-center border border-slate-200 rounded-md bg-white overflow-visible text-[13px] shadow-sm">
                    <span class="px-3 py-1.5 text-slate-600 font-medium border-r border-slate-200 bg-slate-50 shrink-0">Per halaman</span>
                    <div x-data="{ 
                        open: false, 
                        value: '{{ request('per_page', 10) }}', 
                        select(val, url) { 
                            this.value = val; 
                            window.location.href = url; 
                        } 
                    }" class="relative w-[65px]" @click.away="open = false">
                        
                        <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-2.5 py-1.5 text-slate-900 font-bold bg-white hover:bg-slate-50 focus:outline-none transition-colors rounded-r-md cursor-pointer">
                            <span x-text="value"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" 
                            class="absolute left-0 bottom-full mb-1 w-full min-w-[70px] bg-white border border-slate-200 rounded-md shadow-lg z-[60] overflow-hidden" style="display: none;">
                            <div class="py-1">
                                <button type="button" @click="select('10', '{{ request()->fullUrlWithQuery(['per_page' => 10]) }}')" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': value == '10'}">10</button>
                                <button type="button" @click="select('25', '{{ request()->fullUrlWithQuery(['per_page' => 25]) }}')" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': value == '25'}">25</button>
                                <button type="button" @click="select('50', '{{ request()->fullUrlWithQuery(['per_page' => 50]) }}')" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': value == '50'}">50</button>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-xs font-medium text-slate-600">
                    Menampilkan <span class="font-bold text-slate-800">{{ $peminjamans->firstItem() ?? 0 }}</span>
                    sampai <span class="font-bold text-slate-800">{{ $peminjamans->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-slate-800">{{ $peminjamans->total() }}</span> entri
                </p>
            </div>
            
            <div class="flex items-center gap-1.5">
                @if ($peminjamans->onFirstPage())
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <a href="{{ $peminjamans->previousPageUrl() }}"
                        class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                <div
                    class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
                    @foreach ($peminjamans->getUrlRange(max(1, $peminjamans->currentPage() - 2), min($peminjamans->lastPage(), $peminjamans->currentPage() + 2)) as $page => $url)
                        @if ($page == $peminjamans->currentPage())
                            <span
                                class="bg-[#354371] text-white w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors cursor-pointer">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors cursor-pointer">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                @if ($peminjamans->hasMorePages())
                    <a href="{{ $peminjamans->nextPageUrl() }}"
                        class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Pop-up Modal Detail Peminjaman -->
        <div x-show="isDetailOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/60 backdrop-blur-sm p-4">
            <div x-show="isDetailOpen" @click.outside="closeDetail()"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0B266E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Detail Arsip Peminjaman
                    </h3>
                    <button @click="closeDetail()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="space-y-6">
                        <!-- Bagian A: Identitas Peminjam -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">A. Identitas Peminjam</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                                <div>
                                    <div class="text-[11px] text-gray-500 mb-0.5">Nama Lengkap</div>
                                    <div class="text-[13px] font-semibold text-gray-900" x-text="detailData.nama"></div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-gray-500 mb-0.5">NIM / NIP</div>
                                    <div class="text-[13px] font-medium text-gray-900" x-text="detailData.nim_nip"></div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-gray-500 mb-0.5">Nomor Telepon/WA</div>
                                    <div class="text-[13px] font-medium text-gray-900" x-text="detailData.telepon"></div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-gray-500 mb-0.5">Role / Peran</div>
                                    <div class="inline-block px-2 py-0.5 text-[11px] font-semibold rounded bg-blue-100 text-blue-700" x-text="detailData.role"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian B: Detail Peminjaman -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">B. Detail Peminjaman</h4>
                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-[11px] text-gray-500 mb-0.5">Ruangan</div>
                                        <div class="text-[13px] font-bold text-[#0B266E] flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            <span x-text="detailData.ruangan"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[11px] text-gray-500 mb-0.5">Tanggal & Waktu</div>
                                        <div class="text-[13px] font-semibold text-gray-900" x-text="detailData.tanggal"></div>
                                        <div class="text-[12px] text-gray-600 mt-0.5"><span x-text="detailData.jam"></span> <span class="text-gray-400 mx-1">•</span> <span x-text="detailData.durasi"></span></div>
                                    </div>
                                </div>
                                
                                <div class="pt-3 border-t border-gray-100">
                                    <div class="text-[11px] text-gray-500 mb-1">Kegiatan Lengkap</div>
                                    <p class="text-[13px] text-gray-800 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100" x-text="detailData.kegiatan"></p>
                                </div>

                                <template x-if="detailData.berkas">
                                    <div class="pt-3 border-t border-gray-100">
                                        <div class="text-[11px] text-gray-500 mb-1.5">Dokumen Pendukung</div>
                                        <a :href="detailData.berkas" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#0B266E]/10 text-[#0B266E] hover:bg-[#0B266E]/20 rounded-lg text-xs font-semibold transition-colors border border-[#0B266E]/20 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Lihat Berkas Terlampir
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Bagian C: Riwayat Proses (Jejak Audit) -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">C. Jejak Audit</h4>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <div class="text-[11px] text-slate-500 mb-0.5">Waktu Pengajuan</div>
                                        <div class="text-[13px] font-medium text-slate-900" x-text="detailData.waktu_pengajuan"></div>
                                    </div>
                                    <div>
                                        <div class="text-[11px] text-slate-500 mb-0.5">Waktu Diproses</div>
                                        <div class="text-[13px] font-medium text-slate-900" x-text="detailData.waktu_diproses"></div>
                                    </div>
                                    <div>
                                        <div class="text-[11px] text-slate-500 mb-0.5">Diproses Oleh (Admin)</div>
                                        <div class="text-[13px] font-medium text-slate-900" x-text="detailData.diproses_oleh"></div>
                                    </div>
                                </div>
                                
                                <template x-if="detailData.status === 'ditolak' || detailData.status === 'dibatalkan'">
                                    <div class="mt-3 pt-3 border-t border-slate-200">
                                        <div class="bg-red-50 border border-red-100 rounded-lg p-3 flex gap-3 items-start">
                                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            <div>
                                                <h5 class="text-[12px] font-bold text-red-800">Catatan / Alasan Penolakan</h5>
                                                <p class="text-[12px] text-red-700 mt-0.5 leading-relaxed" x-text="detailData.alasan_penolakan || 'Tidak ada keterangan tambahan.'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function arsipManager() {
            return {
                isDetailOpen: false,
                detailData: {},
                openDetail(data) {
                    this.detailData = data;
                    this.isDetailOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeDetail() {
                    this.isDetailOpen = false;
                    setTimeout(() => {
                        this.detailData = {};
                        document.body.style.overflow = '';
                    }, 300);
                }
            };
        }
    </script>
</x-eoffice::manajemen-ruangan.layout>
<x-eoffice::manajemen-ruangan.layout pageTitle="Persetujuan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Verifikasi Peminjaman</h1>
            <p class="mp-page-sub">Kelola dan verifikasi seluruh permohonan peminjaman ruangan yang diajukan oleh
                pengguna.</p>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;" x-data="persetujuanManager()">
        <div class="mp-card-body">
            <div
                class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Antrean</h2>

                <form action="{{ route('eoffice.peminjaman.admin.persetujuan.index') }}" method="GET"
                    class="w-full md:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">

                    {{-- Search --}}
                    <div class="relative w-full sm:w-auto flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full sm:w-64 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:bg-slate-50 focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] outline-none transition-all placeholder-gray-400"
                            placeholder="Cari Nama/NIM/Ruangan..." x-on:input.debounce.700ms="$el.form.submit()">
                    </div>

                    {{-- Filter Ruangan --}}
                    <div x-data="{ 
                        open: false, 
                        selectedId: '{{ request('ruangan_id') }}', 
                        selectItem(id) { 
                            this.selectedId = id; 
                            $refs.ruanganInput.value = id;
                            $refs.ruanganInput.form.submit();
                        } 
                    }" class="relative inline-block text-left w-full sm:w-auto" @click.away="open = false">
                        
                        <input type="hidden" name="ruangan_id" x-ref="ruanganInput" :value="selectedId">

                        <button type="button" @click="open = !open" 
                            class="relative inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 border border-slate-200 rounded-md bg-white hover:bg-slate-50 focus:outline-none transition-colors font-semibold text-[13px] h-9 text-slate-700 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 shrink-0 text-slate-600">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            Filter
                            <span x-show="selectedId !== ''" style="display: none;" class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-[#0B266E] border-2 border-white box-content"></span>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" 
                            class="absolute left-0 origin-top-left sm:left-auto sm:right-0 sm:origin-top-right top-full mt-2 w-full sm:w-44 bg-white border border-slate-200 rounded-xl shadow-lg z-50 overflow-hidden" style="display: none;">
                            <div class="py-2 max-h-64 overflow-y-auto">
                                <button type="button" @click="selectItem('')" class="w-[calc(100%-16px)] text-left px-3 py-2 mx-2 mb-1 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#F0F2F9] text-[#0B266E] font-bold': selectedId == '', 'text-slate-700 hover:bg-slate-50 font-medium': selectedId != ''}">Semua Ruangan</button>
                                
                                @foreach($ruangans as $ruangan)
                                    <button type="button" @click="selectItem('{{ $ruangan->id }}')" class="w-[calc(100%-16px)] text-left px-3 py-2 mx-2 mb-1 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#F0F2F9] text-[#0B266E] font-bold': selectedId == '{{ $ruangan->id }}', 'text-slate-700 hover:bg-slate-50 font-medium': selectedId != '{{ $ruangan->id }}'}">
                                        {{ $ruangan->nama }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table" style="table-layout: auto; width: 100%;">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Peminjam</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Ruangan & Kegiatan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Waktu</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Status</th>
                            <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody x-data="{
                        init() {
                            setInterval(() => {
                                fetch(window.location.href)
                                    .then(res => res.text())
                                    .then(html => {
                                        const doc = new DOMParser().parseFromString(html, 'text/html');
                                        const newTbody = doc.querySelector('tbody');
                                        if (newTbody && this.$el.innerHTML !== newTbody.innerHTML) {
                                            this.$el.innerHTML = newTbody.innerHTML;
                                        }
                                    })
                                    .catch(err => console.error('Polling error:', err));
                            }, 15000);
                        }
                    }">
                        @forelse($peminjamans as $pinjam)
                            <tr class="mp-tr">
                                <td>
                                    <div class="text-[13px] font-medium text-[#111827] flex items-center gap-2">
                                        @php
                                            $fullName = $pinjam->user->name ?? 'User Tidak Diketahui';
                                            $isDosen = $pinjam->user && $pinjam->user->hasRole('dosen');
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
                                        @if($isDosen)
                                            <div class="truncate max-w-[160px]" title="{{ $fullName }}">{{ $fullName }}</div>
                                        @else
                                            <div title="{{ $fullName }}">{{ $displayName }}</div>
                                        @endif
                                        @if($pinjam->created_by && $pinjam->created_by !== $pinjam->user_id)
                                            <span
                                                class="bg-slate-100 text-slate-500 border border-slate-200 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider flex-shrink-0"
                                                title="Didaftarkan oleh Sistem/Admin">By Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        @php
                                            $identityNumber = null;
                                            if ($pinjam->user) {
                                                $identityNumber = $pinjam->user->student->student_number ?? $pinjam->user->lecturer->employee_number ?? $pinjam->user->external_id;
                                            }
                                        @endphp
                                        @if($identityNumber)
                                            <span class="mr-1">({{ $identityNumber }})</span>
                                        @endif
                                        @if($pinjam->nomor_telepon)
                                            @php
                                                $waNumber = preg_replace('/^0/', '62', $pinjam->nomor_telepon);
                                            @endphp
                                            <a href="https://wa.me/{{ $waNumber }}" target="_blank" title="Hubungi via WhatsApp" class="text-[#25D366] hover:text-[#1da851] hover:underline font-semibold transition-colors">
                                                {{ $pinjam->nomor_telepon }}
                                            </a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="max-w-[200px]">
                                    <div class="text-[13px] font-medium text-[#111827] truncate" title="{{ $pinjam->ruangan->nama ?? 'Dihapus' }}">
                                        {{ $pinjam->ruangan->nama ?? 'Dihapus' }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 truncate mt-0.5" title="{{ $pinjam->tujuan }}">
                                        {{ $pinjam->tujuan }}
                                    </div>
                                        @if($pinjam->berkas_pendukung)
                                            <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($pinjam->berkas_pendukung) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-[#0065ff] hover:text-[#0052cc] transition-colors mt-1">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                Lihat Berkas
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-[13px] font-medium text-[#111827]">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                        <span class="text-gray-400 mx-1">•</span>
                                        {{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                        if ($pinjam->status === 'disetujui')
                                            $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                        elseif ($pinjam->status === 'ditolak')
                                            $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                        elseif ($pinjam->status === 'menunggu')
                                            $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                        elseif ($pinjam->status === 'selesai')
                                            $st = ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'];

                                        $berkasUrl = $pinjam->berkas_pendukung ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($pinjam->berkas_pendukung) : '';
                                    @endphp
                                    <span style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 12px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                        {{ $pinjam->status }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <button
                                        @click="openAction({{ $pinjam->id }}, '{{ addslashes($pinjam->user->name ?? "User") }}', '{{ addslashes($pinjam->user->student->student_number ?? $pinjam->user->external_id ?? "-") }}', '{{ addslashes($pinjam->nomor_telepon ?? $pinjam->user->whatsapp ?? "-") }}', '{{ $pinjam->status }}', '{{ $pinjam->ruangan_id }}', '{{ $pinjam->tanggal_pinjam }}', '{{ substr($pinjam->jam_mulai, 0, 5) }}', '{{ substr($pinjam->jam_selesai, 0, 5) }}', '{{ addslashes($pinjam->ruangan->nama ?? 'Ruangan Dihapus') }}', '{{ addslashes($pinjam->tujuan) }}', '{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}', '{{ $berkasUrl }}')"
                                        class="h-8 px-4 rounded-md bg-white border border-gray-200 text-[#0B266E] text-[12px] font-bold hover:bg-gray-50 shadow-sm transition-colors cursor-pointer">
                                        Kelola
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="mp-tr">
                                <td colspan="5" style="padding:40px; text-align:center;">
                                    <div style="font-size:13px; font-weight:500; color:#666D80;">Belum ada antrean permohonan ruangan atau jadwal berjalan yang masuk.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
                <div class="flex items-center gap-4 text-[13px] text-slate-500">
                    <div class="flex items-center gap-2">
                        <span class="font-medium">Per halaman</span>
                        <div x-data="{ 
                            open: false, 
                            selectedVal: '{{ request('per_page', 10) }}',
                            selectItem(val, url) {
                                this.selectedVal = val;
                                this.open = false;
                                window.location.href = url;
                            }
                        }" class="relative" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-between px-3 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border border-slate-200 rounded-lg shadow-sm gap-2 min-w-[64px] transition-colors">
                                <span x-text="selectedVal"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                    :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
    
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 bottom-full mb-1 w-full bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-hidden"
                                style="display: none;">
                                <div class="p-1">
                                    <button type="button"
                                        @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                    <button type="button"
                                        @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                    <button type="button"
                                        @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="w-px h-4 bg-slate-200"></div>
    
                    <p class="hidden md:block font-medium text-slate-500">
                        Menampilkan <span class="font-bold text-slate-800">{{ $peminjamans->firstItem() ?? 0 }}</span>
                        sampai <span class="font-bold text-slate-800">{{ $peminjamans->lastItem() ?? 0 }}</span>
                        dari <span class="font-bold text-slate-800">{{ $peminjamans->total() }}</span> entri
                    </p>
                </div>
    
                <div class="flex items-center gap-1.5">
                    @if ($peminjamans->onFirstPage())
                        <button disabled
                            class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    @else
                        <a href="{{ $peminjamans->previousPageUrl() }}"
                            class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif
    
                    @php
                        $startPage = max(1, $peminjamans->currentPage() - 1);
                        $endPage = min($peminjamans->lastPage(), $peminjamans->currentPage() + 1);
    
                        // Adjust start and end to always show at least 3 pages if possible
                        if ($endPage - $startPage < 2) {
                            if ($startPage == 1) {
                                $endPage = min($peminjamans->lastPage(), 3);
                            } elseif ($endPage == $peminjamans->lastPage()) {
                                $startPage = max(1, $peminjamans->lastPage() - 2);
                            }
                        }
                    @endphp
    
                    @if($startPage > 1)
                        <a href="{{ $peminjamans->url(1) }}"
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                        @if($startPage > 2)
                            <span
                                class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                        @endif
                    @endif
    
                    @foreach ($peminjamans->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $peminjamans->currentPage())
                            <span
                                class="bg-[#0f1b40] shadow-md shadow-[#0f1b40]/20 text-white font-bold text-[13px] w-8 h-8 flex items-center justify-center rounded-lg transition-colors">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
    
                    @if($endPage < $peminjamans->lastPage())
                        @if($endPage < $peminjamans->lastPage() - 1)
                            <span
                                class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                        @endif
                        <a href="{{ $peminjamans->url($peminjamans->lastPage()) }}"
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $peminjamans->lastPage() }}</a>
                    @endif
    
                    @if ($peminjamans->hasMorePages())
                        <a href="{{ $peminjamans->nextPageUrl() }}"
                            class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <button disabled
                            class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Modal Tindakan --}}
            <div x-show="modalTindakan" style="display: none;"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div x-show="modalTindakan" x-transition.opacity
                    class="fixed inset-0 bg-slate-900/60 transition-opacity" @click="closeModal()">
                </div>

                <div x-show="modalTindakan" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-8"
                    class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col border border-gray-100">

                    <div
                        class="px-6 py-4 border-b border-[#0B266E]/10 flex-shrink-0 flex justify-between items-center bg-[#0B266E]/[0.06] z-10">
                        <div class="flex items-center gap-3">
                            <div class="bg-[#0B266E] p-2.5 rounded-xl">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-[#1A1C1E]">Verifikasi Peminjaman</h3>
                                <p class="text-xs text-[#0B266E] mt-0.5">Penanganan peminjaman dari <span class="font-bold" x-text="selectedName"></span></p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()"
                            class="text-[#0B266E] hover:bg-[#0B266E]/10 transition-colors cursor-pointer p-1.5 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="actionForm" method="POST" :action="getFormAction()"
                        class="flex flex-col flex-1 overflow-hidden">
                        @csrf
                        <input type="hidden" name="status" x-model="selectedAction">

                        <div class="p-6 flex-1 overflow-y-auto bg-white">

                            <!-- Ringkasan Informasi -->
                            <div class="mb-5 p-4 bg-white border border-slate-200 rounded-xl space-y-3 shadow-sm">
                                <h4
                                    class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2">
                                    Informasi Peminjam</h4>
                                <div class="grid grid-cols-[80px_1fr] gap-x-2 gap-y-2 text-[13px] mb-3 border-b border-slate-100 pb-3">
                                    <div class="text-slate-500 font-medium">Nama</div>
                                    <div class="text-slate-800 font-bold" x-text="selectedName"></div>

                                    <div class="text-slate-500 font-medium">NIM/NIP</div>
                                    <div class="text-slate-800 font-medium" x-text="selectedNim"></div>

                                    <div class="text-slate-500 font-medium flex items-center">No. Telp</div>
                                    <div class="font-medium flex items-center">
                                        <template x-if="selectedTelp && selectedTelp !== '-'">
                                            <a :href="'https://wa.me/' + (selectedTelp.toString().startsWith('0') ? '62' + selectedTelp.toString().substring(1) : selectedTelp)" 
                                                target="_blank" 
                                                class="inline-flex items-center gap-1.5 text-[#25D366] hover:text-[#1da851] hover:underline transition-colors group"
                                                title="Hubungi via WhatsApp">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                                </svg>
                                                <span x-text="selectedTelp"></span>
                                            </a>
                                        </template>
                                        <template x-if="!selectedTelp || selectedTelp === '-'">
                                            <span class="text-slate-800" x-text="selectedTelp || '-'"></span>
                                        </template>
                                    </div>
                                </div>

                                <h4
                                    class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2 mt-3">
                                    Informasi Peminjaman</h4>
                                <div class="grid grid-cols-[80px_1fr] gap-x-2 gap-y-2 text-[13px]">
                                    <div class="text-slate-500 font-medium">Ruangan</div>
                                    <div class="text-slate-800 font-bold" x-text="selectedRuanganName"></div>

                                    <div class="text-slate-500 font-medium">Waktu</div>
                                    <div class="text-slate-800 font-bold">
                                        <span x-text="selectedFormatTanggal"></span> <span
                                            class="bg-blue-50 text-[#0B266E] px-1.5 py-0.5 rounded text-xs ml-1 font-semibold border border-blue-200"
                                            x-text="selectedJamMulai + ' - ' + selectedJamSelesai + ' WIB'"></span>
                                    </div>

                                    <div class="text-slate-500 font-medium">Kegiatan</div>
                                    <div class="text-slate-800 font-medium leading-snug" x-text="selectedKegiatan">
                                    </div>

                                    <div class="text-slate-500 font-medium pt-1">Berkas</div>
                                    <div class="pt-1">
                                        <template x-if="selectedBerkasUrl">
                                            <a :href="selectedBerkasUrl" target="_blank"
                                                class="text-[13px] font-bold text-[#0065ff] hover:text-[#0052cc] hover:underline transition-colors mt-0.5 inline-block">
                                                Lihat Berkas
                                            </a>
                                        </template>
                                        <template x-if="!selectedBerkasUrl">
                                            <span class="text-gray-400 italic text-[12px]">Tidak dilampirkan</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Jika Status Menunggu -->
                            <div x-show="selectedStatus === 'menunggu'" class="grid grid-cols-2 gap-3 mb-5">
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="selectedAction" value="disetujui" class="sr-only">
                                    <div :class="selectedAction === 'disetujui' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                        class="rounded-xl border-2 px-4 py-3.5 transition-all text-center font-semibold text-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7">
                                            </path>
                                        </svg>
                                        Setujui
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="selectedAction" value="ditolak" class="sr-only">
                                    <div :class="selectedAction === 'ditolak' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                        class="rounded-xl border-2 px-4 py-3.5 transition-all text-center font-semibold text-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12">
                                            </path>
                                        </svg>
                                        Tolak
                                    </div>
                                </label>
                            </div>

                            <!-- Jika Status Disetujui -->
                            <div x-show="selectedStatus === 'disetujui'" style="display: none;" class="mb-5">
                                <div
                                    class="p-3 bg-amber-50 border border-amber-100 rounded-xl flex gap-3 text-amber-800 text-[13px] leading-relaxed mb-4">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: Status permohonan ini <strong>Telah Disetujui</strong>. Segala bentuk
                                        modifikasi akan langsung berdampak pada jadwal resmi ruangan.</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer block">
                                        <input type="radio" x-model="selectedAction" value="ditolak" class="sr-only">
                                        <div :class="selectedAction === 'ditolak' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                            class="rounded-xl border-2 px-3 py-2.5 transition-all text-center font-semibold text-[13px] flex flex-col items-center justify-center gap-1.5 h-full leading-tight">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24"
                                                :class="selectedAction === 'ditolak' ? 'text-red-700' : 'text-red-600/70'">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <span>Cabut Hak<br>(Batalkan)</span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer block">
                                        <input type="radio" x-model="selectedAction" value="edit" class="sr-only">
                                        <div :class="selectedAction === 'edit' ? 'border-[#0B266E] bg-blue-50 text-[#0B266E]' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                            class="rounded-xl border-2 px-3 py-2.5 transition-all text-center font-semibold text-[13px] flex flex-col items-center justify-center gap-1.5 h-full leading-tight">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24"
                                                :class="selectedAction === 'edit' ? 'text-[#0B266E]' : 'text-[#0B266E]/70'">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            <span>Modifikasi<br>Waktu & Ruang</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Formulir Edit Overrride (Tampil Jika dipilih) -->
                            <div x-show="selectedAction === 'edit'" x-transition
                                class="mt-4 mb-2 p-4 bg-slate-50 border border-slate-100 rounded-xl relative overflow-hidden group">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#0B266E]"></div>
                                <h4 class="text-[13px] font-bold text-slate-800 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#0B266E]" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Konfigurasi Jadwal Baru
                                </h4>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Pindahkan Ke Ruangan <span class="text-red-500">*</span></label>
                                            <div x-data="{ 
                                                    open: false,
                                                    get selectedName() {
                                                        const room = [
                                                            @foreach($ruangans as $ruangan)
                                                                {id: '{{ $ruangan->id }}', name: '{{ addslashes($ruangan->nama) }}'},
                                                            @endforeach
                                                        ].find(r => r.id == currentRoom);
                                                        return room ? room.name : 'Pilih Ruangan...';
                                                    },
                                                    selectItem(id) { 
                                                        currentRoom = id;
                                                        this.open = false; 
                                                    } 
                                                }" class="relative w-full" @click.away="open = false">
                                                
                                                <input type="hidden" name="override_ruangan_id" :value="currentRoom" :required="selectedAction === 'edit'">

                                                <button type="button" @click="open = !open" 
                                                    class="mp-input w-full bg-white text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E] flex items-center justify-between transition-colors h-[42px] px-3 cursor-pointer">
                                                    <span x-text="selectedName" class="truncate" :class="{'text-gray-400': !currentRoom, 'text-gray-800': currentRoom}"></span>
                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                                
                                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                    class="absolute left-0 bottom-full mb-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-48 overflow-y-auto" style="display: none;">
                                                    <div class="p-1">
                                                        <button type="button" @click="selectItem('')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentRoom == '', 'text-gray-700 hover:bg-gray-50': currentRoom != ''}">Pilih Ruangan...</button>
                                                        
                                                        @foreach($ruangans as $ruangan)
                                                            <button type="button" @click="selectItem('{{ $ruangan->id }}')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentRoom == '{{ $ruangan->id }}', 'text-gray-700 hover:bg-gray-50': currentRoom != '{{ $ruangan->id }}'}">
                                                                {{ $ruangan->nama }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Ubah Kegiatan <span class="text-red-500">*</span></label>
                                            <input type="text" name="override_tujuan" x-model="currentTujuan"
                                                class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                :required="selectedAction === 'edit'" placeholder="Misal: Rapat Internal">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Ubah
                                                Tanggal <span class="text-red-500">*</span></label>
                                            <input type="date" name="override_tanggal_pinjam" x-model="currentDate"
                                                class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                :required="selectedAction === 'edit'">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label
                                                    class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Jam
                                                    Mulai</label>
                                                <input type="time" name="override_jam_mulai" x-model="currentJamMulai"
                                                    class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                    :required="selectedAction === 'edit'">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Sampai</label>
                                                <input type="time" name="override_jam_selesai"
                                                    x-model="currentJamSelesai"
                                                    class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                    :required="selectedAction === 'edit'">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="conflictError" x-transition
                                    class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl flex items-start gap-2 text-red-700 text-xs font-semibold">
                                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <span x-text="conflictError"></span>
                                </div>
                            </div>

                            <div x-show="selectedAction === 'ditolak'" x-transition class="mt-4 mb-2">
                                <label
                                    class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Alasan
                                    Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="alasan_penolakan"
                                    class="w-full text-[13px] p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none transition-colors mb-2"
                                    rows="3" placeholder="Contoh: Jadwal bentrok dengan acara jurusan..."
                                    :required="selectedAction === 'ditolak'"></textarea>
                            </div>
                        </div> <!-- End of scrollable body -->

                        <!-- Fixed Footer -->
                        <div class="px-6 py-4 flex gap-3 z-10 w-full justify-end border-t border-slate-100 bg-white">
                            <button type="button" @click="closeModal()"
                                class="flex-1 cursor-pointer py-2.5 px-4 border border-slate-200 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all">Batalkan</button>
                            <button type="submit"
                                class="flex-1 cursor-pointer py-2.5 px-4 bg-[#0B266E] hover:bg-[#071946] text-white rounded-xl font-semibold text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                :disabled="!selectedAction || (selectedAction === 'edit' && (conflictError !== '' || isCheckingOut))">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('persetujuanManager', () => ({
                modalTindakan: false,
                selectedId: null,
                selectedName: '',
                selectedNim: '',
                selectedTelp: '',
                selectedStatus: '',
                selectedAction: '',

                selectedRuanganName: '',
                selectedKegiatan: '',
                selectedFormatTanggal: '',
                selectedJamMulai: '',
                selectedJamSelesai: '',
                selectedBerkasUrl: '',

                currentRoom: '',
                currentDate: '',
                currentJamMulai: '',
                currentJamSelesai: '',
                currentTujuan: '',

                conflictError: '',
                isCheckingOut: false,
                checkTimeout: null,

                init() {
                    this.$watch('currentRoom', () => this.triggerCheck());
                    this.$watch('currentDate', () => this.triggerCheck());
                    this.$watch('currentJamMulai', () => this.triggerCheck());
                    this.$watch('currentJamSelesai', () => this.triggerCheck());
                },

                triggerCheck() {
                    if (this.selectedAction !== 'edit') {
                        this.conflictError = '';
                        return;
                    }
                    if (this.checkTimeout) clearTimeout(this.checkTimeout);

                    this.isCheckingOut = true;
                    this.checkTimeout = setTimeout(() => {
                        this.executeCheck();
                    }, 500);
                },

                async executeCheck() {
                    if (!this.currentRoom || !this.currentDate || !this.currentJamMulai || !this.currentJamSelesai) {
                        this.isCheckingOut = false;
                        return;
                    }

                    try {
                        let url = `{{ route('eoffice.peminjaman.admin.persetujuan.check-collision') }}?ruangan_id=${this.currentRoom}&tanggal_pinjam=${this.currentDate}&jam_mulai=${this.currentJamMulai}&jam_selesai=${this.currentJamSelesai}&exclude_id=${this.selectedId}`;
                        let res = await fetch(url);
                        let data = await res.json();

                        if (data.conflict) {
                            this.conflictError = data.message;
                        } else {
                            this.conflictError = '';
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isCheckingOut = false;
                    }
                },

                openAction(id, name, nim, telp, status, ruanganId, tanggal, jamMulai, jamSelesai, ruanganName, kegiatan, formatTanggal, berkasUrl) {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.selectedNim = nim;
                    this.selectedTelp = telp;
                    this.selectedStatus = status;
                    this.selectedAction = '';

                    this.selectedRuanganName = ruanganName;
                    this.selectedKegiatan = kegiatan;
                    this.selectedFormatTanggal = formatTanggal;
                    this.selectedJamMulai = jamMulai;
                    this.selectedJamSelesai = jamSelesai;
                    this.selectedBerkasUrl = berkasUrl;

                    this.currentRoom = ruanganId;
                    this.currentDate = tanggal;
                    this.currentJamMulai = jamMulai;
                    this.currentJamSelesai = jamSelesai;
                    this.currentTujuan = kegiatan;
                    this.conflictError = '';

                    if (status === 'disetujui') {
                        this.selectedAction = 'ditolak';
                    } else {
                        this.selectedAction = '';
                    }

                    this.modalTindakan = true;
                },

                closeModal() {
                    this.modalTindakan = false;
                    this.selectedId = null;
                    if (this.checkTimeout) clearTimeout(this.checkTimeout);
                },

                getFormAction() {
                    if (!this.selectedId) return '#';

                    if (this.selectedAction === 'edit') {
                        let baseUrl = "{{ route('eoffice.peminjaman.admin.persetujuan.override', 'REPLACE_ID') }}";
                        return baseUrl.replace('REPLACE_ID', this.selectedId);
                    } else {
                        let baseUrl = "{{ route('eoffice.peminjaman.admin.persetujuan.update', 'REPLACE_ID') }}";
                        return baseUrl.replace('REPLACE_ID', this.selectedId);
                    }
                }
            }))
        })
    </script>
</x-eoffice::manajemen-ruangan.layout>
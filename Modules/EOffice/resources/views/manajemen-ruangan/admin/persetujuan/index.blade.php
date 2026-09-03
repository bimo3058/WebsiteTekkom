<x-eoffice::manajemen-ruangan.layout pageTitle="Persetujuan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Persetujuan</h1>
            <p class="mp-page-sub">Kelola dan verifikasi seluruh permohonan peminjaman ruangan yang diajukan oleh
                pengguna / mahasiswa.</p>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;" x-data="persetujuanManager()">
        <div class="mp-card-body">
            <div
                class="px-5 py-4 border-b border-gray-100 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white rounded-t-[12px]">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Antrean Pengajuan & Jadwal Berjalan</h2>

                <form action="{{ route('eoffice.peminjaman.admin.persetujuan.index') }}" method="GET"
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
                            class="w-full sm:w-64 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:bg-slate-50 focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] outline-none transition-all placeholder-gray-400"
                            placeholder="Cari Nama/NIM/Ruangan..." x-on:input.debounce.700ms="$el.form.submit()">
                    </div>

                    {{-- Filter Ruangan (No Title Label) --}}
                    <div
                        class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-xs shadow-sm">
                        <select name="ruangan_id"
                            class="px-3 py-1.5 text-[13px] text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border-none appearance-none pr-7 relative bg-no-repeat w-full max-w-[140px] sm:max-w-[180px] truncate"
                            style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' stroke=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/></svg>'); background-position: right 0.5rem center; background-size: 0.9rem;"
                            onchange="this.form.submit()">
                            <option value="">Semua Ruangan</option>
                            @foreach($ruangans as $ruangan)
                                <option value="{{ $ruangan->id }}" {{ request('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                    {{ $ruangan->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="mp-table-wrap">
                <table class="mp-table" style="table-layout: auto; width: 100%;">
                    <thead>
                        <tr>
                            <th>PENGAJU</th>
                            <th>RUANGAN & TUJUAN</th>
                            <th>WAKTU ACARA</th>
                            <th>STATUS</th>
                            <th style="width: 120px; text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamans as $pinjam)
                            <tr class="mp-tr">
                                <td>
                                    <div class="text-[13px] font-medium text-[#111827] flex items-center gap-2">
                                        {{ $pinjam->user->name ?? 'User Tidak Diketahui' }}
                                        @if($pinjam->created_by && $pinjam->created_by !== $pinjam->user_id)
                                            <span
                                                class="bg-slate-100 text-slate-500 border border-slate-200 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider"
                                                title="Didaftarkan oleh Sistem/Admin">By Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        @if(!empty($pinjam->user->external_id))({{ $pinjam->user->external_id }})
                                        @endif{{ $pinjam->nomor_telepon ?: '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-[13px] font-medium text-[#111827]">
                                        {{ $pinjam->ruangan->nama ?? 'Dihapus' }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                        title="{{ $pinjam->tujuan }}">{{ $pinjam->tujuan }}</div>
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
                                        $style = '';
                                        if ($pinjam->status === 'disetujui')
                                            $style = 'bg-emerald-100 text-emerald-800';
                                        elseif ($pinjam->status === 'ditolak')
                                            $style = 'bg-red-100 text-red-800';
                                        elseif ($pinjam->status === 'menunggu')
                                            $style = 'bg-amber-100 text-amber-800';
                                        elseif ($pinjam->status === 'selesai')
                                            $style = 'bg-purple-100 text-purple-800';
                                        else
                                            $style = 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="inline-flex items-center justify-center px-[12px] py-[4px] rounded-full {{ $style }} text-[12px] font-medium tracking-wide">
                                        {{ ucfirst($pinjam->status) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <button
                                        @click="openAction({{ $pinjam->id }}, '{{ addslashes($pinjam->user->name ?? "User") }}', '{{ $pinjam->status }}')"
                                        class="h-8 px-4 rounded-md bg-white border border-gray-200 text-[#0B266E] text-[12px] font-bold hover:bg-gray-50 shadow-sm transition-colors">
                                        Kelola
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="mp-tr">
                                <td colspan="5" class="py-12 text-center text-gray-500 text-[13px]">Belum ada antrean
                                    permohonan ruangan atau jadwal berjalan yang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center border border-slate-200 rounded-md bg-white overflow-hidden text-[13px] shadow-sm">
                        <span class="px-3 py-1.5 text-slate-600 font-medium border-r border-slate-200 bg-slate-50">Per
                            halaman</span>
                        <select aria-label="Per halaman" onchange="window.location.href=this.value"
                            class="px-2.5 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border-none appearance-none pr-7 relative bg-no-repeat"
                            style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' stroke=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/></svg>'); background-position: right 0.5rem center; background-size: 0.9rem;">
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
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
                            class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
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
                                    class="bg-[#354371] text-white w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>

                    @if ($peminjamans->hasMorePages())
                        <a href="{{ $peminjamans->nextPageUrl() }}"
                            class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
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

            {{-- Modal Tindakan --}}
            <div x-show="modalTindakan" style="display: none;"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div x-show="modalTindakan" x-transition.opacity
                    class="fixed inset-0 bg-gray-800/60 backdrop-blur-sm transition-opacity" @click="closeModal()">
                </div>

                <div x-show="modalTindakan" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="relative bg-white rounded-[16px] shadow-2xl w-full max-w-md overflow-hidden flex flex-col border border-gray-100">

                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                        <h3 class="font-bold text-gray-900 text-lg tracking-tight">Verifikasi Pengajuan</h3>
                        <button type="button" @click="closeModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-6">Tentukan status pengajuan ruangan dari <span
                                class="font-semibold text-blue-900 bg-blue-50 px-1.5 py-0.5 rounded"
                                x-text="selectedName"></span>.</p>

                        <form id="actionForm" method="POST" :action="getFormAction()">
                            @csrf
                            <input type="hidden" name="status" x-model="selectedAction">

                            <!-- Jika Status Menunggu -->
                            <div x-show="selectedStatus === 'menunggu'" class="grid grid-cols-2 gap-3 mb-5">
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="selectedAction" value="disetujui" class="peer sr-only">
                                    <div
                                        class="rounded-xl border-2 border-gray-100 px-4 py-3.5 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 hover:bg-gray-50 transition-all text-center text-gray-700 peer-checked:text-emerald-700 font-semibold text-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7">
                                            </path>
                                        </svg>
                                        Setujui
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="selectedAction" value="ditolak" class="peer sr-only">
                                    <div
                                        class="rounded-xl border-2 border-gray-100 px-4 py-3.5 peer-checked:border-red-500 peer-checked:bg-red-50/50 hover:bg-gray-50 transition-all text-center text-gray-700 peer-checked:text-red-700 font-semibold text-sm flex items-center justify-center gap-2">
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
                                    <p>Status permohonan ini sudah <strong>Disetujui</strong>. Pembatalkan sepihak akan
                                        langsung merebut hak ruangan dari pihak yang bersangkutan.</p>
                                </div>

                                <label class="cursor-pointer block">
                                    <input type="radio" x-model="selectedAction" value="ditolak" class="peer sr-only">
                                    <div
                                        class="rounded-xl border-2 border-gray-100 px-4 py-3.5 peer-checked:border-red-500 peer-checked:bg-red-50/50 hover:bg-gray-50 transition-all text-center text-gray-700 peer-checked:text-red-700 font-semibold text-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12">
                                            </path>
                                        </svg>
                                        Cabut Hak Penggunaan (Batal)
                                    </div>
                                </label>
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

                            <div class="mt-8 flex gap-3">
                                <button type="button" @click="closeModal()"
                                    class="flex-1 py-2.5 px-4 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition-colors">Tutup</button>
                                <button type="submit"
                                    class="flex-1 py-2.5 px-4 bg-[#0B266E] text-white rounded-xl font-medium text-sm hover:bg-[#071946] shadow-sm hover:shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="!selectedAction">Simpan Status</button>
                            </div>
                        </form>
                    </div>
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
                selectedStatus: '',
                selectedAction: '',

                openAction(id, name, status) {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.selectedStatus = status;
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
                },

                getFormAction() {
                    if (!this.selectedId) return '#';
                    let baseUrl = "{{ route('eoffice.peminjaman.admin.persetujuan.update', 'REPLACE_ID') }}";
                    return baseUrl.replace('REPLACE_ID', this.selectedId);
                }
            }))
        })
    </script>
</x-eoffice::manajemen-ruangan.layout>
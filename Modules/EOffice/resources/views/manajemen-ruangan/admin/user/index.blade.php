<x-eoffice::manajemen-ruangan.layout pageTitle="Buku Tamu & Pengawasan">
    {{-- Container Theme Override to match Global --}}
    <style>
        :root {
            --c-bg: #F8FAFC;
            --c-border: #E2E8F0;
            --c-border-strong: #CBD5E1;
            --c-fg: #0F172A;
            --c-fg-sec: #475569;
            --c-fg-muted: #64748B;
            --c-fg-placeholder: #94A3B8;
            --c-primary: #4F46E5;
        }
    </style>

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Buku Tamu & Pengawasan</h1>
            <p class="mp-page-sub">Daftar pengguna yang sudah pernah menggunakan fasilitas ruangan. Anda dapat
                menonaktifkan pengguna yang melakukan pelanggaran agar tidak dapat melakukan peminjaman ruangan di masa
                depan.</p>
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

    <div x-data="userManager()">
        {{-- Main Table Card matching Global User --}}
        <div
            style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-top: 15px;">

            {{-- Table Toolbar --}}
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
                <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">Riwayat
                    Pengunjung</h2>

                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0;">
                    <form method="GET" action="{{ route('eoffice.peminjaman.admin.user.index') }}"
                        style="display:flex; align-items:center; gap:8px; margin:0;">
                        {{-- Search --}}
                        <div style="position:relative; width:min(220px, calc(100vw - 200px)); min-width:120px;">
                            <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;"
                                width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama/NIM..."
                                style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:#fff;"
                                onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(79, 70, 229, 0.08)'"
                                onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; min-width:650px;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">
                                No</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:200px;">
                                User Name</th>
                            <th
                                style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                Total Pinjam</th>
                            <th
                                style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                Status</th>
                            <th
                                style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            @php
                                $isSuspended = isset($blacklists[$user->id]);
                                $rowNo = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
                            @endphp
                            <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s; {{ $isSuspended ? 'background:#FFF9F9;' : '' }}"
                                onmouseover="this.style.background='{{ $isSuspended ? '#FFF5F5' : '#FAFAFA' }}'"
                                onmouseout="this.style.background='{{ $isSuspended ? '#FFF9F9' : 'transparent' }}'">

                                {{-- No --}}
                                <td
                                    style="padding:14px 16px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:48px;">
                                    {{ $rowNo }}
                                </td>

                                {{-- User Name --}}
                                <td style="padding:14px 16px; min-width:200px;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <x-ui.user-avatar :user="$user" size="md" :suspended="$isSuspended" />
                                        <div style="min-width:0;">
                                            <div style="display:flex; align-items:center; gap:5px;">
                                                <p
                                                    style="font-size:13px; font-weight:600; color:{{ $isSuspended ? '#DC2626' : 'var(--c-fg)' }}; {{ $isSuspended ? 'text-decoration:line-through; text-decoration-color:#FCA5A5;' : '' }} white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">
                                                    {{ $user->name }}
                                                </p>
                                            </div>
                                            <p
                                                style="font-size:11px; color:var(--c-fg-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; margin-top:1px;">
                                                {{ $user->nomor_induk ?? $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Total Pinjam --}}
                                <td style="padding:14px 16px; text-align:center;">
                                    <div
                                        style="display:inline-flex; align-items:center; justify-content:center; min-width:28px; height:24px; padding:0 8px; border-radius:6px; background:rgba(79, 70, 229, 0.08); text-color:#4F46E5; font-size:12px; font-weight:700; border:1px solid rgba(79, 70, 229, 0.2); color: #4F46E5;">
                                        {{ $bookingCounts[$user->id] ?? 0 }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td style="padding:12px 16px; text-align:center;">
                                    @if($isSuspended)
                                        <div style="display:inline-flex; align-items:center; padding:2px 8px; border-radius:999px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; background:rgba(220, 38, 38, 0.1); color:#DC2626; border:1px solid rgba(220, 38, 38, 0.2);"
                                            title="{{ $blacklists[$user->id]->alasan }}">
                                            <span
                                                style="width:5px; height:5px; border-radius:50%; background:#DC2626; margin-right:5px;"></span>
                                            Suspend
                                        </div>
                                    @else
                                        <div
                                            style="display:inline-flex; align-items:center; padding:2px 8px; border-radius:999px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; background:rgba(16, 185, 129, 0.1); color:#10B981; border:1px solid rgba(16, 185, 129, 0.2);">
                                            <span
                                                style="width:5px; height:5px; border-radius:50%; background:#10B981; margin-right:5px;"></span>
                                            Active
                                        </div>
                                    @endif
                                </td>

                                {{-- Action (3 dots) --}}
                                <td style="padding:14px 16px; text-align:center;">
                                    <div style="position:relative; display:inline-block;" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" @click.outside="open = false"
                                            style="width:28px; height:28px; border-radius:6px; border:1px solid var(--c-border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg-muted); transition:all .15s; margin:0 auto;"
                                            onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
                                            onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
                                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="5" cy="12" r="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <circle cx="19" cy="12" r="2" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            style="position:absolute; right:100%; top:0; background:#fff; border:1px solid var(--c-border); border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,.08); min-width:160px; z-index:40; overflow:hidden; display:none; margin-right: 8px;">

                                            <div style="padding:5px;">
                                                <button type="button"
                                                    @click="openHistory({{ $user->id }}, '{{ addslashes($user->name) }}'); open = false"
                                                    style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:var(--c-fg-sec); cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                                    onmouseover="this.style.background='var(--c-bg)'"
                                                    onmouseout="this.style.background='none'">
                                                    <svg width="13" height="13" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Lihat Riwayat
                                                </button>

                                                @if($isSuspended)
                                                    <form method="POST"
                                                        action="{{ route('eoffice.peminjaman.admin.user.toggleBlacklist', $user->id) }}"
                                                        style="display:block;">
                                                        @csrf
                                                        <button type="submit"
                                                            style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#059669; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                                            onmouseover="this.style.background='#ECFDF5'"
                                                            onmouseout="this.style.background='none'">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Cabut Suspend
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button"
                                                        @click="openSuspend({{ $user->id }}, '{{ addslashes($user->name) }}'); open = false"
                                                        style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#DC2626; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                                        onmouseover="this.style.background='#FEF2F2'"
                                                        onmouseout="this.style.background='none'">
                                                        <svg width="13" height="13" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                                            <path
                                                                d="M15.5 15.5L12 12M8.5 8.5L12 12M8.5 15.5L12 12M15.5 8.5L12 12M12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12C22 17.52 17.52 22 12 22Z" />
                                                        </svg>
                                                        Suspend User
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:60px 24px; text-align:center;">
                                    <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                        <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M14.86 10.83C15.58 9.73 16 8.42 16 7C16 5.58 15.58 4.27 14.86 3.17C15.22 3.06 15.6 3 16 3C18.21 3 20 4.79 20 7C20 9.21 18.21 11 16 11C15.6 11 15.22 10.94 14.86 10.83ZM17.87 21C17.96 20.68 18 20.35 18 20V19C18 17.11 17.34 15.37 16.25 14H17C19.76 14 22 16.24 22 19V20C22 20.55 21.55 21 21 21H17.87Z"
                                                fill="currentColor" />
                                            <path
                                                d="M10 14H8C5.24 14 3 16.24 3 19V20C3 20.55 3.45 21 4 21H14C14.55 21 15 20.55 15 20V19C15 16.24 12.76 14 10 14Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                            <path
                                                d="M9 11C11.21 11 13 9.21 13 7C13 4.79 11.21 3 9 3C6.79 3 5 4.79 5 7C5 9.21 6.79 11 9 11Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                        <p
                                            style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em;">
                                            Tidak Ada Data</p>
                                        <p style="font-size:11px; color:var(--c-fg-placeholder);">Belum ada riwayat
                                            pengunjung.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination footer match global --}}
            @if($users->hasPages())
                <div style="padding:12px 16px; border-top:1px solid var(--c-border); background:#fff; margin:0;">
                    <div style="transform: scale(0.9); transform-origin: left center;">
                        {{ $users->links('pagination::tailwind') }}
                    </div>
                </div>
            @else
                <div
                    style="padding:12px 16px; border-top:1px solid var(--c-border); display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--c-fg-muted); background:#fff;">
                    Menampilkan total {{ $users->count() }} pengunjung
                </div>
            @endif
        </div>

        {{-- Modal Lihat Riwayat --}}
        <div x-show="modalHistory" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="modalHistory" x-transition.opacity
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModals()"></div>
            <div x-show="modalHistory" x-transition
                class="relative bg-white rounded-[16px] shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col border border-gray-100 max-h-[80vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg tracking-tight">Riwayat Peminjaman</h3>
                        <p class="text-[12px] text-gray-500 mt-0.5" x-text="selectedName"></p>
                    </div>
                    <button type="button" @click="closeModals()"
                        class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-100 hover:bg-gray-200 p-1.5 rounded-full">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-0 overflow-y-auto bg-gray-50/50 flex-1">
                    <div x-show="loadingHistory" class="p-12 flex flex-col items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mb-3" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-gray-500">Mencari rekam jejak...</span>
                    </div>

                    <table x-show="!loadingHistory && historyData.length > 0" class="w-full text-left border-collapse"
                        style="display: none;">
                        <thead>
                            <tr class="border-b border-gray-200 bg-white sticky top-0">
                                <th class="py-3 px-5 text-[12px] font-semibold text-gray-500 uppercase tracking-wider">
                                    Ruangan</th>
                                <th class="py-3 px-5 text-[12px] font-semibold text-gray-500 uppercase tracking-wider">
                                    Waktu</th>
                                <th class="py-3 px-5 text-[12px] font-semibold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-for="(hist, index) in historyData" :key="index">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-5 align-middle">
                                        <div class="text-[13px] font-medium text-gray-900" x-text="hist.ruangan"></div>
                                        <div class="text-[11px] text-gray-500 max-w-[200px] truncate"
                                            x-text="hist.tujuan" :title="hist.tujuan"></div>
                                    </td>
                                    <td class="py-3 px-5 align-middle">
                                        <div class="text-[12px] font-medium text-gray-800" x-text="hist.tanggal"></div>
                                        <div class="text-[11px] text-gray-500" x-text="hist.waktu + ' WIB'"></div>
                                    </td>
                                    <td class="py-3 px-5 align-middle">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest bg-gray-100 text-gray-700"
                                            :class="{ 'bg-emerald-100 text-emerald-700': hist.status === 'disetujui', 'bg-red-100 text-red-700': hist.status === 'ditolak', 'bg-amber-100 text-amber-700': hist.status === 'menunggu' }"
                                            x-text="hist.status"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Form Suspend --}}
        <div x-show="modalSuspend" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="modalSuspend" x-transition.opacity
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModals()"></div>
            <div x-show="modalSuspend" x-transition
                class="relative bg-white rounded-[16px] shadow-2xl w-full max-w-md overflow-hidden flex flex-col border border-gray-100">
                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Suspend Pengguna</h3>
                    <p class="text-sm text-gray-600 mb-5">Anda akan memblokir <span class="font-bold text-gray-900"
                            x-text="selectedName"></span> dari sistem. Masukkan alasan pemblokiran log pelanggaran.</p>

                    <form id="suspendForm" method="POST" :action="getSuspendUrl()">
                        @csrf
                        <div class="mb-5">
                            <label
                                class="block text-[12px] font-bold text-gray-700 uppercase tracking-wider mb-2">Alasan
                                Suspend <span class="text-red-500">*</span></label>
                            <textarea name="alasan" required
                                class="w-full text-[13px] p-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm"
                                rows="3"></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="closeModals()"
                                class="flex-1 py-2.5 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                            <button type="submit"
                                class="flex-1 py-2.5 px-4 bg-red-600 text-white rounded-xl font-semibold text-sm hover:bg-red-700 transition-all shadow-sm">Blokir
                                Akses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userManager', () => ({
                modalHistory: false,
                modalSuspend: false,
                selectedId: null,
                selectedName: '',
                historyData: [],
                loadingHistory: false,

                openHistory(id, name) {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.modalHistory = true;
                    this.loadingHistory = true;
                    this.historyData = [];
                    fetch(`/eoffice/peminjaman/admin/user/${id}/history`)
                        .then(res => res.json())
                        .then(res => {
                            this.historyData = res.data;
                            this.loadingHistory = false;
                        });
                },
                openSuspend(id, name) {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.modalSuspend = true;
                },
                closeModals() {
                    this.modalHistory = false;
                    this.modalSuspend = false;
                    this.selectedId = null;
                },
                getSuspendUrl() {
                    if (!this.selectedId) return '#';
                    return "{{ route('eoffice.peminjaman.admin.user.toggleBlacklist', 'REPLACE_ID') }}".replace('REPLACE_ID', this.selectedId);
                }
            }))
        })
    </script>
</x-eoffice::manajemen-ruangan.layout>
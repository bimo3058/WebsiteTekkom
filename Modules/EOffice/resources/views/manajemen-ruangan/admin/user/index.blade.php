<x-eoffice::manajemen-ruangan.layout pageTitle="Buku Tamu & Pengawasan">

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

    <div class="bg-white border border-gray-200 rounded-[12px] mt-6 shadow-sm" x-data="userManager()">
        <div
            class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Pengunjung (Visitor Log)</h2>

            <form method="GET" class="flex flex-wrap items-center gap-2.5">
                {{-- Search --}}
                <div class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full sm:w-64 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400"
                        placeholder="Cari nama atau nomor induk..." x-on:input.debounce.700ms="$el.form.submit()">
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-[#F9FAFB]/50">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#F9FAFB]/80">
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Nama Pengguna</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280] text-center">Total Riwayat Pinjam
                        </th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Status Akses</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280] text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-4 px-5 align-middle">
                                <div class="text-[13px] font-bold text-[#111827]">{{ $user->name }}</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">{{ $user->nomor_induk ?? $user->email }}</div>
                            </td>
                            <td class="py-4 px-5 align-middle text-center">
                                <div
                                    class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-md bg-indigo-50 text-indigo-700 text-[12px] font-bold border border-indigo-100">
                                    {{ $bookingCounts[$user->id] ?? 0 }}
                                </div>
                            </td>
                            <td class="py-4 px-5 align-middle">
                                @if(isset($blacklists[$user->id]))
                                    <div class="inline-flex flex-col gap-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-red-500 text-red-600 bg-red-50/30 text-[12px] font-medium max-w-max">
                                            Diblokir
                                        </span>
                                        <span class="text-[10px] text-red-500 max-w-[200px] truncate"
                                            title="{{ $blacklists[$user->id]->alasan }}">Alasan:
                                            {{ $blacklists[$user->id]->alasan }}</span>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full border border-emerald-500 text-emerald-600 bg-emerald-50/30 text-[12px] font-medium">
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 align-middle text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openHistory({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        type="button"
                                        class="h-8 px-3 rounded-md bg-white border border-gray-200 text-[12px] font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition-all">
                                        Lihat Riwayat
                                    </button>
                                    @if(isset($blacklists[$user->id]))
                                        <form action="{{ route('eoffice.peminjaman.admin.user.toggleBlacklist', $user->id) }}"
                                            method="POST" class="m-0 inline-block">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin memulihkan hak akses peminjaman ruangan pengguna ini?')"
                                                class="h-8 px-3 rounded-md bg-emerald-50 border border-emerald-200 text-[12px] font-medium text-emerald-700 hover:bg-emerald-100 shadow-sm transition-all">
                                                Cabut Blokir
                                            </button>
                                        </form>
                                    @else
                                        <button @click="openSuspend({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            type="button"
                                            class="h-8 px-3 rounded-md bg-red-50 border border-red-200 text-[12px] font-medium text-red-600 hover:bg-red-100 shadow-sm transition-all">
                                            Suspend
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500 text-[13px]">Belum ada data pengunjung
                                ruangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-[13px] text-gray-600">
                    <span>Per page</span>
                    <select class="border border-gray-200 rounded px-1.5 py-0.5 outline-none font-medium text-gray-800">
                        <option>15</option>
                    </select>
                    <span class="ml-2">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of
                        {{ $users->total() }} results</span>
                </div>

                <div class="flex items-center">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
        @else
            <div class="flex items-center gap-2 text-[13px] text-gray-600 px-5 py-3 border-t border-gray-100">
                <span>Showing {{ $users->count() }} results</span>
            </div>
        @endif

        {{-- Modal Lihat Riwayat --}}
        <div x-show="modalHistory" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="modalHistory" x-transition.opacity
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModals()"></div>

            <div x-show="modalHistory" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="relative bg-white rounded-[16px] shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col border border-gray-100 max-h-[80vh]">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg tracking-tight">Riwayat Peminjaman</h3>
                        <p class="text-[12px] text-gray-500 mt-0.5" x-text="selectedName"></p>
                    </div>
                    <button type="button" @click="closeModals()"
                        class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-100 hover:bg-gray-200 p-1.5 rounded-full">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-0 overflow-y-auto bg-gray-50/50 flex-1">
                    <div x-show="loadingHistory" class="p-12 flex flex-col items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mb-3" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-gray-500">Mencari rekam jejak...</span>
                    </div>

                    <div x-show="!loadingHistory && historyData.length === 0"
                        class="p-12 text-center text-sm font-medium text-gray-500" style="display: none;">
                        Pengguna ini belum memiliki riwayat peminjaman.
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
                                            :class="{
                                                'bg-emerald-100 text-emerald-700': hist.status === 'disetujui',
                                                'bg-red-100 text-red-700': hist.status === 'ditolak',
                                                'bg-amber-100 text-amber-700': hist.status === 'menunggu'
                                            }" x-text="hist.status"></span>
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

            <div x-show="modalSuspend" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="relative bg-white rounded-[16px] shadow-2xl w-full max-w-md overflow-hidden flex flex-col border border-gray-100">
                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">Suspend Pengguna</h3>
                    <p class="text-sm text-gray-600 mb-5">Anda akan memblokir <span class="font-bold text-gray-900"
                            x-text="selectedName"></span> dari sistem peminjaman ruangan. Silakan masukkan alasan
                        pemblokiran log pelanggaran.</p>

                    <form id="suspendForm" method="POST" :action="getSuspendUrl()">
                        @csrf
                        <div class="mb-5">
                            <label
                                class="block text-[12px] font-bold text-gray-700 uppercase tracking-wider mb-2">Alasan
                                Suspend <span class="text-red-500">*</span></label>
                            <textarea name="alasan" required
                                class="w-full text-[13px] p-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all shadow-sm"
                                rows="3"
                                placeholder="Contoh: Meninggalkan sampah di Aula secara disengaja..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="closeModals()"
                                class="flex-1 py-2.5 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                            <button type="submit"
                                class="flex-1 py-2.5 px-4 bg-red-600 text-white rounded-xl font-semibold text-sm hover:bg-red-700 focus:ring-4 focus:ring-red-100 transition-all shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                    </path>
                                </svg>
                                Blokir Akses
                            </button>
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
                    let url = "{{ route('eoffice.peminjaman.admin.user.toggleBlacklist', 'REPLACE_ID') }}";
                    return url.replace('REPLACE_ID', this.selectedId);
                }
            }))
        })
    </script>
</x-eoffice::manajemen-ruangan.layout>
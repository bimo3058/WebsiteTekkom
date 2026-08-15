<x-eoffice::manajemen-ruangan.layout pageTitle="Persetujuan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Persetujuan</h1>
            <p class="mp-page-sub">Kelola dan verifikasi seluruh permohonan peminjaman ruangan yang diajukan oleh
                pengguna / mahasiswa.</p>
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
        x-data="persetujuanManager()">
        <div
            class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Antrean Pengajuan</h2>
        </div>

        <div class="overflow-x-auto bg-[#F9FAFB]/50">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-[#F9FAFB]/80">
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Pengaju</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Ruangan & Tujuan</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Waktu Acara</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280]">Status</th>
                        <th class="py-3.5 px-5 text-[13px] font-medium text-[#6B7280] text-center w-[120px]">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($peminjamans as $pinjam)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-4 px-5 align-middle">
                                <div class="text-[13px] font-medium text-[#111827]">
                                    {{ $pinjam->user->name ?? 'User Tidak Diketahui' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">{{ $pinjam->nomor_telepon }}</div>
                            </td>
                            <td class="py-4 px-5 align-middle">
                                <div class="text-[13px] text-[#111827]">{{ $pinjam->ruangan->nama ?? 'Dihapus' }}</div>
                                <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                    title="{{ $pinjam->tujuan }}">{{ $pinjam->tujuan }}</div>
                                @if($pinjam->berkas_pendukung)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($pinjam->berkas_pendukung) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-indigo-600 hover:text-indigo-800 mt-1">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                        Lihat Berkas
                                    </a>
                                @endif
                            </td>
                            <td class="py-4 px-5 align-middle">
                                <div class="text-[13px] text-[#111827]">
                                    {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d F Y') }} <span
                                        class="text-gray-400 mx-1">•</span>
                                    {{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }} WIB
                                </div>
                            </td>
                            <td class="py-4 px-5 align-middle">
                                @php
                                    $style = '';
                                    if ($pinjam->status === 'disetujui')
                                        $style = 'border-emerald-500 text-emerald-600 bg-emerald-50/30';
                                    elseif ($pinjam->status === 'ditolak')
                                        $style = 'border-red-500 text-red-600 bg-red-50/30';
                                    elseif ($pinjam->status === 'menunggu')
                                        $style = 'border-amber-500 text-amber-600 bg-amber-50/30';
                                    else
                                        $style = 'border-gray-400 text-gray-700 bg-gray-50';
                                @endphp
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border {{ $style }} text-[12px] font-medium">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ str_replace(['border-', ' text-', ' bg-', '/30', '500', '600', '700', '400'], ['bg-', '', '', '', '500', '500', '500', '500'], current(explode(' ', $style))) }}"></span>
                                    {{ ucfirst($pinjam->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-5 align-middle text-center">
                                @if($pinjam->status === 'menunggu')
                                    <button @click="openAction({{ $pinjam->id }}, '{{ $pinjam->user->name }}')"
                                        class="h-8 px-3 rounded-md bg-white border border-gray-200 text-[12px] font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-200 shadow-sm transition-all focus:ring-2 focus:ring-offset-1 focus:ring-indigo-100">
                                        Periksa
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500 text-[13px]">Belum ada antrean permohonan
                                ruangan yang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($peminjamans, 'hasPages') && $peminjamans->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-[13px] text-gray-600">
                    <span>Per page</span>
                    <select class="border border-gray-200 rounded px-1.5 py-0.5 outline-none font-medium text-gray-800">
                        <option>15</option>
                    </select>
                    <span class="ml-2">Showing {{ $peminjamans->firstItem() }} to {{ $peminjamans->lastItem() }} of
                        {{ $peminjamans->total() }} results</span>
                </div>

                <div class="flex items-center">
                    {{ $peminjamans->links('pagination::tailwind') }}
                </div>
            </div>
        @else
            <div class="flex items-center gap-2 text-[13px] text-gray-600 px-5 py-3 border-t border-gray-100">
                <span>Showing {{ $peminjamans->count() }} results</span>
            </div>
        @endif

        {{-- Modal Tindakan --}}
        <div x-show="modalTindakan" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="modalTindakan" x-transition.opacity
                class="fixed inset-0 bg-gray-800/60 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            <div x-show="modalTindakan" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
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
                    <p class="text-sm text-gray-600 mb-6">Tentukan status akhir untuk permohonan dari <span
                            class="font-semibold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded"
                            x-text="selectedName"></span>.</p>

                    <form id="actionForm" method="POST" :action="getFormAction()">
                        @csrf
                        <input type="hidden" name="status" x-model="selectedAction">

                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <label class="cursor-pointer">
                                <input type="radio" x-model="selectedAction" value="disetujui" class="peer sr-only">
                                <div
                                    class="rounded-xl border-2 border-gray-100 px-4 py-3.5 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 hover:bg-gray-50 transition-all text-center text-gray-700 peer-checked:text-emerald-700 font-semibold text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12">
                                        </path>
                                    </svg>
                                    Tolak
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
                                class="flex-1 py-2.5 px-4 bg-indigo-600 text-white rounded-xl font-medium text-sm hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!selectedAction">Simpan Status</button>
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
                selectedAction: '', // 'disetujui' or 'ditolak'

                openAction(id, name) {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.selectedAction = '';
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
<x-eoffice::manajemen-ruangan.layout pageTitle="Persetujuan Peminjaman">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Persetujuan Peminjaman</h1>
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

    <div class="mp-card mt-6" x-data="persetujuanManager()">
        <div class="mp-card-header flex justify-between items-center">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <svg width="18" height="18" fill="none" stroke="#6B7280" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                    </path>
                </svg>
                Antrean & Riwayat Pengajuan
            </h3>
        </div>

        <div class="mp-card-body">
            <div class="overflow-x-auto">
                <table class="mp-table w-full">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengaju</th>
                            <th>Ruangan</th>
                            <th>Jadwal Peminjaman</th>
                            <th>Tujuan / Berkas</th>
                            <th>Status Terbaru</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamans as $index => $pinjam)
                            <tr class="mp-tr">
                                <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                <td>
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ $pinjam->user->name ?? 'User Tidak Diketahui' }}
                                    </div>
                                    <div class="text-[11px] text-gray-500">{{ $pinjam->nomor_telepon }}</div>
                                </td>
                                <td>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-indigo-50 border border-indigo-100 text-indigo-700 text-[11px] font-bold tracking-wide uppercase">
                                        {{ $pinjam->ruangan->nama ?? 'Dihapus' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-xs font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td>
                                    <div class="text-[11px] text-gray-600 max-w-[150px] truncate"
                                        title="{{ $pinjam->tujuan }}">{{ $pinjam->tujuan }}</div>
                                    <a href="{{ asset('storage/' . $pinjam->berkas_pendukung) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 mt-1 bg-white border border-gray-200 rounded text-[10px] font-bold text-gray-700 uppercase tracking-wide hover:bg-gray-50 hover:text-indigo-600 transition-colors shadow-sm">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                        Sekilas Berkas
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $badges = [
                                            'menunggu' => 'bg-amber-100/80 text-amber-700 border border-amber-200',
                                            'disetujui' => 'bg-emerald-100/80 text-emerald-700 border border-emerald-200',
                                            'ditolak' => 'bg-red-100/80 text-red-700 border border-red-200',
                                            'dibatalkan' => 'bg-gray-100 text-gray-600 border border-gray-200',
                                        ];
                                        $class = $badges[$pinjam->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest {{ $class }}">
                                        {{ $pinjam->status }}
                                    </span>
                                </td>
                                <td class="text-center relative">
                                    {{-- Hanya Menunggu yang bisa di Approve/Reject --}}
                                    @if($pinjam->status === 'menunggu')
                                        <button @click="openAction({{ $pinjam->id }}, '{{ $pinjam->user->name }}')"
                                            class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-semibold px-3.5 py-1.5 rounded-lg transition-colors shadow-sm hover:shadow focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500">Beri
                                            Tindakan</button>
                                    @else
                                        <span
                                            class="inline-flex text-[11px] font-medium text-gray-500 bg-gray-50 border border-gray-100 px-3 py-1 rounded-md">Diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-500">Belum ada pengajuan peminjaman
                                            ruangan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Tindakan --}}
        <div x-show="modalTindakan" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="modalTindakan" x-transition.opacity
                class="fixed inset-0 bg-gray-800 bg-opacity-70 transition-opacity" @click="closeModal()"></div>

            <div x-show="modalTindakan" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="relative bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">

                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Verifikasi Pengajuan</h3>
                    <button type="button" @click="closeModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">Silakan pilih tindakan untuk pengajuan dari pengusul <span
                            class="font-bold text-gray-900" x-text="selectedName"></span>.</p>

                    <form id="actionForm" method="POST" :action="getFormAction()">
                        @csrf
                        <input type="hidden" name="status" x-model="selectedAction">

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <label class="cursor-pointer">
                                <input type="radio" x-model="selectedAction" value="disetujui" class="peer sr-only">
                                <div
                                    class="rounded-lg border-2 border-gray-200 px-4 py-3 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-gray-50 transition-colors text-center text-emerald-700 font-bold text-sm h-full flex items-center justify-center">
                                    Setujui
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="selectedAction" value="ditolak" class="peer sr-only">
                                <div
                                    class="rounded-lg border-2 border-gray-200 px-4 py-3 peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition-colors text-center text-red-700 font-bold text-sm h-full flex items-center justify-center">
                                    Tolak
                                </div>
                            </label>
                        </div>

                        <div x-show="selectedAction === 'ditolak'" x-transition class="mt-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Alasan
                                Penolakan <span class="text-red-500">*</span></label>
                            <textarea name="alasan_penolakan" class="mp-input w-full" rows="3"
                                placeholder="Sebutkan alasan penolakan... (Wajib)"
                                :required="selectedAction === 'ditolak'"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="closeModal()"
                                class="mp-btn secondary shadow-sm font-semibold">Tutup</button>
                            <button type="submit" class="mp-btn primary px-5 shadow-sm font-semibold"
                                :disabled="!selectedAction">Simpan Tindakan</button>
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
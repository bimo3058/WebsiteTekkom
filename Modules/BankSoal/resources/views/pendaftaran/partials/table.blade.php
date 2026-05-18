{{-- ============================================================ --}}
{{-- Wrapper Alpine.js untuk state checkbox bulk selection --}}
{{-- ============================================================ --}}
<div x-data="{
    selected: [],
    get pendingIds() {
        return Array.from(document.querySelectorAll('.row-checkbox')).map(el => parseInt(el.value));
    },
    get allChecked() {
        const ids = this.pendingIds;
        return ids.length > 0 && ids.every(id => this.selected.includes(id));
    },
    get indeterminate() {
        const ids = this.pendingIds;
        return this.selected.length > 0 && !ids.every(id => this.selected.includes(id));
    },
    toggleAll() {
        const ids = this.pendingIds;
        this.selected = this.allChecked ? [] : [...ids];
    },
    get countSelected() { return this.selected.length; },

    confirmModal: false,
    confirmAction: '',
    confirmMethod: 'POST',
    confirmTitle: '',
    confirmText: '',
    confirmBtnColor: '',
    confirmIconColor: '',
    confirmIconBg: '',
    confirmIsBulk: false,
    confirmData: [],
    confirmStatus: null,

    openConfirm(action, method, title, text, btnColor, iconColor, iconBg, isBulk = false, data = [], status = null) {
        this.confirmAction = action;
        this.confirmMethod = method;
        this.confirmTitle = title;
        this.confirmText = text;
        this.confirmBtnColor = btnColor;
        this.confirmIconColor = iconColor;
        this.confirmIconBg = iconBg;
        this.confirmIsBulk = isBulk;
        this.confirmData = data;
        this.confirmStatus = status;
        this.confirmModal = true;
    },
    closeConfirm() {
        this.confirmModal = false;
    }
}">

    {{-- ── Bulk Action Bar ─────────────────────────────────────────── --}}
    <div x-show="selected.length > 0" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class=" flex items-center justify-between gap-3 px-4 py-3 bg-[#2A3A7C]/5 border border-[#2A3A7C]/10 border-x-0 border-t-0 rounded-none rounded-lg">
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-5 rounded-full bg-[#2A3A7C]"></div>
            <span class="text-sm font-semibold text-[#2A3A7C]">
                <span x-text="countSelected"></span>
                <span x-text="countSelected === 1 ? 'peserta' : 'peserta'"></span>
                dipilih
            </span>
        </div>
        <div class="flex items-center gap-2">
            {{-- Tolak --}}
            <button type="button" @click="openConfirm('{{ route('banksoal.pendaftaran.bulkReject') }}', 'POST', 'Tolak ' + countSelected + ' Peserta?', 'Data akan dihapus permanen dan tidak dapat dikembalikan.', 'bg-red-500 hover:bg-rose-600', 'text-red-500', 'bg-red-50', true, selected)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-300 text-red-700 hover:bg-red-50 active:scale-95 text-xs font-medium rounded-md transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                Tolak yang Dipilih
            </button>

            {{-- Setujui --}}
            <button type="button" @click="openConfirm('{{ route('banksoal.pendaftaran.bulkApprove') }}', 'POST', 'Setujui ' + countSelected + ' Peserta?', 'Aksi ini tidak dapat dibatalkan.', 'bg-emerald-600 hover:bg-emerald-700', 'text-emerald-600', 'bg-emerald-50', true, selected)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-medium rounded-md transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                Setujui yang Dipilih
            </button>
        </div>
    </div>

    {{-- ── Data Table Card ─────────────────────────────────────────── --}}
    <div
        class="bg-white overflow-hidden relative">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-[14px] text-gray-700 border-collapse">
                <thead
                    class="bg-[#F9FAFB] border-b border-gray-200 text-[13px] font-medium text-gray-500 capitalize">
                    <tr>
                        {{-- Checkbox Select All --}}
                        <th scope="col" class="px-6 py-4 w-10 text-center">
                            <input type="checkbox"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 cursor-pointer focus:ring-2 focus:ring-blue-500/20 focus:ring-offset-0"
                                :checked="allChecked" :indeterminate="indeterminate" @change="toggleAll()"
                                title="Pilih semua pending">
                        </th>
                        <th scope="col" class="px-6 py-4 w-12 text-center whitespace-nowrap">No</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">NIM</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Nama Mahasiswa</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Semester</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Riwayat Ujian</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftars as $index => $item)
                        @php $isPending = $item->status_pendaftaran->value !== 'approved'; @endphp
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-200 last:border-b-0"
                            :class="selected.includes({{ $item->id }}) ? 'bg-[#2A3A7C]/5' : ''">

                            {{-- Checkbox per baris --}}
                            <td class="px-6 py-4 text-center">
                                @if ($isPending)
                                    <input type="checkbox"
                                        class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 cursor-pointer focus:ring-2 focus:ring-blue-500/20 focus:ring-offset-0"
                                        value="{{ $item->id }}" x-model="selected">
                                @else
                                    <span class="text-gray-300" title="Sudah disetujui">
                                        <svg class="w-3.5 h-3.5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center text-gray-400 text-xs whitespace-nowrap">
                                {{ $pendaftars->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $item->nim }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $item->nama_lengkap }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">Semester {{ $item->semester_aktif }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                                @php $n = $item->sesi_selesai_count ?? 0; @endphp
                                {{ $n === 0 ? 'Belum pernah' : $n . '× ujian' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->status_pendaftaran->value === 'approved')
                                    <span
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-medium border border-green-600 bg-white text-green-600 shadow-[0_1px_2px_rgba(0,0,0,0.05)] uppercase tracking-wider">
                                        DISETUJUI
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-medium border border-gray-400 bg-white text-gray-600 shadow-[0_1px_2px_rgba(0,0,0,0.05)] uppercase tracking-wider">
                                        PENDING
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Detail — selalu tampil --}}
                                    <button type="button" title="Lihat Detail" onclick="openDetailModal({
                                                            nim: '{{ $item->nim }}',
                                                            nama: '{{ addslashes($item->nama_lengkap) }}',
                                                            semester: '{{ $item->semester_aktif }}',
                                                            target_wisuda: '{{ addslashes($item->target_wisuda ?? '-') }}',
                                                            status: '{{ $item->status_pendaftaran }}',
                                                            tanggal: '{{ $item->created_at->translatedFormat('d F Y, H:i') }}',
                                                            dosen1: '{{ addslashes($item->dosenPembimbing1->name ?? '-') }}',
                                                            dosen2: '{{ addslashes($item->dosenPembimbing2->name ?? '-') }}',
                                                            ujian_count: {{ $item->sesi_selesai_count ?? 0 }}
                                                        })"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-transparent text-gray-400 hover:text-[#2A3A7C] hover:bg-[#2A3A7C]/5 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>

                                    @if ($isPending)
                                        {{-- Approve satu --}}
                                        <button type="button" title="Setujui"
                                            @click="openConfirm('{{ route('banksoal.pendaftaran.updateStatus', $item->id) }}', 'PATCH', 'Setujui Pendaftaran?', 'Setujui pendaftaran {{ addslashes($item->nama_lengkap) }}? Aksi ini tidak dapat dibatalkan.', 'bg-emerald-600 hover:bg-emerald-700', 'text-emerald-500', 'bg-emerald-50', false, [], 'approved')"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-transparent text-gray-400 hover:text-green-600 hover:bg-green-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>

                                        {{-- Tolak & Hapus --}}
                                        <button type="button" title="Tolak & Hapus"
                                            @click="openConfirm('{{ route('banksoal.pendaftaran.destroy', $item->id) }}', 'DELETE', 'Tolak & Hapus?', 'Tolak dan hapus pendaftar {{ addslashes($item->nama_lengkap) }}? Data akan dihapus dari daftar.', 'bg-red-500 hover:bg-rose-600', 'text-red-500', 'bg-red-50')"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-transparent text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center border-b border-transparent bg-white">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-14 h-14 bg-gray-50 flex items-center justify-center rounded-2xl mb-4 border border-gray-100 shadow-sm">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-medium text-gray-900 tracking-tight">Belum Ada Pendaftar</h3>
                                    <p class="text-[13px] text-gray-500 mt-1 max-w-sm mx-auto leading-relaxed">
                                        @if (request('periode_id'))
                                            Tidak ada data pendaftar yang cocok dengan filter yang dipilih.
                                        @else
                                            Pilih periode ujian di atas untuk menampilkan data pendaftar.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer (Pagination & Per Page) --}}
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full">
                <div class="flex items-center gap-2">
                    <span class="text-[13px] text-gray-700 font-medium whitespace-nowrap">Per page</span>
                    <div class="relative">
                        <select onchange="document.getElementById('hidden-per-page').value = this.value; document.getElementById('filter-form').submit();" {{ !request('periode_id') ? 'disabled' : '' }} class="pl-3 pr-8 py-1.5 bg-white border border-gray-300 rounded-lg text-[13px] text-gray-700 font-medium focus:ring-2 focus:ring-[#2A3A7C]/20 focus:border-[#2A3A7C] transition-all cursor-pointer outline-none disabled:bg-gray-50 disabled:cursor-not-allowed">
                            <option value="5"  {{ request('per_page', 5) == 5  ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page', 5) == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 5) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page', 5) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 5) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    @if ($pendaftars instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <span class="text-[13px] text-gray-600 font-medium ml-2">
                        Showing {{ $pendaftars->firstItem() ?? 0 }} to {{ $pendaftars->lastItem() ?? 0 }} of {{ $pendaftars->total() }} results
                    </span>
                    @endif
                </div>
                
                @if ($pendaftars instanceof \Illuminate\Pagination\LengthAwarePaginator && $pendaftars->hasPages())
                <div class="sm:ml-auto">
                    {{ $pendaftars->links('pagination::tailwind') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Popup: Konfirmasi -->
    <div x-show="confirmModal" tabindex="-1" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;" x-cloak>
        <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="closeConfirm()"></div>

        <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">
            
            <div class="px-6 pt-6 pb-4 text-center">
                <div :class="'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 ' + confirmIconBg">
                    <svg :class="'w-7 h-7 ' + confirmIconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-[16px] font-semibold text-gray-900 tracking-tight mb-2" x-text="confirmTitle"></h3>
                <p class="text-[13px] text-gray-500 leading-relaxed" x-text="confirmText"></p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex items-center gap-3">
                <button type="button" @click="closeConfirm()" class="flex-1 px-4 py-2 text-[13px] font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">Batal</button>
                <form :action="confirmAction" method="POST" class="flex-1 m-0">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                    
                    <!-- Untuk Single Status Update -->
                    <template x-if="confirmStatus && !confirmIsBulk">
                        <input type="hidden" name="status_pendaftaran" :value="confirmStatus">
                    </template>
                    
                    <!-- Untuk Bulk Actions -->
                    <template x-if="confirmIsBulk && confirmData.length > 0">
                        <template x-for="id in confirmData" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                    </template>
                    
                    <button type="submit" :class="'w-full px-4 py-2 text-[13px] font-medium text-white rounded-lg transition-all ' + confirmBtnColor">
                        Ya, Lanjutkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
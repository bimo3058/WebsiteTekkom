<x-eoffice::layouts.koordinator>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1
                style="font-family:'Inter Tight',sans-serif; font-size:24px; font-weight:700; color:#0D0D12; letter-spacing:-0.01em;">
                Data Pendaftar KP
            </h1>
            <p class="mt-1 flex items-center justify-between"
                style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; color:#666D80;">
                Manajemen data mahasiswa yang mendaftar dan aktif di Kerja Praktik
            </p>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-8 right-8 z-50 p-4 rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] shadow-lg flex items-center gap-3">
            <svg width="20" height="20" fill="none" stroke="#15803D" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            <span class="text-[#15803D] font-medium" style="font-family:'Inter Tight',sans-serif; font-size:14px;">
                {{ session('success') }}
            </span>
        </div>
    @endif

    {{-- Table Container --}}
    <div style="
        background:#ffffff;
        border-radius:16px;
        border:1px solid #F1F1F3;
        box-shadow:0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04);
        padding:24px;
    ">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h2 style="font-family:'Inter Tight',sans-serif; font-size:16px; font-weight:600; color:#0D0D12;">
                Daftar Mahasiswa
            </h2>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-[280px]">
                    <input type="text" placeholder="Search" style="
                        padding:8px 16px 8px 36px;
                        border:1px solid #E2E8F0;
                        border-radius:8px;
                        font-family:'Inter Tight',sans-serif;
                        font-size:13px;
                        font-weight:500;
                        color:#666D80;
                        outline:none;
                        width:100%;
                    ">
                    <svg class="absolute left-3 top-2.5 text-[#A0AABF]" width="16" height="16" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <button style="
                    display:flex; align-items:center; gap:6px;
                    padding:8px 12px;
                    border:1px solid #E2E8F0;
                    border-radius:8px;
                    background:#ffffff;
                    font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#4B5563;
                    transition:background 0.2s;
                    white-space:nowrap;
                " class="hover:bg-gray-50">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filter
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="border-bottom:1px solid #F1F1F3; background-color:#FAFAFC;">
                        <th
                            style="padding:12px 16px; font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#666D80; text-transform:uppercase; letter-spacing:0.02em;">
                            Mahasiswa
                        </th>
                        <th
                            style="padding:12px 16px; font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#666D80; text-transform:uppercase; letter-spacing:0.02em;">
                            Pembimbing
                        </th>
                        <th
                            style="padding:12px 16px; font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#666D80; text-transform:uppercase; letter-spacing:0.02em;">
                            Status
                        </th>
                        <th
                            style="padding:12px 16px; font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#666D80; text-transform:uppercase; letter-spacing:0.02em;">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $kp)
                        <tr style="border-bottom:1px solid #F1F1F3;" class="hover:bg-[#F8F5FF] transition-colors">
                            <td style="padding:16px;">
                                <div class="flex flex-col">
                                    <span
                                        style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600; color:#0D0D12;">
                                        {{ optional(optional($kp->mahasiswa)->user)->name ?? 'Nama Tidak Ditemukan' }}
                                    </span>
                                    <span
                                        style="font-family:'Inter Tight',sans-serif; font-size:12px; color:#666D80; margin-top:4px;">
                                        NIM: {{ $kp->nim ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td style="padding:16px; font-family:'Inter Tight',sans-serif; font-size:14px; color:#4B5563;">
                                @if($kp->dosenPembimbing)
                                    <div class="flex flex-col">
                                        <span style="font-weight:500; color:#0D0D12;">
                                            {{ optional($kp->dosenPembimbing->user)->name ?? '-' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td style="padding:16px;">
                                <span style="
                                        display:inline-flex; align-items:center; gap:6px;
                                        padding:4px 10px;
                                        background:#EFF6FF;
                                        border:1px solid #BFDBFE;
                                        border-radius:9999px;
                                        font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#1D4ED8;
                                    ">
                                    <span style="width:6px; height:6px; background:#1D4ED8; border-radius:50%;"></span>
                                    {{ $kp->status_kp ?? 'Terdaftar' }}
                                </span>
                            </td>
                            <td style="padding:16px;">
                                <form action="{{ route('eoffice.kp.koordinator.pendaftar.destroy', $kp->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin membatalkan/mereset pendaftaran mahasiswa ini? Data KP terkait akan dihapus secara permanen.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="
                                            display:flex; align-items:center; gap:6px;
                                            padding:6px 12px; border-radius:6px;
                                            background:#FEF2F2; color:#DC2626; border:1px solid #FECACA;
                                            font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600;
                                            transition:all 0.2s;" class="hover:bg-red-100 hover:border-red-300">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Reset Pendaftar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-500 font-medium"
                                style="font-family:'Inter Tight',sans-serif;">
                                Belum ada mahasiswa yang mendaftar KP.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pendaftar->hasPages())
            <div class="mt-6 border-t border-[#F1F1F3] pt-4">
                {{ $pendaftar->links() }}
            </div>
        @endif
    </div>
</x-eoffice::layouts.koordinator>
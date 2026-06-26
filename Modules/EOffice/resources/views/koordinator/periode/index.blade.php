<x-eoffice::layouts.koordinator title="Periode">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Periode</span>
    @endsection

    {{-- Page Header --}}
    <div class="mb-6 lg:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1
                style="font-family:'Inter Tight',sans-serif; font-size:20px; font-weight:600; line-height:1.35; color:#0D0D12;">
                Periode Kerja Praktik
            </h1>
        </div>
        <a href="{{ route('eoffice.kp.koordinator.periode.create') }}"
            class="flex items-center justify-center px-4 py-2.5 bg-slate-900 border border-transparent text-white rounded-xl hover:bg-slate-800 transition-colors shadow-sm text-sm font-semibold">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 5v14m-7-7h14" />
            </svg>
            Periode Baru
        </a>
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
    <div
        style="background:#fff; border:1px solid #EAECF0; border-radius:12px; box-shadow:0 1px 2px rgba(16, 24, 40, 0.05); overflow:hidden; padding:24px;">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h2 style="font-family:'Inter Tight',sans-serif; font-size:16px; font-weight:600; color:#0D0D12;">
                Period Table
            </h2>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- Search --}}
                <div style="position:relative; flex:1; min-width:260px; max-width:320px;">
                    <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:16px; height:16px; color:#98A2B3;"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search" style="
                        width:100%;
                        padding:8px 12px 8px 36px;
                        border:1px solid #E4E7EC;
                        border-radius:8px;
                        font-family:'Inter Tight',sans-serif;
                        font-size:14px;
                        outline:none;
                        transition:border-color 0.2s;
                    " onfocus="this.style.borderColor='#A8B4FB'" onblur="this.style.borderColor='#E4E7EC'">
                </div>

                <button style="
                    display:inline-flex; align-items:center; gap:8px;
                    padding:8px 14px;
                    background:#fff; border:1px solid #E4E7EC; border-radius:8px;
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

                <button style="
                    display:inline-flex; align-items:center; gap:8px;
                    padding:8px 14px;
                    background:#fff; border:1px solid #E4E7EC; border-radius:8px;
                    font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#4B5563;
                    transition:background 0.2s;
                    white-space:nowrap;
                " class="hover:bg-gray-50">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                    Sort by
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="border-bottom:1px solid #F1F1F3; background-color:#FAFAFC;">
                        <th
                            style="padding:8px 12px 8px 36px; font-family:'Inter Tight',sans-serif; font-size:11px; font-weight:700; color:#666D80; text-transform:uppercase; letter-spacing:0.04em;">
                            Nama Periode</th>
                        <th
                            style="padding:8px 12px; font-family:'Inter Tight',sans-serif; font-size:11px; font-weight:700; color:#666D80; text-transform:uppercase; letter-spacing:0.04em;">
                            Durasi</th>
                        <th
                            style="padding:8px 12px; font-family:'Inter Tight',sans-serif; font-size:11px; font-weight:700; color:#666D80; text-transform:uppercase; letter-spacing:0.04em;">
                            Status</th>
                        <th
                            style="padding:8px 12px; font-family:'Inter Tight',sans-serif; font-size:11px; font-weight:700; color:#666D80; text-transform:uppercase; letter-spacing:0.04em;">
                            Action</th>
                    </tr>
                </thead>
                @forelse($periodes as $periode)
                    <tbody x-data="{ openOptions: false, expanded: false }">
                        <tr style="border-bottom:1px solid #F1F1F3;" class="hover:bg-[#F8F5FF] transition-colors">
                            <td
                                style="padding:8px 12px 8px 20px; font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:500; color:#0D0D12;">
                                <div class="flex items-center gap-2 cursor-pointer" @click="expanded = !expanded">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" :class="expanded ? 'rotate-180' : ''"
                                        class="transition-transform duration-200 text-[#666D80]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <span>Semester {{ $periode->semester }} {{ $periode->tahun_ajaran }}</span>
                                </div>
                            </td>
                            <td
                                style="padding:8px 12px; font-family:'Inter Tight',sans-serif; font-size:12px; color:#4B5563;">
                                {{ $periode->tanggal_buka ? \Carbon\Carbon::parse($periode->tanggal_buka)->translatedFormat('d F Y') : '-' }}
                                -
                                {{ $periode->tanggal_tutup ? \Carbon\Carbon::parse($periode->tanggal_tutup)->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td style="padding:8px 12px;">
                                @if($periode->is_active)
                                    <span
                                        style="
                                                                                                                                                                                    display:inline-flex; align-items:center; gap:6px;
                                                                                                                                                                                    padding:4px 10px;
                                                                                                                                                                                    background:#F0FDF4;
                                                                                                                                                                                    border:1px solid #BBF7D0;
                                                                                                                                                                                    border-radius:9999px;
                                                                                                                                                                                    font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#15803D;
                                                                                                                                                                                ">
                                        <span style="width:6px; height:6px; background:#15803D; border-radius:50%;"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        style="
                                                                                                                                                                                    display:inline-flex; align-items:center; gap:6px;
                                                                                                                                                                                    padding:4px 10px;
                                                                                                                                                                                    background:#F1F1F3;
                                                                                                                                                                                    border:1px solid #E2E8F0;
                                                                                                                                                                                    border-radius:9999px;
                                                                                                                                                                                    font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; color:#666D80;
                                                                                                                                                                                ">
                                        <span style="width:6px; height:6px; background:#666D80; border-radius:50%;"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 align-middle text-center relative" x-data="{ openOptions: false }"
                                @click.outside="openOptions = false">
                                <div class="flex items-center justify-center pointer-events-auto" @click.stop>
                                    <button type="button" @click="openOptions = !openOptions"
                                        class="p-1 px-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
                                            </path>
                                        </svg>
                                    </button>

                                    {{-- Dropdown Menu --}}
                                    <div x-show="openOptions" style="display:none;"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-10 top-10 mt-0 w-32 bg-white rounded-lg shadow-lg border border-slate-100 overflow-hidden z-50 text-left">

                                        <a href="{{ route('eoffice.kp.koordinator.periode.edit', $periode->id) }}"
                                            class="block w-full text-left px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('eoffice.kp.koordinator.periode.destroy', $periode->id) }}"
                                            method="POST" class="inline w-full m-0 p-0"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Expandable Details Row -->
                        <tr x-show="expanded" style="display:none;" x-transition>
                            <td colspan="4" class="p-0 border-b border-[#F1F1F3] bg-[#FAFAFC]">
                                <div class="flex flex-col md:flex-row gap-4 px-4 py-3" style="padding-left: 56px;">
                                    <!-- Content Area -->
                                    <div class="flex-1 max-w-[600px] flex flex-col gap-4">

                                        <!-- Kelas Dibuka -->
                                        <div>
                                            <h4
                                                style="font-family:'Inter Tight',sans-serif; font-size:10px; font-weight:700; color:#A0AABF; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:4px;">
                                                Kelas yang Dibuka
                                            </h4>
                                            @if($periode->kelas_dibuka && is_array($periode->kelas_dibuka) && count($periode->kelas_dibuka) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($periode->kelas_dibuka as $kelas)
                                                        <span
                                                            class="px-3 py-1 bg-white border border-[#E2E8F0] text-[#4B5563] text-xs font-semibold rounded-full shadow-sm">{{ $kelas }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-[#848A96] text-sm font-medium"
                                                    style="font-family:'Inter Tight',sans-serif;">Belum ada kelas didefinisikan
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Tanggal Fase -->
                                        <div>
                                            <h4
                                                style="font-family:'Inter Tight',sans-serif; font-size:10px; font-weight:700; color:#A0AABF; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:4px;">
                                                Tanggal Fase
                                            </h4>
                                            <div class="flex flex-col gap-4">
                                                <div class="flex items-center"
                                                    style="font-family:'Inter Tight',sans-serif;">
                                                    <div class="w-[120px] text-xs text-[#848A96] font-medium">Pra KP</div>
                                                    <div class="text-xs text-[#272835] font-semibold">
                                                        {{ $periode->pra_kp_mulai ? \Carbon\Carbon::parse($periode->pra_kp_mulai)->translatedFormat('d M Y') : '-' }}
                                                        -
                                                        {{ $periode->pra_kp_akhir ? \Carbon\Carbon::parse($periode->pra_kp_akhir)->translatedFormat('d M Y') : '-' }}
                                                    </div>
                                                </div>
                                                <div class="flex items-center"
                                                    style="font-family:'Inter Tight',sans-serif;">
                                                    <div class="w-[120px] text-xs text-[#848A96] font-medium">Saat KP</div>
                                                    <div class="text-xs text-[#272835] font-semibold">
                                                        {{ $periode->saat_kp_mulai ? \Carbon\Carbon::parse($periode->saat_kp_mulai)->translatedFormat('d M Y') : '-' }}
                                                        -
                                                        {{ $periode->saat_kp_akhir ? \Carbon\Carbon::parse($periode->saat_kp_akhir)->translatedFormat('d M Y') : '-' }}
                                                    </div>
                                                </div>
                                                <div class="flex items-center"
                                                    style="font-family:'Inter Tight',sans-serif;">
                                                    <div class="w-[120px] text-xs text-[#848A96] font-medium">Pasca KP</div>
                                                    <div class="text-xs text-[#272835] font-semibold">
                                                        {{ $periode->pasca_kp_mulai ? \Carbon\Carbon::parse($periode->pasca_kp_mulai)->translatedFormat('d M Y') : '-' }}
                                                        -
                                                        {{ $periode->pasca_kp_akhir ? \Carbon\Carbon::parse($periode->pasca_kp_akhir)->translatedFormat('d M Y') : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column: Optional configuration (Empty for now) -->
                                        <div class="flex-1"></div>
                                    </div>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 font-medium"
                                style="font-family:'Inter Tight',sans-serif;">
                                Belum ada data periode.
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>

        {{-- Pagination Mockup --}}
        <div class="flex items-center justify-between mt-6 border-t border-[#F1F1F3] pt-4">
            <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#666D80;">
                Menampilkan 1 hingga {{ $periodes->count() }} dari {{ $periodes->count() }} entri
            </span>
            <div class="flex items-center gap-2">
                <button disabled
                    style="padding:6px 10px; border:1px solid #E2E8F0; border-radius:6px; background:#F8F9FB; color:#A0AABF; cursor:not-allowed;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button
                    style="padding:4px 12px; border:1px solid #0B266E; border-radius:6px; background:#F8F5FF; color:#0B266E; font-weight:600; font-size:13px; font-family:'Inter Tight',sans-serif;">1</button>
                <button disabled
                    style="padding:6px 10px; border:1px solid #E2E8F0; border-radius:6px; background:#F8F9FB; color:#A0AABF; cursor:not-allowed;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</x-eoffice::layouts.koordinator>
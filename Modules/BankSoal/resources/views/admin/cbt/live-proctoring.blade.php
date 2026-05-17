<x-banksoal::layouts.admin>
    @section('breadcrumbs')
        <a href="#" class="hover:text-[#2A3A7C] transition-colors text-gray-500">Ujian Komprehensif</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-900 font-semibold">Live Pengawasan</span>
    @endsection

    <div class="w-full">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Live Pengawasan Ujian</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Pantau mahasiswa yang sedang mengerjakan ujian secara real-time.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-[#2A3A7C]/40 hover:text-[#2A3A7C] transition-all duration-200 shadow-sm text-[13px] font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-[13px] font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#2A3A7C]/10 flex items-center justify-center text-[#2A3A7C]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Sedang Ujian</p>
                    <p class="text-[28px] font-bold text-gray-900 leading-none">{{ $sessions->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-500 text-[13px] font-semibold tracking-wide">
                            <th class="px-6 py-4">Peserta</th>
                            <th class="px-6 py-4">Sesi / Token</th>
                            <th class="px-6 py-4">Mulai Pengerjaan</th>
                            <th class="px-6 py-4">Progres Terjawab</th>
                            <th class="px-6 py-4">Status / Cheat Log</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        @forelse($sessions as $session)
                            @php
                                $totalSoal = $session->jawabans_count;
                                $terjawab  = $session->terjawab_count;
                                $progres   = $totalSoal > 0 ? round(($terjawab / $totalSoal) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#2A3A7C]/10 text-[#2A3A7C] flex items-center justify-center font-bold text-[13px] shrink-0">
                                            {{ substr($session->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $session->user->name }}</p>
                                            <p class="text-gray-500 text-[12px] mt-0.5">
                                                {{ $session->user->nim ?? 'NIM tidak tersedia' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[12px] font-medium bg-gray-50 border border-gray-200 text-gray-700">
                                        {{ $session->title }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-900 font-medium">
                                        {{ \Carbon\Carbon::parse($session->started_at)->format('H:i:s') }}
                                    </p>
                                    <p class="text-[12px] text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($session->started_at)->diffForHumans() }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-full bg-gray-100 rounded-full h-2 min-w-[80px]">
                                            <div class="bg-[#2A3A7C] h-2 rounded-full" style="width: {{ $progres }}%;"></div>
                                        </div>
                                        <span class="text-[12px] font-semibold text-gray-600">{{ $terjawab }}/{{ $totalSoal }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[12px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Ongoing
                                    </span>
                                    <!-- Log pelanggaran (Tab Switch) -->
                                    @php $totalPelanggaran = $session->cheat_logs_count; @endphp
                                    <div class="mt-1.5 text-[12px] text-gray-500 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Tab Switches: <strong class="{{ $totalPelanggaran > 0 ? 'text-red-600 font-bold' : 'text-gray-600' }}">{{ $totalPelanggaran }}</strong>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('banksoal.admin.cbt.force-submit', $session->id) }}" method="POST"
                                        onsubmit="return confirm('Anda yakin ingin memaksa selesai sesi ujian mahasiswa ini?');">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center justify-center p-2 text-rose-500 hover:bg-rose-50 hover:text-rose-600 rounded-lg transition-colors border border-transparent hover:border-rose-200"
                                            title="Force Submit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-[13px] font-medium">Tidak ada mahasiswa yang sedang melaksanakan ujian saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-banksoal::layouts.admin>
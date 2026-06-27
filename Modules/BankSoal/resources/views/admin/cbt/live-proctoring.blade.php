<x-banksoal::layouts.admin>
    @section('breadcrumbs')
        <a href="#" class="hover:text-primary transition-colors text-gray-500">Ujian Komprehensif</a>
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
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-primary/40 hover:text-primary transition-all duration-200 shadow-sm text-[13px] font-semibold">
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
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
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
        <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-bottom: 2rem;">
            
            {{-- Table Toolbar (opsional, jika tidak ada toolbar biarkan kosong, tapi kita berikan header sedikit) --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
                <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">Daftar Mahasiswa Ujian</h2>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; min-width:850px;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Peserta</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Sesi</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Mulai Pengerjaan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Progres Terjawab</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status / Cheat Log</th>
                            <th style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            @php
                                $totalSoal = $session->jawabans_count;
                                $terjawab  = $session->terjawab_count;
                                $progres   = $totalSoal > 0 ? round(($terjawab / $totalSoal) * 100) : 0;
                            @endphp
                            <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" 
                                onmouseover="this.style.background='#FAFAFA'" 
                                onmouseout="this.style.background='transparent'">
                                <td style="padding:14px 16px;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div style="width:36px; height:36px; border-radius:99px; background:rgba(94,83,244,0.1); color:var(--c-primary); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0;">
                                            {{ substr($session->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0;">{{ $session->user->name }}</p>
                                            <p style="font-size:12px; color:var(--c-fg-muted); margin:2px 0 0; font-family:monospace;">
                                                {{ $session->user->student->student_number ?? 'NIM tidak tersedia' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:14px 16px;">
                                    @if($session->jadwal)
                                        <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0;">
                                            {{ is_numeric($session->jadwal->nama_sesi) ? 'Sesi ' . $session->jadwal->nama_sesi : $session->jadwal->nama_sesi }}
                                        </p>
                                        <p style="font-size:11px; color:var(--c-fg-muted); margin:2px 0 0;">
                                            {{ \Carbon\Carbon::parse($session->jadwal->tanggal_ujian)->translatedFormat('l, d F Y') }}
                                        </p>
                                    @else
                                        <span style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:99px; font-size:11px; font-weight:600; background:#F3F4F6; color:var(--c-fg-sec);">
                                            {{ $session->title }}
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:14px 16px;">
                                    <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0;">
                                        {{ \Carbon\Carbon::parse($session->started_at)->format('H:i:s') }}
                                    </p>
                                    <p style="font-size:12px; color:var(--c-fg-muted); margin:2px 0 0;">
                                        {{ \Carbon\Carbon::parse($session->started_at)->diffForHumans() }}
                                    </p>
                                </td>
                                <td style="padding:14px 16px;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div style="width:100%; min-width:80px; height:8px; background:#F3F4F6; border-radius:99px; overflow:hidden;">
                                            <div style="height:100%; background:var(--c-primary); border-radius:99px; width: {{ $progres }}%;"></div>
                                        </div>
                                        <span style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">{{ $terjawab }}/{{ $totalSoal }}</span>
                                    </div>
                                </td>
                                <td style="padding:14px 16px;">
                                    <span style="display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:99px; font-size:11px; font-weight:600; background:#ECFDF5; color:#059669; border:1px solid #A7F3D0;">
                                        <span style="width:6px; height:6px; border-radius:99px; background:#10B981;" class="animate-pulse"></span>
                                        Ongoing
                                    </span>
                                    <!-- Log pelanggaran -->
                                    @php $totalPelanggaran = $session->cheat_logs_count; @endphp
                                    <div style="margin-top:6px; font-size:11px; display:flex; align-items:center; gap:6px; color:var(--c-fg-muted);">
                                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Tab Switches: <strong style="font-weight:700; color:{{ $totalPelanggaran > 0 ? '#DC2626' : 'var(--c-fg-sec)' }}">{{ $totalPelanggaran }}</strong>
                                    </div>
                                </td>
                                <td style="padding:14px 16px; text-align:right;">
                                    <form action="{{ route('banksoal.admin.cbt.force-submit', $session->id) }}" method="POST"
                                        onsubmit="return confirm('Anda yakin ingin memaksa selesai sesi ujian mahasiswa ini?');">
                                        @csrf
                                        <button type="submit"
                                            style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:6px; background:transparent; color:#EF4444; border:none; cursor:pointer; transition:all .15s;"
                                            title="Force Submit"
                                            onmouseover="this.style.background='#FEF2F2'"
                                            onmouseout="this.style.background='transparent'">
                                            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:60px 20px; text-align:center;">
                                    <div style="display:flex; flex-direction:column; items-center:center; justify-content:center;">
                                        <div style="width:56px; height:56px; background:#F9FAFB; display:flex; align-items:center; justify-content:center; border-radius:16px; margin:0 auto 16px; border:1px solid #F3F4F6;">
                                            <svg style="width:28px; height:28px; color:var(--c-fg-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                        <h3 style="font-size:14px; font-weight:600; color:var(--c-fg); margin:0 0 4px;">Belum Ada Peserta Ujian</h3>
                                        <p style="font-size:13px; color:var(--c-fg-muted); margin:0;">
                                            Tidak ada mahasiswa yang sedang melaksanakan ujian saat ini.
                                        </p>
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

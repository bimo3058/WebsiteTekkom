<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Dashboard Mahasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter Tight', sans-serif; }
        :root {
            --primary-50:#eef2ff;--primary-100:#e0e7ff;--primary-500:#4f46e5;
            --grey-0:#fff;--grey-50:#f9fafb;--grey-100:#f3f4f6;--grey-200:#e5e7eb;
            --grey-400:#9ca3af;--grey-500:#6b7280;--grey-600:#4b5563;
            --grey-700:#374151;--grey-800:#1f2937;--grey-900:#030712;
            --success-0:#f0fdf4;--success-50:#dcfce7;--success-100:#bbf7d0;--success-300:#16a34a;
            --warning-0:#fffbeb;--warning-50:#fef3c7;--warning-100:#fde68a;--warning-300:#d97706;
            --error-0:#fff1f2;--error-50:#ffe4e6;--error-200:#f87171;--error-300:#dc2626;
            --sky-500:#0ea5e9;
        }
        .sikape-card { background:#fff; border:1px solid var(--grey-200); border-radius:12px; }
    </style>
</head>
<body style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    @include('eoffice::kp.mahasiswa.partials.sidebar')

    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        @include('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Dashboard'])

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-xl border" style="background:var(--success-0);border-color:var(--success-50);color:var(--success-300);">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-xl border" style="background:var(--error-0);border-color:var(--error-50);color:var(--error-300);">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
            @endif

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-semibold" style="color:var(--grey-900);">Dashboard KP</h1>
                <p class="text-sm mt-1" style="color:var(--grey-500);">Selamat datang, <strong style="color:var(--grey-700);">{{ $mahasiswa->nama_lengkap }}</strong>. Pantau seluruh progres KP Anda di sini.</p>
            </div>

            {{-- ═══ STATUS STEPPER ═══ --}}
            @if($kp)
            @php
                $steps = [
                    ['label'=>'Pra KP',   'desc'=>'Pendaftaran', 'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    ['label'=>'Saat KP',  'desc'=>'Pelaksanaan', 'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['label'=>'Pasca KP', 'desc'=>'Seminar',     'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['label'=>'Selesai',  'desc'=>'Penilaian',   'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                $stepMap = ['Pra-KP'=>0,'pending'=>0,'Saat KP'=>1,'active'=>1,'Pasca KP'=>2,'Selesai'=>3,'completed'=>3];
                $cur = $stepMap[$kp->status_kp] ?? 0;
            @endphp
            <div class="sikape-card p-6 mb-6">
                <p class="text-xs font-semibold uppercase tracking-widest mb-5" style="color:var(--grey-400);">Progres Saat Ini</p>
                <div class="flex items-start gap-0">
                    @foreach($steps as $i => $step)
                    <div class="flex-1 flex flex-col items-center relative">
                        {{-- Connector line --}}
                        @if($i < count($steps)-1)
                        <div class="absolute top-5 left-1/2 w-full h-0.5 z-0" style="background:{{ $i < $cur ? '#4f46e5' : 'var(--grey-200)' }};"></div>
                        @endif
                        {{-- Circle --}}
                        <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 relative transition-all duration-300 flex-shrink-0"
                             style="background:{{ $i < $cur ? '#4f46e5' : ($i === $cur ? '#4f46e5' : 'var(--grey-100)') }};
                                    {{ $i === $cur ? 'box-shadow:0 0 0 4px #e0e7ff;' : '' }}">
                            @if($i < $cur)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <svg class="w-5 h-5" style="color:{{ $i <= $cur ? 'white' : 'var(--grey-400)' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $step['icon'] }}"/></svg>
                            @endif
                        </div>
                        {{-- Label --}}
                        <p class="text-xs font-semibold mt-2 text-center" style="color:{{ $i <= $cur ? 'var(--grey-800)' : 'var(--grey-400)' }};">{{ $step['label'] }}</p>
                        <p class="text-[10px] text-center" style="color:{{ $i <= $cur ? 'var(--grey-500)' : 'var(--grey-300)' }};">{{ $step['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ═══ STATS CARDS ═══ --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @php
                    $badgeMap = [
                        'Pra-KP'=>['bg'=>'var(--warning-50)','color'=>'var(--warning-300)'],
                        'Saat KP'=>['bg'=>'#eff6ff','color'=>'#2563eb'],
                        'Pasca KP'=>['bg'=>'#f5f3ff','color'=>'#7c3aed'],
                        'Selesai'=>['bg'=>'var(--success-50)','color'=>'var(--success-300)'],
                    ];
                    $bs = $badgeMap[$kp->status_kp ?? ''] ?? ['bg'=>'var(--grey-100)','color'=>'var(--grey-500)'];
                @endphp

                <div class="sikape-card p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color:var(--grey-400);">Status KP</p>
                    @if($kp)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold" style="background:{{ $bs['bg'] }};color:{{ $bs['color'] }};">{{ $kp->status_kp }}</span>
                    @else
                    <p class="text-sm font-semibold" style="color:var(--grey-400);">Belum Daftar</p>
                    @endif
                </div>

                <div class="sikape-card p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color:var(--grey-400);">Dosen Pembimbing</p>
                    <p class="text-sm font-semibold truncate" style="color:var(--grey-800);">{{ $kp?->dosenPembimbing?->name ?? '—' }}</p>
                </div>

                <div class="sikape-card p-5 flex flex-col justify-center">
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color:var(--grey-400);">Nilai Akhir KP</p>
                    <p class="text-2xl font-bold truncate mt-1" style="color:var(--grey-900);">
                        @if($kp && $kp->penilaian && $kp->penilaian->nilai_akhir)
                            {{ $kp->penilaian->nilai_akhir }}
                        @else
                            <span class="text-sm font-semibold" style="color:var(--grey-400);">Belum Dinilai</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Timeline --}}
                <div class="lg:col-span-2 sikape-card overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b flex items-center justify-between" style="border-color:var(--grey-100);">
                        <h2 class="text-base font-bold" style="color:var(--grey-900);">Timeline KP</h2>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-6">
                        @if(isset($timeline) && $timeline->count() > 0)
                            <div class="relative border-l-2 ml-3 space-y-8" style="border-color:var(--grey-200);">
                                @foreach($timeline as $item)
                                <div class="relative pl-6">
                                    <div class="absolute w-4 h-4 rounded-full -left-[9px] top-1 border-2 border-white" style="background:var(--primary-500); box-shadow: 0 0 0 4px var(--primary-50);"></div>
                                    <p class="text-base font-bold" style="color:var(--grey-900);">{{ $item->judul }}</p>
                                    <p class="text-sm mt-1 leading-relaxed" style="color:var(--grey-600);">{{ $item->konten }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <p class="text-sm font-semibold" style="color:var(--grey-500);">Belum ada timeline KP</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Pengumuman --}}
                <div class="sikape-card overflow-hidden flex flex-col max-h-[600px]">
                    <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--grey-100);">
                        <h2 class="text-sm font-semibold" style="color:var(--grey-800);">Pengumuman</h2>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:var(--primary-50);color:var(--primary-500);">{{ $pengumuman->count() }}</span>
                    </div>
                    <div class="flex-1 divide-y overflow-y-auto" style="divide-color:var(--grey-100);">
                        @forelse($pengumuman as $ann)
                        <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background:var(--primary-500);"></div>
                                <div>
                                    <p class="text-sm font-semibold" style="color:var(--grey-800);">{{ $ann->judul }}</p>
                                    <p class="text-xs mt-1 line-clamp-2 leading-relaxed" style="color:var(--grey-500);">{{ Str::limit($ann->konten, 100) }}</p>
                                    <p class="text-[10px] mt-2 font-medium uppercase tracking-wider" style="color:var(--grey-400);">{{ $ann->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center py-12 px-5 text-center">
                            <svg class="w-10 h-10 mb-3" style="color:var(--grey-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            <p class="text-xs font-semibold" style="color:var(--grey-500);">Belum ada pengumuman</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>
</body>
</html>

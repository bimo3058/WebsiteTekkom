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
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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

                <div class="sikape-card p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color:var(--grey-400);">Dokumen</p>
                    <div class="flex items-end gap-2">
                        <p class="text-3xl font-bold" style="color:var(--grey-900);">{{ $dokumenStats['total'] }}</p>
                        @if($dokumenStats['menunggu'] > 0)
                        <p class="text-xs pb-1 font-semibold" style="color:var(--warning-300);">{{ $dokumenStats['menunggu'] }} pending</p>
                        @elseif($dokumenStats['total'] > 0)
                        <p class="text-xs pb-1 font-semibold" style="color:var(--success-300);">{{ $dokumenStats['disetujui'] }} acc</p>
                        @endif
                    </div>
                </div>

                <div class="sikape-card p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color:var(--grey-400);">Tempat KP</p>
                    <p class="text-sm font-semibold truncate" style="color:var(--grey-800);">{{ $kp?->tempat_fix ?? $kp?->rencana_tempat ?? '—' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Langkah Selanjutnya + Detail KP --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Next Steps --}}
                    <div class="sikape-card p-6">
                        <h2 class="text-sm font-semibold mb-4" style="color:var(--grey-800);">Langkah Selanjutnya</h2>

                        @if(!$kp)
                        <div class="flex items-start gap-4 p-4 rounded-xl border" style="background:var(--warning-0);border-color:var(--warning-100);">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--warning-50);">
                                <svg class="w-5 h-5" style="color:var(--warning-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold" style="color:#92400e;">Belum Mendaftar KP</p>
                                <p class="text-xs mt-1" style="color:#92400e;">Mulai proses KP dengan mengisi formulir pendaftaran dan upload transkrip nilai terbaru.</p>
                                <a href="{{ route('eoffice.kp.mahasiswa.pendaftaran') }}" class="inline-flex items-center mt-3 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all" style="background:var(--warning-300);">
                                    Daftar Sekarang →
                                </a>
                            </div>
                        </div>

                        @elseif(in_array($kp->status_kp, ['Pra-KP','pending']))
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 p-3 rounded-lg" style="background:var(--grey-50);">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background:#dbeafe;">
                                    <svg class="w-3.5 h-3.5" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm" style="color:var(--grey-700);">Menunggu validasi Koordinator KP</p>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-lg" style="background:var(--grey-50);">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background:var(--grey-200);">
                                    <svg class="w-3.5 h-3.5" style="color:var(--grey-400);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <p class="text-sm" style="color:var(--grey-500);">Menunggu penunjukan dosen pembimbing</p>
                            </div>
                        </div>

                        @elseif(in_array($kp->status_kp, ['Saat KP','active']))
                        <div class="space-y-2">
                            @if(empty($kp->judul_fix) || empty($kp->tempat_fix))
                            <a href="{{ route('eoffice.kp.mahasiswa.dokumen') }}" class="flex items-center gap-3 p-3 rounded-lg border transition-all hover:shadow-sm" style="background:var(--warning-0);border-color:var(--warning-100);">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background:var(--warning-50);"><svg class="w-3.5 h-3.5" style="color:var(--warning-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg></div>
                                <p class="text-sm font-semibold" style="color:#92400e;">Isi judul dan tempat KP fix →</p>
                            </a>
                            @endif
                            <a href="{{ route('eoffice.kp.mahasiswa.dokumen') }}" class="flex items-center gap-3 p-3 rounded-lg border transition-all hover:shadow-sm" style="background:var(--grey-50);border-color:var(--grey-200);">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background:#dbeafe;"><svg class="w-3.5 h-3.5" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                                <p class="text-sm" style="color:var(--grey-700);">Upload dokumen KP (laporan, makalah, bukti terima) →</p>
                            </a>
                        </div>

                        @else
                        <a href="{{ route('eoffice.kp.mahasiswa.seminar') }}" class="flex items-center gap-3 p-3 rounded-lg border transition-all hover:shadow-sm" style="background:var(--success-0);border-color:var(--success-100);">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center" style="background:var(--success-50);"><svg class="w-3.5 h-3.5" style="color:var(--success-300);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                            <p class="text-sm font-semibold" style="color:var(--success-300);">Daftarkan seminar KP Anda →</p>
                        </a>
                        @endif
                    </div>

                    {{-- KP Details --}}
                    @if($kp)
                    <div class="sikape-card overflow-hidden">
                        <div class="px-6 py-4 border-b" style="border-color:var(--grey-100);">
                            <h2 class="text-sm font-semibold" style="color:var(--grey-800);">Detail Kerja Praktik</h2>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                            @foreach([
                                ['Rencana Judul', $kp->rencana_judul],
                                ['Rencana Instansi', $kp->rencana_tempat],
                                ['Judul Final', $kp->judul_fix ?: '—'],
                                ['Instansi Final', $kp->tempat_fix ?: '—'],
                                ['Mulai', $kp->tanggal_mulai ? \Carbon\Carbon::parse($kp->tanggal_mulai)->translatedFormat('d F Y') : '—'],
                                ['Selesai', $kp->tanggal_selesai ? \Carbon\Carbon::parse($kp->tanggal_selesai)->translatedFormat('d F Y') : '—'],
                            ] as [$label, $val])
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wider mb-0.5" style="color:var(--grey-400);">{{ $label }}</p>
                                <p class="text-sm font-medium" style="color:var(--grey-800);">{{ $val ?? '—' }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Pengumuman --}}
                <div class="sikape-card overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--grey-100);">
                        <h2 class="text-sm font-semibold" style="color:var(--grey-800);">Pengumuman</h2>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:var(--primary-50);color:var(--primary-500);">{{ $pengumuman->count() }}</span>
                    </div>
                    <div class="flex-1 divide-y overflow-y-auto" style="divide-color:var(--grey-100);">
                        @forelse($pengumuman as $ann)
                        <div class="px-5 py-4">
                            <div class="flex items-start gap-2">
                                <div class="w-1.5 h-1.5 rounded-full mt-2 flex-shrink-0" style="background:var(--primary-500);"></div>
                                <div>
                                    <p class="text-sm font-semibold" style="color:var(--grey-800);">{{ $ann->judul }}</p>
                                    <p class="text-xs mt-0.5 line-clamp-2" style="color:var(--grey-500);">{{ Str::limit($ann->konten, 100) }}</p>
                                    <p class="text-[10px] mt-1.5" style="color:var(--grey-400);">{{ $ann->created_at->diffForHumans() }}</p>
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

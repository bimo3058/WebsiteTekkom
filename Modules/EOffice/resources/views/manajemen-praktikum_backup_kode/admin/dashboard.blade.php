<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard">
@php
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Praktikum[] $praktikums */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumumanTerbaru */
    /** @var \Illuminate\Support\Collection|array $dosenTerbaru */
    /** @var \Illuminate\Support\Collection|array $matkulTerbaru */
    $name      = auth()->user()->name;
    $firstName = explode(' ', $name)[0];
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
@endphp

<x-slot name="header">
<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">Dashboard</h1>
            <span style="font-size:10px; font-weight:600; color:var(--c-primary); background:rgba(94,83,244,0.09); border:1px solid rgba(94,83,244,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Admin</span>
        </div>
        <p style="font-size:12px; color:var(--c-fg-muted);">
            Selamat datang, <span style="color:var(--c-fg); font-weight:600;">{{ $firstName }}</span>
            <span style="margin-left:4px; color:var(--c-fg-placeholder);">·</span>
            <span style="margin-left:4px; color:var(--c-fg-muted);">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}</span>
        </p>
    </div>
</div>
</x-slot>
{{-- ═══════════════════════════════════════════════
     STAT CARDS
═══════════════════════════════════════════════ --}}
<div class="mp-stats-grid cols-3" style="flex-shrink:0;">

    {{-- Praktikum Aktif --}}
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg);">Praktikum Aktif</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;">{{ $totalPraktikumAktif ?? 0 }}</div>
        <div class="mp-stat-sub" style="font-size:13px;">Saat Ini</div>
    </div>

    {{-- Total Dosen --}}
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg);">Total Dosen</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;">{{ $totalDosen ?? 0 }}</div>
        <div class="mp-stat-sub" style="font-size:13px;">Terdaftar di Sistem</div>
    </div>

    {{-- Pendaftaran --}}
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon yellow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg);">Pendaftaran</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;">{{ ($totalAsprakPending ?? 0) + ($totalKoorPending ?? 0) }}</div>
        <div class="mp-stat-sub" style="font-size:13px;">Perlu Peninjauan</div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MIDDLE ROW: Daftar Praktikum & Pendaftaran
═══════════════════════════════════════════════ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:24px; flex-shrink:0;">

    {{-- Panel Daftar Praktikum --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border);">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Daftar Praktikum</div>
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}"
               style="font-size:12px; font-weight:600; color:var(--c-primary); text-decoration:none;">Lihat Semua &rarr;</a>
        </div>
        <div style="display:grid; grid-template-columns:1fr 100px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Praktikum</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Status</div>
        </div>
        @forelse($praktikums ?? [] as $p)
        <div style="display:grid; grid-template-columns:1fr 100px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
            <div style="font-size:13px; font-weight:600; color:var(--c-fg);">{{ $p->nama }}</div>
            <div style="text-align:right;">
                @if($p->status === 'aktif')
                <span class="mp-badge success sm">Aktif</span>
                @else
                <span class="mp-badge neutral sm">Tutup</span>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Daftar Praktikum Aktif belum tersedia</span>
        </div>
        @endforelse
    </div>

    {{-- Panel Pendaftaran --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border);">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Pendaftaran</div>
        </div>
        <div style="display:grid; grid-template-columns:1fr auto; gap:16px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Praktikum</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Jenis Pendaftaran</div>
        </div>
        @php
            $listPendaftaran = [];
            if(isset($periodeBuka)) {
                foreach($periodeBuka as $periode) {
                    $isKoor = $periode->jenis === 'koor';
                    $listPendaftaran[] = [
                        'nama' => $periode->praktikum?->nama ?? 'Praktikum', 
                        'jenis' => $isKoor ? 'Koordinator Praktikum' : 'Asisten Praktikum',
                        'bg' => $isKoor ? 'rgba(94,83,244,0.1)' : '#DDF2EE',
                        'text' => $isKoor ? 'var(--c-primary)' : '#287F6E',
                        'border' => $isKoor ? 'rgba(94,83,244,0.2)' : '#40C4AA'
                    ];
                }
            }
        @endphp
        @forelse(array_slice($listPendaftaran, 0, 5) as $pend)
        <div style="display:grid; grid-template-columns:1fr auto; gap:16px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
            <div style="font-size:13px; font-weight:600; color:var(--c-fg);">{{ $pend['nama'] }}</div>
            <div style="text-align:right;">
                <span style="display:inline-block; font-size:11px; font-weight:600; background:{{ $pend['bg'] }}; color:{{ $pend['text'] }}; border:1px solid {{ $pend['border'] }}; padding:2px 8px; border-radius:6px;">{{ $pend['jenis'] }}</span>
            </div>
        </div>
        @empty
        <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                <path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"/>
            </svg>
            <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Pendaftaran Koordinator Praktikum dan Asisten Praktikum belum tersedia</span>
        </div>
        @endforelse
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     BOTTOM ROW: Daftar Dosen
═══════════════════════════════════════════════ --}}
<div id="daftar-dosen" style="margin-top:24px; background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); flex-shrink:0;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border);">
        <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Daftar Dosen</div>
        
        <form id="dosen_filter_form" method="GET" action="{{ url()->current() }}#daftar-dosen" style="display:flex; gap:10px; margin:0;">
            <div class="relative flex items-center">
                <svg class="absolute left-3 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="search_dosen" value="{{ request('search_dosen') }}" placeholder="Search dosen" onchange="this.form.submit()" style="padding:6px 12px 6px 32px; border:1px solid var(--c-border); border-radius:8px; font-size:13px; outline:none; min-width:200px;">
            </div>
        </form>
    </div>
    
    <div class="grid gap-4 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
         style="grid-template-columns: 50px 2fr 1.5fr 150px 130px;">
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">No</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Nama Dosen</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Email</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">NIP</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Praktikum Diampu</div>
    </div>

    @forelse($dosenTerbaru ?? [] as $i => $d)
    @php
        $parts   = explode(' ', $d['name'] ?? 'D');
        $ini     = strtoupper(substr($parts[0] ?? 'D', 0, 1) . substr($parts[1] ?? $parts[0], 0, 1));
    @endphp
    <div class="grid gap-4 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
         style="grid-template-columns: 50px 2fr 1.5fr 150px 130px;">
        {{-- No --}}
        <div class="text-[12px] font-semibold text-[#666D80]">
            {{ $dosenPaginator->firstItem() + $i }}
        </div>

        {{-- Nama --}}
        <div class="flex items-center gap-[10px] min-w-0 pr-3">
            <div class="mp-av sky flex-shrink-0">
                @if($d['avatar_url'] ?? null)
                <img src="{{ $d['avatar_url'] }}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                @else
                {{ $ini }}
                @endif
            </div>
            <div class="min-w-0">
                <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $d['name'] }}</div>
            </div>
        </div>

        {{-- Email --}}
        <div class="text-[12px] text-[#666D80] truncate">{{ $d['email'] }}</div>
        
        {{-- NIP --}}
        <div class="text-[12px] font-medium text-[#353849]">{{ $d['employee_number'] }}</div>
        
        {{-- Praktikum Diampu --}}
        <div class="text-center">
            <span class="text-[13px] font-medium text-[#353849]">{{ $d['jumlah_praktikum'] ?? 0 }}</span>
            <span class="text-[12px] font-medium text-[#353849]"> praktikum</span>
        </div>
    </div>
    @empty
    <div style="padding:32px; text-align:center; font-size:13px; color:var(--c-fg-muted);">Belum ada data dosen.</div>
    @endforelse

    {{-- Pagination Custom Fungsional --}}
    @if($dosenPaginator->hasPages() || $dosenPaginator->total() > 0)
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [5, 10, 20] }" 
                 class="relative flex items-center gap-2 border border-[#DFE1E7] rounded-[8px] px-2 py-1 cursor-pointer bg-white hover:bg-[#F6F8FA] transition-colors"
                 @click="open = !open">
                <span class="text-[12px] text-[#666D80]">Per halaman</span>
                <div class="flex items-center gap-1 font-semibold text-[12px] text-[#0D0D12]">
                    <span x-text="selected"></span>
                    <svg class="w-3 h-3 text-[#666D80] transition-transform duration-200" :class="{'rotate-180': open}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <div x-show="open" @click.away="open = false" style="display: none;" 
                     class="absolute bottom-full left-0 mb-1 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-lg shadow-[0_4px_12px_rgba(0,0,0,0.08)] py-1.5 overflow-hidden">
                    <template x-for="option in options" :key="option">
                        <label class="flex items-center justify-between px-3 py-2 cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0"
                               :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                            <input type="radio" :value="option" name="per_page" form="dosen_filter_form" x-model="selected" @change="document.getElementById('dosen_filter_form').submit()" class="hidden">
                            <span x-text="option"></span>
                            <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </label>
                    </template>
                </div>
            </div>
            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $dosenPaginator->firstItem() ?? 0 }} sampai {{ $dosenPaginator->lastItem() ?? 0 }} dari {{ $dosenPaginator->total() }} data</div>
        </div>
        
        <div style="display:flex; gap:4px;">
            {{-- Tombol Prev --}}
            @if ($dosenPaginator->onFirstPage())
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @else
                <a href="{{ $dosenPaginator->previousPageUrl() }}" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            {{-- Looping Nomor Halaman (Pola Custom) --}}
            @php
                $current = $dosenPaginator->currentPage();
                $last = $dosenPaginator->lastPage();
                
                if ($current % 3 == 1) {
                    $start = $current;
                } else {
                    $start = $current - 1;
                }

                // Jika sudah mentok di halaman terakhir
                if ($start + 2 > $last) {
                    $start = max(1, $last - 2);
                }
                
                $end = min($start + 2, $last);
            @endphp
            @if($start > 1)
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <span style="width:32px; height:32px; border:none; background:var(--c-primary); color:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600;">{{ $i }}</span>
                @else
                    <a href="{{ $dosenPaginator->url($i) }}" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; font-weight:600; color:var(--c-fg); text-decoration:none;">{{ $i }}</a>
                @endif
            @endfor

            @if($end < $last)
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            @endif

            {{-- Tombol Next --}}
            @if ($dosenPaginator->hasMorePages())
                <a href="{{ $dosenPaginator->nextPageUrl() }}" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>
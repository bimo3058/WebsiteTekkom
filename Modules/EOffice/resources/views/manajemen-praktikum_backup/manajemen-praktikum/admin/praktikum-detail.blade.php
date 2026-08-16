<x-eoffice::manajemen-praktikum.layout pageTitle="Detail Praktikum">

{{-- ════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════ --}}
<div class="mp-page-header flex-shrink-0">
    <div class="flex items-center gap-2.5 flex-wrap min-w-0">
        <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}"
           class="flex items-center gap-1.5 text-[13px] font-medium text-[#666D80] hover:text-[#0B266E] transition-colors whitespace-nowrap flex-shrink-0">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            Daftar Praktikum
        </a>
        <span class="text-[#DFE1E7] text-[16px] font-light">/</span>
        <div class="flex items-center gap-2 min-w-0">
            <h1 class="text-[13px] font-semibold text-[#0D0D12] overflow-hidden text-ellipsis whitespace-nowrap">{{ $praktikum->nama }}</h1>
            @if($praktikum->kode)
            <span class="text-[11px] font-mono font-bold text-[#0B266E] bg-[#0B266E]/10 px-2 py-0.5 rounded-[6px] whitespace-nowrap flex-shrink-0">{{ $praktikum->kode }}</span>
            @endif
            <span class="mp-badge {{ $praktikum->status === 'aktif' ? 'success' : 'neutral' }} sm flex-shrink-0">
                <span class="dot"></span>{{ ucfirst($praktikum->status) }}
            </span>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════
     2-COLUMN LAYOUT
════════════════════════════════════════════ --}}
<div class="flex gap-4 flex-1 min-h-0 pb-0.5" style="align-items: flex-start;">

    {{-- ══ SIDEBAR KIRI ═══════════════════════════════════════════════════ --}}
    <div class="w-[300px] flex-shrink-0 flex flex-col gap-3 pb-1 sticky top-0">

        {{-- Info Praktikum --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
            <div class="px-4 py-3 border-b border-[#F3F4F6] bg-[#FAFAFA]">
                <div class="text-[11px] font-bold text-[#0D0D12] uppercase tracking-[0.07em]">Info Praktikum</div>
            </div>
            <div class="p-4 flex flex-col gap-3.5">
                <div>
                    <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[0.05em] mb-1">Status</div>
                    <span class="mp-badge {{ $praktikum->status === 'aktif' ? 'success' : 'neutral' }} sm"><span class="dot"></span>{{ ucfirst($praktikum->status) }}</span>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[0.05em] mb-1">Periode Praktikum</div>
                    <div class="text-[13px] font-semibold text-[#0D0D12]">Tahun Ajaran {{ $praktikum->tahun_ajaran }} Semester {{ ucfirst($praktikum->semester) }}</div>
                </div>
                @if($praktikum->matkul)
                <div>
                    <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[0.05em] mb-1">Mata Kuliah</div>
                    <div class="text-[12px] font-semibold text-[#0D0D12] leading-[1.4]">{{ $praktikum->matkul->nama }}</div>
                    <span class="text-[10px] font-mono font-bold text-[#0B266E] bg-[#0B266E]/10 px-1.5 py-0.5 rounded-[4px] inline-block mt-1">{{ $praktikum->matkul->kode }}</span>
                </div>
                @endif

                <div class="h-px bg-[#F3F4F6] -mx-0.5"></div>
                <div>
                    <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[0.05em] mb-1">Dibuat</div>
                    <div class="text-[11px] text-[#666D80]">{{ $praktikum->created_at?->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                </div>
            </div>
        </div>

        {{-- Dosen Pengampu --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
            <div class="px-4 py-3 border-b border-[#F3F4F6] bg-[#FAFAFA]">
                <div class="text-[11px] font-bold text-[#0D0D12] uppercase tracking-[0.07em]">Dosen Pengampu</div>
            </div>
            <div class="p-4 flex flex-col gap-3">
                @if($praktikum->dosens->count() > 0)
                    @php $avColors = ['bg-[#E0F2FE] text-[#0369A1]', 'bg-[#FEF9C3] text-[#A16207]', 'bg-[#DCFCE7] text-[#15803D]', 'bg-[#F3E8FF] text-[#7E22CE]', 'bg-[#FFE4E6] text-[#BE123C]']; @endphp
                    @foreach($praktikum->dosens as $d)
                    @php
                        $dp = explode(' ', $d->name);
                        $di = strtoupper(substr($dp[0],0,1).substr($dp[1]??$dp[0],0,1));
                        $avColor = $avColors[crc32($d->email ?? '') % count($avColors)];
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-[12px] flex-shrink-0 {{ $avColor }}">{{ $di }}</div>
                        <div class="min-w-0">
                            <div class="text-[12px] font-semibold text-[#0D0D12] overflow-hidden text-ellipsis whitespace-nowrap">{{ $d->name }}</div>
                            <div class="text-[10px] text-[#A4ABB8] overflow-hidden text-ellipsis whitespace-nowrap">{{ $d->email }}</div>
                        </div>
                    </div>
                    @endforeach
                @else
                <span class="mp-badge error sm">Belum ada dosen pengampu</span>
                @endif
            </div>
        </div>

        {{-- Koordinator --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
            <div class="px-4 py-3 border-b border-[#F3F4F6] bg-[#FAFAFA]">
                <div class="text-[11px] font-bold text-[#0D0D12] uppercase tracking-[0.07em]">Koordinator Praktikum</div>
            </div>
            <div class="p-4">
                @if($praktikum->koordinator)
                @php
                    $k  = $praktikum->koordinator;
                    $kp = explode(' ', $k->name);
                    $ki = strtoupper(substr($kp[0],0,1).substr($kp[1]??$kp[0],0,1));
                    $avColors = ['bg-[#E0F2FE] text-[#0369A1]', 'bg-[#FEF9C3] text-[#A16207]', 'bg-[#DCFCE7] text-[#15803D]', 'bg-[#F3E8FF] text-[#7E22CE]', 'bg-[#FFE4E6] text-[#BE123C]'];
                    $avColorK = $avColors[crc32($k->email ?? '') % count($avColors)];
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-[12px] flex-shrink-0 {{ $avColorK }}">{{ $ki }}</div>
                    <div class="min-w-0">
                        <div class="text-[12px] font-semibold text-[#0D0D12] overflow-hidden text-ellipsis whitespace-nowrap">{{ $k->name }}</div>
                        <div class="text-[10px] text-[#A4ABB8] overflow-hidden text-ellipsis whitespace-nowrap">{{ $k->email }}</div>
                    </div>
                </div>
                @else
                <span class="mp-badge error sm">Belum ditunjuk</span>
                @endif
            </div>
        </div>

    </div>
    {{-- /Sidebar --}}

    {{-- ══ KONTEN KANAN: Tab Praktikan & Asisten Praktikum ═══════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col h-full min-h-0"
         x-data="{ tab: '{{ request()->has('tab') ? request('tab') : 'praktikan' }}' }">

        {{-- Tab Bar --}}
        <div class="flex items-center gap-3 mb-4 flex-shrink-0 w-fit">
            <button @click="tab='praktikan'"
                    :class="tab==='praktikan' ? 'border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]' : 'border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5'"
                    class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Praktikan
                <span :class="tab==='praktikan' ? 'bg-[#0B266E] text-white' : 'bg-[#DFE1E7] text-[#666D80]'"
                      class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-[6px] ml-1">{{ $praktikans->total() }}</span>
            </button>
            <button @click="tab='asprak'"
                    :class="tab==='asprak' ? 'border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]' : 'border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5'"
                    class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Asisten Praktikum
                <span :class="tab==='asprak' ? 'bg-[#0B266E] text-white' : 'bg-[#DFE1E7] text-[#666D80]'"
                      class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-[6px] ml-1">{{ $aspraks->count() }}</span>
            </button>
        </div>

        {{-- ── TAB: PRAKTIKAN ───────────────────────────────────────────── --}}
        <div x-show="tab==='praktikan'" class="flex flex-col flex-1 min-h-0 h-full" x-cloak>
            
            {{-- Tabel Praktikan --}}
            <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1 min-h-0 mb-0.5">
                
                {{-- Toolbar Table --}}
                <div class="px-4 py-3 border-b border-[#DFE1E7] bg-white flex items-center justify-between gap-3 flex-shrink-0">
                    <form method="GET" class="flex-1 flex gap-2 max-w-[320px]" action="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}">
                        <input type="hidden" name="tab" value="praktikan">
                        <div class="relative flex-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round"
                                 class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                   placeholder="Cari nama atau NIM..."
                                   class="mp-input w-full h-[36px] text-[13px]" style="padding-left: 34px;">
                        </div>
                        <button type="submit" class="mp-btn secondary md">Cari</button>
                        @if(!empty($search))
                        <a href="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}" class="mp-btn secondary md px-3">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </a>
                        @endif
                    </form>
                    <button onclick="document.getElementById('modalImport').classList.remove('hidden')"
                            class="mp-btn primary md flex items-center gap-1.5 flex-shrink-0 h-[36px]">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                        Import Praktikan
                    </button>
                </div>

                <div class="overflow-y-auto flex-1">
                    <table class="w-full border-collapse min-w-[500px]">
                        <thead class="sticky top-0 z-10 bg-[#FAFAFA] shadow-[0_1px_0_#DFE1E7]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-[#666D80] uppercase tracking-wider w-[48px]">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-[#666D80] uppercase tracking-wider">Nama Praktikan</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-[#666D80] uppercase tracking-wider w-[140px]">NIM</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-[#666D80] uppercase tracking-wider w-[100px]">Angkatan</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-[#666D80] uppercase tracking-wider w-[90px]">Status</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-[#666D80] uppercase tracking-wider w-[70px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @forelse($praktikans as $i => $p)
                            @php
                                $np  = explode(' ', $p->user?->name ?? 'PR');
                                $ini = strtoupper(substr($np[0]??'P',0,1).substr($np[1]??$np[0]??'R',0,1));
                                $avc = ['bg-[#E0F2FE] text-[#0369A1]', 'bg-[#FEF9C3] text-[#A16207]', 'bg-[#DCFCE7] text-[#15803D]', 'bg-[#F3E8FF] text-[#7E22CE]', 'bg-[#FFE4E6] text-[#BE123C]'];
                                $av  = $avc[crc32($p->user?->email??'')%count($avc)];
                                $st  = $p->user ? \App\Models\Student::where('user_id',$p->user->id)->first() : null;
                                $nim = $st?->student_number ?? '—';
                                $ang = $st?->cohort_year ?? '—';
                            @endphp
                            <tr class="hover:bg-[#F6F8FA] transition-colors">
                                <td class="px-4 py-3 text-[12px] text-[#A4ABB8] text-center">{{ $praktikans->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] flex-shrink-0 {{ $av }}">{{ $ini }}</div>
                                        <div class="min-w-0">
                                            <div class="text-[13px] font-bold text-[#0D0D12] whitespace-nowrap overflow-hidden text-ellipsis max-w-[250px]">{{ $p->user?->name ?? '—' }}</div>
                                            <div class="text-[11px] text-[#666D80] whitespace-nowrap overflow-hidden text-ellipsis max-w-[250px]">{{ $p->user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-[12px] font-mono text-[#353849]">{{ $nim }}</td>
                                <td class="px-4 py-3 text-center text-[12px] font-bold text-[#353849]">{{ $ang }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.destroy', $p->id) }}" onsubmit="return confirm('Hapus praktikan ini?')" class="inline-flex">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg border border-[#FADAE1] bg-[#FFF5F5] text-[#DF1C41] hover:bg-[#FEE2E2] flex items-center justify-center transition-colors" title="Hapus Praktikan">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <div class="w-12 h-12 rounded-full bg-[#F6F8FA] flex items-center justify-center mx-auto mb-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/></svg>
                                    </div>
                                    <div class="text-[13px] font-semibold text-[#666D80]">
                                        @if(!empty($search)) Tidak ditemukan hasil untuk "{{ $search }}"
                                        @else Belum ada praktikan terdaftar @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($praktikans->hasPages())
                <div class="px-4 py-3 border-t border-[#DFE1E7] bg-[#FAFAFA] flex-shrink-0">
                    {{ $praktikans->links() }}
                </div>
                @endif
            </div>
        </div>
        {{-- /Tab Praktikan --}}

        {{-- ── TAB: ASISTEN PRAKTIKUM ──────────────────────────────────────────────── --}}
        <div x-show="tab==='asprak'" class="flex flex-col flex-1 min-h-0 h-full" x-cloak>

            {{-- Tabel Asisten Praktikum --}}
            <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1 min-h-0 mb-0.5">
                
                {{-- Toolbar Table --}}
                <div class="px-4 py-3 border-b border-[#DFE1E7] bg-white flex items-center justify-between gap-3 flex-shrink-0">
                    <div>
                        <div class="text-[14px] font-bold text-[#0D0D12]">Daftar Asisten Praktikum</div>
                        <div class="text-[12px] text-[#666D80] mt-0.5">{{ $aspraks->count() }} asisten praktikum terdaftar di praktikum ini</div>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1">
                    <table class="w-full border-collapse min-w-[500px]">
                        <thead class="sticky top-0 z-10 bg-[#FAFAFA] shadow-[0_1px_0_#DFE1E7]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-[#666D80] uppercase tracking-wider w-[48px]">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-[#666D80] uppercase tracking-wider">Nama Asisten Praktikum</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-[#666D80] uppercase tracking-wider w-[140px]">NIM</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-[#666D80] uppercase tracking-wider">Modul</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @forelse($aspraks as $i => $asprak)
                            @php
                                $user      = $asprak->user;
                                $np        = explode(' ', $user?->name ?? 'AS');
                                $ini       = strtoupper(substr($np[0]??'A',0,1).substr($np[1]??$np[0]??'S',0,1));
                                $avc       = ['bg-[#E0F2FE] text-[#0369A1]', 'bg-[#FEF9C3] text-[#A16207]', 'bg-[#DCFCE7] text-[#15803D]', 'bg-[#F3E8FF] text-[#7E22CE]', 'bg-[#FFE4E6] text-[#BE123C]'];
                                $av        = $avc[crc32($user?->email??'')%count($avc)];
                                $st        = $user ? \App\Models\Student::where('user_id',$user->id)->first() : null;
                                $nim       = $st?->student_number ?? '—';
                            @endphp
                            <tr class="hover:bg-[#F6F8FA] transition-colors">
                                <td class="px-4 py-3 text-[12px] text-[#A4ABB8] text-center">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] flex-shrink-0 {{ $av }}">{{ $ini }}</div>
                                        <div class="min-w-0">
                                            <div class="text-[13px] font-bold text-[#0D0D12] whitespace-nowrap overflow-hidden text-ellipsis max-w-[220px]">{{ $user?->name ?? '—' }}</div>
                                            <div class="text-[11px] text-[#666D80] whitespace-nowrap overflow-hidden text-ellipsis max-w-[220px]">{{ $user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-[12px] font-mono text-[#353849]">{{ $nim }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $moduls = $asprak->modulAsprak?->map(fn($ma) => $ma->modul)->filter() ?? collect();
                                        @endphp
                                        @forelse($moduls as $m)
                                        <span class="mp-badge primary sm">{{ $m->judul ?? ('Modul ' . $m->urutan) }}</span>
                                        @empty
                                        <span class="text-[11px] text-[#A4ABB8] italic">Belum ada</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-16 text-center">
                                    <div class="w-12 h-12 rounded-full bg-[#F6F8FA] flex items-center justify-center mx-auto mb-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div class="text-[13px] font-semibold text-[#666D80]">Belum ada asisten praktikum</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- /Tab Asisten Praktikum --}}
    </div>
</div>

{{-- Modal Import Praktikan --}}
<div id="modalImport" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300 hidden">
    <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[16px] text-[#0D0D12]">Import Praktikan</div>
            <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-[#F6F8FA] border-none bg-transparent cursor-pointer text-[#666D80] transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
            <div class="mb-5">
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">File Excel (.xlsx) / CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-[#DFE1E7] rounded-[12px] p-6 text-center hover:bg-[#F6F8FA] transition-colors cursor-pointer relative"
                     onclick="document.getElementById('fileExcel').click()">
                    <input type="file" name="file" id="fileExcel" accept=".csv,.xlsx,.xls" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mx-auto mb-2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                    <div class="text-[13px] font-semibold text-[#0B266E]">Pilih file atau drag kemari</div>
                    <div class="text-[11px] text-[#A4ABB8] mt-1">Maksimal 2MB</div>
                </div>
                <div class="mt-3 flex items-start gap-2 bg-[#F6F8FA] p-3 rounded-[8px]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" class="mt-0.5 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <div class="text-[11px] text-[#353849] leading-[1.6]">
                        Format file harus berisi kolom <strong>NIM</strong> dan <strong>Nama</strong>, atau format mentah SSO Kampus. Akun mahasiswa akan dibuatkan otomatis jika belum terdaftar.
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-end pt-2 border-t border-[#DFE1E7] mt-2">
                <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
                    Import
                </button>
            </div>
        </form>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
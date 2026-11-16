<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" pageTitle="Detail Praktikum">
<style>
.mp-admin-detail{color:#353849;min-width:0}
.mp-admin-detail .mp-page-header{position:relative;display:flex;justify-content:space-between;gap:16px;align-items:flex-start;padding-bottom:24px;margin-bottom:24px}
.mp-admin-detail .mp-page-header::after{content:"";position:absolute;bottom:0;left:-24px;right:-24px;border-bottom:1px solid #DFE1E7}
.mp-detail-layout{display:flex;flex-direction:column;gap:24px;min-width:0}
.mp-detail-summary{display:grid;grid-template-columns:1.15fr 1fr 1fr;gap:16px;min-width:0}
.mp-detail-summary>div{border:1px solid #DFE1E7;border-radius:12px;background:#fff;overflow:hidden;box-shadow:0 1px 3px #0d0d1208}
.mp-detail-summary>div>div:first-child{padding:16px 20px;background:#F8F9FC;border-bottom:1px solid #DFE1E7}
.mp-detail-summary>div>div:first-child>div{font-size:14px;font-weight:600;color:#353849;text-transform:none;letter-spacing:normal}
.mp-detail-summary>div>div:nth-child(2){padding:20px;display:flex;flex-direction:column;gap:16px}
.mp-detail-summary>div:first-child>div:nth-child(2)>div{display:flex;justify-content:space-between;gap:20px;align-items:flex-start}
.mp-detail-summary>div:first-child span{font-size:13px;line-height:20px}
.mp-detail-summary>div:first-child span:first-child:not(.mp-badge){color:#666D80;flex-shrink:0}
.mp-detail-summary>div:first-child span:last-child:not(.mp-badge){color:#353849;font-weight:500;text-align:right;overflow-wrap:anywhere}
.mp-detail-members{width:100%;min-width:0;display:flex;flex-direction:column}
.mp-detail-tabs{display:flex;gap:24px;border-bottom:1px solid #DFE1E7;margin-bottom:20px;width:100%}
.mp-detail-tabs>button{display:flex;align-items:center;gap:8px;padding:14px 4px;background:none!important;border:0;border-bottom:2px solid transparent;border-radius:0;font-size:13px;font-weight:600;cursor:pointer;color:#666D80}
.mp-detail-tabs>button[aria-selected="true"]{color:#0B266E;border-bottom-color:#0B266E}
.mp-detail-tabs>button>span{font-size:11px;padding:2px 7px;border-radius:6px;background:#F1F3F9;color:#666D80}
.mp-detail-members [x-show]{width:100%;min-width:0}
.mp-detail-members [x-show]>div{width:100%;border-radius:12px;overflow:hidden}
.mp-detail-members table{width:100%;border-collapse:collapse;min-width:700px}
.mp-detail-members thead{background:#F8F9FC}
.mp-detail-members th{font-size:12px;font-weight:600;color:#666D80;letter-spacing:0;padding:16px 20px;white-space:nowrap}
.mp-detail-members td{padding:16px 20px;font-size:13px;color:#353849;border-bottom:1px solid #F1F3F9}
.mp-detail-members .overflow-y-auto{overflow:auto;min-height:0}
.mp-detail-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;padding:16px 20px;border-bottom:1px solid #DFE1E7}
.mp-detail-toolbar form{display:flex;gap:8px;flex:1;max-width:360px;min-width:200px}
.mp-detail-summary .mp-settings-email,.mp-detail-members .mp-settings-email{font-size:12px;color:#666D80;overflow-wrap:anywhere}
@media(max-width:1000px){.mp-detail-summary{grid-template-columns:1fr 1fr}.mp-detail-summary>div:first-child{grid-column:1/-1}}
@media(max-width:640px){.mp-detail-summary{grid-template-columns:1fr}.mp-admin-detail .mp-page-header::after{left:-16px;right:-16px}.mp-detail-tabs{gap:12px}.mp-detail-toolbar form{max-width:none;width:100%}}
</style>
<div class="mp-admin-detail">

{{-- ════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════ --}}
<div class="mp-page-header flex-shrink-0">
    <div style="min-width:0;flex:1;">
        <h1 class="mp-page-title" style="margin:0;color:#353849;overflow-wrap:anywhere;">{{ $praktikum->nama }}</h1>
        <p class="mp-page-sub">Informasi praktikum dan daftar anggota.</p>
    </div>
    <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn secondary md">Kembali</a>
</div>

{{-- ════════════════════════════════════════════
     2-COLUMN LAYOUT
════════════════════════════════════════════ --}}
<div class="mp-detail-layout">

    {{-- ══ SIDEBAR KIRI ═══════════════════════════════════════════════════ --}}
    <div class="mp-detail-summary">

        {{-- Info Praktikum --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
            <div class="px-4 py-3 border-b border-[#F3F4F6] bg-[#FAFAFA]">
                <div class="text-[11px] font-bold text-[#0D0D12] uppercase tracking-[0.07em]">Info Praktikum</div>
            </div>
            <div class="p-4 flex flex-col gap-4">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Status</span>
                    <span class="mp-badge {{ $praktikum->status === 'aktif' ? 'success' : 'neutral' }} sm"><span class="dot"></span>{{ ucfirst($praktikum->status) }}</span>
                </div>
                
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Periode</span>
                    <span class="text-[12px] font-semibold text-[#0D0D12] text-right">Tahun Ajaran {{ $praktikum->tahun_ajaran }} / Semester {{ ucfirst($praktikum->semester) }}</span>
                </div>

                @if($praktikum->matkul)
                <div class="flex items-start justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Mata Kuliah</span>
                    <div class="flex flex-col items-end gap-1 text-right min-w-0">
                        <span class="text-[12px] font-semibold text-[#0D0D12] leading-[1.3]">{{ $praktikum->matkul->nama }}</span>
                    </div>
                </div>
                @endif

                <div class="h-px bg-[#F3F4F6] -mx-4"></div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Dibuat</span>
                    <span class="text-[11px] font-medium text-[#0D0D12]">{{ $praktikum->created_at?->locale('id')->isoFormat('D MMM YYYY') }}</span>
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
                    @php $avColors = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48']; @endphp
                    @foreach($praktikum->dosens as $d)
                    @php
                        $dp = explode(' ', $d->name);
                        $di = strtoupper(substr($dp[0],0,1).substr($dp[1]??$dp[0],0,1));
                        $avColor = $avColors[crc32($d->email ?? '') % count($avColors)];
                    @endphp
                    <x-eoffice::manajemen-praktikum.ui.staff-person :user="$d" :show-email="true" :wrap="true" />
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
                    $avColors = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48'];
                    $avColorK = $avColors[crc32($k->email ?? '') % count($avColors)];
                @endphp
                <x-eoffice::manajemen-praktikum.ui.staff-person :user="$k" :show-email="true" :wrap="true" />
                @else
                <span class="mp-badge error sm" style="align-self:flex-start;width:max-content;max-width:100%;font-size:11px;line-height:16px;padding:2px 8px;">Belum ditunjuk</span>
                @endif
            </div>
        </div>

    </div>
    {{-- /Sidebar --}}

    {{-- ══ KONTEN KANAN: Tab Praktikan & Asisten Praktikum ═══════════════════════════ --}}
    <div class="mp-detail-members"
         x-data="{ tab: @js(request('tab') === 'asprak' ? 'asprak' : 'praktikan') }">

        {{-- Tab Bar --}}
        <div class="mp-detail-tabs" role="tablist" aria-label="Anggota praktikum">
            <button @click="tab='praktikan'"
                    role="tab" :aria-selected="tab==='praktikan'"
                    :class="tab==='praktikan' ? 'border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]' : 'border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5'"
                    class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Praktikan
                <span :class="tab==='praktikan' ? 'bg-[#0B266E] text-white' : 'bg-[#DFE1E7] text-[#666D80]'"
                      class="text-[10px] font-extrabold px-2 py-0.5 rounded-full ml-1">{{ $praktikans->total() }}</span>
            </button>
            <button @click="tab='asprak'"
                    role="tab" :aria-selected="tab==='asprak'"
                    :class="tab==='asprak' ? 'border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]' : 'border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5'"
                    class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                Asisten Praktikum
                <span :class="tab==='asprak' ? 'bg-[#0B266E] text-white' : 'bg-[#DFE1E7] text-[#666D80]'"
                      class="text-[10px] font-extrabold px-2 py-0.5 rounded-full ml-1">{{ $aspraks->total() }}</span>
            </button>
        </div>

        {{-- ── TAB: PRAKTIKAN ───────────────────────────────────────────── --}}
        <div x-show="tab==='praktikan'" class="flex flex-col flex-1 min-h-0 h-full" x-cloak>
            @include('eoffice::manajemen-praktikum.admin.partials.participant-import-result')
            
            {{-- Tabel Praktikan --}}
            <div class="bg-white border border-[#DFE1E7] rounded-[16px] shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1 min-h-0 mb-0.5">
                
                {{-- Toolbar Table --}}
                <div class="mp-detail-toolbar">
                    <form method="GET" id="filter_praktikan_form" class="flex-1 flex gap-2 max-w-[320px]" action="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}">
                        <input type="hidden" name="tab" value="praktikan">
                        <div class="relative flex-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round"
                                 class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                   placeholder="Cari nama atau NIM..."
                                   class="mp-input w-full h-[38px] text-[13px]" style="padding-left: 34px;">
                        </div>
                        @if(!empty($search))
                        <a href="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}" class="mp-btn secondary md px-3">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </a>
                        @endif
                    </form>
                    <div class="flex items-center gap-2">
                        @if($canAddParticipants ?? false)
                        <button type="button" class="mp-btn primary md" @click="$dispatch('open-add-participant')">
                            <x-eoffice::manajemen-praktikum.ui.icon name="plus" /> Tambah Mahasiswa
                        </button>
                        @endif
                        <button class="mp-btn secondary md flex items-center gap-1.5 flex-shrink-0 h-[38px]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            Ekspor
                        </button>
                        <button @disabled(!($canAddParticipants ?? false)) @click="$dispatch('open-import-participant')"
                                class="mp-btn primary md flex items-center gap-1.5 flex-shrink-0 h-[38px]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                            Impor Praktikan
                        </button>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1" style="scrollbar-gutter:stable;">
                    <table class="w-full border-collapse min-w-[500px]" style="table-layout:fixed;min-width:800px;">
                        <colgroup><col style="width:64px"><col><col style="width:190px"><col style="width:100px"><col style="width:90px"><col style="width:70px"></colgroup>
                        <thead class="sticky top-0 z-10 bg-[#FAFAFA] border-b border-[#DFE1E7]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[48px]">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em]">Nama Praktikan</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[140px]">NIM</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[100px]">Angkatan</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[90px]">Status</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[70px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @forelse($praktikans as $i => $p)
                            @php
                                $np  = explode(' ', $p->user?->name ?? 'PR');
                                $ini = strtoupper(substr($np[0]??'P',0,1).substr($np[1]??$np[0]??'R',0,1));
                                $avc = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48'];
                                $av  = $avc[crc32($p->user?->email??'')%count($avc)];
                                $st  = $p->user ? \App\Models\Student::where('user_id',$p->user->id)->first() : null;
                                $nim = $st?->student_number ?? '—';
                                $ang = $st?->cohort_year ?? '—';
                            @endphp
                            <tr class="hover:bg-[#F6F8FA] transition-colors">
                                <td class="px-4 py-3 text-[12px] text-[#A4ABB8] text-left">{{ $praktikans->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    @if($p->user)
                                        <x-eoffice::manajemen-praktikum.ui.staff-person :user="$p->user" :show-email="true" :wrap="true" />
                                    @else
                                        <span style="color:#666D80;">Akun tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#0D0D12]" style="font-family: 'Inter', sans-serif;">{{ $nim }}</td>
                                <td class="px-4 py-3 text-center text-[12px] font-normal text-[#0D0D12]">{{ $ang }}</td>
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
                @if(isset($praktikans) && $praktikans instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$praktikans" :options="[10, 20, 30]" />
                @endif
            </div>
        </div>
        {{-- /Tab Praktikan --}}

        {{-- ── TAB: ASISTEN PRAKTIKUM ──────────────────────────────────────────────── --}}
        <div x-show="tab==='asprak'" class="flex flex-col flex-1 min-h-0 h-full" x-cloak>
            <form method="GET" id="filter_asprak_form" class="hidden" action="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}">
                <input type="hidden" name="tab" value="asprak">
            </form>

            {{-- Tabel Asisten Praktikum --}}
            <div class="bg-white border border-[#DFE1E7] rounded-[16px] shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1 min-h-0 mb-0.5">
                
                {{-- Toolbar Table --}}
                <div class="mp-detail-toolbar">
                    <div>
                        <div class="text-[14px] font-bold text-[#0D0D12]">Daftar Asisten Praktikum</div>
                        <div class="text-[12px] text-[#666D80] mt-0.5">{{ $aspraks->count() }} asisten praktikum terdaftar di praktikum ini</div>
                    </div>
                    <button class="mp-btn secondary md flex items-center gap-1.5 flex-shrink-0 h-[38px]">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        Export
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 min-h-[400px]">
                    <table class="w-full border-collapse min-w-[500px]">
                        <thead class="sticky top-0 z-10 bg-[#FAFAFA] border-b border-[#DFE1E7]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[48px]">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em]">Nama Asisten</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[140px]">NIM</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em]">Modul</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @forelse($aspraks as $i => $asprak)
                            @php
                                $user      = $asprak->user;
                                $np        = explode(' ', $user?->name ?? 'AS');
                                $ini       = strtoupper(substr($np[0]??'A',0,1).substr($np[1]??$np[0]??'S',0,1));
                                $avc       = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48'];
                                $av        = $avc[crc32($user?->email??'')%count($avc)];
                                $st        = $user ? \App\Models\Student::where('user_id',$user->id)->first() : null;
                                $nim       = $st?->student_number ?? '—';
                            @endphp
                            <tr class="hover:bg-[#F6F8FA] transition-colors">
                                <td class="px-4 py-3 text-[12px] text-[#A4ABB8] text-left">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    @if($user)
                                        <x-eoffice::manajemen-praktikum.ui.staff-person :user="$user" :show-email="true" :wrap="true" />
                                    @else
                                        <span style="color:#666D80;">Akun tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#353849]" style="font-family: 'Inter', sans-serif;">{{ $nim }}</td>
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

                @if(isset($aspraks) && $aspraks instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$aspraks" per-page-name="per_page_asprak" :options="[10, 20, 30]" />
                @endif
            </div>
        </div>
        {{-- /Tab Asisten Praktikum --}}
    </div>
</div>

@if($canAddParticipants ?? false)
<div id="modalAddParticipant" x-data="{open:false}" x-show="open" x-cloak @open-add-participant.window="open=true" @click.self="open=false" @keydown.escape.window="open=false" x-transition.opacity.duration.200ms style="display:none;position:fixed;inset:0;z-index:100;background:#0D0D1280;padding:20px;overflow:auto;" role="dialog" aria-modal="true" aria-labelledby="addParticipantTitle">
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="position:relative;top:50%;transform:translateY(-50%);width:100%;max-width:560px;margin:auto;background:#fff;border-radius:16px;box-shadow:0 20px 60px #0003;">
        <h2 id="addParticipantTitle" style="font-size:18px;font-weight:600;color:#353849;padding:24px;border-bottom:1px solid #DFE1E7;margin:0;">Tambah Mahasiswa</h2>
        <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.store') }}">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
            <div style="padding:24px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:8px;">Mahasiswa <span style="color:#DF1C41;">*</span></label>
                <x-eoffice::manajemen-praktikum.ui.select name="user_ids[]" :options="$studentOptions ?? []" placeholder="Pilih mahasiswa..." :searchable="true" searchPlaceholder="Cari nama, NIM, atau email..." />
                <p style="font-size:12px;line-height:18px;color:#666D80;margin:12px 0 0;">Hanya mahasiswa yang belum terdaftar ditampilkan. Kelompok dan shift diatur oleh koordinator setelah mahasiswa ditambahkan.</p>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:12px;padding:20px 24px;border-top:1px solid #DFE1E7;background:#F8F9FC;border-radius:0 0 16px 16px;">
                <button type="button" class="mp-btn secondary md" @click="open=false">Batal</button>
                <button type="submit" class="mp-btn primary md">Tambah</button>
            </div>
        </form>
    </div>
</div>
@else
<p style="font-size:12px;color:#666D80;margin-top:12px;">Penambahan mahasiswa dan impor hanya tersedia pada praktikum aktif yang belum memiliki catatan aktivitas.</p>
@endif
{{-- Modal Import Praktikan --}}
<template x-teleport="body">
<div id="modalImport" x-data="{open:false}" x-show="open" x-cloak @open-import-participant.window="open=true" @click.self="open=false" @keydown.escape.window="open=false" x-transition.opacity.duration.200ms class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display:none;padding:20px;overflow-y:auto;z-index:100;" role="dialog" aria-modal="true" aria-labelledby="importParticipantTitle">
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-3 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-3 scale-95" class="bg-white rounded-[16px] shadow-2xl w-full max-w-md mx-4 overflow-hidden" style="max-height:calc(100dvh - 40px);overflow-y:auto;">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DFE1E7]">
            <div id="importParticipantTitle" style="font-size:18px;font-weight:600;color:#353849;">Impor Praktikan</div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.store') }}" enctype="multipart/form-data" class="p-6" x-data="{submitting:false}" @submit="if(submitting){$event.preventDefault();return;}submitting=true">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
            <div class="mb-5" x-data="{ 
                isDragging: false, 
                fileName: '',
                handleDrop(e) {
                    this.isDragging = false;
                    if(e.dataTransfer.files.length > 0) {
                        this.$refs.fileInput.files = e.dataTransfer.files;
                        this.fileName = e.dataTransfer.files[0].name;
                    }
                },
                handleChange(e) {
                    if(e.target.files.length > 0) {
                        this.fileName = e.target.files[0].name;
                    } else {
                        this.fileName = '';
                    }
                }
            }">
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">File Excel (.xlsx) / CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed rounded-[12px] p-6 text-center transition-colors cursor-pointer"
                     :class="isDragging ? 'border-[#0B266E] bg-[#EEF1FA]' : 'border-[#DFE1E7] hover:bg-[#F6F8FA]'"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)"
                     role="button" tabindex="0" @keydown.enter.prevent="$refs.fileInput.click()" @keydown.space.prevent="$refs.fileInput.click()"
                     @click="if($event.target !== $refs.fileInput) $refs.fileInput.click()">
                     
                    <input type="file" name="file" id="fileExcel" x-ref="fileInput" @click.stop @change="handleChange($event)" accept=".csv,.xlsx,.xls" required class="hidden">
                    
                    <svg x-show="!fileName" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mx-auto mb-2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                    <svg x-show="fileName" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5" stroke-linecap="round" class="mx-auto mb-2" style="display:none;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 12 18 16 13"></polyline></svg>

                    <div x-show="!fileName" class="text-[13px] font-semibold text-[#0B266E]">Pilih file atau drag kemari</div>
                    <div x-show="!fileName" class="text-[11px] text-[#A4ABB8] mt-1">Maksimal 5MB</div>
                    
                    <div x-show="fileName" class="text-[13px] font-semibold text-[#0D0D12]" style="display:none;" x-text="fileName"></div>
                    <div x-show="fileName" class="text-[11px] text-[#059669] mt-1" style="display:none;">File siap diunggah</div>
                </div>
                <div class="mt-3 flex items-start gap-2 bg-[#F6F8FA] p-3 rounded-[8px]">
                    <span style="display:inline-flex;flex-shrink:0;color:#666D80;margin-top:2px;" aria-hidden="true"><x-eoffice::manajemen-praktikum.ui.icon name="alert-circle" :size="16" /></span>
                    <div class="text-[11px] text-[#353849] leading-[1.6]">
                        Gunakan file Excel atau CSV dengan kolom <strong>Nama</strong> dan <strong>NIM</strong>.
                    </div>
                </div>
            </div>
            <div class="mp-modal-footer mp-modal-footer-bleed flex gap-3 justify-end pt-2 border-t border-[#DFE1E7] mt-2" style="--mp-modal-footer-radius:16px;">
                <button type="button" @click="open=false" class="mp-btn secondary md px-5">Batal</button>
                <button type="submit" class="mp-btn primary md px-5" :disabled="submitting" x-text="submitting ? 'Memproses...' : 'Impor'">Impor</button>
            </div>
        </form>
    </div>
</div>
</template>

</div>
</x-eoffice::manajemen-praktikum.layout>

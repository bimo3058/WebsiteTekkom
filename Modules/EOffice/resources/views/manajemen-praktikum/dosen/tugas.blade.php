<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Tugas">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Tugas Praktikum</h1>
            <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">Pantau tugas dan progres pengumpulan mahasiswa · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

<div class="mp-alert info flex-shrink-0">
    Halaman ini bersifat <strong>read-only</strong>. Pembuatan dan penilaian tugas dilakukan oleh Asisten Praktikum.
</div>

{{-- Pilih Praktikum --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:14px 18px;">
        @if($praktikumList->isEmpty())
        <div class="mp-alert warning">Anda belum mengampu praktikum aktif manapun.</div>
        @else
        <form method="GET" class="flex gap-2 flex-wrap">
            <select name="praktikum_id" class="mp-input mp-select" style="max-width:320px;"
                    onchange="this.form.submit()">
                @foreach($praktikumList as $p)
                <option value="{{ $p->id }}" {{ ($praktikum?->id == $p->id) ? 'selected' : '' }}>
                    {{ $p->nama }}

                    · {{ $p->semester }} {{ $p->tahun_ajaran }}
                </option>
                @endforeach
            </select>
        </form>
        @endif
    </div>
</div>

@if($praktikum)

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Tugas — {{ $praktikum->nama }}</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $tugas->total() }} tugas</span>
</div>

@if($tugas->isEmpty())
<div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Tugas</div>
        <div style="font-size:12px;color:#666D80;">Belum ada tugas yang dibuat oleh asprak pada praktikum ini.</div>
    </div>
</div>
@else

<div class="flex flex-col gap-3">
    @foreach($tugas as $t)
    @php
        $dl    = $t->deadline ? \Carbon\Carbon::parse($t->deadline) : null;
        $lewat = $dl && now()->gt($dl);
        $total = $t->pengumpulan_count ?? 0;
    @endphp
    <div class="mp-card flex-shrink-0"
         onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
         onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">
        <div style="padding:18px 20px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                {{-- Info Kiri --}}
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                        <div style="font-size:14px;font-weight:700;color:#0D0D12;">{{ $t->judul }}</div>
                        @if($lewat)
                        <span class="mp-badge neutral sm">Berakhir</span>
                        @else
                        <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                        @endif
                    </div>
                    <div style="font-size:12px;color:#666D80;display:flex;gap:12px;flex-wrap:wrap;">
                        <span>Modul: <strong style="color:#353849;">{{ $t->modul?->nama ?? '—' }}</strong></span>
                        @if($dl)
                        <span>Deadline: <strong style="color:{{ $lewat ? '#666D80' : '#353849' }};">{{ $dl->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</strong></span>
                        @else
                        <span style="color:#A4ABB8;">Tanpa deadline</span>
                        @endif
                    </div>
                    @if($t->deskripsi)
                    <div style="font-size:12px;color:#666D80;margin-top:6px;line-height:1.5;">{{ Str::limit($t->deskripsi, 120) }}</div>
                    @endif
                </div>

                {{-- Stats Kanan --}}
                <div style="display:flex;align-items:center;gap:20px;flex-shrink:0;">
                    <div style="text-align:center;">
                        <div style="font-size:20px;font-weight:700;color:#0D0D12;">{{ $total }}</div>
                        <div style="font-size:10px;color:#666D80;margin-top:2px;">Dikumpulkan</div>
                    </div>
                    <a href="{{ route('eoffice.manprak.dosen.tugas.pengumpulan', $t->id) }}"
                       class="mp-btn secondary sm" style="text-decoration:none;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Lihat Pengumpulan
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($tugas->hasPages())
<div class="flex-shrink-0" style="padding:8px 0;">{{ $tugas->links() }}</div>
@endif

@endif {{-- end if tugas not empty --}}
@endif {{-- end if praktikum --}}

</x-eoffice::manajemen-praktikum.layout>
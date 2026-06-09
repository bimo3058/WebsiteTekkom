<x-eoffice::manajemen-praktikum.layout pageTitle="Data Praktikan">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Data Praktikan</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
@else

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Import & Daftar Praktikan</span>
    <span class="sec-rule"></span>
</div>

{{-- Import Card --}}
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Import Data Praktikan</div>
    <form method="POST" action="{{ route('eoffice.manprak.koor.praktikan.import') }}" enctype="multipart/form-data" class="flex items-end gap-3 flex-wrap">
        @csrf
        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
        <div>
            <label class="block text-[12px] font-semibold text-[#353849] mb-1">Import CSV/XLSX</label>
            <input type="file" name="file" class="mp-input">
        </div>
        <button class="mp-btn primary md">Import Praktikan</button>
    </form>
</div>

{{-- Daftar Praktikan Card --}}
<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikan Terdaftar</span>
        <div class="right">
            <form method="GET">
                <input name="search" value="{{ $search }}" placeholder="Cari nama/email..." class="mp-input" style="width:200px;">
            </form>
        </div>
    </div>
    <table class="mp-table">
        <thead>
            <tr style="background:#F9FAFB;">
                <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Tanggal Masuk</th>
            </tr>
        </thead>
        <tbody>
        @forelse($praktikans as $p)
            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                <td style="padding:12px 16px;">
                    <div class="flex items-center gap-[10px]">
                        <div class="mp-av yellow">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                        <div>
                            <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '-' }}</div>
                            <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding:12px 16px;">
                    <span class="mp-badge success sm"><span class="dot"></span>{{ $p->status ?? 'terdaftar' }}</span>
                </td>
                <td style="padding:12px 16px;font-size:12px;color:#666D80;">{{ $p->created_at?->format('d M Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">
                    <div style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan.</div>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    @if($praktikans->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $praktikans->links() }}</div>
    @endif
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>

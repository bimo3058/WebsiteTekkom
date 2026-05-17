<x-eoffice::manajemen-praktikum.layout pageTitle="Data Praktikan">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Data Praktikan</h1>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
@else
<div class="mp-card flex-shrink-0" style="padding:20px;">
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

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikan Terdaftar</span>
        <form method="GET">
            <input name="search" value="{{ $search }}" placeholder="Cari nama/email..." class="mp-input">
        </form>
    </div>
    <table class="w-full" style="font-size:13px;">
        <thead style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
            <tr>
                <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                <th class="mp-th text-left" style="padding:10px 16px;">Tanggal Masuk</th>
            </tr>
        </thead>
        <tbody>
        @forelse($praktikans as $p)
            <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                <td style="padding:12px 16px;">
                    <div style="font-weight:600;color:var(--c-fg);">{{ $p->user?->name ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);">{{ $p->user?->email }}</div>
                </td>
                <td style="padding:12px 16px;">
                    <span class="mp-badge success sm">{{ $p->status ?? 'terdaftar' }}</span>
                </td>
                <td style="padding:12px 16px;font-size:12px;color:var(--c-fg-muted);">{{ $p->created_at?->format('d M Y H:i') }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada praktikan.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($praktikans->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--c-border);flex-shrink:0;">{{ $praktikans->links() }}</div>
    @endif
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>

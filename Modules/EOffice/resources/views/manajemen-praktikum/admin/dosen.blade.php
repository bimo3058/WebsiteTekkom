<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Dosen Pengampu">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Dosen Pengampu</h1>
        <p class="mp-page-sub">Data dosen diambil dari tabel lecturers</p>
    </div>
</div>

{{-- Search --}}
<form method="GET" class="flex gap-2 flex-shrink-0">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email / NIP..."
           class="mp-input flex-1">
    <button type="submit" class="mp-btn primary sm">Cari</button>
    <a href="{{ route('eoffice.manprak.admin.dosen.index') }}" class="mp-btn secondary sm">Reset</a>
</form>

{{-- Table --}}
<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-body" style="flex-shrink:0;">
        <div style="display:flex;align-items:center;padding:8px 20px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
            <div class="mp-th flex-1">Nama Dosen</div>
            <div class="mp-th" style="width:180px;">Email</div>
            <div class="mp-th" style="width:140px;">No. Pegawai</div>
            <div class="mp-th" style="width:110px;">Praktikum Diampu</div>
            <div class="mp-th" style="width:80px;">Bergabung</div>
        </div>
    </div>

    <div class="overflow-y-auto flex-1">
        @forelse($dosens as $lecturer)
        @php $user = $lecturer->user; @endphp
        <div class="mp-tr" style="display:flex;align-items:center;padding:12px 20px;">
            <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                <div class="w-[34px] h-[34px] rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#7B2FBE,#9B59B6);">
                    {{ strtoupper(substr($user?->name ?? 'D', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $user?->name ?? '—' }}</div>
                </div>
            </div>
            <div style="width:180px;font-size:12px;color:var(--c-fg-muted);" class="truncate">{{ $user?->email ?? '—' }}</div>
            <div style="width:140px;font-size:12px;font-family:monospace;color:var(--c-fg-sub);">{{ $lecturer->employee_number }}</div>
            <div style="width:110px;text-align:center;">
                @php $jumlah = \Modules\EOffice\Models\Praktikum::where('dosen_id', $lecturer->user_id)->count(); @endphp
                <span class="mp-badge primary sm">{{ $jumlah }} praktikum</span>
            </div>
            <div style="width:80px;font-size:11px;color:var(--c-fg-muted);">
                {{ $lecturer->created_at?->format('M Y') ?? '—' }}
            </div>
        </div>
        @empty
        <div style="padding:56px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada dosen terdaftar di tabel lecturers.</div>
        @endforelse
    </div>

    @if($dosens->hasPages())
    <div style="padding:12px 20px;border-top:1px solid var(--c-border);flex-shrink:0;">{{ $dosens->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>

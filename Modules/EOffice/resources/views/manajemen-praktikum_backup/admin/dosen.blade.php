<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Dosen Pengampu">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Dosen Pengampu</h1>
        <p class="mp-page-sub">Data dosen diambil dari tabel lecturers</p>
    </div>
</div>

{{-- Section title --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Pencarian Dosen</span>
    <span class="sec-rule"></span>
</div>

{{-- Search --}}
<form method="GET" class="flex gap-2 flex-shrink-0">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email / NIP..."
           class="mp-input flex-1">
    <button type="submit" class="mp-btn primary sm">Cari</button>
    <a href="{{ route('eoffice.manprak.admin.dosen.index') }}" class="mp-btn secondary sm">Reset</a>
</form>

{{-- Section title --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Data Dosen</span>
    <span class="sec-rule"></span>
</div>

{{-- Table --}}
<div class="mp-card flex-1 min-h-0">
    <div style="flex-shrink:0;">
        <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
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
                <div class="mp-av violet flex-shrink-0">
                    {{ strtoupper(substr($user?->name ?? 'D', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate">{{ $user?->name ?? '—' }}</div>
                </div>
            </div>
            <div style="width:180px;font-size:12px;color:#666D80;" class="truncate">{{ $user?->email ?? '—' }}</div>
            <div style="width:140px;font-size:12px;font-family:monospace;color:#353849;">{{ $lecturer->employee_number }}</div>
            <div style="width:110px;text-align:center;">
                @php $jumlah = \Modules\EOffice\Models\Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $lecturer->user_id))->count(); @endphp
                <span class="mp-badge primary sm">{{ $jumlah }} praktikum</span>
            </div>
            <div style="width:80px;font-size:11px;color:#666D80;">
                {{ $lecturer->created_at?->format('M Y') ?? '—' }}
            </div>
        </div>
        @empty
        <div style="padding:64px 20px;text-align:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <div style="font-size:13px;color:#666D80;">Belum ada dosen terdaftar di tabel lecturers.</div>
        </div>
        @endforelse
    </div>

    @if($dosens->hasPages())
    <div style="padding:12px 20px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $dosens->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>

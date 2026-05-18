<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Asisten Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Pendaftaran Asisten Praktikum</h1>
        <p class="mp-page-sub">Review dan proses permohonan calon asprak</p>
    </div>
    <div class="mp-page-actions">
        @foreach(['pending'=>'warning', 'approved'=>'success', 'rejected'=>'error'] as $st=>$variant)
        <a href="{{ request()->fullUrlWithQuery(['status'=>$st]) }}"
           class="mp-badge {{ $variant }} sm {{ request('status')===$st ? 'ring-2 ring-offset-1 ring-[#0B266E]' : '' }}"
           style="text-decoration:none;padding:6px 12px;cursor:pointer;">
            @if($st === 'pending') Menunggu @elseif($st === 'approved') Disetujui @else Ditolak @endif
        </a>
        @endforeach
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}" class="mp-btn secondary sm">Semua</a>
    </div>
</div>

{{-- Section title --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Asprak</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div style="flex-shrink:0;">
        <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
            <div class="mp-th flex-1">Nama / NIM</div>
            <div class="mp-th" style="width:200px;">Praktikum</div>
            <div class="mp-th" style="width:100px;">IPK</div>
            <div class="mp-th" style="width:110px;">Tanggal Daftar</div>
            <div class="mp-th" style="width:90px;">Status</div>
            <div class="mp-th" style="width:130px;">Aksi</div>
        </div>
    </div>
    <div class="overflow-y-auto flex-1">
        @forelse($pendaftaran ?? [] as $pend)
        <div class="mp-tr" style="display:flex;align-items:center;padding:12px 20px;">
            <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                <div class="mp-av navy flex-shrink-0">
                    {{ strtoupper(substr($pend->user?->name ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate">{{ $pend->user?->name ?? '—' }}</div>
                    <div style="font-size:11px;color:#666D80;">{{ $pend->user?->student_number ?? '—' }}</div>
                </div>
            </div>
            <div style="width:200px;font-size:12px;color:#353849;" class="truncate">{{ $pend->praktikum?->nama ?? '—' }}</div>
            <div style="width:100px;font-size:13px;font-weight:600;color:#0D0D12;">{{ $pend->ipk ?? '—' }}</div>
            <div style="width:110px;font-size:12px;color:#666D80;">{{ $pend->created_at?->format('d M Y') }}</div>
            <div style="width:90px;">
                @if($pend->status === 'pending')
                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                @elseif($pend->status === 'approved')
                    <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                @else
                    <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                @endif
            </div>
            <div class="flex gap-1" style="width:130px;">
                @if($pend->status === 'pending')
                <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id) }}">
                    @csrf
                    <button type="submit" class="mp-btn ghost sm">Terima</button>
                </form>
                <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="mp-btn destructive sm">Tolak</button>
                </form>
                @else
                <span style="font-size:12px;color:#808897;">—</span>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:64px 20px;text-align:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <div style="font-size:13px;color:#666D80;">Tidak ada data pendaftaran asprak.</div>
        </div>
        @endforelse
    </div>
    @if(isset($pendaftaran) && method_exists($pendaftaran, 'links'))
    <div style="padding:12px 20px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $pendaftaran->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>

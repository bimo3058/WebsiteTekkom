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
           style="text-decoration:none;padding:6px 12px;cursor:pointer;">{{ ucfirst($st) }}</a>
        @endforeach
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}" class="mp-btn secondary sm">Semua</a>
    </div>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-body" style="flex-shrink:0;">
        <div style="display:flex;align-items:center;padding:8px 20px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
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
                <div class="w-[32px] h-[32px] rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                    {{ strtoupper(substr($pend->user?->name ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $pend->user?->name ?? '—' }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);">{{ $pend->user?->student_number ?? '—' }}</div>
                </div>
            </div>
            <div style="width:200px;font-size:12px;color:var(--c-fg-sub);" class="truncate">{{ $pend->praktikum?->nama ?? '—' }}</div>
            <div style="width:100px;font-size:13px;font-weight:600;color:var(--c-fg);">{{ $pend->ipk ?? '—' }}</div>
            <div style="width:110px;font-size:12px;color:var(--c-fg-muted);">{{ $pend->created_at?->format('d M Y') }}</div>
            <div style="width:90px;">
                @if($pend->status === 'pending')
                    <span class="mp-badge warning sm">Pending</span>
                @elseif($pend->status === 'approved')
                    <span class="mp-badge success sm">Diterima</span>
                @else
                    <span class="mp-badge error sm">Ditolak</span>
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
                <span style="font-size:12px;color:var(--c-fg-placeholder);">—</span>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:56px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Tidak ada data pendaftaran asprak.</div>
        @endforelse
    </div>
    @if(isset($pendaftaran) && method_exists($pendaftaran, 'links'))
    <div style="padding:12px 20px;border-top:1px solid var(--c-border);flex-shrink:0;">{{ $pendaftaran->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>

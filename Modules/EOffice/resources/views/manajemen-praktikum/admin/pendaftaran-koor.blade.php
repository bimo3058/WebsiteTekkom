<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Koordinator Praktikum">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Pendaftaran Koordinator Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Review dan proses permohonan calon koordinator</div>
    </div>
    <div class="flex gap-2">
        @foreach(['pending'=>['#F9ECCB','#7C5309'], 'approved'=>['#DDF2EE','#174E43'], 'rejected'=>['#FADAE1','#7C1028']] as $st=>[$bg,$fg])
        <a href="{{ request()->fullUrlWithQuery(['status'=>$st]) }}"
           class="text-[12px] font-semibold px-3 py-[7px] rounded-[8px] no-underline {{ request('status')===$st ? 'ring-2 ring-offset-1 ring-[#0B266E]' : '' }}"
           style="background:{{ $bg }}; color:{{ $fg }};">{{ ucfirst($st) }}</a>
        @endforeach
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-koor.index') }}"
           class="text-[12px] text-[#666D80] px-3 py-[7px] rounded-[8px] border border-[#DFE1E7] no-underline hover:bg-[#F6F8FA]">Semua</a>
    </div>
</div>

<div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-h-0">
    <div class="flex px-5 py-[10px] bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
        <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama / NIM</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:200px;">Praktikum</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:100px;">IPK</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:110px;">Tanggal Daftar</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:90px;">Status</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:130px;">Aksi</div>
    </div>
    <div class="overflow-y-auto flex-1">
        @forelse($pendaftaran ?? [] as $pend)
        <div class="flex items-center px-5 py-[12px] border-b border-[#F8F9FB] hover:bg-[#FAFAFC] last:border-0">
            <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                <div class="w-[32px] h-[32px] rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                    {{ strtoupper(substr($pend->user?->name ?? 'K', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $pend->user?->name ?? '—' }}</div>
                    <div class="text-[11px] text-[#666D80]">{{ $pend->user?->student_number ?? '—' }}</div>
                </div>
            </div>
            <div class="text-[12px] text-[#353849] truncate" style="width:200px;">{{ $pend->praktikum?->nama ?? '—' }}</div>
            <div class="text-[13px] font-semibold text-[#0D0D12]" style="width:100px;">{{ $pend->ipk ?? '—' }}</div>
            <div class="text-[12px] text-[#666D80]" style="width:110px;">{{ $pend->created_at?->format('d M Y') }}</div>
            <div style="width:90px;">
                @if($pend->status === 'pending')
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F9ECCB] text-[#7C5309]">Pending</span>
                @elseif($pend->status === 'approved')
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43]">Diterima</span>
                @else
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#FADAE1] text-[#7C1028]">Ditolak</span>
                @endif
            </div>
            <div class="flex gap-1" style="width:130px;">
                @if($pend->status === 'pending')
                <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.approve', $pend->id) }}">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold px-3 py-[5px] rounded-[7px] bg-[#DDF2EE] text-[#174E43] border-none cursor-pointer hover:bg-[#c0e8e0]">Terima</button>
                </form>
                <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.reject', $pend->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-[11px] font-semibold px-3 py-[5px] rounded-[7px] bg-[#FADAE1] text-[#7C1028] border-none cursor-pointer hover:bg-[#f5b8c5]">Tolak</button>
                </form>
                @else
                <span class="text-[12px] text-[#A4ABB8]">—</span>
                @endif
            </div>
        </div>
        @empty
        <div class="py-14 text-center text-[13px] text-[#666D80]">Tidak ada data pendaftaran koordinator.</div>
        @endforelse
    </div>
    @if(isset($pendaftaran) && method_exists($pendaftaran, 'links'))
    <div class="px-5 py-3 border-t border-[#DFE1E7] flex-shrink-0">{{ $pendaftaran->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>
<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Dosen Pengampu">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Daftar Dosen Pengampu</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Data dosen diambil dari tabel lecturers</div>
    </div>
</div>

{{-- Search --}}
<form method="GET" class="flex gap-2 flex-shrink-0">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email / NIP..."
           class="flex-1 border border-[#DFE1E7] rounded-[8px] px-3 py-[7px] text-[13px] focus:outline-none focus:border-[#0B266E]">
    <button type="submit" class="px-4 py-[7px] rounded-[8px] bg-[#0B266E] text-white text-[13px] font-medium border-none cursor-pointer">Cari</button>
    <a href="{{ route('eoffice.manprak.admin.dosen.index') }}" class="px-4 py-[7px] rounded-[8px] border border-[#DFE1E7] text-[13px] text-[#666D80] no-underline hover:bg-[#F6F8FA]">Reset</a>
</form>

{{-- Table --}}
<div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-h-0">
    <div class="flex px-5 py-[10px] bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
        <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama Dosen</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:180px;">Email</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:140px;">No. Pegawai</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:110px;">Praktikum Diampu</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Bergabung</div>
    </div>

    <div class="overflow-y-auto flex-1">
        @forelse($dosens as $lecturer)
        @php $user = $lecturer->user; @endphp
        <div class="flex items-center px-5 py-[12px] border-b border-[#F8F9FB] hover:bg-[#FAFAFC] last:border-0">
            <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                <div class="w-[34px] h-[34px] rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#7B2FBE,#9B59B6);">
                    {{ strtoupper(substr($user?->name ?? 'D', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $user?->name ?? '—' }}</div>
                </div>
            </div>
            <div class="text-[12px] text-[#666D80] truncate" style="width:180px;">{{ $user?->email ?? '—' }}</div>
            <div class="text-[12px] font-mono text-[#353849]" style="width:140px;">{{ $lecturer->employee_number }}</div>
            <div class="text-center" style="width:110px;">
                @php $jumlah = \Modules\EOffice\Models\Praktikum::where('dosen_id', $lecturer->user_id)->count(); @endphp
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F0E6FA] text-[#9B59B6]">
                    {{ $jumlah }} praktikum
                </span>
            </div>
            <div class="text-[11px] text-[#666D80]" style="width:80px;">
                {{ $lecturer->created_at?->format('M Y') ?? '—' }}
            </div>
        </div>
        @empty
        <div class="py-14 text-center text-[13px] text-[#666D80]">Belum ada dosen terdaftar di tabel lecturers.</div>
        @endforelse
    </div>

    @if($dosens->hasPages())
    <div class="px-5 py-3 border-t border-[#DFE1E7] flex-shrink-0">{{ $dosens->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>
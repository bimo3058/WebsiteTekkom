<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" pageTitle="Daftar Dosen">

{{-- ════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════ --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Dosen</h1>
        <p class="mp-page-sub">Berikut daftar dosen di Teknik Komputer Universitas Diponegoro</p>
    </div>
</div>

{{-- ════════════════════════════════════════════
     CARD TABEL TERINTEGRASI
════════════════════════════════════════════ --}}
<div id="daftar-dosen" class="mp-card mt-2">

    {{-- Toolbar: Search + Filter --}}
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex flex-wrap gap-3 items-center justify-between">
        <form id="filter-dosen-form" method="GET" action="{{ url()->current() }}#daftar-dosen" class="flex flex-wrap gap-3 items-center flex-1">
            
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama / email / NIP..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]"
                       onchange="this.form.submit()">
            </div>

            @if(request()->filled('search'))
            <a href="{{ route('eoffice.manprak.admin.dosen.index') }}"
               class="mp-btn secondary md px-4" style="height:35px;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Kolom Header Tabel --}}
    <div class="mp-card-body p-0 mp-data-scroll">
        <div class="grid gap-4 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
             style="grid-template-columns: 50px 2fr 1.5fr 150px 130px 100px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">No</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Nama Dosen</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Email</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">NIP</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Praktikum Diampu</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Bergabung</div>
        </div>

        {{-- Rows --}}
        @forelse($dosens as $lecturer)
        @php $user = $lecturer->user; @endphp
        <div class="grid gap-4 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
             style="grid-template-columns: 50px 2fr 1.5fr 150px 130px 100px;">
            
            {{-- No --}}
            <div class="text-[12px] font-semibold text-[#666D80]">
                {{ $loop->iteration + ($dosens->firstItem() ?? 1) - 1 }}
            </div>

            {{-- Nama --}}
            <div class="flex items-center gap-[10px] min-w-0 pr-3">
                <div class="mp-av violet flex-shrink-0">
                    {{ strtoupper(substr($user?->name ?? 'D', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $user?->name ?? '—' }}</div>
                </div>
            </div>

            {{-- Email --}}
            <div class="text-[12px] text-[#666D80] truncate">{{ $user?->email ?? '—' }}</div>
            
            {{-- NIP --}}
            <div class="text-[12px] font-medium text-[#353849]">{{ $lecturer->employee_number }}</div>
            
            {{-- Praktikum Diampu --}}
            <div class="text-center">
                @php $jumlah = \Modules\EOffice\Models\Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $lecturer->user_id))->count(); @endphp
                <span class="text-[13px] font-medium text-[#353849]">{{ $jumlah }}</span>
                <span class="text-[12px] font-medium text-[#353849]"> praktikum</span>
            </div>
            
            {{-- Bergabung --}}
            <div class="text-[12px] text-[#666D80]">
                {{ $lecturer->created_at?->locale('id')->translatedFormat('F Y') ?? '—' }}
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-20 gap-4">
            <div class="w-14 h-14 rounded-full bg-[#F6F8FA] flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-[14px] font-semibold text-[#353849] mb-1">Belum ada dosen terdaftar</div>
                <div class="text-[12px] text-[#A4ABB8]">
                    @if(request()->filled('search'))
                        Tidak ada hasil untuk pencarian "{{ request('search') }}"
                    @else
                        Belum ada data dosen di dalam tabel lecturers.
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination Custom Fungsional --}}
    @if(isset($dosens) && $dosens instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$dosens" :options="[5, 10, 20]" fragment="daftar-dosen" />
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>

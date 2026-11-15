<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" pageTitle="Pendaftaran Asisten">
@php
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Praktikum[] $praktikumList */
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Modules\EOffice\Models\PendaftaranAsprak[] $pendaftaran */
@endphp
<div class="flex flex-col gap-5 mb-2">
    {{-- Page Header --}}
    <div class="mp-page-header" style="margin-bottom: 0;">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Pendaftaran</h1>
            </div>
            <p class="mp-page-sub">Kelola seleksi pendaftaran Koordinator dan Asisten &bull; {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </div>

    @include('eoffice::manajemen-praktikum.admin.partials.registration-tabs', ['activeRegistrationTab' => 'asprak'])
</div>

<div id="daftar-pendaftaran" class="mp-card" style="font-family:'Inter Tight',system-ui,sans-serif;">
    {{-- Toolbar: Filter & Search --}}
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex items-center justify-between">
        <form id="filter_pendaftaran_asprak_form" method="GET" action="{{ url()->current() }}#daftar-pendaftaran" class="flex w-full gap-3 items-center">
            @php
                $praktikumOptions = [['value' => '', 'label' => 'Semua Praktikum']];
                foreach($praktikumList as $p) {
                    $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $p->nama];
                }
            @endphp
            {{-- Praktikum Filter --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="request('praktikum_id', '')"
                placeholder="Semua Praktikum"
                onChange="$event.target.form.submit()"
                minWidth="160px"
            />

            {{-- Status Koordinator Filter --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="status_koor"
                :options="[
                    ['value' => '', 'label' => 'Status Koordinator'],
                    ['value' => 'menunggu', 'label' => 'Menunggu'],
                    ['value' => 'disetujui', 'label' => 'Disetujui'],
                    ['value' => 'ditolak', 'label' => 'Ditolak']
                ]"
                :selected="request('status_koor', '')"
                placeholder="Status Koordinator"
                onChange="$event.target.form.submit()"
                minWidth="140px"
            />

            {{-- Status Admin Filter --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="status"
                :options="[
                    ['value' => '', 'label' => 'Status Admin'],
                    ['value' => 'pending', 'label' => 'Menunggu'],
                    ['value' => 'approved', 'label' => 'Disetujui'],
                    ['value' => 'rejected', 'label' => 'Ditolak']
                ]"
                :selected="request('status', '')"
                placeholder="Status Admin"
                onChange="$event.target.form.submit()"
                minWidth="140px"
            />
            <div class="relative flex-1 min-w-[150px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama / NIM..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]"
                       onchange="this.form.submit()">
            </div>
            @if(request()->anyFilled(['search','status_koor','status','praktikum_id']))
            <a href="{{ route('eoffice.manprak.'.(request()->routeIs('eoffice.manprak.cerc.*')?'cerc':'admin').'.pendaftaran-asprak.index') }}"
               class="mp-btn secondary md px-4" style="height:35px; text-decoration:none; flex-shrink: 0;">Reset</a>
            @endif
        </form>
    </div>

    <div id="table_and_pagination">
    <div class="mp-card-body p-0 overflow-x-auto">
        <div style="min-width: max-content;">
        {{-- Header Tabel --}}
        <div class="grid gap-4 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7] items-center"
             style="grid-template-columns:44px 260px 150px 280px 70px 200px 160px 120px 190px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">No</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Nama Mahasiswa</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">NIM</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">IPK</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Berkas Tambahan</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Status Koordinator</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Status Admin</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Aksi</div>
        </div>

        {{-- Baris Tabel --}}
        @forelse($pendaftaran ?? [] as $pend)
        @php
            $nameParts = explode(' ', $pend->user?->name ?? 'AS');
            $initials = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
            $avColors = ['sky','navy','green','yellow','violet'];
            $avColor = $avColors[crc32($pend->user?->email ?? '') % count($avColors)];
        @endphp
        <div class="grid gap-4 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
             style="grid-template-columns:44px 260px 150px 280px 70px 200px 160px 120px 190px; min-height:76px;">
            {{-- No --}}
            <div class="text-[12px] font-semibold text-[#666D80]">
                {{ $loop->iteration + ($pendaftaran->firstItem() ?? 1) - 1 }}
            </div>

            {{-- Mahasiswa --}}
            <div class="flex items-center gap-[10px] pr-3">
                <div class="mp-av {{ $avColor }} flex-shrink-0">{{ $initials }}</div>
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] leading-5">{{ $pend->user?->name ?? '—' }}</div>
                    <div class="text-[11px] text-[#666D80] leading-4 break-all">{{ $pend->user?->email ?? '—' }}</div>
                </div>
            </div>

            {{-- NIM --}}
            <div class="text-[13px] text-[#0D0D12] whitespace-nowrap" style="font-family: 'Inter', sans-serif;">
                {{ $pend->user?->student?->student_number ?? $pend->user?->external_id ?? '—' }}
            </div>

            {{-- Praktikum --}}
            <div class="text-[13px] text-[#0D0D12] leading-5">{{ $pend->praktikum?->nama ?? '—' }}</div>
            <div class="text-center">
                <span class="text-[13px] text-[#0D0D12]">
                    {{ number_format($pend->ipk ?? 0, 2) }}
                </span>
            </div>
            @php
                $additionalDocumentName = $namaBerkasTambahan->get($pend->praktikum_id);
            @endphp
            <div class="text-[12px] text-[#666D80]">
                @if(filled($additionalDocumentName) && $pend->berkas_tambahan_path)
                    <div class="font-semibold text-[#353849] mb-1">{{ $additionalDocumentName }}</div>
                    <a class="text-[#0B266E] font-semibold hover:underline" target="_blank" rel="noopener"
                       href="{{ route('eoffice.manprak.'.(request()->routeIs('eoffice.manprak.cerc.*')?'cerc':'admin').'.pendaftaran.document', ['type' => 'asprak', 'id' => $pend->id, 'document' => 'berkas_tambahan_path']) }}">Lihat Berkas</a>
                @elseif(filled($additionalDocumentName))
                    <div class="font-semibold text-[#353849] mb-1">{{ $additionalDocumentName }}</div>
                    <span>Tidak mengunggah</span>
                @else
                    <span>Tidak disyaratkan</span>
                @endif
            </div>
            <div class="text-center">
                @if($pend->status_koor === 'disetujui')
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                @elseif($pend->status_koor === 'ditolak')
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                @else
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                @endif
            </div>
            <div class="text-center">
                @if($pend->status === 'approved')
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                @elseif($pend->status === 'rejected')
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                @else
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                @endif
            </div>
            <div class="text-center flex flex-col items-center justify-center gap-2">
                <x-eoffice::manajemen-praktikum.ui.action-menu>
                <a class="mp-btn secondary sm" href="{{ route('eoffice.manprak.'.(request()->routeIs('eoffice.manprak.cerc.*')?'cerc':'admin').'.pendaftaran.show', ['type' => 'asprak', 'id' => $pend->id]) }}">
                    <x-eoffice::manajemen-praktikum.ui.icon name="file" /> Detail &amp; Dokumen
                </a>
                @if(auth()->user()->hasAnyRole(['superadmin','admin_praktikum']) && $pend->status === 'pending' && $pend->status_koor === 'disetujui')
                <div class="flex gap-2 justify-center" x-data="{ alasan: '' }">
                    <form method="POST" action="{{ route('eoffice.manprak.'.(request()->routeIs('eoffice.manprak.cerc.*')?'cerc':'admin').'.pendaftaran-asprak.approve', $pend->id) }}">
                        @csrf
                        <button type="submit" class="mp-btn" style="padding:6px 8px; background:rgba(16, 185, 129, 0.15); color:#10B981;" title="Terima">
                            <span>Terima</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.'.(request()->routeIs('eoffice.manprak.cerc.*')?'cerc':'admin').'.pendaftaran-asprak.reject', $pend->id) }}">
                        @csrf
                        <input type="hidden" name="alasan_penolakan" :value="alasan">
                        <button type="button" @click="let r = prompt('Alasan Penolakan:'); if(r!==null){ alasan=r; $el.closest('form').submit(); }" class="mp-btn" style="padding:6px 8px; background:rgba(223, 28, 65, 0.15); color:#DF1C41;" title="Tolak">
                            <span>Tolak</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                </div>
                @endif
                </x-eoffice::manajemen-praktikum.ui.action-menu>
                @if($pend->status === 'pending' && $pend->status_koor === 'menunggu')
                <span class="text-[11px] text-[#666D80]">Tunggu Koordinator</span>
                @elseif(!($pend->status === 'pending' && $pend->status_koor === 'disetujui'))
                <span class="text-[11px] text-[#666D80]">Selesai</span>
                @endif
            </div>
        </div>
        @empty
        <div class="py-12 flex flex-col items-center justify-center">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mb-3">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5"/>
            </svg>
            <div class="text-[13px] text-[#666D80]">Tidak ada data pendaftaran asisten praktikum.</div>
        </div>
        @endforelse
        </div>
    </div>

    {{-- Pagination Custom Fungsional --}}
    @if(isset($pendaftaran) && $pendaftaran instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$pendaftaran" :options="[5, 10, 20]" fragment="daftar-pendaftaran" />
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>

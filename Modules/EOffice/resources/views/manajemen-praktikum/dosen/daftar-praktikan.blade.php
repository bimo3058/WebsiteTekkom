<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikan">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Praktikan</h1>
            <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">Lihat daftar mahasiswa peserta praktikum yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

{{-- Info --}}
<div class="mp-alert info flex-shrink-0">
    Halaman ini bersifat <strong>read-only</strong>. Penambahan dan impor praktikan dilakukan oleh Admin dan Koordinator.
</div>

{{-- Pilih Praktikum --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:14px 18px;">
        @if($praktikumList->isEmpty())
        <div class="mp-alert warning">Anda belum mengampu praktikum aktif manapun.</div>
        @else
        <form method="GET" class="flex gap-2 flex-wrap">
            <select name="praktikum_id" class="mp-input mp-select" style="max-width:320px;"
                    onchange="this.form.submit()">
                @foreach($praktikumList as $p)
                <option value="{{ $p->id }}" {{ ($praktikum?->id == $p->id) ? 'selected' : '' }}>
                    {{ $p->nama }}
                    @if($p->kode) [{{ $p->kode }}] @endif
                    · {{ $p->semester }} {{ $p->tahun_ajaran }}
                </option>
                @endforeach
            </select>
        </form>
        @endif
    </div>
</div>

@if($praktikum)

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Praktikan — {{ $praktikum->nama }}</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $praktikans->total() }} praktikan</span>
</div>

{{-- Search --}}
<div class="mp-card flex-shrink-0">
    <div style="padding:14px 18px;">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                   class="mp-input" style="flex:1;min-width:240px;">
            <button type="submit" class="mp-btn primary sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari
            </button>
            @if($search)
            <a href="{{ route('eoffice.manprak.dosen.daftar-praktikan.index', ['praktikum_id' => $praktikum->id]) }}"
               class="mp-btn secondary sm" style="text-decoration:none;">Hapus Filter</a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Praktikan</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">#</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Nama</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Email</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($praktikans as $i => $p)
                @php
                    $nameParts = explode(' ', $p->user?->name ?? 'PR');
                    $initials  = strtoupper(substr($nameParts[0] ?? 'P', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'R', 0, 1));
                    $avColors  = ['sky','navy','green','yellow','violet'];
                    $avColor   = $avColors[crc32($p->user?->email ?? '') % count($avColors)];
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;color:#808897;font-size:12px;">{{ $praktikans->firstItem() + $i }}</td>
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '—' }}</div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#666D80;font-size:12px;">{{ $p->user?->email ?? '—' }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span class="mp-badge success sm"><span class="dot"></span>{{ ucfirst($p->status ?? 'aktif') }}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:12px;color:#666D80;">
                        {{ $p->created_at?->locale('id')->isoFormat('D MMM YYYY') ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                             stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/>
                        </svg>
                        <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Tidak Ada Praktikan</div>
                        <div style="font-size:12px;color:#666D80;">
                            @if($search)
                            Praktikan dengan kata kunci "{{ $search }}" tidak ditemukan.
                            @else
                            Belum ada praktikan terdaftar pada praktikum ini.
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($praktikans->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $praktikans->links() }}</div>
    @endif
</div>

@endif {{-- end if praktikum --}}

</x-eoffice::manajemen-praktikum.layout>
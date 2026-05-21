<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikan">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Praktikan</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Kelola data praktikan per praktikum · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
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
        <div class="mp-alert warning">Belum ada praktikum aktif. Buat praktikum terlebih dahulu.</div>
        @else
        <form method="GET" class="flex gap-2 flex-wrap">
            <select name="praktikum_id" class="mp-input mp-select" style="max-width:340px;"
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
    <span class="sec-title">Tambah Praktikan</span>
    <span class="sec-rule"></span>
</div>

{{-- Import Card --}}
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Import Data Praktikan dari File</span>
    </div>
    <div style="padding:18px 20px;border-bottom:1px solid #DFE1E7;">
        <p style="font-size:12px;color:#666D80;margin-bottom:12px;">
            Upload file CSV atau XLSX dengan kolom <code style="background:#F6F8FA;padding:2px 6px;border-radius:4px;font-size:11px;">email</code> 
            atau <code style="background:#F6F8FA;padding:2px 6px;border-radius:4px;font-size:11px;">nim</code>. Satu per baris.
        </p>
        <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.store') }}" 
              enctype="multipart/form-data" class="flex items-end gap-3 flex-wrap">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Pilih File</label>
                <input type="file" name="file" class="mp-input" accept=".csv,.xlsx,.xls" required>
            </div>
            <button type="submit" class="mp-btn primary md">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                Import File
            </button>
        </form>
    </div>
</div>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Praktikan — {{ $praktikum->nama }}</span>
    <span class="sec-rule"></span>
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
            <a href="{{ route('eoffice.manprak.admin.daftar-praktikan.index', ['praktikum_id' => $praktikum->id]) }}"
               class="mp-btn secondary sm" style="text-decoration:none;">Hapus Filter</a>
            @endif
        </form>
    </div>
</div>

{{-- Tabel --}}
<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border, #DFE1E7);">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">Daftar Praktikan Terdaftar</h2>
    </div>
    <div class="overflow-x-auto flex-1">
        <table style="width:100%; border-collapse:collapse; min-width:780px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:48px;">#</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Email</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Status</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Terdaftar</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Aksi</th>
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
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">{{ $praktikans->firstItem() + $i }}</td>
                    <td style="padding:14px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div style="font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);">{{ $p->user?->name ?? '—' }}</div>
                        </div>
                    </td>
                    <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-muted, #666D80);">{{ $p->user?->email ?? '—' }}</td>
                    <td style="padding:14px 16px; text-align:center;">
                        <span class="mp-badge success sm"><span class="dot"></span>{{ ucfirst($p->status ?? 'aktif') }}</span>
                    </td>
                    <td style="padding:14px 16px; text-align:center; font-size:12px; color:var(--c-fg-muted, #666D80);">
                        {{ $p->created_at?->locale('id')->isoFormat('D MMM YYYY') ?? '—' }}
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.destroy', $p->id) }}"
                              onsubmit="return confirm('Hapus praktikan ini dari daftar?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="mp-btn destructive xs">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5"
                             stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/>
                        </svg>
                        <div style="font-size:14px;font-weight:600;color:var(--c-fg, #0D0D12);margin-bottom:4px;">Tidak Ada Praktikan</div>
                        <div style="font-size:12px;color:var(--c-fg-muted, #666D80);">
                            @if($search)
                            Praktikan dengan kata kunci "{{ $search }}" tidak ditemukan.
                            @else
                            Belum ada praktikan terdaftar pada praktikum ini. Import atau tambahkan praktikan terlebih dahulu.
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($praktikans->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--c-border, #DFE1E7);flex-shrink:0;">{{ $praktikans->links() }}</div>
    @endif
</div>

@endif {{-- end if praktikum --}}

</x-eoffice::manajemen-praktikum.layout>
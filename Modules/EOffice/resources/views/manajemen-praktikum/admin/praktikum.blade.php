<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Praktikum</h1>
        <p class="mp-page-sub">Kelola semua praktikum terdaftar</p>
    </div>
    <div class="mp-page-actions">
        <button onclick="document.getElementById('modalCreate').classList.remove('hidden')" class="mp-btn primary md">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Praktikum
        </button>
    </div>
</div>

{{-- Section title --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter &amp; Pencarian</span>
    <span class="sec-rule"></span>
</div>

{{-- Filter Bar --}}
<div class="flex gap-3 flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode..."
               class="mp-input flex-1">
        <select name="status" class="mp-input mp-select">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status')==='aktif'?'selected':'' }}>Aktif</option>
            <option value="nonaktif" {{ request('status')==='nonaktif'?'selected':'' }}>Nonaktif</option>
        </select>
        <select name="semester" class="mp-input mp-select">
            <option value="">Semua Semester</option>
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
        </select>
        <button type="submit" class="mp-btn primary sm">Filter</button>
        <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn secondary sm">Reset</a>
    </form>
</div>

{{-- Section title --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Praktikum</span>
    <span class="sec-rule"></span>
</div>

{{-- Table --}}
<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border, #DFE1E7);">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">Daftar Praktikum Aktif</h2>
    </div>
    <div class="overflow-x-auto flex-1">
        <table style="width:100%; border-collapse:collapse; min-width:850px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:100px;">Kode</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama Praktikum</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">Dosen Pengampu</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:140px;">Koordinator</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:80px;">Praktikan</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:90px;">Status</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($praktikums ?? [] as $p)
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px; font-size:12px; font-weight:700; letter-spacing:.05em; color:#0B266E; font-family:monospace;">
                        {{ $p->kode ?? '—' }}
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);" class="truncate">{{ $p->nama }}</div>
                        <div style="font-size:11px; color:var(--c-fg-muted, #666D80);">{{ $p->tahun_ajaran }} / Sem. {{ $p->semester }}</div>
                    </td>
                    <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-muted, #666D80);" class="truncate">
                        {{ $p->dosen?->name ?? '—' }}
                    </td>
                    <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-muted, #666D80);" class="truncate">
                        @if($p->koordinator)
                            {{ $p->koordinator->name }}
                        @else
                            <span style="color:#EF4444; font-size:11px; font-weight:600;">Belum ditunjuk</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px; font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12); text-align:center;">
                        {{ $p->daftar_praktikan_count ?? 0 }}
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        @if($p->status === 'aktif')
                            <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                        @else
                            <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <div class="flex gap-1 justify-center">
                            <a href="{{ route('eoffice.manprak.admin.praktikum.edit', $p->id) }}"
                               class="mp-btn secondary sm" style="padding:5px 8px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.destroy', $p->id) }}"
                                  onsubmit="return confirm('Hapus praktikum {{ $p->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="mp-btn destructive sm" style="padding:5px 8px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:64px 20px; text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border, #DFE1E7)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        <div style="font-size:13px;color:var(--c-fg-muted, #666D80);">Belum ada data praktikum.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($praktikums) && method_exists($praktikums, 'links'))
    <div style="padding:12px 20px; border-top:1px solid var(--c-border, #DFE1E7); flex-shrink:0;">
        {{ $praktikums->links() }}
    </div>
    @endif
</div>

{{-- Modal Create --}}
<div id="modalCreate" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4);">
    <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[16px] text-[#0D0D12]">Tambah Praktikum Baru</div>
            <button onclick="document.getElementById('modalCreate').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-[#F6F8FA] border-none bg-transparent cursor-pointer text-[#666D80] text-lg">×</button>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.store') }}" class="p-6">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Praktikum <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Praktikum Algoritma dan Pemrograman"
                           value="{{ old('nama') }}" class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Mata Kuliah Praktikum</label>
                    <select name="matkul_id" class="mp-input mp-select w-full">
                        <option value="">— Pilih Mata Kuliah —</option>
                        @foreach(($matkulList ?? collect())->groupBy('semester') as $sem => $items)
                        <optgroup label="Semester {{ $sem }}">
                            @foreach($items as $mk)
                            <option value="{{ $mk->id }}" {{ old('matkul_id') == $mk->id ? 'selected' : '' }}>
                                [{{ $mk->kode }}] {{ $mk->nama }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_ajaran" required placeholder="2025" value="{{ old('tahun_ajaran', now()->year) }}"
                               class="mp-input w-full">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Semester <span class="text-red-500">*</span></label>
                        <select name="semester" required class="mp-input mp-select w-full">
                            <option value="Ganjil" {{ old('semester')=='Ganjil'?'selected':'' }}>Ganjil</option>
                            <option value="Genap"  {{ old('semester','Genap')=='Genap'?'selected':'' }}>Genap</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dosen Pengampu</label>
                    <select name="dosen_id" class="mp-input mp-select w-full">
                        <option value="">— Pilih Dosen —</option>
                        @foreach($dosenList ?? [] as $d)
                        <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat praktikum..."
                              class="mp-input w-full resize-none">{{ old('deskripsi') }}</textarea>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Status</label>
                    <select name="status" class="mp-input mp-select w-full">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                        class="mp-btn secondary md flex-1">Batal</button>
                <button type="submit" class="mp-btn primary md flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.addEventListener('DOMContentLoaded',()=>document.getElementById('modalCreate').classList.remove('hidden'))</script>
@endif

</x-eoffice::manajemen-praktikum.layout>

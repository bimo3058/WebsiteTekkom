<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Modul</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum terdaftar di praktikum manapun' }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum terdaftar sebagai Asisten Praktikum aktif.</div>
@else

{{-- Form Buat Modul Baru --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Buat Modul Baru</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="padding:20px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.modul.store') }}"
          style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        @csrf
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">
                Nama Modul <span style="color:#DF1C41;">*</span>
            </label>
            <input name="nama" value="{{ old('nama') }}" required class="mp-input" style="width:100%;"
                   placeholder="cth. Modul 1 — Pengenalan">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Jadwal / Minggu</label>
            <input name="jadwal_minggu" value="{{ old('jadwal_minggu') }}" class="mp-input" style="width:100%;"
                   placeholder="cth. Minggu 1 / Senin 08.00–10.00">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">
                Urutan <span style="color:#DF1C41;">*</span>
            </label>
            <input type="number" name="urutan" value="{{ old('urutan', $moduls->count() + 1) }}" min="1"
                   required class="mp-input" style="width:100%;">
        </div>
        <div style="grid-row:span 2;display:flex;flex-direction:column;">
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="mp-input" style="width:100%;flex:1;"
                      placeholder="Deskripsi singkat modul ini">{{ old('deskripsi') }}</textarea>
        </div>
        <div style="grid-column:1/-1;display:flex;justify-content:flex-end;">
            <button class="mp-btn primary md">+ Tambah Modul</button>
        </div>
    </form>
</div>

{{-- Daftar Modul --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Semua Modul Praktikum</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $moduls->count() }} modul</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Semua Modul Praktikum</span>
        <div class="right">
            <span style="font-size:12px;color:#666D80;">{{ $moduls->count() }} modul</span>
        </div>
    </div>

    @forelse($moduls as $modul)
    @php $isMine = $assignedModulIds->contains($modul->id); @endphp
    <div class="mp-tr" style="display:flex;align-items:flex-start;gap:16px;padding:16px 20px;border-bottom:1px solid #DFE1E7;"
         onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background=''">

        {{-- Urutan Badge --}}
        <div style="width:36px;height:36px;border-radius:50%;font-size:13px;font-weight:700;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;
                    background:{{ $isMine ? '#F6F8FA' : '#F0F1F4' }};
                    color:{{ $isMine ? '#0B266E' : '#666D80' }};">
            {{ $modul->urutan }}
        </div>

        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                <span style="font-size:14px;font-weight:600;color:#0D0D12;">{{ $modul->nama }}</span>
                @if($isMine)
                <span class="mp-badge success sm"><span class="dot"></span>Diampu Anda</span>
                @endif
            </div>
            <div style="font-size:12px;color:#666D80;margin-bottom:4px;">{{ $modul->jadwal_minggu ?? '—' }}</div>
            @if($modul->deskripsi)
            <div style="font-size:12px;color:#353849;margin-bottom:8px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $modul->deskripsi }}</div>
            @endif
            <div style="display:flex;gap:12px;font-size:11px;color:#666D80;">
                <span>{{ $modul->materi->count() }} materi</span>
                <span>{{ $modul->tugas->count() }} tugas</span>
                <span>{{ $modul->modulAsprak->count() }} asprak</span>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            @if($isMine)
                <a href="{{ route('eoffice.manprak.asprak.modul.show', $modul->id) }}" class="mp-btn primary sm" style="text-decoration:none;">Detail</a>
                <button onclick="openEdit({{ $modul->id }}, '{{ addslashes($modul->nama) }}', {{ $modul->urutan }}, '{{ addslashes($modul->jadwal_minggu ?? '') }}', '{{ addslashes($modul->deskripsi ?? '') }}')"
                        class="mp-btn secondary sm">Edit</button>
                <form method="POST" action="{{ route('eoffice.manprak.asprak.modul.destroy', $modul->id) }}"
                      onsubmit="return confirm('Hapus modul {{ addslashes($modul->nama) }}? Materi dan tugas di dalamnya juga akan dihapus.')">
                    @csrf @method('DELETE')
                    <button class="mp-btn secondary sm" style="color:#DF1C41;border-color:#DF1C41;">Hapus</button>
                </form>
            @else
                <button class="mp-btn secondary sm" disabled title="Anda bukan pengampu modul ini" style="opacity:0.5;cursor:not-allowed;">Tidak ada akses</button>
            @endif
        </div>
    </div>
    @empty
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul. Buat modul pertama di atas.</div>
    </div>
    @endforelse
</div>

{{-- Modal Edit Modul --}}
<div id="modal-edit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div style="background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.18);width:100%;max-width:480px;margin:0 16px;padding:24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="font-weight:700;font-size:15px;color:#0D0D12;">Edit Modul</div>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    style="color:#666D80;font-size:18px;line-height:1;border:none;background:transparent;cursor:pointer;">✕</button>
        </div>
        <form id="form-edit" method="POST" style="display:flex;flex-direction:column;gap:12px;">
            @csrf @method('PUT')
            <div>
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Nama Modul</label>
                <input id="edit-nama" name="nama" required class="mp-input" style="width:100%;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Urutan</label>
                    <input id="edit-urutan" type="number" name="urutan" min="1" required class="mp-input" style="width:100%;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Jadwal</label>
                    <input id="edit-jadwal" name="jadwal_minggu" class="mp-input" style="width:100%;">
                </div>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Deskripsi</label>
                <textarea id="edit-deskripsi" name="deskripsi" rows="3" class="mp-input" style="width:100%;"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:4px;">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="mp-btn secondary md">Batal</button>
                <button class="mp-btn primary md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, nama, urutan, jadwal, deskripsi) {
    document.getElementById('form-edit').action = '/eoffice/manprak/asprak/modul/' + id;
    document.getElementById('edit-nama').value    = nama;
    document.getElementById('edit-urutan').value  = urutan;
    document.getElementById('edit-jadwal').value  = jadwal;
    document.getElementById('edit-deskripsi').value = deskripsi;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>

@endif

</x-eoffice::manajemen-praktikum.layout>

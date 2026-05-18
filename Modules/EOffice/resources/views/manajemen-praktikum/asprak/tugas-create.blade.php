<x-eoffice::manajemen-praktikum.layout pageTitle="Buat Tugas">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Buat Tugas Baru</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">Pilih modul yang Anda ampu lalu isi detail tugas · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Detail Tugas</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="max-width:720px;padding:20px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.store') }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf

        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Modul</label>
            <select name="modul_id" required class="mp-select" style="width:100%;">
                <option value="">Pilih modul</option>
                @foreach($moduls as $m)
                <option value="{{ $m->id }}">{{ $m->nama }} - {{ $m->praktikum?->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Judul Tugas</label>
            <input name="judul" required class="mp-input" style="width:100%;" placeholder="Masukkan judul tugas...">
        </div>

        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Deskripsi</label>
            <textarea name="deskripsi" rows="6" class="mp-input" style="width:100%;" placeholder="Deskripsi tugas, instruksi pengerjaan..."></textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Deadline</label>
                <input type="datetime-local" name="deadline" class="mp-input" style="width:100%;">
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding-top:24px;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" id="chk-published" style="accent-color:#0B266E;width:16px;height:16px;" checked>
                <label for="chk-published" style="font-size:13px;font-weight:600;color:#353849;cursor:pointer;">Publikasikan</label>
            </div>
        </div>

        <div style="padding-top:4px;">
            <button class="mp-btn primary md">Simpan Tugas</button>
        </div>
    </form>
</div>

</x-eoffice::manajemen-praktikum.layout>

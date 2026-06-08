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

{{-- Main Scrollable Container --}}
<div style="display:flex;flex-direction:column;gap:20px;min-height:0;flex:1;overflow-y:auto;padding-right:8px;">

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Detail Tugas</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card" style="padding:28px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.store') }}" style="display:flex;flex-direction:column;gap:20px;">
        @csrf

        <div>
            <label class="block text-[12px] font-semibold text-[#353849] mb-3">Modul <span style="color:#DF1C41;">*</span></label>
            <select name="modul_id" required class="mp-input w-full" style="min-height:44px;">
                <option value="">Pilih modul</option>
                @foreach($moduls as $m)
                <option value="{{ $m->id }}">{{ $m->nama }} - {{ $m->praktikum?->nama }}</option>
                @endforeach
            </select>
            @error('modul_id')
            <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-[#353849] mb-3">Judul Tugas <span style="color:#DF1C41;">*</span></label>
            <input name="judul" required class="mp-input w-full" style="min-height:44px;" placeholder="Contoh: Percobaan Seri RLC, Implementasi Flip-flop, dst." value="{{ old('judul') }}">
            @error('judul')
            <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-[#353849] mb-3">Deskripsi <span style="color:#999;">(opsional)</span></label>
            <textarea name="deskripsi" rows="6" class="mp-input w-full resize-none" placeholder="Jelaskan detail tugas, instruksi pengerjaan, kriteria penilaian, atau catatan penting lainnya...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
            <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-3">Deadline <span style="color:#999;">(opsional)</span></label>
                <input type="datetime-local" name="deadline" class="mp-input w-full" style="min-height:44px;" value="{{ old('deadline') }}">
                @error('deadline')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;align-items:flex-end;padding-bottom:2px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none;flex:1;">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" id="chk-published" style="accent-color:#6366F1;width:18px;height:18px;cursor:pointer;" @if(old('is_published', true)) checked @endif>
                    <span style="font-size:13px;font-weight:600;color:#353849;">Publikasikan Sekarang</span>
                </label>
                <div style="font-size:11px;color:#666D80;text-align:right;">Langsung terlihat mahasiswa</div>
            </div>
        </div>

        <div style="padding-top:16px;border-top:1px solid #DFE1E7;display:flex;gap:12px;">
            <button type="submit" class="mp-btn primary md" style="flex:1;">Simpan Tugas</button>
            <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary md" style="text-decoration:none;flex:1;text-align:center;">Batal</a>
        </div>
    </form>
</div>

</div>
{{-- End Scrollable Container --}}

</x-eoffice::manajemen-praktikum.layout>
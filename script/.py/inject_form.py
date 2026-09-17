import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

target = r"""            {{-- ORIGINAL SECTION: Filter Pendaftaran --}}
            <div class="sec-head mt-4">"""

insertion = """            {{-- NEW SECTION: Form Pendaftaran Koordinator --}}
            <div class="mp-card" style="margin-top: 24px; flex-shrink: 0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Pendaftaran Koordinator</span>
                </div>
                <div style="padding: 24px;">
                    <form method="POST" action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.store') }}">
                        @csrf
                        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                        <input type="hidden" name="tipe" value="koordinator">

                        <div style="margin-bottom: 20px;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Judul</label>
                            <input type="text" name="nama" class="mp-input w-full" placeholder="Contoh: Seleksi Koordinator Genap 2025" style="border-radius: 8px; font-size:13px; padding: 10px 14px;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Deskripsi</label>
                            <textarea name="deskripsi" class="mp-input w-full" rows="3" placeholder="Tuliskan deskripsi atau link soal tes..." style="border-radius: 8px; font-size:13px; padding: 10px 14px;"></textarea>
                            <div style="font-size:11px; color:#666D80; margin-top:4px;">Maksimal 500 karakter.</div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Dibuka Pada</label>
                            <input type="datetime-local" name="dibuka_pada" class="mp-input w-full" style="border-radius: 8px; font-size:13px; padding: 10px 14px; color: #6B7280;">
                            <div style="font-size:11px; color:#666D80; margin-top:4px;">Kosongkan untuk langsung dibuka saat ini.</div>
                        </div>

                        <div style="margin-bottom: 32px;">
                            <label style="display:block;font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Ditutup Pada <span style="color:#DF1C41;">*</span></label>
                            <input type="datetime-local" name="ditutup_pada" class="mp-input w-full" style="border-radius: 8px; font-size:13px; padding: 10px 14px; color: #6B7280;" required>
                            <div style="font-size:11px; color:#666D80; margin-top:4px;">Wajib diisi sebagai batas penutupan.</div>
                        </div>

                        <div style="height: 1px; background: #E5E7EB; margin: 0 -24px 24px -24px;"></div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                            <div>
                                <label style="display:block;font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:6px;">IPK <span style="color:#DF1C41;">*</span></label>
                                <input type="text" class="mp-input w-full" value="" placeholder="Diisi oleh mahasiswa" disabled style="border-radius: 8px; background:#F9FAFB; cursor:not-allowed; font-size:13px; padding: 10px 14px;">
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Transkrip Nilai <span style="color:#DF1C41;">*</span></label>
                                <input type="text" class="mp-input w-full" value="" placeholder="Upload File (.pdf)" disabled style="border-radius: 8px; background:#F9FAFB; cursor:not-allowed; font-size:13px; padding: 10px 14px;">
                            </div>
                            <div>
                                <label style="display:block;font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Keanggotaan CERC</label>
                                <input type="text" class="mp-input w-full" value="" placeholder="Upload File (.pdf)" disabled style="border-radius: 8px; background:#F9FAFB; cursor:not-allowed; font-size:13px; padding: 10px 14px;">
                            </div>
                            <div style="display: flex; align-items: flex-end;">
                                <button type="button" class="w-full flex items-center justify-center gap-2" style="border-radius: 8px; font-weight:600; font-size:13px; border: 1px dashed #D1D5DB; background: white; color: #0B266E; padding: 10px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='white'">
                                    <span style="font-size: 16px; font-weight:400; line-height:1;">+</span> Tambah Berkas Tambahan
                                </button>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 32px;">
                            <button type="submit" style="background:#0B266E; border-radius:8px; padding: 10px 24px; font-weight:600; font-size:13px; color:white; border:none; cursor:pointer; box-shadow: 0 4px 6px rgba(11,38,110,0.15); transition: background 0.2s;" onmouseover="this.style.background='#081b4f'" onmouseout="this.style.background='#0B266E'">
                                Buka Periode Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ORIGINAL SECTION: Filter Pendaftaran --}}
            <div class="sec-head mt-4">"""

text = text.replace(target, insertion)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")
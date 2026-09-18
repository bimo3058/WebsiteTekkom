<x-eoffice::manajemen-ruangan.layout pageTitle="Pengaturan Sistem">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Pengaturan Sistem & Operasional</h1>
            <p class="mp-page-sub">Atur jam buka pelayanan peminjaman dan blokir tanggal merah (Hari Libur).</p>
        </div>
    </div>

    <div style="display: flex; gap: 24px; margin-top: 24px; align-items: flex-start; flex-wrap: wrap;">

        {{-- Panel Kiri: Jam Operasional --}}
        <div class="mp-card" style="flex: 1; min-width: 300px; max-width: 400px; border-radius: 12px;">
            <div class="mp-card-header" style="background: #FAFBFC;">
                <h3 class="mp-card-title" style="font-size: 14px;">Jam Operasional</h3>
            </div>
            <form method="POST" action="{{ route('eoffice.peminjaman.admin.pengaturan.operasional') }}">
                @csrf
                <div class="mp-card-body" style="padding: 24px;">
                    <div style="margin-bottom: 20px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Jam
                            Buka Peminjaman</label>
                        <input type="time" name="jam_buka" class="mp-input cursor-text" value="{{ $jamBuka ?? '08:00' }}"
                            style="padding: 10px 14px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Jam
                            Tutup (Batas Akhir)</label>
                        <input type="time" name="jam_tutup" class="mp-input cursor-text" value="{{ $jamTutup ?? '16:00' }}"
                            style="padding: 10px 14px; font-size: 14px;" required>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Batas
                            Minimal H- Booking (Hari)</label>
                        <input type="number" name="batas_h_min_booking" min="0" max="30" class="mp-input cursor-text"
                            value="{{ $batasHMinBooking ?? 0 }}" style="padding: 10px 14px; font-size: 14px;" required>
                        <p style="font-size: 11px; color: #6B7280; margin-top: 6px;">Contoh: Isi <strong>2</strong> jika
                            booking minimal harus 2 hari sebelum pemakaian. Isi <strong>0</strong> untuk mengizinkan
                            booking di hari yang sama.</p>
                    </div>

                    <div style="background: #F8F9FB; padding: 16px; border-radius: 10px; border: 1px solid #E5E7EB;">
                        <label class="cursor-pointer" style="display:flex; align-items:flex-start; gap:12px;">
                            <input type="checkbox" name="buka_akhir_pekan" value="1" {{ $bukaAkhirPekan ? 'checked' : '' }} style="margin-top: 2px; width: 16px; height: 16px; accent-color: #0B266E;">
                            <div>
                                <span style="display:block; font-size:13px; font-weight:600; color:#111827;">Buka di
                                    Akhir Pekan</span>
                                <span style="display:block; font-size:12px; color:#6B7280; margin-top:2px;">Jika aktif,
                                    pengguna bisa pinjam ruangan di hari Sabtu & Minggu.</span>
                            </div>
                        </label>
                    </div>
                </div>
                <div style="padding: 16px 24px; border-top:1px solid #E5E7EB; background:#fff; text-align:right;">
                    <button type="submit" class="mp-btn primary cursor-pointer"
                        style="padding: 10px 24px; font-size: 13px; border-radius: 8px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Panel Kanan: Blackout Dates --}}
        <div class="mp-card" style="flex: 2; min-width: 400px; border-radius: 12px;">
            <div class="mp-card-header" style="background: #FAFBFC;">
                <h3 class="mp-card-title" style="font-size: 14px;">Tanggal Merah</h3>
            </div>
            <div class="mp-card-body" style="padding: 24px;">
                <form method="POST" action="{{ route('eoffice.peminjaman.admin.pengaturan.libur') }}"
                    style="display:flex; gap:16px; align-items: flex-end; margin-bottom: 24px; background: #FAFBFC; padding: 20px; border-radius: 10px; border: 1px solid #E5E7EB;">
                    @csrf
                    <div style="flex: 1.2;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Pilih
                            Tanggal</label>
                        <input type="date" name="tanggal" class="mp-input cursor-text"
                            style="padding: 10px 14px; font-size: 14px; width: 100%; box-sizing: border-box;" required>
                    </div>
                    <div style="flex: 2;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Keterangan</label>
                        <input type="text" name="keterangan" class="mp-input cursor-text"
                            style="padding: 10px 14px; font-size: 14px; width: 100%; box-sizing: border-box;"
                            placeholder="Misal: Libur Idul Fitri" required>
                    </div>
                    <div style="padding-bottom: 0;">
                        <button type="submit" class="mp-btn primary cursor-pointer"
                            style="padding: 10px 20px; font-size: 13px; border-radius: 8px; white-space: nowrap;">+
                            Tambah Libur</button>
                    </div>
                </form>

                @error('tanggal')
                    <div
                        style="background: #FEF2F2; color: #991B1B; padding: 10px 14px; border-radius: 8px; margin-top:-14px; margin-bottom: 16px; font-size:13px; border: 1px solid #FCA5A5;">
                        {{ $message }}
                    </div>
                @enderror

                <div class="mp-table-wrap" style="border:1px solid #E5E7EB; border-radius: 8px;">
                    <table class="mp-table" style="margin: 0;">
                        <thead>
                            <tr>
                                <th style="background: #F8F9FB; padding-top: 12px; padding-bottom: 12px;">TANGGAL LIBUR
                                </th>
                                <th style="background: #F8F9FB; padding-top: 12px; padding-bottom: 12px;">KETERANGAN
                                </th>
                                <th
                                    style="background: #F8F9FB; padding-top: 12px; padding-bottom: 12px; text-align:right;">
                                    AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tanggalLibur as $libur)
                                <tr class="mp-tr">
                                    <td style="font-weight:600; color: #111827;">
                                        {{ \Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y') }}
                                    </td>
                                    <td style="color: #4B5563;">{{ $libur->keterangan }}</td>
                                    <td style="text-align:right;">
                                        <div x-data="{ showDeleteModal: false }">
                                            <form method="POST"
                                                action="{{ route('eoffice.peminjaman.admin.pengaturan.libur.destroy', $libur->id) }}"
                                                style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="showDeleteModal = true"
                                                    class="mp-btn secondary sm cursor-pointer"
                                                    style="padding:8px 8px; color: #DC2626; border-color: #FCA5A5; background: #FEF2F2; display: inline-flex;">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                        <path
                                                            d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6M14 11v6" />
                                                    </svg>
                                                </button>

                                                {{-- Decision Modal --}}
                                                <div x-show="showDeleteModal" x-cloak style="display: none;"
                                                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 text-left">
                                                    <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0"
                                                        x-transition:enter-end="opacity-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100"
                                                        x-transition:leave-end="opacity-0"
                                                        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm cursor-pointer"
                                                        @click="showDeleteModal = false"></div>

                                                    <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden z-10 text-center p-6">
                                                        <div
                                                            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-4">
                                                            <svg class="h-6 w-6 text-red-600" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-base font-bold text-slate-800 mb-2">Hapus tanggal
                                                            libur ini?</h3>
                                                        <p class="text-xs text-slate-500 mb-6">Tindakan ini tidak dapat
                                                            dikembalikan.</p>

                                                        <div class="flex justify-center gap-3">
                                                            <button type="button" @click="showDeleteModal = false"
                                                                class="mp-btn secondary px-4 py-2 cursor-pointer">Batal</button>
                                                            <button type="submit"
                                                                class="mp-btn px-4 py-2 bg-red-600 hover:bg-red-700 text-white border-transparent cursor-pointer"
                                                                style="border:none;">Hapus</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        style="text-align:center; padding: 30px; color:#9CA3AF; font-size:13px; font-style: italic;">
                                        Belum ada hari libur / blackout date yang ditambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>



</x-eoffice::manajemen-ruangan.layout>
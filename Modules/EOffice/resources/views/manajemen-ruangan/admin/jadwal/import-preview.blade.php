<x-eoffice::manajemen-ruangan.layout pageTitle="Pratinjau Impor Jadwal Kuliah">

    <div class="mp-page-header">
        <div>
            <a href="{{ route('eoffice.peminjaman.admin.jadwal-akademik.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-900 mb-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Jadwal
            </a>
            <h1 class="mp-page-title">Pratinjau Sandbox
                <span
                    class="ml-2 inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Belum
                    Disimpan</span>
            </h1>
            <p class="mp-page-sub">Tinjau ulang data kelas di bawah ini sebelum disimpan permanen ke database.</p>
        </div>
        <div class="mp-page-actions">
            <!-- Form Execution -->
            @php
                $namaHariArray = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                $payloadArray = [];
                foreach ($csvData as $row) {
                    $payloadArray[] = [
                        'hari' => (int) $row[0],
                        'ruangan_id' => (int) $row[1],
                        'jam_mulai' => $row[2],
                        'jam_selesai' => $row[3],
                        'mata_kuliah' => $row[4],
                        'kode_mk' => $row[5],
                        'kelas' => $row[6],
                        'sks' => (int) $row[7],
                        'kuota' => (int) $row[8],
                        'pengampu' => $row[9]
                    ];
                }
            @endphp
            <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.import-execute') }}" method="POST"
                id="executeImportForm"
                class="flex items-center gap-3 bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
                @csrf
                <textarea name="validated_payload" class="hidden">{{ json_encode($payloadArray) }}</textarea>

                <div class="flex flex-col text-left">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1 mb-0.5">Mulai
                        Semester</label>
                    <input type="date" name="tgl_mulai_efektif_global" required
                        class="mp-input !py-1.5 !text-xs !bg-gray-50 border-none shadow-inner w-[130px] rounded-lg">
                </div>
                <div class="flex flex-col text-left">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1 mb-0.5">Akhir
                        Semester</label>
                    <input type="date" name="tgl_selesai_efektif_global" required
                        class="mp-input !py-1.5 !text-xs !bg-gray-50 border-none shadow-inner w-[130px] rounded-lg">
                </div>

                <div class="h-8 w-px bg-gray-200 mx-1"></div>

                <button type="submit" class="mp-btn primary md !py-2.5" {{ count($payloadArray) === 0 ? 'disabled' : '' }}>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="mr-1" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Simpan {{ count($payloadArray) }} Kelas Rutin
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Block -->
    <div
        class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative text-[13px] font-medium shadow-sm">
        <strong class="font-bold mr-1">Sandbox Mode:</strong>
        {{ count($csvData) }} baris data valid berhasil dibaca dari SIAP. Tinjau dan simpan ke database.
    </div>

    <div class="mp-card" style="margin-top: 15px;">
        <div class="mp-card-body">
            <div class="mp-table-wrap"
                style="max-height: 500px; overflow-y: auto; border: 1px solid #E2E8F0; border-radius: 8px;">
                <table class="mp-table" style="position: relative;">
                    <thead
                        style="position: sticky; top: 0; z-index: 20; background: #F8FAFC; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        <tr>
                            <th style="width:44px; text-align:center; background: #F8FAFC;">#</th>
                            <th style="background: #F8FAFC;">HARI</th>
                            <th style="background: #F8FAFC;">WAKTU</th>
                            <th style="background: #F8FAFC;">MATA KULIAH</th>
                            <th style="background: #F8FAFC; text-align:center;">KELAS</th>
                            <th style="background: #F8FAFC;">RUANGAN</th>
                        </tr>
                    </thead>
                    <tbody style="background: white;">
                        @foreach($csvData as $row)
                            @php
                                $hari = (int) $row[0];
                                $ruangId = (int) $row[1];
                                $jamMulai = $row[2];
                                $jamSelesai = $row[3];
                                $matkul = $row[4];
                                $kelas = $row[6];
                                $ruanganObj = $ruangans[$ruangId] ?? null;
                            @endphp
                            <tr class="mp-tr">
                                <td style="text-align:center;">
                                    <div
                                        style="margin:0 auto; width:24px; height:24px; border-radius:6px; background:#D1FAE5; display:flex; align-items:center; justify-content:center; color:#065F46;">
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:600; color:#3730A3;">
                                        {{ $namaHariArray[$hari] ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:700; color:#0D0D12;">
                                        {{ $jamMulai }} - {{ $jamSelesai }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:13px; font-weight:700; color:#0D0D12;">
                                        {{ $matkul }}
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <div
                                        style="font-size:13px; font-weight:700; color:#3730A3; display:inline-block; padding:2px 8px; background:#EEF2FF; border-radius:6px; border:1px solid #C7D2FE;">
                                        {{ $kelas ?: '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:700; color:#0D0D12;">
                                        {{ $ruanganObj->nama ?? 'Tidak Diketahui' }}
                                        <span style="font-size:12px; color:#666D80; margin-left:4px; font-weight:500;">(Lt.
                                            {{ $ruanganObj->lantai ?? '-' }})</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(count($csvData) === 0)
                <div class="py-12 text-center text-gray-500 font-medium">
                    Tidak ada data yang dapat dibaca. Pastikan format CSV sesuai dengan template (tanpa header baris).
                </div>
            @endif
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>
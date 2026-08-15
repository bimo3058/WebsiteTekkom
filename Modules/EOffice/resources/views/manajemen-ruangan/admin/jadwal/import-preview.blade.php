<x-eoffice::manajemen-ruangan.layout pageTitle="Pratinjau Impor Jadwal Kuliah">

    <div x-data="sandboxEditor()">
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
                            'ruangan_id' => $row[1] ?: null,
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
                <form action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.import-execute') }}" method="POST"
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

                    <button type="submit" id="simpan-btn" class="mp-btn primary md !py-2.5" {{ count($payloadArray) === 0 ? 'disabled' : '' }}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="mr-1" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan <span id="simpan-btn-counter" class="mx-1">{{ count($payloadArray) }}</span> Kelas Rutin
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
            <style>
                .preview-table th,
                .preview-table td {
                    white-space: normal !important;
                    /* Allow text wrapping to prevent forced wide tables */
                }
            </style>
            <div class="mp-card-body">
                <div class="mp-table-wrap"
                    style="max-height: 500px; overflow-y: auto; overflow-x: auto; border: 1px solid #E2E8F0; border-radius: 8px;">
                    <table class="mp-table preview-table" style="position: relative; width: 100%; min-width: 900px;">
                        <thead
                            style="position: sticky; top: 0; z-index: 20; background: #F8FAFC; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                            <tr>
                                <th style="width:44px; text-align:center; background: #F8FAFC;">#</th>
                                <th style="width:100px; background: #F8FAFC;">HARI</th>
                                <th style="width:130px; background: #F8FAFC;">WAKTU</th>
                                <th style="min-width:200px; background: #F8FAFC;">MATA KULIAH</th>
                                <th style="width:100px; background: #F8FAFC; text-align:center;">KELAS</th>
                                <th style="min-width:180px; background: #F8FAFC;">RUANGAN</th>
                                <th style="background: #F8FAFC; width:60px; text-align:center;">AKSI</th>
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
                                    $conflictMsg = $row[10] ?? '';
                                @endphp
                                <tr class="mp-tr" id="preview-row-{{ $loop->index }}"
                                    style="{{ $conflictMsg ? 'background-color: #FEF2F2;' : '' }}">
                                    <td style="text-align:center;" id="icon-container-{{ $loop->index }}">
                                        @if(empty($ruangId))
                                            <div
                                                style="margin:0 auto; width:24px; height:24px; border-radius:6px; background:#FEF3C7; display:flex; align-items:center; justify-content:center; color:#B45309;">
                                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @elseif($conflictMsg)
                                            <div
                                                style="margin:0 auto; width:24px; height:24px; border-radius:6px; background:#FEE2E2; display:flex; align-items:center; justify-content:center; color:#991B1B;">
                                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @else
                                            <div
                                                style="margin:0 auto; width:24px; height:24px; border-radius:6px; background:#D1FAE5; display:flex; align-items:center; justify-content:center; color:#065F46;">
                                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @endif
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
                                        <div id="msg-container-{{ $loop->index }}"
                                            style="font-size:11px; font-weight:600; color:#DC2626; margin-top:2px; display: {{ $conflictMsg ? 'block' : 'none' }};">
                                            <span
                                                class="inline-block w-1.5 h-1.5 rounded-full bg-red-600 mr-1 align-middle"></span><span
                                                id="msg-text-{{ $loop->index }}">{{ $conflictMsg }}</span>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div
                                            style="font-size:13px; font-weight:700; color:#3730A3; display:inline-block; padding:2px 8px; background:#EEF2FF; border-radius:6px; border:1px solid #C7D2FE;">
                                            {{ $kelas ?: '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($ruanganObj)
                                            <div style="font-weight:700; color:#0D0D12;">
                                                {{ $ruanganObj->nama }}
                                                <span
                                                    style="font-size:12px; color:#666D80; margin-left:4px; font-weight:500;">(Lt.
                                                    {{ $ruanganObj->lantai ?? '-' }})</span>
                                            </div>
                                        @else
                                            <div style="font-weight:700; color:#B45309; margin-bottom: 4px; font-size: 11px;">
                                                Tidak Dikenal: <span
                                                    class="bg-orange-100 px-1 rounded">{{ $row[11] ?? '?' }}</span>
                                            </div>
                                            <select
                                                class="mp-input !py-1 !px-2 !text-xs !bg-orange-50 hover:bg-orange-100 border-orange-200 text-orange-900 cursor-pointer"
                                                onchange="updateRuangan({{ $loop->index }}, this.value)"
                                                style="width: 100%; border-radius: 6px;">
                                                <option value="">-- Pilih Manual --</option>
                                                @foreach($ruangans as $r)
                                                    <option value="{{ $r->id }}">{{ $r->nama }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td style="text-align:center; min-width: 90px;">
                                        <div style="display: flex; justify-content: center; align-items: center; gap: 4px;">
                                            <button type="button" @click="openEditModal({{ $loop->index }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                                title="Edit Jadwal">
                                                <svg width="18" height="18" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button type="button" onclick="removeTableRow({{ $loop->index }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                title="Keluarkan dari daftar Simpan">
                                                <svg width="18" height="18" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <script>
                    let rawPayload = {!! json_encode($payloadArray) !!};

                    function updateRuangan(index, newRuanganId) {
                        if (rawPayload[index]) {
                            rawPayload[index].ruangan_id = newRuanganId || null;
                            syncPayload();
                        }
                    }

                    function syncPayload() {
                        let cleanPayload = rawPayload.filter(item => item !== null);
                        document.querySelector('textarea[name="validated_payload"]').value = JSON.stringify(cleanPayload);
                        document.getElementById('simpan-btn-counter').innerText = cleanPayload.length;
                        document.getElementById('simpan-btn').disabled = cleanPayload.length === 0;

                        recalculateCollisions();
                    }

                    function recalculateCollisions() {
                        let tracked = {};
                        rawPayload.forEach((row, idx) => {
                            if (!row) return;

                            let ruangId = row.ruangan_id;
                            let tr = document.getElementById('preview-row-' + idx);
                            let iconContainer = document.getElementById('icon-container-' + idx);
                            let msgContainer = document.getElementById('msg-container-' + idx);
                            let msgText = document.getElementById('msg-text-' + idx);

                            if (!tr) return;

                            if (!ruangId) {
                                tr.style.backgroundColor = '';
                                if (msgContainer) msgContainer.style.display = 'none';
                                if (iconContainer) iconContainer.innerHTML = '<div style="margin:0 auto; width:24px; height:24px; border-radius:6px; background:#FEF3C7; display:flex; align-items:center; justify-content:center; color:#B45309;"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>';
                                return;
                            }

                            let key = ruangId + '-' + row.hari;
                            if (!tracked[key]) tracked[key] = [];

                            let hasConflict = false;
                            let conflictWith = '';

                            for (let exist of tracked[key]) {
                                if (row.jam_mulai < exist.selesai && row.jam_selesai > exist.mulai) {
                                    if (row.mata_kuliah.trim() === exist.matkul.trim()) {
                                        continue;
                                    }
                                    hasConflict = true;
                                    conflictWith = exist.matkul;
                                    break;
                                }
                            }

                            tracked[key].push({
                                mulai: row.jam_mulai,
                                selesai: row.jam_selesai,
                                matkul: row.mata_kuliah
                            });

                            if (hasConflict) {
                                tr.style.backgroundColor = '#FEF2F2';
                                if (msgText) msgText.innerText = 'Konflik jam dengan: ' + conflictWith;
                                if (msgContainer) msgContainer.style.display = 'block';
                                if (iconContainer) iconContainer.innerHTML = '<div style="margin:0 auto; width:24px; height:24px; border-radius:6px; background:#FEE2E2; display:flex; align-items:center; justify-content:center; color:#991B1B;"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>';
                            } else {
                                tr.style.backgroundColor = '';
                                if (msgContainer) msgContainer.style.display = 'none';
                                if (iconContainer) iconContainer.innerHTML = '<div style="margin:0 auto; width:24px; height:24px; border-radius:6px; background:#D1FAE5; display:flex; align-items:center; justify-content:center; color:#065F46;"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg></div>';
                            }
                        });
                    }

                    function removeTableRow(index) {
                        // Sembunyikan baris tabel secara visual
                        let rowEl = document.getElementById('preview-row-' + index);
                        if (rowEl) {
                            rowEl.remove();
                        }

                        // Kosongkan indeks terkait dari keranjang payload asli
                        rawPayload[index] = null;

                        // Filter dan update textarea
                        syncPayload();
                    }
                </script>

                @if(count($csvData) === 0)
                    <div class="py-12 text-center text-gray-500 font-medium">
                        Tidak ada data yang dapat dibaca. Pastikan format CSV sesuai dengan template (tanpa header baris).
                    </div>
                @endif
            </div>
        </div>

        <!-- EVAL EDIT MODAL -->
        <div x-show="editModalOpen" style="display:none;" class="fixed inset-0 z-[60] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition.opacity
                    class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm"
                    @click="editModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="editModalOpen" x-transition
                    class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 relative z-[70]">
                    <h3 class="text-[16px] font-bold text-gray-900 mb-4 font-['Inter_Tight']">Edit Baris Jadwal</h3>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Hari</label>
                            <select x-model="editData.hari" class="mp-input text-[14px]">
                                <option value="1">Senin</option>
                                <option value="2">Selasa</option>
                                <option value="3">Rabu</option>
                                <option value="4">Kamis</option>
                                <option value="5">Jumat</option>
                                <option value="6">Sabtu</option>
                                <option value="7">Minggu</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Ruangan</label>
                            <select x-model="editData.ruangan_id" class="mp-input text-[14px]">
                                <option value="">-- Kosong / Pilih Nanti --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mata
                                Kuliah</label>
                            <input type="text" x-model="editData.mata_kuliah" class="mp-input text-[14px] w-full">
                        </div>
                        <div>
                            <label
                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kelas</label>
                            <input type="text" x-model="editData.kelas" class="mp-input text-[14px] w-full">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Jam
                                    Mulai</label>
                                <input type="time" x-model="editData.jam_mulai" class="mp-input text-[14px]">
                            </div>
                            <div>
                                <label
                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Jam
                                    Selesai</label>
                                <input type="time" x-model="editData.jam_selesai" class="mp-input text-[14px]">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="editModalOpen = false" class="mp-btn secondary md">Batal</button>
                        <button type="button" @click="saveEdit()" class="mp-btn primary md">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- Close Alpine x-data wrapper -->

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sandboxEditor', () => ({
                editModalOpen: false,
                editIndex: null,
                editData: { hari: 1, ruangan_id: '', jam_mulai: '', jam_selesai: '', mata_kuliah: '', kelas: '' },
                ruanganMap: {
                    @foreach($ruangans as $r)
                           "{{ $r->id }}": '{{ addslashes($r->nama) }}',
                    @endforeach
                },

            openEditModal(index) {
            this.editIndex = index;
            let row = rawPayload[index];
            this.editData = {
                hari: row.hari,
                ruangan_id: row.ruangan_id || '',
                jam_mulai: row.jam_mulai.substring(0, 5),
                jam_selesai: row.jam_selesai.substring(0, 5),
                mata_kuliah: row.mata_kuliah,
                kelas: row.kelas
            };
            this.editModalOpen = true;
        },

            saveEdit() {
                let row = rawPayload[this.editIndex];
                if(!row) return;

                row.hari = parseInt(this.editData.hari);
                row.ruangan_id = this.editData.ruangan_id ? parseInt(this.editData.ruangan_id) : null;
                row.mata_kuliah = this.editData.mata_kuliah;
                row.kelas = this.editData.kelas;

                let tsMulai = this.editData.jam_mulai + (this.editData.jam_mulai.length === 5 ? ':00' : '');
                let tsSelesai = this.editData.jam_selesai + (this.editData.jam_selesai.length === 5 ? ':00' : '');

                row.jam_mulai = tsMulai;
                row.jam_selesai = tsSelesai;

                syncPayload();

                    // Reactive DOM text modification
                    let tr = document.getElementById('preview-row-' + this.editIndex);
                let hariStr =['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][row.hari];
                if(tr && tr.cells[1]) {
            tr.cells[1].innerHTML = '<div style="font-weight:600; color:#3730A3;">' + hariStr + '</div>';
            tr.cells[2].innerHTML = '<div style="font-weight:700; color:#0D0D12;">' + (tsMulai.substring(0, 5)) + ' - ' + (tsSelesai.substring(0, 5)) + '</div>';

            // Kolom Matkul (cells[3])
            let msgBox = tr.cells[3].querySelector('div[id^="msg-container"]');
            tr.cells[3].innerHTML = '<div style="font-size:13px; font-weight:700; color:#0D0D12;">' + row.mata_kuliah + '</div>';
            if (msgBox) tr.cells[3].appendChild(msgBox); // kembalikan conflict message box

            // Kolom Kelas (cells[4])
            tr.cells[4].innerHTML = '<div style="font-size:13px; font-weight:700; color:#3730A3; display:inline-block; padding:2px 8px; background:#EEF2FF; border-radius:6px; border:1px solid #C7D2FE;">' + (row.kelas || '-') + '</div>';

            let rNama = this.ruanganMap[row.ruangan_id] || '<div style="font-weight:700; color:#B45309; font-size:11px;">Tidak Dikenal</div>';
            tr.cells[5].innerHTML = '<div style="font-weight:700; color:#0D0D12;">' + rNama + '</div>';
        }

        this.editModalOpen = false;
                }
            }));
        });
    </script>
</x-eoffice::manajemen-ruangan.layout>
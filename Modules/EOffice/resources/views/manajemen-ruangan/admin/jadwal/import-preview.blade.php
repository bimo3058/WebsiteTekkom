<x-eoffice::manajemen-ruangan.layout pageTitle="Pratinjau Impor Jadwal Kuliah">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

    <div x-data="sandboxEditor()">
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
            $validCountInit = count(array_filter($payloadArray, fn($r) => !empty($r['ruangan_id'])));
            $invalidCountInit = count($payloadArray) - $validCountInit;
        @endphp

        <!-- Back Button Area -->
        <div class="mb-5">
            <a href="{{ route('eoffice.peminjaman.admin.jadwal-akademik.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 bg-slate-100 hover:bg-slate-200 hover:text-gray-900 px-3 py-1.5 rounded-full transition-all shadow-sm w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Jadwal
            </a>
        </div>

        <div class="mp-page-header flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="mp-page-title">Pratinjau Sandbox
                    <span
                        class="ml-2 inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Belum
                        Disimpan</span>
                </h1>
                <p class="mp-page-sub">Tinjau ulang data kelas di bawah ini sebelum disimpan permanen ke database.</p>
            </div>

            <div class="w-full md:w-auto mt-2 md:mt-0">
                <form action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.import-execute') }}" method="POST"
                    id="executeImportForm"
                    class="flex flex-col md:flex-row md:items-center gap-3 bg-white p-3 md:p-2 rounded-xl border border-gray-200 shadow-sm w-full md:w-auto">
                    @csrf
                    <textarea name="validated_payload" class="hidden">{{ json_encode($payloadArray) }}</textarea>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-2 w-full md:w-auto">
                        <div class="flex flex-col text-left w-full sm:w-auto">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1 mb-0.5">Mulai Semester</label>
                            <input type="date" name="tgl_mulai_efektif_global" required
                                class="mp-input !py-1.5 !text-xs !bg-gray-50 border-none shadow-inner w-full sm:w-[130px] md:w-[130px] rounded-lg">
                        </div>
                        <div class="flex flex-col text-left w-full sm:w-auto">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1 mb-0.5">Akhir Semester</label>
                            <input type="date" name="tgl_selesai_efektif_global" required
                                class="mp-input !py-1.5 !text-xs !bg-gray-50 border-none shadow-inner w-full sm:w-[130px] md:w-[130px] rounded-lg">
                        </div>
                    </div>

                    <div class="hidden md:block h-8 w-px bg-gray-200 mx-1"></div>

                    <button type="submit" id="simpan-btn" class="mp-btn primary md shadow-sm cursor-pointer w-full md:w-auto justify-center" {{ $validCountInit === 0 ? 'disabled' : '' }}>
                        Simpan <span id="simpan-btn-counter" class="mx-1">{{ $validCountInit }}</span> Jadwal
                    </button>
                </form>
            </div>
        </div>

        <!-- Alert Block -->
        <div
            class="mt-4 mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative text-[13px] font-medium shadow-sm">
            <strong class="font-bold mr-1">Sandbox Mode:</strong>
            <span id="alert-text-summary">
                {{ count($csvData) }} baris terbaca. <strong>{{ $validCountInit }}</strong> disimpan (hijau & merah), dan <strong>{{ $invalidCountInit }}</strong> jadwal tidak disimpan karena ruangan tidak tersedia (kuning).
            </span>
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
                                            style="font-size:13px; font-weight:700; color:#3730A3; display:inline-block; padding:2px 8px; background:#EBEDF6; border-radius:6px; border:1px solid #C7D2FE;">
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
                                            <div x-data="{ 
                                                    open: false,
                                                    search: '',
                                                    ruanganId: '',
                                                    rooms: [
                                                        @foreach($ruangans as $r)
                                                            {id: '{{ $r->id }}', name: '{{ addslashes($r->nama) }}'},
                                                        @endforeach
                                                    ],
                                                    get filteredRooms() {
                                                        if (this.search === '') return this.rooms;
                                                        return this.rooms.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()));
                                                    },
                                                    selectItem(id) { 
                                                        this.ruanganId = id;
                                                        this.open = false;
                                                        if (id) {
                                                            const room = this.rooms.find(r => r.id == id);
                                                            this.search = room ? room.name : '';
                                                        } else {
                                                            this.search = '';
                                                        }
                                                        updateRuangan({{ $loop->index }}, id);
                                                    },
                                                    init() {
                                                        this.$watch('search', (val) => {
                                                            if (val === '') {
                                                                if (this.ruanganId !== '') {
                                                                    this.ruanganId = '';
                                                                    updateRuangan({{ $loop->index }}, '');
                                                                }
                                                            } else {
                                                                const currentRoom = this.rooms.find(r => r.id == this.ruanganId);
                                                                if (currentRoom && val !== currentRoom.name) {
                                                                    this.ruanganId = '';
                                                                    updateRuangan({{ $loop->index }}, '');
                                                                }
                                                            }
                                                        });
                                                    }
                                                }" class="w-full" @click.away="open = false; if(!ruanganId) search = ''; else search = rooms.find(r => r.id == ruanganId)?.name || ''">
                                                
                                                <div :class="{'text-[#B45309]': !ruanganId, 'text-gray-400': ruanganId}" style="font-weight:700; margin-bottom: 4px; font-size: 11px; transition: all 0.2s;">
                                                    <span x-text="ruanganId ? 'Semula:' : 'Tidak Dikenal:'"></span> <span
                                                        :class="{'bg-orange-100 px-1 rounded': !ruanganId, 'line-through opacity-70': ruanganId}">{{ $row[11] ?? '?' }}</span>
                                                </div>

                                                <div class="relative w-full">
                                                    <div class="relative flex items-center">
                                                        <input type="text" x-model="search" @click="open = true" @focus="open = true"
                                                            placeholder="-- Pilih Manual --" autocomplete="off"
                                                            class="w-full flex items-center justify-between mp-input !py-1 !pl-2 !pr-7 !text-xs border-orange-200 focus:outline-none focus:ring-1 focus:ring-orange-400 transition-colors rounded-md h-[26px] font-semibold"
                                                            :class="{'!bg-orange-50 hover:bg-orange-100 text-orange-900': !ruanganId, '!bg-gray-50 border-gray-200 text-gray-700 focus:ring-gray-300': ruanganId}">
                                                        <div class="absolute right-2 pointer-events-none transition-transform duration-200 shrink-0"
                                                            :class="{'rotate-180': open, 'text-orange-700': !ruanganId, 'text-gray-400': ruanganId}">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    
                                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                        class="absolute left-0 top-full mt-1 w-full min-w-[200px] bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-56 flex flex-col overflow-hidden" style="display: none;">
                                                        
                                                        <div class="p-1 flex flex-col overflow-y-auto overflow-x-hidden flex-1 min-h-0">
                                                            <button type="button" @click="selectItem('')" x-show="search === ''"
                                                                class="w-full text-left px-2 py-1.5 text-[11px] font-medium rounded-md transition-colors" 
                                                                :class="{'bg-orange-50 text-orange-900 font-bold': ruanganId == '', 'text-gray-700 hover:bg-gray-50': ruanganId != ''}">-- Kosongkan --</button>
                                                            
                                                            <template x-for="r in filteredRooms" :key="r.id">
                                                                <button type="button" @click="selectItem(r.id)" 
                                                                    class="w-full text-left px-2 py-1.5 mt-0.5 text-[11px] font-medium rounded-md transition-colors whitespace-normal break-words" 
                                                                    :class="{'bg-orange-50 text-orange-900 font-bold': ruanganId == r.id, 'text-gray-700 hover:bg-gray-50': ruanganId != r.id}"
                                                                    x-text="r.name">
                                                                </button>
                                                            </template>

                                                            <div x-show="filteredRooms.length === 0" class="px-2 py-3 text-[11px] text-center text-gray-500">
                                                                Ruangan tidak ditemukan
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align:center; min-width: 90px;">
                                        <div style="display: flex; justify-content: center; align-items: center; gap: 4px;">
                                            <button type="button" @click="openEditModal({{ $loop->index }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-primary-500 hover:bg-primary-50 transition-colors"
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
                        
                        let validCount = cleanPayload.filter(item => item.ruangan_id).length;
                        let invalidCount = cleanPayload.length - validCount;
                        
                        let counterEl = document.getElementById('simpan-btn-counter');
                        if (counterEl) counterEl.innerText = validCount;
                        
                        let btnEl = document.getElementById('simpan-btn');
                        if (btnEl) btnEl.disabled = (validCount === 0);
                        
                        let alertText = document.getElementById('alert-text-summary');
                        if (alertText) {
                            alertText.innerHTML = cleanPayload.length + ' baris terbaca. <strong>' + validCount + '</strong> disimpan (hijau & merah), dan <strong>' + invalidCount + '</strong> jadwal tidak disimpan karena ruangan tidak tersedia (kuning).';
                        }

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
        <div x-show="editModalOpen" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6" 
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <!-- 1. BACKDROP OVERLAY -->
            <div x-show="editModalOpen" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" 
                aria-hidden="true" @click="editModalOpen = false"></div>

            <!-- 2. MODAL PANEL -->
            <div x-show="editModalOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-md max-h-[90dvh] sm:max-h-[90vh] flex flex-col overflow-visible">
                
                <div class="p-3 sm:p-6 flex flex-col flex-1 min-h-0">

                    <!-- Modal Header -->
                    <div class="-mx-3 sm:-mx-6 -mt-3 sm:-mt-6 mb-3 sm:mb-5 px-4 sm:px-6 py-3 sm:py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] rounded-none sm:rounded-t-[20px]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[#0B266E] flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-[13px] sm:text-sm font-bold text-[#1A1C1E] tracking-tight">Edit Baris Jadwal</h3>
                                <p class="text-[9px] sm:text-[10px] text-[#0B266E] font-medium">Ubah detail baris jadwal akademik</p>
                            </div>
                        </div>
                        <button type="button" @click="editModalOpen = false" class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                        </button>
                    </div>

                    <!-- Modal Content (Scrollable) -->
                    <div class="space-y-3 sm:space-y-4 overflow-y-auto overflow-x-hidden p-1 -m-1 pb-1 flex-1 whitespace-normal mt-1 sm:mt-2">
                        
                        <!-- RUANGAN -->
                        <div class="space-y-1.5 px-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ruangan</label>
                            <div x-data="{ 
                                    open: false,
                                    search: '',
                                    rooms: [
                                        @foreach($ruangans as $r)
                                            {id: '{{ $r->id }}', name: '{{ addslashes($r->nama) }} - Lt. {{ $r->lantai }}'},
                                        @endforeach
                                    ],
                                    get filteredRooms() {
                                        if (this.search === '') return this.rooms;
                                        return this.rooms.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    selectItem(id) { 
                                        editData.ruangan_id = id;
                                        this.open = false; 
                                        if (id) {
                                            const room = this.rooms.find(r => r.id == id);
                                            this.search = room ? room.name : '';
                                        } else {
                                            this.search = '';
                                        }
                                    },
                                    init() {
                                        this.$watch('search', (val) => {
                                            if (val === '') {
                                                editData.ruangan_id = '';
                                            } else {
                                                const currentRoom = this.rooms.find(r => r.id == editData.ruangan_id);
                                                if (currentRoom && val !== currentRoom.name) {
                                                    editData.ruangan_id = '';
                                                }
                                            }
                                        });
                                        this.$watch('editData.ruangan_id', (val) => {
                                            if (!val) {
                                                this.search = '';
                                            } else {
                                                const room = this.rooms.find(r => r.id == val);
                                                if(room) this.search = room.name;
                                            }
                                        });
                                        // initial state sync
                                        if (editData.ruangan_id) {
                                            const room = this.rooms.find(r => r.id == editData.ruangan_id);
                                            if(room) this.search = room.name;
                                        }
                                    }
                                }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}" 
                                @click.away="open = false; if(!editData.ruangan_id) search = ''; else search = rooms.find(r => r.id == editData.ruangan_id)?.name || ''">
                                
                                <div class="relative flex items-center">
                                    <input type="text" x-model="search" @click="open = true" @focus="open = true"
                                        placeholder="-- Kosong / Pilih Nanti --" autocomplete="off"
                                        class="w-full bg-white border border-slate-200 rounded-xl pl-3 pr-10 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px] text-gray-800 shadow-sm">
                                    <div class="absolute right-3 pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4 transition-transform duration-200"
                                            :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                    class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[80] max-h-48 flex flex-col overflow-hidden" style="display: none;">
                                    <div class="p-1 flex flex-col overflow-y-auto overflow-x-hidden flex-1 min-h-0">
                                        <button type="button" @click="selectItem('')" x-show="search === ''"
                                            class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words" 
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.ruangan_id == '', 'text-gray-700 hover:bg-gray-50': editData.ruangan_id != ''}">-- Kosong / Pilih Nanti --</button>
                                        
                                        <template x-for="r in filteredRooms" :key="r.id">
                                            <button type="button" @click="selectItem(r.id)" 
                                                class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words" 
                                                :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.ruangan_id == r.id, 'text-gray-700 hover:bg-gray-50': editData.ruangan_id != r.id}"
                                                x-text="r.name">
                                            </button>
                                        </template>

                                        <div x-show="filteredRooms.length === 0" class="px-3 py-4 text-xs text-center text-gray-500">
                                            Ruangan tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MATA KULIAH -->
                        <div class="space-y-1.5 px-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mata Kuliah</label>
                            <input type="text" x-model="editData.mata_kuliah" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-sm">
                        </div>

                        <!-- KELAS -->
                        <div class="space-y-1.5 px-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kelas</label>
                            <input type="text" x-model="editData.kelas" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-sm">
                        </div>

                        <!-- HARI -->
                        <div class="space-y-1 sm:space-y-1.5 px-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Hari</label>
                            <div x-data="{ 
                                    open: false,
                                    get selectedName() {
                                        const map = {
                                            1: 'Senin', 2: 'Selasa', 3: 'Rabu',
                                            4: 'Kamis', 5: 'Jumat', 6: 'Sabtu', 7: 'Minggu'
                                        };
                                        return map[editData.hari] || 'Pilih Hari...';
                                    },
                                    selectItem(val) { 
                                        editData.hari = val;
                                        this.open = false; 
                                    } 
                                }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}" @click.away="open = false">

                                <button type="button" @click="open = !open" 
                                    class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl focus:outline-none transition-colors h-[42px] px-3 shadow-sm">
                                    <span x-text="selectedName" class="truncate text-gray-800 text-xs font-medium"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                    class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[80] overflow-hidden" style="display: none;">
                                    <div class="p-1">
                                        <button type="button" @click="selectItem(1)" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.hari == 1, 'text-gray-700 hover:bg-gray-50': editData.hari != 1}">Senin</button>
                                        <button type="button" @click="selectItem(2)" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.hari == 2, 'text-gray-700 hover:bg-gray-50': editData.hari != 2}">Selasa</button>
                                        <button type="button" @click="selectItem(3)" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.hari == 3, 'text-gray-700 hover:bg-gray-50': editData.hari != 3}">Rabu</button>
                                        <button type="button" @click="selectItem(4)" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.hari == 4, 'text-gray-700 hover:bg-gray-50': editData.hari != 4}">Kamis</button>
                                        <button type="button" @click="selectItem(5)" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.hari == 5, 'text-gray-700 hover:bg-gray-50': editData.hari != 5}">Jumat</button>
                                        <button type="button" @click="selectItem(6)" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.hari == 6, 'text-gray-700 hover:bg-gray-50': editData.hari != 6}">Sabtu</button>
                                        <button type="button" @click="selectItem(7)" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': editData.hari == 7, 'text-gray-700 hover:bg-gray-50': editData.hari != 7}">Minggu</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- WAKTU -->
                        <div class="grid grid-cols-2 gap-3 px-1">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam Mulai</label>
                                <div class="relative">
                                    <input type="time" x-model="editData.jam_mulai" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-sm">
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam Selesai</label>
                                <div class="relative">
                                    <input type="time" x-model="editData.jam_selesai" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Footer (Inside scrollable area so it doesn't squish content on small screens) -->
                        <div class="sm:hidden mt-6 pt-4 pb-2 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button" @click="editModalOpen = false" class="text-[11px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest px-4 py-2 transition-colors cursor-pointer">Batal</button>
                            <button type="button" @click="saveEdit()" class="bg-[#0B266E] hover:bg-[#091F5E] text-white text-[11px] font-bold uppercase tracking-widest px-6 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">Simpan Perubahan</button>
                        </div>
                    </div>

                    <!-- Desktop Footer (Sticky) -->
                    <div class="hidden sm:flex mt-4 -mx-4 sm:-mx-6 -mb-4 sm:-mb-6 px-6 py-4 border-t border-slate-100 bg-slate-50/50 items-center justify-end gap-3 shrink-0 rounded-b-[20px]">
                        <button type="button" @click="editModalOpen = false" class="text-[11px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest px-4 py-2 transition-colors cursor-pointer">Batal</button>
                        <button type="button" @click="saveEdit()" class="bg-[#0B266E] hover:bg-[#091F5E] text-white text-[11px] font-bold uppercase tracking-widest px-6 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">Simpan Perubahan</button>
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
            tr.cells[4].innerHTML = '<div style="font-size:13px; font-weight:700; color:#3730A3; display:inline-block; padding:2px 8px; background:#EBEDF6; border-radius:6px; border:1px solid #C7D2FE;">' + (row.kelas || '-') + '</div>';

            let rNama = this.ruanganMap[row.ruangan_id] || '<div style="font-weight:700; color:#B45309; font-size:11px;">Tidak Dikenal</div>';
            tr.cells[5].innerHTML = '<div style="font-weight:700; color:#0D0D12;">' + rNama + '</div>';
        }

        this.editModalOpen = false;
                }
            }));
        });
    </script>
</x-eoffice::manajemen-ruangan.layout>
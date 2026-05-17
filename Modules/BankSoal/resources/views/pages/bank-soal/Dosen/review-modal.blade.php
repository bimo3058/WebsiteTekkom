<!-- Review Soal Modal -->
<div id="reviewSoalModal" class="fixed inset-0 z-[110] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div id="reviewSoalModalContent" class="relative w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 flex flex-col max-h-[95vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Review Soal Terpilih</h3>
                <p class="text-sm text-slate-500 mt-1">Lengkapi detail ujian dan periksa daftar soal yang telah diacak.</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="closeReviewModal()">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="px-0 py-0 overflow-y-auto flex-1 flex flex-col">
            <form action="{{ route('banksoal.soal.dosen.cetak-ujian') }}" method="POST" id="formCetakUjian" class="flex flex-col flex-1" target="_blank">
                @csrf
                <input type="hidden" name="mk_id" id="reviewMkId" value="">
                <input type="hidden" name="bobot_total" id="reviewBobotTotal" value="">
                <input type="hidden" name="soal_json" id="reviewSoalJson" value="[]">
                <input type="hidden" name="nama_ekstraksi" id="reviewNamaEkstraksi" value="">
                
                <div class="px-6 py-5 border-b border-slate-200 bg-white sticky top-0 z-10">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Mata Kuliah</label>
                            <input type="text" id="reviewMkNama" readonly class="w-full bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none py-2 px-3 text-slate-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Agenda</label>
                            <select name="agenda" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                                <option value="Ujian Akhir Semester (UAS)">Ujian Akhir Semester (UAS)</option>
                                <option value="Ujian Tengah Semester (UTS)">Ujian Tengah Semester (UTS)</option>
                                <option value="Kuis">Kuis</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" value="{{ date('Y') }}/{{ date('Y')+1 }}" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Semester</label>
                            <select name="semester" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap" selected>Genap</option>
                                <option value="Antara">Antara</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Hari/Tanggal</label>
                            <input type="text" name="hari_tanggal" id="inputHariTanggal" placeholder="Pilih tanggal..." readonly class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam</label>
                            <div class="flex items-center gap-2">
                                <input type="time" name="jam_mulai" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                                <span class="text-slate-400 text-sm">–</span>
                                <input type="time" name="jam_selesai" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ruang Ujian</label>
                            <input type="text" name="ruang_ujian" placeholder="Contoh: Lab A" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sifat Ujian</label>
                            <select name="sifat_ujian" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                                <option value="Tertutup">Tertutup</option>
                                <option value="Terbuka">Terbuka</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Ujian</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="online" checked class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-600">
                                    <span class="text-sm text-slate-600">Terkomputerisasi (Online)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="offline" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-600">
                                    <span class="text-sm text-slate-600">Cetak Kertas (Offline)</span>
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Jika mode <b>Cetak Kertas</b> dipilih, soal akan otomatis diteruskan ke antrean tugas Admin Cetak.</p>
                        </div>
                    </div>

                    <!-- Archive mode and note removed per UX: always save draft on finalize -->
                </div>

                <!-- List Soal -->
                <div class="px-6 py-5 bg-slate-50/80 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-900 text-sm"><i class="fas fa-list-ul mr-2 text-slate-400"></i>Daftar Soal</h3>
                        <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-md shadow-sm" id="soalCountBadge">0 Soal</span>
                    </div>

                    <div class="space-y-3" id="soalListContainer">
                        <!-- Soal items will be injected here by JS -->
                    </div>

                    <div class="mt-4 border-2 border-dashed border-slate-300 rounded-xl bg-white hover:bg-slate-50 transition-colors cursor-pointer text-center py-4 group" onclick="openManualInsertModal()">
                        <span class="text-slate-500 text-sm font-semibold flex items-center justify-center gap-2 group-hover:text-primary transition-colors">
                            <i class="fas fa-plus bg-slate-100 group-hover:bg-primary/10 rounded-full w-6 h-6 flex items-center justify-center"></i> Tambah Soal Manual
                        </span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
                <div class="px-6 py-4 border-t border-slate-200 bg-white rounded-b-2xl flex items-center justify-between z-10">
            <button type="button" onclick="closeReviewModal()" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">
                Batalkan
            </button>
            <div class="flex items-center gap-3">
                <button type="button" onclick="ulangAcakSoal()" class="px-4 py-2.5 bg-primary/10 text-primary font-semibold text-sm rounded-lg hover:bg-primary/20 transition-colors flex items-center gap-2 border border-primary/20">
                    <i class="fas fa-sync-alt"></i> Buat Ulang Acak
                </button>
                <button type="button" id="finalizePrintBtn" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm rounded-lg shadow-md transition-colors flex items-center gap-2" onclick="finalizeAndAskArchive(event)">
                    <i class="fas fa-print"></i> Simpan Ujian & Cetak
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Manual Insert Soal Modal (Nested) -->
<div id="manualInsertModal" class="fixed inset-0 z-[120] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/60 backdrop-blur-[2px] p-4 transition-opacity duration-300 opacity-0">
    <div id="manualInsertContent" class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out flex flex-col max-h-[85vh] translate-y-8 opacity-0">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Pilih Soal Manual</h3>
                <p class="text-xs text-slate-500 mt-1">Tambahkan soal spesifik ke daftar ujian Anda.</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="closeManualInsertModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body / Loading state -->
        <div id="manualInsertLoading" class="p-8 flex flex-col items-center justify-center space-y-3">
             <i class="fas fa-circle-notch fa-spin text-3xl text-primary"></i>
             <span class="text-sm font-medium text-slate-500 animate-pulse">Memuat bank soal...</span>
        </div>

        <!-- List -->
        <div id="manualInsertList" class="px-6 py-4 overflow-y-auto flex-1 space-y-3 hidden bg-slate-50">
            <!-- Soal items will be injected here via Fetch -->
        </div>

        <!-- Footer -->
        <div class="px-6 py-3 border-t border-slate-200 flex justify-end bg-white rounded-b-2xl">
            <button type="button" onclick="closeManualInsertModal()" class="px-5 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">
                Kembali
            </button>
        </div>
    </div>
</div>

<script>
    let lastPenarikanId = null;
    let archiveConfirmPending = false;

    function openReviewModal(data) {
        const modal = document.getElementById('reviewSoalModal');
        const modalContent = document.getElementById('reviewSoalModalContent');
        
        // Setup forms
        document.getElementById('reviewMkId').value = data.mataKuliah.id;
        document.getElementById('reviewBobotTotal').value = data.bobot_total || '';
        document.getElementById('reviewMkNama').value = data.mataKuliah.nama;
        document.getElementById('reviewSoalJson').value = JSON.stringify(data.soals || []);
        document.getElementById('reviewNamaEkstraksi').value = `${data.mataKuliah.nama} - ${document.querySelector('select[name="agenda"]')?.value || 'Ekstraksi'}`;
        
        // Build Soal List
        const container = document.getElementById('soalListContainer');
        container.innerHTML = '';
        
        if (data.soals && data.soals.length > 0) {
            data.soals.forEach((soal, index) => {
                const soalId = String(soal.id).padStart(3, '0');
                let badges = '';
                if (soal.cpl) {
                    badges += `<span class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold rounded uppercase">${soal.cpl}</span>`;
                }
                if (soal.cpmk) {
                    badges += `<span class="px-1.5 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-600 text-[10px] font-semibold rounded uppercase">${soal.cpmk}</span>`;
                }
                
                container.innerHTML += `
                    <div class="card-soal overflow-hidden border border-slate-200 rounded-xl bg-white shadow-sm flex items-start p-4 transition-all" id="soal-row-${soal.id}">
                        <input type="hidden" name="soal_ids[]" value="${soal.id}">
                        
                        <div class="flex-shrink-0 w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center font-bold text-slate-600 mr-4 number-badge text-sm">
                            ${index + 1}
                        </div>
                        
                        <div class="flex-1 min-w-0 pr-4">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="font-bold text-slate-800 text-sm">Q-${soalId}</span>
                                ${badges}
                            </div>
                            <div class="text-sm text-slate-600 leading-relaxed max-h-32 overflow-y-auto prose prose-sm prose-slate max-w-none prose-img:max-h-24 prose-img:w-auto prose-img:rounded-md mt-2">
                                ${soal.soal}
                            </div>
                        </div>

                        <button type="button" onclick="removeSoal(${soal.id})" class="flex-shrink-0 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors p-2 mt-1">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                `;
            });
        } else {
            container.innerHTML = `<div class="text-center py-8 text-slate-500 font-medium text-sm border-2 border-dashed border-slate-200 rounded-xl bg-white">Daftar soal kosong.</div>`;
        }
        
        updateSoalCount();
        
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
    }

    // Finalize: open print tab, then show archive confirmation when user returns to the app
    async function finalizeAndAskArchive(e) {
        e?.preventDefault?.();
        const form = document.getElementById('formCetakUjian');
        if (!form) return;

        // Submit form to new tab (finalize & print)
        archiveConfirmPending = true;
        form.submit();

        // Close modal shortly after
        setTimeout(() => closeReviewModal(), 500);
    }

    function closeReviewModal() {
        const modal = document.getElementById('reviewSoalModal');
        const modalContent = document.getElementById('reviewSoalModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    async function handleSimpanKeArsip(event, forceDirect) {
        event?.preventDefault?.();

        const form = document.getElementById('formCetakUjian');
        const formData = new FormData(form);
        formData.set('soal_json', document.getElementById('reviewSoalJson').value || '[]');

        // If forceDirect provided, override select value (used by confirmation dialog)
        const arsipMode = typeof forceDirect !== 'undefined' ? String(forceDirect) : (document.getElementById('reviewDirectArchive')?.value || '0');
        formData.set('direct_archive', arsipMode);
        formData.set('nama_ekstraksi', document.getElementById('reviewNamaEkstraksi').value || 'Ekstraksi Soal');

        if (arsipMode === '1' && lastPenarikanId) {
            formData.set('penarikan_id', String(lastPenarikanId));
        }

        const arsipStoreUrl = "{{ route('banksoal.soal.dosen.arsip-soal.store') }}";

        showLoading(arsipMode === '1' ? 'Mengarsipkan paket soal...' : 'Menyimpan draf penarikan...');

        try {
            const response = await fetch(arsipStoreUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData,
            });

            const data = await response.json();

                if (data && data.success) {
                    archiveConfirmPending = false;
                    if (arsipMode === '0' && data.data && data.data.id) {
                        lastPenarikanId = data.data.id;
                    }
                    showSnackbar(data.message || 'Soal tersimpan.', 'success');
                    // If this was a draft save triggered by finalize, we intentionally do not close the modal here
                    // because finalize already closes it. But if user invoked save from review, close it.
                    if (!document.getElementById('reviewSoalModal')?.classList.contains('hidden')) {
                        // modal is still open (user-triggered save)
                        closeReviewModal();
                    }
                    return true;
                } else {
                    showSnackbar((data && data.message) ? data.message : 'Gagal menyimpan.', 'error');
                    return false;
                }
        } catch (err) {
            console.error('Save error', err);
            showSnackbar('Gagal menyimpan. Periksa koneksi.', 'error');
                return false;
            } finally {
            hideLoading();
        }
            // fallback
            return false;
    }
    
    function removeSoal(id) {
        const row = document.getElementById('soal-row-' + id);
        if (row) {
            row.style.opacity = '0';
            row.style.transform = 'scale(0.95)';
            
            let jsonVal = document.getElementById('reviewSoalJson').value;
            try {
                let arr = JSON.parse(jsonVal);
                arr = arr.filter(s => String(s.id) !== String(id));
                document.getElementById('reviewSoalJson').value = JSON.stringify(arr);
            } catch(e){}

            setTimeout(() => {
                row.remove();
                updateSoalCount();
                updateBadgeNumbers();
            }, 200);
        }
    }

    function updateSoalCount() {
        const count = document.querySelectorAll('#soalListContainer .card-soal').length;
        const badge = document.getElementById('soalCountBadge');
        if (badge) {
            badge.innerText = count + ' Soal';
        }
        
        if(count === 0 && document.getElementById('soalListContainer')) {
            document.getElementById('soalListContainer').innerHTML = `<div class="text-center py-8 text-slate-500 font-medium text-sm border-2 border-dashed border-slate-200 rounded-xl bg-white">Daftar soal kosong.</div>`;
        }
    }

    function updateBadgeNumbers() {
        const badges = document.querySelectorAll('#soalListContainer .number-badge');
        badges.forEach((badge, index) => {
            badge.innerText = index + 1;
        });
    }
    
    function ulangAcakSoal() {
        // Trigger AJAX fetch again
        const form = document.getElementById('formTarikSoal');
        if (form) {
            // Kita bisa membuat tombolnya berputar loading, atau langsung submit ajax
            handleTarikSoalSubmit(new Event('submit'), form);
        }
    }

    // Modal Insert Manual logic
    function openManualInsertModal() {
        const mkId = document.getElementById('reviewMkId').value;
        const modal = document.getElementById('manualInsertModal');
        const content = document.getElementById('manualInsertContent');
        const listDiv = document.getElementById('manualInsertList');
        const loadDiv = document.getElementById('manualInsertLoading');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'translate-y-8');
            content.classList.add('opacity-100', 'translate-y-0');
        }, 10);

        listDiv.classList.add('hidden');
        loadDiv.classList.remove('hidden');
        loadDiv.classList.add('flex');

        fetch(`/bank-soal/soal/dosen/get-by-mk/${mkId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                loadDiv.classList.add('hidden');
                loadDiv.classList.remove('flex');
                listDiv.classList.remove('hidden');
                
                listDiv.innerHTML = '';
                
                if (data.success && data.soals.length > 0) {
                    // Check existing IDs
                    const existingInputs = document.querySelectorAll('input[name="soal_ids[]"]');
                    const existingIds = Array.from(existingInputs).map(inp => parseInt(inp.value));

                    let totalAvailable = 0;

                    data.soals.forEach(soal => {
                        const isAdded = existingIds.includes(soal.id);
                        if (isAdded) return; // Skip if already added
                        
                        totalAvailable++;

                        let badges = '';
                        if (soal.cpl) badges += `<span class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold rounded uppercase">${soal.cpl}</span>`;
                        if (soal.cpmk) badges += `<span class="px-1.5 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-600 text-[10px] font-semibold rounded uppercase">${soal.cpmk}</span>`;

                        const cleanSoalRaw = soal.soal.replace(/"/g, '&quot;');

                        listDiv.innerHTML += `
                            <div class="card-soal overflow-hidden border border-slate-200 rounded-lg bg-white shadow-sm flex items-start p-3 hover:border-primary/20 transition-colors" id="manual-soal-${soal.id}">
                                <div class="flex-1 min-w-0 pr-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-slate-800 text-sm">Q-${String(soal.id).padStart(3, '0')}</span>
                                        ${badges}
                                    </div>
                                    <p class="text-sm text-slate-600 line-clamp-3">${cleanSoalRaw}</p>
                                </div>
                                <button type="button" onclick="insertManualSoal(${soal.id}, '${cleanSoalRaw}', '${soal.cpl || ''}', '${soal.cpmk || ''}')" class="flex-shrink-0 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg px-3 py-1.5 text-xs font-semibold shadow-sm transition-colors border border-primary/20 mt-0.5 flex items-center gap-1.5">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                        `;
                    });

                    if (totalAvailable === 0) {
                        listDiv.innerHTML = `<div class="text-center py-8 text-slate-500 font-medium text-sm">Semua soal telah ditambahkan ke lembar kerja.</div>`;
                    }

                } else {
                    listDiv.innerHTML = `<div class="text-center py-8 text-slate-500 font-medium text-sm">Tidak ada soal tersisa dalam basis data.</div>`;
                }
            })
            .catch(error => {
                loadDiv.classList.add('hidden');
                loadDiv.classList.remove('flex');
                listDiv.classList.remove('hidden');
                listDiv.innerHTML = `<div class="text-center py-8 text-red-500 font-medium text-sm"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat soal. Silakan coba lagi.</div>`;
            });
    }

    function closeManualInsertModal() {
        const modal = document.getElementById('manualInsertModal');
        const content = document.getElementById('manualInsertContent');
        
        content.classList.remove('opacity-100', 'translate-y-0');
        content.classList.add('opacity-0', 'translate-y-8');
        modal.classList.add('opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function insertManualSoal(soalId, text, cpl, cpmk) {
        const container = document.getElementById('soalListContainer');
        
        // Remove empty placeholder if exist
        if (container.innerHTML.includes('Daftar soal kosong')) {
            container.innerHTML = '';
        }

        const count = document.querySelectorAll('#soalListContainer .card-soal').length;
        const index = count + 1;
        const fmtId = String(soalId).padStart(3, '0');
        
        let badges = '';
        if (cpl) {
            badges += `<span class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold rounded uppercase">${cpl}</span>`;
        }
        if (cpmk) {
            badges += `<span class="px-1.5 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-600 text-[10px] font-semibold rounded uppercase">${cpmk}</span>`;
        }

        const domString = `
            <div class="card-soal overflow-hidden border border-primary/20 rounded-xl bg-primary/5 shadow-sm flex items-start p-4 transition-all opacity-0 scale-95" id="soal-row-${soalId}">
                <input type="hidden" name="soal_ids[]" value="${soalId}">
                <div class="flex-shrink-0 w-10 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center font-bold text-primary mr-4 number-badge text-sm">${index}</div>
                <div class="flex-1 min-w-0 pr-4">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="font-bold text-slate-800 text-sm">Q-${fmtId}</span>
                        ${badges}
                        <span class="px-1.5 py-0.5 bg-orange-100 text-orange-600 text-[10px] font-bold rounded shadow-sm"><i class="fas fa-hand-pointer mr-0.5"></i> Inserted</span>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed line-clamp-2">${text}</p>
                </div>
                <button type="button" onclick="removeSoal(${soalId})" class="flex-shrink-0 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors p-2 mt-1">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        `;
        
        // Append
        container.insertAdjacentHTML('beforeend', domString);
        
        let jsonVal = document.getElementById('reviewSoalJson').value;
        let soalDataArr = [];
        try {
            soalDataArr = JSON.parse(jsonVal);
        } catch(e) {}
        soalDataArr.push({
            id: soalId,
            soal: text,
            cpl: cpl,
            cpmk: cpmk,
            bobot: 1
        });
        document.getElementById('reviewSoalJson').value = JSON.stringify(soalDataArr);

        // Setup row transition
        const newRow = document.getElementById('soal-row-' + soalId);
        setTimeout(() => {
            newRow.classList.remove('opacity-0', 'scale-95');
        }, 10);
        
        // Remove from manual list UI
        const rowInManual = document.getElementById('manual-soal-' + soalId);
        if (rowInManual) {
            rowInManual.style.opacity = '0';
            rowInManual.style.transform = 'scale(0.95)';
            setTimeout(() => {
                rowInManual.remove();
                if (document.querySelectorAll('#manualInsertList .card-soal').length === 0) {
                     document.getElementById('manualInsertList').innerHTML = `<div class="text-center py-8 text-slate-500 font-medium text-sm">Semua soal telah ditambahkan ke lembar kerja.</div>`;
                }
            }, 200);
        }

        updateSoalCount();
        updateBadgeNumbers();
    }

    /* Archive confirmation modal handlers */
    function showArchiveConfirm() {
        const modal = document.getElementById('archiveConfirmModal');
        const content = document.getElementById('archiveConfirmContent');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
            content.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function hideArchiveConfirm() {
        const modal = document.getElementById('archiveConfirmModal');
        const content = document.getElementById('archiveConfirmContent');
        if (!modal) return;
        content.classList.remove('opacity-100', 'scale-100');
        content.classList.add('opacity-0', 'scale-95');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 220);
    }

    function onArchiveConfirmYes() {
        hideArchiveConfirm();
        handleSimpanKeArsip(null, '1');
    }

    function onArchiveConfirmNo() {
        hideArchiveConfirm();
        handleSimpanKeArsip(null, '0');
    }

    function maybeShowArchiveConfirmAfterReturn() {
        if (!archiveConfirmPending || document.hidden) {
            return;
        }

        archiveConfirmPending = false;
        showArchiveConfirm();
    }

    window.addEventListener('focus', maybeShowArchiveConfirmAfterReturn);
    document.addEventListener('visibilitychange', maybeShowArchiveConfirmAfterReturn);
</script>

<!-- Loading overlay (used during saving draft / archiving) -->
<div id="reviewLoadingOverlay" class="hidden fixed inset-0 z-[130] items-center justify-center bg-black/40">
    <div class="bg-white rounded-lg p-6 flex items-center gap-4 shadow-lg">
        <i class="fas fa-circle-notch fa-spin text-2xl text-slate-700"></i>
        <div>
            <div id="reviewLoadingText" class="text-sm font-medium text-slate-800">Memproses...</div>
            <div class="text-xs text-slate-500">Mohon tunggu—jangan tutup halaman.</div>
        </div>
    </div>
</div>

<script>
    function showLoading(message) {
        const overlay = document.getElementById('reviewLoadingOverlay');
        const text = document.getElementById('reviewLoadingText');
        if (text && message) text.innerText = message;
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }
    }

    function hideLoading() {
        const overlay = document.getElementById('reviewLoadingOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
    }

</script>

<!-- Archive confirmation modal -->
<div id="archiveConfirmModal" class="hidden fixed inset-0 z-[120] items-center justify-center bg-black/40 p-4 opacity-0">
    <div id="archiveConfirmContent" class="w-full max-w-md bg-white rounded-xl p-6 shadow-lg transform transition-all opacity-0 scale-95">
        <h3 class="text-lg font-bold text-slate-900 mb-3">Kirim ke Arsip?</h3>
        <p class="text-sm text-slate-600 mb-5">Apakah Anda ingin mengirim paket soal ini langsung ke Arsip? Pilih "Ya" untuk menyimpan di tabel arsip, atau "Tidak" untuk menyimpan sebagai draf penarikan.</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="onArchiveConfirmNo()" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700">Tidak (Simpan Draf)</button>
            <button type="button" onclick="onArchiveConfirmYes()" class="px-4 py-2 rounded-lg bg-emerald-600 text-white">Ya, Arsipkan</button>
        </div>
    </div>
</div>

<!-- Flatpickr Indonesian date picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar { z-index: 9999 !important; }
    /* Hide numInput up/down spinners that overlap month name */
    .flatpickr-current-month .numInputWrapper span { display: none !important; }
    .flatpickr-current-month .numInputWrapper input { padding-right: 0 !important; }
    .flatpickr-current-month select { appearance: auto !important; -webkit-appearance: auto !important; background-image: none !important; }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    flatpickr('#inputHariTanggal', {
        locale: 'id',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'l, d F Y',
        allowInput: false,
        position: 'auto',
        appendTo: document.getElementById('reviewSoalModalContent'),
    });
</script>

<!-- Snackbar container -->
<div id="snackbarContainer" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end gap-2 pointer-events-none"></div>

<style>
    .snackbar {
        @apply max-w-xs w-auto bg-white text-slate-800 rounded-lg shadow-lg px-4 py-3 border border-slate-200 flex items-center gap-3 pointer-events-auto;
    }
    .snackbar-success {
        @apply border-emerald-200; 
    }
    .snackbar-error {
        @apply border-rose-200; 
    }
</style>

<script>
    function showSnackbar(message, variant = 'success', duration = 3500) {
        const container = document.getElementById('snackbarContainer');
        if (!container) return;

        const id = 'snackbar-' + Date.now();
        const el = document.createElement('div');
        el.id = id;
        el.className = 'snackbar ' + (variant === 'error' ? 'snackbar-error' : 'snackbar-success');
        el.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex items-center justify-center rounded-full text-white" style="background:${variant === 'error' ? '#ef4444' : '#10b981'}">
                    <i class="fas ${variant === 'error' ? 'fa-times' : 'fa-check'}"></i>
                </div>
                <div class="text-sm leading-snug text-slate-800">${message}</div>
            </div>
        `;

        container.appendChild(el);

        // Animate in
        el.style.transform = 'translateY(12px)';
        el.style.opacity = '0';
        requestAnimationFrame(() => {
            el.style.transition = 'transform 220ms ease, opacity 220ms ease';
            el.style.transform = 'translateY(0)';
            el.style.opacity = '1';
        });

        setTimeout(() => {
            // Animate out
            el.style.transform = 'translateY(12px)';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 240);
        }, duration);
    }
</script>

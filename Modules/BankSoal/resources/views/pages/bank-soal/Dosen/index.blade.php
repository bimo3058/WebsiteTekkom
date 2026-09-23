<x-banksoal::layouts.dosen-admin :bank-soal="true">
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Bank Soal</span>
    @endsection

<div
    id="banksoal-dosen-page-data"
    data-mk-json="{{ base64_encode($mataKuliahDosen->toJson()) }}"
    hidden
></div>

<x-banksoal::ui.bank-soal-page>
    <x-slot:header>
        @include('banksoal::pages.bank-soal.Dosen._header')
    </x-slot:header>

    @include('banksoal::pages.bank-soal.Dosen._soal-table')
    @include('banksoal::pages.bank-soal.Dosen._packages-table')
</x-banksoal::ui.bank-soal-page>

<!-- Tarik Soal Modal -->
<div id="tarikSoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div id="tarikSoalModalContent" class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 flex flex-col max-h-[90vh]">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tarik Soal</h3>
                <p class="text-sm text-slate-500 mt-0.5">Atur parameter untuk mengekstrak soal</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="closeTarikModal()">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto w-full">
            <form action="{{ route('banksoal.soal.dosen.ekstrak') }}" method="POST" id="formTarikSoal">
                @csrf

                <!-- Mata Kuliah -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Mata Kuliah</label>
                    <div class="relative">
                        <select class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2.5 pl-4 pr-10 shadow-sm appearance-none" style="appearance: none; -webkit-appearance: none; background-image: none;" name="mk_id" id="tarikMkId" required onchange="loadCplCpmk(this.value)">
                            <option value="">Pilih  Mata Kuliah</option>
                            @foreach($mataKuliahDosen as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Jenis Soal -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-3">Jenis Soal</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Pilihan Ganda" class="w-4 h-4 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Pilihan Ganda</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Essay" class="w-4 h-4 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Essay</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Take-Home" class="w-4 h-4 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Take-Home Test</span>
                        </label>
                    </div>
                </div>

                <div class="mb-5 rounded-xl border border-slate-200 p-4 bg-slate-50/60">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="requireBlindReview" name="require_blind_review" value="1" class="w-4 h-4 border-slate-300 rounded">
                        <span class="text-sm font-semibold text-slate-700">Aktifkan Blind Review Antar Dosen</span>
                    </label>
                    <p class="text-xs text-slate-500 mt-1">Saat aktif, soal hasil tarik otomatis dikirim ke dosen pengampu lain untuk review anonim.</p>

                    <div id="blindReviewerCountWrap" class="mt-3 hidden">
                        <label for="requiredReviewers" class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Reviewer per Soal</label>
                        <select id="requiredReviewers" name="required_reviewers" class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2 px-3 shadow-sm">
                            <option value="1" selected>1 Reviewer</option>
                            <option value="2">2 Reviewer</option>
                            <option value="3">3 Reviewer</option>
                        </select>
                    </div>
                </div>

                <!-- CPL -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">CPL (Capaian Pembelajaran Lulusan)</label>
                    <select class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none" name="cpl_id[]" id="cplSelectElement" multiple placeholder="Pilih CPL">
                        <option value="">Pilih CPL</option>
                    </select>
                </div>

                <!-- CPMK -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">CPMK (Capaian Pembelajaran Mata Kuliah)</label>
                    <select class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none" name="cpmk_id[]" id="cpmkSelectElement" multiple placeholder="Pilih CPMK">
                        <option value="">Pilih CPMK</option>
                    </select>
                </div>


            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 rounded-b-2xl flex items-center justify-end gap-3 mt-auto">
            <button type="button" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors" onclick="closeTarikModal()">
                Batal
            </button>
            <button type="submit" form="formTarikSoal" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors shadow-sm">
                <i class="fas fa-check"></i> Proses Tarik Soal
            </button>
        </div>

    </div>
</div>

<script>
    const pageData = document.getElementById('banksoal-dosen-page-data');
    let mkData = [];

    try {
        if (pageData?.dataset.mkJson) {
            mkData = JSON.parse(atob(pageData.dataset.mkJson));
        }
    } catch (error) {
        console.error('Gagal memuat data mata kuliah dosen', error);
        mkData = [];
    }

    window.tarikSoalsData = [];
    window.currentTarikMkId = null;

    let isUpdatingFilter = false;

    function renderCplCpmk(selectedMk, validCplIds = null, validCpmkIds = null) {
        const cplSelect = document.getElementById('cplSelectElement');
        const cpmkSelect = document.getElementById('cpmkSelectElement');

        // Remember existing values
        const currentCpls = cplSelect.tomselect ? cplSelect.tomselect.getValue() : [];
        const currentCpmks = cpmkSelect.tomselect ? cpmkSelect.tomselect.getValue() : [];

        // Destroy existing TomSelect instance if it exists
        if (cplSelect.tomselect) cplSelect.tomselect.destroy();
        if (cpmkSelect.tomselect) cpmkSelect.tomselect.destroy();

        cplSelect.innerHTML = '<option value="">Pilih CPL</option>';
        cpmkSelect.innerHTML = '<option value="">Pilih CPMK</option>';

        if (selectedMk.all_cpls && selectedMk.all_cpls.length > 0) {
            selectedMk.all_cpls.forEach(cpl => {
                if (validCplIds === null || validCplIds.includes(cpl.id)) {
                    cplSelect.innerHTML += `<option value="${cpl.id}">${cpl.kode}</option>`;
                }
            });
        }
        if (selectedMk.all_cpmks && selectedMk.all_cpmks.length > 0) {
            selectedMk.all_cpmks.forEach(cpmk => {
                if (validCpmkIds === null || validCpmkIds.includes(cpmk.id)) {
                    cpmkSelect.innerHTML += `<option value="${cpmk.id}">${cpmk.kode}</option>`;
                }
            });
        }

        // Init TomSelect for multi-select
        new TomSelect(cplSelect, {
            plugins: { remove_button: { title: 'Hapus CPL' } },
            create: false,
            placeholder: 'Pilih CPL (bisa lebih dari 1)',
            searchField: ['text'],
            onChange: function() {
                if (!isUpdatingFilter) filterCplCpmk();
            }
        });

        new TomSelect(cpmkSelect, {
            plugins: { remove_button: { title: 'Hapus CPMK' } },
            create: false,
            placeholder: 'Pilih CPMK (bisa lebih dari 1)',
            searchField: ['text'],
            onChange: function() {
                if (!isUpdatingFilter) filterCplCpmk();
            }
        });

        // Restore values
        isUpdatingFilter = true;
        if (currentCpls.length) cplSelect.tomselect.setValue(currentCpls);
        if (currentCpmks.length) cpmkSelect.tomselect.setValue(currentCpmks);
        isUpdatingFilter = false;
    }

    function filterCplCpmk() {
        if (!window.currentTarikMkId || window.tarikSoalsData.length === 0) {
            return;
        }

        if (isUpdatingFilter) return;
        isUpdatingFilter = true;

        const cbs = Array.from(document.querySelectorAll('input[name="jenis_soal[]"]:checked'));
        const typesChecked = cbs.map(cb => {
            if (cb.value === 'Pilihan Ganda') return 'pilihan_ganda';
            if (cb.value === 'Take-Home') return 'take_home';
            return 'essay';
        });

        const selectedMk = mkData.find(mk => mk.id == window.currentTarikMkId);
        if (!selectedMk) {
            isUpdatingFilter = false;
            return;
        }

        const cplSelect = document.getElementById('cplSelectElement');
        const cpmkSelect = document.getElementById('cpmkSelectElement');

        const selectedCplIds = cplSelect && cplSelect.tomselect ?
            (Array.isArray(cplSelect.tomselect.getValue()) ? cplSelect.tomselect.getValue() : [cplSelect.tomselect.getValue()]).filter(v => v).map(Number) : [];

        const selectedCpmkIds = cpmkSelect && cpmkSelect.tomselect ?
            (Array.isArray(cpmkSelect.tomselect.getValue()) ? cpmkSelect.tomselect.getValue() : [cpmkSelect.tomselect.getValue()]).filter(v => v).map(Number) : [];

        // Base filter by Tipe Soal
        let filteredSoals = window.tarikSoalsData;
        if (typesChecked.length > 0) {
            filteredSoals = filteredSoals.filter(soal => typesChecked.includes(soal.tipe_soal));
        }

        // To determine available CPLs: filter by selected types & selected CPMKs
        const availableCplsFromSoal = filteredSoals
            .filter(soal => selectedCpmkIds.length === 0 || selectedCpmkIds.includes(soal.cpmk_id))
            .map(soal => soal.cpl_id)
            .filter(id => id !== null);

        // To determine available CPMKs: filter by selected types & selected CPLs
        const availableCpmksFromSoal = filteredSoals
            .filter(soal => selectedCplIds.length === 0 || selectedCplIds.includes(soal.cpl_id))
            .map(soal => soal.cpmk_id)
            .filter(id => id !== null);

        const uniqueCplIds = [...new Set(availableCplsFromSoal)];
        const uniqueCpmkIds = [...new Set(availableCpmksFromSoal)];

        renderCplCpmk(selectedMk, typesChecked.length === 0 && selectedCpmkIds.length === 0 ? null : uniqueCplIds, typesChecked.length === 0 && selectedCplIds.length === 0 ? null : uniqueCpmkIds);
        isUpdatingFilter = false;
    }

    function loadCplCpmk(mk_id) {
        window.currentTarikMkId = mk_id;
        const selectedMk = mkData.find(mk => mk.id == mk_id);

        if (!selectedMk) return;

        // Reset checkboxes state
        document.querySelectorAll('input[name="jenis_soal[]"]').forEach(cb => {
            cb.checked = false;
        });

        renderCplCpmk(selectedMk, null, null);

        // Fetch questions exactly for this MK to drive the filtering
        fetch(@json(route('banksoal.soal.dosen.get-available-soals', ['mk_id' => '__MK_ID__'])).replace('__MK_ID__', mk_id), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success && data.soals) {
                window.tarikSoalsData = data.soals;
            } else {
                window.tarikSoalsData = [];
            }

            filterCplCpmk();
        })
        .catch(() => {
            window.tarikSoalsData = [];
            renderCplCpmk(selectedMk, null, null);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[name="jenis_soal[]"]').forEach(cb => {
            cb.addEventListener('change', filterCplCpmk);
        });

        const blindToggle = document.getElementById('requireBlindReview');
        const blindCountWrap = document.getElementById('blindReviewerCountWrap');
        if (blindToggle && blindCountWrap) {
            blindToggle.addEventListener('change', () => {
                if (blindToggle.checked) {
                    blindCountWrap.classList.remove('hidden');
                } else {
                    blindCountWrap.classList.add('hidden');
                }
            });
        }
    });

    function openTarikModal(mk_id = null) {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Timeout to trigger transition after display swap
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);

        if (mk_id) {
            document.getElementById('tarikMkId').value = mk_id;
            loadCplCpmk(mk_id);
        }
    }

    function closeTarikModal() {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');

        // Start exit animation
        modal.classList.add('opacity-0');
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');

        // Hide elements after animation finishes
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Modal Lihat Soal
    function openLihatSoalModal(mk_id, mk_nama) {
        document.getElementById('lihatSoalSubtitle').innerText = 'Mata Kuliah: ' + mk_nama;
        const modal = document.getElementById('lihatSoalModal');
        const modalContent = document.getElementById('lihatSoalModalContent');
        const listDiv = document.getElementById('lihatSoalList');
        const loadDiv = document.getElementById('lihatSoalLoading');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);

        listDiv.classList.add('hidden');
        if (window.Spinner) window.Spinner.showTable('lihatSoalLoading');

        fetch(@json(route('banksoal.soal.dosen.get-available-soals', ['mk_id' => '__MK_ID__'])).replace('__MK_ID__', mk_id), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (window.Spinner) window.Spinner.hideTable('lihatSoalLoading');
            listDiv.classList.remove('hidden');
            listDiv.innerHTML = '';

            if (data.success && data.soals.length > 0) {
                data.soals.forEach(soal => {
                    const fmtId = String(soal.id).padStart(3, '0');
                    let badges = '';
                    if (soal.cpl) {
                        badges += `<span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 text-[10px] font-bold rounded uppercase">${soal.cpl}</span>`;
                    }
                    if (soal.cpmk) {
                        badges += `<span class="px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold rounded uppercase">${soal.cpmk}</span>`;
                    }

                    const tipeLabel = soal.tipe_soal === 'essay'
                        ? 'Essay'
                        : (soal.tipe_soal === 'take_home' ? 'Take-Home' : 'Pilihan Ganda');
                    const tipeColor = soal.tipe_soal === 'essay'
                        ? 'bg-purple-50 text-purple-700 border-purple-200'
                        : (soal.tipe_soal === 'take_home'
                            ? 'bg-amber-50 text-amber-700 border-amber-200'
                            : 'bg-primary/10 text-primary border-primary/20');
                    badges += `<span class="px-2 py-0.5 ${tipeColor} border text-[10px] font-bold rounded uppercase">${tipeLabel}</span>`;

                    listDiv.innerHTML += `
                        <div class="px-5 py-4 border-b border-slate-100 hover:bg-slate-50/50 transition-colors flex items-start cursor-default">
                            <div class="flex-shrink-0 w-10 h-10 bg-white border border-slate-200 shadow-sm rounded-lg flex items-center justify-center font-bold text-slate-800 mr-4 text-xs">
                                Q-${fmtId}
                            </div>
                            <div class="flex-1 min-w-0 pr-4">
                                <div class="flex flex-wrap gap-2 items-center mb-1">
                                    ${badges}
                                </div>
                                <div class="text-sm text-slate-700 leading-relaxed prose prose-sm prose-slate max-w-none prose-img:max-h-48 prose-img:w-auto prose-img:rounded-md mt-2">
                                    ${soal.soal}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                listDiv.innerHTML = `
                    <div class="text-center py-12 text-slate-500 bg-white rounded-xl border border-slate-200 shadow-sm mx-5 mb-5">
                        <i class="fas fa-folder-open text-4xl text-slate-300 mb-3 block"></i>
                        Tidak ada butir soal di paket ini.
                    </div>`;
            }
        })
        .catch(error => {
            if (window.Spinner) window.Spinner.hideTable('lihatSoalLoading');
            listDiv.classList.remove('hidden');
            listDiv.innerHTML = `<div class="text-center py-12 text-red-500 bg-white border border-slate-200 shadow-sm rounded-xl mx-5 mb-5"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p class="text-sm font-medium">Gagal memuat soal.</p></div>`;
        });
    }

    function closeLihatSoalModal() {
        const modal = document.getElementById('lihatSoalModal');
        const modalContent = document.getElementById('lihatSoalModalContent');

        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        modal.classList.add('opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // AJAX for Form Tarik Soal
    function handleTarikSoalSubmit(e, formEl = null) {
        if(e) e.preventDefault();

        const form = formEl || document.getElementById('formTarikSoal');
        const formData = new FormData(form);
        // Cari tombol submit, entah di dalam form atau di luar dengan atribut form="..."
        const submitBtn = document.querySelector('button[form="formTarikSoal"]') || form.querySelector('[type="submit"]');
        let originalText = '';

        if (submitBtn) {
            originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            submitBtn.disabled = true;
        }
        window.showLoader();

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            window.hideLoader();
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }

            if (data.success) {
                if (data.blind_review?.enabled && !data.blind_review?.round_id) {
                    if (typeof showSnackbar === 'function') {
                        showSnackbar('Blind review tidak terbentuk karena tidak ada dosen pengampu lain pada MK ini.', 'warning');
                    } else {
                        alert('Blind review tidak terbentuk karena tidak ada dosen pengampu lain pada MK ini.');
                    }
                }

                closeTarikModal();
                // Pass extra param for modal filler
                data.mk_id = formData.get('mk_id');
                setTimeout(() => {
                    openReviewModal(data);
                }, 300);
            } else {
                alert(data.message || 'Terjadi kesalahan saat menarik soal.');
            }
        })
        .catch(error => {
            window.hideLoader();
            console.error('Error:', error);
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            alert('Terjadi kesalahan tidak terduga pada server.');
        });
    }

    document.getElementById('formTarikSoal').addEventListener('submit', handleTarikSoalSubmit);
</script>

<!-- Lihat Soal Modal -->
<div id="lihatSoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div id="lihatSoalModalContent" class="relative w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Kumpulan Butir Soal Terkait</h3>
                <p class="text-sm text-slate-500 mt-0.5" id="lihatSoalSubtitle">Mata Kuliah: ...</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="closeLihatSoalModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto bg-slate-50/50">
            <div id="lihatSoalLoading" style="display:none;"></div>
            <div id="lihatSoalList" class="flex flex-col">
                <!-- Items go here -->
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-200 bg-white rounded-b-2xl flex justify-end">
            <button type="button" onclick="closeLihatSoalModal()" class="px-6 py-2 bg-slate-800 text-white font-medium hover:bg-slate-700 rounded-lg transition-colors">Tutup Jendela</button>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="importModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="if(event.target === this) closeImportModal()">
    <div id="importModalContent" class="relative w-full max-w-lg transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 translate-y-4">

        <form action="{{ route('banksoal.soal.dosen.import-csv') }}" method="POST" enctype="multipart/form-data" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
            @csrf

            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Import Massal via Spreadsheet</h3>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">Unggah file template XLS/CSV Bank Soal.</p>
                </div>
                <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <div class="mb-4 text-sm text-slate-600 bg-primary/10 border border-primary/20 rounded-xl p-4">
                    <p class="mb-2"><i class="fas fa-info-circle text-primary mr-1.5"></i> <strong>Langkah Import Baru:</strong></p>
                    <ol class="list-decimal ml-6 space-y-1">
                        <li>Gunakan template impor standar (SOAL pada kolom Jenis).</li>
                        <li>Pastikan baris jawaban berada persis di bawah baris soal tersebut.</li>
                        <li>Format yang didukung: <code class="bg-primary/20 px-1 py-0.5 rounded text-primary">.xls</code>, <code class="bg-primary/20 px-1 py-0.5 rounded text-primary">.xlsx</code>, <code class="bg-primary/20 px-1 py-0.5 rounded text-primary">.csv</code>.</li>
                    </ol>
                </div>

                <label for="csv_file" class="block text-sm font-semibold text-slate-700 mb-2">Unggah File Target (Excel/CSV)</label>
                <div class="relative group cursor-pointer">
                    <input type="file" name="csv_file" id="csv_file" accept=".csv, .txt, .xls, .xlsx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="document.getElementById('fileNameP').textContent = this.files[0]?.name || 'Pilih file excel/csv Anda';">
                    <div class="w-full flex-col flex items-center justify-center border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 p-8 text-center group-hover:bg-slate-100 group-hover:border-primary/20 transition-all">
                        <div class="w-12 h-12 mb-3 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <i class="fas fa-file-excel text-xl"></i>
                        </div>
                        <p id="fileNameP" class="text-sm font-medium text-slate-700">Pilih file atau seret file .xls/.xlsx/.csv ke sini</p>
                        <p class="text-xs text-slate-500 mt-1">Maksimal ukuran file: 5MB</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeImportModal()" class="px-5 py-2.5 font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
                <button type="submit" onclick="if(this.closest('form').checkValidity()){ this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Memproses...'; this.classList.add('opacity-75'); }" class="px-5 py-2.5 bg-emerald-600 text-white font-semibold hover:bg-emerald-700 rounded-xl shadow-sm transition-all focus:ring-4 focus:ring-emerald-500/20">
                    <i class="fas fa-upload mr-1.5"></i> Proses Import
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function toggleUploadDropdown() {
        const menu = document.getElementById('uploadDropdownMenu');
        const isClosed = menu.classList.contains('opacity-0');

        if (isClosed) {
            menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            menu.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
        } else {
            closeUploadDropdown();
        }
    }

    function closeUploadDropdown() {
        const menu = document.getElementById('uploadDropdownMenu');
        if (menu && !menu.classList.contains('opacity-0')) {
            menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        }
    }

    // Tutup dropdown jika user klik/klik di luar area dropdown
    window.addEventListener('click', function(e) {
        const container = document.getElementById('uploadDropdownContainer');
        if (container && !container.contains(e.target)) {
            closeUploadDropdown();
        }
    });

    function openImportModal() {
        const modal = document.getElementById('importModal');
        const modalContent = document.getElementById('importModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        const modalContent = document.getElementById('importModalContent');

        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // reset value input file
            document.getElementById('csv_file').value = '';
            document.getElementById('fileNameP').textContent = 'Pilih file atau seret file .csv ke sini';
        }, 300);
    }

    // Load CPL based on chosen MK
    document.getElementById('import_mk_id')?.addEventListener('change', function() {
        const cplSelect = document.getElementById('import_cpl_id');
        const mkId = this.value;
        cplSelect.innerHTML = '<option value="">Memuat CPL...</option>';

        if(mkId) {
            fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                .then(r => r.json())
                .then(data => {
                    cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                    data.forEach(c => cplSelect.innerHTML += `<option value="${c.id}">${c.kode} - ${c.deskripsi ? c.deskripsi.substring(0, 60) : ''}...</option>`);
                })
                .catch(error => {
                    console.error('Error fetching CPL:', error);
                    cplSelect.innerHTML = '<option value="">Gagal memuat CPL</option>';
                });
        } else {
            cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
        }
    });
</script>


<!-- Modal Ajukan Soal -->
<div id="ajukanModal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="ajukanModalTitle" aria-hidden="true" class="bs-submit-modal hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-[100] justify-center items-center transition-opacity duration-300 opacity-0">
    <div id="ajukanModalContent" class="relative p-4 w-full max-w-lg max-h-full transform transition-all duration-300 scale-95 opacity-0 translate-y-4">
        <form action="{{ route('banksoal.soal.dosen.ajukan-semua') }}" method="POST" class="bs-modal-card relative flex flex-col max-h-[90vh]" onsubmit="if(this.checkValidity()){ this.querySelector('.bs-modal-confirm').textContent = 'Memproses...'; window.showLoader(); return true; }">
            @csrf

            <div class="bs-modal-header">
                <div class="bs-modal-heading">
                    <span class="bs-modal-icon"><i class="fas fa-paper-plane" aria-hidden="true"></i></span>
                    <div>
                        <h3 id="ajukanModalTitle">Ajukan Soal (Draf)</h3>
                        <p>Kirim soal draf untuk divalidasi oleh GPM.</p>
                    </div>
                </div>
                <button type="button" onclick="closeAjukanModal()" class="bs-modal-close">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Tutup popup</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <div class="mb-5">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Pilih Mata Kuliah</label>
                    <div x-data="{
                            open: false,
                            selected: '',
                            selectedLabel: '-- Pilih Mata Kuliah --',
                            options: [
                                { value: '', label: '-- Pilih Mata Kuliah --', cls: 'text-slate-500' },
                                { value: 'all', label: '-- Ajukan Semua Mata Kuliah --', cls: 'font-semibold text-primary' },
                                @foreach($mataKuliahDosen as $mk)
                                { value: '{{ $mk->id }}', label: '{{ $mk->kode }} - {{ addslashes($mk->nama) }}', cls: 'text-slate-700' },
                                @endforeach
                            ],
                            selectOption(opt) {
                                this.selected = opt.value;
                                this.selectedLabel = opt.label;
                                this.open = false;
                            }
                        }"
                        class="relative"
                        @click.outside="open = false">

                        <!-- Hidden input for form submission & validation -->
                        <input type="text" name="mk_id" id="ajukan_mk_id" x-model="selected" required class="absolute inset-x-0 bottom-0 opacity-0 pointer-events-none w-full h-1 z-[-1]" tabindex="-1" oninvalid="this.setCustomValidity('Silakan pilih mata kuliah terlebih dahulu')" oninput="this.setCustomValidity('')">

                        <!-- Trigger Button -->
                        <button type="button" @click="open = !open"
                            class="bs-dropdown-trigger bs-dropdown-select"
                            :class="{ 'is-open': open }">
                            <span x-text="selectedLabel" :class="selected === 'all' ? 'font-semibold text-primary' : (selected === '' ? 'text-slate-500' : 'text-slate-800')"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="bs-dropdown-menu bs-dropdown-options absolute z-50 w-full max-h-60 overflow-y-auto"
                             style="display: none;">
                            <ul>
                                <template x-for="opt in options" :key="opt.value">
                                    <li>
                                        <button type="button" @click="selectOption(opt); document.getElementById('ajukan_mk_id').setCustomValidity('')"
                                            class="bs-dropdown-item"
                                            :class="[(selected === opt.value ? 'is-selected' : ''), opt.cls]">
                                            <span x-text="opt.label"></span>
                                            <i class="fas fa-check text-primary text-xs" x-show="selected === opt.value"></i>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="bs-modal-notice">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    <p>Semua soal pada mata kuliah terpilih yang masih berstatus <strong>Draf</strong> akan diubah menjadi <strong>Diajukan</strong> dan dikirimkan ke GPM untuk divalidasi. Apakah Anda yakin?</p>
                </div>
            </div>

            <div class="bs-modal-footer">
                <button type="button" class="bs-modal-cancel" onclick="closeAjukanModal()">
                    Batal
                </button>
                <button type="submit" class="bs-modal-confirm">
                    <i class="fas fa-paper-plane"></i> Ya, Ajukan Pilihan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAjukanModal() {
        const modal = document.getElementById('ajukanModal');
        const modalContent = document.getElementById('ajukanModalContent');

        modal.setAttribute('aria-hidden', 'false');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
    }

    function closeAjukanModal() {
        const modal = document.getElementById('ajukanModal');
        const modalContent = document.getElementById('ajukanModalContent');

        modal.setAttribute('aria-hidden', 'true');
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', function () {
        function debounce(func, delay) {
            let timeoutId;
            return function (...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function searchTable(searchInput, tabId) {
            const searchValue = searchInput.value.toLowerCase().trim();
            const tabContent = document.querySelector(`[data-tab-panel="${tabId}"]`);
            if (!tabContent) return;

            const rows = tabContent.querySelectorAll('table tbody tr:not(.no-results-message)');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchValue) || searchValue === '') {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            const noResultsMsg = tabContent.querySelector('.no-results-message');
            if (noResultsMsg) {
                if (visibleCount === 0) {
                    noResultsMsg.classList.remove('hidden');
                } else {
                    noResultsMsg.classList.add('hidden');
                }
            }

            // Prevent actual form submission to retain real-time UI mapping
            // But if user hits enter, it will trigger the server-side pagination flow.
        }

        // Delegate actions so newly filtered rows retain their modal handlers.
        document.getElementById('packagesSection')?.addEventListener('click', function (event) {
            const button = event.target.closest('[data-package-action]');
            if (!button) return;
            const action = button.dataset.packageAction;
            const mkId = button.dataset.mkId;
            const mkNama = button.dataset.mkNama || '';

            if (action === 'lihat') {
                openLihatSoalModal(mkId, mkNama);
            }

            if (action === 'tarik') {
                openTarikModal(mkId);
            }
        });

        const searchInputs = document.querySelectorAll('[data-search-tab]:not([data-search-tab="paket"])');
        searchInputs.forEach((input) => {
            const tabId = input.getAttribute('data-search-tab');
            input.addEventListener('input', debounce(function () {
                // Untuk table yang di-paginasi server, kita butuh submit form otomatis.
                // Dengan debouncing 500ms, UX akan terasa seperti live-search
                // tetapi akan melakukan fetch ke seluruh data di server.
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    searchTable(this, tabId); // Fallback ke client-side search
                }
            }, 600));
        });

    });
</script>

@include('banksoal::pages.bank-soal.Dosen.review-modal')

@push('scripts')
    <script src="{{ asset('modules/banksoal/js/Banksoal/Dosen/package-filter.js') }}" defer></script>
@endpush

</x-banksoal::layouts.dosen-admin>

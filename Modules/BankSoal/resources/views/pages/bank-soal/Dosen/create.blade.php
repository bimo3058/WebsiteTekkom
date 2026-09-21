<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <a href="{{ route('banksoal.soal.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Bank Soal</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Buat Soal Baru</span>
    @endsection
    <x-banksoal::ui.page-header title="Buat Soal Baru" subtitle="Lengkapi formulir untuk menambahkan butir soal ke bank soal.">
        <x-slot:actions>
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <x-banksoal::ui.panel title="Form Soal" subtitle="Gunakan format pilihan ganda dan tandai satu jawaban benar." padding="p-0">
        <form action="{{ route('banksoal.soal.dosen.store') }}" method="POST" id="formSoal">
            @csrf
            <div x-data="soalForm()" class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Mata Kuliah -->
                <div class="relative" @click.outside="mkOpen = false">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Mata Kuliah</label>
                    <input type="text" name="mk_id" x-model="mkId" required class="absolute inset-x-0 bottom-0 opacity-0 pointer-events-none w-full h-1 z-[-1]" tabindex="-1" oninvalid="this.setCustomValidity('Pilih mata kuliah')" oninput="this.setCustomValidity('')">
                    <button type="button" @click="mkOpen = !mkOpen" class="w-full flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-left shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" :class="mkOpen ? 'border-primary ring-2 ring-primary/20' : ''">
                        <span x-text="getLabel(mkOptions, mkId, 'Pilih Mata Kuliah...')" :class="mkId ? 'text-slate-800' : 'text-slate-500'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="mkOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="mkOpen" x-transition class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                        <ul class="py-1 text-sm text-slate-700">
                            <li>
                                <button type="button" @click="mkId = ''; mkOpen = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-500">Pilih Mata Kuliah...</button>
                            </li>
                            <template x-for="opt in mkOptions" :key="opt.id">
                                <li>
                                    <button type="button" @click="mkId = opt.id; mkOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 transition-colors flex items-center justify-between" :class="mkId === opt.id ? 'bg-primary/5 text-slate-800 font-medium' : ''">
                                        <span x-text="opt.label"></span>
                                        <i class="fas fa-check text-primary text-xs" x-show="mkId === opt.id"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    @error('mk_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- CPL -->
                <div class="relative" @click.outside="cplOpen = false">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">CPL</label>
                    <input type="text" name="cpl_id" x-model="cplId" required class="absolute inset-x-0 bottom-0 opacity-0 pointer-events-none w-full h-1 z-[-1]" tabindex="-1" oninvalid="this.setCustomValidity('Pilih CPL')" oninput="this.setCustomValidity('')">
                    <button type="button" @click="cplOpen = !cplOpen" class="w-full flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-left shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" :class="cplOpen ? 'border-primary ring-2 ring-primary/20' : ''" :disabled="!mkId || cplLoading">
                        <span x-text="cplLoading ? 'Memuat CPL...' : getLabel(cplOptions, cplId, 'Pilih CPL...')" :class="cplId ? 'text-slate-800' : 'text-slate-500'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="cplOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="cplOpen" x-transition class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                        <ul class="py-1 text-sm text-slate-700">
                            <li>
                                <button type="button" @click="cplId = ''; cplOpen = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-500">Pilih CPL...</button>
                            </li>
                            <template x-for="opt in cplOptions" :key="opt.id">
                                <li>
                                    <button type="button" @click="cplId = opt.id; cplOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 transition-colors flex items-center justify-between" :class="cplId === opt.id ? 'bg-primary/5 text-slate-800 font-medium' : ''">
                                        <span x-text="opt.label"></span>
                                        <i class="fas fa-check text-primary text-xs" x-show="cplId === opt.id"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    @error('cpl_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- CPMK -->
                <div class="relative" @click.outside="cpmkOpen = false">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">CPMK</label>
                    <input type="text" name="cpmk_id" x-model="cpmkId" required class="absolute inset-x-0 bottom-0 opacity-0 pointer-events-none w-full h-1 z-[-1]" tabindex="-1" oninvalid="this.setCustomValidity('Pilih CPMK')" oninput="this.setCustomValidity('')">
                    <button type="button" @click="cpmkOpen = !cpmkOpen" class="w-full flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-left shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" :class="cpmkOpen ? 'border-primary ring-2 ring-primary/20' : ''" :disabled="!cplId || cpmkLoading">
                        <span x-text="cpmkLoading ? 'Memuat CPMK...' : getLabel(cpmkOptions, cpmkId, 'Pilih CPMK...')" :class="cpmkId ? 'text-slate-800' : 'text-slate-500'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="cpmkOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="cpmkOpen" x-transition class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                        <ul class="py-1 text-sm text-slate-700">
                            <li>
                                <button type="button" @click="cpmkId = ''; cpmkOpen = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-500">Pilih CPMK...</button>
                            </li>
                            <template x-for="opt in cpmkOptions" :key="opt.id">
                                <li>
                                    <button type="button" @click="cpmkId = opt.id; cpmkOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 transition-colors flex items-center justify-between" :class="cpmkId === opt.id ? 'bg-primary/5 text-slate-800 font-medium' : ''">
                                        <span x-text="opt.label"></span>
                                        <i class="fas fa-check text-primary text-xs" x-show="cpmkId === opt.id"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    @error('cpmk_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Tingkat Kesulitan -->
                <div class="relative" @click.outside="kesulitanOpen = false">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Tingkat Kesulitan</label>
                    <input type="text" name="kesulitan" x-model="kesulitan" required class="absolute inset-x-0 bottom-0 opacity-0 pointer-events-none w-full h-1 z-[-1]" tabindex="-1">
                    <button type="button" @click="kesulitanOpen = !kesulitanOpen" class="w-full flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-left shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" :class="kesulitanOpen ? 'border-primary ring-2 ring-primary/20' : ''">
                        <span x-text="getLabel(kesulitanOptions, kesulitan, 'Pilih Tingkat Kesulitan...')" :class="kesulitan ? 'text-slate-800' : 'text-slate-500'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="kesulitanOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="kesulitanOpen" x-transition class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                        <ul class="py-1 text-sm text-slate-700">
                            <li>
                                <button type="button" @click="kesulitan = ''; kesulitanOpen = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-500">Pilih Tingkat Kesulitan...</button>
                            </li>
                            <template x-for="opt in kesulitanOptions" :key="opt.id">
                                <li>
                                    <button type="button" @click="kesulitan = opt.id; kesulitanOpen = false" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 transition-colors flex items-center justify-between" :class="kesulitan === opt.id ? 'bg-primary/5 text-slate-800 font-medium' : ''">
                                        <span x-text="opt.label"></span>
                                        <i class="fas fa-check text-primary text-xs" x-show="kesulitan === opt.id"></i>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Bobot / Skor -->
                <div>
                    <label for="bobot" class="mb-2 block text-sm font-semibold text-slate-700">Bobot / Skor</label>
                    <input type="number" id="bobot" name="bobot" min="1" max="10" value="{{ old('bobot', 10) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm transition-all" required oninput="if(this.value > 10) this.value = 10; if(this.value < 1) this.value = 1;">
                </div>
            </div>

            <div class="px-6 pb-6">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Pertanyaan</label>
                <div class="flex items-center gap-5 text-sm font-medium text-slate-700">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="pilihan_ganda" id="radioPg" {{ old('tipe_soal', 'pilihan_ganda') == 'pilihan_ganda' ? 'checked' : '' }} class="w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                        Pilihan Ganda
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="essay" id="radioEssay" {{ old('tipe_soal') == 'essay' ? 'checked' : '' }} class="w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                        Essay
                    </label>

                </div>
            </div>

            <div class="px-6 pb-6">
                <label for="soal" class="mb-2 block text-sm font-semibold text-slate-700">Pertanyaan</label>
                <!-- Hidden input untuk disubmit ke form -->
                <input type="hidden" name="soal" id="soalInput" value="{{ old('soal') }}">
                <div id="editor" class="bg-white">{!! old('soal') !!}</div>
                <p class="mt-2 text-xs text-slate-500">Anda dapat menambahkan format teks rata-kiri/kanan, list, tabel, dan gambar menggunakan menu di atas.</p>
            </div>

            <div id="opsiContainerWrapper" class="border-t border-slate-200 px-6 py-5 {{ old('tipe_soal', 'pilihan_ganda') == 'pilihan_ganda' ? '' : 'hidden' }}">
                <div class="mb-3 flex items-center justify-between"><label class="text-sm font-semibold text-slate-700">Opsi Jawaban & Kunci</label><button type="button" id="addOptionBtn" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">+ Tambah Opsi</button></div>
                <div id="optionsContainer" class="space-y-3">
                    @for($i = 0; $i < max(count(old('jawaban', []) ?: [0, 1, 2]), 3); $i++)
                        <label class="option-item flex items-start gap-3 rounded-lg border border-slate-200 p-4 {{ old('jawaban_benar') == (string)$i ? 'border-green-400 bg-green-50' : '' }}">
                            <input type="radio" name="jawaban_benar" value="{{ $i }}" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500" {{ old('jawaban_benar') == (string)$i ? 'checked' : '' }} required>
                            <div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide {{ old('jawaban_benar') == (string)$i ? 'text-green-700' : 'text-slate-400' }}">Pilihan {{ chr(65 + $i) }} {{ old('jawaban_benar') == (string)$i ? '(Kunci Jawaban)' : '' }}</span><input type="text" name="jawaban[{{$i}}][teks]" value="{{ old('jawaban.'.$i.'.teks') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none" required></div>
                            @if($i > 2)<button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>@endif
                        </label>
                    @endfor
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-4">
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-700">Batalkan</a>
                <div class="flex items-center gap-2"><button type="submit" name="submit_action" value="draft" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary transition-colors">Simpan Draft</button><button type="submit" name="submit_action" value="publish" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90 transition-colors">Publikasi Soal</button></div>
            </div>
        </form>
    </x-banksoal::ui.panel>

    @push('styles')
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: #e2e8f0;
            background-color: #f8fafc;
            padding: 0.75rem;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: #e2e8f0;
            min-height: 150px;
            font-family: inherit;
            font-size: 0.875rem;
        }
        .ql-editor {
            min-height: 150px;
        }
        .ql-editor.ql-blank::before {
            color: #94a3b8;
            font-style: normal;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Tuliskan deskripsi pertanyaan di sini...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                        [{ 'align': [] }],
                        ['clean'],                                         // remove formatting button
                        ['link', 'image', 'video']                         // link and image, video
                    ]
                }
            });

            // Kustomisasi Handler Upload Gambar untuk mengatur batas ukuran file (Maks 2MB)
            quill.getModule('toolbar').addHandler('image', function() {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = () => {
                    const file = input.files[0];
                    if (!file) return;

                    const maxMB = 2; // Maksimal ukuran dalam MB
                    const maxSize = maxMB * 1024 * 1024; 
                    if (file.size > maxSize) {
                        alert(`Ukuran gambar terlalu besar! Maksimal ${maxMB}MB.`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', e.target.result);
                        quill.setSelection(range.index + 1);
                    };
                    reader.readAsDataURL(file);
                };
            });

            // Sinkronkan Quill HTML ke hidden input sebelum submit form
            var form = document.getElementById('formSoal');
            form.addEventListener('submit', function() {
                // Populate hidden input on submit
                var soalInput = document.getElementById('soalInput');
                // Mengambil HTML dari editor, jika kosong/hanya whitespace dikembalikan string kosong supaya validasi server menangkap
                var htmlContent = quill.root.innerHTML;
                if (quill.getText().trim().length === 0 && !htmlContent.includes('<img')) {
                    soalInput.value = '';
                } else {
                    soalInput.value = htmlContent;
                }
                
                if (form.checkValidity()) {
                    window.showLoader();
                }
            });

            // Soal Form Alpine Logic
            window.soalForm = function() {
                return {
                    mkId: '{{ old('mk_id', '') }}',
                    mkOptions: [
                        @foreach($mataKuliahDosen as $mk)
                        { id: '{{ $mk->id }}', label: '{{ $mk->kode }} - {{ addslashes($mk->nama) }}' },
                        @endforeach
                    ],
                    mkOpen: false,

                    cplId: '{{ old('cpl_id', '') }}',
                    cplOptions: [],
                    cplLoading: false,
                    cplOpen: false,

                    cpmkId: '{{ old('cpmk_id', '') }}',
                    cpmkOptions: [],
                    cpmkLoading: false,
                    cpmkOpen: false,

                    kesulitan: '{{ old('kesulitan', '') }}',
                    kesulitanOpen: false,
                    kesulitanOptions: [
                        { id: 'easy', label: 'Easy' },
                        { id: 'intermediate', label: 'Intermediate' },
                        { id: 'advanced', label: 'Advanced' }
                    ],

                    init() {
                        this.$watch('mkId', (value) => {
                            this.cplId = '';
                            this.cpmkId = '';
                            this.cplOptions = [];
                            this.cpmkOptions = [];
                            if (value) this.fetchCpl(value);
                        });

                        this.$watch('cplId', (value) => {
                            this.cpmkId = '';
                            this.cpmkOptions = [];
                            if (value && this.mkId) this.fetchCpmk(value, this.mkId);
                        });

                        if (this.mkId) {
                            this.fetchCpl(this.mkId, true);
                        }
                    },

                    fetchCpl(mkId, isInitial = false) {
                        this.cplLoading = true;
                        fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                            .then(r => r.json())
                            .then(data => {
                                this.cplOptions = data.map(c => ({
                                    id: c.id.toString(),
                                    label: `${c.kode} - ${c.deskripsi.substring(0, 60)}...`
                                }));
                                this.cplLoading = false;
                                if (isInitial && this.cplId) {
                                    this.fetchCpmk(this.cplId, mkId, true);
                                }
                            })
                            .catch(() => { this.cplLoading = false; });
                    },

                    fetchCpmk(cplId, mkId, isInitial = false) {
                        this.cpmkLoading = true;
                        fetch(`{{ route('banksoal.rps.dosen.cpmk') }}?cpl_id=${cplId}&mk_id=${mkId}`)
                            .then(r => r.json())
                            .then(data => {
                                this.cpmkOptions = data.map(c => ({
                                    id: c.id.toString(),
                                    label: `${c.kode} - ${c.deskripsi.substring(0, 60)}...`
                                }));
                                this.cpmkLoading = false;
                            })
                            .catch(() => { this.cpmkLoading = false; });
                    },
                    
                    getLabel(options, id, defaultLabel) {
                        if (!id) return defaultLabel;
                        const opt = options.find(o => o.id == id);
                        return opt ? opt.label : defaultLabel;
                    }
                }
            };
            const container = document.getElementById('optionsContainer');
            const addBtn = document.getElementById('addOptionBtn');
            function updateStyles() {
                const boxes = container.querySelectorAll('.option-item');
                boxes.forEach((box, index) => {
                    const radio = box.querySelector('input[type="radio"]');
                    const title = box.querySelector('.option-title');
                    const letter = String.fromCharCode(65 + index);
                    radio.value = index;
                    if (radio.checked) {
                        box.classList.add('border-green-400', 'bg-green-50');
                        title.classList.remove('text-slate-400');
                        title.classList.add('text-green-700');
                        title.textContent = `Pilihan ${letter} (Kunci Jawaban)`;
                    } else {
                        box.classList.remove('border-green-400', 'bg-green-50');
                        title.classList.add('text-slate-400');
                        title.classList.remove('text-green-700');
                        title.textContent = `Pilihan ${letter}`;
                    }
                });
            }
            addBtn.addEventListener('click', function() {
                const optIndex = container.children.length;
                const letter = String.fromCharCode(65 + optIndex);
                const label = document.createElement('label');
                label.className = 'option-item flex items-start gap-3 rounded-lg border border-slate-200 p-4';
                label.innerHTML = `<input type="radio" name="jawaban_benar" value="${optIndex}" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500" required><div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Pilihan ${letter}</span><input type="text" name="jawaban[${optIndex}][teks]" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none" required></div><button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>`;
                container.appendChild(label);
                updateStyles();
            });
            container.addEventListener('change', updateStyles);
            container.addEventListener('click', function(e) { if (e.target.closest('.remove-btn')) { e.preventDefault(); e.target.closest('.option-item').remove(); updateStyles(); } });
            updateStyles();
            
            // Set initial state based on old value
            const initialTipeSoal = document.querySelector('input[name="tipe_soal"]:checked')?.value || 'pilihan_ganda';
            toggleTipeSoal(initialTipeSoal);
        });

        function toggleTipeSoal(tipe) {
            const wrapper = document.getElementById('opsiContainerWrapper');
            const requiredInputs = wrapper.querySelectorAll('input[required]');
            
            if (tipe !== 'pilihan_ganda') {
                wrapper.classList.add('hidden');
                // Remove required attribute from multiple choice inputs when essay is selected
                requiredInputs.forEach(input => {
                    input.dataset.wasRequired = 'true';
                    input.required = false;
                });
            } else {
                wrapper.classList.remove('hidden');
                // Restore required attribute
                const wasRequiredInputs = wrapper.querySelectorAll('input[data-was-required="true"]');
                wasRequiredInputs.forEach(input => {
                    input.required = true;
                });
            }
        }
    </script>
</x-banksoal::layouts.dosen-admin>

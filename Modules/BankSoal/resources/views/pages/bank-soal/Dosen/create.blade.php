<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Buat Soal Baru" subtitle="Lengkapi formulir untuk menambahkan butir soal ke bank soal.">
        <x-slot:actions>
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    @if(session('error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Waduh, Gagal...',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#ef4444',
                    background: '#ffffff',
                    customClass: {
                        title: 'text-slate-800 text-xl font-bold',
                        htmlContainer: 'text-slate-600 text-sm',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-semibold transition-colors'
                    }
                });
            });
        </script>
    @endif

    <x-banksoal::ui.panel title="Form Soal" subtitle="Gunakan format pilihan ganda dan tandai satu jawaban benar." padding="p-0">
        <form action="{{ route('banksoal.soal.dosen.store') }}" method="POST" id="formSoal">
            @csrf
            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="mk_id" class="mb-2 block text-sm font-semibold text-slate-700">Mata Kuliah</label>
                    <select name="mk_id" id="mk_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required>
                        <option value="">Pilih Mata Kuliah...</option>
                        @foreach($mataKuliahDosen as $mk)
                            <option value="{{ $mk->id }}" {{ old('mk_id') == $mk->id ? 'selected' : '' }}>{{ $mk->kode }} - {{ $mk->nama }}</option>
                        @endforeach
                    </select>
                    @error('mk_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="cpl_id" class="mb-2 block text-sm font-semibold text-slate-700">CPL</label>
                    <select name="cpl_id" id="cpl_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="">Pilih CPL...</option></select>
                    @error('cpl_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="cpmk_id" class="mb-2 block text-sm font-semibold text-slate-700">CPMK</label>
                    <select name="cpmk_id" id="cpmk_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="">Pilih CPMK...</option></select>
                    @error('cpmk_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="kesulitan" class="mb-2 block text-sm font-semibold text-slate-700">Tingkat Kesulitan</label>
                    <select name="kesulitan" id="kesulitan" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required>
                        <option value="easy" {{ old('kesulitan') == 'easy' ? 'selected' : '' }}>Mudah (Easy)</option>
                        <option value="intermediate" {{ old('kesulitan') == 'intermediate' ? 'selected' : '' }}>Sedang (Medium)</option>
                        <option value="advanced" {{ old('kesulitan') == 'advanced' ? 'selected' : '' }}>Sulit (Hard)</option>
                    </select>
                </div>
                <div>
                    <label for="bobot" class="mb-2 block text-sm font-semibold text-slate-700">Bobot / Skor</label>
                    <input type="number" id="bobot" name="bobot" min="1" value="{{ old('bobot', 10) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required>
                </div>
            </div>

            <div class="px-6 pb-6">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Pertanyaan</label>
                <div class="flex items-center gap-5 text-sm font-medium text-slate-700">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="pilihan_ganda" id="radioPg" {{ old('tipe_soal', 'pilihan_ganda') == 'pilihan_ganda' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                        Pilihan Ganda
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="essay" id="radioEssay" {{ old('tipe_soal') == 'essay' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
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

            <div id="opsiContainerWrapper" class="border-t border-slate-200 px-6 py-5 {{ old('tipe_soal', 'pilihan_ganda') == 'essay' ? 'hidden' : '' }}">
                <div class="mb-3 flex items-center justify-between"><label class="text-sm font-semibold text-slate-700">Opsi Jawaban & Kunci</label><button type="button" id="addOptionBtn" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">+ Tambah Opsi</button></div>
                <div id="optionsContainer" class="space-y-3">
                    @for($i = 0; $i < max(count(old('jawaban', []) ?: [0, 1, 2]), 3); $i++)
                        <label class="option-item flex items-start gap-3 rounded-xl border border-slate-200 p-4 {{ old('jawaban_benar') == (string)$i ? 'border-green-400 bg-green-50' : '' }}">
                            <input type="radio" name="jawaban_benar" value="{{ $i }}" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500" {{ old('jawaban_benar') == (string)$i ? 'checked' : '' }} required>
                            <div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide {{ old('jawaban_benar') == (string)$i ? 'text-green-700' : 'text-slate-400' }}">Pilihan {{ chr(65 + $i) }} {{ old('jawaban_benar') == (string)$i ? '(Kunci Jawaban)' : '' }}</span><input type="text" name="jawaban[{{$i}}][teks]" value="{{ old('jawaban.'.$i.'.teks') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required></div>
                            @if($i > 2)<button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>@endif
                        </label>
                    @endfor
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-4">
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-700">Batalkan</a>
                <div class="flex items-center gap-2"><button type="submit" name="submit_action" value="draft" class="rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Simpan Draft</button><button type="submit" name="submit_action" value="publish" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Publikasi Soal</button></div>
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
            form.onsubmit = function() {
                // Populate hidden input on submit
                var soalInput = document.getElementById('soalInput');
                // Mengambil HTML dari editor, jika kosong/hanya whitespace dikembalikan string kosong supaya validasi server menangkap
                var htmlContent = quill.root.innerHTML;
                if (quill.getText().trim().length === 0 && !htmlContent.includes('<img')) {
                    soalInput.value = '';
                } else {
                    soalInput.value = htmlContent;
                }
            };

            const mkSelect = document.getElementById('mk_id');
            const cplSelect = document.getElementById('cpl_id');
            const cpmkSelect = document.getElementById('cpmk_id');
            const oldCplId = "{{ old('cpl_id') }}";
            const oldCpmkId = "{{ old('cpmk_id') }}";
            
            mkSelect.addEventListener('change', function() {
                const mkId = this.value;
                cplSelect.innerHTML = '<option value="">Memuat CPL...</option>';
                cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                if (mkId) {
                    fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                        .then(r => r.json())
                        .then(data => {
                            cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                            data.forEach(c => {
                                const selected = oldCplId == c.id ? 'selected' : '';
                                cplSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`;
                            });
                            // Trigger change on CPL to load CPMK if old value exists
                            if (oldCplId) cplSelect.dispatchEvent(new Event('change'));
                        })
                        .catch(() => { cplSelect.innerHTML = '<option value="">Gagal memuat cpl</option>'; });
                } else {
                    cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                }
            });

            cplSelect.addEventListener('change', function() {
                const cplId = this.value;
                cpmkSelect.innerHTML = '<option value="">Memuat CPMK...</option>';
                if (cplId) {
                    fetch(`{{ route('banksoal.rps.dosen.cpmk') }}?cpl_ids[]=${cplId}`)
                        .then(r => r.json())
                        .then(data => {
                            cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                            data.forEach(c => {
                                const selected = oldCpmkId == c.id ? 'selected' : '';
                                cpmkSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`;
                            });
                        })
                        .catch(() => { cpmkSelect.innerHTML = '<option value="">Gagal memuat cpmk</option>'; });
                } else {
                    cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                }
            });

            if (mkSelect.value) { mkSelect.dispatchEvent(new Event('change')); }
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
                label.className = 'option-item flex items-start gap-3 rounded-xl border border-slate-200 p-4';
                label.innerHTML = `<input type="radio" name="jawaban_benar" value="${optIndex}" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500" required><div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Pilihan ${letter}</span><input type="text" name="jawaban[${optIndex}][teks]" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required></div><button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>`;
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
            
            if (tipe === 'essay') {
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

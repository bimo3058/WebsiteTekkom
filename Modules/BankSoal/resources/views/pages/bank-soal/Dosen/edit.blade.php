<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Edit Soal" subtitle="Perbarui detail pertanyaan dan opsi jawaban.">
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

    <x-banksoal::ui.panel title="Form Edit Soal" subtitle="Pastikan jawaban benar tetap ditandai sebelum menyimpan." padding="p-0">
        @if(isset($review) && !empty($review->catatan))
        <div class="p-6 pb-0">
            @if(in_array(strtolower($soal->status), ['revisi', 'ditolak']))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-1 text-red-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-red-800">Catatan Revisi dari GPM</h4>
                        <p class="mt-1 text-sm text-red-700 leading-relaxed">{{ $review->catatan }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-history mt-1 text-amber-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800">Riwayat Catatan GPM Sebelumnya</h4>
                        <p class="mt-1 text-sm text-amber-700 leading-relaxed">{{ $review->catatan }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
        
        <form action="{{ route('banksoal.soal.dosen.update', $soal->id) }}" method="POST" id="formSoal">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div><label for="mk_id" class="mb-2 block text-sm font-semibold text-slate-700">Mata Kuliah</label><select name="mk_id" id="mk_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="">Pilih Mata Kuliah...</option>@foreach($mataKuliahDosen as $mk)<option value="{{ $mk->id }}" {{ old('mk_id', $soal->mk_id) == $mk->id ? 'selected' : '' }}>{{ $mk->kode }} - {{ $mk->nama }}</option>@endforeach</select></div>
                <div><label for="cpl_id" class="mb-2 block text-sm font-semibold text-slate-700">Keterkaitan CPL / Topik</label><select name="cpl_id" id="cpl_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="">Pilih CPL...</option></select></div>
                <div><label for="kesulitan" class="mb-2 block text-sm font-semibold text-slate-700">Tingkat Kesulitan</label><select name="kesulitan" id="kesulitan" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="easy" {{ old('kesulitan', $soal->kesulitan) == 'easy' ? 'selected' : '' }}>Mudah (Easy)</option><option value="intermediate" {{ old('kesulitan', $soal->kesulitan) == 'intermediate' ? 'selected' : '' }}>Sedang (Medium)</option><option value="advanced" {{ old('kesulitan', $soal->kesulitan) == 'advanced' ? 'selected' : '' }}>Sulit (Hard)</option></select></div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Pertanyaan</label>
                    <div class="flex items-center gap-5 text-sm font-medium text-slate-700 mt-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipe_soal" value="pilihan_ganda" {{ old('tipe_soal', $soal->tipe_soal ?? 'pilihan_ganda') == 'pilihan_ganda' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                            Pilihan Ganda
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipe_soal" value="essay" {{ old('tipe_soal', $soal->tipe_soal) == 'essay' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500 w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                            Essay
                        </label>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6">
                <label for="soal" class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi Soal</label>
                <input type="hidden" name="soal" id="soalInput" value="{{ old('soal', $soal->soal) }}">
                <div id="editor" class="bg-white">{!! old('soal', $soal->soal) !!}</div>
                <p class="mt-2 text-xs text-slate-500">Anda dapat menambahkan format teks rata-kiri/kanan, list, tabel, dan gambar menggunakan menu di atas.</p>
            </div>
            <div id="opsiContainerWrapper" class="border-t border-slate-200 px-6 py-5 {{ old('tipe_soal', $soal->tipe_soal ?? 'pilihan_ganda') == 'essay' ? 'hidden' : '' }}">
                <div class="mb-3 flex items-center justify-between"><label class="text-sm font-semibold text-slate-700">Pilihan Jawaban</label><button type="button" id="addOptionBtn" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-plus mr-1"></i> Tambah Opsi</button></div>
                <div id="optionsContainer" class="space-y-3">
                    @php $oldJawaban = old('jawaban', $soal->jawaban->toArray()); $oldJawabanBenar = old('jawaban_benar', $soal->jawaban->search(fn($j) => $j['is_benar'])); @endphp
                    @foreach($oldJawaban as $i => $jawab)
                        <label class="option-item flex items-start gap-3 rounded-xl border p-4 {{ (string)$oldJawabanBenar === (string)$i ? 'border-green-400 bg-green-50' : 'border-slate-200' }}"><input type="radio" name="jawaban_benar" value="{{ $i }}" {{ (string)$oldJawabanBenar === (string)$i ? 'checked' : '' }} required class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500"><div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide {{ (string)$oldJawabanBenar === (string)$i ? 'text-green-700' : 'text-slate-400' }}">Pilihan {{ chr(65 + $i) }} {{ (string)$oldJawabanBenar === (string)$i ? '(Kunci Jawaban)' : '' }}</span><input type="text" name="jawaban[{{$i}}][teks]" value="{{ $jawab['deskripsi'] ?? $jawab['teks'] ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required></div>@if($i > 1)<button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>@endif</label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-end border-t border-slate-200 bg-slate-50 px-6 py-4"><button type="submit" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button></div>
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
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        ['clean'],
                        ['link', 'image', 'video']
                    ]
                }
            });

            var form = document.getElementById('formSoal');
            form.onsubmit = function() {
                var soalInput = document.getElementById('soalInput');
                var htmlContent = quill.root.innerHTML;
                if (quill.getText().trim().length === 0 && !htmlContent.includes('<img')) {
                    soalInput.value = '';
                } else {
                    soalInput.value = htmlContent;
                }
            };

            const mkSelect = document.getElementById('mk_id');
            const cplSelect = document.getElementById('cpl_id');
            const oldCplId = "{{ old('cpl_id', $soal->cpl_id) }}";
            mkSelect.addEventListener('change', function() {
                const mkId = this.value;
                cplSelect.innerHTML = '<option value="">Memuat CPL...</option>';
                if (mkId) {
                    fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`).then(r => r.json()).then(data => {
                        cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                        data.forEach(c => { const selected = oldCplId == c.id ? 'selected' : ''; cplSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`; });
                    }).catch(() => { cplSelect.innerHTML = '<option value="">Gagal memuat cpl</option>'; });
                } else {
                    cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                }
            });
            if (mkSelect.value) { mkSelect.dispatchEvent(new Event('change')); }
            const container = document.getElementById('optionsContainer');
            const addBtn = document.getElementById('addOptionBtn');
            let optIndex = container.children.length;
            function updateStyles() {
                const boxes = container.querySelectorAll('.option-item');
                boxes.forEach((box, index) => {
                    const radio = box.querySelector('input[type="radio"]');
                    const title = box.querySelector('.option-title');
                    const letter = String.fromCharCode(65 + index);
                    radio.value = index;
                    if (radio.checked) { box.classList.add('border-green-400', 'bg-green-50'); box.classList.remove('border-slate-200'); title.classList.remove('text-slate-400'); title.classList.add('text-green-700'); title.textContent = `Pilihan ${letter} (Kunci Jawaban)`; }
                    else { box.classList.remove('border-green-400', 'bg-green-50'); box.classList.add('border-slate-200'); title.classList.add('text-slate-400'); title.classList.remove('text-green-700'); title.textContent = `Pilihan ${letter}`; }
                });
            }
            addBtn.addEventListener('click', function() {
                const div = document.createElement('label');
                div.className = 'option-item flex items-start gap-3 rounded-xl border border-slate-200 p-4';
                div.innerHTML = `<input type="radio" name="jawaban_benar" value="${optIndex}" required class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500"><div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Pilihan ${String.fromCharCode(65 + optIndex)}</span><input type="text" name="jawaban[${optIndex}][teks]" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required></div><button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>`;
                container.appendChild(div);
                optIndex++;
                updateStyles();
            });
            container.addEventListener('change', updateStyles);
            container.addEventListener('click', function(e) { if (e.target.closest('.remove-btn')) { e.target.closest('.option-item').remove(); updateStyles(); } });
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

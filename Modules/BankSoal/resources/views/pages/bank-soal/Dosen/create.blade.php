<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Buat Soal Baru" subtitle="Lengkapi formulir untuk menambahkan butir soal ke bank soal.">
        <x-slot:actions>
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}</div>
    @endif

    <x-banksoal::ui.panel title="Form Soal" subtitle="Gunakan format pilihan ganda dan tandai satu jawaban benar." padding="p-0">
        <form action="{{ route('banksoal.soal.dosen.store') }}" method="POST" id="formSoal">
            @csrf
            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
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
                <div class="flex items-center gap-5 text-sm font-medium text-slate-700"><label class="inline-flex items-center gap-2"><input type="radio" name="tipe" value="pg" checked class="text-blue-600 focus:ring-blue-500"> Pilihan Ganda</label><label class="inline-flex items-center gap-2 text-slate-400"><input type="radio" name="tipe" value="essay" disabled> Essay</label></div>
            </div>

            <div class="px-6 pb-6">
                <label for="soal" class="mb-2 block text-sm font-semibold text-slate-700">Pertanyaan</label>
                <textarea name="soal" id="soal" rows="5" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required>{{ old('soal') }}</textarea>
            </div>

            <div class="border-t border-slate-200 px-6 py-5">
                <div class="mb-3 flex items-center justify-between"><label class="text-sm font-semibold text-slate-700">Opsi Jawaban & Kunci</label><button type="button" id="addOptionBtn" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">+ Tambah Opsi</button></div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mkSelect = document.getElementById('mk_id');
            const cplSelect = document.getElementById('cpl_id');
            const oldCplId = "{{ old('cpl_id') }}";
            mkSelect.addEventListener('change', function() {
                const mkId = this.value;
                cplSelect.innerHTML = '<option value="">Memuat CPL...</option>';
                if (mkId) {
                    fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                        .then(r => r.json())
                        .then(data => {
                            cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                            data.forEach(c => {
                                const selected = oldCplId == c.id ? 'selected' : '';
                                cplSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`;
                            });
                        })
                        .catch(() => { cplSelect.innerHTML = '<option value="">Gagal memuat cpl</option>'; });
                } else {
                    cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
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
        });
    </script>
</x-banksoal::layouts.dosen-admin>

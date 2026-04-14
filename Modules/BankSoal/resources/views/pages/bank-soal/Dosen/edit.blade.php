<x-banksoal::layouts.dosen-admin>
    <x-banksoal::ui.page-header title="Edit Soal" subtitle="Perbarui detail pertanyaan dan opsi jawaban.">
        <x-slot:actions>
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"><i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}</div>
    @endif

    <x-banksoal::ui.panel title="Form Edit Soal" subtitle="Pastikan jawaban benar tetap ditandai sebelum menyimpan." padding="p-0">
        <form action="{{ route('banksoal.soal.dosen.update', $soal->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div><label for="mk_id" class="mb-2 block text-sm font-semibold text-slate-700">Mata Kuliah</label><select name="mk_id" id="mk_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="">Pilih Mata Kuliah...</option>@foreach($mataKuliahDosen as $mk)<option value="{{ $mk->id }}" {{ old('mk_id', $soal->mk_id) == $mk->id ? 'selected' : '' }}>{{ $mk->kode }} - {{ $mk->nama }}</option>@endforeach</select></div>
                <div><label for="cpl_id" class="mb-2 block text-sm font-semibold text-slate-700">Keterkaitan CPL / Topik</label><select name="cpl_id" id="cpl_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="">Pilih CPL...</option></select></div>
                <div><label for="kesulitan" class="mb-2 block text-sm font-semibold text-slate-700">Tingkat Kesulitan</label><select name="kesulitan" id="kesulitan" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required><option value="easy" {{ old('kesulitan', $soal->kesulitan) == 'easy' ? 'selected' : '' }}>Mudah (Easy)</option><option value="intermediate" {{ old('kesulitan', $soal->kesulitan) == 'intermediate' ? 'selected' : '' }}>Sedang (Medium)</option><option value="advanced" {{ old('kesulitan', $soal->kesulitan) == 'advanced' ? 'selected' : '' }}>Sulit (Hard)</option></select></div>
            </div>
            <div class="px-6 pb-6"><label for="soal" class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi Soal</label><textarea name="soal" id="soal" rows="5" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" required>{{ old('soal', $soal->soal) }}</textarea></div>
            <div class="border-t border-slate-200 px-6 py-5">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</x-banksoal::layouts.dosen-admin>

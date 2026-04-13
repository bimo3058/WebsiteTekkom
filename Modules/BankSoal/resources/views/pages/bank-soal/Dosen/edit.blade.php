<x-banksoal::layouts.dosen-master>

@include('banksoal::partials.dosen.layout-styles')
@include('banksoal::partials.dosen.sidebar', ['active' => 'bank-soal'])
@include('banksoal::partials.dosen.topbar')

<main class="main">
    <div class="page-header">
        <div class="page-header-left">
            <a href="{{ route('banksoal.soal.dosen.index') }}" style="color: #64748b; font-size: 14px; text-decoration: none; margin-bottom: 5px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali ke daftar
            </a>
            <h1>Edit Soal Baru</h1>
            <p>Ubah pertanyaan yang telah disimpan di bank soal</p>
        </div>
    </div>

    @if(session('error'))
        <div style="padding: 15px; margin-bottom: 20px; background-color: #fee2e2; color: #b91c1c; border-radius: 8px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="section-card">
        <form action="{{ route('banksoal.soal.dosen.update', $soal->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Mata Kuliah</label>
                    <select name="mk_id" id="mk_id" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background-color: #f8fafc;" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($mataKuliahDosen as $mk)
                            <option value="{{ $mk->id }}" {{ old('mk_id', $soal->mk_id) == $mk->id ? 'selected' : '' }}>{{ $mk->kode }} - {{ $mk->nama }}</option>
                        @endforeach
                    </select>
                    @error('mk_id') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Keterkaitan CPL / Topik</label>
                    <select name="cpl_id" id="cpl_id" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background-color: #f8fafc;" required>
                        <option value="">-- Pilih CPL --</option>
                        <!-- Akan diisi via JavaScript berdasarkan MK -->
                    </select>
                    @error('cpl_id') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Tingkat Kesulitan</label>
                <select name="kesulitan" style="width: 100%; max-width: 300px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" required>
                    <option value="easy" {{ old('kesulitan', $soal->kesulitan) == 'easy' ? 'selected' : '' }}>Mudah (Easy)</option>
                    <option value="intermediate" {{ old('kesulitan', $soal->kesulitan) == 'intermediate' ? 'selected' : '' }}>Sedang (Medium)</option>
                    <option value="advanced" {{ old('kesulitan', $soal->kesulitan) == 'advanced' ? 'selected' : '' }}>Sulit (Hard)</option>
                </select>
                @error('kesulitan') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Deskripsi Soal</label>
                <textarea name="soal" rows="5" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; resize: vertical;" placeholder="Ketik pertanyaan Anda di sini..." required>{{ old('soal', $soal->soal) }}</textarea>
                @error('soal') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 16px; margin: 0;">Pilihan Jawaban</label>
                    <button type="button" id="addOptionBtn" class="btn-outline" style="padding: 6px 12px; font-size: 13px;">
                        <i class="fas fa-plus"></i> Tambah Opsi
                    </button>
                </div>
                
                <div id="optionsContainer">
                    @php 
                        $oldJawaban = old('jawaban', $soal->jawaban->toArray());
                        $oldJawabanBenar = old('jawaban_benar', $soal->jawaban->search(fn($j) => $j['is_benar']));
                    @endphp
                    @foreach($oldJawaban as $i => $jawab)
                        <div class="option-row" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <input type="radio" name="jawaban_benar" value="{{ $i }}" {{ (string)$oldJawabanBenar === (string)$i ? 'checked' : '' }} required style="transform: scale(1.3); cursor: pointer;" title="Tandai sebagai jawaban benar">
                            <input type="text" name="jawaban[{{$i}}][teks]" value="{{ $jawab['deskripsi'] ?? $jawab['teks'] ?? '' }}" placeholder="Opsi Jawaban..." style="flex-grow: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" required>
                            @if($i > 1)
                                <button type="button" class="remove-btn" style="color: #ef4444; background: none; border: none; cursor: pointer; padding: 5px;"><i class="fas fa-times-circle" style="font-size: 18px;"></i></button>
                            @endif
                        </div>
                    @endforeach
                </div>
                @error('jawaban') <span style="color: #ef4444; font-size: 12px; display:block; margin-top:5px;">{{ $message }}</span> @enderror
                @error('jawaban_benar') <span style="color: #ef4444; font-size: 12px; display:block; margin-top:5px;">Pilih salah satu jawaban yang benar.</span> @enderror
            </div>

            <div style="text-align: right; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size: 15px;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mkSelect = document.getElementById('mk_id');
        const cplSelect = document.getElementById('cpl_id');
        const oldCplId = '{{ old('cpl_id', $soal->cpl_id) }}';

        // Function untuk trigger select CPL berdasar API
        mkSelect.addEventListener('change', function() {
            const mkId = this.value;
            cplSelect.innerHTML = '<option value="">-- Memuat CPL... --</option>';
            
            if (mkId) {
                // Di route ada: Route::get('/cpl/{mkId?}', [DosenRpsController::class, 'getCplByMk'])->name('cpl');
                // Endpoint ini harusnya me-return data CPL
                fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                    .then(r => r.json())
                    .then(data => {
                        cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
                        data.forEach(c => {
                            const selected = oldCplId == c.id ? 'selected' : '';
                            cplSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`;
                        });
                    })
                    .catch(e => {
                        cplSelect.innerHTML = '<option value="">-- Gagal memuat cpl --</option>';
                    });
            } else {
                cplSelect.innerHTML = '<option value="">-- Pilih CPL --</option>';
            }
        });

        // Trigger change pada load jika old mk_id ada
        if (mkSelect.value) {
            mkSelect.dispatchEvent(new Event('change'));
        }

        // Dynamic Options
        const container = document.getElementById('optionsContainer');
        const addBtn = document.getElementById('addOptionBtn');
        let optIndex = container.children.length;

        addBtn.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'option-row';
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.gap = '15px';
            div.style.marginBottom = '15px';

            div.innerHTML = `
                <input type="radio" name="jawaban_benar" value="${optIndex}" required style="transform: scale(1.3); cursor: pointer;" title="Tandai sebagai jawaban benar">
                <input type="text" name="jawaban[${optIndex}][teks]" placeholder="Opsi Jawaban..." style="flex-grow: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" required>
                <button type="button" class="remove-btn" style="color: #ef4444; background: none; border: none; cursor: pointer; padding: 5px;"><i class="fas fa-times-circle" style="font-size: 18px;"></i></button>
            `;
            container.appendChild(div);
            optIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) {
                e.target.closest('.option-row').remove();
            }
        });
    });
</script>
</x-banksoal::layouts.dosen-master>

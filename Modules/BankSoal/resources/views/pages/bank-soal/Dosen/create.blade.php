<x-banksoal::layouts.dosen-master>

@include('banksoal::partials.dosen.layout-styles')
@include('banksoal::partials.dosen.sidebar', ['active' => 'bank-soal'])
@include('banksoal::partials.dosen.topbar')

<style>
    .form-label { font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block; }
    .form-control { width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #0f172a; outline: none; transition: border-color 0.2s; background-color: #fff; }
    .form-control:focus { border-color: #2563eb; }
    .form-control:disabled, .form-control[readonly] { background-color: #f8fafc; color: #64748b; }
    
    .opt-box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 16px; background: #fff; transition: all 0.2s; position: relative; }
    .opt-box.is-correct { border-color: #22c55e; background-color: #f0fdf4; box-shadow: 0 0 0 1px #22c55e; }
    .opt-box.is-correct .opt-title { color: #166534; font-weight: 800; display: block; margin-bottom: 4px; font-size: 11px; }
    .opt-box:not(.is-correct) .opt-title { color: #94a3b8; font-weight: 700; display: block; margin-bottom: 4px; font-size: 11px; text-transform: uppercase; }
    
    .opt-radio { margin-top: 10px; transform: scale(1.3); cursor: pointer; accent-color: #22c55e; }
    .opt-input { flex-grow: 1; min-width: 0; }
    .opt-input input { width: 100%; border: none; background: transparent; outline: none; font-size: 14px; padding: 4px 0; color: #0f172a; }
    .opt-input input::placeholder { color: #94a3b8; }
    
    .btn-action { padding: 12px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; cursor: pointer; border: none; transition: background 0.2s; display: inline-flex; align-items: center; justify-content: center; }
    .btn-draft { background-color: #1e293b; color: #fff; }
    .btn-draft:hover { background-color: #0f172a; }
    .btn-publish { background-color: #2563eb; color: #fff; }
    .btn-publish:hover { background-color: #1d4ed8; }
    
    .radio-wrap { display: flex; align-items: center; gap: 20px; font-size: 14px; color: #0f172a; font-weight: 600; }
    .radio-wrap label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .radio-wrap input[type="radio"] { accent-color: #2563eb; transform: scale(1.2); }
</style>

<main class="main" style="background-color: #f1f5f9; padding: 40px;">
    
    @if(session('error'))
        <div style="padding: 15px; margin-bottom: 20px; background-color: #fee2e2; color: #b91c1c; border-radius: 8px; max-width: 850px; margin-left: auto; margin-right: auto;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="max-width: 850px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">Buat Soal Baru</h2>
                <p style="font-size: 13px; color: #64748b; margin: 0;">Lengkapi formulir di bawah untuk menambahkan butir soal.</p>
            </div>
            <div>
                <a href="{{ route('banksoal.soal.dosen.index') }}" style="color: #94a3b8; font-size: 22px; text-decoration: none; padding: 4px 8px; border-radius: 6px; transition: 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">&times;</a>
            </div>
        </div>

        <form action="{{ route('banksoal.soal.dosen.store') }}" method="POST" id="formSoal">
            @csrf
            
            <div style="padding: 32px 24px;">
                
                <!-- Baris 1: Mata Kuliah & CPL -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label class="form-label">Mata Kuliah</label>
                        <select name="mk_id" id="mk_id" class="form-control" required style="background-image: url('data:image/svg+xml;utf8,<svg width=\"14\" height=\"14\" fill=\"%2364748b\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z\"/></svg>'); background-repeat: no-reverse; background-position: right 16px center; background-size: 10px; appearance: none;">
                            <option value="">Pilih Mata Kuliah...</option>
                            @foreach($mataKuliahDosen as $mk)
                                <option value="{{ $mk->id }}" {{ old('mk_id') == $mk->id ? 'selected' : '' }}>{{ $mk->kode }} - {{ $mk->nama }}</option>
                            @endforeach
                        </select>
                        @error('mk_id') <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="form-label">CPL (Capaian Pembelajaran Lulusan)</label>
                        <select name="cpl_id" id="cpl_id" class="form-control" required style="background-image: url('data:image/svg+xml;utf8,<svg width=\"14\" height=\"14\" fill=\"%2364748b\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z\"/></svg>'); background-repeat: no-reverse; background-position: right 16px center; background-size: 10px; appearance: none;">
                            <option value="">Pilih CPL...</option>
                        </select>
                        @error('cpl_id') <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Baris 2: Kesulitan & Bobot -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label class="form-label">Tingkat Kesulitan</label>
                        <select name="kesulitan" class="form-control" required>
                            <option value="easy" {{ old('kesulitan') == 'easy' ? 'selected' : '' }}>Mudah (Easy)</option>
                            <option value="intermediate" {{ old('kesulitan') == 'intermediate' ? 'selected' : '' }}>Sedang (Medium)</option>
                            <option value="advanced" {{ old('kesulitan') == 'advanced' ? 'selected' : '' }}>Sulit (Hard)</option>
                        </select>
                        @error('kesulitan') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Bobot / Skor</label>
                        <input type="number" name="bobot" class="form-control" value="{{ old('bobot', 10) }}" placeholder="Contoh: 10" required min="1">
                        @error('bobot') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Baris 3: Tipe Pertanyaan -->
                <div style="margin-bottom: 24px;">
                    <label class="form-label">Tipe Pertanyaan</label>
                    <div class="radio-wrap">
                        <label><input type="radio" name="tipe" value="pg" checked> Pilihan Ganda</label>
                        <label style="opacity: 0.5;"><input type="radio" name="tipe" value="essay" disabled> Essay</label>
                    </div>
                </div>

                <!-- Baris 4: Teks Pertanyaan -->
                <div style="margin-bottom: 24px;">
                    <label class="form-label">Pertanyaan</label>
                    <textarea name="soal" rows="5" class="form-control" placeholder="Tuliskan isi pertanyaan di sini..." style="resize: vertical; min-height: 120px;" required>{{ old('soal') }}</textarea>
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                        <a href="javascript:void(0)" style="font-size: 12px; color: #2563eb; font-weight: 600; text-decoration: none;">
                            <i class="far fa-image"></i> Tambah Gambar
                        </a>
                        <span style="font-size: 11px; color: #94a3b8; font-weight: 700; letter-spacing: 0.5px;">MARKDOWN DIDUKUNG</span>
                    </div>
                    @error('soal') <span style="color: #ef4444; font-size: 12px;">{{ $message }}</span> @enderror
                </div>

                <!-- Baris 5: Opsi Jawaban & Kunci -->
                <div style="margin-bottom: 20px;">
                    <label class="form-label">Opsi Jawaban & Kunci</label>
                    
                    <div id="optionsContainer">
                        @for($i = 0; $i < max(count(old('jawaban', []) ?: [0, 1, 2]), 3); $i++)
                            <label class="opt-box {{ old('jawaban_benar') == (string)$i ? 'is-correct' : '' }}">
                                <input type="radio" name="jawaban_benar" value="{{ $i }}" class="opt-radio" {{ old('jawaban_benar') == (string)$i ? 'checked' : '' }} required title="Tandai sebagai jawaban benar">
                                <div class="opt-input">
                                    <span class="opt-title">Pilihan {{ chr(65 + $i) }} {{ old('jawaban_benar') == (string)$i ? '(KUNCI JAWABAN)' : '' }}</span>
                                    <input type="text" name="jawaban[{{$i}}][teks]" value="{{ old('jawaban.'.$i.'.teks') }}" placeholder="Masukkan teks jawaban {{ chr(65 + $i) }}..." required autocomplete="off">
                                </div>
                                @if($i > 2)
                                    <button type="button" class="remove-btn" style="position:absolute; right:16px; top:20px; color: #ef4444; background: none; border: none; cursor: pointer; padding: 5px; opacity:0.6;"><i class="fas fa-times-circle" style="font-size: 16px;"></i></button>
                                @endif
                            </label>
                        @endfor
                    </div>
                    @error('jawaban') <span style="color: #ef4444; font-size: 12px; display:block; margin-top:5px;">{{ $message }}</span> @enderror
                    @error('jawaban_benar') <span style="color: #ef4444; font-size: 12px; display:block; margin-top:5px;">Pilih salah satu jawaban yang benar.</span> @enderror
                    
                    <div style="margin-top: 16px;">
                        <a href="javascript:void(0)" id="addOptionBtn" style="font-size: 13px; color: #2563eb; font-weight: 700; text-decoration: none;">
                            + Tambah Opsi Lainnya
                        </a>
                    </div>
                </div>

            </div>

            <!-- Action Bar -->
            <div style="padding: 20px 24px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                <a href="{{ route('banksoal.soal.dosen.index') }}" style="color: #64748b; font-weight: 700; font-size: 14px; text-decoration: none;">Batalkan</a>
                <div style="display: flex; gap: 12px;">
                    <button type="submit" name="submit_action" value="draft" class="btn-action btn-draft">
                        Simpan sebagai Draft
                    </button>
                    <button type="submit" name="submit_action" value="publish" class="btn-action btn-publish">
                        Publikasi Soal
                    </button>
                </div>
            </div>

        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mkSelect = document.getElementById('mk_id');
        const cplSelect = document.getElementById('cpl_id');
        const oldCplId = '{{ old('cpl_id') }}';

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
                    .catch(e => {
                        cplSelect.innerHTML = '<option value="">Gagal memuat cpl</option>';
                    });
            } else {
                cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
            }
        });

        if (mkSelect.value) { mkSelect.dispatchEvent(new Event('change')); }

        // Logic Visual Radio / Kunci Jawaban
        const container = document.getElementById('optionsContainer');
        
        function updateStyles() {
            const boxes = container.querySelectorAll('.opt-box');
            boxes.forEach((box, index) => {
                const radio = box.querySelector('input[type="radio"]');
                const title = box.querySelector('.opt-title');
                const letter = String.fromCharCode(65 + index); // A, B, C...
                
                if(radio.checked) {
                    box.classList.add('is-correct');
                    title.textContent = `PILIHAN ${letter} (KUNCI JAWABAN)`;
                } else {
                    box.classList.remove('is-correct');
                    title.textContent = `PILIHAN ${letter}`;
                }
            });
        }
        
        container.addEventListener('change', updateStyles);
        
        // Add Button
        const addBtn = document.getElementById('addOptionBtn');
        addBtn.addEventListener('click', function() {
            let optIndex = container.children.length;
            let letter = String.fromCharCode(65 + optIndex);
            
            const label = document.createElement('label');
            label.className = 'opt-box';
            label.innerHTML = `
                <input type="radio" name="jawaban_benar" value="${optIndex}" class="opt-radio" required title="Tandai sebagai jawaban benar">
                <div class="opt-input">
                    <span class="opt-title">Pilihan ${letter}</span>
                    <input type="text" name="jawaban[${optIndex}][teks]" placeholder="Masukkan teks jawaban ${letter}..." required autocomplete="off">
                </div>
                <button type="button" class="remove-btn" style="position:absolute; right:16px; top:20px; color: #ef4444; background: none; border: none; cursor: pointer; padding: 5px; opacity:0.6;"><i class="fas fa-times-circle" style="font-size: 16px;"></i></button>
            `;
            container.appendChild(label);
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) {
                e.preventDefault();
                e.target.closest('.opt-box').remove();
                updateStyles(); // refresh letters
            }
        });
    });
</script>
</x-banksoal::layouts.dosen-master>

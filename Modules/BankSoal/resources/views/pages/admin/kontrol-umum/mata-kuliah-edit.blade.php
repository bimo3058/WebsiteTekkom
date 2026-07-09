<x-banksoal::layouts.admin>
    @push('styles')
    <style>
        :root {
            --primary-blue: rgb(11, 38, 110);
            --primary-hover: rgb(8, 28, 82);
            --danger-red: #ef4444;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
        }

        .form-section {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--slate-100);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-700);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
            color: var(--slate-800);
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-error {
            display: none;
            margin-top: 6px;
            font-size: 12px;
            color: var(--danger-red);
        }

        .form-error.show {
            display: block;
        }

        .sks-counter {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sks-counter button {
            width: 42px;
            height: 42px;
            border: 1px solid var(--slate-300);
            background: var(--slate-50);
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            color: var(--slate-600);
            transition: all 0.2s;
        }

        .sks-counter input {
            width: 70px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--slate-100);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: #fff;
            box-shadow: 0 2px 4px rgba(11, 38, 110, 0.15);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #fff;
            color: var(--slate-700);
            border: 1px solid var(--slate-300);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>
    @endpush

    @section('breadcrumbs')
    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="text-slate-500 hover:text-primary transition-colors">Manajemen Data</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Edit Mata Kuliah</span>
    @endsection

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="header-content">
            <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-800); margin: 0;">Edit Mata Kuliah</h1>
            <p style="font-size: 14px; color: var(--slate-500); margin-top: 4px;">Perbarui informasi mata kuliah <strong>{{ $mataKuliah->nama }}</strong></p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="form-section">
        <h2 class="form-title">Informasi Mata Kuliah</h2>
        <form id="mataKuliahForm" onsubmit="handleFormSubmit(event)">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="kode">Kode Mata Kuliah <span style="color: var(--danger-red)">*</span></label>
                <input type="text" id="kode" name="kode" value="{{ $mataKuliah->kode }}" placeholder="Contoh: TSK6103" required maxlength="50">
                <div class="form-error" id="error-kode"></div>
            </div>

            <div class="form-group">
                <label for="nama">Nama Mata Kuliah <span style="color: var(--danger-red)">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ $mataKuliah->nama }}" placeholder="Contoh: Dasar Komputer &amp; Pemrograman" required>
                <div class="form-error" id="error-nama"></div>
            </div>

            <div class="form-group">
                <label for="sks">Jumlah SKS <span style="color: var(--danger-red)">*</span></label>
                <div class="sks-counter">
                    <button type="button" onclick="decrementSKS()">-</button>
                    <input type="number" id="sks" name="sks" value="{{ $mataKuliah->sks }}" min="1" max="3" readonly>
                    <button type="button" onclick="incrementSKS()">+</button>
                </div>
                <div class="form-error" id="error-sks"></div>
            </div>

            <div class="form-group">
                <label for="semester">Semester <span style="color: var(--danger-red)">*</span></label>
                <select id="semester" name="semester" required>
                    <option value="">-- Pilih Semester --</option>
                    @for($i=1; $i<=8; $i++)
                        <option value="{{ $i }}" {{ $mataKuliah->semester == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                    @endfor
                </select>
                <div class="form-error" id="error-semester"></div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Perbarui Mata Kuliah</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const UPDATE_URL = '{{ route("banksoal.api.v1.admin.mata-kuliah.update", $mataKuliah->id) }}';
        const csrfToken = '{{ csrf_token() }}';

        function incrementSKS() {
            const input = document.getElementById('sks');
            if (parseInt(input.value) < 3) input.value = parseInt(input.value) + 1;
        }

        function decrementSKS() {
            const input = document.getElementById('sks');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }

        async function handleFormSubmit(e) {
            e.preventDefault();
            
            document.querySelectorAll('.form-error').forEach(el => {
                el.classList.remove('show');
                el.textContent = '';
            });

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
            window.showLoader();

            const payload = {
                _method: 'PUT',
                kode: document.getElementById('kode').value,
                nama: document.getElementById('nama').value,
                sks: parseInt(document.getElementById('sks').value),
                semester: parseInt(document.getElementById('semester').value)
            };

            try {
                const response = await fetch(UPDATE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: data.message || 'Mata kuliah berhasil diperbarui' } }));
                    setTimeout(() => {
                        window.location.href = '{{ route("banksoal.admin.kontrol-umum.mata-kuliah") }}';
                    }, 1500);
                } else {
                    if (response.status === 422 && data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const errEl = document.getElementById(`error-${field}`);
                            if (errEl) {
                                errEl.textContent = messages[0];
                                errEl.classList.add('show');
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Gagal memperbarui mata kuliah');
                    }
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: error.message || 'Terjadi kesalahan sistem' } }));
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                window.hideLoader();
            }
        }
    </script>
    @endpush
</x-banksoal::layouts.admin>

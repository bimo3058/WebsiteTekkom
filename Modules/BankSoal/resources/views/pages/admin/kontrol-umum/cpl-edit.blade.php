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

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
            color: var(--slate-800);
            transition: all 0.2s;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
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
    <span class="text-slate-800 font-semibold">Edit CPL</span>
    @endsection

    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="header-content">
            <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-800); margin: 0;">Edit CPL</h1>
            <p style="font-size: 14px; color: var(--slate-500); margin-top: 4px;">Perbarui deskripsi Capaian Pembelajaran Lulusan.</p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="form-section">
        <h2 class="form-title">Informasi CPL</h2>
        <form id="cplEditForm" onsubmit="handleFormSubmit(event)">
            @csrf
            <div class="form-group">
                <label for="kode">Kode CPL</label>
                <input type="text" id="kode" name="kode" value="{{ $cpl->kode }}" required maxlength="50">
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi CPL <span style="color: var(--danger-red)">*</span></label>
                <textarea id="deskripsi" name="deskripsi" required>{{ $cpl->deskripsi }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const UPDATE_URL = '{{ route("banksoal.api.v1.admin.cpl.update", $cpl->id) }}';
        const csrfToken = '{{ csrf_token() }}';

        async function handleFormSubmit(e) {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            window.showLoader();

            const payload = {
                _method: 'PUT',
                kode: document.getElementById('kode').value,
                deskripsi: document.getElementById('deskripsi').value
            };

            try {
                const response = await fetch(UPDATE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: 'Data berhasil disimpan.' } }));
                    setTimeout(() => {
                        window.location.href = '{{ route("banksoal.admin.kontrol-umum.mata-kuliah") }}';
                    }, 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: data.message || 'Terjadi kesalahan.' } }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: 'Terjadi kesalahan sistem.' } }));
            } finally { 
                submitBtn.disabled = false; 
                window.hideLoader();
            }
        }
    </script>
    @endpush
</x-banksoal::layouts.admin>

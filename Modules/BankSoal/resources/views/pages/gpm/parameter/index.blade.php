<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Kontrol Umum</span>
    @endsection

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0B266E;
            --primary-hover: #081C52;
            --danger-red: #ef4444;
            --slate-200: #e2e8f0;
            --slate-700: #334155;
            --slate-800: #1e293b;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            color: #1e293b;
            padding: 10px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .btn-add:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        /* ── 3-dot dropdown ── */
        .dots-wrap { position: relative; display: inline-block; }
        .btn-dots {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 8px;
            border: 1px solid var(--slate-200); background: #fff;
            font-size: 18px; cursor: pointer; color: #64748b;
            transition: all 0.15s;
        }
        .btn-dots:hover { border-color: var(--primary-blue); color: var(--primary-blue); background: #f0f4ff; }
        
        .dots-menu {
            display: none; position: absolute; right: 0; top: 100%;
            background: #fff; border: 1px solid var(--slate-200);
            border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            min-width: 180px; z-index: 50; overflow: hidden;
            margin-top: 8px;
        }
        .dots-menu.open { display: block; }
        .dots-menu a, .dots-menu button {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 12px 16px;
            background: none; border: none; border-bottom: 1px solid #f1f5f9;
            font-size: 13px; font-weight: 600; color: var(--slate-700);
            cursor: pointer; text-align: left; transition: background 0.2s;
        }
        .dots-menu a:hover, .dots-menu button:hover { background: #f8fafc; color: var(--primary-blue); }
        .dots-menu .menu-delete { color: var(--danger-red); }
        .dots-menu .menu-delete:hover { background: #fff1f2; color: var(--danger-red); }
    </style>
    @endpush

    <x-banksoal::notification.alerts />

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Manajemen Parameter</h1>
            <p class="text-slate-500 mt-1">Kelola aspek penilaian standar untuk RPS dan Bank Soal.</p>
        </div>
        <a href="{{ route('banksoal.soal.gpm.parameter.create') }}" class="btn-add hover:shadow-lg transition-all">
            <i class="fas fa-plus text-[10px] bg-primary text-white p-1.5 rounded-full mr-2"></i>
            Tambah Parameter
        </a>
    </div>

    <!-- Controls Section -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="searchInput" placeholder="Cari aspek parameter..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary outline-none transition-all">
        </div>

        <div class="flex items-center gap-4 bg-slate-50 border border-slate-100 px-4 py-2 rounded-2xl">
            <div class="hidden sm:block">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Standar Skor</p>
                <p class="text-[9px] text-slate-400">Minimal Lulus</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="number" id="skorMinimumInput" value="{{ $skorMinimum }}" min="0" max="100" class="w-16 px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-center focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                <button type="button" onclick="saveSkorMinimum()" class="bg-[#0B266E] text-white text-[11px] font-bold px-4 py-2 rounded-lg hover:opacity-90 transition-all shadow-sm">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/30 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Daftar Parameter Penilaian</span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 px-2 py-1 rounded-md">{{ count($parameters) }} Total</span>
        </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-6 py-5 w-16 text-center">No</th>
                                <th class="px-6 py-5">Jenis Penilaian</th>
                                <th class="px-6 py-5">Aspek Parameter</th>
                                <th class="px-6 py-5 text-right">Bobot (Poin)</th>
                                <th class="px-6 py-5 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($parameters as $param)
                            <tr class="hover:bg-slate-50/50 transition-colors group parameter-row" data-aspek="{{ strtolower($param->aspek) }}">
                                <td class="px-6 py-5 text-center text-sm font-medium text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase border {{ $param->jenis === 'rps' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-purple-50 text-purple-600 border-purple-100' }}">
                                        {{ $param->jenis === 'rps' ? 'RPS' : 'Bank Soal' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-bold text-slate-700 leading-relaxed">{{ $param->aspek }}</p>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                        {{ $param->bobot }} Poin
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="dots-wrap" id="dots-{{ $param->id }}">
                                        <button class="btn-dots" onclick="toggleMenu({{ $param->id }}, event)">
                                            <i class="fas fa-ellipsis-v text-xs"></i>
                                        </button>
                                        <div class="dots-menu" id="menu-{{ $param->id }}">
                                            <a href="{{ route('banksoal.soal.gpm.parameter.edit', $param->id) }}">
                                                <i class="fas fa-edit w-4 text-blue-500"></i> Edit Parameter
                                            </a>
                                            <button class="menu-delete" onclick="deleteParameter({{ $param->id }}, '{{ addslashes($param->aspek) }}')">
                                                <i class="fas fa-trash-alt w-4"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <i class="fas fa-clipboard-list text-5xl mb-4"></i>
                                        <p class="text-sm font-medium">Belum ada parameter yang didefinisikan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
        const API_SKOR_URL = '{{ route("banksoal.soal.gpm.parameter.skor.update") }}';
        const csrfToken = '{{ csrf_token() }}';

        // Search logic
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.parameter-row').forEach(row => {
                const aspek = row.getAttribute('data-aspek');
                row.style.display = aspek.includes(query) ? '' : 'none';
            });
        });

        // --- Menu Logic ---
        function toggleMenu(id, event) {
            event.stopPropagation();
            const menus = document.querySelectorAll('.dots-menu');
            menus.forEach(m => {
                if (m.id !== `menu-${id}`) m.classList.remove('open');
            });
            const menu = document.getElementById(`menu-${id}`);
            menu.classList.toggle('open');
        }

        document.addEventListener('click', () => {
            document.querySelectorAll('.dots-menu').forEach(m => m.classList.remove('open'));
        });

        async function deleteParameter(id, aspek) {
            const confirm = await Swal.fire({
                title: 'Hapus Parameter?',
                text: `Anda yakin ingin menghapus parameter "${aspek}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            });

            if (confirm.isConfirmed) {
                try {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const deleteUrl = `{{ url('/bank-soal/soal/gpm/parameter') }}/${id}`;
                    
                    const response = await fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', result.message || 'Gagal menghapus parameter.', 'error');
                    }
                } catch (error) {
                    console.error('Delete error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                }
            }
        }

        async function saveSkorMinimum() {
            const skorInput = document.getElementById('skorMinimumInput');
            const skor = skorInput.value;

            if (skor === '' || skor < 0 || skor > 100) {
                Swal.fire('Input Tidak Valid', 'Skor harus diisi antara 0 hingga 100.', 'warning');
                return;
            }

            try {
                const response = await fetch(API_SKOR_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ skor: skor })
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal!', result.message || 'Gagal menyimpan skor.', 'error');
                }
            } catch (error) {
                console.error('Save skor error:', error);
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            }
        }
    </script>
    @endpush
</x-banksoal::layouts.gpm-master>

<x-manajemenmahasiswa::layouts.admin>
    @push('styles')
    <style>
        .user-card { transition: all 0.2s; }
        .card-chevron { transition: transform 0.3s; }
        .card-chevron.rotated { transform: rotate(180deg); }

        /* Page Header */
        .mp-page-header {
            margin-bottom: 28px;
        }
        .mp-page-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 2px;
            letter-spacing: -0.02em;
        }
        .mp-page-header p {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }

        /* Search Bar */
        .mp-search-bar {
            position: relative;
        }
        .mp-search-bar input {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 9px 14px 9px 38px;
            font-size: 13px;
            color: #374151;
            width: 280px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .mp-search-bar input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background: #fff;
        }
        .mp-search-bar svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        /* Stat Cards */
        .mp-stat-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        .mp-stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .mp-stat-card:hover {
            border-color: #c7d2fe;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.08);
            transform: translateY(-2px);
        }
        .mp-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .mp-stat-icon.mahasiswa { background: #fef3c7; color: #d97706; }
        .mp-stat-icon.pengurus  { background: #e0e7ff; color: #4f46e5; }
        .mp-stat-icon.alumni    { background: #d1fae5; color: #059669; }
        .mp-stat-info .mp-stat-count {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }
        .mp-stat-info .mp-stat-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            margin-top: 2px;
        }

        /* Section Header */
        .mp-section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }
        .mp-section-header h2 {
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            white-space: nowrap;
            margin: 0;
        }
        .mp-section-header .mp-line {
            height: 1px;
            background: #e5e7eb;
            flex-grow: 1;
        }
        .mp-section-header .mp-view-all {
            font-size: 13px;
            font-weight: 600;
            color: #6366f1;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            transition: color 0.2s;
        }
        .mp-section-header .mp-view-all:hover {
            color: #4338ca;
        }

        /* Button */
        .mp-btn-outline {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .mp-btn-outline:hover {
            border-color: #6366f1;
            color: #6366f1;
        }

        /* Flash Messages */
        .mp-flash {
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
            border: none;
        }
        .mp-flash.success {
            background: #dcfce7;
            color: #16a34a;
        }
        .mp-flash.error {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Empty State */
        .mp-empty {
            background: #fff;
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            padding: 36px;
            text-align: center;
        }
        .mp-empty p {
            color: #9ca3af;
            font-size: 13px;
            font-weight: 600;
            margin: 0;
        }

        /* User Card List */
        .mp-card-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
    </style>
    @endpush

    {{-- Header --}}
    <div class="mp-page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <h1>Manajemen Pengguna</h1>
            <p>Kelola role anggota himpunan, pengurus, dan alumni</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Search --}}
            <form action="{{ route('manajemenmahasiswa.pengguna.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <div class="mp-search-bar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIM, email...">
                </div>
                @if($search)
                    <a href="{{ route('manajemenmahasiswa.pengguna.index') }}" style="font-size:12px;color:#6b7280;text-decoration:none;font-weight:600;">
                        Reset
                    </a>
                @endif
            </form>

            {{-- Cek Alumni (admin only) --}}
            @if(auth()->user()->hasAnyRole(['admin_kemahasiswaan','admin','superadmin']))
                <button onclick="openAlumniModal()" class="mp-btn-outline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                    Cek Alumni Otomatis
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mp-flash success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mp-flash error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stat Cards --}}
    @php
        $categoryIcons = [
            'Mahasiswa Aktif'   => 'mahasiswa',
            'Pengurus Himpunan' => 'pengurus',
            'Alumni'            => 'alumni',
        ];
        $categoryRoutes = [
            'Mahasiswa Aktif'   => route('manajemenmahasiswa.pengguna.category', 'Mahasiswa Aktif'),
            'Pengurus Himpunan' => route('manajemenmahasiswa.pengguna.category', 'Pengurus Himpunan'),
            'Alumni'            => route('manajemenmahasiswa.pengguna.category', 'Alumni'),
        ];
        $categorySvg = [
            'Mahasiswa Aktif'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
            'Pengurus Himpunan' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
            'Alumni'            => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>',
        ];
    @endphp

    <div class="mp-stat-row">
        @foreach($categories as $title => $sectionUsers)
            <a href="{{ $categoryRoutes[$title] }}" class="mp-stat-card">
                <div class="mp-stat-icon {{ $categoryIcons[$title] }}">
                    {!! $categorySvg[$title] !!}
                </div>
                <div class="mp-stat-info">
                    <div class="mp-stat-count">{{ count($sectionUsers) }}</div>
                    <div class="mp-stat-label">{{ $title }}</div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Category Sections --}}
    <div style="display:flex;flex-direction:column;gap:36px;">
        @foreach($categories as $title => $sectionUsers)
            <section>
                {{-- Section Header --}}
                <div class="mp-section-header">
                    <h2>{{ $title }}</h2>
                    <div class="mp-line"></div>
                    <a href="{{ $categoryRoutes[$title] }}" class="mp-view-all">
                        Lihat Semua
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>

                {{-- User Cards --}}
                <div class="mp-card-list">
                    @forelse($sectionUsers as $user)
                        @include('manajemenmahasiswa::permissions._user_card', [
                            'user'            => $user,
                            'assignableRoles' => $assignableRoles,
                        ])
                    @empty
                        <div class="mp-empty">
                            <p>Tidak ada pengguna</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    {{-- Modal Cek Alumni --}}
    <div id="alumniModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;padding:28px;width:460px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                <div style="width:40px;height:40px;background:#d1fae5;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                </div>
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">Cek Alumni Otomatis</h3>
                    <p style="font-size:12px;color:#6b7280;margin:0;">Mahasiswa angkatan ≤ {{ now()->year - 5 }} akan dijadikan alumni</p>
                </div>
            </div>

            <div id="alumniPreview" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:20px;min-height:60px;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:13px;color:#6b7280;">Klik "Preview" untuk melihat data...</span>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button onclick="previewAlumni()" class="mp-btn-outline" style="padding:8px 18px;">
                    Preview
                </button>
                <button onclick="closeAlumniModal()" class="mp-btn-outline" style="padding:8px 18px;">
                    Batal
                </button>
                <button id="btnExecAlumni" onclick="execAlumni()" disabled
                    style="background:#e5e7eb;border:none;border-radius:10px;padding:8px 18px;font-size:13px;font-weight:700;color:#9ca3af;cursor:not-allowed;transition:all .2s;">
                    Jalankan
                </button>
            </div>
        </div>
    </div>

    @include('manajemenmahasiswa::permissions._scripts')

    @push('scripts')
    <script>
    // ── Alumni Modal ──────────────────────────────────────────────────────────
    function openAlumniModal() {
        document.getElementById('alumniModal').style.display = 'flex';
    }
    function closeAlumniModal() {
        document.getElementById('alumniModal').style.display = 'none';
        document.getElementById('alumniPreview').innerHTML = '<span style="font-size:13px;color:#6b7280;">Klik "Preview" untuk melihat data...</span>';
        document.getElementById('btnExecAlumni').disabled = true;
        document.getElementById('btnExecAlumni').style.cssText = 'background:#e5e7eb;border:none;border-radius:10px;padding:8px 18px;font-size:13px;font-weight:700;color:#9ca3af;cursor:not-allowed;transition:all .2s;';
    }
    function previewAlumni() {
        const preview = document.getElementById('alumniPreview');
        preview.innerHTML = '<span style="font-size:13px;color:#6b7280;">Memuat...</span>';

        fetch('{{ route('manajemenmahasiswa.pengguna.check-alumni') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ dry_run: true }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                preview.innerHTML = `<span style="color:#dc2626;font-size:13px;">${data.error}</span>`;
                return;
            }
            const names = data.preview.map(p => `<span style="font-size:12px;color:#374151;">• ${p.name} (${p.nim}, angkatan ${p.cohort_year})</span>`).join('<br>');
            const more  = data.count > 10 ? `<br><span style="font-size:11px;color:#9ca3af;">...dan ${data.count - 10} lainnya</span>` : '';
            preview.innerHTML = `
                <div style="width:100%;">
                    <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 8px;">${data.message}</p>
                    <div style="line-height:1.8;">${names}${more}</div>
                </div>`;

            if (data.count > 0) {
                const btn = document.getElementById('btnExecAlumni');
                btn.disabled = false;
                btn.style.cssText = 'background:#059669;border:none;border-radius:10px;padding:8px 18px;font-size:13px;font-weight:700;color:#fff;cursor:pointer;transition:all .2s;';
            }
        })
        .catch(() => {
            preview.innerHTML = '<span style="color:#dc2626;font-size:13px;">Gagal mengambil data.</span>';
        });
    }
    function execAlumni() {
        if (!confirm('Yakin ingin mengubah semua mahasiswa yang terdeteksi menjadi alumni?')) return;

        fetch('{{ route('manajemenmahasiswa.pengguna.check-alumni') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ dry_run: false }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeAlumniModal();
                location.reload();
            } else {
                alert('Gagal: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(() => alert('Terjadi kesalahan jaringan.'));
    }
    </script>
    @endpush
</x-manajemenmahasiswa::layouts.admin>

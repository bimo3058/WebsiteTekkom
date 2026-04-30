<x-manajemenmahasiswa::layouts.admin>
    @push('styles')
    <style>
        .user-card { transition: all 0.2s; }
        .card-chevron { transition: transform 0.3s; }
        .card-chevron.rotated { transform: rotate(180deg); }
    </style>
    @endpush

    <div class="min-h-screen" style="background:#F8F9FA; padding: 28px;">
        <div style="max-width: 100%;">

            {{-- Header --}}
            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <h1 style="font-size:20px; font-weight:700; color:#1A1C1E; letter-spacing:-0.02em; margin:0;">
                        Manajemen Pengguna
                    </h1>
                    <p style="color:#6C757D; font-size:13px; margin:4px 0 0;">
                        Kelola role anggota himpunan, pengurus, dan alumni
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Search --}}
                    <form action="{{ route('manajemenmahasiswa.pengguna.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <div style="position:relative;">
                            <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:16px;color:#ADB5BD;">search</span>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIM, email..."
                                style="background:#fff;border:1px solid #DEE2E6;border-radius:10px;padding:7px 14px 7px 34px;font-size:13px;color:#495057;width:240px;outline:none;"
                                onfocus="this.style.borderColor='#5E53F4'" onblur="this.style.borderColor='#DEE2E6'">
                        </div>
                        @if($search)
                            <a href="{{ route('manajemenmahasiswa.pengguna.index') }}"
                                style="font-size:12px;color:#6C757D;text-decoration:none;font-weight:600;">
                                Reset
                            </a>
                        @endif
                    </form>

                    {{-- Cek Alumni (admin only) --}}
                    @if(auth()->user()->hasAnyRole(['admin_kemahasiswaan','admin','superadmin']))
                        <button onclick="openAlumniModal()"
                            style="background:#fff;border:1px solid #DEE2E6;border-radius:10px;padding:7px 14px;font-size:12px;font-weight:600;color:#1A1C1E;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .2s;"
                            onmouseover="this.style.borderColor='#5E53F4';this.style.color='#5E53F4'"
                            onmouseout="this.style.borderColor='#DEE2E6';this.style.color='#1A1C1E'">
                            <span class="material-symbols-outlined" style="font-size:16px;">school</span>
                            Cek Alumni Otomatis
                        </button>
                    @endif
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div style="background:#E7F9F3;border:1px solid #B2EBD9;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#00C08D;">check_circle</span>
                    <span style="font-size:13px;color:#007A5A;font-weight:500;">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div style="background:#FEF2F2;border:1px solid #FEE2E2;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#EF4444;">error</span>
                    <span style="font-size:13px;color:#B91C1C;font-weight:500;">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Categories --}}
            @php
                $categoryIcons = [
                    'Mahasiswa Aktif'   => 'person',
                    'Pengurus Himpunan' => 'groups',
                    'Alumni'            => 'school',
                ];
                $categoryRoutes = [
                    'Mahasiswa Aktif'   => route('manajemenmahasiswa.pengguna.category', 'Mahasiswa Aktif'),
                    'Pengurus Himpunan' => route('manajemenmahasiswa.pengguna.category', 'Pengurus Himpunan'),
                    'Alumni'            => route('manajemenmahasiswa.pengguna.category', 'Alumni'),
                ];
            @endphp

            <div style="display:flex;flex-direction:column;gap:40px;">
                @foreach($categories as $title => $sectionUsers)
                    <section>
                        {{-- Section Header --}}
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                            <h2 style="font-size:11px;font-weight:700;color:#6C757D;text-transform:uppercase;letter-spacing:.15em;white-space:nowrap;margin:0;">
                                {{ $title }}
                            </h2>
                            <div style="height:1px;background:#DEE2E6;flex-grow:1;"></div>
                            <a href="{{ $categoryRoutes[$title] }}"
                                style="font-size:13px;font-weight:600;color:#5E53F4;text-decoration:none;display:flex;align-items:center;gap:4px;white-space:nowrap;"
                                onmouseover="this.style.color='#4A42C1'" onmouseout="this.style.color='#5E53F4'">
                                Lihat Semua
                                <span class="material-symbols-outlined" style="font-size:14px;">chevron_right</span>
                            </a>
                        </div>

                        {{-- User Cards --}}
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            @forelse($sectionUsers as $user)
                                @include('manajemenmahasiswa::permissions._user_card', [
                                    'user'            => $user,
                                    'assignableRoles' => $assignableRoles,
                                ])
                            @empty
                                <div style="background:#fff;border:2px dashed #DEE2E6;border-radius:12px;padding:32px;text-align:center;">
                                    <p style="color:#ADB5BD;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin:0;">
                                        Tidak ada pengguna
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal Cek Alumni --}}
    <div id="alumniModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;padding:28px;width:460px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                <div style="width:36px;height:36px;background:#E7F9F3;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-symbols-outlined" style="font-size:20px;color:#00C08D;">school</span>
                </div>
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#1A1C1E;margin:0;">Cek Alumni Otomatis</h3>
                    <p style="font-size:12px;color:#6C757D;margin:0;">Mahasiswa angkatan ≤ {{ now()->year - 5 }} akan dijadikan alumni</p>
                </div>
            </div>

            <div id="alumniPreview" style="background:#F8F9FA;border-radius:10px;padding:12px;margin-bottom:16px;min-height:60px;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:13px;color:#6C757D;">Klik "Preview" untuk melihat data...</span>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button onclick="previewAlumni()"
                    style="background:#F8F9FA;border:1px solid #DEE2E6;border-radius:10px;padding:8px 18px;font-size:12px;font-weight:600;color:#495057;cursor:pointer;">
                    Preview
                </button>
                <button onclick="closeAlumniModal()"
                    style="background:#F8F9FA;border:1px solid #DEE2E6;border-radius:10px;padding:8px 18px;font-size:12px;font-weight:600;color:#495057;cursor:pointer;">
                    Batal
                </button>
                <button id="btnExecAlumni" onclick="execAlumni()" disabled
                    style="background:#DEE2E6;border:none;border-radius:10px;padding:8px 18px;font-size:12px;font-weight:700;color:#ADB5BD;cursor:not-allowed;transition:all .2s;">
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
        document.getElementById('alumniPreview').innerHTML = '<span style="font-size:13px;color:#6C757D;">Klik "Preview" untuk melihat data...</span>';
        document.getElementById('btnExecAlumni').disabled = true;
        document.getElementById('btnExecAlumni').style.cssText = 'background:#DEE2E6;border:none;border-radius:10px;padding:8px 18px;font-size:12px;font-weight:700;color:#ADB5BD;cursor:not-allowed;transition:all .2s;';
    }
    function previewAlumni() {
        const preview = document.getElementById('alumniPreview');
        preview.innerHTML = '<span style="font-size:13px;color:#6C757D;">Memuat...</span>';

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
                preview.innerHTML = `<span style="color:#EF4444;font-size:13px;">${data.error}</span>`;
                return;
            }
            const names = data.preview.map(p => `<span style="font-size:11px;color:#495057;">• ${p.name} (${p.nim}, angkatan ${p.cohort_year})</span>`).join('<br>');
            const more  = data.count > 10 ? `<br><span style="font-size:11px;color:#ADB5BD;">...dan ${data.count - 10} lainnya</span>` : '';
            preview.innerHTML = `
                <div style="width:100%;">
                    <p style="font-size:13px;font-weight:700;color:#1A1C1E;margin:0 0 8px;">${data.message}</p>
                    <div style="line-height:1.8;">${names}${more}</div>
                </div>`;

            if (data.count > 0) {
                const btn = document.getElementById('btnExecAlumni');
                btn.disabled = false;
                btn.style.cssText = 'background:#00C08D;border:none;border-radius:10px;padding:8px 18px;font-size:12px;font-weight:700;color:#fff;cursor:pointer;transition:all .2s;';
            }
        })
        .catch(() => {
            preview.innerHTML = '<span style="color:#EF4444;font-size:13px;">Gagal mengambil data.</span>';
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

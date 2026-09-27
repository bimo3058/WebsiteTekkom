{{--
    Detail satu tiket dalam "satu kotak", meniru Detail User SITKOM
    (resources/views/superadmin/users/show.blade.php). Dipakai halaman Detail
    (staff & pelapor) dan halaman Lacak magic link.

    Param:
      $pengaduan      Pengaduan
      $title          judul bilah atas ("Detail Pengaduan" / "Status Pengaduan")
      $isStaff        bool — tampilkan Pelapor, Riwayat Tiket, tombol Tandai Tercatat
      $canDelete      bool — tombol Hapus (bawaan false)
      $backUrl        tujuan tombol kembali; null = tanpa tombol
      $kategoriLabel  label kategori (opsional; bawaan dari key kategori)
      $buktiToken     token magic link untuk tautan bukti (opsional)
--}}
@php
    $isStaff   = (bool) ($isStaff ?? false);
    $canDelete = (bool) ($canDelete ?? false);
    $backUrl   = $backUrl ?? null;

    $tpl = fn (string $key) => data_get($pengaduan, 'data_template.' . $key);
    $judul = $tpl('judul') ?: '-';
    $badge = $pengaduan->statusBadge();

    $kategoriLabel = $kategoriLabel ?? ucwords(str_replace('_', ' ',
        \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori((string) $pengaduan->kategori)));

    $waktu = $tpl('waktu_kejadian') ?? $tpl('tanggal_kejadian');
    if ($waktu) {
        try {
            $waktu = \Carbon\Carbon::parse($waktu)->translatedFormat('d F Y, H:i');
        } catch (\Throwable $e) {
            // tampilkan apa adanya
        }
    }

    $infoItems = [];
    if ($isStaff) {
        $infoItems[] = ['label' => 'Pelapor', 'value' => $pengaduan->is_anonim
            ? 'Konfidensial'
            : (optional($pengaduan->pelapor)->name ?? null)];
    }
    // Angkatan tiket konfidensial sudah dibuang controller; barisnya tidak ditampilkan.
    if (!$pengaduan->is_anonim) {
        $infoItems[] = ['label' => 'Angkatan', 'value' => $tpl('angkatan')];
    }
    $infoItems = array_merge($infoItems, [
        ['label' => 'Lokasi Kejadian', 'value' => $tpl('lokasi')],
        ['label' => 'Waktu Kejadian',  'value' => $waktu],
        ['label' => 'Mata Kuliah',     'value' => $tpl('mata_kuliah')],
        ['label' => 'Dosen Terkait',   'value' => $tpl('nama_dosen')],
        ['label' => 'Tendik Terkait',  'value' => $tpl('nama_tendik')],
        ['label' => 'Frekuensi',       'value' => $tpl('frekuensi')],
    ]);

    // Keterangan untuk pelapor, menggantikan banner status lama.
    $catatanPelapor = [
        'baru'     => 'Belum dibuka admin.',
        'dibaca'   => 'Sudah dibaca admin.',
        'tercatat' => 'Sudah dicatat admin.',
    ][$badge['tone']];

    $adaFlash = session('success') || session('info') || session('error') || $errors->any();

    $logs = $isStaff && $pengaduan->relationLoaded('logs') ? $pengaduan->logs : collect();
    $actionLabels = [
        'dibuat'            => 'Tiket Dibuat',
        'dibaca'            => 'Dibaca Admin',
        'diproses'          => 'Diterima Admin',
        'dijawab'           => 'Tercatat',
        'ditutup_admin'     => 'Ditutup Admin',
        'ditutup_mahasiswa' => 'Ditutup',
        'ditutup_otomatis'  => 'Ditutup Otomatis',
        'selesai'           => 'Tercatat',
        'tercatat'          => 'Ditandai Tercatat',
        'batal_tercatat'    => 'Tanda Tercatat Dicabut',
        // Label lama — tetap ditampilkan dengan istilah netral
        'didelegasikan'     => 'Diteruskan',
        'ditanggapi_dosen'  => 'Ditanggapi',
        'ditolak_dosen'     => 'Dikembalikan',
        'diajukan_ulang'    => 'Diajukan Ulang',
    ];
@endphp

@include('manajemenmahasiswa::pengaduan.partials.box-styles')
@if ($canDelete)
    @include('manajemenmahasiswa::pengaduan.partials.hapus-script')
@endif

<div class="kf-box">
    {{-- ── Bilah atas ── --}}
    <div class="kf-toolbar">
        <div class="kf-toolbar-lead">
            @if ($backUrl)
                <a href="{{ $backUrl }}" class="kf-back" title="Kembali" aria-label="Kembali ke daftar pengaduan">
                    <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
                </a>
            @endif
            <h1 class="kf-toolbar-title">{{ $title }}</h1>
            <span class="kf-toolbar-id">#{{ $pengaduan->id }}</span>
        </div>

        @if ($isStaff || $canDelete)
            <div class="kf-toolbar-actions">
                @if ($isStaff)
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.toggle.tercatat', $pengaduan->id) }}">
                        @csrf
                        @if ($pengaduan->isTercatat())
                            <button type="submit" class="mk-btn mk-btn--secondary mk-btn--sm">Batalkan Tercatat</button>
                        @else
                            <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">
                                <x-manajemenmahasiswa::ui.icon name="check" size="15" /> Tandai Tercatat
                            </button>
                        @endif
                    </form>
                @endif
                @if ($canDelete)
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.destroy', $pengaduan->id) }}"
                          data-judul="{{ $judul }}" onsubmit="return pgdKonfirmasiHapus(this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mk-btn mk-btn--secondary mk-btn--sm">
                            <x-manajemenmahasiswa::ui.icon name="trash" size="15" /> Hapus
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    @if ($adaFlash)
        <div class="kf-alerts">
            @include('manajemenmahasiswa::pengaduan.partials.alerts')
        </div>
    @endif

    {{-- ── Profil tiket: judul, badge, grid informasi ── --}}
    <div class="kf-profile">
        <div class="kf-icon {{ $pengaduan->is_anonim ? 'is-konfidensial' : '' }}">
            <x-manajemenmahasiswa::ui.icon :name="$pengaduan->is_anonim ? 'shield-02' : 'file-01'" size="26" />
        </div>

        <div style="flex: 1; min-width: 0;">
            <div class="kf-heading">
                <h2 class="kf-subject">{{ $judul }}</h2>
                <span class="pgd-pill kategori">{{ $kategoriLabel }}</span>
                @include('manajemenmahasiswa::pengaduan.partials.status-badge', ['pengaduan' => $pengaduan])
                @if ($pengaduan->is_anonim)
                    <span class="pgd-pill konfidensial">
                        <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Konfidensial
                    </span>
                @endif
            </div>
            <p class="kf-sub">
                Diajukan {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }} WIB
                @unless ($isStaff)
                    <span class="kf-sub-note">{{ $catatanPelapor }}</span>
                @endunless
            </p>

            <div class="kf-grid">
                @foreach ($infoItems as $info)
                    <div class="kf-row">
                        <span class="kf-label">{{ $info['label'] }}</span>
                        <span class="kf-value {{ empty($info['value']) ? 'is-empty' : '' }}">{{ $info['value'] ?: '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Hal Aduan sudah tidak ditanyakan; tampil hanya untuk tiket lama yang masih menyimpannya. --}}
    @if ($tpl('hal_aduan'))
        <div class="kf-split">
            <div class="kf-side">
                <h3>Hal Aduan</h3>
                <p>Ringkasan aduan dari formulir versi lama.</p>
            </div>
            <div class="kf-main">{{ $tpl('hal_aduan') }}</div>
        </div>
    @endif

    <div class="kf-split">
        <div class="kf-side">
            <h3>Pesan</h3>
            <p>Isi pengaduan sebagaimana dikirim pelapor.</p>
        </div>
        <div class="kf-main">{{ $tpl('kronologi') ?: '-' }}</div>
    </div>

    <div class="kf-split">
        <div class="kf-side">
            <h3>Bukti Dukung</h3>
            <p>Berkas yang dilampirkan pelapor. Klik untuk melihat isinya.</p>
        </div>
        <div class="kf-main is-fields">
            @php $buktiParams = ['pengaduan' => $pengaduan] + (!empty($buktiToken) ? ['token' => $buktiToken] : []); @endphp
            @include('manajemenmahasiswa::pengaduan.partials.bukti-list', $buktiParams)
        </div>
    </div>

    @if ($logs->count())
        <div class="kf-split">
            <div class="kf-side">
                <h3>Riwayat Tiket</h3>
                <p>{{ $logs->count() }} aktivitas tercatat, terbaru di atas.</p>
            </div>
            <div class="kf-main is-fields">
                <div class="kf-timeline">
                    @foreach ($logs as $log)
                        <div class="kf-tl-item">
                            <span class="kf-tl-dot"></span>
                            <div class="kf-tl-date">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</div>
                            <div class="kf-tl-title">{{ $actionLabels[$log->action] ?? ucwords(str_replace('_', ' ', $log->action)) }}</div>
                            @if ($log->actor)
                                <div class="kf-tl-actor">Oleh: {{ $log->actor->name }}</div>
                            @endif
                            @if ($log->notes)
                                <div class="kf-tl-note">"{{ \Illuminate\Support\Str::limit($log->notes, 100) }}"</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{--
    Detail satu tiket dalam "satu kotak", meniru Detail User SITKOM
    (resources/views/superadmin/users/show.blade.php). Dipakai halaman Detail
    (staff & pelapor) dan halaman Lacak magic link.

    Param:
      $pengaduan      Pengaduan
      $title          judul bilah atas
      $isStaff        bool — tampilkan Pelapor, badge status, dan tombol Tandai Tercatat
                      (pelapor tidak pernah melihat status tiket)
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

    $kategoriLabel = $kategoriLabel ?? \Modules\ManajemenMahasiswa\Models\Pengaduan::kategoriLabel((string) $pengaduan->kategori);

    $adaBukti = !empty(data_get($pengaduan, 'data_template.bukti')) || $tpl('link_bukti');

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
    // Pertanyaan Detail Kejadian sesuai kategori tiket; jawaban tiket lama di luar
    // set kategorinya tetap ditampilkan bila berisi.
    $infoItems = array_merge($infoItems, \Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan::barisInfo(
        (string) $pengaduan->kategori,
        (array) ($pengaduan->data_template ?? [])
    ));

    $adaFlash = session('success') || session('info') || session('error') || $errors->any();
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
                {{-- Status hanya untuk staff; pelapor (reguler & konfidensial) tidak melihatnya. --}}
                @if ($isStaff)
                    @include('manajemenmahasiswa::pengaduan.partials.status-badge', ['pengaduan' => $pengaduan])
                @endif
                @if ($pengaduan->is_anonim)
                    <span class="pgd-pill konfidensial">
                        <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Konfidensial
                    </span>
                @endif
            </div>
            <p class="kf-sub">
                Diajukan {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }} WIB
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
            <p>{{ $adaBukti ? 'Berkas yang dilampirkan pelapor. Klik untuk melihat isinya.' : 'Pelapor tidak melampirkan berkas.' }}</p>
        </div>
        <div class="kf-main is-fields">
            @php $buktiParams = ['pengaduan' => $pengaduan] + (!empty($buktiToken) ? ['token' => $buktiToken] : []); @endphp
            @include('manajemenmahasiswa::pengaduan.partials.bukti-list', $buktiParams)
        </div>
    </div>
</div>

{{--
    Halaman konfirmasi pengaduan — satu tampilan untuk pengaduan biasa & anonim.
    Strukturnya mengikuti halaman Detail User (superadmin/users/show): satu card berisi
    toolbar, bagian profil dengan grid label–nilai, lalu bagian dua kolom.

    Param:
      $payload     ['kategori','template','bukti_items']
      $isAnonim    bool — pelapor mencentang "Sembunyikan identitas saya"
      $backUrl     tujuan tombol Kembali (satu-satunya, ikon di kiri bilah atas)
      $formAction  route pengiriman akhir
      $reporterName nama pelapor
--}}
@php
    use Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan;

    $tpl = fn (string $key) => data_get($payload, 'template.' . $key);

    $kategoriLabel = \Modules\ManajemenMahasiswa\Models\Pengaduan::kategoriLabel((string) $payload['kategori']);
    $buktiItems = (array) ($payload['bukti_items'] ?? []);
    $buktiCount = count($buktiItems);

    // Anonim: baris Pelapor, Angkatan & Email Kontak tidak ditampilkan (badge "Anonim"
    // sudah cukup; anonim tidak punya kontak, R-43).
    $identitas = $isAnonim ? [] : [
        ['label' => 'Pelapor',      'value' => $reporterName ?? '-'],
        ['label' => 'Angkatan',     'value' => $tpl('angkatan')],
        ['label' => 'Email Kontak', 'value' => $tpl('email_kontak')],
    ];

    $infoItems = array_merge($identitas, PengaduanPertanyaan::barisInfo(
        (string) $payload['kategori'],
        (array) ($payload['template'] ?? [])
    ));
@endphp

@include('manajemenmahasiswa::pengaduan.partials.box-styles')

<div class="kf-box">
    {{-- ── Toolbar: tombol kembali ikon + judul, seperti Detail User SITKOM ── --}}
    <div class="kf-toolbar">
        <div class="kf-toolbar-lead">
            <a href="{{ $backUrl }}" class="kf-back" title="Kembali" aria-label="Kembali ubah pengaduan">
                <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
            </a>
            <h1 class="kf-toolbar-title">Konfirmasi Pengaduan</h1>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}" style="position: relative; margin: 0;">
        @csrf
        @include('manajemenmahasiswa::pengaduan.partials.honeypot')

        <input type="hidden" name="is_anonim" value="{{ $isAnonim ? 1 : 0 }}">
        <input type="hidden" name="kategori" value="{{ $payload['kategori'] }}">
        @foreach(($payload['template'] ?? []) as $key => $value)
            @continue(is_array($value))
            <input type="hidden" name="template[{{ $key }}]" value="{{ (string) $value }}">
        @endforeach

        <div class="kf-notice">
            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="16" />
            <span>
                Periksa kembali; setelah dikirim tidak bisa diedit.
                @if($isAnonim)
                    Simpan <strong>tautan rahasia</strong> yang akan diberikan untuk melihatnya kembali.
                @endif
            </span>
        </div>

        {{-- ── Profil: subjek + badge + grid informasi ── --}}
        <div class="kf-profile">
            <div class="kf-icon {{ $isAnonim ? 'is-konfidensial' : '' }}">
                <x-manajemenmahasiswa::ui.icon :name="$isAnonim ? 'shield-02' : 'file-01'" size="26" />
            </div>

            <div style="flex: 1; min-width: 0;">
                <div class="kf-heading">
                    <h2 class="kf-subject">{{ $tpl('judul') ?: '-' }}</h2>
                    <span class="pgd-pill kategori">{{ $kategoriLabel }}</span>
                    @if($isAnonim)
                        <span class="pgd-pill konfidensial">
                            <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Anonim
                        </span>
                    @endif
                </div>
                <p class="kf-sub">Akan dikirim {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>

                <div class="kf-grid">
                    @foreach($infoItems as $info)
                        <div class="kf-row">
                            <span class="kf-label">{{ $info['label'] }}</span>
                            <span class="kf-value {{ empty($info['value']) ? 'is-empty' : '' }}">{{ $info['value'] ?: '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Pesan ── --}}
        <div class="kf-split">
            <div class="kf-side">
                <h3>Pesan</h3>
                <p>Isi yang diterima pengelola.</p>
            </div>
            <div class="kf-main">{{ $tpl('kronologi') ?: '-' }}</div>
        </div>

        {{-- ── Bukti dukung ── --}}
        <div class="kf-split">
            <div class="kf-side">
                <h3>Bukti Dukung</h3>
                <p>Metadata penulis PDF dikosongkan otomatis.</p>
            </div>
            <div class="kf-main is-fields">
                @if($buktiCount > 0)
                    <div style="font-size: 13px; font-weight: 600; color: var(--c-fg-sec);">{{ $buktiCount }} berkas — klik untuk melihat</div>
                    @include('manajemenmahasiswa::pengaduan.partials.bukti-items', ['items' => $buktiItems])
                @else
                    <span class="kf-empty">Tidak ada bukti dilampirkan</span>
                @endif
            </div>
        </div>

        <div class="kf-footer">
            <button type="submit" class="mk-btn mk-btn--primary">
                <x-manajemenmahasiswa::ui.icon name="check" size="16" /> Kirim Pengaduan
            </button>
        </div>
    </form>
</div>

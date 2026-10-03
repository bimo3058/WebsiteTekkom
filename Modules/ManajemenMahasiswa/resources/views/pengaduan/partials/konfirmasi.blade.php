{{--
    Halaman konfirmasi pengaduan — SATU tampilan untuk jalur Reguler & Konfidensial.
    Strukturnya mengikuti halaman Detail User (superadmin/users/show): satu card berisi
    toolbar, bagian profil dengan grid label–nilai, lalu bagian dua kolom.

    Param:
      $payload     ['kategori','template','bukti_items']
      $isAnonim    bool — jalur konfidensial
      $backUrl     tujuan tombol Kembali (satu-satunya, ikon di kiri bilah atas)
      $formAction  route pengiriman akhir
      $reporterName nama pelapor (hanya jalur reguler)
--}}
@php
    $tpl = fn (string $key) => data_get($payload, 'template.' . $key);

    $kategoriLabel = ucwords(str_replace('_', ' ', $payload['kategori']));
    $buktiItems = (array) ($payload['bukti_items'] ?? []);
    $buktiCount = count($buktiItems);

    $waktu = $tpl('waktu_kejadian');
    if ($waktu) {
        try {
            $waktu = \Carbon\Carbon::parse($waktu)->translatedFormat('d F Y, H:i');
        } catch (\Throwable $e) {
            // tampilkan apa adanya
        }
    }

    $infoItems = [
        ['label' => 'Pelapor',         'value' => $isAnonim ? 'Dirahasiakan' : ($reporterName ?? '-')],
        ['label' => 'Angkatan',        'value' => $tpl('angkatan')],
        ['label' => 'Lokasi Kejadian', 'value' => $tpl('lokasi')],
        ['label' => 'Waktu Kejadian',  'value' => $waktu],
        ['label' => 'Mata Kuliah',     'value' => $tpl('mata_kuliah')],
        ['label' => 'Dosen Terkait',   'value' => $tpl('nama_dosen')],
        ['label' => 'Tendik Terkait',  'value' => $tpl('nama_tendik')],
        ['label' => 'Frekuensi',       'value' => $tpl('frekuensi')],
    ];
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

        @unless($isAnonim)
            <input type="hidden" name="is_anonim" value="0">
        @endunless
        <input type="hidden" name="kategori" value="{{ $payload['kategori'] }}">
        @foreach(($payload['template'] ?? []) as $key => $value)
            @continue(is_array($value))
            <input type="hidden" name="template[{{ $key }}]" value="{{ (string) $value }}">
        @endforeach

        <div class="kf-notice">
            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="16" />
            Periksa kembali data di bawah. Setelah dikirim, pengaduan tidak bisa diedit lagi.
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
                            <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Konfidensial
                        </span>
                    @else
                        <span class="pgd-status dibaca">Reguler</span>
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
                <p>Isi pengaduan yang akan diterima pengelola. Pastikan sudah jelas dan lengkap.</p>
            </div>
            <div class="kf-main">{{ $tpl('kronologi') ?: '-' }}</div>
        </div>

        {{-- ── Bukti dukung ── --}}
        <div class="kf-split">
            <div class="kf-side">
                <h3>Bukti Dukung</h3>
                <p>Berkas PDF yang dilampirkan. Metadata penulis dokumen sudah dikosongkan sebisanya.</p>
            </div>
            <div class="kf-main is-fields">
                @if($buktiCount > 0)
                    <div style="font-size: 13px; font-weight: 600; color: var(--c-fg-sec);">{{ $buktiCount }} berkas — klik untuk melihat isinya</div>
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

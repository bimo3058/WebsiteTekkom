{{--
    Halaman konfirmasi pengaduan — SATU tampilan untuk jalur Reguler & Konfidensial.
    Strukturnya mengikuti halaman Detail User (superadmin/users/show): satu card berisi
    toolbar, bagian profil dengan grid label–nilai, lalu bagian dua kolom.

    Param:
      $payload     ['kategori','template','bukti_items']
      $isAnonim    bool — jalur konfidensial
      $backUrl     tujuan tombol Kembali (satu-satunya, di kanan atas)
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

<style>
    .kf-box { background: #fff; border: 1px solid #D4D5D8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; font-family: 'Inter Tight', sans-serif; }
    .kf-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px; background: #fff; border-bottom: 1px solid #D4D5D8; }
    .kf-toolbar-title { font-size: 14px; font-weight: 800; color: #1E293B; margin: 0; text-transform: uppercase; letter-spacing: .02em; }
    .kf-back { display: inline-flex; align-items: center; gap: 8px; height: 32px; padding: 0 12px; border-radius: 8px; background: #fff; border: 1px solid #D0D1D5; color: #475569; box-shadow: 0 1px 2px rgba(0,0,0,.05); text-decoration: none; font-size: 13px; font-weight: 600; transition: all .2s; }
    .kf-back:hover { background: #F6F8FA; color: #0D0D12; }

    .kf-notice { display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #FEF3C7; color: #92400E; font-size: 13px; font-weight: 600; border-bottom: 1px solid #FDE68A; }

    .kf-profile { display: flex; gap: 20px; align-items: flex-start; padding: 20px; border-bottom: 1px solid #d1d1d1; }
    .kf-icon { width: 56px; height: 56px; border-radius: 14px; background: #EEF2FF; color: #293C79; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .kf-icon.is-konfidensial { background: #E2E8F0; color: #334155; }
    .kf-heading { display: flex; align-items: center; flex-wrap: wrap; gap: 8px 12px; margin-bottom: 4px; }
    .kf-subject { font-size: 20px; font-weight: 800; color: #1E293B; margin: 0; letter-spacing: -.02em; overflow-wrap: anywhere; }
    .kf-badge { display: inline-flex; align-items: center; gap: 5px; padding: 2px 10px; border-radius: 99px; font-size: 10px; font-weight: 700; text-transform: capitalize; background: transparent; white-space: nowrap; }
    .kf-dot { width: 5px; height: 5px; border-radius: 50%; }
    .kf-sub { font-size: 14px; font-weight: 500; color: #64748B; margin: 0 0 16px; }

    .kf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 40px; max-width: 900px; }
    .kf-row { display: flex; align-items: center; min-width: 0; }
    .kf-label { width: 140px; font-size: 13px; color: #94A3B8; flex-shrink: 0; font-weight: 500; }
    .kf-value { font-size: 13px; font-weight: 600; color: #334155; min-width: 0; overflow-wrap: anywhere; }
    .kf-value.is-empty { color: #CBD5E1; }

    .kf-split { display: flex; border-bottom: 1px solid #d1d1d1; }
    .kf-side { width: 240px; padding: 20px; border-right: 1px solid #d1d1d1; background: #fff; flex-shrink: 0; }
    .kf-side h3 { font-size: 14px; font-weight: 800; color: #1E293B; margin: 0 0 8px; }
    .kf-side p { font-size: 12px; font-weight: 500; color: #64748B; line-height: 1.6; margin: 0; }
    .kf-main { flex: 1; padding: 20px; min-width: 0; font-size: 14px; font-weight: 500; color: #1E293B; line-height: 1.7; white-space: pre-wrap; overflow-wrap: anywhere; }

    .kf-footer { display: flex; justify-content: flex-end; padding: 14px 20px; background: #FAFAFA; }
    .kf-submit { display: inline-flex; align-items: center; gap: 8px; background: #293C79; color: #fff; border: none; border-radius: 10px; padding: 10px 24px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .2s; }
    .kf-submit:hover { background: #415086; box-shadow: 0 4px 12px rgba(41,60,121,.3); }

    @media (max-width: 768px) {
        .kf-profile { flex-direction: column; }
        .kf-grid { grid-template-columns: 1fr; }
        .kf-split { flex-direction: column; }
        .kf-side { width: auto; border-right: none; border-bottom: 1px solid #E2E8F0; }
    }
</style>

<div class="kf-box">
    {{-- ── Toolbar: judul di kiri, satu-satunya tombol Kembali di kanan atas ── --}}
    <div class="kf-toolbar">
        <h1 class="kf-toolbar-title">Konfirmasi Pengaduan</h1>
        <a href="{{ $backUrl }}" class="kf-back" title="Kembali" aria-label="Kembali ubah pengaduan">
            <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
            Kembali
        </a>
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
                    <span class="kf-badge" style="border: 1px solid #4F46E5; color: #4F46E5;">
                        <span class="kf-dot" style="background: #4F46E5;"></span> {{ $kategoriLabel }}
                    </span>
                    @if($isAnonim)
                        <span class="kf-badge" style="border: 1px solid #111827; color: #111827;">
                            <span class="kf-dot" style="background: #111827;"></span> Konfidensial
                        </span>
                    @else
                        <span class="kf-badge" style="border: 1px solid #3B82F6; color: #3B82F6;">
                            <span class="kf-dot" style="background: #3B82F6;"></span> Reguler
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
            <div class="kf-main" style="white-space: normal;">
                @if($buktiCount > 0)
                    <div style="font-size: 13px; font-weight: 600; color: #334155;">{{ $buktiCount }} berkas — klik untuk melihat isinya</div>
                    @include('manajemenmahasiswa::pengaduan.partials.bukti-items', ['items' => $buktiItems])
                @else
                    <span style="color: #CBD5E1;">Tidak ada bukti dilampirkan</span>
                @endif
            </div>
        </div>

        <div class="kf-footer">
            <button type="submit" class="kf-submit">
                <x-manajemenmahasiswa::ui.icon name="check" size="16" /> Kirim Pengaduan
            </button>
        </div>
    </form>
</div>

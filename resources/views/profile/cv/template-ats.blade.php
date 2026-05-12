<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $user['name'] }}</title>
    <!-- Font standar sistem yang aman untuk ATS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* Menggunakan font standar yang sangat aman untuk ATS */
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            background: #ffffff;
            line-height: 1.5;
            font-size: 11pt;
        }

        .cv-container {
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            padding: 40px 50px;
            background: #ffffff;
            min-height: 100vh;
        }

        /* -------------------------------------
           HEADER (Data Diri)
           ------------------------------------- */
        header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid #000000;
        }

        h1.name {
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .contact-info {
            font-size: 10.5pt;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            color: #333333;
        }

        .contact-info span::after {
            content: " | ";
            margin-left: 8px;
            color: #000000;
        }

        .contact-info span:last-child::after {
            content: "";
        }

        /* -------------------------------------
           SECTIONS
           ------------------------------------- */
        section {
            margin-bottom: 20px;
        }

        h2.section-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000000;
            margin-bottom: 12px;
            padding-bottom: 4px;
            letter-spacing: 0.5px;
        }

        /* -------------------------------------
           ITEMS (Pendidikan, Pengalaman)
           ------------------------------------- */
        .item {
            margin-bottom: 14px;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 2px;
        }

        h3.item-title {
            font-size: 11pt;
            font-weight: bold;
        }

        .item-date {
            font-size: 10.5pt;
            white-space: nowrap;
            font-weight: bold;
        }

        .item-subtitle {
            font-size: 11pt;
            font-style: italic;
            margin-bottom: 4px;
        }

        .item-desc {
            font-size: 10.5pt;
            text-align: justify;
        }

        /* -------------------------------------
           SKILLS
           ------------------------------------- */
        .skills-list {
            font-size: 10.5pt;
        }
        
        ul {
            padding-left: 20px;
            margin-top: 4px;
        }
        
        li {
            margin-bottom: 4px;
            text-align: justify;
        }

        /* -------------------------------------
           PRINT & SCREEN CONTROLS
           ------------------------------------- */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        @media screen {
            body {
                background: #f3f4f6;
            }
            .cv-container {
                margin: 40px auto;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            }
        }

        @media print {
            body { background: white; }
            .print-controls { display: none !important; }
            .cv-container { box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            @page { margin: 1.5cm; size: A4; }
        }
    </style>
</head>
<body onload="{{ isset($is_print) && $is_print ? 'window.print()' : '' }}">

<!-- Print Controls -->
<div class="print-controls">
    <button onclick="window.print()" class="btn-print">Cetak / Download PDF</button>
</div>

<div class="cv-container">
    <!-- ATS Header: No photo, pure semantic tags -->
    <header>
        <h1 class="name">{{ $user['name'] }}</h1>
        <div class="contact-info">
            @if($user['whatsapp'])<span>{{ $user['whatsapp'] }}</span>@endif
            @if(!empty($user['personal_email']))
                <span>{{ $user['personal_email'] }}</span>
            @elseif(!empty($user['email']))
                <span>{{ $user['email'] }}</span>
            @endif
            <span>{{ $cv->cv_domisili ?? 'Semarang, Indonesia' }}</span>
            @if(!empty($cv->cv_portfolio))
                <span>{{ $cv->cv_portfolio }}</span>
            @endif
        </div>
    </header>

    <!-- Professional Summary -->
    @if(!empty($cv->tentang_diri))
    <section>
        <h2 class="section-title">Ringkasan Profesional</h2>
        <div class="item-desc" style="white-space: pre-line;">{{ $cv->tentang_diri }}</div>
    </section>
    @endif

    <!-- Education -->
    @if(count($pendidikan) > 0)
    <section>
        <h2 class="section-title">Pendidikan</h2>
        @foreach($pendidikan as $edu)
        <div class="item">
            <div class="item-header">
                <h3 class="item-title">{{ $edu['institusi'] }}</h3>
                <span class="item-date">{{ $edu['tahun_masuk'] }} - {{ $edu['tahun_lulus'] ?? 'Sekarang' }}</span>
            </div>
            <div class="item-subtitle">{{ $edu['jurusan'] }}</div>
        </div>
        @endforeach
    </section>
    @endif

    <!-- Work Experience -->
    @if(count($pengalaman) > 0)
    <section>
        <h2 class="section-title">Pengalaman Kerja</h2>
        @foreach($pengalaman as $exp)
        <div class="item">
            <div class="item-header">
                <h3 class="item-title">{{ $exp['posisi'] }}</h3>
                <span class="item-date">{{ $exp['tahun_mulai'] }} - {{ $exp['tahun_selesai'] ?? 'Sekarang' }}</span>
            </div>
            <div class="item-subtitle">{{ $exp['perusahaan'] }}</div>
            @if(!empty($exp['deskripsi']))
            <div class="item-desc">
                <ul>
                    <!-- Jika deskripsi berisi bullet point text, akan dirender dengan baik. Idealnya dipecah menjadi list, tapi string multiline juga cukup -->
                    <li>{{ $exp['deskripsi'] }}</li>
                </ul>
            </div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    <!-- Organization & Activities -->
    @if(count($kegiatan) > 0 || count($kegiatan_manual ?? []) > 0)
    <section>
        <h2 class="section-title">Organisasi & Kepanitiaan</h2>
        @foreach($kegiatan as $keg)
        <div class="item">
            <div class="item-header">
                <h3 class="item-title">{{ $keg['peran'] }}</h3>
                <span class="item-date">{{ $keg['tanggal'] ? \Carbon\Carbon::parse($keg['tanggal'])->format('M Y') : '' }}</span>
            </div>
            <div class="item-subtitle">{{ $keg['nama'] }}</div>
        </div>
        @endforeach
        
        @foreach($kegiatan_manual ?? [] as $keg)
        <div class="item">
            <div class="item-header">
                <h3 class="item-title">{{ $keg['peran'] }}</h3>
                <span class="item-date">{{ $keg['tahun_mulai'] }} - {{ $keg['tahun_selesai'] ?? 'Sekarang' }}</span>
            </div>
            <div class="item-subtitle">{{ $keg['organisasi'] }}</div>
            @if(!empty($keg['deskripsi']))
            <div class="item-desc">
                <ul>
                    <li>{{ $keg['deskripsi'] }}</li>
                </ul>
            </div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    <!-- Projects -->
    @if(count($proyek ?? []) > 0)
    <section>
        <h2 class="section-title">Proyek & Portofolio</h2>
        @foreach($proyek ?? [] as $proj)
        <div class="item">
            <div class="item-header">
                <h3 class="item-title">{{ $proj['nama'] }}</h3>
                <span class="item-date">{{ $proj['tahun'] }}</span>
            </div>
            <div class="item-subtitle">{{ $proj['peran'] }} @if(!empty($proj['tautan'])) | {{ $proj['tautan'] }} @endif</div>
            @if(!empty($proj['deskripsi']))
            <div class="item-desc">
                <ul>
                    <li>{{ $proj['deskripsi'] }}</li>
                </ul>
            </div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    <!-- Certifications -->
    @if(count($sertifikasi ?? []) > 0)
    <section>
        <h2 class="section-title">Sertifikasi & Pelatihan</h2>
        @foreach($sertifikasi ?? [] as $cert)
        <div class="item">
            <div class="item-header">
                <h3 class="item-title">{{ $cert['nama'] }}</h3>
                <span class="item-date">{{ $cert['tahun'] }}</span>
            </div>
            <div class="item-subtitle">{{ $cert['penerbit'] }}</div>
        </div>
        @endforeach
    </section>
    @endif

    <!-- Achievements -->
    @if(count($prestasi) > 0)
    <section>
        <h2 class="section-title">Penghargaan & Prestasi</h2>
        @foreach($prestasi as $p)
        <div class="item">
            <div class="item-header">
                <h3 class="item-title">{{ $p['nama'] }}</h3>
                <span class="item-date">{{ $p['tahun'] }}</span>
            </div>
            <div class="item-subtitle">Tingkat: {{ ucfirst($p['tingkat']) }}</div>
        </div>
        @endforeach
    </section>
    @endif

    <!-- Skills -->
    @if(count($keahlian) > 0)
    <section>
        <h2 class="section-title">Keahlian (Skills)</h2>
        <div class="skills-list">
            <ul>
                @foreach($keahlian as $skill)
                <li><strong>{{ $skill['nama'] }}</strong> — {{ $skill['level'] }}</li>
                @endforeach
            </ul>
        </div>
    </section>
    @endif

    <!-- Languages -->
    @if(count($bahasa ?? []) > 0)
    <section>
        <h2 class="section-title">Kemampuan Bahasa</h2>
        <div class="skills-list">
            <ul>
                @foreach($bahasa ?? [] as $lang)
                <li><strong>{{ $lang['nama'] }}</strong> — {{ $lang['level'] }} @if(!empty($lang['skor'])) ({{ $lang['skor'] }}) @endif</li>
                @endforeach
            </ul>
        </div>
    </section>
    @endif

</div>
</body>
</html>

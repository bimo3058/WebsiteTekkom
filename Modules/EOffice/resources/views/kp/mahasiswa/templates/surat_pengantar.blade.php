<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar KP - {{ $data['instansi'] ?? 'Instansi' }}</title>
    <style>
        /* 1. Pengaturan Halaman (Page Setup) */
        @page { 
            size: A4; 
            margin: 0; /* Dinonaktifkan agar margin diatur penuh lewat .page-a4 */
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            background: #f3f4f6;
        }

        .page-a4 { 
            width: 210mm; 
            min-height: 297mm; 
            /* Top: 3cm, Bottom/Left/Right: 2.54cm */
            padding: 3cm 2.54cm 2.54cm 2.54cm; 
            margin: 0 auto; 
            background: white; 
            box-sizing: border-box; 
            position: relative;
        }

        /* 2. Struktur Konten Dokumen */
        /* A. Kop Surat (Header) */
        .kop-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 2px;
        }
        .kop-table td { 
            padding: 0; 
            vertical-align: middle; 
        }
        .logo-col { 
            width: 15%; 
            text-align: left; 
        }
        .logo-img { 
            height: 90px; 
            width: auto; 
        }
        .title-col { 
            width: 85%; 
            text-align: center; 
            padding-right: 12%; /* Memberi offset agar teks simetris di tengah halaman */
        }
        
        /* Aturan Tipografi Kop Surat */
        .title-col h1 { 
            font-size: 14pt; 
            font-weight: bold; 
            margin: 0; 
            text-transform: uppercase; 
            line-height: 1.2; 
        }
        .title-col h2 { 
            font-size: 12pt; 
            font-weight: bold; 
            margin: 0; 
            text-transform: uppercase; 
            line-height: 1.2; 
        }
        .title-col .address { 
            font-size: 9.5pt; 
            font-weight: normal; 
            margin-top: 4px; 
            line-height: 1.3; 
        }

        /* Garis Kop Surat Horizontal Tebal */
        .kop-border { 
            border-top: 3px solid #000; 
            border-bottom: 1px solid #000; 
            height: 2px; 
            margin-top: 8px; 
            margin-bottom: 25px; 
        }

        /* B. Bagian Atas Surat (Metadata) */
        .meta-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
            font-size: 12pt; 
            line-height: 1.5;
        }
        .meta-table td { 
            padding: 2px 0; 
            vertical-align: top; 
        }

        /* C. Alamat Tujuan Surat */
        .recipient { 
            margin-top: 25px; 
            margin-bottom: 25px; 
            font-size: 12pt; 
            line-height: 1.5;
        }
        .recipient p { 
            margin: 0; 
        }

        /* D. Paragraf Pembuka & Data Mahasiswa */
        .body-text { 
            font-size: 12pt; 
            line-height: 1.5; 
            text-indent: 40px; 
            text-align: justify; 
            margin-bottom: 15px;
        }
        
        .student-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-left: 40px; 
            margin-top: 10px; 
            margin-bottom: 15px;
            font-size: 12pt; 
            line-height: 1.5;
        }
        .student-table td { 
            padding: 3px 0; 
            vertical-align: top; 
        }

        /* E. Paragraf Penutup & Spacing */
        .closing-text {
            font-size: 12pt; 
            line-height: 1.5; 
            text-indent: 40px; 
            text-align: justify; 
            margin-top: 20px; 
            margin-bottom: 30px;
        }

        /* F. Bagian Tanda Tangan */
        .signature-container { 
            margin-top: 40px; 
            width: 100%; 
            font-size: 12pt;
        }
        .signature-box { 
            float: right; 
            width: 320px; 
            text-align: left; 
        }
        .signature-box p { 
            margin: 0; 
            line-height: 1.5; 
        }
        .signature-space { 
            height: 80px; 
        }

        /* Cetak / Print Utility */
        @media print {
            body { 
                background: none; 
                padding: 0; 
            }
            .page-a4 { 
                margin: 0; 
                border: none; 
                box-shadow: none; 
                width: 100%; 
            }
            .no-print { 
                display: none !important; 
            }
        }
        
        .btn-print { 
            display: block; 
            margin: 20px auto; 
            padding: 10px 20px; 
            font-size: 16px; 
            background: #4f46e5; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            text-align: center; 
            max-width: 200px; 
            text-decoration: none; 
            font-family: sans-serif; 
            font-weight: bold; 
        }
    </style>
</head>
<body style="background: #f3f4f6; padding: 20px 0;">

    @if(($data['format'] ?? 'pdf') !== 'word')
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <p style="font-family: sans-serif; color: #6b7280; font-size: 14px;">Pastikan pengaturan margins/scale default di dialog print agar layout presisi.</p>
    </div>
    @endif

    <div class="page-a4" style="@if(($data['format'] ?? 'pdf') !== 'word') box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); @endif">
        
        <!-- A. Kop Surat (Header) Resmi -->
        <table class="kop-table">
            <tr>
                <td class="logo-col">
                    <img class="logo-img" src="{{ ($data['format'] ?? 'pdf') === 'pdf' ? public_path('images/logo-undip.png') : asset('images/logo-undip.png') }}" alt="Logo Universitas Diponegoro">
                </td>
                <td class="title-col">
                    <h2>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h2>
                    <h1>UNIVERSITAS DIPONEGORO</h1>
                    <h2>FAKULTAS TEKNIK</h2>
                    <h2>DEPARTEMEN TEKNIK KOMPUTER</h2>
                    <div class="address">
                        Jalan Prof. Sudarto, S.H. Tembalang Semarang Kode Pos 50275<br>
                        Telp. (024) 76480609 | www.ft.undip.ac.id | email: teknik@undip.ac.id
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Garis Pembatas Kop Surat -->
        <div class="kop-border"></div>

        <!-- B. Bagian Atas Surat (Metadata) -->
        <table class="meta-table">
            <tr>
                <td width="90">Nomor</td>
                <td width="15">:</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/UN7.F3.5.12/AK/IV/2026</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td>Permohonan Kerja Praktik</td>
            </tr>
        </table>

        <!-- C. Alamat Tujuan Surat -->
        <div class="recipient">
            <p>Yth.</p>
            <p>Wakil Dekan Bidang Akademik dan Kemahasiswaan</p>
            <p>Fakultas Teknik Universitas Diponegoro</p>
            <p>di tempat</p>
        </div>

        <!-- D. Paragraf Pembuka -->
        <p class="body-text">
            Dalam rangka menyelesaikan mata kuliah kerja praktik, mahasiswa Fakultas Teknik yang tersebut di bawah ini:
        </p>

        <!-- Formulir Data Mahasiswa (Mendukung Multi-Anggota) -->
        @foreach($anggota as $index => $mhs)
        <table class="student-table">
            <tr>
                <td width="180">Nama</td>
                <td width="20">:</td>
                <td><strong>{{ $mhs['nama'] ?? '......................................................' }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $mhs['nim'] ?? '......................................................' }}</td>
            </tr>
            <tr>
                <td>Prodi/Jurusan</td>
                <td>:</td>
                <td>Teknik Komputer</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $mhs['semester'] ?? '4 (Empat)' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $mhs['alamat'] ?? '......................................................' }}</td>
            </tr>
            <tr>
                <td>Telepon/Hp</td>
                <td>:</td>
                <td>{{ $mhs['no_hp'] ?? '......................................................' }}</td>
            </tr>
        </table>
        @if(!$loop->last)
        <div style="height: 10px; border-bottom: 1px dashed #ddd; margin: 15px 0 15px 40px;"></div>
        @endif
        @endforeach

        <!-- E. Paragraf Penutup & Isi Permohonan -->
        <p class="closing-text">
            Mohon izin bersama ini kami memohon kiranya berkenan memberikan Surat Pengantar kepada <strong>{{ $data['instansi'] ?? '......................................................' }}</strong> guna keperluan pelaksanaan Kerja Praktik yang direncanakan berlangsung pada tanggal <strong>{{ $data['durasi'] ?? '......................................................' }}</strong>.
        </p>

        <p class="closing-text" style="margin-top: 15px;">
            Demikian permohonan ini kami sampaikan, atas perhatian dan bantuan Ibu, kami ucapkan terima kasih.
        </p>

        <!-- F. Bagian Tanda Tangan (Sign-off) -->
        @php
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $dateStr = date('j') . ' ' . $bulan[date('n')] . ' ' . date('Y');
        @endphp
        
        <div class="signature-container">
            <div class="signature-box">
                <p>Semarang, {{ $dateStr }}</p>
                <p style="margin-top: 10px; margin-bottom: 5px;">Ketua Departemen Teknik Komputer</p>
                <div class="signature-space"></div>
                <p><strong><u>Dr. Maman Somantri S.T., M.T.</u></strong></p>
                <p>NIP. 197910022009122002</p>
            </div>
            <div style="clear: both;"></div>
        </div>

    </div>

    @if(($data['format'] ?? 'pdf') !== 'word')
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
    @endif
</body>
</html>

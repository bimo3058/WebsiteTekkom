<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proposal KP - {{ $data['judul'] ?? 'Judul' }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; line-height: 1.6; color: #000; margin: 0; padding: 0; font-size: 12pt; }
        .page-a4 { width: 210mm; min-height: 297mm; padding: 30mm 25mm 30mm 30mm; margin: 0 auto; background: white; box-sizing: border-box; }
        .cover { text-align: center; height: 297mm; display: flex; flex-direction: column; justify-content: center; align-items: center; page-break-after: always; }
        .cover h1 { font-size: 18pt; text-transform: uppercase; margin-bottom: 20px; }
        .cover h2 { font-size: 16pt; margin-bottom: 40px; }
        .cover img { width: 150px; height: auto; margin-bottom: 40px; }
        .cover .instansi { font-size: 14pt; margin-bottom: 60px; font-weight: bold; }
        .cover .bottom { position: absolute; bottom: 50mm; }
        .content { text-align: justify; }
        .content h2 { font-size: 14pt; margin-top: 30px; margin-bottom: 15px; }
        .content p { margin-bottom: 15px; }
        @media print {
            body { background: none; }
            .page-a4 { margin: 0; border: none; box-shadow: none; width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }
        @page { size: A4; margin: 25mm; }
        .btn-print { display: block; margin: 20px auto; padding: 10px 20px; font-size: 16px; background: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer; text-align: center; max-width: 200px; text-decoration: none; font-family: sans-serif; }
    </style>
</head>
<body style="background: #f3f4f6; padding: 20px 0;">

    @if(($data['format'] ?? 'pdf') !== 'word')
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <p style="font-family: sans-serif; color: #6b7280; font-size: 14px;">Pastikan pengaturan kertas adalah A4.</p>
    </div>
    @endif

    <div class="page-a4" style="@if(($data['format'] ?? 'pdf') !== 'word') box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); @endif">
        
        <!-- Halaman Cover -->
        <div class="cover" style="position: relative;">
            <h1>PROPOSAL KERJA PRAKTIK</h1>
            <h2>{{ $data['judul'] ?? 'JUDUL PROPOSAL KERJA PRAKTIK' }}</h2>
            
            <div style="height: 150px; border: 2px dashed #ccc; width: 150px; display: flex; align-items: center; justify-content: center; margin-bottom: 40px;">
                <span style="color: #999; font-family: sans-serif; font-size: 10px;">[Logo Universitas]</span>
            </div>
            
            <div class="instansi">
                Diajukan ke:<br>
                {{ $data['instansi'] ?? 'Nama Instansi/Perusahaan' }}
            </div>

            <div class="bottom">
                <h3 style="font-size: 14pt;">DEPARTEMEN TEKNIK KOMPUTER</h3>
                <h3 style="font-size: 14pt;">FAKULTAS TEKNIK</h3>
                <h3 style="font-size: 14pt;">UNIVERSITAS XYZ</h3>
                <h3 style="font-size: 14pt;">{{ date('Y') }}</h3>
            </div>
        </div>

        <!-- Halaman Isi -->
        <div class="content">
            {!! $data['content'] ?? '<p>Tidak ada konten.</p>' !!}
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

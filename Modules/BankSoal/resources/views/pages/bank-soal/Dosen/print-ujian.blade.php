<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ekstraksi Soal - {{ $mataKuliah->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: white; font-size: 11pt; }
            .page-break { page-break-after: always; }
        }
        body { font-family: 'Times New Roman', Times, serif; background-color: #f1f5f9; padding: 2rem 0; }
        .document-container { max-width: 21cm; margin: 0 auto; background: white; padding: 2.5cm; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="text-center no-print mb-6 space-x-4">
        <button onclick="window.print()" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-sans font-medium text-sm shadow-sm transition-colors">
            Cetak PDF / Print
        </button>
        <button onclick="window.close()" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-6 py-2.5 rounded-lg font-sans font-medium text-sm transition-colors">
            Tutup Tab
        </button>
    </div>

    <div class="document-container">
        <!-- Header -->
        <div style="border-bottom: 3px solid black; padding-bottom: 8px; margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <tr style="vertical-align: middle;">
                    <!-- Logo (kiri) -->
                    <td style="width: 13%; text-align: center; padding: 0; line-height: 0">
                        <img src="{{ asset('images/logo-undip.png') }}" style="width: 180px; height: 180px; object-fit: contain; display: block; margin: 0 auto;" alt="Logo Undip" />
                    </td>

                    <!-- Judul & Info -->
                    <td style="width: 52%; padding: 0 2px; vertical-align: middle;">
                        <div style="font-size: 9.5pt; font-weight: bold; color: #003366; line-height: 1.3;">
                            KEMENTERIAN PENDIDIKAN TINGGI, SAIN, DAN TEKNOLOGI
                        </div>
                        <div style="font-size: 16pt; font-weight: bold; color: #003366; line-height: 1.1; letter-spacing: 0.3px;">
                            UNIVERSITAS DIPONEGORO
                        </div>
                        <div style="font-size: 10pt; font-weight: bold; color: #003366; line-height: 1.3;">
                            FAKULTAS TEKNIK
                        </div>
                        <div style="font-size: 10pt; font-weight: bold; color: #003366; line-height: 1.3;">
                            DEPARTEMEN TEKNIK KOMPUTER
                        </div>
                    </td>

                    <!-- Kontak (kanan) -->
                    <td style="width: 35%; text-align: right; padding-left: 0; vertical-align: middle;">
                        <div style="font-size: 9pt; color: #003366; line-height: 1.6;">
                            <p style="margin: 0;">Jalan Prof. Sudarto, S.H.</p>
                            <p style="margin: 0;">Tembalang Semarang Kode Pos 50275</p>
                            <p style="margin: 0;">Telp. (024) 7460055, (024) 7460053, Faks. (024) 7460053</p>
                            <p style="margin: 0;"><u>tekkom.ft.undip.ac.id</u> | email: tekkom[at]undip.ac.id</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Judul Ujian & Informasi Pelaksanaan -->
        <div class="mb-8">
            <table class="w-full text-left text-[11pt] border-none">
                <tbody>
                    <tr>
                        <td class="py-1.5 w-[120px] align-top">Matakuliah</td>
                        <td class="py-1.5 w-4 align-top">:</td>
                        <td class="py-1.5 align-top pr-4">{{ $mataKuliah->nama }}</td>
                        
                        <td class="py-1.5 w-[120px] align-top pl-4">Ruang Ujian</td>
                        <td class="py-1.5 w-4 align-top">:</td>
                        <td class="py-1.5 align-top">{{ $request->ruang_ujian ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 align-top">Hari/Tanggal</td>
                        <td class="py-1.5 align-top">:</td>
                        <td class="py-1.5 align-top pr-4">............................</td>
                        
                        <td class="py-1.5 align-top pl-4">Sifat Ujian</td>
                        <td class="py-1.5 align-top">:</td>
                        <td class="py-1.5 align-top">{{ $request->sifat_ujian ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1.5 align-top">Jam</td>
                        <td class="py-1.5 align-top">:</td>
                        <td class="py-1.5 align-top pr-4">{{ $request->waktu ?? '............................' }}</td>
                        
                        <td class="py-1.5 align-top pl-4">Dosen Penguji</td>
                        <td class="py-1.5 align-top">:</td>
                        <td class="py-1.5 align-top">{{ auth()->user()->name ?? '............................' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-4">
            <p class="font-bold underline mb-1">PETUNJUK:</p>
            <ol class="list-decimal list-outside ml-5 text-[11pt]">
                <li>Berdoalah sebelum mengerjakan soal.</li>
                <li>Jawablah pertanyaan dengan jujur, jelas, dan rapi.</li>
                <li>Perhatikan bobot setiap butir soal.</li>
            </ol>
        </div>

        <div class="mb-6 border-t-2 border-black pt-2"></div>

        <!-- Daftar Soal -->
        <div class="space-y-6">
            @foreach($soals as $index => $soal)
            <div class="soal-item">
                <div class="flex gap-4">
                    <div class="font-bold">{{ $index + 1 }}.</div>
                    <div class="flex-1">
                        <div class="text-justify prose prose-sm max-w-none">{!! $soal->soal !!}</div>
                        
                        @if($soal->jawaban && $soal->jawaban->count() > 0)
                        <div class="mt-3 space-y-1">
                            @foreach($soal->jawaban as $idx => $jawab)
                                @php $char = chr(65 + $idx); @endphp
                                <div class="flex gap-2 {{ $jawab->is_benar ? 'font-bold' : '' }}">
                                    <div>{{ $jawab->opsi ?? $char }}.</div>
                                    <div class="prose prose-sm max-w-none">{!! $jawab->deskripsi !!}</div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                        
                        <div class="mt-4 text-xs text-gray-500 text-right italic no-print">
                            Tipe: {{ ucwords(str_replace('_', ' ', $soal->tipe_soal ?? 'Essay')) }} &bull; 
                            CPL: {{ $soal->cpl?->kode ?? '-' }} 
                            @if($soal->cpmk) &bull; CPMK: {{ $soal->cpmk->kode }} @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
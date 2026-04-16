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
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-sans font-medium text-sm shadow-sm transition-colors">
            Cetak PDF / Print
        </button>
        <button onclick="window.close()" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-6 py-2.5 rounded-lg font-sans font-medium text-sm transition-colors">
            Tutup Tab
        </button>
    </div>

    <div class="document-container">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
            <div class="w-24">
                <!-- Placeholder untuk Logo Universitas -->
                <div class="w-20 h-20 border-2 border-gray-400 border-dashed rounded-full flex items-center justify-center text-xs text-gray-400 no-print">
                    Logo
                </div>
            </div>
            <div class="flex-1 text-center">
                <h1 class="text-[14pt] font-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
                <h2 class="text-[16pt] font-bold">UNIVERSITAS DIPONEGORO</h2>
                <h3 class="text-[14pt] font-bold uppercase">FAKULTAS TEKNIK</h3>
                <h4 class="text-[12pt] font-bold uppercase">DEPARTEMEN TEKNIK KOMPUTER</h4>
                <p class="text-[10pt]">Jl. Prof. Soedarto, SH, Kampus Undip Tembalang, Semarang 50275</p>
            </div>
            <div class="w-24"></div>
        </div>

        <!-- Judul Ujian & Informasi Pelaksanaan -->
        <div class="text-center mb-6">
            <h2 class="text-[14pt] font-bold underline mb-4 uppercase">{{ $request->agenda ?? 'SOAL UJIAN' }}</h2>
            <table class="w-full text-left font-bold text-[11pt] mx-auto" style="max-width: 80%;">
                <tbody>
                    <tr>
                        <td class="py-1 w-[150px]">Mata Kuliah</td>
                        <td class="py-1 w-2">:</td>
                        <td class="py-1 font-normal">{{ $mataKuliah->nama }} ({{ $mataKuliah->kode }})</td>
                    </tr>
                    <tr>
                        <td class="py-1">Semester / T.A</td>
                        <td class="py-1">:</td>
                        <td class="py-1 font-normal">{{ $request->semester ?? '-' }} / {{ $request->tahun_ajaran ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1">Sifat Ujian</td>
                        <td class="py-1">:</td>
                        <td class="py-1 font-normal">Tertutup / Terbuka</td>
                    </tr>
                    <tr>
                        <td class="py-1">Hari/Tanggal</td>
                        <td class="py-1">:</td>
                        <td class="py-1 font-normal">............................</td>
                    </tr>
                    <tr>
                        <td class="py-1">Waktu</td>
                        <td class="py-1">:</td>
                        <td class="py-1 font-normal">{{ $request->waktu ?? '120 Menit' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1">Dosen Pengampu</td>
                        <td class="py-1">:</td>
                        <td class="py-1 font-normal">{{ auth()->user()->name ?? '............................' }}</td>
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

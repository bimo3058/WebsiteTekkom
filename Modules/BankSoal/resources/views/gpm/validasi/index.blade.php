<div class="soal-container">
    {{-- Menampilkan Informasi Soal --}}
    @if($soal)
        <div class="mb-4">
            <h3 class="text-lg font-bold">Mata Kuliah: {{ $soal->mk_nama }} ({{ $soal->mk_kode }})</h3>
            <p class="text-sm text-gray-600">CPL: {{ $soal->cpl_kode }} - {{ $soal->cpl_deskripsi }}</p>
            <p class="mt-2 text-md">{{ $soal->soal }}</p>
            <p class="text-sm text-gray-500">Bobot: {{ $soal->bobot }} | Kesulitan: {{ $soal->kesulitan }}</p>
        </div>

        {{-- Looping Opsi Jawaban --}}
        <div class="jawaban-container">
            <h4 class="font-semibold mb-2">Pilihan Jawaban:</h4>
            <ul class="space-y-2">
                @foreach($jawaban as $opsi)
                    <li class="p-2 border rounded {{ $opsi->is_benar ? 'bg-green-100 border-green-500' : 'bg-gray-50 border-gray-200' }}">
                        <span class="font-bold">{{ $opsi->opsi }}.</span> 
                        {{ $opsi->deskripsi }}
                        
                        @if($opsi->is_benar)
                            <span class="text-green-600 text-sm font-bold ml-2">(Jawaban Benar)</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="text-red-500">Soal tidak ditemukan.</p>
    @endif
</div>
<x-banksoal::layouts.dosen-master>

@include('banksoal::partials.dosen.layout-styles')
@include('banksoal::partials.dosen.sidebar', ['active' => 'bank-soal'])
@include('banksoal::partials.dosen.topbar')

<main class="main">
    <div class="page-header">
        <div class="page-header-left">
            <a href="{{ route('banksoal.soal.dosen.index') }}" style="color: #64748b; font-size: 14px; text-decoration: none; margin-bottom: 5px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Kembali ke daftar
            </a>
            <h1>Detail Soal ({{ 'Q-' . str_pad($soal->id, 3, '0', STR_PAD_LEFT) }})</h1>
            <p>Informasi detail pertanyaan dan opsi jawaban</p>
        </div>
    </div>

    <div class="section-card" style="padding: 25px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px;">
            <div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 5px 0;">Mata Kuliah</p>
                <div style="font-weight: 600;">{{ $soal->mataKuliah->kode ?? '-' }} - {{ $soal->mataKuliah->nama ?? '-' }}</div>
            </div>
            <div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 5px 0;">CPL / Topik</p>
                <div style="font-weight: 600;">{{ $soal->cpl->kode ?? '-' }}</div>
            </div>
            <div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 5px 0;">Tingkat Kesulitan / Bobot</p>
                <div style="font-weight: 600;">{{ ucfirst($soal->kesulitan) }} ({{ $soal->bobot }} Poin)</div>
            </div>
            <div>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 5px 0;">Status Pertanyaan</p>
                <div>
                     <span class="badge" style="background-color: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                        {{ strtoupper($soal->status) }}
                     </span>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <p style="font-size: 14px; font-weight: 600; color: #334155; margin: 0 0 10px 0;">Pertanyaan:</p>
            <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 15px;">
                {!! nl2br(e($soal->soal)) !!}
            </div>
        </div>

        <div>
            <p style="font-size: 14px; font-weight: 600; color: #334155; margin: 0 0 10px 0;">Pilihan Jawaban:</p>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($soal->jawaban as $jawab)
                    <div style="padding: 12px 15px; border-radius: 8px; border: 1px solid {{ $jawab->is_benar ? '#22c55e' : '#e2e8f0' }}; background-color: {{ $jawab->is_benar ? '#f0fdf4' : '#fff' }}; display: flex; align-items: flex-start; gap: 15px;">
                        <div style="font-weight: 700; color: {{ $jawab->is_benar ? '#166534' : '#64748b' }}; min-width: 25px;">
                            {{ $jawab->opsi }}.
                        </div>
                        <div style="flex-grow: 1; color: #334155;">
                            {{ $jawab->deskripsi }}
                        </div>
                        @if($jawab->is_benar)
                            <div style="color: #22c55e;" title="Jawaban Benar">
                                <i class="fas fa-check-circle" style="font-size: 18px;"></i>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        
        <div style="margin-top: 30px; text-align: right;">
            @can('banksoal.edit')
                <a href="{{ route('banksoal.soal.dosen.edit', $soal->id) }}" class="btn-primary" style="text-decoration: none;">
                    <i class="fas fa-pen"></i> Edit Soal
                </a>
            @endcan
        </div>
    </div>
</main>
</x-banksoal::layouts.dosen-master>

{{-- resources/views/modules/banksoal/pages/bank-soal/Dosen/_stats.blade.php --}}

@php
    // Calculate stats from $soals collection
    $totalSoal = ($soals ?? collect())->count();
    $totalDraft = ($soals ?? collect())->where('status', 'draft')->count();
    $totalDiajukan = ($soals ?? collect())->where('status', 'diajukan')->count();
    $totalDisetujui = ($soals ?? collect())->where('status', 'disetujui')->count();
    $totalRevisi = ($soals ?? collect())->whereIn('status', ['revisi', 'ditolak'])->count();

    $stats = [
        ['label' => 'Total Soal', 'value' => $totalSoal, 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'var(--c-primary)'],
        ['label' => 'Draft', 'value' => $totalDraft, 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'color' => '#64748b'],
        ['label' => 'Diajukan', 'value' => $totalDiajukan, 'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8', 'color' => 'var(--c-primary)'],
        ['label' => 'Disetujui', 'value' => $totalDisetujui, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => '#28a745'],
    ];
@endphp

<div class="dash-stats">
    @foreach($stats as $stat)
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
         onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
         onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
            <div style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:{{ $stat['color'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">{{ $stat['label'] }}</p>
        </div>
        <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
            {{ number_format($stat['value']) }}
        </p>
    </div>
    @endforeach
</div>

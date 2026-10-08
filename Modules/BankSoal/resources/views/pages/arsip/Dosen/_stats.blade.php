<div class="bs-archive-stats">
    @foreach([
        ['label' => 'Total Arsip', 'value' => $stats['total_arsip'], 'icon' => 'fa-archive'],
        ['label' => 'Riwayat Penarikan', 'value' => $stats['total_penarikan'], 'icon' => 'fa-history'],
        ['label' => 'Mata Kuliah', 'value' => $stats['mata_kuliah'], 'icon' => 'fa-book'],
    ] as $archiveStat)
        <div class="bs-archive-stat">
            <div class="bs-archive-stat-label">
                <span><i class="fas {{ $archiveStat['icon'] }}" aria-hidden="true"></i></span>
                <p>{{ $archiveStat['label'] }}</p>
            </div>
            <strong>{{ number_format($archiveStat['value']) }}</strong>
        </div>
    @endforeach
</div>

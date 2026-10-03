<header class="dosen-page-header">
    <div class="bs-heading-row">
        <div>
            <div class="bs-heading-label"><h1>{{ $title }}</h1><span class="bs-role-badge">Admin</span></div>
            <p>{{ $description }}</p>
        </div>
    </div>
    <nav class="control-tabs" aria-label="Kontrol bank soal">
        <a href="{{ route('banksoal.admin.kontrol-banksoal.rps') }}" @if($active === 'rps') aria-current="page" @endif>Dokumen RPS</a>
        <a href="{{ route('banksoal.admin.kontrol-banksoal.soal') }}" @if($active === 'soal') aria-current="page" @endif>Cetak soal ujian</a>
    </nav>
</header>

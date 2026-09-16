<div class="eo-summary">
    @if(!empty($semesterLabel))<span>{{ $semesterLabel }}</span>@endif
    <span>Praktikum · Kerja Praktik · Surat · Ruangan</span>
    @if($dashboardRole === 'mahasiswa')
        <span><strong>{{ $tugasPendingCount ?? $tugasMendatang->count() }}</strong> tugas perlu ditindaklanjuti</span>
        @if($absensiPct !== null)<span><strong>{{ $absensiPct }}%</strong> kehadiran</span>@endif
        @if($user->student)<span>NIM <strong>{{ $user->student->student_number }}</strong></span>@endif
    @endif
</div>

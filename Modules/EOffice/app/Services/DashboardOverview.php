<?php

namespace Modules\EOffice\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Schema};
use Modules\EOffice\Models\{Absensi, DaftarPraktikan, KerjaPraktik, KpPeriode, KpPengumuman, Peminjaman, Praktikum, Tugas};

/** Read-only dashboard queries. Personal data is always scoped before aggregation. */
class DashboardOverview
{
    public function forUser(User $user, string $role): array
    {
        $letters = $this->letters($user, $role);
        $rooms = $this->rooms($user, $role);
        $kp = $this->kp($user, $role);
        $practice = $this->practice($user, $role);

        return array_merge($practice['data'], [
            'dashboardServices' => ['praktikum' => $practice['card'], 'kp' => $kp['card'], 'surat' => $letters, 'ruangan' => $rooms],
            'kpTimeline' => $kp['timeline'],
            'kpTimelineNotices' => KpPengumuman::where('is_active', true)->where('tipe', 'timeline')->latest()->limit(5)->get(['judul', 'konten']),
            'statusKp' => $kp['status'],
        ]);
    }

    private function card(string $title, string $scope, ?string $route, array $metrics, array $entries = [], ?string $note = null): array
    {
        return compact('title', 'scope', 'route', 'metrics', 'entries', 'note');
    }

    public function letters(User $user, string $role): array
    {
        $scope = $role === 'admin' ? 'Seluruh pengajuan surat' : 'Surat yang Anda ajukan';
        if (!Schema::hasTable('eo_surat')) {
            return $this->card('Manajemen Surat', $scope, null, [], [], 'Data surat belum tersedia. Halaman layanan surat belum diimplementasikan.');
        }
        $query = DB::table('eo_surat')->when($role !== 'admin', fn ($q) => $q->where('pemohon_id', $user->id));
        $counts = (clone $query)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $metrics = [
            'Menunggu / diproses' => (int) ($counts['diajukan'] ?? 0) + (int) ($counts['diproses'] ?? 0),
            'Disetujui / selesai' => (int) ($counts['disetujui'] ?? 0) + (int) ($counts['selesai'] ?? 0),
            'Ditolak' => (int) ($counts['ditolak'] ?? 0),
            'Draf' => (int) ($counts['draft'] ?? 0),
        ];
        if ($role === 'dosen' && Schema::hasTable('eo_approval')) {
            $metrics['Persetujuan ditugaskan kepada Anda'] = DB::table('eo_approval')->where('approver_id', $user->id)->where('status', 'menunggu')->distinct()->count('surat_id');
        }
        $entries = (clone $query)->orderByDesc('tanggal_pengajuan')->orderByDesc('id')->limit(3)
            ->get(['id', 'nomor_surat', 'status', 'tanggal_pengajuan'])
            ->map(fn ($s) => ['title' => $s->nomor_surat ?: 'Pengajuan #'.$s->id, 'detail' => $this->date($s->tanggal_pengajuan), 'status' => ucfirst($s->status)])->all();
        return $this->card('Manajemen Surat', $scope, null, $metrics, $entries, 'Ringkasan data surat tersedia; halaman pengelolaan surat belum diimplementasikan.');
    }

    public function rooms(User $user, string $role): array
    {
        $query = Peminjaman::query()->when($role !== 'admin', fn ($q) => $q->where('user_id', $user->id));
        // Match auto-expiry rules without mutating bookings during dashboard reads.
        $future = fn ($q) => $q->where('tanggal_pinjam', '>', today()->toDateString())->orWhere(fn ($q) => $q->whereDate('tanggal_pinjam', today())->where('jam_mulai', '>', now()->format('H:i:s')));
        $pending = (clone $query)->where('status', 'menunggu')->where($future)->count();
        $upcoming = (clone $query)->where('status', 'disetujui')->where(fn ($q) => $q->where('tanggal_pinjam', '>', today()->toDateString())->orWhere(fn ($q) => $q->whereDate('tanggal_pinjam', today())->where('jam_selesai', '>', now()->format('H:i:s'))));
        $counts = (clone $query)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $entries = (clone $upcoming)->with('ruangan:id,nama')->orderBy('tanggal_pinjam')->orderBy('jam_mulai')->limit(3)->get()
            ->map(fn ($p) => ['title' => $p->ruangan?->nama ?? 'Ruangan', 'detail' => $this->date($p->tanggal_pinjam).' · '.substr($p->jam_mulai, 0, 5).'–'.substr($p->jam_selesai, 0, 5).' · '.$p->tujuan, 'status' => 'Disetujui'])->all();
        $requests = (clone $query)->with('ruangan:id,nama')->whereIn('status', ['menunggu', 'ditolak'])->latest()->limit(2)->get()
            ->map(fn ($p) => ['title' => 'Pengajuan · '.($p->ruangan?->nama ?? 'Ruangan'),
                'detail' => $this->date($p->tanggal_pinjam).' · '.substr($p->jam_mulai, 0, 5).' · '.($p->alasan_penolakan ?: $p->tujuan),
                'status' => $p->status === 'menunggu' && Carbon::parse($p->tanggal_pinjam->toDateString().' '.$p->jam_mulai)->lte(now()) ? 'Kedaluwarsa' : ucfirst($p->status)])->all();
        return $this->card('Peminjaman Ruangan', $role === 'admin' ? 'Persetujuan dan agenda seluruh ruangan' : 'Pengajuan dan agenda peminjaman Anda', 'eoffice.peminjaman.dashboard', [
            'Menunggu konfirmasi' => $pending, 'Agenda mendatang' => (clone $upcoming)->count(),
            'Ditolak' => (int) ($counts['ditolak'] ?? 0), 'Menunggu kedaluwarsa' => max(0, (int) ($counts['menunggu'] ?? 0) - $pending),
        ], array_merge($entries, $requests), 'Maksimal 3 agenda disetujui berikutnya dan 2 pengajuan menunggu / ditolak terbaru.');
    }

    public function kp(User $user, string $role): array
    {
        $query = KerjaPraktik::query();
        if ($role === 'mahasiswa') $query->whereHas('mahasiswa', fn ($q) => $q->where('user_id', $user->id));
        elseif ($role !== 'admin') $query->whereHas('dosenPembimbing', fn ($q) => $q->where('user_id', $user->id));
        $counts = (clone $query)->selectRaw('status_kp, COUNT(*) as total')->groupBy('status_kp')->pluck('total', 'status_kp');
        $records = (clone $query)->with(['mahasiswa', 'seminar', 'dosenPembimbing'])->latest()->orderByDesc('id')->limit(3)->get();
        $current = $role === 'mahasiswa' ? $records->first() : null;
        // Use the stored FK: the legacy model accessor infers periods from created_at.
        $periodId = $current?->getRawOriginal('periode_id');
        $period = $periodId ? KpPeriode::find($periodId) : null;
        if (!$period && $current?->created_at) {
            $period = KpPeriode::whereDate('pra_kp_mulai', '<=', $current->created_at)->whereDate('pra_kp_akhir', '>=', $current->created_at)->latest('id')->first();
        }
        // An existing student's KP must not be assigned a different active period.
        if (!$current) $period = KpPeriode::where('is_active', true)->latest('id')->first();
        $pending = (clone $query)->where('is_acc_admin', false)->whereIn('status_kp', ['Pra-KP', 'Pra KP'])->count();
        $kpIds = (clone $query)->select('eo_kerja_praktik.id');
        $documents = DB::table('eo_kp_dokumen')->whereIn('kp_id', clone $kpIds)->where('status_validasi', '!=', 'draft')
            ->whereIn('approval_status', ['pending', 'menunggu', 'revisi', 'rejected'])->count();
        $seminars = DB::table('eo_kp_seminar')->whereIn('kp_id', clone $kpIds)->where(function ($q) {
            $q->whereNull('status_validasi_dosen')->orWhereNotIn('status_validasi_dosen', ['approved', 'disetujui', 'diterima']);
        })->count();
        $entries = $records->map(function ($kp) use ($role) {
            $detail = $kp->instansi_kp ?: 'Instansi belum ditentukan';
            if ($role === 'mahasiswa') $detail .= ' · Pembimbing: '.($kp->dosenPembimbing?->nama_lengkap ?: 'Belum ditentukan');
            if ($kp->tanggal_mulai || $kp->tanggal_selesai) $detail .= ' · Pelaksanaan '.$this->date($kp->tanggal_mulai).'–'.$this->date($kp->tanggal_selesai);
            if ($kp->seminar?->tanggal_seminar) $detail .= ' · Seminar '.$this->date($kp->seminar->tanggal_seminar).' ('.($kp->seminar->status_validasi_dosen ?: 'menunggu validasi').')';
            return ['title' => $role === 'mahasiswa' ? ($kp->judul_kp ?: 'Judul belum ditentukan') : ($kp->mahasiswa?->nama_lengkap ?: 'Mahasiswa'), 'detail' => $detail, 'status' => $kp->status_kp];
        })->all();
        $route = 'eoffice.kp.'.($role === 'admin' || $user->hasRole('koor_kp') ? 'koordinator' : $role).'.dashboard';
        return ['card' => $this->card('Kerja Praktik', $role === 'admin' ? 'Seluruh peserta KP' : ($role === 'dosen' ? 'Mahasiswa bimbingan Anda' : 'Perkembangan KP Anda'), $route, [
            'Pra-KP' => (int) ($counts['Pra-KP'] ?? 0) + (int) ($counts['Pra KP'] ?? 0),
            'Pelaksanaan KP' => (int) ($counts['Saat KP'] ?? 0) + (int) ($counts['KP Berjalan'] ?? 0),
            'Pasca-KP' => (int) ($counts['Pasca KP'] ?? 0),
            'Selesai' => (int) ($counts['Selesai'] ?? 0) + (int) ($counts['Selesai KP'] ?? 0),
            'Menunggu persetujuan registrasi' => $pending,
            'Dokumen menunggu / perlu revisi' => $documents,
            'Seminar belum disetujui dosen' => $seminars,
        ], $entries), 'timeline' => $this->timeline($period, $current, $route), 'status' => $current?->status_kp];
    }

    public function practice(User $user, string $role): array
    {
        $query = Praktikum::where('status', 'aktif');
        if ($role === 'dosen') $query->whereHas('dosens', fn ($q) => $q->where('users.id', $user->id));
        if ($role === 'mahasiswa') $query->whereHas('daftarPraktikan', fn ($q) => $q->where('user_id', $user->id));
        $ids = (clone $query)->select('eo_praktikum.id');
        $tasks = Tugas::where('is_published', true)->whereHas('modul', fn ($q) => $q->whereIn('praktikum_id', clone $ids));
        $data = [];
        $metrics = ['Praktikum aktif' => (clone $query)->count()];
        if ($role === 'mahasiswa') {
            $registrations = DaftarPraktikan::where('user_id', $user->id)->whereIn('praktikum_id', clone $ids)->select('id');
            $pending = (clone $tasks)->whereDoesntHave('pengumpulan', fn ($q) => $q->whereIn('daftar_praktikan_id', clone $registrations)->whereIn('status_pengumpulan', ['belum_dicek', 'acc']));
            $metrics['Belum dikumpulkan / revisi'] = (clone $pending)->count();
            $metrics['Tenggat pengumpulan lewat'] = (clone $pending)->whereRaw('COALESCE(deadline_acc, deadline) < ?', [now()])->count();
            $attendance = Absensi::whereIn('daftar_praktikan_id', clone $registrations)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
            $data['absensiPct'] = $attendance->sum() ? (int) round(($attendance['hadir'] ?? 0) / $attendance->sum() * 100) : null;
            $metrics['Kehadiran tercatat'] = $data['absensiPct'] === null ? 'Belum ada' : $data['absensiPct'].'%';
            $data['tugasPendingCount'] = $metrics['Belum dikumpulkan / revisi'];
            $data['tugasMendatang'] = (clone $pending)->with(['modul', 'pengumpulan' => fn ($q) => $q->whereIn('daftar_praktikan_id', clone $registrations)])
                ->orderByRaw('CASE WHEN COALESCE(deadline_acc, deadline) IS NULL THEN 1 ELSE 0 END')->orderByRaw('COALESCE(deadline_acc, deadline)')->limit(5)->get()->map(fn ($t) => [
                'judul' => $t->judul, 'deadline' => $t->deadline_acc ?? $t->deadline, 'sudah_kumpul' => false, 'revisi' => $t->pengumpulan->contains('status_pengumpulan', 'revisi'),
                'url' => route('eoffice.manprak.mahasiswa.tugas.index', ['praktikum_id' => $t->modul->praktikum_id]),
            ]);
        } else {
            $metrics['Peserta terdaftar'] = DaftarPraktikan::whereIn('praktikum_id', clone $ids)->count();
            $metrics['Tugas terbit'] = (clone $tasks)->count();
            $metrics['Pengumpulan perlu diperiksa'] = DB::table('pengumpulan_tugas')->whereIn('tugas_id', (clone $tasks)->select('tugas_praktikum.id'))->where('status_pengumpulan', 'belum_dicek')->count();
        }
        $entries = (clone $query)->withCount('daftarPraktikan')->latest()->limit(3)->get()->map(fn ($p) => [
            'title' => $p->nama, 'detail' => ($p->kode ?: 'Tanpa kode').' · '.$p->daftar_praktikan_count.' peserta', 'status' => 'Aktif',
        ])->all();
        return ['card' => $this->card('Manajemen Praktikum', match ($role) {'admin' => 'Seluruh praktikum aktif', 'dosen' => 'Praktikum yang Anda ampu', default => 'Seluruh praktikum aktif yang Anda ikuti'}, 'eoffice.manprak.'.$role.'.dashboard', $metrics, $entries), 'data' => $data];
    }

    public function timeline(?KpPeriode $period, ?KerjaPraktik $kp, string $route): array
    {
        $definitions = [
            ['Pendaftaran', 'tanggal_buka', 'tanggal_tutup', 'Daftar pada periode KP, lengkapi identitas dan persyaratan akademik.'],
            ['Pra-KP', 'pra_kp_mulai', 'pra_kp_akhir', 'Lengkapi pengajuan, proposal, instansi, dan persetujuan pembimbing.'],
            ['Pelaksanaan KP', 'saat_kp_mulai', 'saat_kp_akhir', 'Laksanakan KP sesuai jadwal dan lengkapi dokumen serta laporan kegiatan.'],
            ['Pasca-KP', 'pasca_kp_mulai', 'pasca_kp_akhir', 'Selesaikan laporan, persyaratan seminar, revisi, dan penilaian akhir.'],
        ];
        $steps = [];
        foreach ($definitions as [$title, $startKey, $endKey, $description]) {
            $start = $period?->$startKey ? Carbon::parse($period->$startKey)->startOfDay() : null;
            $end = $period?->$endKey ? Carbon::parse($period->$endKey)->endOfDay() : null;
            $state = !$start || !$end ? 'unscheduled' : ($end->lt($start) ? 'invalid' : (now()->lt($start) ? 'upcoming' : (now()->gt($end) ? 'ended' : 'active')));
            $days = $end ? (int) today()->diffInDays($end->copy()->startOfDay(), false) : null;
            $steps[] = ['title' => $title, 'description' => $description, 'start' => $this->date($start), 'end' => $this->date($end), 'state' => $state,
                'label' => match ($state) {'active' => 'Sedang berlangsung', 'ended' => 'Jadwal berakhir', 'upcoming' => 'Akan datang', 'invalid' => 'Jadwal perlu diperiksa', default => 'Belum dijadwalkan'},
                'deadline' => $state === 'active' ? ($days === 0 ? 'Batas akhir hari ini' : $days.' hari menuju batas akhir') : null];
        }
        return ['period' => $period ? $period->tahun_ajaran.' · '.$period->semester : 'Periode belum tersedia', 'status' => $kp?->status_kp,
            'steps' => $steps, 'route' => $route, 'registered' => $kp !== null,
            'next' => match ($kp?->status_kp) {
                'Selesai', 'Selesai KP' => 'KP telah selesai. Periksa hasil penilaian dan arsip dokumen Anda.',
                'Pasca KP' => 'Periksa kelengkapan laporan, jadwal seminar, revisi, dan penilaian akhir.',
                'Saat KP', 'KP Berjalan' => 'Lengkapi laporan kegiatan dan dokumen selama pelaksanaan KP.',
                'Dibatalkan', 'Gagal' => 'Hubungi koordinator untuk memastikan kelanjutan atau pendaftaran ulang KP.',
                default => $kp ? 'Periksa persetujuan registrasi, proposal, instansi, dan pembimbing Anda.' : 'Periksa jadwal periode serta persyaratan pendaftaran di menu Kerja Praktik.',
            }];
    }

    private function date($value): string
    {
        return $value ? Carbon::parse($value)->locale('id')->translatedFormat('d M Y') : 'Belum ditentukan';
    }
}

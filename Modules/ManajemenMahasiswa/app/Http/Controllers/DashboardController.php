<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Services\DashboardAnalitikService;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Pengumuman;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Models\Prestasi;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardAnalitikService $analitikService
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        $pengurusRoles = ['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'];
        if (!empty(array_intersect($roles, $pengurusRoles))) {
            return redirect()->route('manajemenmahasiswa.pengumuman.index');
        }

        if (
            \in_array('superadmin', $roles) ||
            \in_array('admin', $roles) ||
            \in_array('admin_kemahasiswaan', $roles) ||
            \in_array('gpm', $roles) ||
            \in_array('dpm', $roles) ||
            \in_array('ketua_departemen', $roles)
        ) {
            $scope     = $this->analitikService->resolveScope($roles);
            $dashboard = $this->analitikService->getRealTimeDashboard($scope);

            return view('manajemenmahasiswa::dashboard.dashboard-analitik', compact('dashboard'));
        }

        if (\in_array('dosen', $roles)) {
            return redirect()->route('manajemenmahasiswa.pengumuman.index');
        }

        if (\in_array('mahasiswa', $roles)) {
            return redirect()->route('manajemenmahasiswa.pengumuman.index');
        }

        if (\in_array('alumni', $roles)) {
            return redirect()->route('manajemenmahasiswa.pengumuman.index');
        }

        abort(403);
    }

    public function switchMode()
    {
        return redirect()->back();
    }

    /**
     * AJAX endpoint — mengembalikan data list untuk popup modal dashboard.
     * Diakses hanya oleh admin/GPM via fetch() di frontend.
     */
    public function modalData(Request $request): JsonResponse
    {
        $type = $request->query('type');

        switch ($type) {

            case 'mahasiswa':
                $status = $request->query('status', Kemahasiswaan::STATUS_AKTIF);
                $rows = Kemahasiswaan::where('status', $status)
                    ->with('user:id,email')
                    ->whereNotNull('angkatan')
                    ->orderBy('angkatan')
                    ->orderBy('nama')
                    ->get()
                    ->map(fn ($m) => [
                        'nama'     => $m->nama ?? $m->user?->name ?? '-',
                        'nim'      => $m->nim ?? '-',
                        'angkatan' => (int) $m->angkatan,
                        'email'    => $m->user?->email ?? '-',
                        'status'   => $m->status,
                    ]);

                $angkatanList = $rows->pluck('angkatan')->unique()->sort()->values();

                return response()->json([
                    'data'         => $rows,
                    'angkatan_list' => $angkatanList,
                    'total'        => $rows->count(),
                ]);

            case 'alumni':
                $filter = $request->query('filter', 'semua');
                $query  = Alumni::with('user:id,name');

                if ($filter === 'terdata')           $query->whereNotNull('status_karir');
                elseif ($filter === 'belum_terdata') $query->whereNull('status_karir');
                elseif ($filter === 'bekerja')       $query->whereIn('status_karir', [Alumni::STATUS_BEKERJA, Alumni::STATUS_WIRAUSAHA]);

                $rows = $query->orderBy('angkatan')->orderByDesc('tahun_lulus')->get()
                    ->map(fn ($a) => [
                        'nama'         => $a->user?->name ?? '-',
                        'nim'          => $a->nim ?? '-',
                        'angkatan'     => $a->angkatan,
                        'tahun_lulus'  => $a->tahun_lulus,
                        'status_karir' => $a->status_karir_label,
                        'perusahaan'   => $a->perusahaan ?? '-',
                        'jabatan'      => $a->jabatan ?? '-',
                        'url'          => route('manajemenmahasiswa.direktori.alumni.show', $a->id),
                    ]);

                return response()->json(['data' => $rows, 'total' => $rows->count()]);

            case 'kegiatan':
                $rows = Kegiatan::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(fn ($k) => [
                        'judul'         => $k->judul ?? '-',
                        'tanggal_mulai' => $k->tanggal_mulai?->format('d M Y') ?? '-',
                        'lokasi'        => $k->lokasi ?? '-',
                        'url'           => route('manajemenmahasiswa.kegiatan.show', $k->id),
                    ]);

                return response()->json(['data' => $rows, 'total' => $rows->count()]);

            case 'pengumuman':
                $rows = Pengumuman::published()
                    ->whereYear('published_at', now()->year)
                    ->whereMonth('published_at', now()->month)
                    ->with('author:id,name')
                    ->orderByDesc('published_at')
                    ->get()
                    ->map(fn ($p) => [
                        'judul'           => $p->judul,
                        'kategori'        => $p->kategori ? ucfirst(str_replace('_', ' ', $p->kategori)) : '-',
                        'target_audience' => $p->target_audience === 'all' ? 'Semua' : ucfirst($p->target_audience),
                        'author'          => $p->author?->name ?? '-',
                        'published_at'    => $p->published_at?->format('d M Y') ?? '-',
                        'url'             => route('manajemenmahasiswa.pengumuman.show', $p->id),
                    ]);

                return response()->json(['data' => $rows, 'total' => $rows->count()]);

            case 'thread':
                $rows = Thread::where('created_at', '>=', now()->startOfWeek())
                    ->with('user:id,name')
                    ->orderByDesc('created_at')
                    ->get()
                    ->map(fn ($t) => [
                        'judul'         => $t->judul,
                        'kategori'      => is_array($t->kategori) ? implode(', ', $t->kategori) : ($t->kategori ?? '-'),
                        'author'        => $t->user?->name ?? '-',
                        'vote_count'    => $t->vote_count ?? 0,
                        'comment_count' => $t->comment_count ?? 0,
                        'created_at'    => $t->created_at?->diffForHumans() ?? '-',
                        'url'           => route('manajemenmahasiswa.forum.show', $t->id),
                    ]);

                return response()->json(['data' => $rows, 'total' => $rows->count()]);

            case 'prestasi-semua':
                // Semua prestasi terverifikasi dengan bukti, untuk modal dashboard analitik
                $rows = Prestasi::approved()
                    ->with(['kemahasiswaan.user', 'buktiFiles'])
                    ->orderByDesc('verified_at')
                    ->get()
                    ->map(function ($p) {
                        $student = $p->kemahasiswaan;
                        return [
                            'id'            => $p->id,
                            'nama_prestasi' => $p->nama_prestasi,
                            'tingkat'       => $p->tingkat,
                            'tahun'         => $p->tahun,
                            'tanggal'       => $p->tanggal?->translatedFormat('d M Y') ?? '-',
                            'verified_at'   => $p->verified_at?->translatedFormat('d M Y') ?? '-',
                            'student_name'  => $student?->user?->name ?? $student?->nama ?? 'Mahasiswa',
                            'nim'           => $student?->nim ?? '-',
                            'angkatan'      => (int) ($student?->angkatan ?? 0),
                            'bukti'         => $p->buktiFiles->map(fn ($b) => [
                                'nama'     => $b->nama_file ?? basename($b->path_file),
                                'url'      => $b->public_url,
                                'is_image' => $b->isImage(),
                            ])->values()->all(),
                        ];
                    });

                $angkatanList = $rows->pluck('angkatan')
                    ->unique()->filter()->sort()->values();

                return response()->json([
                    'data'          => $rows,
                    'total'         => $rows->count(),
                    'angkatan_list' => $angkatanList,
                ]);

            case 'calon-do':
                // Mahasiswa aktif yang sudah memasuki semester >= 13 (early warning DO)
                $thresholdAngkatan = now()->year - 6;
                $currentYear  = now()->year;
                $currentMonth = now()->month;

                $rows = Kemahasiswaan::aktif()
                    ->whereNotNull('angkatan')
                    ->where('angkatan', '<=', $thresholdAngkatan)
                    ->with('user:id,email')
                    ->orderBy('angkatan')
                    ->orderBy('nama')
                    ->get()
                    ->map(function ($m) use ($currentYear, $currentMonth) {
                        $yearsElapsed = $currentYear - $m->angkatan;
                        $semester = $currentMonth >= 8 ? ($yearsElapsed * 2 + 1) : ($yearsElapsed * 2);
                        return [
                            'nama'     => $m->nama ?? $m->user?->name ?? '-',
                            'nim'      => $m->nim ?? '-',
                            'angkatan' => (int) $m->angkatan,
                            'semester' => max($semester, 13),
                            'email'    => $m->user?->email ?? '-',
                        ];
                    });

                $angkatanList = $rows->pluck('angkatan')->unique()->sort()->values();

                return response()->json([
                    'data'          => $rows,
                    'total'         => $rows->count(),
                    'angkatan_list' => $angkatanList,
                ]);

            case 'lulusan-periode':
                // Mahasiswa berstatus alumni — dipantau per tahun lulus + status sinkron
                $alumniUserIds = Alumni::whereNotNull('user_id')->pluck('user_id')->all();

                $rows = Kemahasiswaan::alumni()
                    ->whereNotNull('tahun_lulus')
                    ->with('user:id,email')
                    ->orderByDesc('tahun_lulus')
                    ->orderBy('nama')
                    ->get()
                    ->map(fn ($m) => [
                        'nama'        => $m->nama ?? $m->user?->name ?? '-',
                        'nim'         => $m->nim ?? '-',
                        'angkatan'    => (int) $m->angkatan,
                        'tahun_lulus' => (int) $m->tahun_lulus,
                        'tersinkron'  => in_array($m->user_id, $alumniUserIds, true),
                    ]);

                $tahunList = $rows->pluck('tahun_lulus')->unique()->sort()->values();

                return response()->json([
                    'data'       => $rows,
                    'total'      => $rows->count(),
                    'tahun_list' => $tahunList,
                ]);

            default:
                return response()->json(['error' => 'Unknown modal type'], 400);
        }
    }
}

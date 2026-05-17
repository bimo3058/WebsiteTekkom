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
            \in_array('gpm', $roles)
        ) {
            $dashboard = $this->analitikService->getRealTimeDashboard();

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

            default:
                return response()->json(['error' => 'Unknown modal type'], 400);
        }
    }
}

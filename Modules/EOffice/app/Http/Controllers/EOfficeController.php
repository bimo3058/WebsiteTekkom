<?php

namespace Modules\EOffice\Http\Controllers;

use App\Models\EoAuditLog;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\KerjaPraktik;
use Modules\EOffice\Models\KpDosen;
use Modules\EOffice\Models\KpMahasiswa;
use Modules\EOffice\Models\KpPengumuman;
use Modules\EOffice\Models\Praktikum;

class EOfficeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('eoffice.view');
        return view('eoffice::index');
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Methods
    |--------------------------------------------------------------------------
    */

    public function adminDashboard()
    {
        // $this->authorize('eoffice.view'); // Bypassed for email logic

        $praktikums = Praktikum::with(['dosen', 'koordinator'])
            ->where('status', 'aktif')
            ->latest()
            ->take(8)
            ->get();

        $recentActivities = EoAuditLog::with('user')
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($log) {
                $typeMap = [
                    'create' => 'blue',
                    'update' => 'success',
                    'delete' => 'warning',
                ];

                $modelLabel = match ($log->model) {
                    'Praktikum'    => 'Praktikum',
                    'EoMaster'     => 'Surat',
                    'EoPeminjaman' => 'Peminjaman',
                    default        => $log->model,
                };

                $actionLabel = match ($log->action) {
                    'create' => 'dibuat',
                    'update' => 'diperbarui',
                    'delete' => 'dihapus',
                    default  => $log->action,
                };

                $newValues = is_array($log->new_values) ? $log->new_values : [];
                $name      = $newValues['nama'] ?? $newValues['name'] ?? ('#' . substr($log->model_id ?? '', 0, 8));

                return [
                    'type' => $typeMap[$log->action] ?? 'blue',
                    'text' => '<strong>' . e($log->user?->name ?? 'Sistem') . '</strong> ' . $actionLabel . ' ' . $modelLabel,
                    'desc' => $name ?: null,
                    'time' => $log->created_at?->diffForHumans() ?? '—',
                ];
            })
            ->toArray();

        return view('eoffice::dashboard.admin', [
            'totalSuratDiproses'     => 0,
            'statSuratHariIni'       => 0,
            'totalPeminjamanAktif'   => 0,
            'totalPeminjamanPending' => 0,
            'totalSuratPending'      => 0,
            'totalPraktikumAktif'    => Praktikum::where('status', 'aktif')->count(),
            'totalKpBerjalan'        => KerjaPraktik::whereNotIn('status_kp', ['Selesai'])->count(),
            'totalKpPending'         => KerjaPraktik::where('status_kp', 'Pra-KP')->where('is_acc_admin', false)->count(),
            'statKpBaru'             => KerjaPraktik::whereDate('created_at', today())->count(),
            'totalDosen'             => User::whereHas('roles', fn($q) => $q->where('name', 'dosen')->where('module', 'eoffice'))->count(),
            'totalLogHariIni'        => EoAuditLog::whereDate('created_at', today())->count(),
            'totalNotifikasi'        => 0,
            'praktikums'             => $praktikums,
            'recentActivities'       => $recentActivities,
            'semesterLabel'          => $this->semesterLabel(),
        ]);
    }

    public function dosenDashboard()
    {
        // $this->authorize('eoffice.view'); // Bypassed for email logic

        $user = auth()->user();

        // Praktikum yang diampu dosen ini
        $praktikumList = Praktikum::with(['koordinator'])
            ->where('dosen_id', $user->id)
            ->withCount('daftarPraktikan')
            ->orderByDesc('created_at')
            ->get();

        // Bimbingan KP — cari lewat tabel eo_kp_dosen dulu
        $kpDosen = KpDosen::where('user_id', $user->id)->first();
        $kpList  = $kpDosen
            ? KerjaPraktik::where('dosen_pembimbing_id', $kpDosen->id)
                ->whereNotIn('status_kp', ['Selesai'])
                ->get()
            : collect();

        return view('eoffice::dashboard.dosen', [
            'praktikumList' => $praktikumList,
            'kpList'        => $kpList,
            'semesterLabel' => $this->semesterLabel(),
        ]);
    }

    public function mahasiswaDashboard()
    {
        // $this->authorize('eoffice.view'); // Bypassed for email logic

        $user = auth()->user();

        // Praktikum aktif yang diikuti mahasiswa
        $daftarPraktikan = DaftarPraktikan::with(['praktikum.dosen'])
            ->where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->where('status', 'aktif'))
            ->first();

        $praktikumAktif = $daftarPraktikan?->praktikum;

        // Persentase kehadiran — TODO: isi saat tabel absensi siap
        $absensiPct = null;

        // Tugas mendatang — TODO: isi saat tabel tugas siap
        $tugasMendatang = collect();

        // Pengumuman (sementara pakai KpPengumuman)
        $pengumuman = KpPengumuman::where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Status KP mahasiswa
        $kpMahasiswa = KpMahasiswa::where('user_id', $user->id)->first();
        $statusKp    = $kpMahasiswa
            ? KerjaPraktik::where('mahasiswa_id', $kpMahasiswa->id)->value('status_kp')
            : null;

        return view('eoffice::dashboard.mahasiswa', [
            'praktikumAktif' => $praktikumAktif,
            'absensiPct'     => $absensiPct,
            'tugasMendatang' => $tugasMendatang,
            'pengumuman'     => $pengumuman,
            'statusKp'       => $statusKp,
            'semesterLabel'  => $this->semesterLabel(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD Methods
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $this->authorize('eoffice.edit');
        return view('eoffice::create');
    }

    public function store(Request $request)
    {
        $this->authorize('eoffice.edit');
        DB::beginTransaction();
        try {
            DB::commit();
            return redirect()->back()->with('success', 'Dokumen berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('eoffice.view');
        return view('eoffice::show');
    }

    public function edit($id)
    {
        $this->authorize('eoffice.edit');
        return view('eoffice::edit');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('eoffice.edit');
    }

    public function destroy($id)
    {
        $this->authorize('eoffice.delete');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function semesterLabel(): string
    {
        $month = now()->month;
        $year  = now()->year;

        if ($month >= 8) {
            return "Semester Ganjil {$year}/" . ($year + 1);
        } elseif ($month <= 1) {
            return "Semester Ganjil " . ($year - 1) . "/{$year}";
        } else {
            return "Semester Genap {$year}/" . ($year + 1);
        }
    }
}

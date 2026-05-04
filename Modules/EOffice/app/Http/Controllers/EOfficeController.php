<?php

namespace Modules\EOffice\Http\Controllers;

use App\Models\EoAuditLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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
        $this->authorize('eoffice.view');

        $praktikums = Praktikum::with(['dosen', 'koordinator'])
            ->where('status', 'aktif')
            ->latest()
            ->take(8)
            ->get();

        // Aktivitas terbaru — EoAuditLog global
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

                // Pesan yang lebih deskriptif berdasarkan model
                $modelLabel = match ($log->model) {
                    'Praktikum'  => 'Praktikum',
                    'EoMaster'   => 'Surat',
                    'EoPeminjaman' => 'Peminjaman',
                    default      => $log->model,
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
            // Stats utama
            'totalSuratDiproses'    => 0,   // TODO: isi saat modul Surat selesai
            'statSuratHariIni'      => 0,
            'totalPeminjamanAktif'  => 0,   // TODO: isi saat modul Peminjaman selesai
            'totalPeminjamanPending'=> 0,
            'totalSuratPending'     => 0,
            'totalPraktikumAktif'   => Praktikum::where('status', 'aktif')->count(),
            'totalKpBerjalan'       => 0,   // TODO: isi saat modul KP selesai
            'totalKpPending'        => 0,
            'statKpBaru'            => 0,
            'totalDosen'            => \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'dosen')->where('module', 'eoffice'))->count(),
            'totalLogHariIni'       => EoAuditLog::whereDate('created_at', today())->count(),
            'totalNotifikasi'       => 0,

            // Data tabel
            'praktikums'         => $praktikums,
            'recentActivities'   => $recentActivities,
            'semesterLabel'      => $this->semesterLabel(),
        ]);
    }

    public function dosenDashboard()
    {
        $this->authorize('eoffice.view');
        return view('eoffice::dashboard.dosen');
    }

    public function mahasiswaDashboard()
    {
        $this->authorize('eoffice.view');
        return view('eoffice::dashboard.mahasiswa');
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
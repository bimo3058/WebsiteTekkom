<?php

namespace Modules\EOffice\Http\Controllers;

use App\Models\EoAuditLog;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        $praktikums = Praktikum::with(['dosens', 'koordinator'])
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
            ->whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
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
        $daftarPraktikan = DaftarPraktikan::with(['praktikum.dosens'])
            ->where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->where('status', 'aktif'))
            ->first();

        $praktikumAktif = $daftarPraktikan?->praktikum;

        // Persentase kehadiran — TODO: isi saat tabel absensi siap
        $absensiPct = null;

        // Tugas mendatang — TODO: isi saat tabel tugas siap
        $tugasMendatang = collect();

        // Pengumuman KP
        $kpPengumuman = KpPengumuman::where('is_active', true)
            ->where('tipe', 'pengumuman')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($p) {
                return (object)[
                    'judul' => $p->judul,
                    'konten' => $p->konten,
                    'date' => $p->created_at?->diffForHumans(),
                    'url' => route('eoffice.kp.mahasiswa.pengumuman'),
                    'ts' => $p->created_at?->timestamp ?? 0,
                ];
            });

        // Pengumuman Manajemen Praktikum
        $praktikumIds = $praktikumAktif ? [$praktikumAktif->id] : [];
        $mpPengumuman = \Modules\EOffice\Models\Pengumuman::with('praktikum')
            ->where('is_published', true)
            ->where(function($q) use ($praktikumIds) {
                $q->whereIn('tipe_sistem', ['buka', 'tutup']);
                if (!empty($praktikumIds)) {
                    $q->orWhereIn('praktikum_id', $praktikumIds);
                }
            })
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function($p) {
                $url = null;
                if ($p->tipe_sistem === 'buka') {
                    $url = route('eoffice.manprak.mahasiswa.daftar-asprak.index', ['praktikum_id' => $p->praktikum_id]);
                } else {
                    $url = route('eoffice.manprak.mahasiswa.pengumuman.index', ['praktikum_id' => $p->praktikum_id]);
                }
                return (object)[
                    'judul' => $p->judul,
                    'konten' => $p->konten,
                    'date' => $p->created_at?->diffForHumans(),
                    'url' => $url,
                    'ts' => $p->created_at?->timestamp ?? 0,
                ];
            });

        // Gabungkan pengumuman KP dan MP, urutkan berdasarkan waktu terbaru
        $pengumuman = $kpPengumuman->concat($mpPengumuman)
            ->sortByDesc('ts')
            ->take(5)
            ->values();

        // Timeline KP
        $timelineKp = KpPengumuman::where('is_active', true)
            ->where('tipe', 'timeline')
            ->orderBy('created_at', 'asc')
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
            'timelineKp'     => $timelineKp,
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
    | Template Dokumen (Admin)
    |--------------------------------------------------------------------------
    */

    public function templateProposal()
    {
        $this->authorize('eoffice.edit');
        
        $templateContent = '';
        if (Storage::disk('public')->exists('templates/proposal_kp.html')) {
            $templateContent = Storage::disk('public')->get('templates/proposal_kp.html');
        } else {
            // Default HTML
            $templateContent = '
            <div style="text-align: center;">
                <h2><strong>PROPOSAL KERJA PRAKTEK</strong></h2>
                <h2><strong>UNIVERSITAS DIPONEGORO</strong></h2>
                <h2><strong>SEMARANG</strong></h2>
                <p><br></p><p><br></p><p><br></p>
                <p>[ LOGO UNDIP ]</p>
                <p><br></p><p><br></p><p><br></p>
                <p>Oleh :</p>
                <p>&lt;Nama Mahasiswa&gt;</p>
                <p>&lt;NIM&gt;</p>
                <p><br></p><p><br></p><p><br></p>
                <p><strong>DEPARTEMEN TEKNIK KOMPUTER</strong></p>
                <p><strong>FAKULTAS TEKNIK</strong></p>
                <p><strong>UNIVERSITAS DIPONEGORO SEMARANG</strong></p>
                <p><strong>&lt;TAHUN PENGAJUAN&gt;</strong></p>
            </div>
            <p><br></p>
            <hr>
            <div style="text-align: center;">
                <h2><strong>LEMBAR PENGESAHAN</strong></h2>
            </div>
            <table style="width: 100%; border: none;">
                <tbody>
                    <tr>
                        <td style="width: 50%; text-align: left; padding: 20px;">Pembimbing Kerja Praktek,<br><br><br><br>&lt;Nama Pembimbing KP&gt;<br>NIP. &lt;NIP&gt;</td>
                        <td style="width: 50%; text-align: left; padding: 20px;">Mahasiswa Kerja Praktek,<br><br><br><br>&lt;Nama Mahasiswa KP&gt;<br>NIM. &lt;NIM&gt;</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: left; padding: 20px;">Koordinator Kerja Praktek,<br><br><br><br>&lt;Nama Koordinator KP&gt;<br>NIP. &lt;NIP&gt;</td>
                        <td style="width: 50%; text-align: left; padding: 20px;">Ketua Departemen Teknik Komputer,<br><br><br><br>&lt;Nama Ketua Departemen&gt;<br>NIP. &lt;NIP&gt;</td>
                    </tr>
                </tbody>
            </table>
            <p><br></p>
            <hr>
            <h2>1. Latar Belakang</h2><p><br></p>
            <h2>2. Rumusan Masalah</h2><p><br></p>
            <h2>3. Batasan Masalah</h2><p><br></p>
            <h2>4. Tujuan Kerja Praktek</h2><p><br></p>
            <h2>5. Bentuk Kegiatan</h2><p><br></p>
            <h2>6. Tempat dan Waktu Pelaksanaan</h2><p><br></p>
            <h2>7. Penutup</h2><p><br></p>
            ';
        }

        return view('eoffice::dashboard.admin.template_proposal', compact('templateContent'));
    }

    public function storeTemplateProposal(Request $request)
    {
        $this->authorize('eoffice.edit');
        
        $request->validate([
            'content' => 'required|string',
        ]);

        Storage::disk('public')->put('templates/proposal_kp.html', $request->input('content'));

        return redirect()->route('eoffice.admin.template_proposal')->with('success', 'Template Proposal berhasil disimpan!');
    }

    public function kelolaRole()
    {
        $this->authorize('eoffice.edit');
        return view('eoffice::dashboard.admin.kelola_role');
    }

    public function validasiTimeline()
    {
        $this->authorize('eoffice.edit');
        return view('eoffice::dashboard.admin.validasi_timeline');
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

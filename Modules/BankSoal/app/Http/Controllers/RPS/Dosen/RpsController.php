<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Dosen;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * [Dosen - RPS] Controller untuk manajemen RPS tingkat Dosen
 * 
 * Role: Dosen
 * Fitur: RPS (Rencana Pembelajaran Semester)
 * 
 * Dosen dapat membuat, mengedit, dan mengunggah RPS untuk mata kuliah yang diampu.
 */
class RpsController extends Controller
{
    // -----------------------------------------------------------------------
    // Page
    // -----------------------------------------------------------------------

    public function index(): \Illuminate\View\View
    {
        $user = Auth::user();

        // Dummy data - mata kuliah
        $mataKuliahs = collect([
            (object)['id' => 1, 'kode' => 'MK001', 'nama' => 'Algoritma dan Struktur Data'],
            (object)['id' => 2, 'kode' => 'MK002', 'nama' => 'Pemrograman Web'],
            (object)['id' => 3, 'kode' => 'MK003', 'nama' => 'Basis Data'],
            (object)['id' => 4, 'kode' => 'MK004', 'nama' => 'Sistem Operasi'],
        ]);

        // Dummy data - riwayat RPS (submitted data history)
        $riwayat = collect([
            (object)[
                'id' => 1,
                'mk_id' => 1,
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2025/2026',
                'created_at' => now()->subDays(5),
                'mataKuliah' => (object)['kode' => 'MK001', 'nama' => 'Algoritma dan Struktur Data']
            ],
            (object)[
                'id' => 2,
                'mk_id' => 2,
                'semester' => 'Genap',
                'tahun_ajaran' => '2024/2025',
                'created_at' => now()->subDays(15),
                'mataKuliah' => (object)['kode' => 'MK002', 'nama' => 'Pemrograman Web']
            ],
        ]);

        $currentYear  = (int) now()->format('Y');
        $tahunAjarans = [
            ($currentYear - 1) . '/' . $currentYear,
            $currentYear . '/' . ($currentYear + 1),
            ($currentYear + 1) . '/' . ($currentYear + 2),
        ];

        $semester     = now()->month >= 7 ? 'Ganjil' : 'Genap';
        $academicYear = $semester === 'Ganjil'
            ? $currentYear . '/' . ($currentYear + 1)
            : ($currentYear - 1) . '/' . $currentYear;

        $rpsUploaded = $riwayat->isNotEmpty();

        return view('banksoal::pages.rps.dosen.index', compact(
            'mataKuliahs',
            'riwayat',
            'tahunAjarans',
            'semester',
            'academicYear',
            'rpsUploaded',
        ));
    }

    // -----------------------------------------------------------------------
    // Store
    // -----------------------------------------------------------------------

    public function store(Request $request): RedirectResponse
    {
        // Dummy validation - UI only
        $validated = $request->validate([
            'mata_kuliah_id' => ['required', 'integer'],
            'dosen_lain'     => ['nullable', 'array'],
            'semester'       => ['required', 'in:Ganjil,Genap'],
            'tahun_ajaran'   => ['required', 'string'],
            'cpl_ids'        => ['required', 'array'],
            'cpl_ids.*'      => ['integer'],
            'cpmk_ids'       => ['required', 'array'],
            'cpmk_ids.*'     => ['integer'],
            'dokumen_rps'    => ['required', 'file', 'mimes:pdf,docx'],
        ]);

        // Dummy submission - tidak menyimpan ke DB
        // Hanya tunjukkan success message
        return redirect()->route('banksoal.rps.dosen.index')
            ->with('success', 'RPS berhasil disimpan dan menunggu verifikasi.');
    }

    public function getCplByMk(int $mkId): JsonResponse
    {
        // Dummy data - CPL based on MataKuliah ID
        $cplsByMk = [
            1 => [
                (object)['id' => 1, 'kode' => 'CPL001', 'deskripsi' => 'Memahami konsep dasar algoritma'],
                (object)['id' => 2, 'kode' => 'CPL002', 'deskripsi' => 'Mengimplementasikan struktur data'],
                (object)['id' => 3, 'kode' => 'CPL003', 'deskripsi' => 'Menganalisis kompleksitas algoritma'],
            ],
            2 => [
                (object)['id' => 4, 'kode' => 'CPL004', 'deskripsi' => 'Memahami arsitektur web'],
                (object)['id' => 5, 'kode' => 'CPL005', 'deskripsi' => 'Mengimplementasikan REST API'],
            ],
            3 => [
                (object)['id' => 6, 'kode' => 'CPL006', 'deskripsi' => 'Merancang database relasional'],
                (object)['id' => 7, 'kode' => 'CPL007', 'deskripsi' => 'Membuat queries SQL kompleks'],
            ],
            4 => [
                (object)['id' => 8, 'kode' => 'CPL008', 'deskripsi' => 'Memahami manajemen proses'],
                (object)['id' => 9, 'kode' => 'CPL009', 'deskripsi' => 'Mengoptimalkan kinerja sistem'],
            ],
        ];

        $cpls = $cplsByMk[$mkId] ?? [];
        return response()->json($cpls);
    }

    public function getCpmkByCpl(Request $request, int $mkId): JsonResponse
    {
        $cplIds = (array) $request->query('cpl_ids', []);

        if (empty($cplIds)) {
            return response()->json([]);
        }

        // Dummy data - CPMK based on selected CPL IDs
        $cpmkByCpl = [
            1 => [
                (object)['id' => 1, 'kode' => 'CPMK-001', 'deskripsi' => 'CPMK 1'],
                (object)['id' => 2, 'kode' => 'CPMK-002', 'deskripsi' => 'CPMK 2'],
            ],
            2 => [
                (object)['id' => 3, 'kode' => 'CPMK-003', 'deskripsi' => 'CPMK 3'],
                (object)['id' => 4, 'kode' => 'CPMK-004', 'deskripsi' => 'CPMK 4'],
            ],
            3 => [
                (object)['id' => 5, 'kode' => 'CPMK-005', 'deskripsi' => 'CPMK 5'],
            ],
            4 => [
                (object)['id' => 6, 'kode' => 'CPMK-006', 'deskripsi' => 'CPMK 6'],
                (object)['id' => 7, 'kode' => 'CPMK-007', 'deskripsi' => 'CPMK 7'],
            ],
            5 => [
                (object)['id' => 8, 'kode' => 'CPMK-008', 'deskripsi' => 'CPMK 8'],
            ],
            6 => [
                (object)['id' => 9, 'kode' => 'CPMK-009', 'deskripsi' => 'CPMK 9'],
                (object)['id' => 10, 'kode' => 'CPMK-010', 'deskripsi' => 'CPMK 10'],
            ],
            7 => [
                (object)['id' => 11, 'kode' => 'CPMK-011', 'deskripsi' => 'CPMK 11'],
            ],
            8 => [
                (object)['id' => 12, 'kode' => 'CPMK-012', 'deskripsi' => 'CPMK 12'],
            ],
            9 => [
                (object)['id' => 13, 'kode' => 'CPMK-013', 'deskripsi' => 'CPMK 13'],
            ],
        ];

        $cpmks = [];
        foreach ($cplIds as $cplId) {
            if (isset($cpmkByCpl[$cplId])) {
                $cpmks = array_merge($cpmks, $cpmkByCpl[$cplId]);
            }
        }

        // Remove duplicates by ID
        $unique = [];
        foreach ($cpmks as $cpmk) {
            $unique[$cpmk->id] = $cpmk;
        }

        return response()->json(array_values($unique));
    }

    public function getDosenByMk(int $mkId): JsonResponse
    {
        // Dummy data - list of dosens
        $dosens = [
            (object)['id' => 2, 'name' => 'Dr. Budi Santoso'],
            (object)['id' => 3, 'name' => 'Ir. Siti Nurhaliza'],
            (object)['id' => 4, 'name' => 'Prof. Ahmad Wijaya'],
            (object)['id' => 5, 'name' => 'Dr. Rina Wijayanti'],
        ];

        return response()->json($dosens);
    }
}

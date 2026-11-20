<?php

namespace Modules\BankSoal\Http\Controllers\BS\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Cpmk;
use Modules\BankSoal\Support\MasterDataUpsert;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BankSoal\Exports\CplTemplateExport;
use Modules\BankSoal\Exports\CpmkTemplateExport;
use Modules\BankSoal\Imports\CplImport;
use Modules\BankSoal\Imports\CpmkImport;

class CplController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $this->authorize('banksoal.view');

        return view('banksoal::pages.admin.kontrol-umum.cpl-cpmk');
    }

    public function createCpl()
    {
        $this->authorize('banksoal.edit');

        return view('banksoal::pages.admin.kontrol-umum.cpl-create');
    }

    public function editCpl(int $id)
    {
        $this->authorize('banksoal.edit');

        $cpl = Cpl::findOrFail($id);

        return view('banksoal::pages.admin.kontrol-umum.cpl-edit', compact('cpl'));
    }

    public function listCpl(): JsonResponse
    {
        $this->authorize('banksoal.view');

        try {
            return response()->json([
                'success' => true,
                'data' => Cpl::query()->orderBy('kode')->get(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data CPL: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportCplTemplate()
    {
        $this->authorize('banksoal.view');
        return Excel::download(new CplTemplateExport, 'template_cpl.xlsx');
    }

    public function importCpl(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');
        $request->validate(['file' => 'required|mimes:xlsx,csv,xls|max:1024']);

        try {
            Excel::import(new CplImport, $request->file('file'));
            return response()->json(['success' => true, 'message' => 'Data CPL berhasil diimpor']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengimpor data CSV/Excel: ' . $e->getMessage()], 500);
        }
    }

    public function showCpl(int $id): JsonResponse
    {
        $this->authorize('banksoal.view');

        $item = Cpl::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'CPL tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function nextCplCode(): JsonResponse
    {
        $this->authorize('banksoal.view');

        return response()->json([
            'success' => true,
            'data' => ['kode' => $this->nextKode('bs_cpl', 'CPL')],
        ]);
    }

    /**
     * Simpan CPL baru.
     *
     * Identitas CPL = seluruh field (kode + deskripsi). Kalau sudah ada baris dengan
     * isi yang sama persis, baris itu yang ditampilkan (dipulihkan bila sebelumnya
     * dihapus); kalau ada satu field berbeda, baris baru ditulis.
     */
    public function storeCpl(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'regex:/^CPL-\\d+$/', 'max:50'],
            'deskripsi' => ['required', 'string'],
        ]);

        try {
            $kode = isset($validated['kode']) && $validated['kode'] !== ''
                ? $validated['kode']
                : $this->nextKode('bs_cpl', 'CPL');

            $result = DB::transaction(fn() => MasterDataUpsert::firstOrCreateIdentical(
                Cpl::class,
                ['kode' => $kode, 'deskripsi' => $validated['deskripsi']]
            ));

            if (!$result['created']) {
                return response()->json([
                    'success' => true,
                    'created' => false,
                    'message' => MasterDataUpsert::existingMessage('CPL', $result['restored']),
                    'data' => $result['record'],
                ]);
            }

            return response()->json([
                'success' => true,
                'created' => true,
                'message' => 'CPL berhasil ditambahkan',
                'data' => $result['record'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan CPL: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateCpl(Request $request, int $id): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $item = Cpl::query()->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'CPL tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'kode' => ['required', 'string', 'regex:/^CPL-\\d+$/', 'max:50'],
            'deskripsi' => ['required', 'string'],
        ]);

        try {
            Cpl::query()->whereKey($id)->update($validated);
            $updated = Cpl::query()->find($id);

            return response()->json([
                'success' => true,
                'message' => 'CPL berhasil diperbarui',
                'data' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui CPL: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyCpl(int $id): JsonResponse
    {
        $this->authorize('banksoal.delete');

        try {
            $item = Cpl::findOrFail($id);
            $item->delete();

            return response()->json(['success' => true, 'message' => 'CPL berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus CPL: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function nextKode(string $table, string $prefix): string
    {
        // Baris yang sudah di-soft delete diabaikan supaya kode baru tidak
        // menabrak data yang sedang disembunyikan.
        $codes = DB::table($table)
            ->where('kode', 'like', $prefix . '-%')
            ->whereNull('deleted_at')
            ->pluck('kode');

        $max = 0;
        foreach ($codes as $code) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '-(\\d+)$/', (string) $code, $m)) {
                $num = (int) $m[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }

        return sprintf('%s-%03d', $prefix, $max + 1);
    }
}

<?php

namespace Modules\BankSoal\Http\Controllers\BS\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\BankSoal\Models\MataKuliah;
use Modules\BankSoal\Services\MataKuliahService;

/**
 * [Admin] MataKuliahController - Manajemen Mata Kuliah
 *
 * Role: Admin Bank Soal
 * Fitur: CRUD Mata Kuliah (Create, Read, Update, Delete), Bulk Delete
 */
class MataKuliahController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private MataKuliahService $mataKuliahService)
    {
        $this->middleware(['auth']);
    }

    /**
     * Display list of Mata Kuliah
     */
    public function index(Request $request)
    {
        $this->authorize('banksoal.view');

        // Return JSON for AJAX requests (for client-side filtering)
        if ($request->wantsJson() || $request->ajax()) {
            $mataKuliah = $this->mataKuliahService->listAll();
            return response()->json([
                'success' => true,
                'data' => $mataKuliah
            ]);
        }

        // Return view for page load
        $mataKuliah = MataKuliah::orderBy('kode')->paginate(10);
        return view('banksoal::pages.admin.kontrol-umum.mata-kuliah', compact('mataKuliah'));
    }

    /**
     * Store new Mata Kuliah
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $validated = $request->validate(
            MataKuliah::validationRules(),
            MataKuliah::validationMessages()
        );

        try {
            $mataKuliah = $this->mataKuliahService->create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Mata Kuliah berhasil ditambahkan',
                'data' => $mataKuliah
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Mata Kuliah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific Mata Kuliah for editing
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('banksoal.view');

        $id = (int) $id;

        try {
            $mataKuliah = MataKuliah::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $mataKuliah
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mata Kuliah tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update Mata Kuliah
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $id = (int) $id;

        $validated = $request->validate(
            MataKuliah::validationRules($id),
            MataKuliah::validationMessages()
        );

        try {
            $mataKuliah = $this->mataKuliahService->update($id, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Mata Kuliah berhasil diperbarui',
                'data' => $mataKuliah
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Mata Kuliah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete single Mata Kuliah
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('banksoal.delete');

        $id = (int) $id;

        try {
            $this->mataKuliahService->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Mata Kuliah berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Mata Kuliah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Delete Mata Kuliah
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $this->authorize('banksoal.delete');

        $ids = $request->validate(['ids' => 'required|array|min:1', 'ids.*' => 'integer'])['ids'];

        try {
            MataKuliah::whereIn('id', $ids)->delete();

            // Flush cache
            \Illuminate\Support\Facades\Cache::forget('bs.mk.all');

            return response()->json([
                'success' => true,
                'message' => 'Mata Kuliah terpilih berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Mata Kuliah: ' . $e->getMessage()
            ], 500);
        }
    }
}

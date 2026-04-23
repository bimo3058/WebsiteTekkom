<?php

namespace Modules\BankSoal\Http\Controllers\BS\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Cpmk;
use Modules\BankSoal\Models\DosenPengampuMk;
use Modules\BankSoal\Models\MataKuliah;

class PemetaanController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $this->authorize('banksoal.view');

        return view('banksoal::pages.admin.kontrol-umum.pemetaan');
    }

    public function options(): JsonResponse
    {
        $this->authorize('banksoal.view');

        $dosen = DB::table('users')
            ->join('lecturers', 'lecturers.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'lecturers.employee_number')
            ->orderBy('users.name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'cpl' => Cpl::query()->orderBy('kode')->get(['id', 'kode']),
                'cpmk' => Cpmk::query()->orderBy('kode')->get(['id', 'kode']),
                'mata_kuliah' => MataKuliah::query()->orderBy('kode')->get(['id', 'kode', 'nama']),
                'dosen' => $dosen,
            ],
        ]);
    }

    public function listCpmkCpl(): JsonResponse
    {
        $this->authorize('banksoal.view');

        $allCpl = Cpl::query()
            ->orderBy('kode')
            ->get(['id', 'kode']);

        $rawRows = DB::table('bs_cpl as cpl')
            ->leftJoin('bs_cpl_cpmk as map', 'cpl.id', '=', 'map.cpl_id')
            ->leftJoin('bs_cpmk as cpmk', 'cpmk.id', '=', 'map.cpmk_id')
            ->select(
                'cpl.id as cpl_id',
                'map.cpmk_id',
                'cpl.kode as cpl_kode',
                'cpmk.kode as cpmk_kode'
            )
            ->orderBy('cpl.kode')
            ->orderBy('cpmk.kode')
            ->get();

        $groupedRows = $rawRows->groupBy('cpl_id');

        $rows = $allCpl
            ->map(function ($cpl) use ($groupedRows) {
                $group = $groupedRows->get($cpl->id, collect());

                return [
                    'cpl_id' => $cpl->id,
                    'cpl_kode' => $cpl->kode,
                    'cpmk_codes' => $group
                        ->filter(fn ($row) => !is_null($row->cpmk_id))
                        ->pluck('cpmk_kode')
                        ->values()
                        ->all(),
                    'cpmk_items' => $group
                        ->filter(fn ($row) => !is_null($row->cpmk_id))
                        ->map(fn ($row) => [
                            'cpmk_id' => $row->cpmk_id,
                            'cpmk_kode' => $row->cpmk_kode,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function listMkCpl(): JsonResponse
    {
        $this->authorize('banksoal.view');

        $allCpl = Cpl::query()
            ->orderBy('kode')
            ->get(['id', 'kode']);

        $rawRows = DB::table('bs_cpl as cpl')
            ->leftJoin('bs_mata_kuliah_cpl as map', 'cpl.id', '=', 'map.cpl_id')
            ->leftJoin('bs_mata_kuliah as mk', 'mk.id', '=', 'map.mk_id')
            ->select(
                'map.mk_id',
                'cpl.id as cpl_id',
                'mk.nama as mk_nama',
                'cpl.kode as cpl_kode'
            )
            ->orderBy('cpl.kode')
            ->orderBy('mk.nama')
            ->get();

        $groupedRows = $rawRows->groupBy('cpl_id');

        $rows = $allCpl
            ->map(function ($cpl) use ($groupedRows) {
                $group = $groupedRows->get($cpl->id, collect());

                return [
                    'cpl_id' => $cpl->id,
                    'cpl_kode' => $cpl->kode,
                    'mk_names' => $group
                        ->filter(fn ($row) => !is_null($row->mk_id))
                        ->pluck('mk_nama')
                        ->values()
                        ->all(),
                    'mk_items' => $group
                        ->filter(fn ($row) => !is_null($row->mk_id))
                        ->map(fn ($row) => [
                            'mk_id' => $row->mk_id,
                            'mk_nama' => $row->mk_nama,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function listDosenMk(): JsonResponse
    {
        $this->authorize('banksoal.view');

        $allMk = MataKuliah::query()
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama']);

        $rawRows = DB::table('bs_mata_kuliah as mk')
            ->leftJoin('bs_dosen_pengampu_mk as map', 'mk.id', '=', 'map.mk_id')
            ->leftJoin('users as u', 'u.id', '=', 'map.user_id')
            ->select(
                'map.id',
                'map.user_id',
                'mk.id as mk_id',
                'u.name as dosen_nama',
                'mk.kode as mk_kode',
                'mk.nama as mk_nama'
            )
            ->orderBy('mk.nama')
            ->orderBy('u.name')
            ->get();

        $groupedRows = $rawRows->groupBy('mk_id');

        $rows = $allMk
            ->map(function ($mk) use ($groupedRows) {
                $group = $groupedRows->get($mk->id, collect());

                return [
                    'mk_id' => $mk->id,
                    'mk_kode' => $mk->kode,
                    'mk_nama' => $mk->nama,
                    'dosen_names' => $group
                        ->filter(fn ($row) => !is_null($row->id))
                        ->pluck('dosen_nama')
                        ->values()
                        ->all(),
                    'dosen_items' => $group
                        ->filter(fn ($row) => !is_null($row->id))
                        ->map(fn ($row) => [
                            'id' => $row->id,
                            'user_id' => $row->user_id,
                            'dosen_nama' => $row->dosen_nama,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function storeCpmkCpl(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $validated = $request->validate([
            'cpl_id' => ['required', 'integer', 'exists:bs_cpl,id'],
            'cpmk_ids' => ['required', 'array', 'min:1'],
            'cpmk_ids.*' => ['required', 'integer', 'exists:bs_cpmk,id'],
        ]);

        try {
            $cpmkIds = collect($validated['cpmk_ids'])->map(fn ($id) => (int) $id)->unique()->values();

            $existingCpmkIds = DB::table('bs_cpl_cpmk')
                ->where('cpl_id', $validated['cpl_id'])
                ->whereIn('cpmk_id', $cpmkIds)
                ->pluck('cpmk_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $newRows = $cpmkIds
                ->reject(fn ($cpmkId) => in_array((int) $cpmkId, $existingCpmkIds, true))
                ->map(fn ($cpmkId) => [
                    'cpl_id' => $validated['cpl_id'],
                    'cpmk_id' => (int) $cpmkId,
                ])
                ->values()
                ->all();

            if (empty($newRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua CPMK yang dipilih sudah terpetakan',
                ], 422);
            }

            DB::table('bs_cpl_cpmk')->insert($newRows);

            return response()->json([
                'success' => true,
                'message' => 'Pemetaan CPMK ke CPL berhasil ditambahkan',
                'meta' => [
                    'added' => count($newRows),
                    'skipped' => count($cpmkIds) - count($newRows),
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan pemetaan CPMK ke CPL: ' . $e->getMessage()], 500);
        }
    }

    public function storeMkCpl(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $validated = $request->validate([
            'cpl_id' => ['required', 'integer', 'exists:bs_cpl,id'],
            'mk_ids' => ['required', 'array', 'min:1'],
            'mk_ids.*' => ['required', 'integer', 'exists:bs_mata_kuliah,id'],
        ]);

        try {
            $mkIds = collect($validated['mk_ids'])->map(fn ($id) => (int) $id)->unique()->values();

            $existingMkIds = DB::table('bs_mata_kuliah_cpl')
                ->where('cpl_id', $validated['cpl_id'])
                ->whereIn('mk_id', $mkIds)
                ->pluck('mk_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $newRows = $mkIds
                ->reject(fn ($mkId) => in_array((int) $mkId, $existingMkIds, true))
                ->map(fn ($mkId) => [
                    'mk_id' => (int) $mkId,
                    'cpl_id' => $validated['cpl_id'],
                ])
                ->values()
                ->all();

            if (empty($newRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua mata kuliah yang dipilih sudah terpetakan',
                ], 422);
            }

            DB::table('bs_mata_kuliah_cpl')->insert($newRows);

            return response()->json([
                'success' => true,
                'message' => 'Pemetaan MK ke CPL berhasil ditambahkan',
                'meta' => [
                    'added' => count($newRows),
                    'skipped' => count($mkIds) - count($newRows),
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan pemetaan MK ke CPL: ' . $e->getMessage()], 500);
        }
    }

    public function storeDosenMk(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::exists('lecturers', 'user_id'),
            ],
            'mk_ids' => ['required', 'array', 'min:1'],
            'mk_ids.*' => ['required', 'integer', 'exists:bs_mata_kuliah,id'],
        ]);

        try {
            $mkIds = collect($validated['mk_ids'])->map(fn ($id) => (int) $id)->unique()->values();

            $existingMkIds = DosenPengampuMk::query()
                ->where('user_id', $validated['user_id'])
                ->whereIn('mk_id', $mkIds)
                ->pluck('mk_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $newRows = $mkIds
                ->reject(fn ($mkId) => in_array((int) $mkId, $existingMkIds, true))
                ->map(fn ($mkId) => [
                    'user_id' => $validated['user_id'],
                    'mk_id' => (int) $mkId,
                    'is_rps' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->values()
                ->all();

            if (empty($newRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua mata kuliah yang dipilih sudah terpetakan untuk dosen ini',
                ], 422);
            }

            DB::table('bs_dosen_pengampu_mk')->insert($newRows);

            return response()->json([
                'success' => true,
                'message' => 'Pemetaan dosen ke MK berhasil ditambahkan',
                'meta' => [
                    'added' => count($newRows),
                    'skipped' => count($mkIds) - count($newRows),
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan pemetaan dosen ke MK: ' . $e->getMessage()], 500);
        }
    }

    public function destroyCpmkCpl(Request $request): JsonResponse
    {
        $this->authorize('banksoal.delete');

        $validated = $request->validate([
            'cpl_id' => ['required', 'integer'],
            'cpmk_id' => ['required', 'integer'],
        ]);

        $deleted = DB::table('bs_cpl_cpmk')
            ->where('cpl_id', $validated['cpl_id'])
            ->where('cpmk_id', $validated['cpmk_id'])
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Data pemetaan CPMK ke CPL tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Pemetaan CPMK ke CPL berhasil dihapus']);
    }

    public function destroyMkCpl(Request $request): JsonResponse
    {
        $this->authorize('banksoal.delete');

        $validated = $request->validate([
            'mk_id' => ['required', 'integer'],
            'cpl_id' => ['required', 'integer'],
        ]);

        $deleted = DB::table('bs_mata_kuliah_cpl')
            ->where('mk_id', $validated['mk_id'])
            ->where('cpl_id', $validated['cpl_id'])
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Data pemetaan MK ke CPL tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Pemetaan MK ke CPL berhasil dihapus']);
    }

    public function destroyDosenMk(int $id): JsonResponse
    {
        $this->authorize('banksoal.delete');

        $item = DosenPengampuMk::query()->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Data pemetaan dosen ke MK tidak ditemukan'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Pemetaan dosen ke MK berhasil dihapus']);
    }
}

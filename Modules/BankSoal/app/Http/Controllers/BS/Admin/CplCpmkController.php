<?php

namespace Modules\BankSoal\Http\Controllers\BS\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Cpmk;

class CplCpmkController extends Controller
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

    public function listCpl(): JsonResponse
    {
        $this->authorize('banksoal.view');

        return response()->json([
            'success' => true,
            'data' => Cpl::query()->orderBy('kode')->get(),
        ]);
    }

    public function listCpmk(): JsonResponse
    {
        $this->authorize('banksoal.view');

        return response()->json([
            'success' => true,
            'data' => Cpmk::query()->orderBy('kode')->get(),
        ]);
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

    public function showCpmk(int $id): JsonResponse
    {
        $this->authorize('banksoal.view');

        $item = Cpmk::find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'CPMK tidak ditemukan'], 404);
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

    public function nextCpmkCode(): JsonResponse
    {
        $this->authorize('banksoal.view');

        return response()->json([
            'success' => true,
            'data' => ['kode' => ''],
        ]);
    }

    public function storeCpl(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'regex:/^CPL-\\d+$/', 'max:50', 'unique:bs_cpl,kode'],
            'deskripsi' => ['required', 'string'],
        ]);

        try {
            $created = DB::transaction(function () use ($validated) {
                $payload = ['deskripsi' => $validated['deskripsi']];
                $payload['kode'] = $validated['kode'] ?? $this->nextKode('bs_cpl', 'CPL');

                return Cpl::create($payload);
            });

            return response()->json([
                'success' => true,
                'message' => 'CPL berhasil ditambahkan',
                'data' => $created,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan CPL: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeCpmk(Request $request): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'regex:/^(?:CPMK-)?\\d+\\.\\d+$/', 'max:50', 'unique:bs_cpmk,kode'],
            'deskripsi' => ['required', 'string'],
        ]);

        try {
            $created = DB::transaction(function () use ($validated) {
                $payload = ['deskripsi' => $validated['deskripsi']];
                $payload['kode'] = $validated['kode'] ?? $this->nextKode('bs_cpmk', 'CPMK');

                return Cpmk::create($payload);
            });

            return response()->json([
                'success' => true,
                'message' => 'CPMK berhasil ditambahkan',
                'data' => $created,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan CPMK: ' . $e->getMessage(),
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
            'kode' => ['required', 'string', 'regex:/^CPL-\\d+$/', 'max:50', 'unique:bs_cpl,kode,' . $id],
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

    public function updateCpmk(Request $request, int $id): JsonResponse
    {
        $this->authorize('banksoal.edit');

        $item = Cpmk::query()->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'CPMK tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'kode' => ['required', 'string', 'regex:/^(?:CPMK-)?\\d+\\.\\d+$/', 'max:50', 'unique:bs_cpmk,kode,' . $id],
            'deskripsi' => ['required', 'string'],
        ]);

        try {
            Cpmk::query()->whereKey($id)->update($validated);
            $updated = Cpmk::query()->find($id);

            return response()->json([
                'success' => true,
                'message' => 'CPMK berhasil diperbarui',
                'data' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui CPMK: ' . $e->getMessage(),
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

    public function destroyCpmk(int $id): JsonResponse
    {
        $this->authorize('banksoal.delete');

        try {
            $item = Cpmk::findOrFail($id);
            $item->delete();

            return response()->json(['success' => true, 'message' => 'CPMK berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus CPMK: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function nextKode(string $table, string $prefix): string
    {
        $codes = DB::table($table)
            ->where('kode', 'like', $prefix . '-%')
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

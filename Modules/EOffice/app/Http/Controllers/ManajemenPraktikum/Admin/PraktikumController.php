<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Http\Requests\ManajemenPraktikum\Admin\AssignKoorRequest;
use Modules\EOffice\Http\Requests\ManajemenPraktikum\Admin\StorePraktikumRequest;
use Modules\EOffice\Http\Requests\ManajemenPraktikum\Admin\UpdatePraktikumRequest;
use Modules\EOffice\Http\Resources\ManajemenPraktikum\Admin\PraktikumResource;
use Modules\EOffice\Models\Praktikum;

class PraktikumController extends Controller
{
    /**
     * GET /eoffice/manprak/admin/praktikum
     */
    public function index(Request $request)
    {
        $query = Praktikum::with(['dosen', 'koordinator'])
            ->withCount('daftarPraktikan')
            ->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        $praktikums = $query->paginate(15)->withQueryString();

        // Dosen list untuk dropdown di modal create
        $dosenList = \App\Models\Lecturer::with('user')
            ->get()
            ->map(fn($l) => (object)['id' => $l->user_id, 'name' => $l->user?->name ?? '—']);

        return view('eoffice::manajemen-praktikum.admin.praktikum', compact('praktikums', 'dosenList'));
    }

    /**
     * POST /api/eoffice/manprak/admin/praktikum
     */
    public function store(StorePraktikumRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Validasi Dosen — pakai User global + hasRole() dari superapp
        if (!empty($data['dosen_id'])) {
            $dosen = User::find($data['dosen_id']);
            if (!$dosen || !$dosen->hasRole('dosen')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User yang dipilih untuk dosen tidak valid atau tidak memiliki role dosen.',
                ], 422);
            }
        }

        // Validasi Koordinator
        if (!empty($data['koor_id'])) {
            $koor = User::find($data['koor_id']);
            if (!$koor || !$koor->hasRole('koor_prak')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User yang dipilih untuk koordinator tidak valid atau tidak memiliki role koor_prak.',
                ], 422);
            }
        }

        $praktikum = Praktikum::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Praktikum berhasil dibuat.',
            'data'    => new PraktikumResource($praktikum->load(['dosen', 'koordinator'])),
        ], 201);
    }

    /**
     * GET /api/eoffice/manprak/admin/praktikum/{id}
     */
    public function show(string $id): JsonResponse
    {
        $praktikum = Praktikum::with(['dosen', 'koordinator'])->find($id);

        if (!$praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new PraktikumResource($praktikum),
        ]);
    }

    /**
     * PUT /api/eoffice/manprak/admin/praktikum/{id}
     */
    public function update(UpdatePraktikumRequest $request, string $id): JsonResponse
    {
        $praktikum = Praktikum::find($id);

        if (!$praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum tidak ditemukan.',
            ], 404);
        }

        $data = $request->validated();

        if (array_key_exists('dosen_id', $data) && !empty($data['dosen_id'])) {
            $dosen = User::find($data['dosen_id']);
            if (!$dosen || !$dosen->hasRole('dosen')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User yang dipilih untuk dosen tidak valid atau tidak memiliki role dosen.',
                ], 422);
            }
        }

        if (array_key_exists('koor_id', $data) && !empty($data['koor_id'])) {
            $koor = User::find($data['koor_id']);
            if (!$koor || !$koor->hasRole('koor_prak')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User yang dipilih untuk koordinator tidak valid atau tidak memiliki role koor_prak.',
                ], 422);
            }
        }

        $praktikum->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Praktikum berhasil diperbarui.',
            'data'    => new PraktikumResource($praktikum->load(['dosen', 'koordinator'])),
        ]);
    }

    /**
     * DELETE /api/eoffice/manprak/admin/praktikum/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $praktikum = Praktikum::find($id);

        if (!$praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum tidak ditemukan.',
            ], 404);
        }

        $praktikum->delete();

        return response()->json([
            'success' => true,
            'message' => 'Praktikum berhasil dihapus.',
        ]);
    }

    /**
     * PUT /api/eoffice/manprak/admin/praktikum/{id}/assign-koor
     */
    public function assignKoor(AssignKoorRequest $request, string $id): JsonResponse
    {
        $praktikum = Praktikum::find($id);

        if (!$praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum tidak ditemukan.',
            ], 404);
        }

        $userId = $request->validated()['user_id'];
        $user   = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.',
            ], 404);
        }

        // Assign role koor_prak via sistem permission global superapp
        if (!$user->hasRole('koor_prak')) {
            $roleKoor = \App\Models\Role::where('name', 'koor_prak')
                ->where('module', 'eoffice')
                ->first();

            if ($roleKoor) {
                $user->roles()->syncWithoutDetaching([$roleKoor->id]);
                $user->syncPermissionsFromRoles();
                $user->clearUserCache();
            }
        }

        $praktikum->update(['koor_id' => $userId]);

        return response()->json([
            'success' => true,
            'message' => 'Koordinator berhasil diassign ke Praktikum.',
            'data'    => new PraktikumResource($praktikum->load(['dosen', 'koordinator'])),
        ]);
    }
}
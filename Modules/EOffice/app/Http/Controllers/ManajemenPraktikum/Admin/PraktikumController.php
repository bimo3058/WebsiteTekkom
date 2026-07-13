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
use Modules\EOffice\Models\MatkulPraktikum;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;

class PraktikumController extends Controller
{
    public function __construct(protected KoorPraktikumService $koorService) {}

    /**
     * GET /eoffice/manprak/admin/praktikum
     */
    public function index(Request $request)
    {
        $query = Praktikum::with(['dosens', 'koordinator', 'matkul'])
            ->withCount('daftarPraktikan')
            ->orderBy('status', 'asc') // 'aktif' di atas 'nonaktif'
            ->orderBy('tahun_ajaran', 'desc') // tahun paling baru ke paling lama
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

        if ($tahunAjaran = $request->input('tahun_ajaran')) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        $perPage = $request->input('per_page', 10);
        $praktikums = $query->paginate($perPage)->withQueryString()->fragment('daftar-praktikum');

        // Dosen list untuk dropdown di modal create
        // Filter: hanya tampilkan dosen dengan domain @undip.ac.id (bukan akun seeder @sicata.com)
        $dosenList = \App\Models\Lecturer::with('user')
            ->get()
            ->filter(fn($l) => $l->user && !str_contains($l->user->email ?? '', 'sicata.com'))
            ->map(fn($l) => (object)[
                'id'    => $l->user_id,
                'name'  => ($l->user->name ?? '—') . ' · ' . ($l->user->email ?? ''),
            ])
            ->values();

        // Matkul list untuk dropdown pilih mata kuliah praktikum
        $matkulList = MatkulPraktikum::orderBy('semester')->orderBy('kode')->get();

        return view('eoffice::manajemen-praktikum.admin.praktikum', compact('praktikums', 'dosenList', 'matkulList'));
    }

    /**
     * POST /eoffice/manprak/admin/praktikum
     * Form submit biasa — return redirect, bukan JSON.
     */
    public function store(StorePraktikumRequest $request)
    {
        $data = $request->validated();

        // Validasi dosen-dosen (skip jika tidak ada yang sesuai role)
        $validDosenIds = [];
        if (!empty($data['dosen_ids'])) {
            foreach ($data['dosen_ids'] as $dosenId) {
                if ($dosenId) {
                    $dosen = User::find($dosenId);
                    if ($dosen && $dosen->hasRole('dosen')) {
                        $validDosenIds[] = $dosenId;
                    }
                }
            }
            if (empty($validDosenIds)) {
                return back()->withInput()->with('error', 'Semua dosen yang dipilih tidak valid atau tidak memiliki role dosen.');
            }
        }

        unset($data['dosen_ids']);
        if (isset($data['status'])) {
            $data['is_active'] = $data['status'] === 'aktif';
        }
        $praktikum = Praktikum::create($data);

        // Sync dosen
        if (!empty($validDosenIds)) {
            $praktikum->dosens()->sync($validDosenIds);
        }

        return redirect()
            ->route('eoffice.manprak.admin.praktikum.index')
            ->with('success', 'Praktikum berhasil ditambahkan.');
    }

    /**
     * GET /api/eoffice/manprak/admin/praktikum/{id}
     */
    public function show(string $id): JsonResponse
    {
        $praktikum = Praktikum::with(['dosens', 'koordinator'])->find($id);

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
    public function update(UpdatePraktikumRequest $request, string $id)
    {
        $praktikum = Praktikum::find($id);

        if (!$praktikum) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Praktikum tidak ditemukan.',
                ], 404);
            }
            return redirect()->route('eoffice.manprak.admin.praktikum.index')->with('error', 'Praktikum tidak ditemukan.');
        }

        $data = $request->validated();

        if (array_key_exists('dosen_ids', $data) && !empty($data['dosen_ids'])) {
            $validDosenIds = [];
            foreach ($data['dosen_ids'] as $dosenId) {
                if ($dosenId) {
                    $dosen = User::find($dosenId);
                    if ($dosen && $dosen->hasRole('dosen')) {
                        $validDosenIds[] = $dosenId;
                    }
                }
            }
            if (empty($validDosenIds)) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User yang dipilih untuk dosen tidak valid atau tidak memiliki role dosen.',
                    ], 422);
                }
                return back()->withInput()->with('error', 'User yang dipilih untuk dosen tidak valid atau tidak memiliki role dosen.');
            }
            // Sync dosen
            $praktikum->dosens()->sync($validDosenIds);
        }

        if (array_key_exists('koor_id', $data) && !empty($data['koor_id'])) {
            $koor = User::find($data['koor_id']);
            if (!$koor || !$koor->hasRole('koor_prak')) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User yang dipilih untuk koordinator tidak valid atau tidak memiliki role koor_prak.',
                    ], 422);
                }
                return back()->withInput()->with('error', 'User yang dipilih untuk koordinator tidak valid atau tidak memiliki role koor_prak.');
            }
        }

        unset($data['dosen_ids']);
        if (isset($data['status'])) {
            $data['is_active'] = $data['status'] === 'aktif';
        }
        $praktikum->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Praktikum berhasil diperbarui.',
                'data'    => new PraktikumResource($praktikum->load(['dosens', 'koordinator'])),
            ]);
        }

        return redirect()
            ->route('eoffice.manprak.admin.praktikum.index')
            ->with('success', 'Praktikum berhasil diperbarui.');
    }



    /**
     * DELETE /api/eoffice/manprak/admin/praktikum/{id}
     */
    public function destroy(Request $request, string $id)
    {
        $praktikum = Praktikum::find($id);

        if (!$praktikum) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Praktikum tidak ditemukan.',
                ], 404);
            }
            return redirect()->route('eoffice.manprak.admin.praktikum.index')->with('error', 'Praktikum tidak ditemukan.');
        }

        $praktikum->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Praktikum berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('eoffice.manprak.admin.praktikum.index')
            ->with('success', 'Praktikum berhasil dihapus.');
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

        $this->koorService->assign($praktikum, $user);

        return response()->json([
            'success' => true,
            'message' => 'Koordinator berhasil diassign ke Praktikum dan otomatis aktif sebagai asprak.',
            'data'    => new PraktikumResource($praktikum->load(['dosens', 'koordinator'])),
        ]);
    }

    /**
     * PATCH /eoffice/manprak/admin/praktikum/{id}/toggle-active
     */
    public function toggleActive(string $id)
    {
        $praktikum = Praktikum::findOrFail($id);
        $praktikum->is_active = !$praktikum->is_active;
        $praktikum->status = $praktikum->is_active ? 'aktif' : 'nonaktif';
        $praktikum->save();

        $statusText = $praktikum->is_active ? 'diaktifkan' : 'ditutup (diarsipkan)';
        return back()->with('success', "Praktikum {$praktikum->nama} berhasil {$statusText}.");
    }

    /**
     * POST /eoffice/manprak/admin/praktikum/bulk-toggle-active
     */
    public function bulkToggleActive(Request $request)
    {
        $request->validate([
            'praktikum_ids' => 'required|array',
            'praktikum_ids.*' => 'exists:eo_praktikum,id',
            'action' => 'required|in:activate,deactivate'
        ]);

        $isActive = $request->action === 'activate' ? true : false;
        $status = $isActive ? 'aktif' : 'nonaktif';
        
        Praktikum::whereIn('id', $request->praktikum_ids)->update([
            'is_active' => $isActive,
            'status' => $status
        ]);

        $statusText = $isActive ? 'diaktifkan kembali' : 'ditutup (diarsipkan)';
        return back()->with('success', "Berhasil! " . count($request->praktikum_ids) . " praktikum telah {$statusText}.");
    }

}
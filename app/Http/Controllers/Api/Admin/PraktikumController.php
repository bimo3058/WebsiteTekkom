<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePraktikumRequest;
use App\Http\Requests\Admin\UpdatePraktikumRequest;
use App\Http\Resources\Admin\PraktikumResource;
use App\Models\Pengguna;
use App\Models\Praktikum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PraktikumController extends Controller
{
    /**
     * GET /api/admin/praktikum
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 10), 50);

        $query = Praktikum::with(['dosen', 'koor'])->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($tahunAjaran = $request->input('tahun_ajaran')) {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        $praktikums = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => PraktikumResource::collection($praktikums),
            'pagination' => [
                'current_page' => $praktikums->currentPage(),
                'per_page'     => $praktikums->perPage(),
                'total'        => $praktikums->total(),
                'last_page'    => $praktikums->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/admin/praktikum
     */
    public function store(StorePraktikumRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Validasi Dosen (pastikan user punya role dosen)
        if (!empty($data['dosen_id'])) {
            $dosen = Pengguna::find($data['dosen_id']);
            if (!$dosen || !$dosen->hasRole('dosen')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna yang dipilih untuk dosen tidak valid atau tidak memiliki role dosen.',
                ], 422);
            }
        }

        // Validasi Koordinator (pastikan user punya role koor_prak)
        if (!empty($data['koor_id'])) {
            $koor = Pengguna::find($data['koor_id']);
            if (!$koor || !$koor->hasRole('koor_prak')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna yang dipilih untuk koordinator tidak valid atau tidak memiliki role koor_prak.',
                ], 422);
            }
        }

        $praktikum = Praktikum::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Praktikum berhasil dibuat.',
            'data'    => new PraktikumResource($praktikum->load(['dosen', 'koor'])),
        ], 201);
    }

    /**
     * GET /api/admin/praktikum/{id}
     */
    public function show(string $id): JsonResponse
    {
        $praktikum = Praktikum::with(['dosen', 'koor'])->find($id);

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
     * PUT /api/admin/praktikum/{id}
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

        // Validasi Dosen
        if (array_key_exists('dosen_id', $data) && !empty($data['dosen_id'])) {
            $dosen = Pengguna::find($data['dosen_id']);
            if (!$dosen || !$dosen->hasRole('dosen')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna yang dipilih untuk dosen tidak valid atau tidak memiliki role dosen.',
                ], 422);
            }
        }

        // Validasi Koordinator
        if (array_key_exists('koor_id', $data) && !empty($data['koor_id'])) {
            $koor = Pengguna::find($data['koor_id']);
            if (!$koor || !$koor->hasRole('koor_prak')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna yang dipilih untuk koordinator tidak valid atau tidak memiliki role koor_prak.',
                ], 422);
            }
        }

        $praktikum->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Praktikum berhasil diperbarui.',
            'data'    => new PraktikumResource($praktikum->load(['dosen', 'koor'])),
        ]);
    }

    /**
     * DELETE /api/admin/praktikum/{id}
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

        $praktikum->delete(); // Soft delete, trigger logs_activity otomatis

        return response()->json([
            'success' => true,
            'message' => 'Praktikum berhasil dihapus.',
        ], 200);
    }

    /**
     * PUT /api/admin/praktikum/{id}/assign-koor
     */
    public function assignKoor(\App\Http\Requests\Admin\AssignKoorRequest $request, string $id): JsonResponse
    {
        if (!\Illuminate\Support\Str::isUuid($id)) {
            return response()->json(['success' => false, 'message' => 'Praktikum tidak ditemukan.'], 404);
        }
        $praktikum = Praktikum::find($id);

        if (!$praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum tidak ditemukan.',
            ], 404);
        }

        $data = $request->validated();
        $penggunaId = $data['pengguna_id'];

        // Validasi apakah pengguna punya role koor_prak
        $pengguna = Pengguna::find($penggunaId);
        
        if (!$pengguna->hasRole('koor_prak')) {
            // Auto-assign role koor_prak ke pengguna ini
            $roleKoor = \App\Models\SystemRole::where('nama', 'koor_prak')->first();
            if ($roleKoor) {
                $pengguna->roles()->syncWithoutDetaching([
                    $roleKoor->id => ['status' => 'aktif']
                ]);
            }
        }

        $praktikum->update([
            'koor_id' => $penggunaId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Koordinator berhasil diassign ke Praktikum.',
            'data'    => new PraktikumResource($praktikum->load(['dosen', 'koor'])),
        ]);
    }

    /**
     * GET /api/admin/praktikum/{id}/praktikan
     */
    public function getPraktikans(string $id): JsonResponse
    {
        $praktikum = Praktikum::with('praktikans')->find($id);

        if (!$praktikum) {
            return response()->json(['success' => false, 'message' => 'Praktikum tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => \App\Http\Resources\Admin\PraktikanResource::collection($praktikum->praktikans),
        ]);
    }

    /**
     * POST /api/admin/praktikum/{id}/import-praktikan
     */
    public function uploadPraktikan(\App\Http\Requests\Admin\UploadPraktikanRequest $request, string $id): JsonResponse
    {
        $praktikum = Praktikum::find($id);
        if (!$praktikum) {
            return response()->json(['success' => false, 'message' => 'Praktikum tidak ditemukan.'], 404);
        }

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header
        $header = fgetcsv($handle, 1000, ',');
        
        $results = [
            'total'      => 0,
            'success'    => 0,
            'skipped'    => 0,
            'errors'     => []
        ];

        $authMethod = config('praktikum.auth_method', 'local');
        $roleMahasiswa = \App\Models\SystemRole::where('nama', 'mahasiswa')->first();

        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $results['total']++;
            
            if (count($data) < 2) {
                $results['errors'][] = "Baris {$results['total']}: Format data tidak lengkap (minimal NIM dan Nama).";
                continue;
            }

            $nim  = trim($data[0]);
            $nama = trim($data[1]);
            $email = isset($data[2]) ? trim($data[2]) : "{$nim}@student.undip.ac.id";

            // 1. Cari atau buat pengguna
            $pengguna = Pengguna::where('nim_nip', $nim)->first();

            if (!$pengguna) {
                if ($authMethod === 'sso') {
                    $results['skipped']++;
                    $results['errors'][] = "Baris {$results['total']}: Pengguna dengan NIM {$nim} tidak ditemukan di sistem (Mode SSO).";
                    continue;
                }

                // Mode Local: Auto-create
                try {
                    $pengguna = Pengguna::create([
                        'nama'          => $nama,
                        'email'         => $email,
                        'nim_nip'       => $nim,
                        'password_hash' => \Illuminate\Support\Facades\Hash::make($nim),
                        'status'        => 'aktif',
                    ]);

                    // Assign role mahasiswa
                    if ($roleMahasiswa) {
                        $pengguna->roles()->syncWithoutDetaching([
                            $roleMahasiswa->id => ['status' => 'aktif', 'dibuat_pada' => now()]
                        ]);
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = "Baris {$results['total']}: Gagal membuat akun ({$e->getMessage()}).";
                    continue;
                }
            }

            // 2. Daftarkan ke praktikum (DaftarPraktikan)
            try {
                // Gunakan syncWithoutDetaching untuk pivot table via relationship
                $praktikum->praktikans()->syncWithoutDetaching([
                    $pengguna->id => [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'status' => 'terdaftar',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Baris {$results['total']}: Gagal mendaftarkan NIM {$nim} ({$e->getMessage()}).";
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "Proses import selesai. Berhasil: {$results['success']}, Gagal/Skip: " . ($results['total'] - $results['success']),
            'data'    => $results
        ]);
    }
}

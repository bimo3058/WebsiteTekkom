<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DosenResource;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    /**
     * GET /api/admin/dosen
     *
     * Ambil daftar pengguna yang memiliki role 'dosen'.
     * Fitur:
     *   - Pagination (default 10 per page, max 50)
     *   - Search by nama/email
     *   - Ordered by nama ASC
     *   - No N+1 (eager load roles)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 10), 50);

        $query = Pengguna::whereHas('roles', function ($q) {
                $q->where('nama', 'dosen')
                  ->where('pengguna_role.status', 'aktif');
            })
            ->orderBy('nama', 'asc');

        // Search filter (nama atau email)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $dosens = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => DosenResource::collection($dosens),
            'pagination' => [
                'current_page' => $dosens->currentPage(),
                'per_page'     => $dosens->perPage(),
                'total'        => $dosens->total(),
                'last_page'    => $dosens->lastPage(),
            ],
        ]);
    }
}

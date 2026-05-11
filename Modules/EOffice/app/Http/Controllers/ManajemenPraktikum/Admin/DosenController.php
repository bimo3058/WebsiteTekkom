<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Http\Resources\ManajemenPraktikum\Admin\DosenResource;

class DosenController extends Controller
{
    /**
     * GET /api/eoffice/manprak/admin/dosen
     *
     * Ambil daftar user yang memiliki role 'dosen' di module eoffice.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 10), 50);

        // Pakai sistem role global superapp: Role.module = 'eoffice', Role.name = 'dosen'
        $query = User::whereHas('roles', function ($q) {
                $q->where('name', 'dosen')
                  ->where('module', 'eoffice');
            })
            ->orderBy('name', 'asc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
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

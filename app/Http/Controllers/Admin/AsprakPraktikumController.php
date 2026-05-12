<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Praktikum;
use App\Models\AsprakPraktikum;
use App\Http\Requests\AssignAsprakRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AsprakPraktikumController extends Controller
{
    /**
     * GET /api/admin/praktikum/{praktikum_id}/asparaks
     */
    public function index(string $praktikum_id): JsonResponse
    {
        $praktikum = Praktikum::find($praktikum_id);

        if (!$praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum not found',
                'errors' => null
            ], 404);
        }

        // Fetch asparaks using Pivot model or Relationship
        $asparaks = AsprakPraktikum::with('user')
            ->where('praktikum_id', $praktikum_id)
            ->orderByRaw("CASE WHEN role = 'koor' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $data = \App\Http\Resources\AsprakPraktikumResource::collection($asparaks);

        return response()->json([
            'success' => true,
            'message' => 'List asprak praktikum retrieved successfully',
            'data' => $data
        ]);
    }

    /**
     * POST /api/admin/praktikum/{praktikum_id}/assign-asprak
     */
    public function store(AssignAsprakRequest $request, string $praktikum_id): JsonResponse
    {
        $praktikum = Praktikum::find($praktikum_id);

        if (!$praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum not found',
                'errors' => null
            ], 404);
        }

        $validated = $request->validated();

        // Check unique constraint
        $existing = AsprakPraktikum::withTrashed()
            ->where('praktikum_id', $praktikum_id)
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restore and update if previously soft-deleted
                $existing->restore();
                $existing->update([
                    'role' => $validated['role'],
                    'deskripsi' => $validated['deskripsi'] ?? null,
                ]);
                $record = $existing;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already assigned to this praktikum',
                    'errors' => null
                ], 409);
            }
        } else {
            $record = AsprakPraktikum::create([
                'praktikum_id' => $praktikum_id,
                'user_id' => $validated['user_id'],
                'role' => $validated['role'],
                'deskripsi' => $validated['deskripsi'] ?? null,
            ]);
        }

        $record->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Asprak assigned successfully',
            'data' => new \App\Http\Resources\AsprakPraktikumResource($record)
        ], 201);
    }

    /**
     * DELETE /api/admin/praktikum/{praktikum_id}/asprak/{asprak_id}
     */
    public function destroy(string $praktikum_id, string $asprak_id): JsonResponse
    {
        $record = AsprakPraktikum::where('praktikum_id', $praktikum_id)
            ->where('id', $asprak_id)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Asprak record not found',
                'errors' => null
            ], 404);
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asprak unassigned successfully',
            'data' => [
                'id' => $record->id,
                'user_id' => $record->user_id,
                'praktikum_id' => $record->praktikum_id,
                'unassigned_at' => now()->toIso8601String(),
            ]
        ]);
    }
}

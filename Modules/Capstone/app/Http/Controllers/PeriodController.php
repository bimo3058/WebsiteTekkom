<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Capstone\Http\Requests\Period\StorePeriodRequest;
use Modules\Capstone\Http\Requests\Period\UpdatePeriodRequest;
use Modules\Capstone\Models\Period;

class PeriodController extends Controller
{
    public function index()
    {
        return Period::orderBy('created_at', 'desc')->get();
    }

    public function show(Period $period)
    {
        return response()->json($period);
    }

    public function store(StorePeriodRequest $request)
    {
        $validated = $request->validated();

        // V4: Allow multiple active periods â€” no auto-deactivation

        $period = Period::create($validated);

        return response()->json($period->fresh(), 201);
    }

    public function update(UpdatePeriodRequest $request, Period $period)
    {
        $validated = $request->validated();

        // V4: Allow multiple active periods â€” no auto-deactivation

        $period->update($validated);

        return response()->json($period->fresh());
    }

    public function destroy(Period $period)
    {
        // Prevent deleting active period
        if ($period->is_active) {
            return response()->json(['message' => 'Cannot delete the active period. Deactivate it first.'], 400);
        }

        // Soft delete
        $period->delete();

        return response()->json(['message' => 'Period archived successfully.']);
    }
}

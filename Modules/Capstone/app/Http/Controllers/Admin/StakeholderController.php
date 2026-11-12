<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Http\Controllers\Controller;
use Modules\Capstone\Http\Requests\Admin\Stakeholder\AttachStakeholderRequest;
use Modules\Capstone\Http\Requests\Admin\Stakeholder\StoreStakeholderRequest;
use Modules\Capstone\Http\Requests\Admin\Stakeholder\UpdateStakeholderRequest;
use Modules\Capstone\Models\Stakeholder;
use Modules\Capstone\Models\Title;

class StakeholderController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Stakeholder::query();

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        return $this->successResponse($query->orderBy('name')->get());
    }

    public function store(StoreStakeholderRequest $request)
    {
        $validated = $request->validated();

        $stakeholder = Stakeholder::create($validated);

        return $this->createdResponse($stakeholder, 'Stakeholder created successfully.');
    }

    public function update(UpdateStakeholderRequest $request, Stakeholder $stakeholder)
    {
        $validated = $request->validated();

        $stakeholder->update($validated);

        return $this->successResponse($stakeholder, 'Stakeholder updated successfully.');
    }

    public function destroy(Stakeholder $stakeholder)
    {
        $stakeholder->delete();

        return $this->successResponse(null, 'Stakeholder deleted successfully.');
    }

    public function attachToTitle(AttachStakeholderRequest $request, Title $title)
    {
        $validated = $request->validated();

        // Stakeholders are governed for student-proposed ideas only.
        if ($title->title_source !== 'STUDENT') {
            return $this->errorResponse('Stakeholders can only be attached to student-proposed titles.', 422);
        }

        $title->stakeholders()->syncWithoutDetaching([
            $validated['stakeholder_id'] => [
                'role' => $validated['role'] ?? 'ADVISOR',
                'notes' => $validated['notes'] ?? null,
            ],
        ]);

        return $this->successResponse($title->load('stakeholders'), 'Stakeholder linked to title.');
    }

    public function detachFromTitle(Title $title, Stakeholder $stakeholder)
    {
        $title->stakeholders()->detach($stakeholder->id);

        return $this->successResponse($title->load('stakeholders'), 'Stakeholder unlinked from title.');
    }
}

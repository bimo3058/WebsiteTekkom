<?php

namespace Modules\Capstone\Http\Controllers;

use Modules\Capstone\Http\Requests\Assessment\StoreComponentTemplateRequest;
use Modules\Capstone\Http\Requests\Assessment\UpdateComponentTemplateRequest;
use Modules\Capstone\Models\AssessmentComponentTemplate;

class AssessmentComponentTemplateController extends Controller
{
    use ApiResponseTrait;

    /**
     * List all assessment component templates (bank soal).
     */
    public function index()
    {
        $templates = AssessmentComponentTemplate::query()
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        return $this->successResponse($templates);
    }

    /**
     * Create a new assessment component template.
     */
    public function store(StoreComponentTemplateRequest $request)
    {
        $data = $request->validated();

        $data['created_by'] = $request->user()->id;

        $template = AssessmentComponentTemplate::create($data);

        return $this->createdResponse($template);
    }

    /**
     * Get a single assessment component template.
     */
    public function show($id)
    {
        $template = AssessmentComponentTemplate::findOrFail($id);

        return $this->successResponse($template);
    }

    /**
     * Update an assessment component template.
     */
    public function update(UpdateComponentTemplateRequest $request, $id)
    {
        $template = AssessmentComponentTemplate::findOrFail($id);

        $data = $request->validated();

        $template->update($data);

        return $this->successResponse($template);
    }

    /**
     * Delete (soft delete by marking inactive) an assessment component template.
     */
    public function destroy($id)
    {
        $template = AssessmentComponentTemplate::findOrFail($id);

        // Check if template is used in any period
        $usageCount = $template->periodComponents()->count();
        if ($usageCount > 0) {
            // Soft delete by marking inactive
            $template->update(['is_active' => false]);

            return $this->successResponse(null, 'Template marked as inactive (has existing usage in '.$usageCount.' period configurations)');
        }

        $template->delete();

        return $this->successResponse(null, 'Template deleted');
    }

    /**
     * Check if period setup (templates) exist for the period wizard.
     */
    public function check()
    {
        $count = AssessmentComponentTemplate::where('is_active', true)->count();

        return $this->successResponse([
            'hasTemplates' => $count > 0,
            'message' => $count > 0
                ? "Tersedia {$count} komponen asesment"
                : 'Belum ada komponen asesment. Silakan buat terlebih dahulu di Bank Soal.',
        ]);
    }
}

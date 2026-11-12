<?php

namespace Modules\Capstone\Http\Controllers;

use Modules\Capstone\Http\Requests\Assessment\StoreIndicatorTemplateRequest;
use Modules\Capstone\Http\Requests\Assessment\UpdateIndicatorTemplateRequest;
use Modules\Capstone\Models\PeerReviewIndicatorTemplate;

class PeerReviewIndicatorTemplateController extends Controller
{
    use ApiResponseTrait;

    /**
     * List all peer review indicator templates (bank soal).
     */
    public function index()
    {
        $templates = PeerReviewIndicatorTemplate::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse($templates);
    }

    /**
     * Create a new peer review indicator template.
     */
    public function store(StoreIndicatorTemplateRequest $request)
    {
        $data = $request->validated();

        $data['created_by'] = $request->user()->id;

        $template = PeerReviewIndicatorTemplate::create($data);

        return $this->createdResponse($template);
    }

    /**
     * Update a peer review indicator template.
     */
    public function update(UpdateIndicatorTemplateRequest $request, $id)
    {
        $template = PeerReviewIndicatorTemplate::findOrFail($id);

        $data = $request->validated();

        $template->update($data);

        return $this->successResponse($template);
    }

    /**
     * Delete (soft delete by marking inactive) a peer review indicator template.
     */
    public function destroy($id)
    {
        $template = PeerReviewIndicatorTemplate::findOrFail($id);

        // Check if template is used in any period
        $usageCount = $template->periodIndicators()->count();
        if ($usageCount > 0) {
            // Soft delete by marking inactive
            $template->update(['is_active' => false]);

            return $this->successResponse(null, 'Template marked as inactive (has existing usage in '.$usageCount.' period configurations)');
        }

        $template->delete();

        return $this->successResponse(null, 'Template deleted');
    }
}

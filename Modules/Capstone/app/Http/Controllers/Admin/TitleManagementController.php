<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Title;

/**
 * Admin text-only title editor.
 *
 * Allows an admin to correct the display text of a research title
 * (title, description, problem_statement, scope, specializations)
 * WITHOUT affecting any group's status:
 * - never writes to capstone_groups (no status / title_id change),
 * - never calls GroupStateMachine,
 * - never touches quota / status / ownership columns.
 */
class TitleManagementController extends Controller
{
    public function update(Request $request, Title $title)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'problem_statement' => 'sometimes|nullable|string',
            'scope' => 'sometimes|nullable|string',
            'specializations' => 'sometimes|nullable|array|min:1',
            'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            // Governance columns: admin text edit must never change these.
            'status' => 'prohibited',
            'quota' => 'prohibited',
            'lecturer_id' => 'prohibited',
            'period_id' => 'prohibited',
            'title_source' => 'prohibited',
            'proposed_by_group_id' => 'prohibited',
            'proposed_supervisor_id' => 'prohibited',
            'supervisor_approval_status' => 'prohibited',
            'approved_by_admin' => 'prohibited',
            'is_reserved' => 'prohibited',
            'pre_assigned_group_id' => 'prohibited',
            'rejection_reason' => 'prohibited',
        ]);

        if (empty($validated)) {
            return response()->json(['message' => 'No editable field provided.'], 422);
        }

        $before = $title->only(['title', 'description', 'problem_statement', 'scope', 'specializations']);

        // Pure Title row update — no Group row is read for writing,
        // so Group.status / title_id cannot change here.
        $title->update($validated);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'TITLE_TEXT_EDITED_BY_ADMIN',
            'target_type' => Title::class,
            'target_id' => $title->id,
            'payload' => [
                'before' => $before,
                'after' => $title->fresh()->only(['title', 'description', 'problem_statement', 'scope', 'specializations']),
            ],
        ]);

        return response()->json($title->fresh()->load('lecturer'));
    }
}

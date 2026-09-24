<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Support\CapstoneActor;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = CapstoneActor::role(
            $user,
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        if ($role === 'dosen') {
            return Title::where('lecturer_id', CapstoneActor::lecturer($user)->id)
                ->with('lecturer')
                ->withCount([
                    'groups as active_groups_count' => function ($query) {
                        $query->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED']);
                    },
                ])
                ->get();
        }

        if ($role === 'mahasiswa') {
            PeriodRegistration::where('user_id', CapstoneActor::student($user)->id)->where('status', PeriodRegistration::STATUS_APPROVED)->value('period_id');

            // Dosen titles are cross-period: LECTURER titles ignore period_id entirely
            // and stay visible in every period while global quota remains.
            // Student ideas come from the period-scoped Bursa Ide endpoint.
            return Title::where('status', 'open')
                ->where('quota', '>', 0)
                ->where(function ($query) {
                    $query->where('title_source', 'LECTURER')
                        ->orWhereNull('title_source');
                })
                ->with('lecturer')
                ->withCount([
                    'groups as active_groups_count' => function ($query) {
                        $query->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED']);
                    },
                ])
                ->get()
                ->filter(function ($title) {
                    return $title->active_groups_count < $title->quota;
                })
                ->values();
        }

        return Title::with('lecturer')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'scope' => 'required|string',
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            'quota' => 'required|integer|min:1',
        ]);

        $title = Title::create([
            'lecturer_id' => CapstoneActor::lecturer($request->user())->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'problem_statement' => $validated['problem_statement'],
            'scope' => $validated['scope'],
            'specializations' => $validated['specializations'],
            'quota' => $validated['quota'],
            'status' => 'open',
            'title_source' => 'LECTURER',
            // Dosen titles are cross-period: never bind to a single period.
            'period_id' => null,
        ]);

        return response()->json($title, 201);
    }

    public function show(Request $request, Title $title)
    {
        $role = CapstoneActor::role(
            $request->user(),
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        if ($role === 'dosen') {
            abort_unless(
                $title->lecturer_id === CapstoneActor::lecturer($request->user())->id,
                403,
                'Judul ini bukan milik Anda.'
            );
        }

        return $title->load([
            'lecturer',
            'groups' => function ($q) {
                $q->where('status', '!=', 'REJECTED')->with('members.student');
            },
        ]);
    }

    public function update(Request $request, Title $title)
    {
        if (CapstoneActor::lecturer($request->user())->id !== $title->lecturer_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'problem_statement' => 'sometimes|string',
            'scope' => 'sometimes|string',
            'specializations' => 'sometimes|array|min:1',
            'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            'quota' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:open,closed',
            // Dosen titles stay cross-period: period_id can never be set via update.
            'period_id' => 'prohibited',
        ]);

        unset($validated['period_id']);
        // Guard legacy rows that may carry a period_id: keep them global.
        $title->forceFill(['period_id' => null])->save();
        $title->update($validated);

        return response()->json($title);
    }

    public function destroy(Request $request, Title $title)
    {
        if (CapstoneActor::lecturer($request->user())->id !== $title->lecturer_id) {
            abort(403, 'Unauthorized');
        }

        $title->delete();

        return response()->json(['message' => 'Title deleted']);
    }
}

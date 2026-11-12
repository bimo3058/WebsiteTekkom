<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Capstone\Http\Requests\Title\StoreTitleRequest;
use Modules\Capstone\Http\Requests\Title\UpdateTitleRequest;
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

    public function store(StoreTitleRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdateTitleRequest $request, Title $title)
    {
        if (CapstoneActor::lecturer($request->user())->id !== $title->lecturer_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validated();

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

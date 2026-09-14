<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Modules\Capstone\Support\CapstoneActor;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $requestedRole = $request->string('role')->toString();
        $query = User::query();

        if ($request->has('role')) {
            $roles = match ($request->role) {
                'admin' => ['superadmin', 'admin_capstone'],
                'dosen' => ['dosen'],
                'mahasiswa' => ['mahasiswa'],
                default => [],
            };

            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->whereIn('name', $roles));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->with(['roles', 'student', 'lecturer'])
            ->orderBy('name')
            ->paginate(100)
            ->through(fn (User $user) => [
                // Academic selectors in CTMS submit students.id/lecturers.id.
                // Keep user_id alongside it for audit/account screens.
                'id' => match ($requestedRole) {
                    'dosen' => $user->lecturer?->id,
                    'mahasiswa' => $user->student?->id,
                    default => $user->id,
                },
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => CapstoneActor::role($user),
                'student_id' => $user->student?->id,
                'lecturer_id' => $user->lecturer?->id,
                'created_at' => $user->created_at,
            ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\SuperAdminNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SuperAdminNotificationController extends Controller
{
    public function index(Request $request, SuperAdminNotifications $notifications): JsonResponse
    {
        return response()->json(['notifications' => $notifications->recent($request->user())])
            ->header('Cache-Control', 'private, no-store');
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = array_keys(SuperAdminNotifications::OPTIONS);
        $rules = ['notifications' => ['required', 'array:'.implode(',', $keys)]];
        foreach ($keys as $key) {
            $rules['notifications.'.$key] = ['required', 'boolean'];
        }
        $validated = $request->validate($rules);
        $preferences = array_map(fn ($value) => (bool) $value, $validated['notifications']);

        $user = $request->user();
        $user->forceFill(['notification_preferences' => $preferences])->saveQuietly();
        $user->clearUserCache();

        return redirect()->route('profile.edit', ['tab' => 'notifikasi'])
            ->with('status', 'notifications-updated');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\SuperAdminNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SuperAdminNotificationController extends Controller
{
    public function index(Request $request, SuperAdminNotifications $notifications)
    {
        $notificationsData = $notifications->recent($request->user());

        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $notificationsData,
            ]);
        }

        return view('superadmin.notifications.index', [
            'notifications' => $notificationsData,
        ]);
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

    public function markAsRead(Request $request, $id)
    {
        $result = app(\App\Services\NotificationService::class)->markAsRead($id);

        if ($request->expectsJson()) {
            return response()->json(['success' => $result]);
        }

        return back()->with($result ? 'success' : 'error', $result ? 'Notifikasi ditandai sebagai terbaca.' : 'Gagal memperbarui notifikasi.');
    }
}

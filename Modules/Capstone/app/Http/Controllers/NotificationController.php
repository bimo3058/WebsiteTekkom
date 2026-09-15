<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\GroupInvitation;
use Modules\Capstone\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * List notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(max(1, min(100, (int) $request->get('per_page', 20))));

        $invitationIds = $notifications->getCollection()->where('type', 'GROUP_INVITATION')->pluck('related_id')->filter();
        $invitationStatuses = $invitationIds->isEmpty() ? collect() : GroupInvitation::whereIn('id', $invitationIds)->pluck('status', 'id');

        foreach ($notifications as $notification) {
            if ($notification->type === 'GROUP_INVITATION' && $notification->related_id) {
                if ($invitationStatuses->has($notification->related_id)) {
                    $notification->invitation_status = $invitationStatuses[$notification->related_id];
                }
            }
        }

        return response()->json($notifications);
    }

    /**
     * Get unread count.
     */
    public function unreadCount(Request $request)
    {
        $count = $this->notificationService->unreadCount($request->user()->id);
        return response()->json(['count' => $count]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, int $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $this->notificationService->markAsRead($notification->id);

        return response()->json(['message' => 'Marked as read.']);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $this->notificationService->markAllAsRead($request->user()->id);
        return response()->json(['message' => 'All notifications marked as read.']);
    }
}

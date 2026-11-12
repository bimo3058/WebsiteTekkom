<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Notifikasi;

class NotificationController extends Controller
{
    public function read(Request $request, int $id)
    {
        $role=\Modules\EOffice\Services\PracticumNotificationAudience::authorizedRole($request);
        $item=\Modules\EOffice\Services\PracticumNotificationAudience::query($request->user()->id,$role)->findOrFail($id);
        $item->update(['is_read'=>true]);
        return response()->json(['success'=>true]);
    }
    public function readAll(Request $request)
    {
        $role = \Modules\EOffice\Services\PracticumNotificationAudience::authorizedRole($request);
        \Modules\EOffice\Services\PracticumNotificationAudience::query($request->user()->id, $role)->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function index(Request $request)
    {
        $role = \Modules\EOffice\Services\PracticumNotificationAudience::authorizedRole($request);
        $items = \Modules\EOffice\Services\PracticumNotificationAudience::query($request->user()->id, $role)
            ->where('created_at', '>=', now()->subDays(7))->orderByDesc('created_at')->orderByDesc('id')->get();
        return response()->json(['notifications' => $items->map(fn ($item) => [
            'id' => $item->id, 'title' => $item->judul, 'message' => $item->pesan,
            'time' => $item->created_at?->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i').' WIB',
            'unread' => ! $item->is_read,
            'url' => \Modules\EOffice\Services\ManajemenPraktikum\NotificationDestination::url($item,$role),
            'read_url' => route('eoffice.manprak.notifications.read',['id'=>$item->id,'role'=>$role]),
        ])]);
    }
}

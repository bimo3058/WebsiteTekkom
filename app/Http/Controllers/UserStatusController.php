<?php

namespace App\Http\Controllers;

use App\Services\UserPresence;
use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    public function heartbeat(Request $request, UserPresence $presence)
    {
        $userId = $request->user()?->getAuthIdentifier();

        if (! $userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $presence->touch((int) $userId);

        return response()->json(['status' => 'ok']);
    }
}

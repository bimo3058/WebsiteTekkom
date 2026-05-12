<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LaunchController extends Controller
{
    public function launch(Request $request)
    {
        $user = $request->user();

        if (!$user->can('capstone.view')) {
            abort(403, 'Anda tidak memiliki akses ke modul Capstone.');
        }

        // Generate One-Time-Token (OTT)
        $ott = Str::random(64);
        Cache::put("capstone:ott:{$ott}", [
            'user_id'    => $user->id,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now()->toIso8601String(),
        ], now()->addSeconds(60));

        $feUrl = config('capstone.frontend_url');
        return redirect()->away("{$feUrl}/auth/exchange?ott={$ott}");
    }
}

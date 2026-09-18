<?php

namespace Modules\Capstone\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Capstone\Support\BladeFeatureAccess;
use Modules\Capstone\Support\CapstoneActor;

class BladeSessionFeatureMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $path = '/'.preg_replace('#^capstone/session/capstone/?#', '', $request->path());
        // Registration, group, and workflow reads remain available to show the
        // prerequisites. Existing API controllers validate individual actions.
        $mapping = ['documents'=>'documents','ta'=>'ta-submission','ta-documents'=>'ta-submission','ta-detailed-status'=>'ta-submission','expo-events'=>'expo','peer-review'=>'peer-review',
            'my-grades'=>'grades','ta-defense'=>'ta-defense','ta-defense-schedules'=>'ta-defense',
            'titles'=>'titles','bids'=>'bidding','bursa-ide'=>'group','join-requests'=>'group','solo-titles'=>'titles',
            'proposal'=>'propose-title','propose-title'=>'propose-title','my-proposal'=>'propose-title','group-invitations'=>'group'];
        $parts = explode('/', trim($path, '/'));
        // Recipients can answer invitations from Notifications before joining a
        // period. The controller still checks recipient, pending state and capacity.
        if ($request->isMethod('POST') && preg_match('#^/mahasiswa/group-invitations/[0-9]+/(accept|reject)$#', $path)) {
            return $next($request);
        }
        if (! $request->isMethod('GET')) {
            $mapping['group'] = 'group';
        }
        if (($parts[0] ?? '') === 'mahasiswa' && isset($mapping[$parts[1] ?? ''])) {
            $user = $request->user();
            abort_unless($user && in_array('mahasiswa', CapstoneActor::roles($user), true), 403);
            $state = BladeFeatureAccess::snapshot($user);
            $reason = BladeFeatureAccess::reason('/mahasiswa/'.$mapping[$parts[1]], $state);
            if ($reason) {
                return response()->json(['message'=>$reason, 'code'=>'CAPSTONE_PREREQUISITE_REQUIRED'], 403);
            }
        }
        return $next($request);
    }
}

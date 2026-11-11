<?php

namespace Modules\EOffice\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\{Praktikum,AsprakPraktikum,PendaftaranAsprak,PendaftaranKoordinator};

class PracticumAdministrationBoundary
{
    public function handle(Request $request, Closure $next)
    {
        $action=$request->route()?->getActionMethod();
        $controller=class_basename($request->route()?->getControllerClass() ?? '');
        $staffing=($controller==='KelolRoleController' && in_array($action,['assignRole','revokeRole','restoreRole','revokeAll']))
            || ($controller==='PraktikumController' && $action==='assignKoor');
        $registration=in_array(strtolower($controller),['pendaftarankoorcontroller','pendaftaranasprakcontroller'])
            && in_array($action,['approve','reject']) && str_contains($request->route()?->getName() ?? '', '.admin.');
        if ($staffing || $registration) {
            abort_unless($request->user()?->hasAnyRole($registration?['superadmin','admin_praktikum']:['superadmin','koordinator_cerc']),403,
                $registration?'Persetujuan akhir pendaftaran dikelola Admin Praktikum.':'Penugasan langsung dikelola Koordinator CERC.');
            return DB::transaction(function() use ($request,$next,$controller,$action,$registration) {
                if (in_array($action,['approve','reject'])) {
                    $class=strtolower($controller)==='pendaftarankoorcontroller'?PendaftaranKoordinator::class:PendaftaranAsprak::class;
                    $practicumId=$class::findOrFail($request->route('id'))->praktikum_id;
                } elseif (in_array($action,['revokeRole','restoreRole'])) {
                    $practicumId=AsprakPraktikum::withTrashed()->findOrFail($request->route('id'))->praktikum_id;
                } else $practicumId=$request->route('praktikumId') ?? $request->route('id') ?? $request->input('praktikum_id');
                $practicum=Praktikum::lockForUpdate()->findOrFail($practicumId);
                abort_if(!$practicum->is_active && !$request->user()->hasRole('superadmin'),404);
                $response=$next($request);
                if ($response->getStatusCode()<400 && !session()->has('error')) {
                    \App\Services\AuditLogger::log('eoffice','UPDATE',($registration?'Keputusan pendaftaran oleh Admin Praktikum: ':'Penugasan praktikum oleh Koordinator CERC: ').$action,$practicum,null,
                        ['route'=>$request->route()?->getName(),'user_id'=>$request->input('user_id')],$request->user()->id);
                    foreach (\App\Models\User::whereIn('id',AsprakPraktikum::withTrashed()->where('praktikum_id',$practicum->id)->pluck('user_id'))->get() as $member) {
                        $member->syncPermissionsFromRoles();
                        $member->clearUserCache();
                    }
                }
                return $response;
            });
        }
        if ($controller==='PraktikumController' && in_array($action,['store','update']) && $request->exists('koor_id') && !$request->user()?->hasRole('superadmin')) {
            $previous=$action==='update'?Praktikum::find($request->route('id') ?? $request->route('praktikum'))?->koor_id:null;
            abort_unless((string)$previous===(string)$request->input('koor_id'),403,'Penugasan koordinator dikelola Koordinator CERC.');
            $request->request->remove('koor_id');
        }
        return $next($request);
    }
}

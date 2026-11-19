<?php
namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Models\Notifikasi;
use Modules\EOffice\Models\PracticumBookingRequest;

class NotificationDestination
{
    public static function url(Notifikasi $notification, string $role): string
    {
        $base='eoffice.manprak.'.$role.'.';
        $target=$notification->practicum_target ?? [];
        $title=mb_strtolower($notification->judul ?? '');
        $section=$target['section'] ?? null;
        $parameters=array_intersect_key($target,array_flip(['praktikum_id','pengumuman_id','tugas_id']));
        if ($role==='cerc' && str_contains($title,'jadwal')) {
            if (preg_match('/PRK-\d{8}-[a-f0-9]+/i',$notification->pesan ?? '',$match)) {
                $package=PracticumBookingRequest::where('code',$match[0])->first();
                if ($package) return route($base.'scheduling.show',$package->id);
            }
            return route($base.'scheduling.index',['tab'=>'requests']);
        }
        if (!$section) $section=match(true) {
            str_contains($title,'pengumuman')=>'pengumuman',
            str_contains($title,'modul')=>'modul',
            str_contains($title,'tugas')=>'tugas',
            str_contains($title,'pendaftaran') && (str_contains($title,'koor') || str_contains($title,'koordinator'))=>'pendaftaran-koor',
            str_contains($title,'pendaftaran') && (str_contains($title,'asprak') || str_contains($title,'asisten'))=>'pendaftaran-asprak',
            str_contains($title,'pendaftaran')=>'pendaftaran-praktikan',
            default=>'dashboard',
        };
        $route=$base.$section.'.index';
        // Only named routes in the active Practicum role; never redirect to a stored external URL.
        if (!in_array($section,['pengumuman','modul','tugas','pendaftaran-koor','pendaftaran-asprak','pendaftaran-praktikan'],true) || !Route::has($route)) {
            $route=$base.'dashboard';$parameters=[];
        }
        $definition=Route::getRoutes()->getByName($route);
        foreach ($definition?->parameterNames() ?? [] as $parameter) {
            if (!array_key_exists($parameter,$parameters) && !str_contains($definition->uri(),'{'.$parameter.'?}')) {
                // Older notifications have no object ID; open the role's practicum list safely.
                $route=Route::has($base.'praktikum.index')?$base.'praktikum.index':$base.'dashboard';
                $parameters=[];break;
            }
        }
        return route($route,$parameters);
    }
}

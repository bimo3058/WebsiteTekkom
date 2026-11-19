<?php

namespace Modules\EOffice\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\EOffice\Models\{Praktikum,Tugas};

class StudentPracticumArchive
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->isMethodSafe() && str_contains($request->route()?->getName() ?? '', '.mahasiswa.')) {
            $practicum = null;
            if ($id = $request->route('praktikum')) $practicum = $id instanceof Praktikum ? $id : Praktikum::findOrFail($id);
            elseif (in_array($request->route()?->getActionMethod(), ['kumpul','kirimUlang','batalkan','hapus'])) {
                $practicum = Tugas::with('modul.praktikum')->findOrFail($request->route('id'))->modul?->praktikum;
            }
            elseif ($id = $request->input('praktikum_id')) $practicum = Praktikum::findOrFail($id);
            if ($practicum) return \Illuminate\Support\Facades\DB::transaction(function () use ($practicum,$request,$next) {
                $locked=Praktikum::whereKey($practicum->id)->lockForUpdate()->firstOrFail();
                abort_unless($locked->is_active,403,'Praktikum nonaktif hanya dapat dibaca. Pengumpulan dan perubahan data tidak diperbolehkan.');
                return $next($request);
            });
        }
        return $next($request);
    }
}

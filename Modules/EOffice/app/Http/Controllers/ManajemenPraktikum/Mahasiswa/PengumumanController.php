<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;

/**
 * Mahasiswa: Lihat pengumuman dari asprak/koordinator.
 * Mendukung dropdown filter per praktikum.
 */
class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua praktikum yang diikuti mahasiswa
        $daftarList = DaftarPraktikan::where('user_id', $user->id)
            ->with('praktikum')
            ->get();

        $praktikumList = $daftarList->map(fn($d) => $d->praktikum)->filter();

        // Praktikum yang dipilih (default ke yang pertama)
        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum = $praktikumList->firstWhere('id', $praktikumId);

        // Query pengumuman hanya untuk praktikum yang dipilih (hanya manual/non-sistem)
        $pengumumans = collect();
        if ($praktikum) {
            $query=Pengumuman::where('praktikum_id', $praktikumId)
                ->whereNull('tipe_sistem')
                ->where('is_published',true);
            $page=max(1,$request->integer('page',1));
            if ($request->filled('pengumuman_id')) {
                $request->validate(['pengumuman_id'=>'integer|min:1']);
                $target=(clone $query)->findOrFail($request->integer('pengumuman_id'));
                $newer=fn($q)=>$q->where('created_at','>',$target->created_at)
                    ->orWhere(fn($tie)=>$tie->where('created_at',$target->created_at)->where('id','>',$target->id));
                $ahead=clone $query;
                if (\Illuminate\Support\Facades\Schema::hasColumn((new Pengumuman)->getTable(),'pinned_until')) {
                    if ($target->isPinned()) $ahead->where('pinned_until','>',now())->where($newer);
                    else $ahead->where(fn($q)=>$q->where('pinned_until','>',now())->orWhere(fn($normal)=>$normal
                        ->where(fn($unpinned)=>$unpinned->whereNull('pinned_until')->orWhere('pinned_until','<=',now()))->where($newer)));
                } else $ahead->where($newer);
                $page=intdiv($ahead->count(),10)+1;
            }
            $pengumumans=$query->with(['user','praktikum'])->pinnedFirst()->orderByDesc('created_at')->orderByDesc('id')
                ->paginate(10,['*'],'page',$page)->appends($request->except(['page','pengumuman_id']));
        } elseif ($request->filled('pengumuman_id')) abort(404);

        return view('eoffice::manajemen-praktikum.mahasiswa.pengumuman', compact(
            'pengumumans',
            'praktikumList',
            'praktikum'
        ));
    }
}

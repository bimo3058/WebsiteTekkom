<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\User;
use Modules\EOffice\Models\CercCoordinatorHistory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Services\ManajemenPraktikum\CercCoordinator;

class CercCoordinatorController extends Controller
{
    public function index(Request $request)
    {
        $coordinator=User::whereHas('roles',fn($q)=>$q->where('name','koordinator_cerc'))->first();
        $candidates=\Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent::apply(User::query())->orderBy('name')->get(['id','name','email']);
        $perPage=in_array($request->integer('per_page',10),[5,10,20],true)?$request->integer('per_page',10):10;
        $query=CercCoordinatorHistory::activities();
        $historyLatestId=(int)(clone $query)->max('id');
        $history=$query->with('user')->orderByDesc('created_at')->orderByDesc('id')->paginate($perPage)->withQueryString();
        $historyIds=$history->getCollection()->flatMap(fn($entry)=>array_merge($entry->old_data['user_ids']??[],[$entry->new_data['user_id']??null]))->filter()->unique();
        $historyUsers=User::withTrashed()->whereIn('id',$historyIds)->get()->keyBy('id');
        return view('eoffice::manajemen-praktikum.admin.koordinator-cerc',compact('coordinator','candidates','history','historyUsers','historyLatestId'));
    }

    public function update(Request $request, CercCoordinator $service)
    {
        $data=$request->validate(['user_id'=>'present|nullable|integer|exists:users,id']);
        $service->replace(empty($data['user_id'])?null:User::findOrFail($data['user_id']));
        return back()->with('success','Penugasan Koordinator CERC berhasil diperbarui.');
    }

    public function destroyHistory(Request $request)
    {
        $data=$request->validate(['before_id'=>'required|integer|min:0']);
        $query=CercCoordinatorHistory::activities();
        $query->where('id','<=',$data['before_id']);
        $count=$query->update(['cerc_deleted_at'=>now(),'cerc_deleted_by'=>auth()->id()]);
        return redirect()->route('eoffice.manprak.admin.cerc.index')->withFragment('riwayat-cerc')->with('success',$count?'Riwayat berhasil dihapus.':'Riwayat sudah dihapus atau tidak ditemukan.');
    }
}

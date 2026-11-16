<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Schema};
use Illuminate\Validation\Rule;
use Modules\EOffice\Models\{AsprakPraktikum,Pengumuman,Praktikum};

class AnnouncementPinController
{
    public function update(Request $request, int $id)
    {
        abort_unless(Schema::hasColumn((new Pengumuman)->getTable(),'pinned_until'),503,'Penyematan pengumuman belum tersedia. Jalankan migration pengumuman terlebih dahulu.');
        return DB::transaction(function() use($request,$id){
            $post=Pengumuman::findOrFail($id);
            $course=Praktikum::whereKey($post->praktikum_id)->lockForUpdate()->firstOrFail();
            $post=Pengumuman::whereKey($id)->lockForUpdate()->firstOrFail();
            $actor=$request->user();
            $role=$request->route('announcementRole');
            $assigned=match($role){
                'koor'=>(string)$course->koor_id===(string)$actor->id,
                'asprak'=>AsprakPraktikum::where('praktikum_id',$course->id)->where('user_id',$actor->id)->where('role','asprak')->exists(),
                default=>false,
            };
            abort_unless($assigned && (string)$post->user_id===(string)$actor->id,403,'Anda hanya dapat mengatur sematan pengumuman yang Anda buat pada praktikum yang Anda kelola.');
            abort_unless($course->is_active,403,'Praktikum nonaktif hanya dapat dibaca.');
            abort_unless($post->tipe_sistem===null && ($post->is_published || !$request->boolean('pin')),422,'Hanya pengumuman manual yang sudah dipublikasikan yang dapat disematkan.');
            $data=$request->validate([
                'pin'=>['required','boolean'],
                'pinned_until'=>[Rule::excludeIf(!$request->boolean('pin')),Rule::requiredIf($request->boolean('pin')),'date_format:Y-m-d\TH:i','after:now'],
            ],[
                'pinned_until.required'=>'Tentukan tanggal dan jam berakhirnya penyematan.',
                'pinned_until.date_format'=>'Tanggal dan jam berakhirnya penyematan tidak valid.',
                'pinned_until.after'=>'Tenggat penyematan harus setelah waktu saat ini.',
            ]);
            // Do not change created_at: editing/pinning never resets the 30-day window.
            $post->update(['pinned_until'=>$request->boolean('pin')?$data['pinned_until']:null]);
            return back()->with('success',$request->boolean('pin')?'Pengumuman berhasil disematkan sampai tenggat yang ditentukan.':'Sematan pengumuman berhasil dilepas.');
        });
    }
}

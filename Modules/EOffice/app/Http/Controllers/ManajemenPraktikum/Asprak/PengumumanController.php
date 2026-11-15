<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;
use App\Services\SupabaseStorage;
use Illuminate\Validation\ValidationException;

/**
 * Asprak: Pengumuman (semua asprak bisa upload/hapus).
 * Mendukung dropdown filter per praktikum yang di-assign ke asprak.
 */
class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Semua assignment asprak milik user ini
        $asprakList = AsistenPraktikum::where('user_id', $user->id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->with('praktikum')
            ->get();

        $praktikumList = $asprakList->map(fn($a) => $a->praktikum)->filter();

        // Ambil asprak yang sudah di-resolve oleh middleware (paling akurat)
        $asprakFromMiddleware = $request->attributes->get('asprak');

        // Praktikum yang dipilih: prioritaskan middleware → query param → session → pertama
        $praktikumId = $asprakFromMiddleware?->praktikum_id
            ?? $request->input('praktikum_id')
            ?? session('manprak_asprak_praktikum_id')
            ?? $praktikumList->first()?->id;

        $asprak = $asprakList->firstWhere('praktikum_id', $praktikumId);
        $praktikum = $praktikumList->firstWhere('id', $praktikumId);

        // Update session agar halaman lain ikut context yang sama
        if ($praktikumId) {
            session(['manprak_asprak_praktikum_id' => $praktikumId]);
        }

        // Pengumuman hanya untuk praktikum yang dipilih (hanya manual/non-sistem)
        $pengumumans = $praktikum
            ? Pengumuman::where('praktikum_id', $praktikumId)
                ->whereNull('tipe_sistem')
                ->with('user')
                ->pinnedFirst()
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.pengumuman', compact(
            'pengumumans',
            'asprak',
            'praktikumList',
            'praktikum'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_prak_praktikum,id',
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'is_published' => 'boolean',
            'lampiran' => 'nullable|array|max:3',
            'lampiran.*' => 'file|max:5120',
        ]);

        $user = auth()->user();
        $asprak = AsistenPraktikum::where('user_id', $user->id)
            ->where('role', 'asprak')
            ->where('praktikum_id', $request->praktikum_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$asprak) {
            return back()->with('error', 'Anda tidak terdaftar sebagai asprak di praktikum ini.');
        }

        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            $storage = app(SupabaseStorage::class);
            $uploadFailed = false;
            foreach ($request->file('lampiran') as $file) {
                $path = $storage->upload($file, 'pengumuman', 'eoffice');
                if ($path) {
                    $lampiranPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                    ];
                } else {
                    $uploadFailed = true;
                }
            }
            if ($uploadFailed) {
                foreach ($lampiranPaths as $lampiran) {
                    $storage->delete($lampiran['path'], 'eoffice');
                }
                throw ValidationException::withMessages([
                    'lampiran' => 'Lampiran gagal diunggah. Pengumuman belum disimpan.',
                ]);
            }
        }

        $pengumuman = Pengumuman::create([
            'praktikum_id' => $request->praktikum_id,
            'user_id' => $user->id,
            'judul' => $request->judul,
            'konten' => $request->konten,
            'is_published' => true,
            'lampiran' => empty($lampiranPaths) ? null : $lampiranPaths,
        ]);

        \Modules\EOffice\Services\ManajemenPraktikum\StudentActivityNotification::send(
            $request->praktikum_id, 'Pengumuman praktikum', $request->judul.' — lihat halaman pengumuman praktikum.',
            ['section'=>'pengumuman','praktikum_id'=>$request->praktikum_id,'pengumuman_id'=>$pengumuman->id]);
        return redirect()->route('eoffice.manprak.asprak.pengumuman.index', [
            'praktikum_id' => $request->praktikum_id,
        ])->with('success', 'Pengumuman berhasil diunggah.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'judul'=>'required|string|max:255','konten'=>'required|string',
            'lampiran'=>'nullable|array|max:3','lampiran.*'=>'file|max:5120',
            'attachments_managed'=>'sometimes|boolean',
            'retained_files'=>'nullable|array|max:3','retained_files.*'=>'required|string|max:2000|distinct',
        ]);
        $newPaths=[];
        try {
            return \Illuminate\Support\Facades\DB::transaction(function() use($request,$id,&$newPaths){
                $post=Pengumuman::findOrFail($id);
                $course=Praktikum::whereKey($post->praktikum_id)->lockForUpdate()->firstOrFail();
                $post=Pengumuman::whereKey($id)->lockForUpdate()->firstOrFail();
                abort_unless((string)$post->user_id===(string)auth()->id(),403,'Anda hanya bisa mengedit pengumuman milik sendiri.');
                abort_unless($course->is_active && AsistenPraktikum::where('praktikum_id',$course->id)->where('user_id',auth()->id())->where('role','asprak')->exists(),403,'Penugasan asisten tidak aktif atau praktikum telah diarsipkan.');
                $existing=collect($post->lampiran??[])->keyBy(fn($file)=>is_array($file)?($file['path']??''):$file);
                $retained=$request->boolean('attachments_managed')?$request->input('retained_files',[]):$existing->keys()->all();
                foreach($retained as $path){
                    if(!$existing->has($path))throw ValidationException::withMessages(['retained_files'=>'Lampiran tidak tersedia atau pengumuman telah berubah. Muat ulang halaman sebelum mengedit.']);
                }
                $uploads=$request->file('lampiran',[]);
                if(count($retained)+count($uploads)>3)throw ValidationException::withMessages(['lampiran'=>'Maksimal 3 berkas, termasuk lampiran lama yang dipertahankan.']);
                $files=collect($retained)->map(fn($path)=>$existing[$path])->values()->all();
                foreach($uploads as $file){
                    $path=app(SupabaseStorage::class)->upload($file,'pengumuman','eoffice');
                    if(!$path)throw ValidationException::withMessages(['lampiran'=>'Lampiran gagal diunggah. Perubahan pengumuman belum disimpan.']);
                    $newPaths[]=$path;
                    $files[]=['name'=>$file->getClientOriginalName(),'path'=>$path];
                }
                // Removing an attachment only changes this post; older stored files are not destructively deleted.
                $post->update(['judul'=>$request->judul,'konten'=>$request->konten,'lampiran'=>$files?:null]);
                return back()->with('success','Pengumuman berhasil diperbarui.');
            });
        } catch (\Throwable $exception) {
            foreach($newPaths as $path)app(SupabaseStorage::class)->delete($path,'eoffice');
            throw $exception;
        }
    }
    public function destroy(int $id)
    {
        $user = auth()->user();
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->user_id !== $user->id && !$user->hasAnyRole(['koor_prak', 'superadmin'])) {
            return back()->with('error', 'Anda hanya bisa menghapus pengumuman milik sendiri.');
        }

        $praktikumId = $pengumuman->praktikum_id;
        $pengumuman->delete();

        return redirect()->route('eoffice.manprak.asprak.pengumuman.index', [
            'praktikum_id' => $praktikumId,
        ])->with('success', 'Pengumuman berhasil dihapus.');
    }
}

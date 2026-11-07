<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\MateriModul;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;

class MateriController extends Controller
{
    public function __construct(private SupabaseStorage $supabase) {}

    public function index(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $modulIds = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->pluck('modul_id')
            : collect();
            
        if ($asprak && $modulIds->isEmpty()) {
            session()->now('error', 'Akses terbatas: Anda belum di-assign sebagai pengampu pada modul manapun di praktikum ini.');
        }

        $materis = MateriModul::whereIn('modul_id', $modulIds)->with('modul.praktikum')->orderByDesc('created_at')->get();

        $modulsForSelect = $modulIds->isNotEmpty()
            ? Modul::with(['modulSumber.materi', 'modulSumber.praktikum'])->whereIn('id', $modulIds)->orderBy('urutan')->get()
            : collect();

        $praktikum = $asprak?->praktikum;

        return view('eoffice::manajemen-praktikum.asprak.materi', compact(
            'materis', 'asprak', 'modulsForSelect', 'praktikum'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modul_id'  => 'required|exists:modul_praktikum,id',
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file'      => 'nullable|array|max:10',
            'file.*'    => 'file|max:51200',
        ]);

        $path     = null;
        $tipeFile = null;

        $allowed = ModulAsprak::whereHas('asprak', fn($q) => $q
            ->where('user_id', auth()->id())
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
        )->where('modul_id', $request->modul_id)->exists();

        if (! $allowed) {
            return back()->with('error', 'Anda tidak di-assign ke modul ini.');
        }

        if ($request->hasFile('file')) {
            $files = $request->file('file');
            foreach ($files as $file) {
                $path = $this->supabase->upload($file, 'materi-modul', 'eoffice');
                $tipeFile = $file->getClientMimeType();
                if ($path) {
                    $judul = count($files) > 1 ? $request->judul . ' - ' . $file->getClientOriginalName() : $request->judul;
                    MateriModul::create([
                        'modul_id'  => $request->modul_id,
                        'user_id'   => auth()->id(),
                        'judul'     => $judul,
                        'deskripsi' => $request->deskripsi,
                        'file_path' => $path,
                        'tipe_file' => $tipeFile,
                    ]);
                }
            }
        } else {
            MateriModul::create([
                'modul_id'  => $request->modul_id,
                'user_id'   => auth()->id(),
                'judul'     => $request->judul,
                'deskripsi' => $request->deskripsi,
                'file_path' => null,
                'tipe_file' => null,
            ]);
        }

        return back()->with('success', 'Materi berhasil diunggah.');
    }

    public function reuse(Request $request)
    {
        $data = $request->validate([
            'modul_id' => 'required|exists:modul_praktikum,id',
            'materi_sumber_id' => 'required|exists:materi_modul,id',
        ]);

        $allowed = ModulAsprak::whereHas('asprak', fn ($query) => $query
            ->where('user_id', auth()->id())
            ->where('role', 'asprak')
            ->whereNull('deleted_at'))
            ->where('modul_id', $data['modul_id'])
            ->exists();

        if (! $allowed) {
            abort(403, 'Anda tidak di-assign ke modul ini.');
        }

        $modul = Modul::with('modulSumber')->findOrFail($data['modul_id']);
        $materiSumber = MateriModul::findOrFail($data['materi_sumber_id']);

        if (! $modul->modul_sumber_id || $materiSumber->modul_id !== $modul->modul_sumber_id) {
            abort(422, 'Materi tersebut bukan berasal dari modul periode sebelumnya.');
        }

        MateriModul::firstOrCreate(
            [
                'modul_id' => $modul->id,
                'materi_sumber_id' => $materiSumber->id,
            ],
            [
                'user_id' => auth()->id(),
                'judul' => $materiSumber->judul,
                'deskripsi' => $materiSumber->deskripsi,
                'file_path' => $materiSumber->file_path,
                'tipe_file' => $materiSumber->tipe_file,
            ]
        );

        return back()->with('success', 'Materi periode sebelumnya berhasil digunakan kembali.');
    }

    public function destroy($id)
    {
        $materi = MateriModul::with('modul.modulAsprak.asprak')->findOrFail($id);

        $allowed = $materi->modul?->modulAsprak
            ->contains(fn ($ma) => $ma->asprak?->user_id === auth()->id());

        if (! $allowed) {
            abort(403, 'Anda tidak berhak menghapus materi ini.');
        }

        $dipakaiMateriLain = $materi->file_path
            ? MateriModul::where('file_path', $materi->file_path)->where('id', '!=', $materi->id)->exists()
            : false;

        if ($materi->file_path && ! $dipakaiMateriLain) {
            $this->supabase->delete($materi->file_path, 'eoffice');
        }

        $materi->delete();

        return back()->with('success', 'Materi dihapus.');
    }
}

<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

/**
 * Admin: Unggah/menambah data praktikan berdasarkan praktikum.
 * Sesuai docx Role Admin: "Unggah/menambah data praktikan berdasarkan praktikum".
 */
class DaftarPraktikanController extends Controller
{
    public function index(Request $request)
    {
        $praktikumList = Praktikum::orderByDesc('created_at')->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $search = $request->input('search');

        $query = DaftarPraktikan::with(['user'])
            ->where('praktikum_id', $praktikumId);

        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $praktikans = $query->paginate(max(1, min(100, request()->integer('per_page', 20))))->withQueryString();

        return view('eoffice::manajemen-praktikum.admin.daftar-praktikan', compact(
            'praktikumList',
            'praktikum',
            'praktikans',
            'search'
        ));
    }

    /**
     * Tambah praktikan manual atau import CSV.
     */
    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_prak_praktikum,id',
            'file'         => 'required_without:user_ids|nullable|file|mimes:csv,xlsx,xls|max:5120',
            'user_ids'     => 'required_without:file|nullable|array|min:1',
            'user_ids.*'   => 'required|integer|distinct|exists:users,id',
        ]);

        $praktikum = Praktikum::findOrFail($request->praktikum_id);
        abort_if($praktikum->status !== 'aktif' || $praktikum->is_active === false, 403, 'Praktikum tidak aktif.');
        abort_if(app(\Modules\EOffice\Services\PracticumGroupingLock::class)->isLocked($praktikum->id),
            403, 'Mahasiswa tidak dapat ditambahkan karena aktivitas praktikum sudah tercatat.');

        $added = 0;

        // Tambah manual by ID
        if ($request->has('user_ids')) {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $praktikum = Praktikum::whereKey($request->praktikum_id)->lockForUpdate()->firstOrFail();
                abort_if($praktikum->status !== 'aktif' || $praktikum->is_active === false, 403, 'Praktikum tidak aktif.');
                abort_if(app(\Modules\EOffice\Services\PracticumGroupingLock::class)->isLocked($praktikum->id), 403,
                    'Mahasiswa tidak dapat ditambahkan karena aktivitas praktikum sudah tercatat.');
                $users = User::whereIn('id', $request->user_ids)->whereHas('student')->get();
                if ($users->count() !== count($request->user_ids)) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['user_ids' => 'Pilih akun mahasiswa yang terdaftar di SITKOM.']);
                }
                if (DaftarPraktikan::where('praktikum_id', $praktikum->id)->whereIn('user_id', $request->user_ids)->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['user_ids' => 'Mahasiswa yang dipilih sudah terdaftar pada praktikum ini.']);
                }
                foreach ($users as $user) {
                    \Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent::ensure($user);
                    DaftarPraktikan::create(['praktikum_id' => $praktikum->id, 'user_id' => $user->id, 'status' => 'terdaftar']);
                }
                \App\Services\AuditLogger::log('eoffice', 'CREATE', 'Menambahkan mahasiswa secara manual ke praktikum', null,
                    null, ['praktikum_id' => $praktikum->id, 'user_ids' => $users->pluck('id')->all()]);
                return back()->with('success', $users->count().' mahasiswa berhasil ditambahkan. Koordinator dapat mengatur kelompok dan shiftnya.');
            });
        }

        // Import dari file Excel / CSV yang strukturnya bisa dinamis (seperti dari SSO)
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $worksheet   = $spreadsheet->getActiveSheet();
                $rows        = $worksheet->toArray();

            } catch (\Throwable $e) {
                report($e);
                return back()->with('error', 'File tidak dapat dibaca. Periksa format Excel atau CSV yang diunggah.');
            }

            $result = app(\Modules\EOffice\Services\PraktikanImportService::class)->import($praktikum->id, $rows);
            $key='participant_import_reports.'.$praktikum->id;
            if (empty($result['issues'])) {
                $request->session()->forget($key);
                return back()->with('success','Semua praktikan sudah terdaftar');
            }
            $result['token']=(string)\Illuminate\Support\Str::uuid();
            $request->session()->put($key,$result);
            return back()->with('success',
                $result['added'].' mahasiswa ditambahkan, '.$result['duplicate'].' duplikat dilewati, '.count($result['issues']).' baris perlu diperiksa.');
        }

        return back()->with('success', "{$added} praktikan berhasil ditambahkan.");
    }

    /**
     * Hapus praktikan dari daftar.
     */
    public function destroy(string $id)
    {
        DaftarPraktikan::findOrFail($id)->delete();

        return back()->with('success', 'Praktikan berhasil dihapus dari daftar.');
    }

    public function completeImport(Request $request, string $id)
    {
        Praktikum::findOrFail($id);
        $data=$request->validate(['report_token'=>'required|string','confirmed'=>'required|accepted'],[
            'confirmed.required'=>'Konfirmasikan penyelesaian hasil impor terlebih dahulu.',
            'confirmed.accepted'=>'Konfirmasikan penyelesaian hasil impor terlebih dahulu.',
        ]);
        $key='participant_import_reports.'.$id;
        $report=$request->session()->get($key);
        if (!$report || !hash_equals((string)($report['token']??''),$data['report_token'])) {
            return back()->with('error','Hasil impor telah berubah. Muat ulang halaman terlebih dahulu.');
        }
        $request->session()->forget($key);
        return back()->with('success','Hasil impor ditandai selesai.');
    }
}

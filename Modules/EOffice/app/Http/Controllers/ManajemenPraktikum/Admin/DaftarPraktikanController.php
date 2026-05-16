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

        $praktikans = $query->paginate(20)->withQueryString();

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
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'file'         => 'nullable|file|mimes:csv,xlsx,xls|max:5120',
            'user_ids'     => 'nullable|array',
            'user_ids.*'   => 'exists:users,id',
        ]);

        $added = 0;

        // Tambah manual by ID
        if ($request->has('user_ids')) {
            foreach ($request->user_ids as $userId) {
                DaftarPraktikan::firstOrCreate([
                    'praktikum_id' => $request->praktikum_id,
                    'user_id'      => $userId,
                ], ['status' => 'terdaftar']);
                $added++;
            }
        }

        // Import dari file CSV (kolom: nim atau email)
        if ($request->hasFile('file')) {
            $path   = $request->file('file')->getRealPath();
            $rows   = array_map('str_getcsv', file($path));
            array_shift($rows); // hapus header

            foreach ($rows as $row) {
                if (empty($row[0])) continue;
                $identifier = trim($row[0]);

                $targetUser = User::where('email', $identifier)
                    ->orWhereHas('student', fn($q) => $q->where('student_number', $identifier))
                    ->first();

                if (!$targetUser) continue;

                DaftarPraktikan::firstOrCreate([
                    'praktikum_id' => $request->praktikum_id,
                    'user_id'      => $targetUser->id,
                ], ['status' => 'terdaftar']);
                $added++;
            }
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
}

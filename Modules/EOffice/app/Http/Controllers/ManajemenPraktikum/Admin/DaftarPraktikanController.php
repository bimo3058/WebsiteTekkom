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

        // Import dari file Excel / CSV yang strukturnya bisa dinamis (seperti dari SSO)
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $worksheet   = $spreadsheet->getActiveSheet();
                $rows        = $worksheet->toArray();

                $identifiers = [];

                // Scan seluruh cell untuk mencari header "nim" atau "email"
                foreach ($rows as $r => $row) {
                    foreach ($row as $c => $val) {
                        $header = strtolower(trim((string)$val));
                        if ($header === 'nim' || $header === 'email') {
                            // Baca nilai di bawah header ini sampai habis
                            for ($i = $r + 1; $i < count($rows); $i++) {
                                $cellVal = trim((string)($rows[$i][$c] ?? ''));
                                if ($cellVal === '') continue;

                                // Basic sanitize (hapus spasi)
                                $cellVal = preg_replace('/\s+/', '', $cellVal);
                                if (strlen($cellVal) >= 5) {
                                    $identifiers[] = $cellVal;
                                }
                            }
                        }
                    }
                }

                $identifiers = array_unique($identifiers);

                // Jika formatnya flat (baris pertama langsung data tanpa header), kita perlu fallback
                if (empty($identifiers)) {
                    foreach ($rows as $row) {
                        $cellVal = trim((string)($row[0] ?? ''));
                        if (empty($cellVal)) continue;
                        if (strtolower($cellVal) === 'nim' || strtolower($cellVal) === 'email') continue;

                        $cellVal = preg_replace('/\s+/', '', $cellVal);
                        if (strlen($cellVal) >= 5) {
                            $identifiers[] = $cellVal;
                        }
                    }
                }

                foreach ($identifiers as $identifier) {
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

            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
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

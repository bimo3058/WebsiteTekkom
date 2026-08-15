<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Pengaturan;

class HakAksesController extends Controller
{
    public function index()
    {
        $settings = Pengaturan::where('key', 'like', 'sb_%')->pluck('value', 'key')->toArray();

        // Admin Toggles (Superadmin only visibility)
        $adminKlg = filter_var($settings['sb_admin_kalenderglobal'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $adminJad = filter_var($settings['sb_admin_jadwalakademik'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $adminEvt = filter_var($settings['sb_admin_event'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $adminSet = filter_var($settings['sb_admin_persetujuan'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $adminArs = filter_var($settings['sb_admin_arsip'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $adminUsu = filter_var($settings['sb_admin_manajemenuser'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $adminRua = filter_var($settings['sb_admin_manajemenruangan'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $adminPgt = filter_var($settings['sb_admin_pengaturan'] ?? true, FILTER_VALIDATE_BOOLEAN);

        // Mahasiswa Toggles
        $usrKat = filter_var($settings['sb_user_katalog'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $usrKal = filter_var($settings['sb_user_kalender'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $usrPem = filter_var($settings['sb_user_peminjaman'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $usrRiw = filter_var($settings['sb_user_riwayat'] ?? true, FILTER_VALIDATE_BOOLEAN);

        return view('eoffice::manajemen-ruangan.admin.hak-akses.index', compact(
            'adminKlg',
            'adminJad',
            'adminEvt',
            'adminSet',
            'adminArs',
            'adminUsu',
            'adminRua',
            'adminPgt',
            'usrKat',
            'usrKal',
            'usrPem',
            'usrRiw'
        ));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // Mahasiswa Scope Updates (Available to Superadmin & Admin)
        if ($request->has('scope_user')) {
            Pengaturan::updateOrCreate(['key' => 'sb_user_katalog'], ['value' => $request->has('usr_kat') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_user_kalender'], ['value' => $request->has('usr_kal') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_user_peminjaman'], ['value' => $request->has('usr_pem') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_user_riwayat'], ['value' => $request->has('usr_riw') ? '1' : '0']);
        }

        // Admin Scope Updates (Strictly Superadmin only)
        if ($request->has('scope_admin') && $user->hasRole('superadmin')) {
            Pengaturan::updateOrCreate(['key' => 'sb_admin_kalenderglobal'], ['value' => $request->has('adm_klg') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_admin_jadwalakademik'], ['value' => $request->has('adm_jad') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_admin_event'], ['value' => $request->has('adm_evt') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_admin_persetujuan'], ['value' => $request->has('adm_set') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_admin_arsip'], ['value' => $request->has('adm_ars') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_admin_manajemenuser'], ['value' => $request->has('adm_usu') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_admin_manajemenruangan'], ['value' => $request->has('adm_rua') ? '1' : '0']);
            Pengaturan::updateOrCreate(['key' => 'sb_admin_pengaturan'], ['value' => $request->has('adm_pgt') ? '1' : '0']);
        }

        return redirect()->back()->with('success', 'Hak akses menu berhasil diperbarui.');
    }
}

<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\NotifikasiService;
use Modules\EOffice\Services\PeriodePendaftaranService;

/**
 * Admin: membuka dan mengelola periode pendaftaran mahasiswa sebagai praktikan.
 */
class PeriodePraktikanController extends Controller
{
    public function __construct(
        protected NotifikasiService $notif,
        protected PeriodePendaftaranService $periodeService
    ) {}

    public function index(Request $request)
    {
        $this->periodeService->tutupKadaluarsa();

        $praktikumList = Praktikum::with(['matkul', 'dosens', 'koordinator'])
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikumDipilih = $praktikumList->firstWhere('id', $praktikumId);

        $periodeList = $praktikumDipilih
            ? PeriodePendaftaran::where('praktikum_id', $praktikumDipilih->id)
                ->where('jenis', 'praktikan')
                ->with('dibukaOleh')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.admin.periode-praktikan', compact(
            'praktikumList',
            'praktikumDipilih',
            'praktikumId',
            'periodeList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'nama'         => 'nullable|string|max:255',
            'dibuka_pada'  => 'nullable|date',
            'ditutup_pada' => 'required|date|after_or_equal:dibuka_pada',
        ]);

        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('status', 'aktif')
            ->firstOrFail();

        PeriodePendaftaran::where('praktikum_id', $praktikum->id)
            ->where('jenis', 'praktikan')
            ->update(['is_aktif' => false]);

        $periode = PeriodePendaftaran::create([
            'praktikum_id' => $praktikum->id,
            'jenis'        => 'praktikan',
            'nama'         => $request->nama ?? 'Pendaftaran Praktikan ' . now()->format('M Y'),
            'dibuka_pada'  => $request->dibuka_pada ?? now(),
            'ditutup_pada' => $request->ditutup_pada,
            'is_aktif'     => true,
            'dibuka_oleh'  => auth()->id(),
        ]);

        $batasTeks = $periode->ditutup_pada
            ? 'Batas pendaftaran: ' . $periode->ditutup_pada->format('d M Y H:i') . '.'
            : 'Pendaftaran dibuka hingga pemberitahuan lebih lanjut.';

        Pengumuman::create([
            'praktikum_id' => $praktikum->id,
            'user_id'      => auth()->id(),
            'judul'        => "Pendaftaran Praktikan Dibuka — {$praktikum->nama}",
            'konten'       => "Pendaftaran praktikan untuk {$praktikum->nama} kini dibuka!\n\n"
                . "{$batasTeks}\n\n"
                . 'Silakan unggah IRS melalui menu Pendaftaran Praktikan.',
            'is_published' => true,
            'tipe_sistem'  => 'buka',
            'periode_id'   => $periode->id,
        ]);

        $jumlah = $this->notif->kirimKeSemuaUser(
            'Pendaftaran Praktikan Dibuka',
            "Pendaftaran praktikan untuk {$praktikum->nama} kini dibuka! {$batasTeks}"
        );

        return back()->with('success', "Periode pendaftaran praktikan berhasil dibuka. Notifikasi terkirim ke {$jumlah} pengguna.");
    }

    public function tutup(int $id)
    {
        $periode = PeriodePendaftaran::with('praktikum')->findOrFail($id);

        if ($periode->jenis !== 'praktikan') {
            return back()->with('error', 'Periode yang dipilih bukan periode pendaftaran praktikan.');
        }

        $this->periodeService->tutup($periode, auth()->id());

        return back()->with('success', 'Periode pendaftaran praktikan telah ditutup.');
    }

    public function destroy(int $id)
    {
        $periode = PeriodePendaftaran::findOrFail($id);

        if ($periode->jenis !== 'praktikan') {
            return back()->with('error', 'Periode yang dipilih bukan periode pendaftaran praktikan.');
        }

        $periode->delete();

        return back()->with('success', 'Periode pendaftaran praktikan berhasil dihapus.');
    }
}

<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\NotifikasiService;
use Modules\EOffice\Services\PeriodePendaftaranService;

/**
 * Dosen: Buka / tutup periode pendaftaran KOORDINATOR.
 *
 * Dosen hanya bisa mengelola periode dengan jenis = 'koor'
 * untuk praktikum yang dia ampu (dosen_id = auth()->id()).
 */
class PeriodePendaftaranController extends Controller
{
    public function __construct(
        protected NotifikasiService $notif,
        protected PeriodePendaftaranService $periodeService
    ) {}

    /**
     * Tampilkan semua periode koor untuk praktikum yang diampu dosen ini.
     */
    public function index(Request $request)
    {
        $this->periodeService->tutupKadaluarsa();

        $user = auth()->user();

        // Ambil semua praktikum yang diampu dosen ini
        $praktikumList = Praktikum::with(['matkul', 'koordinator'])
            ->whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);

        // Validasi: praktikum yang dipilih harus milik dosen ini
        $praktikumDipilih = $praktikumId
            ? $praktikumList->firstWhere('id', $praktikumId)
            : null;

        $periodeList = $praktikumDipilih
            ? PeriodePendaftaran::where('praktikum_id', $praktikumDipilih->id)
                ->where('jenis', 'koor')
                ->with(['dibukaOleh'])
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $riwayatPengumuman = $praktikumDipilih
            ? Pengumuman::withTrashed()
                ->where('praktikum_id', $praktikumDipilih->id)
                ->whereNotNull('tipe_sistem')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.dosen.periode-pendaftaran', compact(
            'praktikumList',
            'praktikumDipilih',
            'praktikumId',
            'periodeList',
            'riwayatPengumuman'
        ));
    }

    /**
     * Buka periode pendaftaran koor untuk praktikum yang diampu.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'nama'         => 'nullable|string|max:255',
            'dibuka_pada'  => 'nullable|date',
            'ditutup_pada' => 'required|date|after_or_equal:dibuka_pada',
        ]);

        // Pastikan praktikum milik dosen ini
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->where('status', 'aktif')
            ->firstOrFail();

        if ($praktikum->koor_id) {
            return back()->with('error', 'Praktikum ini sudah memiliki Koordinator. Pendaftaran tidak diperlukan lagi.');
        }

        // Nonaktifkan periode koor yang lama untuk praktikum ini
        PeriodePendaftaran::where('praktikum_id', $praktikum->id)
            ->where('jenis', 'koor')
            ->update(['is_aktif' => false]);

        $periode = PeriodePendaftaran::create([
            'praktikum_id' => $praktikum->id,
            'jenis'        => 'koor',
            'nama'         => $request->nama ?? 'Periode Koordinator ' . now()->format('M Y'),
            'dibuka_pada'  => $request->dibuka_pada ?? now(),
            'ditutup_pada' => $request->ditutup_pada,
            'is_aktif'     => true,
            'dibuka_oleh'  => $user->id,
        ]);

        $batasTeks = $periode->ditutup_pada
            ? "Batas pendaftaran: {$periode->ditutup_pada->format('d M Y H:i')}."
            : 'Pendaftaran dibuka hingga pemberitahuan lebih lanjut.';

        Pengumuman::create([
            'praktikum_id' => $praktikum->id,
            'user_id'      => $user->id,
            'judul'        => "📢 Pendaftaran Koordinator Dibuka — {$praktikum->nama}",
            'konten'       => "Pendaftaran Koordinator untuk praktikum {$praktikum->nama} kini resmi dibuka!\n\n"
                . "{$batasTeks}\n\n"
                . "Segera daftarkan diri Anda melalui menu Pendaftaran.",
            'is_published' => true,
            'tipe_sistem'  => 'buka',
            'periode_id'   => $periode->id,
        ]);

        $jumlah = $this->notif->kirimKeSemuaUser(
            "📢 Pendaftaran Koordinator Dibuka",
            "Pendaftaran Koordinator untuk praktikum {$praktikum->nama} kini dibuka! {$batasTeks}"
        );

        return back()->with('success', "Periode pendaftaran Koordinator berhasil dibuka. Notifikasi terkirim ke {$jumlah} pengguna.");
    }

    /**
     * Tutup periode pendaftaran koor.
     */
    public function tutup(int $id)
    {
        $user    = auth()->user();
        $periode = PeriodePendaftaran::with('praktikum')->findOrFail($id);

        // Pastikan praktikum milik dosen ini & jenis = koor
        if (!$periode->praktikum?->dosens->contains('id', $user->id)) {
            return back()->with('error', 'Anda tidak berhak menutup periode ini.');
        }
        if ($periode->jenis !== 'koor') {
            return back()->with('error', 'Hanya periode jenis Koordinator yang dapat Anda kelola.');
        }

        $this->periodeService->tutup($periode, $user->id);

        return back()->with('success', 'Periode pendaftaran Koordinator telah ditutup.');
    }

    /**
     * Hapus periode pendaftaran koor.
     */
    public function destroy(int $id)
    {
        $user    = auth()->user();
        $periode = PeriodePendaftaran::with('praktikum')->findOrFail($id);

        if (!$periode->praktikum?->dosens->contains('id', $user->id)) {
            return back()->with('error', 'Anda tidak berhak menghapus periode ini.');
        }
        if ($periode->jenis !== 'koor') {
            return back()->with('error', 'Hanya periode jenis Koordinator yang dapat Anda kelola.');
        }

        $periode->delete();
        return back()->with('success', 'Periode dihapus.');
    }
}

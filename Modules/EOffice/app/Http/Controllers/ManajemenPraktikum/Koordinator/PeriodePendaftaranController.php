<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\NotifikasiService;
use Modules\EOffice\Services\PeriodePendaftaranService;

/**
 * Koordinator: Buka / tutup periode pendaftaran ASISTEN PRAKTIKUM.
 *
 * Koor hanya bisa mengelola periode dengan jenis = 'asprak'
 * untuk praktikum yang di-assign kepadanya oleh dosen (koor_id = auth()->id()).
 */
class PeriodePendaftaranController extends Controller
{
    public function __construct(
        protected NotifikasiService $notif,
        protected PeriodePendaftaranService $periodeService
    ) {}

    /**
     * Tampilkan periode asprak untuk praktikum yang dikelola koor ini.
     */
    public function index(Request $request)
    {
        $this->periodeService->tutupKadaluarsa();

        $user = auth()->user();

        // Ambil praktikum yang di-assign ke koor ini
        $praktikumList = Praktikum::with(['matkul', 'dosens'])
            ->where('koor_id', $user->id)
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);

        $praktikumDipilih = $praktikumId
            ? $praktikumList->firstWhere('id', $praktikumId)
            : null;

        $periodeList = $praktikumDipilih
            ? PeriodePendaftaran::where('praktikum_id', $praktikumDipilih->id)
                ->where('jenis', 'asprak')
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

        return view('eoffice::manajemen-praktikum.koordinator.periode-pendaftaran', compact(
            'praktikumList',
            'praktikumDipilih',
            'praktikumId',
            'periodeList',
            'riwayatPengumuman'
        ));
    }

    /**
     * Buka periode pendaftaran asprak.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'nama'         => 'nullable|string|max:255',
            'dibuka_pada'  => 'nullable|date',
            'ditutup_pada' => 'nullable|date|after_or_equal:dibuka_pada',
        ]);

        // Pastikan praktikum di-assign ke koor ini
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Nonaktifkan periode asprak lama untuk praktikum ini
        PeriodePendaftaran::where('praktikum_id', $praktikum->id)
            ->where('jenis', 'asprak')
            ->update(['is_aktif' => false]);

        $periode = PeriodePendaftaran::create([
            'praktikum_id' => $praktikum->id,
            'jenis'        => 'asprak',
            'nama'         => $request->nama ?? 'Periode Asprak ' . now()->format('M Y'),
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
            'judul'        => "📢 Pendaftaran Asisten Praktikum Dibuka — {$praktikum->nama}",
            'konten'       => "Pendaftaran Asisten Praktikum untuk {$praktikum->nama} kini resmi dibuka!\n\n"
                . "{$batasTeks}\n\n"
                . "Segera daftarkan diri Anda melalui menu Pendaftaran.",
            'is_published' => true,
            'tipe_sistem'  => 'buka',
            'periode_id'   => $periode->id,
        ]);

        $jumlah = $this->notif->kirimKeSemuaUser(
            "📢 Pendaftaran Asisten Praktikum Dibuka",
            "Pendaftaran Asisten Praktikum untuk {$praktikum->nama} kini dibuka! {$batasTeks}"
        );

        return back()->with('success', "Periode pendaftaran Asisten Praktikum berhasil dibuka. Notifikasi terkirim ke {$jumlah} pengguna.");
    }

    /**
     * Tutup periode pendaftaran asprak.
     */
    public function tutup(int $id)
    {
        $user    = auth()->user();
        $periode = PeriodePendaftaran::with('praktikum')->findOrFail($id);

        // Pastikan praktikum milik koor ini & jenis = asprak
        if ((string) $periode->praktikum?->koor_id !== (string) $user->id) {
            return back()->with('error', 'Anda tidak berhak menutup periode ini.');
        }
        if ($periode->jenis !== 'asprak') {
            return back()->with('error', 'Hanya periode jenis Asisten Praktikum yang dapat Anda kelola.');
        }

        $this->periodeService->tutup($periode, $user->id);

        return back()->with('success', 'Periode pendaftaran Asisten Praktikum telah ditutup.');
    }

    /**
     * Hapus periode pendaftaran asprak.
     */
    public function destroy(int $id)
    {
        $user    = auth()->user();
        $periode = PeriodePendaftaran::with('praktikum')->findOrFail($id);

        if ((string) $periode->praktikum?->koor_id !== (string) $user->id) {
            return back()->with('error', 'Anda tidak berhak menghapus periode ini.');
        }
        if ($periode->jenis !== 'asprak') {
            return back()->with('error', 'Hanya periode jenis Asisten Praktikum yang dapat Anda kelola.');
        }

        $periode->delete();
        return back()->with('success', 'Periode dihapus.');
    }
}

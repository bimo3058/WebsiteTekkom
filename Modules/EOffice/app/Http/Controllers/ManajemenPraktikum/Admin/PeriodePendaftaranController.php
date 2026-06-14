<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\MatkulPraktikum;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\NotifikasiService;
use Modules\EOffice\Services\PeriodePendaftaranService;

/**
 * Admin: Kelola periode pendaftaran PRAKTIKAN.
 *
 * Periode koor dibuka oleh Dosen, periode asprak dibuka oleh Koordinator.
 * Dropdown utama dari eo_matkul_praktikum (sudah di-seed).
 */
class PeriodePendaftaranController extends Controller
{
    public function __construct(
        protected NotifikasiService $notif,
        protected PeriodePendaftaranService $periodeService
    ) {}

    public function index(Request $request)
    {
        $this->periodeService->tutupKadaluarsa();

        $matkulList    = MatkulPraktikum::orderBy('semester')->orderBy('kode')->get();
        $matkulId      = $request->input('matkul_id');
        $matkulDipilih = $matkulId ? $matkulList->firstWhere('id', $matkulId) : null;

        // Praktikum yg cocok matkul_id-nya (sudah di-link)
        // Jika matkul dipilih tapi belum ada yg di-link → tampilkan semua aktif agar bisa di-assign
        if ($matkulDipilih) {
            $praktikumLinked = Praktikum::with(['dosen', 'koordinator'])
                ->where('matkul_id', $matkulId)
                ->where('status', 'aktif')
                ->orderByDesc('created_at')
                ->get();

            // Semua praktikum aktif (untuk dropdown assign jika belum ada yang terhubung)
            $praktikumSemua = Praktikum::with(['dosen', 'matkul'])
                ->where('status', 'aktif')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $praktikumLinked = collect();
            $praktikumSemua  = collect();
        }

        $praktikumId = $request->input('praktikum_id', $praktikumLinked->first()?->id);

        $periodeList = $praktikumId
            ? PeriodePendaftaran::where('praktikum_id', $praktikumId)
                ->with(['dibukaOleh', 'praktikum.matkul'])
                ->orderByDesc('created_at')
                ->get()
            : collect();

        // Riwayat pengumuman sistem (buka/tutup) termasuk yang sudah di-soft-delete
        $riwayatPengumuman = $praktikumId
            ? Pengumuman::withTrashed()
                ->where('praktikum_id', $praktikumId)
                ->whereNotNull('tipe_sistem')
                ->with('praktikum')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.admin.periode-pendaftaran', compact(
            'matkulList',
            'matkulDipilih',
            'matkulId',
            'praktikumLinked',
            'praktikumSemua',
            'periodeList',
            'praktikumId',
            'riwayatPengumuman'
        ));
    }

    /**
     * Assign matkul_id ke praktikum yang sudah ada.
     */
    public function assignMatkul(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'matkul_id'    => 'required|integer|exists:eo_matkul_praktikum,id',
        ]);

        Praktikum::where('id', $request->praktikum_id)
            ->update(['matkul_id' => $request->matkul_id]);

        return back()->with('success', 'Mata kuliah berhasil dihubungkan ke praktikum.');
    }

    public function edit(int $id)
    {
        $this->periodeService->tutupKadaluarsa();

        $periode = PeriodePendaftaran::with(['praktikum.matkul', 'praktikum.dosen', 'dibukaOleh'])
            ->findOrFail($id);
        $praktikumList = Praktikum::with(['matkul', 'dosen'])
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        return view('eoffice::manajemen-praktikum.admin.periode-pendaftaran-edit', compact(
            'periode',
            'praktikumList'
        ));
    }

    public function update(Request $request, int $id)
    {
        $periode = PeriodePendaftaran::findOrFail($id);

        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'jenis'        => 'required|in:praktikan',
            'nama'         => 'required|string|max:255',
            'dibuka_pada'  => 'nullable|date',
            'ditutup_pada' => 'nullable|date|after_or_equal:dibuka_pada',
            'is_aktif'     => 'nullable|boolean',
        ]);

        $aktif = $request->boolean('is_aktif');

        if ($aktif) {
            PeriodePendaftaran::where('praktikum_id', $request->praktikum_id)
                ->where('jenis', $request->jenis)
                ->where('id', '!=', $periode->id)
                ->update(['is_aktif' => false]);
        }

        $periode->update([
            'praktikum_id' => $request->praktikum_id,
            'jenis'        => $request->jenis,
            'nama'         => $request->nama,
            'dibuka_pada'  => $request->dibuka_pada,
            'ditutup_pada' => $request->ditutup_pada,
            'is_aktif'     => $aktif,
        ]);

        if ($aktif && $periode->ditutup_pada && now()->gte($periode->ditutup_pada)) {
            $this->periodeService->tutup($periode->refresh(), auth()->id(), false);

            return redirect()
                ->route('eoffice.manprak.admin.periode-pendaftaran.edit', $periode->id)
                ->with('success', 'Periode diperbarui dan langsung ditutup karena waktu tutup sudah lewat.');
        }

        return redirect()
            ->route('eoffice.manprak.admin.periode-pendaftaran.edit', $periode->id)
            ->with('success', 'Periode pendaftaran berhasil diperbarui.');
    }

    /**
     * Buka periode pendaftaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'jenis'        => 'required|in:praktikan',
            'nama'         => 'nullable|string|max:255',
            'dibuka_pada'  => 'nullable|date',
            'ditutup_pada' => 'nullable|date|after_or_equal:dibuka_pada',
        ]);

        PeriodePendaftaran::where('praktikum_id', $request->praktikum_id)
            ->where('jenis', $request->jenis)
            ->update(['is_aktif' => false]);

        $periode = PeriodePendaftaran::create([
            'praktikum_id' => $request->praktikum_id,
            'jenis'        => $request->jenis,
            'nama'         => $request->nama ?? 'Periode ' . ucfirst($request->jenis) . ' ' . now()->format('M Y'),
            'dibuka_pada'  => $request->dibuka_pada ?? now(),
            'ditutup_pada' => $request->ditutup_pada,
            'is_aktif'     => true,
            'dibuka_oleh'  => auth()->id(),
        ]);

        $praktikum  = Praktikum::find($request->praktikum_id);
        $jenisLabel = $this->periodeService->jenisLabel($request->jenis);
        $batasTeks  = $periode->ditutup_pada
            ? "Batas pendaftaran: {$periode->ditutup_pada->format('d M Y H:i')}."
            : 'Pendaftaran dibuka hingga pemberitahuan lebih lanjut.';

        // ── Buat pengumuman otomatis "Pendaftaran Dibuka" ──────────────────
        Pengumuman::create([
            'praktikum_id' => $request->praktikum_id,
            'user_id'      => auth()->id(),
            'judul'        => "📢 Pendaftaran {$jenisLabel} Dibuka — {$praktikum?->nama}",
            'konten'       => "Pendaftaran {$jenisLabel} untuk praktikum {$praktikum?->nama} kini resmi dibuka!\n\n"
                . "{$batasTeks}\n\n"
                . "Segera daftarkan diri Anda melalui menu Pendaftaran.",
            'is_published' => true,
            'tipe_sistem'  => 'buka',
            'periode_id'   => $periode->id,
        ]);

        // ── Notifikasi ke semua user ───────────────────────────────────────
        $jumlah = $this->notif->kirimKeSemuaUser(
            "📢 Pendaftaran {$jenisLabel} Dibuka",
            "Pendaftaran {$jenisLabel} untuk praktikum {$praktikum?->nama} kini dibuka! {$batasTeks}"
        );

        return back()->with('success', "Periode pendaftaran {$jenisLabel} berhasil dibuka. Pengumuman otomatis telah dibuat. Notifikasi terkirim ke {$jumlah} pengguna.");
    }

    /**
     * Tutup periode pendaftaran:
     * - Set is_aktif = false
     * - Soft-delete pengumuman "buka" terkait periode ini (hilang dari tampilan user)
     * - Buat pengumuman baru "Pendaftaran Telah Ditutup" (tetap tampil)
     */
    public function tutup(int $id)
    {
        $periode   = PeriodePendaftaran::with('praktikum')->findOrFail($id);
        $jenisLabel = $this->periodeService->jenisLabel($periode->jenis);
        $this->periodeService->tutup($periode, auth()->id());

        return back()->with('success', "Periode pendaftaran {$jenisLabel} telah ditutup. Pengumuman otomatis telah dibuat.");

        $praktikum = $periode->praktikum;
        $jenis     = $periode->jenis;
        $jenisLabel = match ($jenis) {
            'koor'      => 'Koordinator',
            'asprak'    => 'Asisten Praktikum',
            'praktikan' => 'Praktikan',
            default     => ucfirst($jenis),
        };

        // 1. Nonaktifkan periode
        $periode->update(['is_aktif' => false]);

        // 2. Soft-delete pengumuman "buka" milik periode ini
        //    (tidak tampil di user, tapi masih ada di DB untuk riwayat admin)
        Pengumuman::where('praktikum_id', $periode->praktikum_id)
            ->where('tipe_sistem', 'buka')
            ->where('periode_id', $periode->id)
            ->delete(); // soft delete karena model pakai SoftDeletes

        // 3. Buat pengumuman otomatis "Pendaftaran Ditutup"
        Pengumuman::create([
            'praktikum_id' => $periode->praktikum_id,
            'user_id'      => auth()->id(),
            'judul'        => "🔒 Pendaftaran {$jenisLabel} Telah Ditutup — {$praktikum?->nama}",
            'konten'       => "Pendaftaran {$jenisLabel} untuk praktikum {$praktikum?->nama} telah resmi ditutup.\n\n"
                . "Pendaftaran ditutup pada: " . now()->format('d M Y, H:i') . ".\n\n"
                . "Terima kasih kepada seluruh pendaftar. Hasil seleksi akan diumumkan segera.",
            'is_published' => true,
            'tipe_sistem'  => 'tutup',
            'periode_id'   => $periode->id,
        ]);

        // 4. Notifikasi ke semua user
        $this->notif->kirimKeSemuaUser(
            "🔒 Pendaftaran {$jenisLabel} Ditutup",
            "Pendaftaran {$jenisLabel} untuk praktikum {$praktikum?->nama} telah ditutup. Terima kasih kepada seluruh pendaftar."
        );

        return back()->with('success', "Periode pendaftaran {$jenisLabel} telah ditutup. Pengumuman otomatis telah dibuat.");
    }

    public function destroy(int $id)
    {
        PeriodePendaftaran::findOrFail($id)->delete();
        return back()->with('success', 'Periode dihapus.');
    }
}

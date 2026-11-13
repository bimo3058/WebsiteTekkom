<?php

namespace Modules\ManajemenMahasiswa\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\KegiatanCatatan;
use Modules\ManajemenMahasiswa\Policies\KegiatanPolicy;

/**
 * Alur tinjau ketua pelaksana ↔ KABID (S-82, 11 Nov 2026) — dipakai bersama oleh
 * Rencana Proker dan Pelaksanaan Kegiatan.
 *
 *   KABID membuat proker + menunjuk ketua pelaksana  → Menunggu Dilengkapi
 *   Ketua pelaksana mengisi, menekan "Kirim"          → Menunggu Tinjauan (data dikunci)
 *   KABID menolak dengan catatan                      → Perlu Perbaikan
 *   Ketua pelaksana memperbaiki, mengirim lagi        → Menunggu Tinjauan
 *   KABID menyetujui → Rencana diteruskan ke Pelaksanaan (RencanaProkerService::ajukan)
 *                      atau Pelaksanaan diunggah ke Arsip (PelaksanaanController).
 *
 * Notifikasi tidak disimpan sebagai pesan: lonceng menghitung langsung dari status ini
 * (NotifikasiTugasService), jadi hilang sendiri begitu tindakannya selesai.
 * Hak akses ada di KegiatanPolicy (tinjau, kirimTinjauan, gantiKetua); service ini
 * hanya memeriksa status supaya klik ganda/tab basi berakhir di pesan.
 */
class TinjauanKegiatanService
{
    /** Validasi isi dialog "Tolak dengan catatan" — sama di Rencana & Pelaksanaan. */
    public const ATURAN_CATATAN = ['isi' => 'required|string|min:5|max:1000'];

    public const PESAN_CATATAN = [
        'isi.required' => 'Catatan untuk ketua pelaksana wajib diisi — tuliskan apa yang perlu diperbaiki.',
        'isi.min'      => 'Catatan terlalu pendek. Tuliskan apa yang perlu diperbaiki.',
        'isi.max'      => 'Catatan maksimal 1.000 karakter.',
    ];

    /**
     * Ketua pelaksana mengirim data untuk ditinjau. Catatan yang masih terbuka
     * dianggap sudah dijawab oleh kiriman ini.
     *
     * @throws \DomainException bila sedang ditinjau atau bukan tahap yang bertinjau
     */
    public function kirim(Kegiatan $kegiatan, User $oleh): void
    {
        DB::transaction(function () use ($kegiatan, $oleh) {
            $terkini = Kegiatan::whereKey($kegiatan->id)->lockForUpdate()->first();
            if (!$terkini || !in_array($terkini->status, Kegiatan::STATUS_BERTINJAU, true)) {
                throw new \DomainException('Kegiatan ini sudah tidak berada di tahap yang bisa dikirim untuk ditinjau.');
            }
            if ($terkini->sedang_ditinjau) {
                throw new \DomainException('Kegiatan ini sudah dikirim dan sedang menunggu tinjauan.');
            }

            KegiatanCatatan::where('kegiatan_id', $kegiatan->id)
                ->whereNull('diperbaiki_at')
                ->update(['diperbaiki_at' => now(), 'diperbaiki_oleh' => $oleh->id]);

            $terkini->update([
                'status_tinjauan'   => Kegiatan::TINJAU_DITINJAU,
                'dikirim_tinjau_at' => now(),
            ]);
        });
    }

    /**
     * Peninjau mengembalikan kiriman dengan catatan → Perlu Perbaikan.
     *
     * @throws \DomainException bila tidak sedang ditinjau
     */
    public function tolak(Kegiatan $kegiatan, User $oleh, string $isi): void
    {
        DB::transaction(function () use ($kegiatan, $oleh, $isi) {
            $terkini = Kegiatan::whereKey($kegiatan->id)->lockForUpdate()->first();
            if (!$terkini || !$terkini->sedang_ditinjau) {
                throw new \DomainException('Kegiatan ini tidak sedang menunggu tinjauan, jadi tidak bisa dikembalikan.');
            }

            KegiatanCatatan::create([
                'kegiatan_id' => $kegiatan->id,
                'user_id'     => $oleh->id,
                'isi'         => trim($isi),
            ]);

            $terkini->update(['status_tinjauan' => Kegiatan::TINJAU_PERBAIKAN]);
        });
    }

    /**
     * Ganti ketua pelaksana dari dialog (S-82). Calon wajib staff himpunan sebidang
     * (KegiatanPolicy::aturanKetuaSebidang). Status tinjauan tidak berubah: ketua baru
     * meneruskan pekerjaan yang ada.
     *
     * @return string nama ketua baru
     * @throws \Illuminate\Validation\ValidationException
     */
    public function gantiKetuaDariRequest(Request $request, Kegiatan $kegiatan): string
    {
        $bidangId = $kegiatan->bidangs->first()?->id;

        $data = $request->validate([
            'ketua_pelaksana_id' => [
                'required', 'integer', 'exists:students,id',
                Rule::notIn(array_filter([(int) $kegiatan->ketua_pelaksana_id])),
                KegiatanPolicy::aturanKetuaSebidang(null, $bidangId),
            ],
        ], [
            'ketua_pelaksana_id.required' => 'Pilih ketua pelaksana yang baru.',
            'ketua_pelaksana_id.not_in'   => 'Pilih orang lain — dia sudah menjadi ketua pelaksana proker ini.',
            'ketua_pelaksana_id.in'       => KegiatanPolicy::PESAN_KETUA_SEBIDANG,
        ]);

        $student = Student::with('user')->findOrFail($data['ketua_pelaksana_id']);

        $kegiatan->update([
            'ketua_pelaksana_id' => $student->id,
            'penanggung_jawab'   => $student->user?->name,
        ]);

        return $student->user?->name ?? '-';
    }

    /**
     * Pilihan dialog "Ganti ketua pelaksana": staff himpunan sebidang dengan kegiatan,
     * tanpa ketua yang sekarang.
     *
     * @return Collection<int, array{id: int, nama: string, nim: string}>
     */
    public function calonKetua(Kegiatan $kegiatan): Collection
    {
        $bidangId = $kegiatan->bidangs->first()?->id;
        if (!$bidangId) {
            return collect();
        }

        $ids = array_diff(
            KegiatanPolicy::idCalonKetuaPelaksana(null, $bidangId),
            [(int) $kegiatan->ketua_pelaksana_id]
        );

        return Student::with('user')->whereIn('id', $ids)->get()
            ->sortBy(fn ($s) => $s->user->name ?? '')
            ->map(fn ($s) => [
                'id'   => (int) $s->id,
                'nama' => $s->user->name ?? '-',
                'nim'  => (string) $s->student_number,
            ])
            ->values();
    }
}

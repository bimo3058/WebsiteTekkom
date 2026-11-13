<?php

namespace Modules\ManajemenMahasiswa\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;
use Modules\ManajemenMahasiswa\Models\Kegiatan;

/**
 * Jadwal & ruangan proker (S-81, usulan Pak Maman L-41: cegah proker bentrok
 * tempat dan jam).
 *
 * Ruangan kampus diambil dari master Manajemen Ruangan EOffice — sumber yang sama
 * dengan Capstone, KP, dan Praktikum (lihat Capstone\Services\EofficeAvailabilityService).
 * Modul ini HANYA MEMBACA data EOffice: memilih ruangan di proker tidak memesan
 * ruangan. Pemesanan tetap lewat Manajemen Ruangan (bookRoom() EOffice hanya
 * menerima sumber kp/capstone/praktikum).
 *
 * Aturan bentrok sama dengan EOffice: jam_mulai < jam_selesai lain DAN
 * jam_selesai > jam_mulai lain, per tanggal. Kegiatan lintas hari dianggap
 * memakai jam yang sama setiap harinya.
 *
 * Semua hasil hanya peringatan; tidak ada yang memblokir simpan atau Ajukan.
 */
class JadwalRuanganService
{
    /** Kegiatan lintas hari dicek paling banyak sekian hari pertama. */
    public const MAKS_HARI = 14;

    /**
     * Status proker yang dihitung sebagai "proker lain". `diajukan` tidak ikut karena
     * salinannya (status disetujui) sudah mewakili — kalau ikut, satu proker muncul dua kali.
     */
    private const STATUS_DIHITUNG = [Kegiatan::STATUS_DRAFT, Kegiatan::STATUS_DISETUJUI, Kegiatan::STATUS_SELESAI];

    /**
     * Kategori jadwal internal EOffice yang keterangannya disamarkan "Terpakai" di kalender
     * pengguna EOffice (UserPeminjamanController::kalender + view user/kalender). Ikuti
     * daftar itu bila EOffice mengubahnya.
     */
    private const AGENDA_DISAMARKAN = ['Lainnya', 'Lainnya...', 'Event / Kegiatan', 'Rapat Internal'];

    private ?Collection $ruangan = null;

    public function tersedia(): bool
    {
        static $ada = null;

        return $ada ??= class_exists(Ruangan::class)
            && Schema::hasTable('eo_mr_ruangans')
            && Schema::hasTable('eo_mr_peminjamans')
            && Schema::hasTable('eo_mr_jadwal_internal');
    }

    /** Ruangan aktif EOffice, urut kategori lalu nama. */
    public function daftarRuangan(): Collection
    {
        if (!$this->tersedia()) {
            return collect();
        }

        return $this->ruangan ??= Ruangan::query()
            ->where('is_active', true)
            ->orderBy('kategori')->orderBy('nama')
            ->get(['id', 'nama', 'kapasitas', 'kategori'])
            ->map(fn ($r) => [
                'id'        => (int) $r->id,
                'nama'      => $r->nama,
                'kapasitas' => $r->kapasitas !== null ? (int) $r->kapasitas : null,
                'kategori'  => $r->kategori,
            ]);
    }

    public function namaRuangan(?int $id): ?string
    {
        return $id ? $this->daftarRuangan()->firstWhere('id', $id)['nama'] ?? null : null;
    }

    /**
     * Isi kolom lokasi dari pilihan form: ruangan kampus → nama ruangannya; selain itu
     * teks bebas (luar kampus / daring).
     *
     * @return array{ruangan_id: ?int, lokasi: ?string}
     */
    public function kolomLokasi(array $validated): array
    {
        $id = !empty($validated['ruangan_id']) ? (int) $validated['ruangan_id'] : null;
        $nama = $this->namaRuangan($id);

        return $nama !== null
            ? ['ruangan_id' => $id, 'lokasi' => $nama]
            : ['ruangan_id' => null, 'lokasi' => $validated['lokasi'] ?? null];
    }

    /** Aturan validasi ruangan_id: harus ruangan aktif EOffice. */
    public function aturanRuangan(): array
    {
        return ['nullable', 'integer', function ($attr, $value, $fail) {
            if ($value && $this->namaRuangan((int) $value) === null) {
                $fail('Ruangan yang dipilih tidak ditemukan atau sedang dinonaktifkan di Manajemen Ruangan.');
            }
        }];
    }

    /**
     * Status setiap ruangan pada slot itu.
     *
     * @param array{tanggal_mulai: ?string, tanggal_selesai?: ?string, jam_mulai?: ?string, jam_selesai?: ?string} $slot
     * @return array<int, array{status: string, alasan: ?string}> kunci = id ruangan
     *         status: tersedia | terpakai | menunggu | direncanakan | belum_dicek
     */
    public function statusRuangan(array $slot, ?Kegiatan $kecuali = null): array
    {
        $ruangan = $this->daftarRuangan();
        $hasil = $ruangan->mapWithKeys(fn ($r) => [$r['id'] => ['status' => 'tersedia', 'alasan' => null]])->all();

        [$hari, $jamMulai, $jamSelesai] = $this->normalkan($slot);
        if (!$hari || !$jamMulai || !$jamSelesai) {
            return array_map(fn () => ['status' => 'belum_dicek', 'alasan' => 'Isi tanggal, jam mulai, dan jam selesai untuk mengecek ruangan.'], $hasil);
        }

        $tandai = function (int $id, string $status, string $alasan) use (&$hasil) {
            $urutan = ['tersedia' => 0, 'direncanakan' => 1, 'menunggu' => 2, 'terpakai' => 3];
            if (isset($hasil[$id]) && $urutan[$status] > $urutan[$hasil[$id]['status']]) {
                $hasil[$id] = ['status' => $status, 'alasan' => $alasan];
            }
        };

        foreach ($hari as $tgl) {
            $label = count($hari) > 1 ? Carbon::parse($tgl)->locale('id')->translatedFormat('j M') . ' ' : '';

            Peminjaman::with('ruangan:id,nama')
                ->whereDate('tanggal_pinjam', $tgl)
                ->whereIn('status', ['disetujui', 'menunggu'])
                ->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai)
                ->get(['id', 'ruangan_id', 'status', 'tujuan', 'source', 'jam_mulai', 'jam_selesai'])
                ->each(function ($p) use ($tandai, $label) {
                    $jam = substr($p->jam_mulai, 0, 5) . '–' . substr($p->jam_selesai, 0, 5);
                    $p->status === 'disetujui'
                        ? $tandai((int) $p->ruangan_id, 'terpakai', "{$label}Dipinjam ({$p->source_label}) {$jam}")
                        : $tandai((int) $p->ruangan_id, 'menunggu', "{$label}Diajukan pihak lain {$jam}, menunggu persetujuan");
                });

            $iso = Carbon::parse($tgl)->format('N');
            MrJadwalInternal::query()
                ->where(fn ($q) => $q
                    ->where(fn ($r) => $r->where('tipe_jadwal', 'rutin')->where('hari', $iso)
                        ->where(fn ($e) => $e->whereNull('tgl_mulai_efektif')->orWhere('tgl_mulai_efektif', '<=', $tgl))
                        ->where(fn ($e) => $e->whereNull('tgl_selesai_efektif')->orWhere('tgl_selesai_efektif', '>=', $tgl)))
                    ->orWhere(fn ($s) => $s->where('tipe_jadwal', 'spesifik')->whereDate('tanggal_spesifik', $tgl)))
                ->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai)
                ->get()
                ->each(function ($j) use ($tandai, $label) {
                    $jam = substr($j->jam_mulai, 0, 5) . '–' . substr($j->jam_selesai, 0, 5);
                    // Agenda internal kategori "lainnya" disamarkan jadi "Terpakai", sama seperti
                    // kalender pengguna EOffice — keterangannya tidak dibuka ke pengguna lain.
                    $apa = match (true) {
                        $j->kategori === 'Jadwal Akademik (Kuliah)'
                            => 'Kuliah ' . trim($j->mata_kuliah . ($j->kelas ? " ({$j->kelas})" : '')),
                        in_array($j->kategori, self::AGENDA_DISAMARKAN, true) => 'Terpakai',
                        default => $j->keterangan ?: $j->kategori,
                    };
                    $tandai((int) $j->ruangan_id, 'terpakai', "{$label}{$apa} {$jam}");
                });
        }

        foreach ($this->prokerLain($slot, $kecuali) as $k) {
            if ($k['ruangan_id']) {
                $tandai($k['ruangan_id'], 'direncanakan', "{$k['judul']} ({$k['bidang']}) {$k['jam']} — belum tentu dipinjam di EOffice");
            }
        }

        return $hasil;
    }

    /**
     * Proker lain (semua bidang) yang tanggalnya beririsan dengan slot ini. Hanya
     * judul, bidang, tanggal, jam, dan lokasi — tanpa anggaran/dokumen, karena
     * pengurus bidang lain ikut melihatnya (keputusan Surya 10 Nov 2026).
     *
     * @return list<array>
     */
    public function bentrok(array $slot, ?Kegiatan $kecuali = null): array
    {
        return array_values(array_filter(
            $this->prokerLain($slot, $kecuali),
            fn ($k) => $k['jam_beririsan'] !== false
        ));
    }

    /** @return list<array> */
    private function prokerLain(array $slot, ?Kegiatan $kecuali): array
    {
        [$hari, $jamMulai, $jamSelesai] = $this->normalkan($slot);
        if (!$hari) {
            return [];
        }
        $mulai   = $hari[0];
        $selesai = $slot['tanggal_selesai'] ?? null ?: $mulai;

        $kecualiId = array_filter([$kecuali?->id, $kecuali?->rencana_id]);
        $ruanganId = !empty($slot['ruangan_id']) ? (int) $slot['ruangan_id'] : null;
        $lokasi    = mb_strtolower(trim((string) ($slot['lokasi'] ?? '')));

        return Kegiatan::query()
            ->with('bidangs:id,nama_bidang')
            ->whereIn('status', self::STATUS_DIHITUNG)
            ->when($kecualiId, fn ($q) => $q->whereNotIn('id', $kecualiId)
                ->where(fn ($w) => $w->whereNull('rencana_id')->orWhereNotIn('rencana_id', $kecualiId)))
            ->filterRentang($mulai, $selesai)
            ->orderBy('tanggal_mulai')->orderBy('jam_mulai')
            ->get(['id', 'judul', 'status', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'lokasi', 'ruangan_id', 'rencana_id'])
            ->map(function (Kegiatan $k) use ($jamMulai, $jamSelesai, $ruanganId, $lokasi) {
                $jamLain = $k->jam_mulai && $k->jam_selesai;
                // null = tidak bisa dipastikan (salah satu belum berjam) → tetap diperingatkan
                $beririsan = ($jamMulai && $jamSelesai && $jamLain)
                    ? ($jamMulai < $this->detik($k->jam_selesai) && $jamSelesai > $this->detik($k->jam_mulai))
                    : null;
                $ruangSama = ($ruanganId && $k->ruangan_id)
                    ? $ruanganId === (int) $k->ruangan_id
                    : ($lokasi !== '' && $lokasi === mb_strtolower(trim((string) $k->lokasi)));

                return [
                    'id'            => $k->id,
                    'judul'         => $k->judul,
                    'bidang'        => $k->bidangs->pluck('nama_bidang')->implode(', ') ?: 'Prodi',
                    'tahap'         => Kegiatan::STATUS_LABELS[$k->status] ?? $k->status,
                    'tanggal'       => $this->teksTanggal($k),
                    'jam'           => $jamLain ? substr($k->jam_mulai, 0, 5) . '–' . substr($k->jam_selesai, 0, 5) : 'jam belum diisi',
                    'lokasi'        => $k->lokasi ?: '—',
                    'ruangan_id'    => $k->ruangan_id ? (int) $k->ruangan_id : null,
                    'ruang_sama'    => $ruangSama,
                    'jam_beririsan' => $beririsan,
                ];
            })
            ->all();
    }

    /** @return array{0: list<string>, 1: ?string, 2: ?string} hari (Y-m-d), jam mulai & selesai (H:i:s) */
    private function normalkan(array $slot): array
    {
        try {
            $mulai = !empty($slot['tanggal_mulai']) ? Carbon::parse($slot['tanggal_mulai'])->startOfDay() : null;
            $selesai = !empty($slot['tanggal_selesai']) ? Carbon::parse($slot['tanggal_selesai'])->startOfDay() : $mulai;
        } catch (\Throwable) {
            return [[], null, null];
        }
        if (!$mulai) {
            return [[], null, null];
        }
        if ($selesai->lt($mulai)) {
            $selesai = $mulai->copy();
        }

        $hari = [];
        for ($d = $mulai->copy(); $d->lte($selesai) && count($hari) < self::MAKS_HARI; $d->addDay()) {
            $hari[] = $d->toDateString();
        }

        $jamMulai   = $this->detik($slot['jam_mulai'] ?? null);
        $jamSelesai = $this->detik($slot['jam_selesai'] ?? null);
        // Jam selesai ≤ jam mulai pada kegiatan lintas hari: anggap sampai akhir hari.
        if ($jamMulai && $jamSelesai && $jamSelesai <= $jamMulai) {
            $jamSelesai = '23:59:59';
        }

        return [$hari, $jamMulai, $jamSelesai];
    }

    private function detik(?string $jam): ?string
    {
        if (!$jam || !preg_match('/^(\d{1,2}):(\d{2})/', $jam, $m)) {
            return null;
        }

        return sprintf('%02d:%02d:00', $m[1], $m[2]);
    }

    private function teksTanggal(Kegiatan $k): string
    {
        $f = fn ($t) => $t->locale('id')->translatedFormat('j M Y');
        if (!$k->tanggal_selesai || $k->tanggal_selesai->isSameDay($k->tanggal_mulai)) {
            return $f($k->tanggal_mulai);
        }

        return $f($k->tanggal_mulai) . ' – ' . $f($k->tanggal_selesai);
    }
}

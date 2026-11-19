<?php

namespace Modules\ManajemenMahasiswa\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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
 *
 * S-88 (15 Nov 2026, keputusan Surya): yang meminjam ruangan di EOffice adalah KETUA
 * PELAKSANA proker (KABID hanya mendelegasikan). Form boleh diisi ketua pelaksana, staff
 * Akses Kelola, atau admin, tetapi yang dihitung tetap peminjaman milik ketua pelaksana
 * proker itu. Peminjaman ketua tidak dianggap "pihak lain"; statusnya dilaporkan terpisah
 * (dipinjam_ketua | diajukan_ketua | sebagian_ketua | ditolak_ketua).
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

    /**
     * Aturan peminjaman Manajemen Ruangan EOffice (S-90, 15 Nov 2026), BACA-SAJA.
     * Nilai & default sama persis dengan UserPeminjamanController::storeBooking agar
     * ketua pelaksana tahu syaratnya sebelum meminjam. Tidak mengubah apa pun di EOffice.
     */
    public function aturanPeminjaman(): ?array
    {
        if (!class_exists(\Modules\EOffice\Models\Pengaturan::class) || !Schema::hasTable('eo_mr_pengaturan')) {
            return null;
        }
        $nilai = \Modules\EOffice\Models\Pengaturan::whereIn('key', ['jam_buka', 'jam_tutup', 'batas_h_min_booking', 'buka_akhir_pekan'])
            ->pluck('value', 'key');

        return [
            'jam_buka'         => $nilai['jam_buka'] ?? '08:00',
            'jam_tutup'        => $nilai['jam_tutup'] ?? '16:00',
            'batas_h_min'      => (int) ($nilai['batas_h_min_booking'] ?? 0),
            'buka_akhir_pekan' => filter_var($nilai['buka_akhir_pekan'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

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

    public const PESAN_LOKASI_KAMPUS = 'Lokasi di dalam kampus harus dipilih dari daftar ruangan. Teks bebas hanya untuk lokasi luar kampus atau daring.';

    /** Kata kunci nama tempat di dalam kampus, selain nama ruangan master EOffice. */
    // Pola per kata utuh (bukan sekadar mengandung), agar "Sekretariat Panti X" atau
    // "Laboratorium Klinik Y" di luar kampus tidak ikut ditolak. Kata umum seperti
    // "sekretariat" dan "lab" hanya dihitung bila disusul nama unit kampus Tekkom.
    private const POLA_KAMPUS = [
        '/\benviro\b/u',
        '/\baula departemen\b/u',
        '/\bgedung kuliah bersama\b/u',
        '/\bgedung teknik komputer\b/u',
        '/\bsekretariat\s+(himaskom|hima|departemen|tekkom|teknik komputer|dpm)\b/u',
        '/\blab(oratorium)?\.?\s+(sistem|komputer|jaringan|robotika|multimedia|tertanam|rekayasa|dasar|tekkom)\b/u',
    ];

    /** Apakah teks lokasi bebas menyebut ruangan/gedung kampus (master EOffice atau kata kunci)? */
    public function lokasiTeksDalamKampus(?string $lokasi): bool
    {
        $t = mb_strtolower(trim((string) $lokasi));
        if ($t === '') {
            return false;
        }
        foreach (self::POLA_KAMPUS as $pola) {
            if (preg_match($pola, $t) === 1) {
                return true;
            }
        }
        foreach ($this->daftarRuangan() as $r) {
            $nama = mb_strtolower(trim((string) $r['nama']));
            if (mb_strlen($nama) < 4) {
                continue;
            }
            // Nama ruangan harus berdiri sendiri sebagai kata/frasa utuh di dalam teks.
            $pola = '/(?<![\p{L}\p{N}])' . preg_quote($nama, '/') . '(?![\p{L}\p{N}])/u';
            if (preg_match($pola, $t) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Aturan validasi `lokasi` (teks bebas): hanya dicek bila ruangan_id kosong. Nilai lama
     * yang tidak diubah ($tersimpan) tidak ditolak, supaya data lama tetap bisa disimpan.
     */
    public function aturanLokasiTeks(\Illuminate\Http\Request $request, ?string $tersimpan = null): \Closure
    {
        return function ($attr, $value, $fail) use ($request, $tersimpan) {
            if (filled($request->input('ruangan_id')) || blank($value)) {
                return;
            }
            if ($tersimpan !== null && mb_strtolower(trim($value)) === mb_strtolower(trim($tersimpan))) {
                return;
            }
            if ($this->lokasiTeksDalamKampus($value)) {
                $fail(self::PESAN_LOKASI_KAMPUS);
            }
        };
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
     *                 | dipinjam_ketua | diajukan_ketua | sebagian_ketua | ditolak_ketua (S-88)
     */
    public function statusRuangan(array $slot, ?Kegiatan $kecuali = null, ?int $userKetua = null): array
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
                ->get(['id', 'user_id', 'ruangan_id', 'status', 'tujuan', 'source', 'jam_mulai', 'jam_selesai'])
                ->each(function ($p) use ($tandai, $label, $userKetua) {
                    // S-88: peminjaman milik Ketua Pelaksana proker ini bukan "pihak lain"
                    if ($userKetua && (int) $p->user_id === $userKetua) {
                        return;
                    }
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

        if ($userKetua) {
            $this->terapkanStatusKetua($hasil, $hari, $jamMulai, $jamSelesai, $userKetua);
        }

        return $hasil;
    }

    /** user_id ketua pelaksana proker, atau null bila belum ada (S-88). */
    public function userKetua(?Kegiatan $k): ?int
    {
        $id = $k?->ketuaPelaksana?->user_id;

        return $id ? (int) $id : null;
    }

    /** user_id dari id mahasiswa (students.id) yang dipilih sebagai Ketua Pelaksana di form (S-89). */
    public function userDariMahasiswa($studentId): ?int
    {
        $id = $studentId ? \App\Models\Student::whereKey($studentId)->value('user_id') : null;

        return $id ? (int) $id : null;
    }

    /**
     * S-89: apakah ada peminjaman DISETUJUI milik user itu untuk ruangan & tanggal pinjam tsb.
     */
    public function peminjamanKetuaCocok(?int $userKetuaId, int $ruanganId, ?string $tanggal): bool
    {
        if (!$userKetuaId || !$ruanganId || blank($tanggal) || !$this->tersedia()) {
            return false;
        }
        try {
            $tgl = Carbon::parse($tanggal)->toDateString();
        } catch (\Throwable) {
            return false;
        }

        return Peminjaman::query()
            ->where('user_id', $userKetuaId)
            ->where('status', 'disetujui')
            ->where('ruangan_id', $ruanganId)
            ->whereDate('tanggal_pinjam', $tgl)
            ->exists();
    }

    /**
     * S-89: aturan tambahan ruangan_id untuk form. Ruangan yang BERUBAH dari nilai tersimpan
     * wajib berasal dari peminjaman disetujui milik Ketua Pelaksana (semua role). Nilai lama
     * yang tidak diubah tidak ditolak.
     */
    public function aturanRuanganKetua(\Illuminate\Http\Request $request, ?int $tersimpan = null): \Closure
    {
        return function ($attr, $value, $fail) use ($request, $tersimpan) {
            if (!$value || (int) $value === (int) $tersimpan) {
                return;
            }
            $userKetua = $this->userDariMahasiswa($request->input('ketua_pelaksana_id'));
            if (!$this->peminjamanKetuaCocok($userKetua, (int) $value, $request->input('tanggal_mulai'))) {
                $fail('Ruangan harus diambil dari peminjaman Ketua Pelaksana yang sudah disetujui.');
            }
        };
    }

    /**
     * Peminjaman DISETUJUI milik ketua pelaksana yang akan datang, untuk daftar "Pakai"
     * di form (S-88/S-89). Kosong bila EOffice tidak tersedia.
     */
    public function peminjamanKetua(?int $userId): Collection
    {
        if (!$userId || !$this->tersedia()) {
            return collect();
        }
        $aktif = $this->daftarRuangan()->keyBy('id');

        return Peminjaman::query()
            ->where('user_id', $userId)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_pinjam', '>=', Carbon::today()->toDateString())
            ->whereIn('ruangan_id', $aktif->keys()->all())
            ->orderBy('tanggal_pinjam')->orderBy('jam_mulai')
            ->limit(30)
            ->get(['id', 'ruangan_id', 'tanggal_pinjam', 'jam_mulai', 'jam_selesai', 'status', 'tujuan'])
            ->map(function ($p) use ($aktif) {
                $tgl = Carbon::parse($p->tanggal_pinjam);

                return [
                    'id'            => $p->id,
                    'ruangan_id'    => (int) $p->ruangan_id,
                    'ruangan'       => $aktif[(int) $p->ruangan_id]['nama'] ?? null,
                    'kapasitas'     => $aktif[(int) $p->ruangan_id]['kapasitas'] ?? null,
                    'tanggal'       => $tgl->format('Y-m-d'),
                    'tanggal_label' => $tgl->locale('id')->translatedFormat('D, j M Y'),
                    'jam_mulai'     => substr($p->jam_mulai, 0, 5),
                    'jam_selesai'   => substr($p->jam_selesai, 0, 5),
                    'status'        => $p->status,
                    'tujuan'        => Str::limit((string) $p->tujuan, 60),
                ];
            })
            ->values();
    }

    /**
     * Status peminjaman ketua per ruangan (S-88). Hanya ruangan yang masih `tersedia` atau
     * `direncanakan`: pihak lain yang terpakai/menunggu tetap menang.
     */
    private function terapkanStatusKetua(array &$hasil, array $hari, string $jamMulai, string $jamSelesai, int $userKetua): void
    {
        $semua = Peminjaman::query()
            ->where('user_id', $userKetua)
            ->whereBetween('tanggal_pinjam', [$hari[0], end($hari)])
            ->where('jam_mulai', '<', $jamSelesai)
            ->where('jam_selesai', '>', $jamMulai)
            ->get(['id', 'ruangan_id', 'tanggal_pinjam', 'status', 'jam_mulai', 'jam_selesai'])
            ->filter(fn ($p) => in_array(Carbon::parse($p->tanggal_pinjam)->toDateString(), $hari, true))
            ->groupBy('ruangan_id');

        $jam = fn ($p) => str_replace(':', '.', substr($p->jam_mulai, 0, 5)) . '–' . str_replace(':', '.', substr($p->jam_selesai, 0, 5));
        $tglLabel = fn (string $t) => Carbon::parse($t)->locale('id')->translatedFormat('j M');
        $lintas = count($hari) > 1;

        foreach ($semua as $ruanganId => $daftar) {
            $ruanganId = (int) $ruanganId;
            if (!isset($hasil[$ruanganId]) || !in_array($hasil[$ruanganId]['status'], ['tersedia', 'direncanakan'], true)) {
                continue;
            }
            $aktif = $daftar->filter(fn ($p) => in_array($p->status, ['disetujui', 'menunggu'], true));

            if ($aktif->isEmpty()) {
                $p = $daftar->first();
                $apa = $p->status === 'dibatalkan' ? 'dibatalkan' : 'ditolak';
                $baru = ['status' => 'ditolak_ketua',
                    'alasan' => ($lintas ? $tglLabel(Carbon::parse($p->tanggal_pinjam)->toDateString()) . ' ' : '')
                        . "Peminjaman Ketua Pelaksana {$jam($p)} {$apa} — pinjam ulang di Manajemen Ruangan"];
            } else {
                $perHari = [];
                $semuaDisetujui = true;
                $kurang = null;
                foreach ($hari as $tgl) {
                    $hariIni = $aktif->filter(fn ($p) => Carbon::parse($p->tanggal_pinjam)->toDateString() === $tgl);
                    $penuh = $hariIni->filter(fn ($p) => $this->detik($p->jam_mulai) <= $jamMulai && $this->detik($p->jam_selesai) >= $jamSelesai);
                    if ($penuh->isEmpty()) {
                        $kurang ??= $hariIni->isEmpty()
                            ? $tglLabel($tgl) . ' belum dipinjam'
                            : ($lintas ? $tglLabel($tgl) . ': ' : '') . 'Peminjaman Ketua Pelaksana hanya ' . $jam($hariIni->first());
                        continue;
                    }
                    $perHari[] = $penuh->first(fn ($p) => $p->status === 'disetujui') ?? $penuh->first();
                    if (!$penuh->contains(fn ($p) => $p->status === 'disetujui')) {
                        $semuaDisetujui = false;
                    }
                }

                if ($kurang !== null) {
                    $baru = ['status' => 'sebagian_ketua', 'alasan' => $kurang];
                } else {
                    $ref = $perHari[0];
                    $awal = $lintas ? $tglLabel($hari[0]) . '–' . $tglLabel(end($hari)) . ' ' : '';
                    $baru = $semuaDisetujui
                        ? ['status' => 'dipinjam_ketua', 'alasan' => "{$awal}Dipinjam Ketua Pelaksana {$jam($ref)} (disetujui)"]
                        : ['status' => 'diajukan_ketua', 'alasan' => "{$awal}Diajukan Ketua Pelaksana {$jam($ref)}, menunggu persetujuan EOffice"];
                }
            }

            if ($hasil[$ruanganId]['status'] === 'direncanakan' && $hasil[$ruanganId]['alasan']) {
                $baru['alasan'] .= ' · juga direncanakan: ' . $hasil[$ruanganId]['alasan'];
            }
            $hasil[$ruanganId] = $baru;
        }
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

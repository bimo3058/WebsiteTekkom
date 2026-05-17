<?php

namespace Modules\EOffice\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\EOffice\Models\Notifikasi;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\PeriodePendaftaran;

class PeriodePendaftaranService
{
    public function __construct(protected NotifikasiService $notifikasi) {}

    public function tutupKadaluarsa(): Collection
    {
        return PeriodePendaftaran::with('praktikum')
            ->where('is_aktif', true)
            ->whereNotNull('ditutup_pada')
            ->where('ditutup_pada', '<=', now())
            ->get()
            ->map(fn (PeriodePendaftaran $periode) => $this->tutup($periode, null, false));
    }

    public function tutup(PeriodePendaftaran $periode, ?int $actorId = null, bool $kirimNotifikasiTutup = true): PeriodePendaftaran
    {
        $periode->loadMissing('praktikum');
        $praktikum = $periode->praktikum;
        $jenisLabel = $this->jenisLabel($periode->jenis);
        $actorId = $actorId ?? $periode->dibuka_oleh ?? $this->fallbackUserId();

        $periode->update(['is_aktif' => false]);

        Pengumuman::where('praktikum_id', $periode->praktikum_id)
            ->where('tipe_sistem', 'buka')
            ->where('periode_id', $periode->id)
            ->delete();

        $this->hapusNotifikasiPembukaan($periode, $jenisLabel);

        if ($actorId && ! Pengumuman::where('periode_id', $periode->id)->where('tipe_sistem', 'tutup')->exists()) {
            Pengumuman::create([
                'praktikum_id' => $periode->praktikum_id,
                'user_id' => $actorId,
                'judul' => "Pendaftaran {$jenisLabel} Telah Ditutup - {$praktikum?->nama}",
                'konten' => "Pendaftaran {$jenisLabel} untuk praktikum {$praktikum?->nama} telah ditutup pada "
                    . now()->format('d M Y, H:i') . ".",
                'is_published' => true,
                'tipe_sistem' => 'tutup',
                'periode_id' => $periode->id,
            ]);
        }

        if ($kirimNotifikasiTutup) {
            $this->notifikasi->kirimKeSemuaUser(
                "Pendaftaran {$jenisLabel} Ditutup",
                "Pendaftaran {$jenisLabel} untuk praktikum {$praktikum?->nama} telah ditutup."
            );
        }

        return $periode->refresh();
    }

    public function jenisLabel(string $jenis): string
    {
        return match ($jenis) {
            'koor' => 'Koordinator',
            'asprak' => 'Asisten Praktikum',
            'praktikan' => 'Praktikan',
            default => ucfirst($jenis),
        };
    }

    private function hapusNotifikasiPembukaan(PeriodePendaftaran $periode, string $jenisLabel): void
    {
        $praktikumNama = $periode->praktikum?->nama;

        Notifikasi::query()
            ->where('judul', 'like', "%Pendaftaran {$jenisLabel} Dibuka%")
            ->when($praktikumNama, fn ($q) => $q->where('pesan', 'like', "%{$praktikumNama}%"))
            ->delete();
    }

    private function fallbackUserId(): ?int
    {
        return User::role(['admin_eoffice', 'superadmin'])->value('id')
            ?? User::whereNull('deleted_at')->value('id');
    }
}

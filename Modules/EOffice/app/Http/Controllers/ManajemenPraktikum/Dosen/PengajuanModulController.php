<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\PengajuanModul;
use Modules\EOffice\Services\NotifikasiService;
use Modules\EOffice\Services\PengajuanModulService;

class PengajuanModulController extends Controller
{
    public function __construct(
        private PengajuanModulService $service,
        private NotifikasiService $notifikasi,
    ) {}

    public function approve(Request $request, PengajuanModul $pengajuan)
    {
        $this->pastikanDosenPengampu($pengajuan);
        $data = $request->validate(['catatan' => ['nullable', 'string', 'max:500']]);
        try {
            $this->service->setujui($pengajuan, auth()->id(), $data['catatan'] ?? null);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $hambatan = $this->service->hambatanPenghapusan($pengajuan->fresh());
            $koorId = $pengajuan->praktikum?->koor_id;
            if ($koorId && $hambatan) {
                $judul = 'Persetujuan modul belum dapat diproses';
                $pesan = 'Pengajuan versi ' . $pengajuan->versi . ' pada ' . $pengajuan->praktikum->nama . ': ' . implode(' ', $hambatan);
                $sudahDikirim = \Modules\EOffice\Models\Notifikasi::where('user_id', $koorId)->where('judul', $judul)->where('pesan', $pesan)->where('is_read', false)->exists();
                if (!$sudahDikirim) $this->notifikasi->kirim($koorId, $judul, $pesan);
            }
            throw $exception;
        }
        $this->kirimKeKoordinator($pengajuan, 'Pengajuan modul disetujui', 'Susunan modul telah disetujui dan siap digunakan.');

        return back()->with('success', 'Pengajuan modul berhasil disetujui.');
    }

    public function revision(Request $request, PengajuanModul $pengajuan)
    {
        $this->pastikanDosenPengampu($pengajuan);
        $data = $request->validate(['catatan' => ['required', 'string', 'max:500']]);
        $this->service->kembalikan($pengajuan, auth()->id(), $data['catatan']);
        $this->kirimKeKoordinator($pengajuan, 'Pengajuan modul perlu direvisi', $data['catatan']);

        return back()->with('success', 'Pengajuan dikembalikan kepada koordinator untuk direvisi.');
    }

    public function reject(Request $request, PengajuanModul $pengajuan)
    {
        $this->pastikanDosenPengampu($pengajuan);
        $data = $request->validate(['catatan' => ['required', 'string', 'max:500']]);
        $this->service->kembalikan($pengajuan, auth()->id(), $data['catatan'], true);
        $this->kirimKeKoordinator($pengajuan, 'Pengajuan modul ditolak', $data['catatan']);

        return back()->with('success', 'Pengajuan modul ditolak.');
    }

    private function pastikanDosenPengampu(PengajuanModul $pengajuan): void
    {
        abort_unless($pengajuan->praktikum?->dosens()->where('users.id', auth()->id())->exists(), 403);
    }

    private function kirimKeKoordinator(PengajuanModul $pengajuan, string $judul, string $pesan): void
    {
        $koorId = $pengajuan->praktikum?->koor_id;
        if ($koorId) {
            $this->notifikasi->kirim($koorId, $judul, $pesan);
        }
    }
}

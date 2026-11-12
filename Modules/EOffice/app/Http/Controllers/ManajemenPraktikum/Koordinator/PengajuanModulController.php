<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\PengajuanModul;
use Modules\EOffice\Models\PengajuanModulItem;
use Modules\EOffice\Services\NotifikasiService;
use Modules\EOffice\Services\PengajuanModulService;

class PengajuanModulController extends Controller
{
    public function __construct(
        private PengajuanModulService $service,
        private NotifikasiService $notifikasi,
    ) {}

    public function create()
    {
        $praktikum = DashboardController::resolvePraktikum() ?? abort(404);
        $pengajuan = $this->service->buatDraft($praktikum, auth()->id());

        return back()->with('success', $pengajuan->items->isEmpty()
            ? 'Draft pengajuan modul berhasil dibuat.'
            : 'Modul periode sebelumnya berhasil dimuat sebagai draft.');
    }

    public function storeItem(Request $request, PengajuanModul $pengajuan)
    {
        $this->pastikanMilikKoordinator($pengajuan);
        abort_unless($pengajuan->dapatDiedit(), 422, 'Pengajuan tidak dapat diubah.');

        $data = $request->validate(['nama' => ['required', 'string', 'max:255'], 'urutan' => ['required', 'integer', 'min:1', 'max:' . ($pengajuan->items()->where('aksi', '!=', 'hapus')->count() + 1)]]);
        $item = $pengajuan->items()->create([
            'aksi' => 'tambah',
            'nama_baru' => $data['nama'],
            'urutan_baru' => $data['urutan'],
        ]);

        $this->service->susunUrutan($pengajuan, $item, (int) $data['urutan']);

        return back()->with('success', 'Modul ditambahkan ke draft.');
    }

    public function updateItem(Request $request, PengajuanModulItem $item)
    {
        $item->load('pengajuan.praktikum');
        $this->pastikanMilikKoordinator($item->pengajuan);
        abort_unless($item->pengajuan->dapatDiedit(), 422, 'Pengajuan tidak dapat diubah.');

        $data = $request->validate(['nama' => ['required', 'string', 'max:255'], 'urutan' => ['required', 'integer', 'min:1', 'max:' . $item->pengajuan->items()->where('aksi', '!=', 'hapus')->count()]]);
        $aksi = $item->modul_sumber_id
            ? (($item->nama_lama === $data['nama'] && (int) $item->urutan_lama === (int) $data['urutan']) ? 'tetap' : 'ubah')
            : 'tambah';

        $item->update([
            'nama_baru' => $data['nama'],
            'aksi' => $aksi,
        ]);

        $this->service->susunUrutan($item->pengajuan, $item, (int) $data['urutan']);

        return back()->with('success', 'Draft modul berhasil diperbarui.');
    }

    public function destroyItem(PengajuanModulItem $item)
    {
        $item->load('pengajuan.praktikum');
        $this->pastikanMilikKoordinator($item->pengajuan);
        abort_unless($item->pengajuan->dapatDiedit(), 422, 'Pengajuan tidak dapat diubah.');

        if ($item->modul_sumber_id) {
            $item->update(['aksi' => 'hapus', 'nama_baru' => null, 'urutan_baru' => null]);
        } else {
            $item->delete();
        }

        $this->service->susunUrutan($item->pengajuan);
        return back()->with('success', 'Perubahan penghapusan disimpan dalam draft.');
    }

    public function restoreItem(PengajuanModulItem $item)
    {
        $item->load('pengajuan.praktikum');
        $this->pastikanMilikKoordinator($item->pengajuan);
        abort_unless($item->pengajuan->dapatDiedit(), 422, 'Pengajuan tidak dapat diubah.');

        $item->update([
            'aksi' => 'tetap',
            'nama_baru' => $item->nama_lama,
            'urutan_baru' => $item->urutan_lama,
        ]);

        $this->service->susunUrutan($item->pengajuan, $item, min((int) $item->urutan_lama, $item->pengajuan->items()->where('aksi', '!=', 'hapus')->count()));
        return back()->with('success', 'Modul dikembalikan ke dalam draft.');
    }

    public function submit(Request $request, PengajuanModul $pengajuan)
    {
        $this->pastikanMilikKoordinator($pengajuan);
        $data = $request->validate(['catatan' => ['nullable', 'string', 'max:500']]);
        $this->service->ajukan($pengajuan, $data['catatan'] ?? null);

        $praktikum = $pengajuan->praktikum()->with('dosens')->firstOrFail();
        $this->notifikasi->kirimBulk(
            $praktikum->dosens->pluck('id'),
            'Pengajuan modul baru',
            "Koordinator mengajukan susunan modul {$praktikum->nama} versi {$pengajuan->versi}."
        );

        return back()->with('success', 'Pengajuan modul berhasil dikirim kepada dosen.');
    }

    public function usePrevious(Request $request, PengajuanModul $pengajuan)
    {
        $this->pastikanMilikKoordinator($pengajuan);
        $request->validate(['external_approval_confirmed'=>'required|accepted']);
        $this->service->pakaiModulTetap($pengajuan,auth()->id());
        return back()->with('success','Modul periode sebelumnya berhasil digunakan tanpa perubahan.');
    }

    public function cancel(PengajuanModul $pengajuan)
    {
        $this->pastikanMilikKoordinator($pengajuan);
        $sedangDiajukan = $pengajuan->status === 'diajukan';
        $this->service->batalkan($pengajuan);

        if ($sedangDiajukan) {
            $praktikum = $pengajuan->praktikum()->with('dosens')->firstOrFail();
            $this->notifikasi->kirimBulk(
                $praktikum->dosens->pluck('id'),
                'Pengajuan modul ditarik',
                "Koordinator menarik pengajuan modul {$praktikum->nama} versi {$pengajuan->versi}."
            );
        }

        return back()->with('success', 'Pengajuan modul dibatalkan. Modul aktif tidak berubah.');
    }

    private function pastikanMilikKoordinator(PengajuanModul $pengajuan): void
    {
        abort_unless($pengajuan->praktikum?->koor_id === auth()->id(), 403);
    }
}

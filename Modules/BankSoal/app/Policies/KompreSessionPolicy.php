<?php

namespace Modules\BankSoal\Policies;

use App\Models\User;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Models\Komprehensif\KompreSession;

/**
 * Policy untuk operasi admin pada KompreSession (Ujian Komprehensif).
 *
 * Route middleware `role:admin_banksoal,admin` sudah memblokir akses
 * non-admin di level routing. Policy ini menambahkan lapisan otorisasi
 * di level controller untuk setiap aksi sensitif secara eksplisit —
 * sesuai prinsip defence-in-depth.
 */
class KompreSessionPolicy
{
    /**
     * Tampilkan daftar sesi (live proctoring & riwayat).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin_banksoal', 'admin']);
    }

    /**
     * Lihat detail hasil sesi satu mahasiswa.
     */
    public function view(User $user, KompreSession $session): bool
    {
        return $user->hasAnyRole(['admin_banksoal', 'admin']);
    }

    /**
     * Paksa selesaikan sesi ujian yang masih berjalan.
     * Status 'ongoing' dikonfirmasi di method ini untuk menghindari
     * duplikasi guard di controller.
     */
    public function forceSubmit(User $user, KompreSession $session): bool
    {
        return $user->hasAnyRole(['admin_banksoal', 'admin'])
            && $session->status === KompreSessionStatus::Ongoing;
    }

    /**
     * Reset seluruh data ujian — hanya admin_banksoal (super admin modul).
     * Operasi ini bersifat destruktif, sehingga dibatasi lebih ketat.
     */
    public function resetAll(User $user): bool
    {
        return $user->hasRole('admin_banksoal');
    }
}

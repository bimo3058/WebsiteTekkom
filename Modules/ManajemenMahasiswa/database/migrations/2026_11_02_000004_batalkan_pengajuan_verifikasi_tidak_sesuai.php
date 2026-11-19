<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Sejak 2 Nov 2026 staff himpunan hanya boleh mengajukan verifikasi pengumuman ke
 * KABID/WAKABID/KANIT/WAKANIT bidang/unitnya sendiri (mk_pengurus_bidang) atau DPM
 * (PengumumanService::verifikatorUntuk). Pengajuan PENDING lama yang tidak sesuai
 * aturan itu dibatalkan dan pengumumannya dikembalikan ke draft — sama dengan
 * PengumumanService::cancelApprovalRequest() — supaya staff mengajukan ulang.
 *
 * Jalankan SETELAH bidang/unit pengurus diatur di halaman Permission: pengajuan ke
 * pimpinan yang bidangnya belum diatur dianggap tidak sesuai.
 */
return new class extends Migration
{
    private const PIMPINAN_BIDANG = ['ketua_bidang', 'wakil_ketua_bidang', 'ketua_unit', 'wakil_ketua_unit'];

    public function up(): void
    {
        $pending = DB::table('mk_pengumuman_approval_requests')
            ->where('status', 'pending')
            ->get(['id', 'pengumuman_id', 'requester_id', 'verifier_id']);

        $dibatalkan = [];

        foreach ($pending as $req) {
            if (!$this->sesuai((int) $req->requester_id, (int) $req->verifier_id)) {
                $dibatalkan[] = $req;
            }
        }

        if ($dibatalkan === []) {
            return;
        }

        DB::transaction(function () use ($dibatalkan) {
            DB::table('mk_pengumuman_approval_requests')
                ->whereIn('id', array_column($dibatalkan, 'id'))
                ->update(['status' => 'cancelled', 'updated_at' => now()]);

            DB::table('mk_pengumuman')
                ->whereIn('id', array_column($dibatalkan, 'pengumuman_id'))
                ->where('status_publish', 'pending_review')
                ->update(['status_publish' => 'draft', 'updated_at' => now()]);
        });

        Log::info('[ManajemenMahasiswa] Pengajuan verifikasi pengumuman dibatalkan (aturan verifikator per bidang/unit).', [
            'jumlah'      => count($dibatalkan),
            'request_ids' => array_column($dibatalkan, 'id'),
        ]);
    }

    public function down(): void
    {
        // Sengaja no-op: pembatalan tidak dikembalikan — staff mengajukan ulang.
    }

    /** Verifikator DPM, atau pimpinan bidang/unit yang sama dengan requester. */
    private function sesuai(int $requesterId, int $verifierId): bool
    {
        $roleVerifier = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('model_has_roles.model_id', $verifierId)
            ->pluck('roles.name');

        if ($roleVerifier->contains('dpm')) {
            return true;
        }

        if ($roleVerifier->intersect(self::PIMPINAN_BIDANG)->isEmpty()) {
            return false;
        }

        $bidangRequester = DB::table('mk_pengurus_bidang')->where('user_id', $requesterId)->value('bidang_id');
        $bidangVerifier  = DB::table('mk_pengurus_bidang')->where('user_id', $verifierId)->value('bidang_id');

        return $bidangRequester && (int) $bidangRequester === (int) $bidangVerifier;
    }
};

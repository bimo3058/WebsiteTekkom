<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Modules\ManajemenMahasiswa\Models\Pengumuman;
use Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest;
use Modules\ManajemenMahasiswa\Models\PengumumanPersonalPin;

class PengumumanService
{
    /**
     * Listing pengumuman untuk admin/operator dengan dukungan pin global & personal.
     */
    /**
     * Listing pengumuman untuk admin/operator dengan dukungan pin global & personal.
     *
     * @param bool $isAdmin True jika user adalah admin/superadmin/admin_kemahasiswaan —
     *                      admin dapat melihat semua draft saat filter status=draft diterapkan.
     */
    public function listAll(array $filters = [], int $perPage = 20, ?int $userId = null, bool $isAdmin = false): LengthAwarePaginator
    {
        // Status yang bersifat "private" — hanya boleh dilihat pemiliknya di tampilan default
        $privateStatuses = ['draft', 'pending_review'];

        return Pengumuman::with(['author', 'repoMulmed'])
            ->when(
                $userId !== null,
                fn ($q) => $q
                    ->leftJoin('mk_pengumuman_personal_pins', function ($join) use ($userId) {
                        $join->on('mk_pengumuman.id', '=', 'mk_pengumuman_personal_pins.pengumuman_id')
                             ->where('mk_pengumuman_personal_pins.user_id', '=', $userId);
                    })
                    ->select('mk_pengumuman.*', DB::raw(
                        'CASE WHEN mk_pengumuman_personal_pins.id IS NOT NULL THEN 1 ELSE 0 END AS is_personal_pinned'
                    )),
                fn ($q) => $q->select('mk_pengumuman.*', DB::raw('0 AS is_personal_pinned'))
            )
            ->when(isset($filters['status']), function ($q) use ($filters, $userId, $isAdmin, $privateStatuses) {
                $q->where('mk_pengumuman.status_publish', $filters['status']);
                // Non-admin yang filter ke status private hanya boleh lihat milik sendiri
                if (!$isAdmin && in_array($filters['status'], $privateStatuses)) {
                    $q->where('mk_pengumuman.user_id', $userId);
                }
            })
            ->when(!isset($filters['status']), function ($q) use ($userId) {
                // Tampilan default (semua role termasuk admin):
                // - Sembunyikan pending_review → dikelola via dashboard verifikasi
                // - Sembunyikan draft yang pernah masuk alur verifikasi (rejected/cancelled)
                // - Hanya "fresh draft" (belum pernah diajukan) milik sendiri yang terlihat
                $q->where(function ($sub) use ($userId) {
                    $sub->whereNotIn('mk_pengumuman.status_publish', ['draft', 'pending_review'])
                        ->orWhere(function ($fresh) use ($userId) {
                            $fresh->where('mk_pengumuman.status_publish', 'draft')
                                  ->where('mk_pengumuman.user_id', $userId)
                                  ->whereNotExists(function ($e) {
                                      $e->from('mk_pengumuman_approval_requests')
                                        ->whereColumn('pengumuman_id', 'mk_pengumuman.id');
                                  });
                        });
                });
            })
            ->when(isset($filters['audience']), fn ($q) => $q->forAudience($filters['audience']))
            ->when(isset($filters['kategori']), fn ($q) => $q->whereJsonContains('mk_pengumuman.kategori', $filters['kategori']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                return $q->where(function ($sub) use ($search) {
                    $sub->where('mk_pengumuman.judul', 'like', "%{$search}%")
                        ->orWhere('mk_pengumuman.konten', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('mk_pengumuman.is_pinned')
            ->orderByDesc('is_personal_pinned')
            ->orderByDesc('mk_pengumuman.created_at')
            ->paginate($perPage);
    }

    /**
     * Listing pengumuman publik untuk mahasiswa/alumni/dosen.
     * Pin global selalu di atas, diikuti pin pribadi user, lalu terbaru.
     */
    public function listPublished(
        string $userRoleAudience,
        ?string $filterKategori = null,
        ?string $search = null,
        int $perPage = 10,
        ?int $userId = null
    ): LengthAwarePaginator {
        return Pengumuman::with(['author', 'repoMulmed'])
            ->when(
                $userId !== null,
                fn ($q) => $q
                    ->leftJoin('mk_pengumuman_personal_pins', function ($join) use ($userId) {
                        $join->on('mk_pengumuman.id', '=', 'mk_pengumuman_personal_pins.pengumuman_id')
                             ->where('mk_pengumuman_personal_pins.user_id', '=', $userId);
                    })
                    ->select('mk_pengumuman.*', DB::raw(
                        'CASE WHEN mk_pengumuman_personal_pins.id IS NOT NULL THEN 1 ELSE 0 END AS is_personal_pinned'
                    )),
                fn ($q) => $q->select('mk_pengumuman.*', DB::raw('0 AS is_personal_pinned'))
            )
            ->published()
            ->forAudience($userRoleAudience)
            ->when($filterKategori, fn ($q) => $q->whereJsonContains('mk_pengumuman.kategori', $filterKategori))
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('mk_pengumuman.judul', 'like', "%{$search}%")
                        ->orWhere('mk_pengumuman.konten', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('mk_pengumuman.is_pinned')
            ->orderByDesc('is_personal_pinned')
            ->orderByDesc('mk_pengumuman.created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): Pengumuman
    {
        return Pengumuman::with(['author', 'repoMulmed'])->findOrFail($id);
    }

    public function create(int $userId, array $data): Pengumuman
    {
        return DB::transaction(function () use ($userId, $data) {
            $pengumuman = Pengumuman::create(array_merge($data, ['user_id' => $userId]));
            $this->flushCache();
            return $pengumuman;
        });
    }

    public function update(int $id, array $data): Pengumuman
    {
        return DB::transaction(function () use ($id, $data) {
            $pengumuman = Pengumuman::findOrFail($id);
            $pengumuman->update($data);
            $this->flushCache();
            return $pengumuman->fresh();
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            Pengumuman::findOrFail($id)->delete();
            $this->flushCache();
        });
    }

    /**
     * Publish pengumuman (dari draft).
     */
    public function publish(int $id): Pengumuman
    {
        return $this->update($id, [
            'status_publish' => Pengumuman::STATUS_PUBLISHED,
            'scheduled_at'   => null,
            'published_at'   => now(),
        ]);
    }

    /**
     * Jadwalkan pengumuman.
     */
    public function schedule(int $id, \DateTimeInterface $scheduledAt): Pengumuman
    {
        return $this->update($id, [
            'status_publish' => Pengumuman::STATUS_SCHEDULED,
            'scheduled_at'   => $scheduledAt,
        ]);
    }

    /**
     * Proses semua pengumuman yang sudah terjadwal — panggil via Command/Scheduler.
     */
    public function processScheduled(): int
    {
        $count = Pengumuman::scheduledReady()->count();

        Pengumuman::scheduledReady()->update([
            'status_publish' => Pengumuman::STATUS_PUBLISHED,
        ]);

        if ($count > 0) {
            $this->flushCache();
        }

        return $count;
    }

    /**
     * Toggle pin global pengumuman (admin only).
     * Tidak menyentuh updated_at agar tidak mengganggu urutan tampilan.
     */
    public function pinPengumuman(int $id): Pengumuman
    {
        $pengumuman = Pengumuman::findOrFail($id);
        Pengumuman::where('id', $id)->update(['is_pinned' => !$pengumuman->is_pinned]);
        $this->flushCache();
        return $pengumuman->fresh();
    }

    /**
     * Toggle pin pribadi pengumuman per user.
     * Kembalikan true jika sekarang di-pin, false jika di-unpin.
     */
    public function togglePersonalPin(int $pengumumanId, int $userId): bool
    {
        Pengumuman::findOrFail($pengumumanId);

        $existing = PengumumanPersonalPin::where('user_id', $userId)
            ->where('pengumuman_id', $pengumumanId)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        PengumumanPersonalPin::create([
            'user_id'       => $userId,
            'pengumuman_id' => $pengumumanId,
        ]);

        return true;
    }

    /**
     * Cek apakah user sudah personal-pin pengumuman ini.
     */
    public function isPersonalPinned(int $pengumumanId, int $userId): bool
    {
        return PengumumanPersonalPin::where('user_id', $userId)
            ->where('pengumuman_id', $pengumumanId)
            ->exists();
    }

    private function flushCache(): void
    {
        Cache::forget('mk.dashboard.snapshot');
    }

    // =========================================================================
    // Approval Workflow (Staff Himpunan → Ketua)
    // =========================================================================

    /**
     * Cek apakah user memiliki role staff_himpunan yang wajib verifikasi.
     */
    public function requiresApproval(User $user): bool
    {
        $roles = $user->roles->pluck('name');

        // Staff himpunan wajib verifikasi, KECUALI jika juga punya role senior
        $seniorRoles = ['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm',
                        'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit',
                        'dosen', 'dosen_koordinator'];

        return $roles->contains('staff_himpunan')
            && $roles->intersect($seniorRoles)->isEmpty();
    }

    /**
     * Ambil daftar user yang dapat dijadikan verifikator.
     * Hanya ketua-ketua himpunan — admin tidak dipilih langsung,
     * tapi tetap bisa memproses semua request via dashboard verifikasi.
     */
    public function getAvailableVerifiers(): Collection
    {
        $verifierRoleNames = [
            'ketua_himpunan',
            'wakil_ketua_himpunan',
            'ketua_bidang',
            'ketua_unit',
        ];

        return User::whereHas('roles', function ($q) use ($verifierRoleNames) {
            $q->whereIn('name', $verifierRoleNames);
        })
        ->with(['roles' => function ($q) use ($verifierRoleNames) {
            $q->whereIn('name', $verifierRoleNames);
        }])
        ->orderBy('name')
        ->get();
    }

    /**
     * Buat approval request dan set pengumuman ke pending_review.
     */
    public function requestApproval(int $pengumumanId, int $requesterId, int $verifierId, ?string $pesan): PengumumanApprovalRequest
    {
        return DB::transaction(function () use ($pengumumanId, $requesterId, $verifierId, $pesan) {
            // Set pengumuman ke pending_review
            Pengumuman::where('id', $pengumumanId)->update([
                'status_publish' => Pengumuman::STATUS_PENDING_REVIEW,
            ]);

            // Batalkan request pending sebelumnya jika ada
            PengumumanApprovalRequest::where('pengumuman_id', $pengumumanId)
                ->where('status', PengumumanApprovalRequest::STATUS_PENDING)
                ->update(['status' => PengumumanApprovalRequest::STATUS_CANCELLED]);

            $this->flushCache();

            return PengumumanApprovalRequest::create([
                'pengumuman_id'  => $pengumumanId,
                'requester_id'   => $requesterId,
                'verifier_id'    => $verifierId,
                'status'         => PengumumanApprovalRequest::STATUS_PENDING,
                'pesan_pengaju'  => $pesan,
            ]);
        });
    }

    /**
     * Approve pengumuman — publish langsung.
     */
    public function approveRequest(int $requestId, ?string $catatan): void
    {
        DB::transaction(function () use ($requestId, $catatan) {
            $request = PengumumanApprovalRequest::findOrFail($requestId);

            $request->update([
                'status'              => PengumumanApprovalRequest::STATUS_APPROVED,
                'catatan_verifikator' => $catatan,
                'verified_at'         => now(),
            ]);

            // Publish pengumuman
            $this->update($request->pengumuman_id, [
                'status_publish' => Pengumuman::STATUS_PUBLISHED,
                'published_at'   => now(),
            ]);
        });
    }

    /**
     * Reject pengumuman — kembalikan ke draft.
     */
    public function rejectRequest(int $requestId, string $catatan): void
    {
        DB::transaction(function () use ($requestId, $catatan) {
            $request = PengumumanApprovalRequest::findOrFail($requestId);

            $request->update([
                'status'              => PengumumanApprovalRequest::STATUS_REJECTED,
                'catatan_verifikator' => $catatan,
                'verified_at'         => now(),
            ]);

            // Kembalikan ke draft agar staff bisa edit & ajukan ulang
            Pengumuman::where('id', $request->pengumuman_id)->update([
                'status_publish' => Pengumuman::STATUS_DRAFT,
            ]);

            $this->flushCache();
        });
    }

    /**
     * Cancel/tarik kembali approval request yang masih pending.
     */
    public function cancelApprovalRequest(int $pengumumanId, int $requesterId): void
    {
        DB::transaction(function () use ($pengumumanId, $requesterId) {
            PengumumanApprovalRequest::where('pengumuman_id', $pengumumanId)
                ->where('requester_id', $requesterId)
                ->where('status', PengumumanApprovalRequest::STATUS_PENDING)
                ->update(['status' => PengumumanApprovalRequest::STATUS_CANCELLED]);

            // Kembalikan ke draft
            Pengumuman::where('id', $pengumumanId)->update([
                'status_publish' => Pengumuman::STATUS_DRAFT,
            ]);

            $this->flushCache();
        });
    }

    /**
     * Ambil daftar approval request pending milik verifikator tertentu.
     */
    public function getPendingForVerifier(int $userId): Collection
    {
        return PengumumanApprovalRequest::with(['pengumuman.author', 'requester'])
            ->forVerifier($userId)
            ->pending()
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Ambil semua approval request untuk verifikator dengan filter status.
     */
    public function getRequestsForVerifier(int $userId, ?string $status = null): Collection
    {
        return PengumumanApprovalRequest::with(['pengumuman.author', 'requester'])
            ->forVerifier($userId)
            ->when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Ambil SEMUA approval request (untuk admin yang bisa proses request siapapun).
     */
    public function getAllRequests(?string $status = null): Collection
    {
        return PengumumanApprovalRequest::with(['pengumuman.author', 'requester', 'verifier'])
            ->when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Hitung semua approval request pending (untuk counter badge admin).
     */
    public function getAllPendingCount(): int
    {
        return PengumumanApprovalRequest::where('status', PengumumanApprovalRequest::STATUS_PENDING)->count();
    }

    /**
     * Ambil semua approval request yang DIAJUKAN oleh requester (staff himpunan).
     * Digunakan untuk halaman "Status Verifikasi" milik staff sendiri.
     */
    public function getRequestsByRequester(int $userId, ?string $status = null): Collection
    {
        return PengumumanApprovalRequest::with(['pengumuman', 'verifier'])
            ->where('requester_id', $userId)
            ->when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Hitung jumlah approval request pending yang diajukan oleh requester.
     */
    public function getPendingCountByRequester(int $userId): int
    {
        return PengumumanApprovalRequest::where('requester_id', $userId)
            ->where('status', PengumumanApprovalRequest::STATUS_PENDING)
            ->count();
    }
}

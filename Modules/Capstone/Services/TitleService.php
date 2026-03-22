<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Collection;
use Modules\Capstone\Models\CapstoneTitle;

class TitleService
{
    /**
     * Semua judul yang sudah di-approve admin, status OPEN.
     */
    public function getAvailable(): Collection
    {
        return CapstoneTitle::where('status', CapstoneTitle::STATUS_OPEN)
            ->where('approved_by_admin', true)
            ->with(['lecturer'])
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Judul milik dosen tertentu.
     */
    public function getByLecturer(int $lecturerId): Collection
    {
        return CapstoneTitle::where('lecturer_id', $lecturerId)
            ->with(['groups', 'bids'])
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Judul yang diajukan mahasiswa (source = STUDENT), pending approval.
     */
    public function getPendingStudentTitles(): Collection
    {
        return CapstoneTitle::where('title_source', CapstoneTitle::SOURCE_STUDENT)
            ->where('supervisor_approval_status', CapstoneTitle::APPROVAL_PENDING)
            ->with(['proposedByGroup.members.student', 'proposedSupervisor'])
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Semua judul pending approval admin.
     */
    public function getPendingAdminApproval(): Collection
    {
        return CapstoneTitle::where('approved_by_admin', false)
            ->whereNull('deleted_at')
            ->with(['lecturer'])
            ->get();
    }

    public function createTitle(array $data): CapstoneTitle
    {
        return CapstoneTitle::create($data);
    }

    public function approveByAdmin(CapstoneTitle $title): void
    {
        $title->update(['approved_by_admin' => true]);
    }

    public function approveBySupervisor(CapstoneTitle $title): void
    {
        $title->update(['supervisor_approval_status' => CapstoneTitle::APPROVAL_APPROVED]);
    }

    public function rejectBySupervisor(CapstoneTitle $title): void
    {
        $title->update(['supervisor_approval_status' => CapstoneTitle::APPROVAL_REJECTED]);
    }
}
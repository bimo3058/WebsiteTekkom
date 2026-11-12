<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Peminjaman, PracticumBookingMeeting, Ruangan};
use Modules\EOffice\Services\RoomBookingService;

/** Practicum-side adapter. Never changes Room Management's approval rules. */
class PracticumRoomBooking
{
    public function __construct(private RoomBookingService $rooms) {}

    public function assertReady(): void
    {
        $type = Schema::hasColumn('eo_mr_peminjamans', 'source_id')
            ? Schema::getColumnType('eo_mr_peminjamans', 'source_id') : '';
        if (!in_array(strtolower($type), ['varchar', 'character varying', 'string', 'text', 'nvarchar'], true)) {
            throw ValidationException::withMessages(['jadwal' => 'Integrasi ruangan belum siap: source_id harus mendukung ID sesi berupa UUID. Tunggu pembaruan migrasi Manajemen Ruang. Rancangan tetap tersimpan.']);
        }
    }

    public function rooms()
    {
        return Ruangan::where('is_active', true)->whereIn('kategori', ['Kelas', 'Laboratorium']);
    }

    public function availability(string $date, string $start, string $end, int $capacity = 0): array
    {
        $arguments = [$date, $start, $end, $capacity, null, true];
        // main still has the old six-argument contract; the pending MR update adds source.
        $method = new \ReflectionMethod(RoomBookingService::class, 'getAvailableRooms');
        if (collect($method->getParameters())->contains(fn ($p) => $p->getName() === 'source')) {
            $arguments['source'] = 'praktikum';
        }
        $allowed = $this->rooms()->pluck('id')->all();
        return array_values(array_filter($this->rooms->getAvailableRooms(...$arguments),
            fn ($room) => in_array($room['id'], $allowed)));
    }

    public function book(PracticumBookingMeeting $meeting, int $requester): Peminjaman
    {
        $this->assertReady();
        if (!$this->rooms()->whereKey($meeting->room_id)->exists()) {
            throw ValidationException::withMessages(['jadwal' => 'Praktikum hanya boleh menggunakan ruang aktif berkategori Kelas atau Laboratorium.']);
        }
        $title = $meeting->praktikum->nama.' - '.$meeting->module_name.' - Shift '.$meeting->shift;
        try {
            return $this->rooms->bookRoom([
                'source' => 'praktikum', 'source_id' => (string) $meeting->id,
                'user_id' => $requester, 'ruangan_id' => $meeting->room_id,
                'tanggal' => $meeting->date->toDateString(),
                'jam_mulai' => substr($meeting->starts_at, 0, 5),
                'jam_selesai' => substr($meeting->ends_at, 0, 5), 'tujuan' => $title,
            ]);
        } catch (\Illuminate\Database\QueryException $error) {
            throw $error;
        } catch (\Exception $error) {
            throw ValidationException::withMessages(['jadwal' => $meeting->module_name.' (Shift '.$meeting->shift.'): '.$error->getMessage()]);
        }
    }

    public function cancel(PracticumBookingMeeting $meeting, string $reason): void
    {
        $booking = $meeting->booking()->lockForUpdate()->first();
        if (!$booking || $booking->source !== 'praktikum' || (string) $booking->getRawOriginal('source_id') !== (string) $meeting->id) {
            throw ValidationException::withMessages(['jadwal' => 'Pembatalan belum dapat diproses karena data peminjaman belum tersinkron. Hubungi Admin Praktikum.']);
        }
        if (!in_array($booking->status, ['menunggu', 'disetujui'], true) ||
            !$this->rooms->cancelBooking('praktikum', (string) $meeting->id, $reason)) {
            throw ValidationException::withMessages(['jadwal' => 'Booking sudah berubah. Muat ulang jadwal sebelum melanjutkan.']);
        }
    }
}

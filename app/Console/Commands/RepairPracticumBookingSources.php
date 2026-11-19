<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Peminjaman, PracticumBookingMeeting};
use Modules\EOffice\Services\ManajemenPraktikum\PracticumRoomBooking;

class RepairPracticumBookingSources extends Command
{
    protected $signature = 'practicum:repair-booking-sources {--apply : Isi source_id yang kosong setelah pemeriksaan}';
    protected $description = 'Periksa referensi booking praktikum; default hanya pratinjau, tanpa perubahan data.';

    public function handle(PracticumRoomBooking $gateway): int
    {
        if ($this->option('apply')) {
            try { $gateway->assertReady(); }
            catch (ValidationException $error) { $this->error($error->getMessage()); return self::FAILURE; }
        }
        $candidates = PracticumBookingMeeting::whereNotNull('booking_id')->orderBy('id')->get();
        $counts = ['siap' => 0, 'sudah cocok' => 0, 'perlu diperiksa' => 0];
        DB::transaction(function () use ($candidates, &$counts) {
            foreach ($candidates as $candidate) {
                $meeting = PracticumBookingMeeting::whereKey($candidate->id)->lockForUpdate()->first();
                $booking = $meeting->booking()->lockForUpdate()->first();
                if (!$booking || $booking->source !== 'praktikum' ||
                    PracticumBookingMeeting::where('booking_id', $meeting->booking_id)->count() !== 1 ||
                    (int) $booking->ruangan_id !== (int) $meeting->room_id ||
                    $booking->tanggal_pinjam->toDateString() !== $meeting->date->toDateString() ||
                    substr($booking->jam_mulai, 0, 5) !== substr($meeting->starts_at, 0, 5) ||
                    substr($booking->jam_selesai, 0, 5) !== substr($meeting->ends_at, 0, 5)) {
                    $counts['perlu diperiksa']++; continue;
                }
                $sourceId = $booking->getRawOriginal('source_id');
                if ($sourceId !== null) {
                    $counts[(string) $sourceId === (string) $meeting->id ? 'sudah cocok' : 'perlu diperiksa']++;
                    continue;
                }
                $counts['siap']++;
                if ($this->option('apply')) {
                    DB::table('eo_mr_peminjamans')->where('id', $booking->id)->whereNull('source_id')
                        ->update(['source_id' => (string) $meeting->id, 'updated_at' => now()]);
                }
            }
        });
        $orphans = Peminjaman::where('source', 'praktikum')->whereNull('source_id')
            ->whereNotIn('id', PracticumBookingMeeting::whereNotNull('booking_id')->select('booking_id'))->count();
        $counts['perlu diperiksa'] += $orphans;
        $this->table(['Hasil', 'Jumlah'], collect($counts)->map(fn ($count, $label) => [$label, $count])->values()->all());
        $this->info($this->option('apply') ? 'Referensi yang valid telah diisi. Data meragukan tidak diubah.' : 'Pratinjau saja. Tidak ada data yang diubah. Gunakan --apply setelah migrasi MR tersedia.');
        return self::SUCCESS;
    }
}

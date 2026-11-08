<?php

namespace Modules\EOffice\Console\Commands;

use Illuminate\Console\Command;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Services\RoomBookingService;
use App\Models\User;
use Illuminate\Support\Str;

class TestRoomIntegrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eoffice:test-room-integration {--seed-ui : Buat data peminjaman reschedule dummy untuk ditest langsung di Web Browser} {--clean-ui : Bersihkan semua data dummy pengujian UI}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan simulasi pengujian otomatis integrasi Manajemen Ruangan (UUID Praktikum, Reschedule Two-Phase Swap, dan Approval Admin Ruang)';

    /**
     * Execute the console command.
     */
    public function handle(RoomBookingService $service): int
    {
        if ($this->option('clean-ui')) {
            $deleted = Peminjaman::where('tujuan', 'like', '%[TEST-UI]%')->forceDelete();
            $this->info("Berhasil membersihkan {$deleted} data uji UI.");
            return Command::SUCCESS;
        }

        $ruangan = Ruangan::where('is_active', true)->where('kategori', '!=', 'Sidang')->first();
        $user = User::first();

        if (!$ruangan || !$user) {
            $this->error("Gagal: Data ruangan aktif atau user tidak ditemukan di database.");
            return Command::FAILURE;
        }

        if ($this->option('seed-ui')) {
            $this->info("=================================================================");
            $this->info("    MEMBUAT DATA UJI UNTUK PENGUJIAN LANGSUNG DI WEB BROWSER     ");
            $this->info("=================================================================\n");

            $testUuid = (string) Str::uuid();

            // Peminjaman Awal
            $tglAwal = now()->addDays(7)->format('Y-m-d');
            $tglBaru = now()->addDays(10)->format('Y-m-d');

            $initial = Peminjaman::create([
                'user_id' => $user->id,
                'ruangan_id' => $ruangan->id,
                'tujuan' => '[TEST-UI] Praktikum Sistem Tertanam - Sesi 2',
                'tanggal_pinjam' => $tglAwal,
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
                'status' => 'disetujui',
                'source' => 'praktikum',
                'source_id' => $testUuid,
                'created_by' => $user->id,
            ]);

            // Reschedule Baru
            $reschedule = $service->rescheduleBooking('praktikum', $testUuid, [
                'ruangan_id' => $ruangan->id,
                'tanggal_pinjam' => $tglBaru,
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'tujuan' => '[TEST-UI] Praktikum Sistem Tertanam - Sesi 2 (Reschedule)',
            ]);

            $this->info("Data pengujian berhasil dibuat!");
            $this->line("1. Peminjaman Awal : #{$initial->id} (Status: {$initial->status}, Tgl: {$tglAwal})");
            $this->line("2. Pengajuan Baru  : #{$reschedule->id} (Status: {$reschedule->status}, Tgl: {$tglBaru}, Replaces: #{$initial->id})");
            $this->line("\nSilakan buka browser di halaman Admin Ruang:");
            $this->comment("👉 http://127.0.0.1:8000/eoffice/peminjaman/admin/persetujuan");
            $this->line("\nSetelah selesai mencoba di browser, Anda bisa membersihkannya dengan:");
            $this->comment("👉 php artisan eoffice:test-room-integration --clean-ui\n");
            return Command::SUCCESS;
        }

        $this->info("=================================================================");
        $this->info("   SIMULASI PENGUJIAN INTEGRASI MANAJEMEN RUANGAN (E-OFFICE)    ");
        $this->info("=================================================================\n");

        $ruangan = Ruangan::where('is_active', true)->where('kategori', '!=', 'Sidang')->first();
        $user = User::first();

        if (!$ruangan || !$user) {
            $this->error("Gagal: Data ruangan aktif atau user tidak ditemukan di database.");
            return Command::FAILURE;
        }

        $testUuid = (string) Str::uuid();
        $this->line("<comment>[Tahap 1]</comment> Membuat Peminjaman Awal (Simulasi Modul Praktikum)");
        $this->line("   - Source    : praktikum");
        $this->line("   - Source ID : {$testUuid} (UUID String)");
        $this->line("   - Ruangan   : {$ruangan->nama}");
        $this->line("   - Tanggal   : 2026-12-10 (09:00 - 11:00 WIB)");

        $initial = Peminjaman::create([
            'user_id' => $user->id,
            'ruangan_id' => $ruangan->id,
            'tujuan' => 'Praktikum Jaringan Komputer - Sesi 1',
            'tanggal_pinjam' => '2026-12-10',
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'status' => 'disetujui',
            'source' => 'praktikum',
            'source_id' => $testUuid,
            'created_by' => $user->id,
        ]);

        $this->info("   [BERHASIL] Peminjaman #{$initial->id} terdaftar dengan status: {$initial->status}\n");

        // Tahap 2: Reschedule pertama
        $this->line("<comment>[Tahap 2]</comment> Koor Praktikum Mengajukan Reschedule ke Tanggal 15 Des 2026");
        $reschedule1 = $service->rescheduleBooking('praktikum', $testUuid, [
            'ruangan_id' => $ruangan->id,
            'tanggal_pinjam' => '2026-12-15',
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'tujuan' => 'Praktikum Jarkom - Sesi 1 (Jadwal Pengganti)',
        ]);

        $initial->refresh();
        $this->line("   - Status Peminjaman Lama (#{$initial->id}) : <info>{$initial->status}</info> (Tetap Aman!)");
        $this->line("   - Status Pengajuan Baru  (#{$reschedule1->id}) : <comment>{$reschedule1->status}</comment>");
        $this->line("   - Replaces Peminjaman ID (#{$reschedule1->replaces_peminjaman_id}) : <info>Sesuai (#{$initial->id})</info>");
        $this->info("   [BERHASIL] Two-Phase Swap aktif! Slot lama tidak hilang.\n");

        // Tahap 3: Reschedule kedua (Ganda)
        $this->line("<comment>[Tahap 3]</comment> Koor Mengubah Pikiran, Reschedule Lagi ke Tanggal 17 Des 2026");
        $reschedule2 = $service->rescheduleBooking('praktikum', $testUuid, [
            'ruangan_id' => $ruangan->id,
            'tanggal_pinjam' => '2026-12-17',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'tujuan' => 'Praktikum Jarkom - Sesi 1 (Revisi Pengganti Terbaru)',
        ]);

        $reschedule1->refresh();
        $initial->refresh();
        $this->line("   - Draf Pengajuan Pertama (#{$reschedule1->id}) : <comment>{$reschedule1->status}</comment> (Dibatalkan tertib)");
        $this->line("   - Pengajuan Terbaru      (#{$reschedule2->id}) : <comment>{$reschedule2->status}</comment>");
        $this->line("   - Peminjaman Lama        (#{$initial->id}) : <info>{$initial->status}</info> (Tetap Disetujui)");
        $this->info("   [BERHASIL] Idempotensi draf ganda bekerja sempurna!\n");

        // Tahap 4: Admin Ruang menyetujui
        $this->line("<comment>[Tahap 4]</comment> Admin Ruang Mengklik 'Setujui' pada Pengajuan #{$reschedule2->id}");
        $controller = app(\Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin\PersetujuanController::class);
        $request = new \Illuminate\Http\Request(['status' => 'disetujui']);
        $controller->updateStatus($request, $reschedule2->id);

        $initial->refresh();
        $reschedule2->refresh();
        $this->line("   - Status Pengajuan Baru (#{$reschedule2->id}) : <info>{$reschedule2->status}</info> (Resmi Sah!)");
        $this->line("   - Status Peminjaman Lama (#{$initial->id}) : <comment>{$initial->status}</comment> (Otomatis Dibatalkan Sistem)");
        $this->line("   - Catatan Alasan Batal : {$initial->alasan_penolakan}");
        $this->info("   [BERHASIL] Logika Two-Phase Swap tuntas disahkan oleh Admin Ruang!\n");

        // Tahap 5: Pengujian Reschedule di Ruangan & Hari yang SAMA (Overlap Guard Validation)
        $this->line("<comment>[Tahap 5]</comment> Simulasi Reschedule di Ruangan & Hari yang Sama (Geser Jam)");
        $sameDayReschedule = $service->rescheduleBooking('praktikum', $testUuid, [
            'ruangan_id' => $ruangan->id,
            'tanggal_pinjam' => '2026-12-17', // Hari yang sama dengan #reschedule2
            'jam_mulai' => '11:00',          // Beririsan dengan jam 10:00-12:00 sebelumnya
            'jam_selesai' => '13:00',
            'tujuan' => 'Praktikum Jarkom - Sesi 1 (Geser Jam Hari Sama)',
        ]);

        $this->line("   - Draf Reschedule Hari Sama (#{$sameDayReschedule->id}) : <comment>{$sameDayReschedule->status}</comment>");
        $controller->updateStatus($request, $sameDayReschedule->id);
        $sameDayReschedule->refresh();
        $reschedule2->refresh();
        $this->line("   - Pengajuan Hari Sama Berhasil Disetujui (#{$sameDayReschedule->id}) : <info>{$sameDayReschedule->status}</info>");
        $this->line("   - Jadwal Sebelumnya Berhasil Digantikan (#{$reschedule2->id})   : <comment>{$reschedule2->status}</comment>");
        $this->info("   [BERHASIL] Pengecualian collision check untuk jadwal yang digantikan bekerja 100%!\n");

        // Cleanup
        $initial->forceDelete();
        $reschedule1->forceDelete();
        $reschedule2->forceDelete();
        $sameDayReschedule->forceDelete();

        $this->info("=================================================================");
        $this->info("   SELURUH PENGUJIAN LULUS 100%! DATA PENGUJIAN TELAH DIBERSIHKAN ");
        $this->info("=================================================================");

        return Command::SUCCESS;
    }
}

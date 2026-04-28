<?php

namespace Modules\ManajemenMahasiswa\Console\Commands;

use App\Models\Role;
use App\Models\Student;
use App\Services\AuditLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAlumniStatus extends Command
{
    protected $signature   = 'mk:check-alumni {--dry-run : Preview tanpa mengubah data}';
    protected $description = 'Auto-assign role alumni untuk mahasiswa yang angkatannya sudah ≥ 5 tahun';

    public function handle(): int
    {
        $cutoffYear   = now()->year - 5;
        $isDryRun     = $this->option('dry-run');

        $alumniRole    = Role::where('name', 'alumni')->first();
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();

        if (!$alumniRole || !$mahasiswaRole) {
            $this->error('Role alumni atau mahasiswa tidak ditemukan. Jalankan migration terlebih dahulu.');
            return self::FAILURE;
        }

        $himpunanRoles = Role::whereIn('name', [
            'mahasiswa', 'pengurus_himpunan',
            'ketua_himpunan',
            'ketua_bidang', 'ketua_unit', 'staff_himpunan',
        ])->pluck('id')->toArray();

        $students = Student::where('cohort_year', '<=', $cutoffYear)
            ->with('user.roles')
            ->get();

        $toPromote = $students->filter(fn($s) => $s->user && !$s->user->hasRole('alumni'));
        $count     = $toPromote->count();

        $this->info("Cutoff angkatan: ≤ {$cutoffYear}");
        $this->info("Mahasiswa yang akan dijadikan alumni: {$count}");

        if ($count === 0) {
            $this->line('Tidak ada perubahan yang diperlukan.');
            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->table(['Nama', 'NIM', 'Angkatan'], $toPromote->map(fn($s) => [
                $s->user->name,
                $s->student_number,
                $s->cohort_year,
            ])->toArray());
            $this->warn('(dry-run) Tidak ada perubahan yang dilakukan.');
            return self::SUCCESS;
        }

        if (!$this->confirm("Lanjutkan mengubah {$count} mahasiswa menjadi alumni?")) {
            $this->line('Dibatalkan.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        DB::beginTransaction();
        try {
            foreach ($toPromote as $student) {
                $user        = $student->user;
                $currentIds  = $user->roles->pluck('id')->toArray();
                $keptIds     = array_diff($currentIds, $himpunanRoles);
                $newIds      = array_unique([...$keptIds, $alumniRole->id]);

                $user->roles()->sync($newIds);
                $user->load('roles');
                $user->syncPermissionsFromRoles();
                $user->clearUserCache();

                $bar->advance();
            }

            AuditLogger::update(
                module:  'manajemen_mahasiswa',
                desc:    "mk:check-alumni — {$count} mahasiswa diubah menjadi alumni (angkatan ≤ {$cutoffYear})",
                subject: null,
                oldData: [],
                newData: ['count' => $count, 'cutoff_year' => $cutoffYear],
            );

            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("{$count} mahasiswa berhasil dijadikan alumni.");
            return self::SUCCESS;

        } catch (\Throwable $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine();
            $this->error('Gagal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

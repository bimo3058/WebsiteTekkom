<?php

namespace Modules\BankSoal\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetLegacyPassword extends Command
{
    protected $signature = 'kompre:set-legacy-password
                            {--password=password : Password baru untuk akun legacy}
                            {--dry-run : Hitung jumlah akun tanpa menyimpan}';

    protected $description = 'Mengatur password default akun mahasiswa legacy hasil import ujian komprehensif';

    public function handle(): int
    {
        $password = (string) $this->option('password');

        if ($password === '') {
            $this->error('Password tidak boleh kosong.');

            return self::FAILURE;
        }

        $query = User::withTrashed()->where(function ($query) {
            $query->where('external_id', 'like', 'LEGACY-KOMPRE-%')
                ->orWhere('email', 'like', '%@legacy.kompre.local');
        });

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('Tidak ada akun legacy yang ditemukan.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Dry-run: {$total} akun legacy akan diubah.");

            return self::SUCCESS;
        }

        $updated = 0;

        $query->orderBy('id')->chunkById(100, function ($users) use ($password, &$updated) {
            foreach ($users as $user) {
                $user->password = Hash::make($password);
                $user->save();
                $updated++;
            }
        });

        $this->info("{$updated} akun legacy berhasil diubah password-nya.");

        return self::SUCCESS;
    }
}

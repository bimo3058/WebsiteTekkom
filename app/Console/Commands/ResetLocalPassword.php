<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ResetLocalPassword extends Command
{
    protected $signature = 'auth:reset-local-password {email : Email tepat akun yang akan direset}';

    protected $description = 'Reset password satu akun melalui input tersembunyi, hanya di lingkungan lokal';

    public function handle(): int
    {
        if (! app()->environment('local') || ! $this->input->isInteractive()) {
            $this->error('Perintah ini hanya tersedia secara interaktif di lingkungan lokal.');
            return self::FAILURE;
        }

        $user = User::where('email', (string) $this->argument('email'))->first();
        if (! $user) {
            $this->error('Akun tidak ditemukan. Tidak ada data yang diubah.');
            return self::FAILURE;
        }

        $this->warn('Jika database dipakai bersama, perubahan ini juga berlaku pada lingkungan yang menggunakan database tersebut.');
        $this->line('Hanya password lokal akun '.$user->email.' yang akan diubah. Role dan password SSO tidak berubah.');
        if (! $this->confirm('Lanjutkan reset akun ini?', false)) return self::SUCCESS;

        $password = $this->secret('Password lokal baru', false);
        $confirmation = $this->secret('Ulangi password lokal baru', false);
        $validation = Validator::make(
            ['password' => $password, 'password_confirmation' => $confirmation],
            ['password' => ['required', 'string', 'confirmed', Password::defaults()]]
        );
        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $message) $this->error($message);
            $this->error('Tidak ada data yang diubah.');
            return self::FAILURE;
        }

        DB::transaction(function () use ($user, $password) {
            $current = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $current->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
        });
        unset($password, $confirmation);
        $user->clearUserCache();
        $this->info('Password lokal berhasil direset. Gunakan halaman login lokal untuk masuk.');
        return self::SUCCESS;
    }
}

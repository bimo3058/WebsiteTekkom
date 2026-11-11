<?php

namespace App\Console\Commands;

use App\Models\{Role,User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB,Hash,Validator};
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CreatePracticumAdmin extends Command
{
    protected $signature='practicum:create-admin {email=admin.praktikum@undip.ac.id}';
    protected $description='Buat akun jabatan Admin Praktikum dengan password tersembunyi; tidak membuat identitas SSO';

    public function handle(): int
    {
        if (!$this->input->isInteractive()) { $this->error('Jalankan secara interaktif agar password tidak ditulis di argumen.'); return self::FAILURE; }
        $email=Str::lower(trim((string)$this->argument('email')));
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) { $this->error('Email tidak valid.'); return self::FAILURE; }
        if (User::withTrashed()->whereRaw('LOWER(email) = ?',[$email])->exists()) { $this->error('Email sudah digunakan. Akun dan password lama tidak diubah.'); return self::FAILURE; }
        $role=Role::where('name','admin_praktikum')->where('guard_name','web')->first();
        if (!$role) { $this->error('Jalankan migrasi role praktikum terlebih dahulu.'); return self::FAILURE; }
        $this->warn('Perintah ini membuat akun pada database yang sedang terhubung, termasuk jika database digunakan bersama.');
        if (!$this->confirm('Buat akun jabatan '.$email.'?',false)) return self::SUCCESS;
        $password=$this->secret('Password akun',false);
        $confirmation=$this->secret('Ulangi password',false);
        $validator=Validator::make(['password'=>$password,'password_confirmation'=>$confirmation],['password'=>['required','string','confirmed',Password::defaults()]]);
        if ($validator->fails()) { foreach($validator->errors()->all() as $message)$this->error($message); return self::FAILURE; }
        DB::transaction(function() use($email,$password,$role) {
            $user=User::create(['name'=>'Admin Praktikum','email'=>$email,'external_id'=>'local:'.Str::uuid(),'password'=>Hash::make($password)]);
            $user->roles()->syncWithoutDetaching([$role->id]);
            $user->syncPermissionsFromRoles();
            $user->clearUserCache();
        });
        unset($password,$confirmation);
        $this->info('Akun berhasil dibuat. Login melalui /admin-praktikum/login. Akun ini belum ditautkan ke SSO.');
        return self::SUCCESS;
    }
}

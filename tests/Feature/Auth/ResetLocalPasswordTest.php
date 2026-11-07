<?php

namespace Tests\Feature\Auth;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ResetLocalPasswordTest extends TestCase
{
    public function test_command_rejects_non_local_environment(): void
    {
        $this->artisan('auth:reset-local-password', ['email'=>'test@example.com'])->assertExitCode(1);
    }

    public function test_reset_only_changes_the_selected_account(): void
    {
        $this->app->instance('env', 'local');
        config(['database.connections.local_reset_test'=>['driver'=>'sqlite','database'=>':memory:']]);
        DB::setDefaultConnection('local_reset_test');
        Schema::create('users', function (Blueprint $table) {
            $table->id();$table->string('email');$table->string('password');$table->string('remember_token')->nullable();$table->timestamps();$table->softDeletes();
        });
        $original=Hash::make('old-test-password');
        DB::table('users')->insert([['email'=>'target@example.com','password'=>$original],['email'=>'other@example.com','password'=>$original]]);
        $this->artisan('auth:reset-local-password', ['email'=>'target@example.com'])
            ->expectsConfirmation('Lanjutkan reset akun ini?', 'yes')
            ->expectsQuestion('Password lokal baru', 'Test1234')
            ->expectsQuestion('Ulangi password lokal baru', 'Test1234')
            ->assertExitCode(0);
        $this->assertTrue(Hash::check('Test1234', DB::table('users')->where('email','target@example.com')->value('password')));
        $this->assertSame($original, DB::table('users')->where('email','other@example.com')->value('password'));
    }
}

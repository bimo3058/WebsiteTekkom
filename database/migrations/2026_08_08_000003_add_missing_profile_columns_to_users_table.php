<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp', 30)->nullable();
            }
            if (! Schema::hasColumn('users', 'avatar_url')) {
                $table->text('avatar_url')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $columns = array_values(array_filter(
            ['whatsapp', 'avatar_url'],
            fn (string $column) => Schema::hasColumn('users', $column)
        ));

        if ($columns !== []) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};

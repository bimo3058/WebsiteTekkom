<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kemahasiswaan', function (Blueprint $table) {
            $table->decimal('ipk', 3, 2)->nullable()->after('status')
                  ->comment('IPK mahasiswa, range 0.00 - 4.00');
        });
    }

    public function down(): void
    {
        Schema::table('mk_kemahasiswaan', function (Blueprint $table) {
            $table->dropColumn('ipk');
        });
    }
};

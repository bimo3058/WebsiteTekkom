<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("CREATE TYPE bs_role AS ENUM ('admin','dosen','gpm','mahasiswa')");
        DB::statement("CREATE TYPE bs_status AS ENUM ('disetujui','revisi','draft','diajukan')");
        DB::statement("CREATE TYPE bs_kesulitan AS ENUM ('easy','intermediate','advanced')");
    }

    public function down(): void
    {
        DB::statement("DROP TYPE IF EXISTS bs_kesulitan");
        DB::statement("DROP TYPE IF EXISTS bs_status");
        DB::statement("DROP TYPE IF EXISTS bs_role");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $needsEmail = ! Schema::hasColumn('cv_profiles', 'cv_email');
        $needsWhatsapp = ! Schema::hasColumn('cv_profiles', 'cv_whatsapp');

        if (! $needsEmail && ! $needsWhatsapp) {
            return;
        }

        Schema::table('cv_profiles', function (Blueprint $table) use ($needsEmail, $needsWhatsapp) {
            if ($needsEmail) {
                $table->string('cv_email')->nullable()->after('user_id');
            }
            if ($needsWhatsapp) {
                $table->string('cv_whatsapp')->nullable()->after('cv_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kolom utama dimiliki migration global dengan timestamp yang sama.
    }
};
